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
                                        <li><a href="http://7xnaaf.com1.z0.glb.clouddn.com/%E7%99%BB%E5%BD%95:%E6%B3%A8%E5%86%8C:%E6%89%BE%E5%9B%9E%E5%AF%86%E7%A0%81.pdf">登录/注册/找回密码</a></li>
                                        <li><a href="http://7xnaaf.com1.z0.glb.clouddn.com/%E5%88%97%E8%A1%A8%E9%A1%B5%E4%BB%A5%E5%8F%8A%E4%B8%BB%E8%A6%81%E6%B5%81%E7%A8%8B.pdf">列表页以及主要流程</a></li>
                                        <li><a href="http://7xnaaf.com1.z0.glb.clouddn.com/%E5%88%9B%E5%BB%BA%E5%8F%91%E5%B8%83%E4%BF%A1%E6%81%AF.pdf">创建发布信息|历史列表|我的申请</a></li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <strong>视频教程:</strong>
                                <hr>
                                <div>
                                    <ul>
                                        <li>
                                            <a href="#">没有太多时间,以后会录制视频教程</a>
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
