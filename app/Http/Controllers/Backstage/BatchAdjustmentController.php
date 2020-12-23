<?php

namespace App\Http\Controllers\Backstage;

use App\Exports\ExcelTemplateExport;
use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\BatchAdjustmentRequest;
use App\Imports\BatchAdjustmentImport;
use App\Models\Adjustment;
use App\Models\BatchAdjustment;
use App\Models\GamePlatform;
use App\Models\GamePlatformProduct;
use App\Models\TurnoverRequirement;
use App\Repositories\AdjustmentRepository;
use App\Repositories\UserRepository;
use App\Services\GamePlatformService;
use App\Transformers\AdjustmentTransformer;
use App\Transformers\BatchAdjustmentTransformer;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BatchAdjustmentController extends BackstageController
{
    public function index(Request $request)
    {
        $batchAdjustments = BatchAdjustment::with('adjustments')->latest()->paginate($request->per_page);
        return $this->response->paginator($batchAdjustments, new BatchAdjustmentTransformer());
    }

    public function show(BatchAdjustment $adjustment, Request $request)
    {
        $adjustments = $adjustment->adjustments()->paginate($request->per_page);
        return $this->response->paginator($adjustments, new AdjustmentTransformer());
    }

    public function uploadFile(BatchAdjustmentRequest $request)
    {
        $file        = $request->file('file');
        $folder_name = "uploads/excels/" . date("Ym", time()) . '/' . date("d", time()) . '/';
        $upload_path = public_path() . '/' . $folder_name;
        $extension   = strtolower($file->getClientOriginalExtension()) ?: 'xlsx';
        $filename    = time() . '_' . str_random(10) . '.' . $extension;
        $file->move($upload_path, $filename);
        return $this->response->array([
            'path' => "$folder_name$filename",
            'unique_key' => strtolower($file->getClientOriginalName()),
        ]);
    }

    public function store(BatchAdjustmentRequest $request)
    {
        $data = remove_null($request->all());

        $batch              = new BatchAdjustment();
        $batch->type        = $data['type'];
        $batch->file        = $data['file'];
        $batch->unique_key  = $data['unique_key'];
        $batch->upload_by   = $this->user->name;
        $batch->save();
        $batch->refresh();

        return $this->adjustment($batch, $data['file']);
    }

    public function adjustment($batch, $file)
    {
        $import      = new BatchAdjustmentImport;
        $adjustments = Excel::toArray($import, $file);
        # 循环进行验证
        $platformCodes = GamePlatform::getAll()->pluck('code')->toArray();
        $productCodes = GamePlatformProduct::getAll()->pluck('code')->toArray();
        foreach ($adjustments[0] as $key => $adjustment) {
            # 验证会员必须存在
            if (!$user = UserRepository::findByName($adjustment[0])) {
                error_response(422, "Line " . ($key+1) . " Member(" . $adjustment[0] . ") does not exist！");
            }

            # 验证amount必须为大于0的数字
            if (!is_numeric($adjustment[2]) || $adjustment[2]<=0) {
                error_response(422, "Line " . ($key+1) . " Amount(" . $adjustment[2] . ") must be a number greater than 0！");
            }

            # 验证turnover倍数必须大于等于0
            if ($adjustment[3]<0) {
                error_response(422, "Line " . ($key+1) . " Turnover(" . $adjustment[3] . ") must be greater than or equal to 0！");
            }

            # 验证category必须在范围内
            if (!in_array($adjustment[5], array_keys(Adjustment::$categories))) {
                error_response(422, "Line " . ($key+1) . " Category(" . $adjustment[5] . ") does not exist！");
            }

            # 验证platform必须是平台
            if (!empty($adjustment[6]) && !in_array($adjustment[6], $platformCodes)) {
                error_response(422, "Line " . ($key+1) . " Platform(" . $adjustment[6] . ") does not exist！");
            }

            # 验证product_code必须是product
            if (!empty($adjustment[7]) && !in_array($adjustment[7], $productCodes)) {
                error_response(422, "Line " . ($key+1) . " Product(" . $adjustment[7] . ") does not exist！");
            }

            # 验证reason必须存在
            if (empty($adjustment[8])) {
                error_response(422, "Line " . ($key+1) . " Reason(" . $adjustment[8] . ") cannot be empty！");
            }
        }

        foreach ($adjustments[0] as $adjustment) {

            $user = UserRepository::findByName($adjustment[0]);

            $model                      = new Adjustment();
            $model->user_id             = $user->id;
            $model->user_name           = $user->name;
            $model->created_admin_name  = $this->user->name;
            $model->type                = $batch->type;
            $model->batch_adjustment_id = $batch->id;
            if ($adjustment[1]) {
                $model->related_order_no = $adjustment[1];
            }
            $model->amount = $adjustment[2];
            if ($adjustment[3])
                $model->turnover_closed_value = (float)$adjustment[2] * (float)$adjustment[3];
            $model->category = $adjustment[5];
            if ($adjustment[6]) {
                $model->platform_code = $adjustment[6];
            }
            if ($adjustment[7]) {
                $model->product_code = $adjustment[7];
            }
            $model->reason = $adjustment[8];
            if ($adjustment[9]) {
                $model->remark = $adjustment[9];
            }
            $model->save();
            $this->approve($model->refresh());
        }
    }

    public function approve(Adjustment $adjustment)
    {
        # 无第三方平台，正常异动主钱包
        # 有第三方平台
        # 正常发起第三方转账

        $adminName = $this->user->name;
        # 存在第三方游戏平台
        if ($platform = GamePlatform::findByCodeFromCache($adjustment->platform_code)) {

            $gamePlatformService = new GamePlatformService();
            $detail              = null;

            if ($adjustment->isDeposit()) {
                TurnoverRequirement::add($adjustment, $adjustment->is_turnover_closed);

                $detail = $gamePlatformService->redirectTransfer($platform, $adjustment->user, $adjustment->amount, 'Adjustment', true);

                if ($detail->isSuccess()) {
                    # 统计数据
                    AdjustmentRepository::recordReport($adjustment);
                }
            } else {
                $detail = $gamePlatformService->redirectTransfer($platform, $adjustment->user, $adjustment->amount, 'Adjustment', false);

                if ($detail->isSuccess()) {
                    # 判断关联流水被要求
                    AdjustmentRepository::closeTurnoverRequirement($adjustment);
                }
            }

            if ($detail) {
                $adjustment->update(['platform_transfer_detail_id' => $detail->id]);

                # 转账失败
                if ($detail->isFail()) {
                    $remark = 'Transfer to ' . $platform->code . ' failed.';
                    $adjustment->fail($adminName, $remark);
                }

                # 转账等待确认
                if ($detail->isNeedManualCheck()) {
                    $remark = 'Transfer to ' . $platform->code . ' need checking.';
                    $adjustment->waitingCheck($adminName, $remark);
                }

                # 转账成功
                if ($detail->isSuccess()) {
                    AdjustmentRepository::setSuccess($adjustment, $adminName, $adjustment->remark);
                }
            }
        } else { # 不存在游戏平台
            AdjustmentRepository::adjustmentMainWallet($adjustment, $adminName);

            # 判断关联流水被要求
            if ($adjustment->isWithdrawal()) {
                AdjustmentRepository::closeTurnoverRequirement($adjustment);
            }
        }

        return $this->response->noContent();
    }

    public function downloadExcel()
    {
        $headings = [
            'Member code',
            'Related Txn ID',
            'Amount',
            'Turnover',
            'Turnover amount',
            'Category',
            'Platform',
            'Product',
            'Reason',
            'Remark',
        ];
        return Excel::download(new ExcelTemplateExport([], $headings), 'batchAdjustment.xlsx');
    }
}
