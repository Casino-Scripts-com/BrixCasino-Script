let cookieIcon = '<img src="/assets/images/cristal.png" class="cristal">';
let bombIcon = '<img src="/assets/images/boom.png" class="bomb">';
let coefs = [
    [],
    [1.09,1.19,1.3,1.43,1.58,1.75,1.96,2.21,2.5,2.86,3.3,3.85,4.55,5.45,6.67,8.33,10.71,14.29,20,30,50,100,300],
    [1.14,1.3,1.49,1.73,2.02,2.37,2.82,3.38,4.11,5.05,6.32,8.04,10.45,13.94,19.17,27.38,41.07,65.71,115,230,575,2300],
    [1.19,1.43,1.73,2.11,2.61,3.26,4.13,5.32,6.95,9.27,12.64,17.69,25.56,38.33,60.24,100.4,180.71,361.43,843.33,2530,12650],
    [1.25,1.58,2.02,2.61,3.43,4.57,6.2,8.59,12.16,17.69,26.54,41.28,67.08,115,210.83,421.67,948.75,2530,8855,53130],
    [1.32,1.75,2.37,3.26,4.57,6.53,9.54,14.31,22.12,35.38,58.97,103.21,191.67,383.33,843.33,2108.33],
    [1.39,1.96,2.82,4.13,6.2,9.54,15.1,24.72,42.02,74.7,140.06,280.13,606.94,1456.67,4005.83,13352.78],
    [1.47,2.21,3.38,5.32,8.59,14.31,24.72,44.49,84.04,168.08,360.16,840.38,2185,6555,24035,120175,1081575],
    [1.56,2.5,4.11,6.95,12.16,22.12,42.02,84.04,178.58,408.19,1020.47,2857.31,9286.25,37145,204297.5,2042975],
    [1.67,2.86,5.05,9.27,17.69,35.38,74.7,168.08,408.19,1088.5,3265.49,11429.23,49526.67,297160,3268760], 
    [1.79,3.3,6.32,12.64,26.54,58.97,140.06,360.16,1020.47,3265.49,12245.6,57146.15,371450,4457400],
    [1.92,3.85,8.04,17.69,41.28,103.21,280.13,840.38,2857.31,11429.23,57146.15,400023.08,5200300],
    [2.08,4.55,10.45,25.56,67.08,191.67,606.94,2185,9286.25,49526.67,371450,5200300],
    [2.27,5.45,13.94,38.33,115,383.33,1456.67,6555,37145,297160,4457400],
    [2.5,6.67,19.17,60.24,210.83,843.33,4005.83,24035,204297.5,3268760],
    [2.78,8.33,27.38,100.4,421.67,2108.33,13352.78,120175,2042975],
    [3.13,10.71,41.07,180.71,948.75,6325,60087.5,1081575],
    [3.57,14.29,65.71,361.43,2530,25300,480700],
    [4.17,20,115,843.33,8855,177100],
    [5,30,230,2530,53130],
    [6.25,50,575,12650],
    [8.33,100,2300],
    [12.5,300],
    [25]
];

$(document).ready(function () {
    $(".input__bombs").change(function() {
        var inp = $(".input__bombs");
        if(inp.val() > 24) inp.val(24);
        if(inp.val() < 2) inp.val(2);
        if(inp.val() % 1 !== 0) inp.val(parseInt(inp.val()));
        getItems()
    });
});

$(document).ready(function () {
    $(".sidebar__play").click(function() {
        $.post("/mines/create", {
            bomb: $(".input__bombs").val(),
            bet: $(".input__bet").val(),
        }).then((e) => {
            if (e.error) return noty(e.msg, "error");
            $(".sidebar__play").hide();
            $(".sidebar__take_win").show();
            $('#win').html(Number($(".input__bet").val()).toFixed(2));
            $(".mines__cell").html("");
            for (i = 0; i <= 25; i++) {
                $(".mines__cell[data-number=" + i + "]").attr("disabled", false).removeClass('bomb-mine diamond-mine active');
            }
            $('.game__mines_coef').removeClass('active');
            $('.game__mines_coefs').animate({scrollLeft: 0}, 0);
            $("#win").html(0.0);
            updateBalance(e.balance);
            return noty(e.msg, "success");
        });
    });
    $(".sidebar__take_win").click(function() {
        $.post("/mines/take").then((e) => {
            if (e.error) return noty(e.msg, "error");
            $(".sidebar__take_win").hide();
            $(".sidebar__play").show();
            $(".mines_coef").removeClass('active');
            e.bombs.forEach((i) => {
                $(".mines__cell[data-number=" + i + "]")
                    .html(bombIcon)
                    .addClass("bomb-mine")
                    .addClass("active");
            });
    
            renderCristall();
            updateBalance(e.balance);
            return noty(e.msg, "success");
        });
    });
    $(".mines__cell").click(function () {
        var mine = $(this).attr("data-number");
        $.post("/mines/open", {
            open: mine,
        }).then((e) => {
            if (e.error) {
                if (e.noend == 1) return noty(e.msg, "error");
                $(".sidebar__take_win").hide();
                $(".sidebar__play").show();
                e.bombs.forEach((i) => {
                    $(".mines__cell[data-number=" + i + "]")
                        .html(bombIcon)
                        .addClass("bomb-mine")
                        .addClass("active");
                });
                $(".mines__cell[data-number=" + mine + "]")
                    .addClass("active");
                $(".mines_coef").removeClass('active');
                renderCristall();
                return noty(e.msg, "error");
            }
            $(".mines__cell[data-number=" + mine + "], .mines_coef[data-p=" + e.step + "]").addClass("active");
            var leftPos = $(".game__mines_coefs").scrollLeft();
            var isActive = $(".game__mines_coef.active").length;
            if (isActive % 2 === 0 || isActive % 4 !== 0) {
                $(".game__mines_coefs")
                    .stop()
                    .animate(
                        {
                            scrollLeft: $(".game__mines_coef_wrap").width() * ($(".mines_coef.active").length - 6),
                        },
                        800
                    );
            }
            $(this).hide().html(cookieIcon).fadeIn().addClass("diamond-mine");
            $("#win").html(Number(e.coef).toFixed(2));
        });
    });
    renderMines();
    getItems(3);
});

function getItems(bombs = $(".input__bombs").val()) {
    let i = 1;
    $('.game__mines_coef').html('');
    coefs[bombs-1].forEach(e => {
        $('.game__mines_coef').append(`
        <div class="game__mines_coef_wrap">
            <div data-p="${i}" class="mines_coef" style="${(e < 1000) ? '' : 'font-size: 13.5px;'}">
                x${(e < 1000) ? e.toFixed(2) : (e / 1000).toFixed(1).replace('.0', "") + 'K'}
            </div>
        </div>`);
        i++;
    });
}

function renderMines() {
    $.post("/mines/get").then((e) => {
        if (e.error) return noty(e.msg, "error");
        if (e.status == 1) {
            $(".sidebar__play").hide();
            $(".sidebar__take_win").show();
            $(".mines__cell").html("");
            $("#win").html(e.coef);
            for (i = 0; i <= 25; i++) {
                $(".mines__cell[data-number=" + i + "]").attr("disabled", false);
            }
            setTimeout(() => {
                e.click.forEach((i) => {
                    $(".mines__cell[data-number=" + i + "]")
                        .hide()
                        .html(cookieIcon)
                        .fadeIn(500)
                        .addClass("diamond-mine active");
                });
                for(i = 1; i <= e.click.length; i++) {
                    $('.mines_coef[data-p=' + i + ']').addClass('active')
                }
                if(e.click.length % 3 === 0 || e.click.length >= 3) {
                $('.game__mines_coef_wrap').animate({scrollLeft: $('.mines_coef').width() * (e.click.length - 2)}, 800);
                }
            }, 150);
        }
    });
}

function renderCristall() {
    for (i = 0; i <= 25; i++) {
        let mine_now = $(".mines__cell[data-number=" + i + "]");
        if (mine_now.hasClass("bomb-mine") || mine_now.hasClass("diamond-mine")) {
            // nothing to do
        } else {
            mine_now.hide().addClass("active").html('<img src="/assets/images/cristal.png" class="cristal">').fadeIn().addClass("diamond-mine");
        }
    }
}