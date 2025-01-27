@extends('layout')

@section('content')
@if($bet)
<script>
    var game_active = '{{ $gameStatus }}';
    var bet = parseInt('{{ $bet->price }}');
    var isCashout = false;
    var withdraw = parseFloat('{{ $bet->withdraw }}');
</script>
@else
<script>
    var game_active = '{{ $gameStatus }}';
    var bet;
    var isCashout;
    var withdraw;
</script>
@endif
<script src="/assets/js/jquery.flot.min.js"></script>
<script src="/assets/js/chart.js"></script>
<script src="/assets/js/crash.js"></script>
<div class="mines__container">
    <div class="games__area">
        <div class="games__sidebar">
            <div class="games__input_wrapper_bet">
                <label class="games__sidebar_label">Ставка</label>
                <div class="games__sidebar_wrapper_input">
                    <input type="number" class="games__sidebar_input crash_bet" value="0" id="sum">
                </div>
                <div class="games__sidebar_help_bombs">
                    <button class="games__sidebar_bombs_action" onclick="$('.crash_bet').val(+$('.crash_bet').val() + 1)">+1</button>
                    <button class="games__sidebar_bombs_action" onclick="$('.crash_bet').val(+$('.crash_bet').val() + 10)">+10</button>
                    <button class="games__sidebar_bombs_action" onclick="$('.crash_bet').val(+$('.crash_bet').val() + 100)">+100</button>
                    <button class="games__sidebar_bombs_action" onclick="$('.crash_bet').val(1);">Min</button>
                    <button class="games__sidebar_bombs_action" onclick="$('.crash_bet').val($('#balance').text());">Max</button>
                </div>
            </div>
            <div class="games__input_wrapper_bet">
                <label class="games__sidebar_label">Автовывод</label>
                <div class="games__sidebar_wrapper_input">
                    <input type="number" class="games__sidebar_input crash_auto" value="2.00" id="betout">
                </div>
                <div class="games__sidebar_help_bombs">
                    <button class="games__sidebar_bombs_action" onclick="$('.crash_auto').val(1.1)">1.1</button>
                    <button class="games__sidebar_bombs_action" onclick="$('.crash_auto').val(1.5)">1.5</button>
                    <button class="games__sidebar_bombs_action" onclick="$('.crash_auto').val(2)">2</button>
                    <button class="games__sidebar_bombs_action" onclick="$('.crash_auto').val(5);">5</button>
                    <button class="games__sidebar_bombs_action" onclick="$('.crash_auto').val(10);">10</button>
                </div>
            </div>
            <div class="games__sidebar_play_button">
                <button class="sidebar__play crash__play">Играть</button>
            </div>
        </div>
        <div class="games__field">
            <div class="game__field_crash">
                <div class="game__crash_coefs">
                    @foreach($history as $m)
                        <div class="game__crash_coef">
                            <div class="crash__coef" style="color: {{ $m->color }}; border-color: {{ $m->color }};">
                                {{ number_format($m->multiplier, 2, '.', '') }}x
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="game__crash">
                    <div class="crash__block">
                        <div class="crash">
                            <div class="chart" id="chart" style="height:200px;"></div>
                            <div class="chart-info">Загрузка</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="crash__history">
        <div class="crash__bets">
            @foreach($game['bets'] as $bet)
            <div class="crash__bet">
                <div class="crash__bet_user_wr">
                    <div class="crash__bet_user_image_wr">
                        <img src="{{ $bet['user']['avatar'] }}" class="crash__bet_user_image">
                        <img src="/assets/images/ranks/{{ $bet['user']['rank'] }}.png" class="crash__bet_user_rank">
                    </div>
                    <div class="crash__bet_username_wr">
                        <div class="crash__bet_username">{{ $bet['user']['username'] }}</div>
                    </div>
                </div>
                @if(!$bet['status'] == 1)
                <div class="crash__bet_values">
                    <div class="crash__bet_value crash__bet_sum">{{ $bet['price'] }}</div>
                    <div class="crash__bet_value crash__bet_coef">В игре</div>
                    <div class="crash__bet_value crash__bet_win @if(!$bet['status'] == 1) crash__bet_win_hidden @endif"></div>
                </div>
                @else
                <div class="crash__bet_values">
                    <div class="crash__bet_value crash__bet_sum">{{ $bet['price'] }}</div>
                    <div class="crash__bet_value crash__bet_coef">{{ $bet['withdraw'] }}x</div>
                    <div class="crash__bet_value crash__bet_win @if(!$bet['status'] == 1) crash__bet_win_hidden @endif">{{ $bet['won'] }}</div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection