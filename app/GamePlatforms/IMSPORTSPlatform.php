<?php
namespace App\GamePlatforms;

use App\GamePlatforms\Tools\IMBaseTool;

class IMSPORTSPlatform extends IMBasePlatform
{
    /**
     * @var IMBaseTool
     */
    protected $tool;
    protected $productWallet    = 301;

    # 拉取报表 start
    public function pull()
    {
        $data = [];
        foreach ($this->prefixes as $prefix) {
            $result = $this->singlePull($prefix, $this->productWallet, 1);
            if(isset($result['Result'])) {
                if (1 == $result['Pagination']['TotalPage']) {
                    $data[$prefix] = $result['Result'];
                } else {
                    $tempData = [];
                    for ($i = 1; $i <= $result['pages']; $i++) {
                        $result = $this->singlePull($prefix, $this->productWallet, $i);
                        if (isset($result['Result'])) {
                            $tempData[] = array_merge($tempData, $result['Result']);
                        }
                    }
                    $data[$prefix] = $tempData;
                }
            }
        }

        return $this->tool->insertBetDetails($data);
    }

    public function singlePull($prefix, $productWallet, $page = 1, $oldData=false)
    {
        $this->request['url']    = $this->platform->report_request_url . '/Report/GetBetLog';
        $schedule                = $this->data['schedule'];
        $data['ProductWallet']   = $productWallet;
        $data['DateFilterType']  = 1;
        $data['StartDate']       = $schedule->start_at->subMinutes($this->platform->offset)->format('Y-m-d H.i.s');
        $data['EndDate']         = $schedule->end_at->format('Y-m-d H.i.s');
        $data['LastUpdatedDate'] = $data['StartDate'];
        $data['PageSize']        = 1000;
        $data['page']            = $page;
        $data['Currency']        = strtoupper($prefix);
        $data['Language']        = 'EN';
        $request                 = $this->setRequest('pull', $data, $prefix);
        $response                = $this->call($request);
        return $this->tool->checkResponse($response, 'pull', $this->data);
    }

    public function pullOldSportsData()
    {
        $data = [];
        foreach ($this->prefixes as $prefix) {
            if('102' != $this->productWallet) {
                $result = $this->singleOldPull($prefix, $this->productWallet, 1);
                if (isset($result['Result'])) {
                    if (1 == $result['Pagination']['TotalPage']) {
                        $data[$prefix] = $result['Result'];
                    } else {
                        $tempData = [];
                        for ($i = 1; $i <= $result['pages']; $i++) {
                            $result = $this->singleOldPull($prefix, $this->productWallet , $i);
                            if (isset($result['Result'])) {
                                $tempData[] = array_merge($tempData, $result['Result']);
                            }
                        }
                        $data[$prefix] = $tempData;
                    }
                }
            }
        }
        return $this->tool->insertBetDetails($data);
    }

    public function singleOldPull($prefix, $productWallet, $page = 1)
    {
        $this->request['url']    = $this->platform->report_request_url . '/Report/GetBetLog';
        $data['ProductWallet']   = $productWallet;
        $data['DateFilterType']  = 2;
        $data['BetStatus']       = 1;
        $data['StartDate']       = now()->subDays(5)->format('Y-m-d H.i.s');
        $data['EndDate']         = now()->format('Y-m-d H.i.s');
        $data['PageSize']        = 1000;
        $data['page']            = $page;
        $data['Currency']        = strtoupper($prefix);
        $data['Language']        = 'EN';
        $request                 = $this->setRequest('pull', $data, $prefix);
        $response                = $this->call($request);
        return $this->tool->checkResponse($response, 'pull', $this->data);
    }
    # 拉取报表 end

}
