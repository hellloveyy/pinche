<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Info extends Model
{
    use SoftDeletes;

    const STATUS_拼人中 = '拼人中';
    const STATUS_撤销 = '撤销';

    const GO_WHERE_香河方向 = '香河方向';
    const GO_WHERE_北京方向 = '北京方向';

    const MY_我发布的信息 = '我发布的信息';

    public $dates = ['start_at'];

    public static function listNumbers()
    {
        return [1, 2, 3, 4, 5, 6];
    }

    public static function listGoWheres()
    {
        return [
            self::GO_WHERE_北京方向,
            self::GO_WHERE_香河方向,
        ];
    }

    public static function listStatus()
    {
        return [
            self::STATUS_拼人中,
            self::STATUS_撤销
        ];
    }

    public function requests()
    {
        return $this->hasMany(Request::class, 'info_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 增加合同量房状态的筛选
     */
    public function scopeMyReleased($query, $status)
    {
        if (!$status) {
            return $query;
        }

        if ($status === self::MY_我发布的信息) {
            return $query->where('user_id', Auth::id());
        }

        return $query;
    }

}
