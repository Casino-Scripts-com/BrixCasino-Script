<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta content="зубрикс, zubrix, казино, онлайн, лотерея, деньги, халява, бесплатно, nvuti, нвути, драгон, dragon, кабура, cabura" name="keywords">

    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="icon" href="/assets/images/favicon.ico" type="image/x-icon">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="/assets/css/notifyme.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js" integrity="sha512-STof4xm1wgkfm7heWqFJVn58Hm3EtS31XFaagaa8VMReCXAkQnJZ+jEy8PCC/iT18dFy95WcExNHFTqLyp72eQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js" integrity="sha512-3j3VU6WC5rPQB4Ld1jnLV7Kd5xr+cq9avvhwqzbH/taCRNURoeEpoPBK9pDyeukwSxwRPJ8fDgvYXd6SkaZ2TA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js'></script>
    <script src="/assets/js/jquery.kinetic.min.js"></script>
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
    @if(Auth::check())
        <script>
            const client_user = {{Auth::User()->id}};
        </script>
    @endif
    <div class="navbar">
        <div class="logotype">
            <a href="/" class="logo">
                <span>ZUBR<span class="green-x">IX</span></span>
            </a>
        </div> 
        <div class="navmenu">
            <ul class="navmenu__list">
                <li class="navmenu__item">
                    <a href="/" class="navmenu__item_link {{ request()->routeIs('index') ? 'active' : '' }}">Главная</a>
                </li>
                @auth
                <li class="navmenu__item">
                    <a href="/bonus" class="navmenu__item_link {{ request()->routeIs('bonus') ? 'active' : '' }}">Бонусы</a>
                </li>
                <li class="navmenu__item">
                    <a href="/profile" class="navmenu__item_link {{ request()->routeIs('profile') ? 'active' : '' }}">Профиль</a>
                </li>
                <li class="navmenu__item">
                    <a href="/referrals" class="navmenu__item_link {{ request()->routeIs('referrals') ? 'active' : '' }}">Партнёрка</a>
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
        @yield('content')
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endguest

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-2GBFZ1SSVQ"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-2GBFZ1SSVQ');
    </script>
</body>
</html>