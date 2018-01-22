<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InsertWechatDetail extends Model
{
    protected $table = 'wechat_infos';

    public $dates = ['date'];
}
