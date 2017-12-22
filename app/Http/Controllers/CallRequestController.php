<?php

namespace App\Http\Controllers;

use App\CallRequest;
use App\Info;
use App\User;
use Carbon\Carbon;
use Collective\Html\HtmlFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Lego\Lego;
use Whoops\Exception\ErrorException;

class CallRequestController extends Controller
{
    // 拨打电话记录
    public function getCalled()
    {
        $infoId = Input::get('infoId');
        $new = new CallRequest();
        $new->info_id = $infoId;
        $new->user_id = Auth::id() ?? null;
        $new->calltime = Carbon::now();
        $new->save();
    }
}
