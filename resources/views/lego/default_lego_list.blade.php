@extends('layouts.app')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="ibox">

        <div class="ibox-title">
            @if(isset($back) && $back)
                <div class="pull-right">
                    <button class="btn btn-white btn-sm go-back hide"><i class="fa fa-arrow-left"></i> 返回</button>
                </div>
            @endif

            <h2>{{ $title or '筛选' }}</h2></div>

        <div class="ibox-content">
            {!! $grid !!}
        </div>
    </div>

    <script>
        _defer.push(function () {
            if (history.length > 1) {
                $('.go-back').removeClass('hide').on('click', function () {
                    history.go(-1);
                });
            }
        })
    </script>
@endsection
