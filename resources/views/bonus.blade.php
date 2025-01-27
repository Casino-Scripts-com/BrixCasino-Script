@extends('layout')

@section('content')
<div class="bonus__container">
    <div class="bonus__grid">
        <div class="bonus__daily">
            <div class="bonus__text">
                Получай <span class="bonus_green">бонус каждые 24 часа</span>
            </div>
            <div class="bonus__button">
                <button class="daily_btn">Получить</button>
            </div>
        </div>
        <!-- <div class="bonus__vk">
            <div class="bonus__text">
                Получи бонус за подписку на VK
            </div>
            <div class="bonus__button">
                @if(Auth::check() && Auth::user()->vk_bonus == 0)<button class="vk_bonus">Получить</button>@else<button class="vk_bonus success">Получен</button>@endif
            </div>
        </div> -->
        @php
            $promo_x = [0, 1, 1, 2, 3, 4, 4, 5, 5, 6, 7];
        @endphp
        <div class="bonus__promo">
            <div class="bonus__inner">
                <div class="bonus__promo_group">
                    <div class="input__wrapper">
                        <input type="text" class="promo__input" placeholder="Введите промокод" id="promocode">
                    </div>
                    <div class="bonus__promo_x" style="@if(Auth::user()->rank <= 3) background: linear-gradient(90deg,#535353 0%,#6e6e6e 100%); @elseif(Auth::user()->rank >= 4) background:linear-gradient(90deg,#007330 30%,#0077ff 100%); @elseif(Auth::user()->rank >= 7) background:linear-gradient(90deg,#0077ff 0%,#2ac800 100%); @endif">
                        <p>Ваш множитель: x{{$promo_x[Auth::user()->rank]}}</p>
                    </div>
                </div>
                <div class="bonus__button">
                    <button class="promo__activate_btn">Активировать</button>
                </div>
            </div>
        </div>
        <div class="bonus__tg">
            <div class="bonus__text" style="max-width: 300px;">
                Для безопасности привяжи свой Telegram  
            </div>
            <div class="bonus__button">
            @if(Auth::check() && Auth::user()->tg_id == NULL)
                <button class="tg_bonus bind_tg">Привязать</button>
                @else 
                <button class="tg_bonus success">Привязан</button> 
                @endif
            </div>
        </div>
    </div>
    <div class="bonus__rules">
        <!-- <a href="https://vk.com/zubrix16" class="rules__sub">
            <i class='bx bxl-vk'></i>
        </a> -->
        <a href="https://t.me/Zubrix_BK" class="rules__sub">
            <i class='bx bxl-telegram' ></i>
        </a>
    </div>
</div>

@if(Auth::check() && Auth::user()->tg_id == NULL)
<div class="modal__window modal_window_tg">
    <div class="modal__dialog modal__bind_tg">
        <div class="modal" style="border-radius: 15px;">
            <div class="modal__heading">
                Инструкция по привязке Телеграмма
                <div class="modal__close" style="top:9px;"><i class='bx bx-plus'></i></div>
            </div>
            <div class="modal__content">
                <p class="helps__bing_tg" style="margin-bottom: 7px; margin-top: 0px;">Вам нужно привязать свой Telegram-аккаунт к боту.
                    Скопируйте команду и отправьте боту <a href="https://t.me/ZUBRIX_AUTO_BOT" target="_blank" class="colored-link">t.me/ZUBRIX_AUTO_BOT</a>
                </p>
                <div class="bind__tg_code">
                    <div class="bint_code_form">
                        <input type="text" class="bind_code" disabled value="/bind {{Auth::user()->unique_id}}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection