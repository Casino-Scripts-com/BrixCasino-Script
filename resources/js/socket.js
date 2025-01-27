const lang = 'ru';
const abcbank = ['black', 'yellow', 'red', 'green'];

var wheel_connect = 0;
var in_game = 0;
var opened;
var callback_timer;
var callback_get = 0;
var paysys = 0;
var witdrawsys = 0;
var chd = 0;

let gocheck;

const socket = io.connect(':8443');
socket.on('live', function(data) {
    $('.online').html(data.count);
});

socket.on('add_wheel', function(data) {
    if ($('.user__bet[data-userid='+data.user_id+'][data-color='+data.to+']').length >= 1) {
        $('#bet_'+data.to+'_'+data.user_id).html(data.total_bet);
    } else $('.users__bet[data-color='+data.to+']').append(data.html);
    for(i=0;i<=4;i++) {
        $('.bet__money[data-bank='+abcbank[i]+']').html(data.color[abcbank[i]]);
    }
    var colors = {
        'ru':{"black":'черный', "red":'красный', "yellow":'желтый',"green":'зеленый'},
        'en':{"black":'black', "red":'red', "yellow":'yellow',"green":'green'}
    };
    console.log(`[WHEEL ROOM] Ставка на ${colors[lang][data.to]} добавлена`);
});

socket.on('upd_wheel', function(data) {

});

socket.on('wheel_clear', function(data) {
//clearTimeout(gocheck);
    $('.users__bet[data-color]').html('');
    $('.bet__money').html('0.00');
    $('#wheelSpin').css({
        transition: '0s',
        transform: 'rotate(2deg)'
    });
    $('.game__wheel_coef').prepend(`<div class="history__line history__line--${data.last.data}"></div>`);
    $('.game__wheel_coef').children().slice(112).remove();
    gocheck = setTimeout(() => wheelInfo(data.game.id), 650);
});

socket.on('wheel_start', function(data) {
    $('#wheelTime').html(data);
    in_game = 0;
});

socket.on('test', function(data){
    console.log(data);
    let out, total, coef, bet;
    if(data.type == 'lose') {
        bet = parseFloat(data.bet).toFixed(2);
        coef = 0;
        out = "danger";
        total = 0;
    }
    else {
        bet = parseFloat(data.bet).toFixed(2);
        coef = data.coef.toFixed(2);
        total = data.total.toFixed(2);
        out = "success";
    }
    $(".games-history").prepend(`<div class="panel"><span> <img alt="Dice" class="game" src="/img/dice.svg">${data.game}</span><span>${data.name}</span><span>${bet}₽</span> <span>x${coef}</span><span class="${out}">${total}</span></div>`);
    $('.games-history').children().slice(112).remove();

});

socket.on('wheel_roll', function(data) {
    $('#wheelTime').html(data.timer.data);
    if(wheel_connect == 0) {
        var contime = {
            '20':{'data':16},
            '19':{'data':16},
            '18':{'data':16},
            '17':{'data':16},
            '16':{'data':16},
            '15':{'data':11},
            '14':{'data':10},
            '13':{'data':9},
            '12':{'data':8},
            '10':{'data':7},
            '9':{'data':6},
            '8':{'data':5},
            '7':{'data':4},
            '6':{'data':3},
            '5':{'data':2},
            '4':{'data':1},
            '3':{'data':0},
            '2':{'data':0},
            '1':{'data':0}

        };
        if(in_game == 0) {
            $('#wheelSpin').css({
                transition: 'all '+contime[data.timer.data].data+'s cubic-bezier(0, 0.49, 0, 1) -7ms',
                transform: 'rotate('+data.roll.data+'deg)'
            });
            in_game++;
        }
    } else {
        $('#wheelSpin').css({
            transition: '100ms cubic-bezier(0.39, 1.005, 0.32, 1) 0s;',
            transform: 'rotate('+data.roll.data+'deg)'
        });
//} else return n('CLIENT_ERR', 'error');
    }
});

function wheelBet(color) {
    callback_re();
    $.post('/api/wheel/bet/'+color,{_token: $('meta[name="csrf-token"]').attr('content'), bet: $('.input__bet').val()}).then(e=>{
        callback_set();
        if(e.success){
            updateBalance(e.balance);
            return noty('Ставка принята!', 'success');
        }
        if(e.error){
            return noty(e.message, 'error');
        }
    });
    callback_timer = setTimeout(()=>tryPost(),650);
}

function wheelInfo(game_id) {
    if(!client_user) return false;
    $.post('/user/wheel/status', {_token: $('meta[name="csrf-token"]').attr('content'), game_id: game_id}).then(e=>{
        if(e.success){
            $('#balance').html(e.balance);
            for(i=0;i<=e.winarr.length-1;i++) {noty(`Ваша ставка #${e.winarr[i]} выиграла`, 'success', '', 'false');}
        }
        if(e.error){
            return n(e.message, 'error');
        }
    });
}

function wheelColor(color) {
    $.post('/api/wheel/admin/bet/'+color,{_token: $('meta[name="csrf-token"]').attr('content')}).then(e=>{
        if(e.success){
            return noty('Цвет установлен!', 'success');
        }
        if(e.error){
            return n(e.message, 'error');
        }
    });
    callback_timer = setTimeout(()=>tryPost(),650);
}

function callback_set() {
    if(callback_get < 1) callback_get++;
    else console.log(callback_get);
}
function callback_re() {
    clearTimeout(callback_timer);
    callback_get = 0;
}
function tryPost() {

    if(callback_get == 0 && chd >= 1) {
        n('Произошла ошибка!<br>Пожалуйста, обновите страницу', 'error');
        console.log(callback_get);
    } else;
    chd++;
}

function jackpot() {
    callback_re();
    $.post('/api/jackpot/newBet', {_token: $('meta[name="csrf-token"]').attr('content'), sum:$(".jackpot_bet").val()}).then(e=>{
        callback_set();
        if(e.success){
            updateBalance(e.balance);
            return noty('Ставка принята!', 'success');
        }
        if(e.error){
            return noty(e.message, 'error');
        }
    });
    callback_timer = setTimeout(()=>tryPost(),650);
}
function _getTransformOffset(e) {
    var t = e.css("transform").split(",");
    return 6 === t.length ? parseInt(t[4]) : 16 === t.length ? parseInt(t[12]) : 0
}
    socket.on('jackpot.newBet', function(data) {
        var bet = '';
        data.bets.forEach(function (info) {
            bet += '<li class="player__avatar_card">';
            bet += '<img src="'+ info.avatar +'" class="avatar__jackpot">';
            bet += '<div class="player__jackpot_bet">'+ info.sum +'</div>';
            bet += '<div class="player__jackpot_ticket">' + info.from + '-' + info.to + '</div>';
            bet += '</li>';
        });


        var chances = '';
        for(var i = 0; i < data.chances.length; i++) {
            chances += '<div class="jackpot__players">';
            chances += '<img src="'+ data.chances[i].avatar +'">';
            chances += '<div class="winner-bilet">' + data.chances[i].chance + '%</div>';
            chances += '</div>';
        }

        $('.jackpot__bank_game').html(data.game.price);

        $('.players__jackpot').html(bet);
        $('.jackpot__players').show().html(chances);
    });

    socket.on('jackpot.timer', function(data) {
        var sec = data.sec,
            min = data.min,
            time = data.time,
            timer = data.timer;
        if(sec < 10) sec = '0' + sec;
        if(min < 10) min = '0' + min;
        $('.jackpot_time').html(min+':'+sec);
    });

socket.on('jackpot.ngTimer', function(data) {
    if(data.ngtime < 10) data.ngtime = '0' + data.ngtime;
    $('.jackpot_time').html('00:'+data.ngtime);
});
    socket.on('jackpot.slider', function(data) {
        var time = 20;
        setInterval(() => {
            time--;
        },1000)
        $('.jackpot__roll').slideDown();
        var members = '';
        var e = 5000;
        var l, h, d = (l = 5195, h = 5239, l = Math.ceil(l), h = Math.floor(h), Math.floor(Math.random() * (h - l)) + l),
                    p = Math.round(d),
                    v = 1e3 * e;
                v < 0 && (v = 0);
                var g = 1 - (v - 5e3) / 15e3,
                    m = 15e3 - 15e3 * (1 - (g = g < 0 ? 0 : g));
        for(var i = 0; i < data.members.length; i++) members += '<img id='+ i +' src='+ data.members[i].avatar + ' alt='+ data.members[i].username +'>';
        $('.jackpot__roll .inbox .jackpot__players').html(members);
        $('.jackpot__roll .inbox .jackpot__players').css({
            transition: "15000ms cubic-bezier(0, 0, 0, 1) -6ms",
            transform: "translate3d(-" + data.ml + "px, 0px, 0px)"
        });
        
        var m = 4, p = 0;
        rouletteInterval = setInterval(function () {
            p = _getTransformOffset($('.jackpot__roll')), m - p >= 60 && (m = p);
        }, 80);
        setTimeout(function () {
            $('.jackpot__winner').css('display', '');
            $('.jackpot__winner').slideDown();
            $('.jackpot__winner .winner__username').text(data.winner.username);
            $('.winner__sum').text(data.winner.sum);
            $('.winner__chance').text(data.winner.chance+'%');

        }, 16000);
    });
    socket.on('jackpot.newGame', function(data) {
        $('.jackpot__roll').slideUp();
        $('.jackpot__winner').slideUp();
        $('.jackpot__roll .inbox .jackpot__players').css({
            transform: "translateX(0px)"
        });
        $('.jackpot_time').text(data.time[0]+':'+data.time[1]);
        $('.jackpot__bank_game').html('0');
        $('.players__jackpot').html('');
    });