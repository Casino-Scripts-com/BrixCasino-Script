@extends('admin')

@section('content')

<div class="statistic__container">
    <div class="statistic__inner">
        <div class="statistic__payments">
            <div class="statistic__payments_day">
                <div class="statitic__payments__header">
                    Пополнения за сегодня
                </div>
                <div class="statistic__payments_amount">
                    {{$pay_today}}
                </div>
            </div>
            <div class="statistic__payments_week">
                <div class="statitic__payments__header">
                    Пополнения за неделю
                </div>
                <div class="statistic__payments_amount">
                    {{$pay_week}}
                </div>
            </div>
            <div class="statistic__payments_all">
                <div class="statitic__payments__header">
                    Пополнения за все время
                </div>
                <div class="statistic__payments_amount">
                    {{$pay_all}}
                </div>
            </div>
        </div>
        <div class="site__info_users">
            <div class="statistic__amount_users">
                <div class="statitic__payments__header">
                    Сумма пользователей
                </div>
                <div class="statistic__payments_amount">
                    {{$users_balance}}
                </div>
            </div>
            <div class="statistic__amount_users">
                <div class="statitic__payments__header">
                    Пользователей
                </div>
                <div class="statistic__payments_amount">
                    {{$users}}
                    <span class="new__users aw_green">+{{$new_users}}</span>
                </div>
            </div>
            <div class="statistic__amount_users">
                <div class="statitic__payments__header">
                    Баланс RubPay
                </div>
                <div class="statistic__payments_amount balance_rp">
                    
                </div>
            </div>
        </div>
        <div class="statistic__payments">
            <div class="statistic__payments_day">
                <div class="statitic__payments__header">
                    Выводы за сегодня
                </div>
                <div class="statistic__payments_amount">
                    {{$with_today}}
                </div>
            </div>
            <div class="statistic__payments_week">
                <div class="statitic__payments__header">
                    Выводы за неделю
                </div>
                <div class="statistic__payments_amount">
                    {{$with_week}}
                </div>
            </div>
            <div class="statistic__payments_all">
                <div class="statitic__payments__header">
                    Выводы за все время
                </div>
                <div class="statistic__payments_amount">
                    {{$with_all}}
                </div>
            </div>
        </div>
        <div class="statistics__money">
            <div class="statistics__recent statistics__recent__pay">
                <p class="name__table">Недавние пополнения</p>
                <table class="table__user table__statistic">
                    <thead>
                        <tr>
                            <td>Профиль</td>
                            <td class="tb-center">Сумма</td>
                            <td class="tb-right">Дата</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($last_pay as $last_pay)
                        <tr>
                            <td><a href="/admin/user/edit/{{$last_pay->user_id}}">Профиль</td>
                            <td class="tb-center">{{$last_pay->amount}}</td>
                            <td class="tb-right">{{$last_pay->created_at->format('H:i:s d.m.Y')}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="statistics__recent statistics__recent__withdraws">
            <p class="name__table">Недавние выводы</p>
                <table class="table__user table__statistic">
                    <thead>
                        <tr>
                            <td>Профиль</td>
                            <td class="tb-center">Сумма</td>
                            <td class="tb-right">Дата</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($last_withdraws as $last_with)
                        <tr>
                            <td><a href="/admin/user/edit/{{$last_with->user_id}}">Профиль</td>
                            <td class="tb-center">{{$last_with->amount}}</td>
                            <td class="tb-right">{{$last_with->created_at->format('H:i:s d.m.Y')}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection