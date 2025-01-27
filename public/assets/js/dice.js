$(document).ready(function () {
    $(".play__small").click(function() {
        $(".dice__play").attr("disabled", true);
        $(".dice__play").css("opacity", "0.7");
        setTimeout(() => {
            $(".dice__play").attr("disabled", false);
            $(".dice__play").css("opacity", "1");
        }, 300);
        $.post('/dice/bet', {
            _token: $('meta[name="csrf-token"]').attr('content'),
            bet: $('.input__bet').val(),
            percent: $('.input__chance').val(),
            type: 'min'
        }).then(e => {
            if (e.type == 'success')
                if (e.out == 'win') {
                    $('.dice__result').css('display', 'block').removeClass("danger").addClass("success").html("Выиграли <b>" + e.cash.toFixed(2) + "</b>");
                    updateBalance(e.balance);
                    return false;
                }
            if (e.out == 'lose') {
                $('.dice__result').css('display', 'block').removeClass("success").addClass("danger").html('Выпало <b>' + e.random + '</b>');
                updateBalance(e.balance);
                return false;
            } else $('.dice__result').css('display', 'block').removeClass("success").addClass("danger").html(e.msg);
        });
    });
    $(".play__big").click(function() {
        $(".dice__play").attr("disabled", true);
        $(".dice__play").css("opacity", "0.7");
        setTimeout(() => {
            $(".dice__play").attr("disabled", false);
            $(".dice__play").css("opacity", "1");
        }, 300);
        $.post('/dice/bet', {
            _token: $('meta[name="csrf-token"]').attr('content'),
            bet: $('.input__bet').val(),
            percent: $('.input__chance').val(),
            type: 'max'
        }).then(e => {
            if (e.type == 'success')
                if (e.out == 'win') {
                    $('.dice__result').css('display', 'block').removeClass("danger").fadeIn().addClass("success").html("Выиграли <b>" + e.cash.toFixed(2) + "</b>");
                    updateBalance(e.balance);
                    return false;
                }
            if (e.out == 'lose') {
                $('.dice__result').css('display', 'block').removeClass("success").fadeIn().addClass("danger").html('Выпало <b>' + e.random + '</b>');
                updateBalance(e.balance);
                return false;
            } else $('.dice__result').css('display', 'block').removeClass("success").fadeIn().addClass("danger").html(e.msg);
        });
    });
    $(".input__chance").keyup(function() {
        $('.min__prog').html('0 - ' + Math.floor(($('.input__chance').val() / 100) * 999999));
        $('.max__prog').html(999999 - Math.floor(($('.input__chance').val() / 100) * 999999) + ' - 999999');
        var inp = $(".input__chance");
        if (inp.val() > 90) inp.val(90);
        updateWin(((100 / $('.input__chance').val()) * $('.input__bet').val()));
    });
    $(".input__bet").keyup(function() {
        $('.min__prog').html('0 - ' + Math.floor(($('.input__chance').val() / 100) * 999999));
        $('.max__prog').html(999999 - Math.floor(($('.input__chance').val() / 100) * 999999) + ' - 999999');
        var inp = $(".input__chance");
        if (inp.val() > 90) inp.val(90);
        updateWin(((100 / $('.input__chance').val()) * $('.input__bet').val()));
    });
});

function updateWin(win) {
    current_balance = win;
    var init_balance = parseInt($('.dice__possible_win').text().split(' ').join(''));
    $({cur_balance: init_balance}).animate({cur_balance: win}, {
        duration: 350,
        easing: 'swing',
        step: function () {
            $('.dice__possible_win').text(this.cur_balance.toFixed(2));
        },
        complete: function () {
            $('.dice__possible_win').text(win.toFixed(2));
        }
    });
}

function changeBet() {
    $('.min__prog').html('0 - ' + Math.floor(($('.input__chance').val() / 100) * 999999));
    $('.max__prog').html(999999 - Math.floor(($('.input__chance').val() / 100) * 999999) + ' - 999999');
    var inp = $(".input__chance");
    if (inp.val() > 90) inp.val(90);
    updateWin(((100 / $('.input__chance').val()) * $('.input__bet').val()));
}

function changeChance() {
    $('.min__prog').html('0 - ' + Math.floor(($('.input__chance').val() / 100) * 999999));
    $('.max__prog').html(999999 - Math.floor(($('.input__chance').val() / 100) * 999999) + ' - 999999');
    var inp = $(".input__chance");
    if (inp.val() > 90) inp.val(90);
    updateWin(((100 / $('.input__chance').val()) * $('.input__bet').val()));
}
