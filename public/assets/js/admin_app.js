function noty(text, type) {
    new $.notify({
        type: type,
        message: text
    });
}

$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});

$(document).ready(function () {
	$.ajax({
		url: '/admin/balance/rp',
		type: 'get',
		success: function (e) {
			$('.balance_rp').html(e.balance);
		}
	});
    $(".promocodes__create").click(function() {
        $(".modal__window").toggleClass("active");
    });
    $(".promocodes__cancel").click(function() {
        $(".modal__window").removeClass("active");
    });
    $(".manage__copy_id").click(function() {
        var copyText = document.getElementById('user_id');
        copyText.select();
        copyText.setSelectionRange(4, 999)
        document.execCommand("copy");
        noty('ID скопирован', 'success');
    });
    $('#search').on('input',function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        $value=$(this).val();
		setTimeout(function () {
			$('.users__list').addClass('loading');
			$.ajax({
				type : 'get',
				url : '/admin/users/search',
				data:{'userid':$value},
				success:function(data){
					$('.users__list').removeClass('loading');
					$('.users__list').html(data);
				},
				error: function (data) {
					$.notify({
						position: 'bottom-right',
						type: 'error',
						message: 'Ошибка при поиске пользователя'
					});
					$('.users__list').removeClass('loading');
				}
			});
		}, 300);
    });
    $('.dice_enabled').click(function() {
		$('.dice_enabled').attr('disabled', true);
		$.post('/admin/enabled/dice', { _token: $('meta[name="csrf-token"]').attr('content') }).then(e => {
			if (e.success) {
				if(e.active == 'active') {
					$('.game__toggle_dice').removeClass('active');
					noty('Режим выключен', 'success');
				} else {
					$('.game__toggle_dice').addClass('active');
					noty('Режим включен', 'success');
				}
			}
			if (e.error) {

			}
		});
		setTimeout(function () {
			$('.dice_enabled').attr('disabled', false);
		}, 1000);
	});
	$('.mines_enabled').click(function() {
		$('.mines_enabled').attr('disabled', true);
		$.post('/admin/enabled/mines', { _token: $('meta[name="csrf-token"]').attr('content') }).then(e => {
			if (e.success) {
				if(e.active == 'active') {
					$('.game__toggle_mines').removeClass('active');
					noty('Режим выключен', 'success');
				} else {
					$('.game__toggle_mines').addClass('active');
					noty('Режим включен', 'success');
				}
			}
			if (e.error) {

			}
		});
		setTimeout(function () {
			$('.mines_enabled').attr('disabled', false);
		}, 1000);
	});
	$('.wheel_enabled').click(function() {
		$('.wheel_enabled').attr('disabled', true);
		$.post('/admin/enabled/wheel', { _token: $('meta[name="csrf-token"]').attr('content') }).then(e => {
			if (e.success) {
				if(e.active == 'active') {
					$('.game__toggle_wheel').removeClass('active');
					noty('Режим выключен', 'success');
				} else {
					$('.game__toggle_wheel').addClass('active');
					noty('Режим включен', 'success');
				}
			}
			if (e.error) {

			}
		});
		setTimeout(function () {
			$('.wheel_enabled').attr('disabled', false);
		}, 1000);
	});
	$('.jackpot_enabled').click(function() {
		$('.jackpot_enabled').attr('disabled', true);
		$.post('/admin/enabled/jackpot', { _token: $('meta[name="csrf-token"]').attr('content') }).then(e => {
			if (e.success) {
				if(e.active == 'active') {
					$('.game__toggle_jackpot').removeClass('active');
					noty('Режим выключен', 'success');
				} else {
					$('.game__toggle_jackpot').addClass('active');
					noty('Режим включен', 'success');
				}
			}
			if (e.error) {

			}
		});
		setTimeout(function () {
			$('.jackpot_enabled').attr('disabled', false);
		}, 1000);
	});
	$('.select__game').on('click', function(){
        $('.select__game.active').removeClass('active');
        $(this).addClass('active');
		var game = $(this).attr('data-game');
		$('.user__game.active').removeClass('active');
		$('.table__'+game).addClass('active');
    });
	$(".restartWheel").click(function() {
        $.ajax({
			url: '/admin/restartWheel',
			type: 'POST',
			success: function (data) {
			}
		});
    });
	$(".stopWheel").click(function() {
		$('.stopWheel').attr('disabled', true).html('В процессе..');
		$.ajax({
			url: '/admin/stopWheel',
			type: 'POST',
			success: function (data) {
				setTimeout(() => {
					$('.stopWheel').attr('disabled', false).html('Stop Wheel');
				}, 1000);
			}
		});
    });
	$('.user__sort').click(function() {
		$('.users__list').addClass('loading');
		$.post('/admin/users/sort', { _token: $('meta[name="csrf-token"]').attr('content') }).then(e => {
			if (e.success) {
				$('.users__list').html(e.data);
				$('.users__list').removeClass('loading');
			}
			if (e.error) {
				noty('Произошла ошибка!', 'error');
				$('.users__list').removeClass('loading');
			}
		});
    });
	$('.withdraw__multi_show').click(function() {
		var button = $(this);
		var id = button.data('id');
		button.prop('disabled', true);
        button.text('Поиск...');
		$.ajax({
            url : '/admin/user/' + id + '/multiaccounts',
            type : 'post',
            success : function(data) {
				console.log(data);
				var multi = Object.values(data.data.multiaccounts);
				var html = ''
				button.hide();
				var c1 = 0, c2 = 0, c3 = 0;

				for(var i = multi.length - 1; i >= 0; i--) {
                    var has_c3 = multi[i].by.indexOf("c3") > -1;
                    var has_c2 = multi[i].by.indexOf("c2") > -1;
                    var has_c1 = multi[i].by.indexOf("c1") > -1;
                    c1 += has_c1;
                    c2 += has_c2;
                    c3 += has_c3;

                    html += '<div class="withdraw__multi_item"><a href="/admin/user/edit/' + multi[i].id + '" target="_blank" class="">' +  multi[i].username + '(' + multi[i].by.join(' + ') + ')</a></div>';
                }

				if (html.length == 0) {
                    html = '<span class="aw_green">Мультиаккаунты не обнаружены</span>';
                }

                button.parent().html(html);
            },
            error : function(data) {
                button.text('Поиск');
                button.prop('disabled', false);
                $.notify({
                    position : 'bottom-right',
                    type: 'error',
                    message: 'Произошла ошибка при запросе.'
                });
            }
        });
	});
});