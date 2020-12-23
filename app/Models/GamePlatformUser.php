<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;

class GamePlatformUser extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;

    const PASSWORD_LENGTH = 10;

    protected $casts = [
        'balance_status' => 'bool',
        'balance'        => 'float',
    ];

    protected $dates = [
        'platform_created_at',
    ];

    protected $auditInclude = [
        'balance_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function platform()
    {
        return $this->belongsTo(GamePlatform::class, 'platform_code', 'code');
    }

    public function isRemoteRegistered()
    {
        return is_null($this->platform_created_at) ? false : true;
    }

    public function updatePlatformUserId($platformUserId)
    {
        return $this->update([
            'platform_user_id'      => $platformUserId,
            'platform_created_at'   => now(),
        ]);
    }

    public function updateBalance($balance)
    {
        return $this->update([
            'balance' =>  $balance,
        ]);
    }

    public function updateBalanceStatus($status)
    {
        return $this->update([
            'balance_status' => $status,
        ]);
    }

    public function updateName($name)
    {
        return $this->update([
            'name' => $name,
        ]);
    }

}
