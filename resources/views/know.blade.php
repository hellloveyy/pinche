@extends('layouts.app')

@section('title')
    {{ $title }}
@endsection

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">{{ $title ?? '拼车信息表' }}</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-8">
                                <strong>文档教程:</strong>
                                <hr>
                                <div>
                                    <ol>
                                        <li><a href="#">登录/注册/找回密码</a></li>
                                        <li><a href="#">列表页以及主要流程</a></li>
                                        <li><a href="#">创建发布信息|历史列表|我的申请</a></li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <strong>视频教程:</strong>
                                <hr>
                                <div>
                                    <ul>
                                        <li>
                                            <a href="https://v.qq.com/x/page/i0516k6v2mr.html">公众号使用流程准备中...</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
