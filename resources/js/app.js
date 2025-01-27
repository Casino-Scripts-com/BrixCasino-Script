function noty(text, type) {
    new $.notify({
        type: type,
        message: text
    });
}

function updateBalance(balance) {
    current_balance = balance;
    var init_balance = parseInt($('#balance').text().split(' ').join(''));
    $({cur_balance: init_balance}).animate({cur_balance: balance}, {
        duration: 750,
        easing: 'swing',
        step: function () {
            $('#balance').text(this.cur_balance.toFixed(2));
        },
        complete: function () {
            $('#balance').text(balance.toFixed(2));
        }
    });
}

$(document).ready(function () {
    function getVideoCardInfo() {
        const gl = document.createElement("canvas").getContext("webgl");
        if (!gl) {
            return {
                error: "no webgl",
            };
        }
        const debugInfo = gl.getExtension("WEBGL_debug_renderer_info");
        if (debugInfo) {
            return {
                vendor: gl.getParameter(debugInfo.UNMASKED_VENDOR_WEBGL),
                renderer: gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL),
            };
        }
        return {
            error: "no WEBGL_debug_renderer_info",
        };
    }

    let data = getVideoCardInfo();

    update_videocard();
    function update_videocard() {
        id = JSON.stringify(data);
        $.post("/user/card", { 
          id 
	})
	.then((e) => {});
    }

    $(".copy__referrals_link").click(function() {
        var copyText = document.getElementById('parther');
        copyText.select();
        copyText.setSelectionRange(0, 100)
        document.execCommand("copy");
        noty('Реферальная ссылка скопирована', 'success');
    });

    $(".user__copy_id").click(function() {
        var copyText = document.getElementById('user_id');
        copyText.select();
        copyText.setSelectionRange(4, 100)
        document.execCommand("copy");
        noty('Ваш ID скопирован', 'success');
    });
    
    $(".auth__btn").click(function () {
        $(".modal__window").toggleClass("active");
    });
    $(".modal__close").click(function () {
        $(".modal__window").removeClass("active");
    });
    $(".custom-checkbox").click(function() {
        if ($('#auth__rules').is(':checked') && $('#auth__multi').is(':checked')) {
            $('.auth__social_vk').removeClass('auth__social_btn_unactive');
        }
        else {
            $('.auth__social_vk').addClass('auth__social_btn_unactive');
        }
    });
    $(".promo__activate_btn").click(function() {
        $.post('/user/promo',{_token: $('meta[name="csrf-token"]').attr('content'), code: $('#promocode').val()}).then(e=>{
            if(e.success){
                noty(e.message, 'success');
                updateBalance(e.balance);
            }
            if(e.error){
                return noty(e.message, 'error');
            }
        });
    });
    $(".daily_btn").click(function() {
        $.post('/user/daily', {_token: $('meta[name="csrf-token"]').attr('content')}).then(e=>{
            if(e.success){
                noty('В раздаче получено '+e.bonus+' рублей', 'success');
                updateBalance(e.balance);
            }
            if(e.error){
                return noty(e.message, 'error');
            }
        });
    });
    $(".vk_bonus").click(function() {
        $.post('/user/vk_bonus', { _token: $('meta[name="csrf-token"]').attr('content') }).then(e => {
            if (e.success) {
                noty('Вы успешно получили бонус', 'success');
                $('.vk_bonus').removeClass('vk_bonus');
                $('.vk_bonus').addClass('success');
                $('.vk_bonus').html('Получен');
                updateBalance(e.balance);
            }
            if (e.error) {
                return noty(e.message, 'error');
            }
        });
    });
});