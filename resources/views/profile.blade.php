@extends('layout')

@section('content')
<div class="profile__container">
    <div class="profile__wrapper">
        <div class="profile__header">
            <div class="profile__name">Личный кабинет</div>
            <a href="/logout" class="profile__logout">Выход</a>
        </div>
        <div class="profile__block_info">
            <div class="profile__block profile__block_balance">
                <div class="profile__title_block">Мой баланс</div>
                <div class="profile__block_wallet">
                    <div class="profile__balance">{{Auth::user()->balance}}</div>
                    <div class="profile__wallet_button">
                        <a href="/wallet/pay">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-in"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                        </a>
                        <a href="/wallet/withdraw">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="profile__block profile__block_rank">
                <div class="profile__title_block rank__title">
                    Ранг
                    <div class="rank__next_bonus">
                        <i class='bx bxs-gift'></i>
                        {{$user_gift}}₽
                    </div>
                </div>
                <div class="profile__user_rank">
                    <img src="/assets/images/ranks/{{Auth::user()->rank}}.png" alt="" class="profile__rank_icon">
                    <div class="profile__progress_rank">
                        <div class="pay__progress">
                            Пополнений:
                            <span class="progress__sum">
                                <span class="user__sum_pay">
                                    {{$user_pays}}
                                </span>
                                /
                                <span class="user__next_sumpay">
                                    {{$max_pays}}
                                </span>
                                ₽
                            </span>
                            <div class="progress__fill" style="width: {{$user_pays * 100 / $max_pays}}%"></div>
                        </div>
                    </div>
                    <img src="/assets/images/ranks/{{Auth::user()->rank + 1}}.png" alt="" class="profile__rank_icon">
                </div>
            </div>
            <div class="profile__block profile__block_social">
                <div class="profile__title_block">Мои привязки</div>
                <div class="binds__user">
                    <div class="bind__user_vk">
                        <i class='bx bxl-vk'></i>
                    </div>
                    @if(Auth::user()->tg_id != NULL)
                    <div class="bind__user_tg">
                        <i class='bx bxl-telegram'></i>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="profile__user">
            <div class="profile__personal_info">
                <div class="profile__user_info">
                    <img src="{{Auth::user()->avatar}}" alt="Аватарка" class="profile__avatar">
                    <img src="/assets/images/ranks/{{Auth::user()->rank}}.png" class="user__rank_icon">
                    <div class="profile__user_fastinfo">
                        <span class="profile__username">{{Auth::user()->username}}</span>
                        <div class="profile__user_id">
                            <input type="text" class="input__user_id" readonly="readonly" value="ID: {{Auth::user()->id}}" id="user_id">
                            <div class="user__copy_id">
                                <i class='bx bx-copy bx-rotate-270' ></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="block__user_info">
                <div class="block__user_inf block__user_sumbet">
                    <span class="block_name_profile">Сумма ставок:</span>
                    <span class="block__kol_profile">{{$sumbet}}</span>
                </div>
                <div class="block__user_inf block__user_bestwin">
                    <span class="block_name_profile">Лучший выигрыш:</span>
                    <span class="block__kol_profile">{{$maxwin}}</span>
                </div>
                <div class="block__user_inf block__user_colgame">
                    <span class="block_name_profile">Всего игр:</span>
                    <span class="block__kol_profile">{{$sumgame}}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection