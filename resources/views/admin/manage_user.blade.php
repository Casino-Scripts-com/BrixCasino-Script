@extends('admin')

@section('content')
<div class="user__container">
        <div class="user__manage_info">
            <div class="manage__username">
                <img src="{{$user->avatar}}" class="user__avatar_mg">
                <img src="/assets/images/ranks/{{$user->rank}}.png" class="user__rank_icon">
                <div class="manage__personal_info">
                    <span class="username__personal">{{$user->username}}</span>
                    <div class="id__personal">
                        <input type="text" class="input__user_id" readonly="readonly" value="ID: {{$user->id}}" id="user_id">
                        <div class="manage__copy_id">
                            <i class='bx bx-copy bx-rotate-270' ></i>
                        </div>
                    </div>
                </div>
                <div class="user__personal_securite">
                    <form action="/admin/user/save" method="POST" novalidate>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <input name="id" value="{{$user->id}}" type="hidden" required>
                        <div class="user__manipulation">
                            <div class="manage__user_fast_info manage_user__balance">
                                <label class="manage__user_label">Баланс</label>
                                <input type="number" required class="user__balance" placeholder="Баланс" name="balance" value="{{$user->balance}}">
                            </div>
                            <div class="manage__user_fast_info manage__user_ip">
                            <label class="manage__user_label">IP</label>
                                <input type="text" class="user__ip" value="{{$user->ip}}" disabled>
                            </div>
                        </div>
                        <div class="user__manipulation">
                            <div class="manage__user_fast_info manage_user__balance">
                            <label class="manage__user_label">Реферер</label>
                                <input type="text" class="user__balance" disabled value="{{$user_ref}}">
                            </div>
                            <div class="manage__user_fast_info manage__user_ip">
                                <label class="manage__user_label">PROFIT</label>
                                <input type="text" class="user__ip" value="{{$profit_user}}" disabled>
                            </div>
                        </div>
                        <div class="user__manipulation">
                            <div class="manage__user_fast_info">
                                <label class="manage__user_label">Блокировка</label>
                                <select name="ban" required id="status" class="user__banned">
                                    <option value="0" @if($user->ban == 0) selected @endif>Нет</option>
                                    <option value="1" @if($user->ban == 1) selected @endif>Да</option>
                                </select>
                            </div>
                            <div class="manage__user_fast_info">
                            <label class="manage__user_label">Роль</label>
                            <select name="role" required id="role" class="user__banned">
                                <option value="1" @if($user->admin == 1) selected @endif>Администратор</option>
                                <option value="0" @if($user->admin == 0) selected @endif>Пользователь</option>
                            </select>
                            </div>
                        </div>
                        <button type="submit" class="manage__user__save">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="user__withdraws">
            <p class="name__table">Выводы</p>
                <div class="user__withdraws_table">
                    <table class="table__user user__withdraw">
                        <thead>
                            <tr>
                                <td>ID</td>
                                <td>Метод</td>
                                <td>Счёт</td>
                                <td>Сумма</td>
                                <td class="tb-center">Статус</td>
                                <td class="tb-right">Дата</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user_with as $user_with)
                            <tr>
                                <td>{{$user_with->id}}</td>
                                <td>{{$user_with->system}}</td>
                                <td>{{$user_with->number}}</td>
                                <td>{{$user_with->amount}}</td>
                                @if($user_with->status == 1)<td class="tb-center" style="color: #2BD301;">Выполнено</td> 
                                @elseif($user_with->status == 2)<td class="tb-center" style="color: rgb(216,51,51);">Отменён</td>
                                @elseif($user_with->status == 3)<td class="tb-center" style="color: #7c7c8a;">Обрабатывается</td>
                                @else<td class="tb-center" style="color: #F4A900;">В процессе</td>@endif
                                <td class="tb-right">{{$user_with->created_at->format('H:i:s d.m.Y')}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="user__payments">
                <div class="user__payments_table">
                    <p class="name__table">Пополнения</p>
                    <table class="table__user user__payment">
                        <thead>
                            <tr>
                                <td>ID</td>
                                <td>Метод</td>
                                <td>Сумма</td>
                                <td class="tb-center">Статус</td>
                                <td class="tb-right">Дата</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user_pay as $user_pay)
                            <tr>
                                <td>{{$user_pay->id}}</td>
                                <td>{{$user_pay->system}}</td>
                                <td>{{$user_pay->amount}}</td>
                                @if($user_pay->status == 1)<td class="tb-center" style="color: #2BD301;">Выполнено</td> 
                                @elseif($user_pay->status == 0)<td class="tb-center" style="color: #F4A900;">В процессе</td>
                                @else<td class="tb-center">В процессе</td>@endif
                                <td class="tb-right">{{$user_pay->created_at->format('H:i:s d.m.Y')}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="user__games">
                <div class="user__payments_table">
                    <p class="name__table">Игры пользователя</p>
                    <div class="select__games">
                        <div class="select__game active" data-game="dice">
                            <i class='bx bx-dice-5'></i>
                            DICE
                        </div>
                        <div class="select__game mines_game" data-game="mines">
                            <i class='bx bx-bomb' ></i>
                            MINES
                        </div>
                        <div class="select__game wheel_game" data-game="wheel">
                            <i class='bx bx-color' ></i>
                            WHEEL
                        </div>
                        <div class="select__game jackpot_game" data-game="jackpot">
                            <i class='bx bx-crown' ></i>
                            JACKPOT
                        </div>
                        <div class="select__game crash_game" data-game="crash">
                            <i class='bx bx-line-chart'></i>
                            CRASH
                        </div>
                    </div>
                    <div class="user__game table__dice active">
                        <table class="table__user">
                            <thead>
                                <tr>
                                    <td>ID</td>
                                    <td>Сумма</td>
                                    <td>Статус</td>
                                    <td>Выигрыш</td>
                                    <td class="tb-right">Дата</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usergame_dice as $game_dice)
                                <tr>
                                    <td>{{$game_dice->id}}</td>
                                    <td>{{$game_dice->bet}}</td>
                                    <td>@if($game_dice->type == 'win') Выигрыш @else Проигрыш @endif</td>
                                    <td>{{$game_dice->win}}</td>
                                    <td class="tb-right">{{$game_dice->created_at->format('H:i:s d.m.Y')}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{$usergame_dice->links('pagination::bootstrap-4')}}
                    </div>
                    <div class="user__game table__mines">
                        <table class="table__user">
                            <thead>
                                <tr>
                                    <td>ID</td>
                                    <td>Сумма</td>
                                    <td>Статус</td>
                                    <td>Выигрыш</td>
                                    <td class="tb-right">Дата</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usergame_mines as $game_mines)
                                <tr>
                                    <td>{{$game_mines->id}}</td>
                                    <td>{{$game_mines->bet}}</td>
                                    <td>@if($game_mines->onOff == 1) Начата @else Закончена @endif</td>
                                    <td>{{$game_mines->win}}</td>
                                    <td class="tb-right">{{$game_mines->created_at->format('H:i:s d.m.Y')}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{$usergame_mines->links('pagination::bootstrap-4')}}
                    </div>
                    <div class="user__game table__wheel">
                        <table class="table__user">
                            <thead>
                                <tr>
                                    <td>ID</td>
                                    <td>Сумма</td>
                                    <td>Цвет</td>
                                    <td>Выигрыш</td>
                                    <td class="tb-right">Дата</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usergame_wheel as $game_wheel)
                                <tr>
                                    <td>{{$game_wheel->id}}</td>
                                    <td>{{$game_wheel->price}}</td>
                                    <td>{{$game_wheel->color}}</td>
                                    <td>{{$game_wheel->win_sum}}</td>
                                    <td class="tb-right">{{$game_wheel->created_at->format('H:i:s d.m.Y')}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{$usergame_wheel->links('pagination::bootstrap-4')}}
                    </div>
                    <div class="user__game table__jackpot">
                        <table class="table__user">
                            <thead>
                                <tr>
                                    <td>ID</td>
                                    <td>Сумма</td>
                                    <td>Статус</td>
                                    <td>Выигрыш</td>
                                    <td class="tb-right">Дата</td>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                    <div class="user__game table__crash">
                        <table class="table__user table__crash">
                            <thead>
                                <tr>
                                    <td>ID</td>
                                    <td class="tb-center">ID раунда</td>
                                    <td class="tb-center">X раунда</td>
                                    <td class="tb-center">Ставка</td>
                                    <td class="tb-center">Вывод</td>
                                    <td class="tb-center">Выигрыш</td>
                                    <td class="tb-right">Дата</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($game_crash as $info_crash)
                                <tr>
                                    <td>{{$info_crash['id']}}</td>
                                    <td class="tb-center">{{$info_crash['round_id']}}</td>
                                    <td class="tb-center">{{$info_crash['multiplier']}}</td>
                                    <td class="tb-center">{{$info_crash['price']}}</td>
                                    <td class="tb-center">{{$info_crash['withdraw']}}</td>
                                    <td class="tb-center">{{$info_crash['won']}}</td>
                                    <td class="tb-right">{{$info_crash['created_at']}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="user__referrals">
                <div class="user__promocodes_table">
                    <p class="name__table">Рефералы пользователя ({{$count_reft}})</p>
                    <table class="table__user user__referrals_table">
                        <thead>
                            <tr>
                                <td>ID</td>
                                <td>Пользователь</td>
                                <td class="tb-center"> Пополнения</td>
                                <td class="tb-right">Дата</td>
                            </tr>
                        </thead>
                        <tbody class="tbody__ref">
                            @foreach($referrals as $refs)
                            <tr>
                                <td>{{$refs['user_id']}}</td>
                                <td>{{$refs['username']}}</td>
                                <td class="tb-center">{{$refs['pay']}}</td>
                                <td class="tb-right">{{$refs['date']}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$user_refs->links('pagination::bootstrap-4')}}
                </div>
            </div>
            <div class="user__promocodes">
                <div class="user__promocodes_table">
                    <p class="name__table">Активированные промокоды</p>
                    <table class="table__user user__promocodes_table">
                        <thead>
                            <tr>
                                <td>ID</td>
                                <td>Код</td>
                                <td class="tb-center">Сумма</td>
                                <td class="tb-right">Дата</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($promolog as $promo)
                            <tr>
                                <td>{{$promo->id}}</td>
                                <td>{{$promo->name}}</td>
                                <td class="tb-center">{{$promo->sum}}</td>
                                <td class="tb-right">{{$promo->created_at->format('H:i:s d.m.Y')}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="user__multiaccounts">
                <div class="user__payments_table">
                    <p class="name__table">Мультиаккаунты</p>
                    <table class="table__user multiacc__table">
                        <thead>
                            <tr>
                                <td>Пользователь</td>
                                <td>IP</td>
                                <td>Баланс</td>
                                <td>Тип</td>
                                <td class="tb-center">Блокировка</td>
                                <td class="tb-right">Действие</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($multiacc as $multi)
                            <tr>
                                <td><a href="/admin/user/edit/{{$multi->id}}">{{$multi->username}}</a></td>
                                <td>{{$multi->ip}}</td>
                                <td>{{$multi->balance}}</td>
                                <td>@if($multi->ip == $user->ip) IP @else Одни реквизиты @endif</td>
                                <td class="tb-center">@if($multi->ban == 1) Да @else Нет @endif</td>
                                @if($multi->ban == 0) 
                                <td class="tb-right">
                                    <a href="/admin/user/ban/{{$multi->id}}">Блокировать</a>
                                </td>
                                @else
                                <td class="tb-right">
                                    <a href="/admin/user/unban/{{$multi->id}}">Разблокировать</a>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
@endsection