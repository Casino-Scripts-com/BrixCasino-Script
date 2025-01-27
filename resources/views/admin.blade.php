<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="stylesheet" href="/assets/css/admin_style.css">
    <link rel="icon" href="/assets/images/favicon.ico" type="image/x-icon">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="/assets/css/notifyme.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js" integrity="sha512-STof4xm1wgkfm7heWqFJVn58Hm3EtS31XFaagaa8VMReCXAkQnJZ+jEy8PCC/iT18dFy95WcExNHFTqLyp72eQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="/assets/js/notifyme.min.js"></script>
    <script src="/assets/js/admin_app.js"></script>

    <title>ADMIN-PANEL || ZUBRIX</title>
</head>
<body>
    <div class="header">
        <a href="/" class="logo" style="margin-top: 0;">
            <span>ZUBR<span class="green-x">IX</span></span>
         </a>
         <div class="admin__info">
            <span class="admin__name">{{Auth::user()->username}}</span>
            <div class="admin__avatar">
                <img src="{{Auth::user()->avatar}}" alt="admin_avatar">
            </div>
         </div>
    </div>
    <div class="pc__menu">
        <div class="admin_menu">
            <a href="/" class="logo">
                <span>ZUBR<span class="green-x">IX</span></span>
            </a>
            <div class="admin__items">
                <div class="admin__item {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                    <a href="/admin">
                        <i class='bx bxs-bar-chart-alt-2'></i>
                        <span class="admin__item_name">Статистика</span>
                    </a>
                </div>
                <div class="admin__item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <a href="/admin/users">
                        <i class='bx bxs-user'></i>
                        <span class="admin__item_name">Пользователи</span>
                    </a>
                </div>
                <div class="admin__item {{ request()->routeIs('admin.promocodes') ? 'active' : '' }}">
                    <a href="/admin/promocodes">
                        <i class='bx bxs-gift'></i>
                        <span class="admin__item_name">Промокоды</span>
                    </a>
                </div>
                <div class="admin__item {{ request()->routeIs('admin.withdraws') ? 'active' : '' }}">
                    <a href="/admin/withdraws">
                        <i class='bx bx-money-withdraw' ></i>
                        <span class="admin__item_name">Выводы</span>
                    </a>
                </div>
                <div class="admin__item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                    <a href="/admin/settings">
                        <i class='bx bxs-cog' ></i>
                        <span class="admin__item_name">Настройки</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="mobile_menu">
        <div class="mobile_menu__content">
            <a href="/admin" class="mobile_menu__link">
                <i class='bx bxs-bar-chart-alt-2'></i>
                Статистика
            </a>
            <a href="/admin/users" class="mobile_menu__link">
                <i class='bx bxs-user'></i>
                Пользователи
            </a>
            <a href="/admin/promocodes" class="mobile_menu__link">
                <i class='bx bxs-gift'></i>
                Промокоды
            </a>
            <a href="/admin/withdraws" class="mobile_menu__link">
                <i class='bx bx-money-withdraw' ></i>
                Выводы
            </a>
            <a href="/admin/settings" class="mobile_menu__link">
                <i class='bx bxs-cog' ></i>
                Настройки
            </a>
        </div>
    </div>
    <div class="admin__content">
        @yield('content')
    </div>

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
</body>
</html>