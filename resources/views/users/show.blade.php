@extends('layouts.default')
@section('title', $user->name)

@section('content')
    <div class="row">
        <div class="offset-md-2 col-md-8">
            <div class="col-md-12">
                <div class="offset-md-2 col-md-8">
                    <section class="user_info">
                        @include('shared._user_info', ['user' => $user])
                        {{ $user->name }} - {{ $user->email }}   <!--显示name和email-->
                    </section>

                    @if (Auth::check())
                        @include('users._follow_form')
                    @endif

                    <section class="stats mt-2">
                        @include('shared._stats', ['user' => $user]) <!--显示followers&followings-->
                    </section>

                    <section class="status">
                        @if ($statuses->count() > 0)
                            <ul class="list-unstyled">
                                @foreach ($statuses as $status)
                                    @include('statuses._status')   <!--lists of weibos-->
                                @endforeach
                            </ul>
                            <div class="mt-5">
                                {!! $statuses->render() !!}  <!--渲染页码用{！！     ！！} }，render()：html代码默认用bootstrap链接统一带 page?= -->
                            </div>
                        @else
                            <p>No Weibo for now!</p>
                        @endif
                    </section>

                </div>
            </div>
        </div>
    </div>

@stop