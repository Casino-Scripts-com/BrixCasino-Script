<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/assets/css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <title>Ваш аккаунт заблокирован</title>
</head>
<body>
    <a href="/" class="logo" style="justify-content:center; font-size: 38px;">
        <span>ZUBR<span class="green-x">IX</span></span>
    </a>
    <div class="main__error">
        <div class="code__error" style="margin-bottom: 10px;">Ваш аккаунт заблокирован</div>
        <div class="error__text"><span>{{Auth::user()->username}}, вы были заблокированы, напишите в поддержку для уточнения.</div>
        <div class="error__text">Telegram поддержки: @ZubrixTechno_bot</div>
</body>
</html>