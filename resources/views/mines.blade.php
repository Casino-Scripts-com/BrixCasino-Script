@extends('layout')

@section('content')
<div class="mines__container">
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
            <div class="games__input_wrapper_bombs">
                <label class="games__sidebar_label">Количество бомб</label>
                <div class="games__sidebar_wrapper_input">
                    <input type="number" class="games__sidebar_input input__bombs" min="2" max="24" value="3" id="InputBombs">
                </div>
                <div class="games__sidebar_help_bombs">
                    <button class="games__sidebar_bombs_action" onclick="$('#InputBombs').val(3);getItems()">3</button>
                    <button class="games__sidebar_bombs_action" onclick="$('#InputBombs').val(5);getItems()">5</button>
                    <button class="games__sidebar_bombs_action" onclick="$('#InputBombs').val(10);getItems()">10</button>
                    <button class="games__sidebar_bombs_action" onclick="$('#InputBombs').val(20);getItems()">20</button>
                    <button class="games__sidebar_bombs_action" onclick="$('#InputBombs').val(24);getItems()">24</button>
                </div>
            </div>
            <div class="games__sidebar_play_button">
                <button class="sidebar__play">Играть</button>
                <button class="sidebar__take_win" style="display: none;">Забрать <span id="win"></span>₽</button>
            </div>
        </div>
        <div class="games__field">
            <script src="/assets/js/mines.js"></script>
            <div class="game__field_mines">
                <div class="game__mines_coefs">
                    <div class="game__mines_coef"></div>
                </div>   
                <div class="games__area_field">
                    <div class="mines__field">
                        <button type="button" data-number="1" disabled class="mines__cell"></button>
                        <button type="button" data-number="2" disabled class="mines__cell"></button>
                        <button type="button" data-number="3" disabled class="mines__cell"></button>
                        <button type="button" data-number="4" disabled class="mines__cell"></button>
                        <button type="button" data-number="5" disabled class="mines__cell"></button>
                        <button type="button" data-number="6" disabled class="mines__cell"></button>
                        <button type="button" data-number="7" disabled class="mines__cell"></button>
                        <button type="button" data-number="8" disabled class="mines__cell"></button>
                        <button type="button" data-number="9" disabled class="mines__cell"></button>
                        <button type="button" data-number="10" disabled class="mines__cell"></button>
                        <button type="button" data-number="11" disabled class="mines__cell"></button>
                        <button type="button" data-number="12" disabled class="mines__cell"></button>
                        <button type="button" data-number="13" disabled class="mines__cell"></button>
                        <button type="button" data-number="14" disabled class="mines__cell"></button>
                        <button type="button" data-number="15" disabled class="mines__cell"></button>
                        <button type="button" data-number="16" disabled class="mines__cell"></button>
                        <button type="button" data-number="17" disabled class="mines__cell"></button>
                        <button type="button" data-number="18" disabled class="mines__cell"></button>
                        <button type="button" data-number="19" disabled class="mines__cell"></button>
                        <button type="button" data-number="20" disabled class="mines__cell"></button>
                        <button type="button" data-number="21" disabled class="mines__cell"></button>
                        <button type="button" data-number="22" disabled class="mines__cell"></button>
                        <button type="button" data-number="23" disabled class="mines__cell"></button>
                        <button type="button" data-number="24" disabled class="mines__cell"></button>
                        <button type="button" data-number="25" disabled class="mines__cell"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection