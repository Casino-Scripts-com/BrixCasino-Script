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
                <a href="/wallet/withdraw" class="modal__wallet_header_item active">
                    <i class='bx bx-minus-circle'></i>
                    <span>Вывод</span>
                </a>
                <a href="/wallet/history" class="modal__wallet_header_item">
                    <i class='bx bx-history'></i>
                    <span>История</span>
                </a>
            </div>
        </div>
        <div class="modal__wallet_body">
            <div class="modal__wallet_body_paysys">
                <label class="modal__wallet_body-label">
                    Баланс
                </label>
                <div class="modal__wallet_balance">
                    {{Auth::user()->balance}}
                </div>
                <label class="modal__wallet_body-label">
                    Способы оплаты
                </label>
                <button class="modal__wallet_withdraw_method active" data-payway="qiwi">
                    <img src="/assets/images/method/qiwi.png" alt="Qiwi">
                    Qiwi
                </button>
                <button class="modal__wallet_withdraw_method" data-payway="yoomoney">
                    <img src="/assets/images/method/yoomoney.png" alt="YooMoney">
                    YooMoney
                </button>
                <button class="modal__wallet_withdraw_method" data-payway="piastrix">
                    <img src="/assets/images/method/piastrix.png" alt="Piastrix">
                    Piastrix
                </button>
                <button class="modal__wallet_withdraw_method" data-payway="card">
                    <img src="/assets/images/method/card.png" alt="Card">
                    Card
                </button>
                <!-- <button class="modal__wallet_withdraw_method" data-payway="fkwallet">
                    <img src="/assets/images/method/fkwallet.png" alt="FKwallet">
                    FKwallet
                </button> -->
            </div>
            <div class="modal__wallet_body_pay">
                <label class="modal__wallet_body-label"> 
                    Номер кошелька
                </label>
                <div class="modal__wallet_input_wrapper" style="margin-bottom: 10px;">
                    <div class="modal__wallet_input_currency"><img src="/assets/images/method/qiwi.png" class="withdraw__img_method"></div>
                    <input type="text" class="modal__wallet_input" placeholder="7xxxxxxxxx" required name="number" id="number"> 
                </div>
                <div class="withdraws__inputs">
                    <div class="withdraw__input_wr withdraw__amount_input">
                        <label class="modal__wallet_body-label">
                            Сумма вывода
                        </label>
                        <div class="modal__wallet_input_wrapper">
                            <div class=" modal__wallet_input_currency">₽</div>
                            <input type="number" class="modal__wallet_input" placeholder="0" required name="amount" id="amount">
                        </div>
                    </div>
                    <div class="withdraw__input_wr withdraw__comcomission_input">
                        <label class="modal__wallet_body-label">
                            К получению
                        </label>
                        <div class="modal__wallet_input_wrapper">
                            <div class="modal__wallet_input_currency">₽</div>
                            <input type="number" class="modal__wallet_input" placeholder="0" disabled required name="final_amount" id="final_amount">
                        </div> 
                    </div>
                </div>
                <input type="hidden" name="payway" value="qiwi" id="payment_payway">
                <button class="modal__wallet_sumbit create__with" >
                    Создать выплату
                </button>
            </div>
        </div>
    </div>
    <script src="/assets/js/wallet.js"></script>
</div>
@endsection