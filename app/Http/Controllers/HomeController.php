<?php

namespace App\Http\Controllers;

use App\Info;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lego\Lego;

class HomeController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = '拼车信息列表';

        $filter = Lego::filter(Info::where('start_at', '>=', Carbon::now()));
        $filter->addSelect('go_where', '行进方向')->values(Info::listGoWheres());
        $filter->addText('start', '出发地');
        $filter->addText('end', '目的地');
        $filter->addDatetimeRange('start_at', '出发时间');
        $filter->addSelect('status', '信息状态')->values(Info::listStatus());
        $filter->addText('note', '补充');
        $filter->addSelect('myReleased', '只显示我发布的信息')->values([Info::MY_我发布的信息])->scope('myReleased');

        $grid = Lego::grid($filter);

        $grid->addLeftTopButton('新增新的信息', action('InfoController@anyCreateInfo'))
            ->attribute('target', '_blank')
            ->bootstrapStyle('info');
        $grid->addLeftTopButton('修改历史信息', action('InfoController@getHistoryList'))
            ->attribute('target', '_blank')
            ->bootstrapStyle('default');
        $grid->addLeftTopButton('我的申请', action('InfoController@getMyRequest'))
            ->attribute('target', '_blank')
            ->bootstrapStyle('success');
        $grid->addRightTopButton('! 使用须知 !', action('HomeController@getHasKnow'))
            ->attribute('target', '_blank')
            ->bootstrapStyle('danger');

        $grid->add('id', '操作')->cell(function ($_, Info $info) {
            if (Auth::id() == $info->user_id) { // 发布人
                if ($info->status === Info::STATUS_撤销) {
                    return '';
                }
                $str = link_to(action('InfoController@anyCreateInfo', ['id' => $info->id]), '编辑', ['target' => '_blank']);
                if ($info->status !== Info::STATUS_撤销) {
                    $str .= ' | ' . link_to(action('InfoController@getWithdraw', ['id' => $info->id]), '撤回');
                }
                if (!$info->requests->isEmpty()) {
                    $str .= ' | '  . link_to(action('InfoController@getRequestList', ['id' => $info->id]), '申请列表', ['target' => '_blank']);
                }
            } else { // 申请人
                $request = $info->requests->where('user_id', Auth::id())->first();
                $str = $request
                    ? $request->status
                    : ($info->status === Info::STATUS_撤销
                        ? '撤回信息无法申请'
                        : link_to(action('InfoController@getRequest', ['id' => $info->id]), '申请'));
            }
            return $str;
        });
        $grid->add('go_where', '行进方向');
        $grid->add('start', '出发地');
        $grid->add('end', '目的地');
        $grid->add('start_at', '出发时间');
        $grid->add('weekend', '星期')->cell(function ($_, Info $info) {
            return [
                0 => '日',
                1 => '一',
                2 => '二',
                3 => '三',
                4 => '四',
                5 => '五',
                6 => '六',
            ][$info->start_at->dayOfWeek] ?? null;
        });
        $grid->add('num', '人数');
        $grid->add('had_num', '已通过申请人数')->cell(function ($_, Info $info) {
            return $info->requests->where('status', \App\Request::STATUS_申请通过)->count() === $info->num
                ? '!车满!'
                : $info->requests->where('status', \App\Request::STATUS_申请通过)->count();
        });
        $grid->add('amount_yuan', '费用(人)');
        $grid->add('status', '信息状态');
        $grid->add('note', '补充');

        $grid->paginate(30)->orderBy('start_at', 'desc');

        return $grid->view('home', compact('title', 'grid'));
    }

    public function getHasKnow()
    {
        $title = '使用须知';
        return view('know', compact('title'));
    }
}
