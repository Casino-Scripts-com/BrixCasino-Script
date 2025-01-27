@extends('layout')

@section('content')
<div class="withdraw__container">
    <div class="modal__wallet">
        <div class="modal__wallet_header">
            <div class="modal__wallet_header_nav">
                <a href="/wallet/pay" class="modal__wallet_header_item">
                    <i class='bx bx-plus-circle'></i>
                    <span>Пополнение</span>
                </a>
                <a href="/wallet/withdraw" class="modal__wallet_header_item">
                    <i class='bx bx-minus-circle'></i>
                    <span>Вывод</span>
                </a>
                <a href="/wallet/history" class="modal__wallet_header_item active">
                    <i class='bx bx-history'></i>
                    <span>История</span>
                </a>
            </div>
        </div>
        <div class="history__content">
            <div class="history__types">
                <button class="history__type history__pay active" onclick="setHistoryType(1);loadTable(0)">Пополнения</button>
                <button class="history__type history__with" onclick="setHistoryType(0);loadTable(0)">Выводы</button>
            </div>
            <input type="hidden" id="historyType" value="0">
            <input type="hidden" id="nowPageWithdraws" value="0">
            <input type="hidden" id="nowPagePays" value="0">
            <table class="history__table history__pays">
                <thead>
                    <tr>
                        <td>ID</td>
                        <td class="tb-center">Метод</td>
                        <td class="tb-center">Сумма</td>
                        <td class="tb-center no_mobile">Дата</td>
                        <td class="tb-center">Статус</td>
                    </tr>
                </thead>
                <tbody id="paysTable">
                    @foreach($history_pay as $pays)
                    <tr>
                        <td>#{{$pays->id}}</td>
                        <td class="tb-center">
                            <img src="/assets/images/method/{{$pays->system}}.png" class="method__img">
                        </td>
                        <td class="tb-center">{{$pays->amount}}</td>
                        <td class="tb-center no_mobile">{{$pays->created_at->format('H:i d.m.Y')}}</td>
                        @if($pays->status == 1)<td class="tb-center" style="color: #2BD301;">Выполнено</td>@endif
                        @if($pays->status == 0)<td class="tb-center" style="color: #F4A900;">В процессе</td>@endif
                        @if($pays->status == 2)<td class="tb-center" style="color: #B5111E;">Отменено</td>@endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <table class="history__table history__withdraws" style="display: none;">
                <thead>
                    <tr>
                        <td>ID</td>
                        <td class="tb-center">Метод</td>
                        <td class="tb-center">Сумма</td>
                        <td class="tb-center no_mobile">Дата</td>
                        <td class="tb-center">Статус</td>
                    </tr>
                </thead>
                <tbody id="withdrawsTable">
                    @foreach($history_with as $with)
                    <tr>
                        <td>#{{$with->id}}</td>
                        <td class="tb-center">
                            <img src="/assets/images/method/{{$with->system}}.png" class="method__img">
                        </td>
                        <td class="tb-center">{{$with->amount}}</td>
                        <td class="tb-center no_mobile">{{$with->created_at->format('H:i d.m.Y')}}</td>
                        @if($with->status == 1)<td class="tb-center" style="color: #2BD301;">Выполнено</td>@endif
                        @if($with->status == 0)
                        <td class="tb-center" style="color: #F4A900; display: flex; flex-direction: column">
                            В процессе
                            <a href="/wallet/cancel/withdraw/{{$with->id}}" style="font-size: 14px; color: rgb(216,51,51); font-weight: 500;">Отменить</a>
                        </td>
                        @endif
                        @if($with->status == 2)<td class="tb-center" style="color: #B5111E;">Отменено</td>@endif
                        @if($with->status == 3)<td class="tb-center" style="color: #7c7c8a;">Обрабатывается</td>@endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div id="showWithdraws">
                            
                        
                        <div class="stats__controls active" id="pagesTableWithdraws" style="display:none">
                            <button class="btn-prev btn__full-stats" onclick="loadTable(-1)">
                                <span class="user-btn__text">Предыдущая страница</span>
                            </button>
                            <button class="btn-next btn__full-stats" onclick="loadTable(1)">
                                <span class="btn__text">Следующая страница</span>
                            </button>
                        </div>
                        
                        </div>
                        
                         <div id="showPays">
                        
                        <div class="stats__controls active" id="pagesTablePays" style="display:none">
                            <button class="btn-prev btn__full-stats" onclick="loadTable(-1)">
                                <span class="user-btn__text">Предыдущая страница</span>
                            </button>
                            <button class="btn-next btn__full-stats" onclick="loadTable(1)">
                                <span class="btn__text">Следующая страница</span>
                            </button>
                        </div>
                        
                        </div>
        </div>
    </div>
    <script src="/assets/js/wallet.js"></script>
</div>
@endsection