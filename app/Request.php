<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    const STATUS_申请中 = '申请中';
    const STATUS_申请通过 = '申请通过';
    const STATUS_驳回 = '驳回';

    public static function listStatus()
    {
        return [
            self::STATUS_申请中,
            self::STATUS_申请通过,
            self::STATUS_申请中,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function info()
    {
        return $this->belongsTo(Info::class);
    }
}
