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

    /**
     * 发布车找人
     */
    public function anyCreateCar()
    {
        $title = '发布车找人';

        $id = Input::get('id');
        $status = Input::get('status');

        $edit = Lego::form(Info::find($id) ?? new Info());

        $edit->addSelect('go_where', '方向')
            ->values(Info::listGoWheres());
        $edit->addText('start', '出发地');
        $edit->addText('end', '目的地');
        $edit->addNumber('amount_yuan', '费用(人)')
            ->note('请大家维持原价');
        $edit->addTime('start_at', '出发时间')//测试 time 是否可以适应 ios 新系统
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
        $edit->addHidden('cate')->default(Info::CATE_车找人);
        if ($status === Info::STATUS_进行中) {
            $edit->addHidden('status')->default(Info::STATUS_进行中);
        }
        $edit->required();

        Info::saving(function (Info $info) {
            // 同一天同一人同一方向只能有一条有效信息,防止有人多次刷屏 || 超管可以录入
            if (Info::where('status', Info::STATUS_进行中)
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

    /**
     * 发布人找车
     */
    public function anyCreatePeople()
    {
        $title = '发布人找车';

        $id = Input::get('id');

        $edit = Lego::form(Info::find($id) ?? new Info());

        $edit->addSelect('go_where', '方向')
            ->values(Info::listGoWheres());
        $edit->addText('start', '出发地');
        $edit->addText('end', '目的地');
        $edit->addNumber('amount_yuan', '费用(人)')
            ->note('请大家维持原价');
        $edit->addDatetime('start_at', '出发时间')
            ->note('请仔细检查出发时间!');
        $edit->addText('mobile', '联系手机号');
        $edit->addText('note', '补充信息');
        $edit->addHidden('user_id')->default(Auth::id());
        $edit->addHidden('cate')->default(Info::CATE_人找车);
        $edit->required();

        return $edit->view('info.info', compact('title', 'edit'));
    }

    /**
     * 车主历史发布
     */
    public function getHistoryList()
    {
        $title = '历史信息';
        $source = Info::where('user_id', Auth::id())
            ->where('start_at', '<', Carbon::now())
            ->where('cate', Info::CATE_车找人);

        $grid = Lego::grid($source);

        $grid->add('id', '编辑详细')->cell(function ($_, Info $info) {
            return link_to(action('InfoController@anyCreateCar', ['id' => $info->id, 'status' => Info::STATUS_进行中])
                , '修改时间等信息重新发布'
                , ['target' => '_blank']);
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
        $grid->add('num', '座位数');
        $grid->add('mobile', '手机号');
        $grid->add('plate_number', '车牌号');
        $grid->add('color', '车身颜色');
        $grid->add('car_brand', '汽车品牌');
        $grid->add('amount_yuan', '费用(人)');
        $grid->add('note', '补充');
        $grid->paginate(15)->orderBy('start_at', 'desc');

        return $grid->view('home', compact('title', 'grid'));
    }

    /**
     * 车主确认已拼满
     */
    public function getFullPeople()
    {
        $info = Info::find(Input::get('id'));
        if (!$info) {
            return Lego::message(
                '没有此条信息,请在列表中重新选择'
            );
        }
        return Lego::confirm(
            '请确认已经拼满车?此操作不可逆,如果乘车人取消或者有变动请重新发布车找人!确认之后将在乘客的车找人列表中下架!',
            function ($sure) use ($info) {
                if ($sure) {
                    $info->status = Info::STATUS_车满;
                    $info->saveOrFail();
                }
            }
        );
    }

    /**
     * 寻车成功
     */
    public function getFindCar()
    {
        $info = Info::find(Input::get('id'));
        if (!$info) {
            return Lego::message(
                '没有此条信息,请在列表中重新选择'
            );
        }
        return Lego::confirm(
            '请确认已经拼到车?此操作不可逆,如果车主取消或者有变动请重新发布人找车!确认之后将在车主的人找车列表中下架!',
            function ($sure) use ($info) {
                if ($sure) {
                    $info->status = Info::STATUS_寻车成功;
                    $info->saveOrFail();
                }
            }
        );
    }


}
