@extends('admin')

@section('content')
    <div class="withdraws__container">
        <div class="withdraws__body">
            <div class="withdraws__wt">
                @foreach($withdraws as $withdraw)
                <div class="withdraw__wrapper">
                    <div class="withdraw__card_shell">
                        <div class="withdraw__card">
                            <a href="/admin/user/edit/{{$withdraw['user_id']}}" class="withdraw__card_topside" target="_blank">
                                <div class="withdraw__card_time">
                                    <span class="withdraw__time_text" style="color: #6CC100;">{{$withdraw['time']}}</span>
                                </div>
                                <div class="withdraw__user_image_wr">
                                    <img src="{{$withdraw['avatar']}}" class="withdraw__user_image">
                                    <img src="/assets/images/ranks/{{$withdraw['rank']}}.png" class="withdraw__user_image_rank">
                                </div>
                                <div class="withdraw__user_info">
                                    <div class="withdraw__user_username">{{$withdraw['username']}}</div>
                                    <div class="withdraw__sum">
                                        <span>{{$withdraw['amount']}} руб</span>
                                    </div>
                                </div>
                            </a>
                            <div class="withdraw__section">
                                ID:
                                <span class="text-bold">{{$withdraw['id']}}</span>
                                <br>
                                Система:
                                <span class="text-bold">{{$withdraw['system']}}</span>
                                <br>
                                Кошелёк:
                                <span class="text-bold">{{$withdraw['number']}}</span>
                                <br>
                                IP:
                                <span class="text-bold">
                                    <a href="https://iphub.info/?ip={{$withdraw['ip']}}" class="withdraw__link" target="_blank">
                                        {{$withdraw['ip']}}
                                    </a>
                                </span>
                                <br>
                            </div>
                            <div class="withdraw__button_side">
                                <form action="/admin/withdraw/fk/{{$withdraw['id']}}" method="GET" class="withdraw__process_form" style="width: 50%;">
                                    <button type="submit" class="withdraw__confirm">FKwallet</button>
                                </form>
                                <form action="/admin/withdraw/rp/{{$withdraw['id']}}" method="GET" class="withdraw__process_form" style="width: 50%;">
                                    <button type="submit" class="withdraw__confirm">RubPay</button>
                                </form>
                                <a class="withdraw__confirm withdraw__process_cancel" href="/admin/cancel/withdraw/{{$withdraw['id']}}">Отменить</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection