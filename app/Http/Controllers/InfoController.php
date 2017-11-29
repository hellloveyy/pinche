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

class InfoController extends Controller
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
    public function anyCreateInfo()
    {
        $title = '创建拼车信息';

        $id = Input::get('id');

        $edit = Lego::form(Info::find($id) ?? new Info());

        $edit->addSelect('go_where', '方向')
            ->values(Info::listGoWheres());
        $edit->addText('start', '出发地');
        $edit->addText('end', '目的地');
        $edit->addNumber('amount_yuan', '费用(人)');
        $edit->addDatetime('start_at', '出发时间')
            ->note('请仔细检查出发时间!');
        $edit->addSelect('num', '空余座位数')
            ->values(Info::listNumbers());
        $edit->addText('plate_number', '车牌号');
        $edit->addText('color', '车身颜色');
        $edit->addText('car_brand', '汽车品牌');
        $edit->addText('mobile', '联系手机号');
        $edit->addText('note', '补充信息')
            ->note('包含途径点等信息,自由发挥');
        $edit->addHidden('user_id')->default(Auth::id());

        Info::saving(function (Info $info) {
            // 同一天同一人同一方向只能有一条有效信息,防止有人多次刷屏
            if (Info::where('status', Info::STATUS_拼人中)
                ->where('go_where', $info->go_where)
                ->where('user_id', Auth::id())
                ->whereBetween('start_at', [$info->start_at->startOfDay(), $info->start_at->endOfDay()])
                ->exists()) {
                return Lego::message(
                    '实在抱歉 -- O(∩_∩)O -- 同一天同一人同一方向只能有一条信息,防止有人多次刷屏'
                );
            }
        });
        return $edit->view('info.info', compact('title', 'edit'));
    }

    // 历史发布列表今天以前
    public function getHistoryList()
    {
        $title = '历史信息';
        $source = Info::where('user_id', Auth::id())->where('start_at', '<', Carbon::now());

        $grid = Lego::grid($source);

        $grid->add('id', '编辑详细')->cell(function ($_, Info $info) {
            return link_to(action('InfoController@anyCreateInfo', ['id' => $info->id]), '编辑', ['target' => '_blank']);
        });
        $grid->add('go_where', '行进方向');
        $grid->add('start', '出发地');
        $grid->add('end', '目的地');
        $grid->add('start_at', '出发时间');
        $grid->add('num', '人数');
        $grid->add('amount_yuan', '费用(人)');
        $grid->add('note', '补充');

        $grid->paginate(30)->orderBy('id', 'desc');

        return $grid->view('home', compact('title', 'grid'));
    }

    // 以下为申请流程
    public function getRequestList()
    {
        $title = '我的发布申请列表';

        $infoId = Input::get('id');
        $source = \App\Request::with('user', 'info')->where('info_id', $infoId);
        $filter = Lego::filter($source);
        $filter->addText('user.name', '申请人');
        $filter->addText('user.mobile', '申请手机号');
        $filter->addSelect('info.go_where', '申请同行方向')->values(Info::listGoWheres());
        $filter->addSelect('status', '申请状态')->values(\App\Request::listStatus());

        $grid = Lego::grid($filter);

        $grid->add('id', '操作')->cell(function ($_, \App\Request $request) {
            if ($request->status === \App\Request::STATUS_申请中) {
                return link_to(action('InfoController@getApprove', ['id' => $request->id]), '同意');
            }
            if ($request->status === \App\Request::STATUS_申请通过) {
                return link_to(action('InfoController@getReject', ['id' => $request->id]), '驳回');
            }
        });
        $grid->add('user.name', '申请人');
        $grid->add('user.mobile', '申请人手机号');
        $grid->add('info.go_where', '申请同行方向');
        $grid->add('status', '申请状态');
        $grid->add('created_at', '申请时间');
        $grid->paginate(30)->orderBy('created_at');

        return $grid->view('home', compact('title', 'grid'));
    }

    // 申请加入
    public function getRequest()
    {
        $info = Info::find(Input::get('id'));
        if (!$info) {
            return Lego::message(
                '没有此条信息,请在列表中重新选择'
            );
        }
        return Lego::confirm(
            '确认申请乘坐此条信息吗?保证大家的效率,请认真选择!',
            function ($sure) use ($info) {
                if ($sure) {
                    $request = new \App\Request();
                    $request->info_id = $info->id;
                    $request->user_id = Auth::id();
                    $request->status = \App\Request::STATUS_申请中;
                    $request->saveOrFail();
                }
            }
        );
    }

    // 同意申请
    public function getApprove()
    {
        $request = \App\Request::find(Input::get('id'));
        if (!$request) {
            return Lego::message(
                '没有此申请,请在列表中重新选择'
            );
        }
        // 申请相关的 info
        $info = $request->info;

        // 人满不能申请
        $approveNum = $info->requests->where('status', \App\Request::STATUS_申请通过)->count();
        if ($approveNum >= $info->num) {
            return Lego::message(
                '乘坐人已满 ' . $request->info->num . ' 人,不能再同意申请!'
            );
        }
        // 同一天 同一方向 同一人不能被同意两次
        $checkOnlyOne = \App\Request::where('status', \App\Request::STATUS_申请通过)
            ->where('user_id', $request->user_id)
            ->whereHas('info', function ($query) use ($info) {
                return $query->where('go_where', $info->go_where)
                    ->whereBetween('start_at', [$info->start_at->startOfDay(), $info->start_at->endOfDay()]);
            })
            ->first();
        if ($checkOnlyOne) {
            return Lego::message(
                '此人已经被他人抢走啦,不能再同意申请!'
            );
        }
        return Lego::confirm(
            '确认同意此申请? 此操作不能撤回',
            function ($sure) use ($request) {
                if ($sure) {
                    $request->status = \App\Request::STATUS_申请通过;
                    $request->saveOrFail();
                }
            }
        );
    }

    // 发布人驳回申请通过的申请
    public function getReject()
    {
        $request = \App\Request::find(Input::get('id'));
        if (!$request) {
            return Lego::message(
                '没有此申请,请在列表中重新选择'
            );
        }
        return Lego::confirm(
            '确认同意此申请? 此操作不能撤回',
            function ($sure) use ($request) {
                if ($sure) {
                    $request->status = \App\Request::STATUS_申请通过;
                    $request->saveOrFail();
                }
            }
        );
    }

    // 发布人撤回发布的信息
    public function getWithdraw()
    {
        $info = Info::find(Input::get('id'));
        if (!$info) {
            return Lego::message(
                '没有此条信息,请在列表中重新选择撤回'
            );
        }
        return Lego::confirm(
            '确认撤销此条信息吗?请不要频繁撤销!这样会给他人不好的体验!请尽量确认好再发布!经常撤销者影响他人乘坐者会被封号!',
            function ($sure) use ($info) {
                if ($sure) {
                    $info->status = Info::STATUS_撤销;
                    $info->saveOrFail();
                }
            }
        );
    }

    // 我的申请列表
    public function getMyRequest()
    {
        $title = '我的申请';

        $source = \App\Request::with('info.user')->where('created_at', '>=', Carbon::today());
        $filter = Lego::filter($source);
        $filter->addText('info.user.name', '发布人');
        $filter->addText('info.start', '出发地点');
        $filter->addSelect('info.go_where', '申请同行方向')->values(Info::listGoWheres());
        $filter->addSelect('status', '申请状态')->values(\App\Request::listStatus());

        $grid = Lego::grid($filter);

        $grid->add('info.user.name', '发布人');
        $grid->add('info.start', '出发地点');
        $grid->add('info.go_where', '申请同行方向');
        $grid->add('status', '申请状态');
        $grid->add('created_at', '申请时间');
        $grid->add('detail', '详细信息')->cell(function ($_, \App\Request $request) {
            if ($request->status === \App\Request::STATUS_申请通过) {
                return link_to(action('InfoController@getDetail', ['id' => $request->info->id]), '详细车主信息', ['target' => '_blank']);
            }
        });
        $grid->paginate(30);

        return $grid->view('home', compact('title', 'grid'));
    }

    public function getDetail()
    {
        $title = '车主详细信息';

        $id = Input::get('id');
        if (!($info = Info::find($id))) {
            return Lego::message(
                '没有此条车主信息'
            );
        }
        $edit = Lego::form($info);

        $edit->addSelect('go_where', '方向')
            ->values(Info::listGoWheres());
        $edit->addText('start', '出发地');
        $edit->addText('end', '目的地');
        $edit->addNumber('amount_yuan', '费用(人)');
        $edit->addDatetime('start_at', '出发时间');
        $edit->addSelect('num', '空余座位数')
            ->values(Info::listNumbers());
        $edit->addText('plate_number', '车牌号');
        $edit->addText('color', '车身颜色');
        $edit->addText('car_brand', '汽车品牌');
        $edit->addText('mobile', '联系手机号');
        $edit->addText('note', '补充信息');
        $edit->readonly();

        return $edit->view('info.info', compact('title', 'edit'));
    }
}
