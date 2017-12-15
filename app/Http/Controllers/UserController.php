<?php

namespace App\Http\Controllers;

use App\Info;
use App\User;
use Carbon\Carbon;
use Collective\Html\HtmlFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Lego\Lego;
use Whoops\Exception\ErrorException;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 创建发布信息
    public function getMyInfo()
    {
        $title = '我的信息';

        $edit = Lego::form(User::find(Auth::id()));
        $edit->addText('name', '昵称');
        $edit->addText('email', '邮箱');
        $edit->addText('mobile', '手机号');
        $edit->readonly();

        return $edit->view('info.info', compact('title', 'edit'));
    }
}
