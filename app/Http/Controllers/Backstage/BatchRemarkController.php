<?php

namespace App\Http\Controllers\Backstage;

use App\Exports\ExcelTemplateExport;
use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\BatchRemarkRequest;
use App\Imports\BatchRemarkImport;
use App\Models\BatchRemark;
use App\Models\BatchRemarkFail;
use App\Models\Remark;
use App\Repositories\UserRepository;
use App\Transformers\BatchRemarkFailTransformer;
use App\Transformers\BatchRemarkTransformer;
use App\Transformers\RemarkTransformer;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BatchRemarkController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/batch_remark/index",
     *      operationId="api.backstage.batch_remark.index",
     *      tags={"Backstage-批量上传备注"},
     *      summary="批量上传备注",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/BatchRemark"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index(Request $request)
    {
        $batchRemarks = BatchRemark::with('remarks')->latest()->paginate($request->per_page);
        return $this->response->paginator($batchRemarks, new BatchRemarkTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/batch_remark/show/{batchRemarkId}",
     *      operationId="api.backstage.batch_remark.index",
     *      tags={"Backstage-批量上传备注"},
     *      summary="文件详情",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Remark"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function show($batchRemarkId, Request $request)
    {
        $batchRemark = BatchRemark::find($batchRemarkId);

        $batchRemarks = $batchRemark->remarks()->paginate($request->per_page);
        return $this->response->paginator($batchRemarks, new RemarkTransformer('batch_remark'));
    }

    /**
     * @OA\Get(
     *      path="/backstage/batch_remark/fails/{batchRemarkId}",
     *      operationId="api.backstage.batch_remark.index",
     *      tags={"Backstage-批量上传备注"},
     *      summary="失败记录详情",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/FailRemark"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function fails($batchRemarkId, Request $request)
    {
        $batchRemark = BatchRemark::find($batchRemarkId);

        $batchRemarks = $batchRemark->fails()->paginate($request->per_page);
        return $this->response->paginator($batchRemarks, new BatchRemarkFailTransformer());
    }

    public function uploadFile(BatchRemarkRequest $request)
    {
        $file        = $request->file('file');
        $folder_name = "uploads/excels/" . date("Ym", time()) . '/' . date("d", time()) . '/';
        $upload_path = public_path() . '/' . $folder_name;
        $extension   = strtolower($file->getClientOriginalExtension()) ?: 'xlsx';
        $filename    = time() . '_' . str_random(10) . '.' . $extension;
        $file->move($upload_path, $filename);

        return $this->response->array([
            'path' => "$folder_name$filename"
        ]);
    }

    public function store(BatchRemarkRequest $request)
    {
        $data = remove_null($request->all());

        $batch            = new BatchRemark();
        $batch->file      = $data['file'];
        $batch->upload_by = $this->user->name;
        $batch->save();
        $batch->refresh();

        return $this->remark($batch, $data['file']);
    }

    public function remark($batch, $file)
    {
        $import      = new BatchRemarkImport();
        $remarks = Excel::toArray($import, $file);

        $remarkTypes = array_flip(Remark::$types);
        $remarkCategory = array_flip(Remark::$categories);
        $remarkSubCategory = array_flip(Remark::$subCategories);

        foreach ($remarks[0] as $remark) {
            # 是否处理成功.
            $flag = true;
            if (!$user = UserRepository::findByName($remark[0])) {
                $flag = false;
            }


            # type
            if (!$remark[1] || empty($remarkTypes[$remark[1]])) {
                $flag = false;
            }

            #category
            if (!$remark[2] || empty($remarkCategory[$remark[2]])) {
                $flag = false;
            }

            if (!empty($remark[3]) && empty($remarkSubCategory[$remark[3]])) {
                $flag = false;
            } elseif (!empty($remark[3]) && !empty($remarkSubCategory[$remark[3]]) && !empty($remark[2]) && !empty($remarkCategory[$remark[2]])) { // category 和subcategory 同事存在时 需要判断关联关系

                # 存在子栏目映射
                if (!empty(Remark::$subCategoriesRelated[$remarkCategory[$remark[2]]])) {
                    $subCategoryRelated = Remark::$subCategoriesRelated[$remarkCategory[$remark[2]]];
                    $subCategoryRelatedKeys = array_column($subCategoryRelated,'key');

                    if (!in_array($remarkSubCategory[$remark[3]], $subCategoryRelatedKeys)) {
                        $flag = false;
                    }

                } else {
                    $flag = false;
                }

            }

            $batch->increment('total_num');

            if (!$flag) { // excel读取失败.
                # 写入处理失败的信息
                BatchRemarkFail::create([
                    'batch_remark_id' => $batch->id,
                    'user_name' => !empty($remark[0]) ? $remark[0] : null,
                    'type' => !empty($remark[1]) ? $remark[1] : null,
                    'category' => !empty($remark[2]) ? $remark[2] : null,
                    'sub_category' => !empty($remark[3]) ? $remark[3] : null,
                    'reason' => !empty($remark[4]) ? $remark[4] : null,
                ]);
                $batch->increment('fail_num');
                $batch->save();
                continue;
            } else {
                $batch->increment('success_num');
                $batch->save();

            }
            $type = $remarkTypes[$remark[1]];
            $category = $remarkCategory[$remark[2]];


            $model                      = new Remark();
            $model->user_id             = $user->id;
            $model->type             = $type;
            $model->category             = $category;

             #sub category
            if (isset($remark[3]) || !empty($remarkSubCategory[$remark[3]])) {
                $model->sub_category = $remarkSubCategory[$remark[3]];
            }

            #reason
            if (isset($remark[4])) {
                $model->reason = $remark[4];
            }

            # 管理员name
            $model->admin_name = $batch->upload_by;

            $model->batch_remark_id = $batch->id;

            $model->save();
        }

        return $this->response->noContent();
    }

    public function downloadExcel()
    {
        $headings = [
            'MemberCode',
            'Type',
            'Category',
            'subCategory',
            'Reason',
        ];
        return Excel::download(new ExcelTemplateExport([], $headings), 'batchRemark.xlsx');
    }
}
