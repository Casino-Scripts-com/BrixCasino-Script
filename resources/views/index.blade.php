<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="yandex-verification" content="5116afe53bf0aaa1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta content="зубрикс, zubrix, казино, онлайн, лотерея, деньги, халява, бесплатно, nvuti, нвути, драгон, dragon, кабура, cabura" name="keywords">

    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="icon" href="/assets/images/favicon.ico" type="image/x-icon">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="/assets/css/notifyme.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js" integrity="sha512-STof4xm1wgkfm7heWqFJVn58Hm3EtS31XFaagaa8VMReCXAkQnJZ+jEy8PCC/iT18dFy95WcExNHFTqLyp72eQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js'></script>
    <script src="/assets/js/notifyme.min.js"></script>
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/socket.js"></script>

    <script type="text/javascript" >
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();
        for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
        k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(92949077, "init", {
                clickmap:true,
                trackLinks:true,
                accurateTrackBounce:true
        });
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/92949077" style="position:absolute; left:-9999px;" alt="" /></div></noscript>

    <title>ZUBRIX - сервис быстрых игр</title>
</head>
<body>
    <div class="navbar">
        <div class="logotype">
            <a href="/" class="logo">
                <span>ZUBR<span class="green-x">IX</span></span>
            </a>
        </div> 
        <div class="navmenu">
            <ul class="navmenu__list">
                <li class="navmenu__item">
                    <a href="/" class="navmenu__item_link active">Главная</a>
                </li>
                @auth
                <li class="navmenu__item">
                    <a href="/bonus" class="navmenu__item_link">Бонусы</a>
                </li>
                <li class="navmenu__item">
                    <a href="/profile" class="navmenu__item_link">Профиль</a>
                </li>
                <li class="navmenu__item">
                    <a href="/referrals" class="navmenu__item_link">Партнёрка</a>
                </li>
                @endauth
            </ul>
        </div>
        @guest
        <div class="auth__btn">
            Авторизоваться
        </div>
        @endguest
        @auth
        <div class="user__wallet">
            <div class="user__balance">
                <!-- <i class='bx bxs-coin-stack'></i> -->
                <span id="balance">{{Auth::user()->balance}} <span class="ruble">₽</span></span>
            </div>
            <a href="/wallet/pay" class="wallet_up_btn">
                Кошелёк
            </a>
        </div>
        @endauth
    </div>
    <div class="mobile_menu">
        <div class="mobile_menu__content">
            <a href="/" class="mobile_menu__link">
                <i class='bx bxs-home' ></i>
                Главная
            </a>
            <a href="/bonus" class="mobile_menu__link">
                <i class='bx bxs-gift' ></i>
                Бонусы
            </a>
            <a href="/referrals" class="mobile_menu__link">
                <i class='bx bxs-user-plus' ></i>
                Партнёрка
            </a>
            <a href="/profile" class="mobile_menu__link">
                <i class='bx bxs-user-circle' ></i>
                Профиль
            </a>
            @if(Auth::check() && Auth::user()->admin == 1)
            <a href="/admin" class="mobile_menu__link">
                <i class='bx bxs-window-alt'></i>
                Админка
            </a>
            @endif
        </div>
    </div>
    <div class="fix__left_nav">
        <div class="leftside__games">
            <div class="leftside__game">
                <a href="/dice">
                    <i class='bx bx-dice-5'></i>
                </a>
            </div>
            <div class="leftside__game">
                <a href="/mines">
                    <i class='bx bx-bomb' ></i>
                </a>
            </div>
            <div class="leftside__game">
                <a href="/wheel">
                    <i class='bx bx-color'></i>
                </a>
            </div>
            <div class="leftside__game">
                <a href="/jackpot">
                    <i class='bx bx-crown'></i>
                </a>
            </div>
            <div class="leftside__game">
                <a href="/crash">
                    <i class='bx bx-line-chart'></i>
                </a>
            </div>
        </div>
        <div class="leftside__social">
            <div class="social social_vk">
                <a href="https://vk.com/zubrix16">
                    <i class='bx bxl-vk' ></i>
                </a>
            </div>
            <div class="social social_tg">
                <a href="https://t.me/zubrix16">
                    <i class='bx bxl-telegram' ></i>
                </a>
            </div>
            @if(Auth::check() && Auth::user()->admin == 1)
            <div class="leftside__game">
                <a href="/admin">
                    <i class='bx bxs-window-alt'></i>
                </a>
            </div>
            @endif
            @if(Auth::check() && Auth::user()->admin == 1)
            <div class="leftside__online">
                <span class="site__online">
                    <span class="online__dot"></span>
                    <span class="online"></span>
                </span>
            </div>
            @endif
        </div>
    </div>
    <div class="main__content">
        <div class="games__container">
            <div class="games__grid">
                <a href="/dice" class="games__item">
                    <div class="games__card card_dice">
                        <div class="card_btn">
                            <span class="card__play">Играть</span>
                        </div>
                    </div>
                </a>
                <a href="/mines" class="games__item">
                    <div class="games__card card_mines">
                        <div class="card_btn">
                            <span class="card__play">Играть</span>
                        </div>
                    </div>
                </a>
                <a href="/wheel" class="games__item">
                    <div class="games__card card_wheel">
                        <div class="card_btn">
                            <span class="card__play">Играть</span>
                        </div>
                    </div>
                </a>
                <a href="/jackpot" class="games__item">
                    <div class="games__card card_jackpot">
                        <div class="card_btn">
                            <span class="card__play">Играть</span>
                        </div>
                    </div>
                </a>
                <a href="/crash" class="games__item">
                    <div class="games__card card_crash">
                        <div class="card_btn">
                            <span class="card__play">Играть</span>
                        </div>
                    </div>
                </a>
                <a class="games__item games__other">
                    <div class="games__card card_other"></div>
                </a>
            </div>
        </div>
        <footer class="footer">
            <div class="footer__header">
                <div class="footer__logo">
                    <a href="/" class="logo">
                        <span>ZUBR<span class="green-x">IX</span></span>
                    </a>
                    <strong class="footer_security">© 2023 GRIBLIX. Все права защищены.</strong>
                </div>
                <div class="footer__warn">
                    <div class="warn_mark">18+</div>
                    <div class="warn_text">
                        Азартные игры призваны развлекать. Помните, что Вы рискуете деньгами, когда делаете ставки. Не тратьте больше, чем можете позволить себе проиграть.
                    </div>
                </div>
            </div>
            <div class="footer__bottom">
                <a href="/rules" class="footer__rules">Условия использования</a>
                <a href="/privacy" class="footer__privacy">Политика конфиденциальности</a>
            </div>
        </footer>
    </div>
    @guest
    <div class="modal__window modal_auth">
        <div class="modal__dialog modal__auth">
            <div class="modal">
                <div class="modal__close"><i class='bx bx-plus'></i></div>
                <div class="auth__modal">
                    <div class="auth__modal_content">
                        <div class="auth__modal_content_inner">
                            <div class="auth__modal_head">Авторизация</div>
                            <div class="start_auth_modal">
                                <div class="auth__checkbox">
                                    <div class="checkbox">
                                        <input autocomplete="off" class="custom-checkbox auth__checkbox" type="checkbox" id="auth__rules" name="auth__rules">
                                        <label for="auth__rules" class="auth__rules">
                                            <span>
                                                Я принимаю <a target="_blank" href="/rules" class="colored-link"> условия использования</a>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="checkbox" style="margin-top: -40px;">
                                        <input autocomplete="off" class="custom-checkbox auth__checkbox" type="checkbox" id="auth__multi" name="auth__multi">
                                        <label for="auth__multi" class="auth__multi">
                                            <span>
                                                Я подтверждаю, что использую не более одного аккаунта для игры на сайте.
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                <div class="auth__social">
                                    <a disabled href="/auth/google" class="auth__social_btn auth__social_google auth__social_btn_unactive"></a>
                                    <a disabled href="/auth/vk" class="auth__social_btn auth__social_vk auth__social_btn_unactive"></a>
                                    <a disabled href="/auth/steam" class="auth__social_btn auth__social_steam auth__social_btn_unactive"></a>
                                </div>
                                <div class="other_auth_modal">
                                    <div class="other_method_auth login_method">Войти по данным</div>
                                    или
                                    <div class="other_method_auth register_method">Регистрация</div>
                                </div>
                            </div>
                            <div class="register_modal_auth" style="display: none;">
                                <div class="games__sidebar_wrapper_input">
                                    <input type="email" class="games__sidebar_input reg_email" name="log_email" placeholder="mail@mail.ru">
                                </div>
                                <div class="games__sidebar_wrapper_input">
                                    <input type="password" class="games__sidebar_input reg_password" name="log_password" placeholder="*********">
                                </div>
                                <div class="games__sidebar_play_button">
                                    <button class="sidebar__play register_button" style="padding: 13px 15px">Зарегистрировать</button>
                                </div>
                            </div>
                            <div class="login_modal_auth" style="display: none;">
                                <div class="games__sidebar_wrapper_input">
                                    <input type="email" class="games__sidebar_input log_email" name="reg_email" placeholder="mail@mail.ru">
                                </div>
                                <div class="games__sidebar_wrapper_input">
                                    <input type="password" class="games__sidebar_input log_password" name="reg_password" placeholder="*********">
                                </div>
                                <div class="games__sidebar_play_button">
                                    <button class="sidebar__play login_button" style="padding: 13px 15px">Авторизоваться</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('.login_method').click(function() {
            $('.start_auth_modal').hide();
            $('.login_modal_auth').show();
        });
        $('.register_method').click(function() {
            $('.start_auth_modal').hide();
            $('.register_modal_auth').css('display', 'flex');
        });
        $('.register_button').click(function() {
            $.post('/auth/register', {_token: $('meta[name="csrf-token"]').attr('content'), email: $('.reg_email').val(), password: $('.reg_password').val()}).then(e=>{
                if(e.success){
                    noty('Успешно!', 'success');
                    window.location.href = "/";
                }
                if(e.error){
                    noty(e.message, 'error');
                }
            });
        });

        $('.login_button').click(function() {
            $.post('/auth/login', {_token: $('meta[name="csrf-token"]').attr('content'), email: $('.log_email').val(), password: $('.log_password').val()}).then(e=>{
                if(e.success){
                    noty('Успешно!', 'success');
                    window.location.href = "/";
                }
                if(e.error){
                    noty(e.message, 'error');
                }
            });
        });
    </script>
    @endguest

    @if(session()->has('error'))
    <script>
        $.notify({
            type: "error",
            message: "{{ session()->get('error') }}"
        });  
    </script>  
    @endif            
    @if(session()->has('success'))
    <script>
        $.notify({
            type: "success",
            message: "{{ session()->get('success') }}"
        });  
    </script>  
    @endif

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-2GBFZ1SSVQ"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-2GBFZ1SSVQ');
    </script>
</body>
</html>