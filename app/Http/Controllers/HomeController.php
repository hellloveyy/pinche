<?php

namespace App\Http\Controllers;

use App\Info;
use App\User;
use Carbon\Carbon;
use Encore\Admin\Form\Field\Html;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Lego\Lego;
use Lego\Widget\Filter;

class HomeController extends Controller
{
    public function getCarFindPeople()
    {
        $title = '车找人列表';

        $filter = Lego::filter(Info::where('start_at', '>=', Carbon::now())
            ->where('cate', Info::CATE_车找人)
            ->where('status', Info::STATUS_进行中)
        );
        $filter->addSelect('go_where', '行进方向')->values(Info::listGoWheres());
        $filter->addText('start', '出发地');
        $filter->addText('end', '目的地');
        $filter->addDatetimeRange('start_at', '出发时间');
        $filter->addText('note', '补充');

        $grid = Lego::grid($filter);

        $grid->add('id', '编号');
        $grid->add('go_where', '行进方向');
        $grid->add('start', '出发地')->cell(function ($_, Info $info) {
            return new HtmlString("<span style='color:red'>{$info->start}</span>");
        });
        $grid->add('end', '目的地')->cell(function ($_, Info $info) {
            return new HtmlString("<span style='color:red'>{$info->end}</span>");
        });
        $grid->add('start_at', '出发时间')->cell(function ($_, Info $info) {
            return new HtmlString("<span style='color:red'>{$info->start_at}</span>");
        });
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
        $grid->add('mobile', '手机号')->cell(function ($_, Info $info) {
            return link_to('tel:' . $info->mobile, '点击拨打');
        });
        $grid->add('plate_number', '车牌号');
        $grid->add('color', '车身颜色');
        $grid->add('car_brand', '汽车品牌');
        $grid->add('amount_yuan', '费用(人)');
        $grid->add('note', '途径点信息');
        $grid->paginate(15)->orderBy('start_at', 'desc');

        return $grid->view('home', compact('title', 'grid'));
    }

    public function getPeopleFindCar()
    {
        $title = '人找车列表';

        $filter = Lego::filter(Info::where('start_at', '>=', Carbon::now())
            ->where('cate', Info::CATE_人找车)
            ->where('status', Info::STATUS_进行中)
        );
        $filter->addSelect('go_where', '行进方向')->values(Info::listGoWheres());
        $filter->addText('start', '出发地');
        $filter->addText('end', '目的地');
        $filter->addDatetimeRange('start_at', '出发时间');

        $grid = Lego::grid($filter);

        $grid->add('id', '编号');
        $grid->add('go_where', '行进方向');
        $grid->add('start', '出发地')->cell(function ($_, Info $info) {
            return new HtmlString("<span style='color:red'>{$info->start}</span>");
        });
        $grid->add('end', '目的地')->cell(function ($_, Info $info) {
            return new HtmlString("<span style='color:red'>{$info->end}</span>");
        });
        $grid->add('start_at', '出发时间')->cell(function ($_, Info $info) {
            return new HtmlString("<span style='color:red'>{$info->start_at}</span>");
        });
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
        $grid->add('mobile', '手机号')->cell(function ($_, Info $info) {
            return link_to('tel:' . $info->mobile, '点击拨打');
        });
        $grid->add('note', '补充信息');
        $grid->paginate(15)->orderBy('start_at', 'desc');

        return $grid->view('home', compact('title', 'grid'));
    }

    /**
     * 我的发布
     */
    public function getMyTripCar()
    {
        $this->middleware('auth');
        $title = '我的发布';

        $grid = Lego::grid(Info::where('start_at', '>=', Carbon::now())
            ->where('user_id', Auth::id())
            ->where('cate', Info::CATE_车找人)
        );

        $grid->add('id', '操作')->cell(function ($_, Info $info) {
            return in_array($info->status, [Info::STATUS_撤销, Info::STATUS_车满])
                ? '当前状态无可用操作'
                : (link_to(action('InfoController@getFullPeople', ['id' => $info->id]), '确认车已满', ['target' => '_blank'])
                    . ' | '
                    . link_to(action('InfoController@anyCreateCar', ['id' => $info->id]), '编辑', ['target' => '_blank'])
                    . ' | '
                    . link_to(action('InfoController@getRevokeCar', ['id' => $info->id]), '撤销行程', ['target' => '_blank'])
                );
        });
        $grid->add('go_where', '行进方向');
        $grid->add('start', '出发地');
        $grid->add('end', '目的地');
        $grid->add('status', '行程状态')->cell(function ($_, Info $info) {
            if ($info->status === Info::STATUS_车满) {
                return new HtmlString("<span style='color:red'>!车满!</span>");
            }
            if ($info->status === Info::STATUS_撤销) {
                return new HtmlString("<span style='color:red'>!撤销!</span>");
            }
            return new HtmlString("<span style='color:green'>{$info->status}</span>");
        });
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
     * 我的寻车
     */
    public function getMyTripPeople()
    {
        $this->middleware('auth');
        $title = '我的寻车';

        $grid = Lego::grid(Info::where('start_at', '>=', Carbon::now())
            ->where('user_id', Auth::id())
            ->where('cate', Info::CATE_人找车)
        );

        $grid->add('id', '操作')->cell(function ($_, Info $info) {
            return in_array($info->status, [Info::STATUS_撤销, Info::STATUS_寻车成功])
                ? '当前状态无可用操作'
                : (link_to(action('InfoController@anyCreatePeople', ['id' => $info->id]), '编辑', ['target' => '_blank'])
                    . ' | '
                    . link_to(action('InfoController@getFindCar', ['id' => $info->id]), '寻车成功', ['target' => '_blank'])
                    . ' | '
                    . link_to(action('InfoController@getRevokePeople', ['id' => $info->id]), '撤销寻车', ['target' => '_blank'])
                );
        });
        $grid->add('go_where', '行进方向');
        $grid->add('start', '出发地');
        $grid->add('end', '目的地');
        $grid->add('start_at', '出发时间');
        $grid->add('status', '寻车状态')->cell(function ($_, Info $info) {
            if ($info->status === Info::STATUS_寻车成功) {
                return new HtmlString("<span style='color:red'>!寻车成功!</span>");
            }
            if ($info->status === Info::STATUS_撤销) {
                return new HtmlString("<span style='color:red'>!撤销!</span>");
            }
            return new HtmlString("<span style='color:green'>{$info->status}</span>");
        });
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
        $grid->add('mobile', '手机号');
        $grid->add('note', '补充');
        $grid->paginate(15)->orderBy('start_at', 'desc');

        return $grid->view('home', compact('title', 'grid'));
    }

    public function getHasKnow()
    {
        $title = '使用须知';
        return view('know', compact('title'));
    }

    public function getDisclaimer()
    {
        $title = '免责声明';
        return view('disclaimer', compact('title'));
    }
}
