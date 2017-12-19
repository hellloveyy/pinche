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
                                    <div class="markdown-body">
                                        <h1 id="toc_0">一、我是车主</h1>

                                        <p><img style="width: 100%;height: 100%" src="http://7xnaaf.com1.z0.glb.clouddn.com/chezhu.jpeg" alt="chezhu"/>￼</p>

                                        <h3 id="toc_1">a.历史行程</h3>
                                        <p>车主点击历史行程，可以看到所有<strong>当前</strong>时间之前发布的行程，点击<strong>修改时间等信息重新发布</strong>可以编辑之前发布过的行程，重新选择时间等信息点击发布。之后关闭页面，乘客即可在<strong>我是乘客-&gt;车找人列表</strong>之中看到</p>
                                        <h3 id="toc_2">b.发布车找人</h3>
                                        <p>车主新建一个车找人信息，填写完毕之后点击保存发布。之后关闭页面，乘客即可在<strong>我是乘客-&gt;车找人列表</strong>之中看到</p>
                                        <h3 id="toc_3">c.人找车列表</h3>
                                        <p>车主可以在此列表查看到，所有<strong>当前</strong>时间之后的人找车信息。</p>
                                        <h3 id="toc_4">d.我的发布</h3>
                                        <p>车主可以看到所有<strong>当前</strong>时间之后的自己发布的行程，可以在这个列表里面发现两个按钮<br/>
                                            <strong>确认车已满：</strong>当车主确认乘车人已经拼满，请点击此按钮，这条你发布的行程将会在<strong>我是乘客-&gt;车找人列表</strong>之中下架，这样乘车人就不会看到您的行程了。<br/>
                                            <strong>编辑：</strong>当车主发现自己的信息写的有误的时候，比如手机号填写错误，车主可以点击编辑之后保存，更新行程<br/>
                                            <strong>撤销发布：</strong>当车主发现自己发布的行程有误，或者行程有变动的时候，可以点击撤销发布</p>

                                        <h1 id="toc_5">二、我是乘客</h1>
                                        <p><img style="width: 100%;height: 100%" src="http://7xnaaf.com1.z0.glb.clouddn.com/chengke.jpeg" alt="chengke"/>￼</p>
                                        <h3 id="toc_6">a.发布人找车</h3>
                                        <p>乘客新建一个人找车信息，填写完毕之后点击保存发布。之后关闭页面，车主即可在<strong>我是车主-&gt;人找车列表</strong>之中看到</p>
                                        <h3 id="toc_7">b.车找人列表</h3>
                                        <p>乘客可以在此列表查看到，所有<strong>当前</strong>时间之后的车找人信息。</p>
                                        <h3 id="toc_8">c.我的寻车</h3>
                                        <p>乘客可以看到所有<strong>当前</strong>时间之后的自己发布的寻车，可以在这个列表里面发现两个按钮<br/>
                                            <strong>确认车已满：</strong>当乘车人确认已经找到车，请点击此按钮，这条你发布的寻车将会在<strong>我是车主-&gt;人找车列表</strong>之中下架，这样车主就不会看到您的行程了。<br/>
                                            <strong>编辑：</strong>当乘车人发现自己的信息写的有误的时候，比如手机号填写错误，乘车人可以点击编辑之后保存，更新寻车<br/>
                                            <strong>撤销发布：</strong>当乘车人发现自己发布的寻车有误，或者行程有变动的时候，可以点击撤销寻车</p>

                                        <h1 id="toc_9">三、我</h1>
                                        <p><img style="width: 100%;height: 100%" src="http://7xnaaf.com1.z0.glb.clouddn.com/wo.jpeg" alt="wo"/>￼</p>
                                        <h3 id="toc_10">注册会员</h3>
                                        <p>点击此按钮注册京香拼车的会员，注册成功之后会显示您的信息，关闭此页面即可操作其他功能</p>
                                        <h3 id="toc_11">使用须知</h3>
                                        <p>使用须知</p>
                                        <h3 id="toc_12">身份认证</h3>
                                        <p>敬请期待。。。。开发中</p>
                                    </div>
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
