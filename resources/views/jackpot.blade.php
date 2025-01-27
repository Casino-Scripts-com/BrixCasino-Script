@extends('layout')

@section('content')
<div class="mines__container">
    <div class="games__area">
        <div class="games__sidebar">
            <div class="games__input_wrapper_bet">
                <label class="games__sidebar_label">Ставка</label>
                <div class="games__sidebar_wrapper_input">
                    <input type="number" class="games__sidebar_input jackpot_bet" value="0">
                </div>
                <div class="games__sidebar_help_bombs">
                    <button class="games__sidebar_bombs_action" onclick="$('.jackpot_bet').val(+$('.jackpot_bet').val() + 1)">+1</button>
                    <button class="games__sidebar_bombs_action" onclick="$('.jackpot_bet').val(+$('.jackpot_bet').val() + 10)">+10</button>
                    <button class="games__sidebar_bombs_action" onclick="$('.jackpot_bet').val(+$('.jackpot_bet').val() + 100)">+100</button>
                    <button class="games__sidebar_bombs_action" onclick="$('.jackpot_bet').val(1);">Min</button>
                    <button class="games__sidebar_bombs_action" onclick="$('.jackpot_bet').val($('#balance').text());">Max</button>
                </div>
            </div>
            <div class="games__sidebar_play_button">
                <button class="sidebar__play jackpot__bet" onclick="jackpot()">Играть</button>
            </div>
        </div>
        <div class="games__field">
            <div class="jackpot__field_wrapper">
                <div class="players__wrapper">
                    <ul class="players__jackpot" style="cursor: grab; transform: translate3d(0px, 0px, 0px); perspective: 1000px; backface-visibility: hidden;">
                        @foreach($bets as $bet)
                        <li class="player__avatar_card">
                            <img src="{{$bet->avatar}}" class="avatar__jackpot">
                            <img src="/assets/images/ranks/{{$bet->rank}}.png" class="avatar__jackpot_rank">
                            <div class="player__jackpot_bet">{{$bet->sum}}</div>
                            <div class="player__jackpot_ticket">{{$bet->from}}-{{$bet->to}}</div>
                        </li>
                        @endforeach
                    </ul>
                    <div class="jackpot__winner" style="display:none;">
                        <span class="winner__title">Победитель</span>
                        <div class="winner__username"></div>
                        <div class="winner__sum_title"> 
                            Выиграл
                            <span class="winner__sum"></span>
                        </div>
                        <div class="winner__sum_title"> 
                            Шанс
                            <span class="winner__chance"></span>
                        </div>
                    </div>
                </div>
                <div class="jackpot__timer">
                    <span class="jackpot__time jackpot_time">00:20</span>
                </div>
                <div class="jackpot__bank">
                    <span class="jackpot__bank_title">Банк:<span class="jackpot__bank_game">{{$game->price}}</span></span>
                </div>
                <div class="jackpot__roll" style="display: none;">
                    <div class="inbox">
                        <div class="jackpot__players">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection