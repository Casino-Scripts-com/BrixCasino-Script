$(document).ready(function() {

    var avaiable_payways = [{"id":1,"title":"Qiwi","name":"qiwi","min":300,"max":15000,"comission":0,"status":1,"is_default":1},{"id":2,"title":"YooMoney","name":"yoomoney","min":300,"max":100000,"comission":0,"status":1,"is_default":0},{"id":3,"title":"Visa\/Mastercard","name":"card","min":500,"max":100000,"comission":0,"status":1,"is_default":0},{"id":6,"title":"FKwallet","name":"fkwallet","min":250,"max":15000,"comission":0,"status":1,"is_default":0},{"id":7,"title":"SBP","name":"sbp","min":200,"max":15000,"comission":0,"status":1,"is_default":0},{"id":8,"title":"Crypto","name":"crypto","min":200,"max":15000,"comission":1,"status":1,"is_default":0}];
    var avaiable_withdraw = [{"id":1,"title":"Card","name":"card","min":500,"max":15000,"comission":5,"status":1,"format":"220115016890xxxx","is_default":1},{"id":2,"title":"YooMoney","name":"yoomoney","min":200,"max":100000,"comission":3,"status":1,"format":"4100xxxxxxxxxxx","is_default":0},{"id":3,"title":"FKwallet","name":"fkwallet","min":200,"max":15000,"comission":3,"status":1,"format":"F1xxxxxxxx","is_default":0},{"id":4,"title":"Qiwi","name":"qiwi","min":200,"max":15000,"comission":5,"status":1,"format":"7xxxxxxxxxx","is_default":0},{"id":5,"title":"Piastrix","name":"piastrix","min":500,"max":15000,"comission":2,"status":1,"format":"20xxxxxxxxxx","is_default":0}];
    var payment_options = {};
    var withdraw_options = {};

    
    for (var i = 0; i < avaiable_payways.length; i++) {
        payment_options[avaiable_payways[i].name] = {
            name: avaiable_payways[i].title,
            min: avaiable_payways[i].min,
            max: avaiable_payways[i].max,
            comission: avaiable_payways[i].comission,
            image: '/assets/images/method/' + avaiable_payways[i].name + '.png'
        };
    }

    for (var i = 0; i < avaiable_withdraw.length; i++) {
        withdraw_options[avaiable_withdraw[i].name] = {
            name: avaiable_payways[i].title,
            min: avaiable_withdraw[i].min,
            max: avaiable_withdraw[i].max,
            comission: avaiable_withdraw[i].comission,
            format: avaiable_withdraw[i].format,
            image: '/assets/images/method/' + avaiable_withdraw[i].name + '.png'
        };
    }

    function changePayway(payway){
        $('#payment_payway').val(payway);
        comission = payment_options[payway]['comission'];
        $('#comission').html(comission);
        if( $('#amount').val() > payment_options[payway]['max']) {
            $('#amount').val(payment_options[payway]['min']);
        }
    }

    function changeWithdraw(payway){
        $('#payment_payway').val(payway);
        //comission = withdraw_options[payway]['comission'];
        //$('#comission').html(comission);
        if( $('#amount').val() > withdraw_options[payway]['max']) {
            $('#amount').val(withdraw_options[payway]['min']);
        }
        if( $('#amount').val() < withdraw_options[payway]['min']) {
            $('#amount').val(withdraw_options[payway]['min']);
        }
        $('.withdraw__img_method').attr('src', withdraw_options[payway]['image']);
        $('#number').attr('placeholder', withdraw_options[payway]['format']);
    }

    function withdraw(payway){
        $('.create__with').attr('disabled', true);
        var amount = parseInt($('#amount').val());
        var final_amount = parseInt($('#final_amount').val());
        payway = $('#payment_payway').val();
        if(payway == 'fkwallet') {
            var wallet = $('#number').val();
        } else {
            var wallet = $('#number').val().replace(/[^0-9]/g, ""); 
        }
        if($('#amount').val() < withdraw_options[payway]['min']) {
            return noty('Минимальная сумма вывода - '+withdraw_options[payway]['min'], 'error');
        }
        if($('#amount').val() > withdraw_options[payway]['max']) {
            return noty('Максимальная сумма вывода - '+withdraw_options[payway]['max'], 'error');
        }
        $.post('/user/withdraw',{_token: $('meta[name="csrf-token"]').attr('content'), amount: amount, system: payway, number: wallet, final_amount: final_amount }).then(e=>{
            if(e.success){
                noty('Заявка успешно создана', 'success');
                updateBalance(e.balance);
                $('.modal__wallet_balance').html(e.balance);
                setTimeout(() => {
                    $('.create__with').attr('disabled', false);
                }, 500);
            }
            if(e.error){
                noty(e.message, 'error');
                setTimeout(() => {
                    $('.create__with').attr('disabled', false);
                }, 500);
            }
        });
    }
    
    $('.modal__wallet_header_item').on('click', function(){
        $('.modal__wallet_header_item.active').removeClass('active');
        $(this).addClass('active');
    });

    $('.modal__wallet_pay_method').on('click', function(){
        $('.modal__wallet_pay_method.active').removeClass('active');
        $(this).addClass('active');
        changePayway($(this).data('payway'));
    });

    $('.modal__wallet_withdraw_method').on('click', function(){
        $('.modal__wallet_withdraw_method.active').removeClass('active');
        $(this).addClass('active');
        changeWithdraw($(this).data('payway'));
    });

    $("#amount").keyup(function() {
        payway = $('#payment_payway').val();
        var comission = withdraw_options[payway]['comission'];
        var amount = $('#amount').val();
        $('#final_amount').val(amount - (amount * comission / 100));
    });

    $('.pay__submit').click(function() {
        paysys = $('#payment_payway').val();
        payway = $('#payment_payway').val();
        if($('#amount').val() < payment_options[payway]['min']) {
            return noty('Минимальная сумма пополнения - '+payment_options[payway]['min'], 'error');
        }
        if($('#amount').val() > payment_options[payway]['max']) {
            return noty('Максимальная сумма пополнения - '+payment_options[payway]['max'], 'error');
        }
        $.post('/wallet/pay/' + paysys, { _token: $('meta[name="csrf-token"]').attr('content'), size: $('#amount').val() }).then(e => {
            if (e.success) {
                noty('Перенаправление...', 'success');
                setTimeout(() => location.href = e.redirect, 1000);
            }
            if (e.error) {
                return noty(e.message, 'error');
            }
        });
    });

    $('.history__pay').click(() => {
        $('.history__with').removeClass('active');
        $('.history__pay').addClass('active');
        $('.history__pays').css('display', 'table');
        $('.history__withdraws').css('display', 'none');
    });

    $('.history__with').click(() => {
        $('.history__with').addClass('active');
        $('.history__pay').removeClass('active');
        $('.history__pays').css('display', 'none');
        $('.history__withdraws').css('display', 'table');
    });

    $('.create__with').click(function(){
        withdraw();
    });
});

function setHistoryType(id){
    $('#historyType').val(id)  
    if (id == 0){
        $('#showWithdraws').show();
        $('#showPays').hide();
    }else{
        $('#showWithdraws').hide();
        $('#showPays').show();
    }
}


function loadTable(type){
    
    
    historyType = $('#historyType').val()
    if (historyType == 0){
        $('#withdrawsTable').html('')
        nPage = $('#nowPageWithdraws').val()
    }else{
        $('#paysTable').html('')
        nPage = $('#nowPagePays').val()
    }
    
    nPage = Number(nPage)
    nPage += Number(type)
    
    if (historyType == 0){
        
        $('#nowPageWithdraws').val(nPage)
    }else{
        $('#nowPagePays').val(nPage)
    }
    
    $.post('/load/table', {_token: $('meta[name="csrf-token"]').attr('content'), historyType:$('#historyType').val(), nPage}).then(e=>{
        $('#withdrawsTable').html('')
        $('#paysTable').html('')
        if(e.success){
                if(e.morePage == 1){
                    if (historyType == 0){
                        $('#pagesTableWithdraws').show();
                     }else{
                         $('#pagesTablePays').show();
                     }
                }else{
                     if (historyType == 0){
                        $('#pagesTableWithdraws').hide();
                     }else{
                         $('#pagesTablePays').hide();
                     }
                } 
                
                if(e.lastPage == 1){
                    $('.btn-prev').addClass('active').removeAttr('disabled', 'disabled')
                }else{
                    $('.btn-prev').removeClass('active').attr('disabled', 'disabled')
                }
                
                //$('.btn-next').addClass('active')
                 
                if(e.nextPage == 1){
                    $('.btn-next').addClass('active').removeAttr('disabled', 'disabled')
                }else{
                   $('.btn-next').removeClass('active').attr('disabled', 'disabled')
                }
                
                
                e.history.forEach((e)=>{
                    let st = ""
                    let d = new Date(e.created_at);
                    let dateresult = d.getHours()+':'+(("0" + d.getMinutes())).slice(-2)+' '+(("0" + d.getDate())).slice(-2)+'.'+(("0" + d.getMonth())).slice(-2)+'.'+d.getFullYear();
                    if (historyType == 0){

                        status = e.status
                        
                        if(status == 0){
                            st = '<td class="tb-center" style="color: #F4A900;display:flex;flex-direction:column;">В процессе <a href="/wallet/cancel/withdraw/'+e.id+'" style="font-size: 14px; color: rgb(216,51,51); font-weight: 500;">Отменить</a></td>'
                        }
                        
                        if(status == 1){
                            st = '<td class="tb-center" style="color: #2BD301;">Выполнено</td>'
                        }
                        
                        if(status == 2){
                            st = '<td class="tb-center" style="color: #B5111E;">Отменено</td>'
                        }
                        
                        $('#withdrawsTable').append('<tr>\
                        <td>#'+e.id+'</td>\
                        <td class="tb-center"><img src="/assets/images/method/'+e.system+'.png" class="method__img"></td>\
                        <td class="tb-center">-'+e.amount+'</td>\
                        <td class="tb-center no_mobile">'+dateresult+'</td>\
                        '+st+'\
                        </tr>')
                        
                    }else{
                        status = e.status
                        
                        if(status == 0){
                            st = '<td class="tb-center" style="color: #F4A900;">В процессе</td>'
                        }
                        
                        if(status == 1){
                            st = '<td class="tb-center" style="color: #2BD301;">Выполнено</td>'
                        }
                        
                        if(status == 2){
                            st = '<td class="tb-center" style="color: #B5111E;">Отменено</td>'
                        }
                        
                        
                         $('#paysTable').append('<tr>\
                         <td>#'+e.id+'</td>\
                         <td class="tb-center"><img src="/assets/images/method/'+e.system+'.png" class="method__img"></td>\
                         <td class="tb-center">-'+e.amount+'</td>\
                         <td class="tb-center no_mobile">'+dateresult+'</td>\
                         '+st+'\
                         </tr>')
                        
                    }
                })
        }
        if(e.error){
            return noty(e.message, 'error');
        }
    });
}
