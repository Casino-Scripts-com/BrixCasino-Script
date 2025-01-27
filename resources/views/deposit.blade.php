@extends('layout')

@section('content')
<div class="pay__container">
    <div class="modal__wallet">
        <div class="modal__wallet_header">
            <div class="modal__wallet_header_nav">
                <a href="/wallet/pay" class="modal__wallet_header_item modal__pay active">
                    <i class='bx bx-plus-circle'></i>
                    <span>Пополнение</span>
                </a>
                <a href="/wallet/withdraw" class="modal__wallet_header_item modal__withdraw">
                    <i class='bx bx-minus-circle'></i>
                    <span>Вывод</span>
                </a>
                <a href="/wallet/history" class="modal__wallet_header_item modal__history">
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
                <button class="modal__wallet_pay_method active" data-payway="qiwi">
                    <img src="/assets/images/method/qiwi.png" alt="Qiwi">
                    Qiwi
                </button>
                <button class="modal__wallet_pay_method" data-payway="yoomoney">
                    <img src="/assets/images/method/yoomoney.png" alt="YooMoney">
                    YooMoney
                </button>
                <button class="modal__wallet_pay_method" data-payway="card">
                    <img src="/assets/images/method/card.png" alt="Card">
                    Card
                </button>
                <button class="modal__wallet_pay_method" data-payway="fkwallet">
                    <img src="/assets/images/method/fkwallet.png" alt="FKwallet">
                    FKwallet
                </button>
                <button class="modal__wallet_pay_method method__sbp" data-payway="sbp">
                    <img src="/assets/images/method/sbp.png" alt="SBP">
                    SBP
                </button>
                <!-- <button class="modal__wallet_pay_method method__crypto" data-payway="crypto">
                    <img src="/assets/images/method/crypto.png" alt="Crypto">
                    Crypto
                </button> -->
            </div>
            <div class="modal__wallet_body_pay">
                    <label class="modal__wallet_body-label">
                        Сумма к пополнению
                    </label>
                    <div class="modal__wallet_input_wrapper">
                        <div class="modal__wallet_input_currency">₽</div>
                        <input type="number" class="modal__wallet_input" placeholder="0" required name="amount"
                            id="amount">
                    </div>
                    <input type="hidden" name="payway" value="qiwi" id="payment_payway">
                    <button type="submit" class="modal__wallet_sumbit pay__submit">
                        Оплатить
                    </button>
                    <div class="modal__wallet_rates">
                        <span>Коммисия: <span id="comission">0</span>%</span>
                    </div>
            </div>
        </div>
    </div>
    <script src="/assets/js/wallet.js"></script>
</div>
@endsection