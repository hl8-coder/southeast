<?php

namespace App\Models;

class TurnoverRequirement extends Model
{
    protected $fillable = [
        'requireable_id',
        'requireable_type',
        'is_closed',
        'closed_at'
    ];

    protected $casts = [
        'is_closed' => 'boolean',
    ];

    protected $dates = [
        'closed_at',
    ];

    public function requireable()
    {
        return $this->morphTo();
    }

    /**
     * 添加
     *
     * @param   $model
     * @param   bool                $isClosed       是否关闭
     * @param   integer             $userId         会员id
     * @return  TurnoverRequirement
     */
    public static function add($model, $isClosed, $userId=null)
    {
        # 检查是否存在该类型
        if (static::isExists($model)) {
            return null;
        }

        $requirement                    = new static();
        $requirement->user_id           = !empty($userId) ? $userId : $model->user_id;
        $requirement->requireable_id    = $model->id;
        $requirement->requireable_type  = get_class($model);
        $requirement->is_closed         = $isClosed;

        $requirement->save();

        return $requirement;
    }

    public static function isExists($model)
    {
        return static::query()->where('requireable_id', $model->id)->where('requireable_type', get_class($model))->exists();
    }

    /**
     * 获取未关闭的流水项
     *
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getNotCloseRequirement($userId)
    {
        return static::query()->where('is_closed', false)
            ->where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function close()
    {
        return $this->update([
                'is_closed'  => true,
                'closed_at'  => now(),
            ]);
    }
}
