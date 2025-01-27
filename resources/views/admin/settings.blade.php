@extends('admin')

@section('content')
<div class="settings__inner">
    <div class="settings__site">
        <p class="settings__head">Общие настройки сайта</p>
        <form action="/admin/setting/save" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <div class="settings__inputs">
                <div class="settings__input">
                    <label>Минимальный вывод</label>
                    <input type="number" value="{{$settings->min_withdraw}}" required name="min_with">
                </div>
                <div class="settings__input">
                    <label>Минимальный депозит</label>
                    <input type="number" value="{{$settings->min_payment}}" required name="min_pay">
                </div>
                <div class="settings__input">
                    <label>Тех.работы</label>
                    <select class="select__setting" name="tech" required>
                        <option value="0" @if($settings->tech_work == 0) selected @endif>Нет</option>
                        <option value="1" @if($settings->tech_work == 1) selected @endif>Да</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="save__settings">Сохранить</button>
        </form>
    </div>
    <div class="settings__games">
        <p class="settings__head">Настройки режимов</p>
        <div class="setting__game">
            <div class="setting__game_name">DICE</div>
            <div class="game__toggle dice_enabled">
                <label class="game__toggle_btn game__toggle_dice @if($settings->dice_enabled == 0) active @else no_active @endif">
                    <input disabled="" type="checkbox">
                    <span class="toggle__switch_game"></span>
                </label>
            </div>
        </div>
        <div class="setting__game">
            <div class="setting__game_name">MINES</div>
            <div class="game__toggle mines_enabled">
                <label class="game__toggle_btn game__toggle_mines @if($settings->mines_enabled == 0) active @else no_active @endif">
                    <input disabled="" type="checkbox">
                    <span class="toggle__switch_game"></span>
                </label>
            </div>
        </div>
        <div class="setting__game">
            <div class="setting__game_name">WHEEL</div>
            <div class="game__toggle wheel_enabled">
                <label class="game__toggle_btn game__toggle_wheel @if($settings->wheel_enabled == 0) active @else no_active @endif">
                    <input disabled="" type="checkbox">
                    <span class="toggle__switch_game"></span>
                </label>
            </div>
        </div>
        <div class="setting__game">
            <div class="setting__game_name">JACKPOT</div>
            <div class="game__toggle jackpot_enabled">
                <label class="game__toggle_btn game__toggle_jackpot @if($settings->jackpot_enabled == 0) active @else no_active @endif">
                    <input disabled="" type="checkbox">
                    <span class="toggle__switch_game"></span>
                </label>
            </div>
        </div>
    </div>
    <div class="reboot__games">
        <p class="settings__head">Перезапуск режимов</p>
        <div class="reboot__btns_game">
            <div class="reboot__btn_game restartWheel">Reload Wheel</div>
            <div class="reboot__btn_game stopWheel">Stop Wheel</div>
        </div>
    </div>
</div>
@endsection