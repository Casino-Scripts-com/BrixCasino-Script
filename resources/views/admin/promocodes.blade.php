@extends('admin')

@section('content')
<div class="promocodes__container">
    <div class="promocodes__inner">
        <a class="promocodes__create">Создать промокод</a>
        <div class="table__promocodes">
            <table class="table__user table__promocodes">
                <thead>
                    <tr>
                        <td>ID</td>
                        <td>Код</td>
                        <td>Лимит</td>
                        <td>Сумма</td>
                        <td>Кол-во</td>
                        <td class="tb-center">Депозитный</td>
                        <td class="tb-right">Действие</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($promocodes as $promo)
                    <tr>
                        <td>{{$promo->id}}</td>
                        <td>{{$promo->name}}</td>
                        <td>{{$promo->activate_limit}}</td>
                        <td>{{$promo->sum}}</td>
                        <td>{{$promo->activate}}/{{$promo->activate_limit}}</td>
                        <td class="tb-center">@if($promo->type == 1) Да @else Нет @endif</td>
                        <td class="tb-right" style="color: #D73400; cursor: pointer;">
                            <a href="/admin/promocodes/delete/{{$promo->id}}" style="color: #D73400;">Удалить</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal__window">
    <div class="modal__promocodes">
        <p class="promocodes__create_head">Создание промокода</p>
        <form action="/admin/promocodes/new" method="POST">
        @csrf
            <div class="promocodes__inputs">
                <div class="promocodes__input">
                    <label class="promocodes__label_name">Код</label>
                    <input type="text" required class="promocodes__input_code" name="code" id="promo_name">
                </div>
                <div class="promocodes__input">
                    <label class="promocodes__label_name">Сумма</label>
                    <input type="number" required class="promocodes__input_code" name="amount">
                </div>
                <div class="promocodes__input">
                    <label class="promocodes__label_name">Кол-во активаций</label>
                    <input type="number" required class="promocodes__input_code" name="count_use">
                </div>
                <!-- <div class="promocodes__input">
                    <label class="promocodes__label_name">Депозитный</label>
                    <select name="status" required class="promocodes__select">
                        <option value="0">Нет</option>
                        <option value="0">Да</option>
                    </select>
                </div> -->
            </div>
            <div class="promocodes__bottom_btn">
                <button class="promocodes__create__btn random__code">Сгенерировать код</button>
                <button type="submit" class="promocodes__create__btn">Создать промокод</button>
                <a class="promocodes__cancel">Закрыть окно</a>
            </div>
        </form>
    </div>
</div>

<script>
        $(function() {
            function str_rand() {
                var result       = '';
                var words        = '0123456789QWERTYUIOPASDFGHJKLZXCVBNM';
                var max_position = words.length - 1;
                    for( i = 0; i < 8; ++i ) {
                        position = Math.floor ( Math.random() * max_position );
                        result = result + words.substring(position, position + 1);
                    }
                return result;
            }

            
            $(".random__code").click(function() { 
            $("#promo_name").val(str_rand());        
        });
        });
    </script>
@endsection