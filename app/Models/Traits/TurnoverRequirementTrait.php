<?php

namespace App\Models\Traits;

trait TurnoverRequirementTrait
{
    public function closeTurnoverRequirement()
    {
        if (!$this->isTurnoverRequirementClosed()) {
            $turnoverClosedValue = $this->turnover_closed_value;

            $this->update([
                'turnover_current_value'        => $turnoverClosedValue,
                'is_turnover_closed'            => true,
                'turnover_closed_at'            => now(),
            ]);

            $this->turnoverRequirement->close();
        }
    }

    public function manualCloseTurnoverRequirement()
    {

        if (!$this->isTurnoverRequirementClosed()) {
            $this->update([
                'is_turnover_closed'            => true,
                'turnover_closed_at'            => now(),
            ]);
            $this->turnoverRequirement ? $this->turnoverRequirement->close() : '';
        }
    }

    public function incrementCurrentValue($value)
    {
        $this->increment('turnover_current_value', $value);

    }

    public function isTurnoverRequirementClosed()
    {
        return $this->is_turnover_closed;
    }

    /**
     * 减去目标值
     * 1、判断是否已关闭，若关闭无需扣减
     * 2、判断减去后剩余值的情况 剩余值:remain 当前累计值：current
     * 2.1、 remain > current 流程结算
     * 2.2、 remain <= current 执行关闭流程
     *
     * @param $value
     */
    public function decrementCloseValue($value)
    {
        if (!$this->isTurnoverRequirementClosed()) {
            $remain = $this->turnover_closed_value - $value;
            $remain = $remain >= 0 ? $remain : 0;

            # 先更新关闭值
            $this->update(['turnover_closed_value'=>$remain]);

            if ($remain <= $this->turnover_current_value) {
                $this->closeTurnoverRequirement();
            }
        }
    }

    public function scopeClosed($query)
    {
        return $query->where('is_turnover_closed', true);
    }

    public function scopeNotClosed($query)
    {
        return $query->where('is_turnover_closed', false);
    }

    /**
     * 人为关闭
     *
     * @param $adminName
     * @param $remark
     * @return bool
     */
    public function adminClose($adminName, $remark)
    {
        $result = $this->update([
            'is_turnover_closed'        => true,
            'turnover_closed_at'        => now(),
            'turnover_closed_admin_name'=> $adminName,
            'remark'                    => !empty($remark) ? $remark : '',
        ]);

        if ($result && !empty($this->turnoverRequirement)) {
            $this->turnoverRequirement->close();
        }

        return $result;
    }
}