@extends('layout')

@section('content')
<div class="wheel__container">
    <div class="games__area">
        <div class="games__sidebar">
            <div class="games__input_wrapper_bet">
                <label class="games__sidebar_label">Ставка</label>
                <div class="games__sidebar_wrapper_input">
                    <input type="number" class="games__sidebar_input input__bet" value="0">
                </div>
                <div class="games__sidebar_help_bombs">
                    <button class="games__sidebar_bombs_action" onclick="$('.input__bet').val(+$('.input__bet').val() + 1)">+1</button>
                    <button class="games__sidebar_bombs_action" onclick="$('.input__bet').val(+$('.input__bet').val() + 10)">+10</button>
                    <button class="games__sidebar_bombs_action" onclick="$('.input__bet').val(+$('.input__bet').val() + 100)">+100</button>
                    <button class="games__sidebar_bombs_action" onclick="$('.input__bet').val(1);">Min</button>
                    <button class="games__sidebar_bombs_action" onclick="$('.input__bet').val($('#balance').text());">Max</button>
                </div>
            </div>
            <div class="wheel__colors">
                <div class="wheel__bet_color wheel__x2" onclick="wheelBet('black');" onkeydown="if(event.keyCode==13){return false;}">x2</div>
                <div class="wheel__bet_color wheel__x3" onclick="wheelBet('yellow');" onkeydown="if(event.keyCode==13){return false;}">x3</div>
                <div class="wheel__bet_color wheel__x5" onclick="wheelBet('red');" onkeydown="if(event.keyCode==13){return false;}">x5</div>
                <div class="wheel__bet_color wheel__x50" onclick="wheelBet('green');" onkeydown="if(event.keyCode==13){return false;}">x50</div>
            </div>
            @if(Auth::check() && Auth::user()->admin == 1)
            <p style="color: #FFFFFF; font-weight: 500; font-size: 16px; margin-top: 15px;">Админ-цвета</p>
            <div class="wheel__colors">
                <div class="wheel__bet_color wheel__x2" onclick="wheelColor('black');">x2</div>
                <div class="wheel__bet_color wheel__x3" onclick="wheelColor('yellow');">x3</div>
                <div class="wheel__bet_color wheel__x5" onclick="wheelColor('red');">x5</div>
                <div class="wheel__bet_color wheel__x50" onclick="wheelColor('green');">x50</div>
            </div>
            @endif
        </div>
        <div class="games__field">
            <div class="game__field_mines">
                <div class="game__wheel_coefs">
                    <div class="game__wheel_coef">
                        @foreach($history as $wheelhs)
                            <div class="history__line history__line--{{$wheelhs->winner_color}}"></div>
                        @endforeach
                    </div>
                </div>   
                <div class="game__wheel">
                    <div class="wheel__timer">
                        <span class="time" id="wheelTime">15</span>
                    </div>
                    <div class="wheel__pointer"></div>
                    <div class="wheel__row" style="transform: rotate(2deg); transition: transform 0s linear 0s;" id="wheelSpin">
                        <img src="/assets/images/wheeld.svg">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="wheel__bet_history">
        <div class="wheel__colors_history">
            <div class="wheel__history_color wheel__color_x2">
                <div class="wheel__history_col_head" onclick="wheelBet('black');" onkeydown="if(event.keyCode==13){return false;}">
                    <span class="bet__money" data-bank="black">{{$bank[0]}}</span>
                    <span class="">x2</span>
                </div>
                <div class="wheel__history_bets_color">
                    <div class="wheel__history_bet_color">

                    </div>
                </div>
            </div>
            <div class="wheel__history_color wheel__color_x3">
                <div class="wheel__history_col_head" onclick="wheelBet('yellow');" onkeydown="if(event.keyCode==13){return false;}">
                    <span class="bet__money" data-bank="yellow">{{$bank[1]}}</span>
                    <span class="">x3</span>
                </div>
                <div class="wheel__history_bets_color">
                    <div class="wheel__history_bet_color">
                        
                    </div>
                </div>
            </div>
            <div class="wheel__history_color wheel__color_x5">
                <div class="wheel__history_col_head" onclick="wheelBet('red');" onkeydown="if(event.keyCode==13){return false;}">
                    <span class="bet__money" data-bank="red">{{$bank[2]}}</span>
                    <span class="">x5</span>
                </div>
                <div class="wheel__history_bets_color">
                    <div class="wheel__history_bet_color">
                        
                    </div>
                </div>
            </div>
            <div class="wheel__history_color wheel__color_x50" onclick="wheelBet('green');" onkeydown="if(event.keyCode==13){return false;}">
                <div class="wheel__history_col_head">
                    <span class="bet__money" data-bank="green">{{$bank[3]}}</span>
                    <span class="">x50</span>
                </div>
                <div class="wheel__history_bets_color">
                    <div class="wheel__history_bet_color">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection