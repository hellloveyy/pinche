<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallRequest extends Model
{
    protected $table = 'requests';

    public $dates = ['calltime'];

    public function info()
    {
        return $this->belongsTo(Info::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
