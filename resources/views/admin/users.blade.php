@extends('admin')

@section('content')
    <div class="users__container">
        <div class="search__group">
            <input type="text" class="input__search" placeholder="Введите ID" id="search" name="search">
            <div class="users__search">
                <i class='bx bx-search-alt-2' ></i>
            </div>
            <div class="users__search user__sort" style="border-radius: 50px;">
                <i class='bx bxs-sort-alt'></i>
            </div>
        </div>
        <div class="users__list">
            @foreach($userget as $user)
            <div class="admin__user">
                <div class="admin__user_info">
                    <img src="{{$user->avatar}}" alt="Аватарка">
                    <span>{{$user->username}}</span>
                </div>
                <div class="admin__user_ip">
                   IP: {{$user->ip}}
                </div>
                <div class="admin__user_balance">
                   Баланс: {{$user->balance}} ₽
                </div>
                <div class="admin__user_block">
                   Заблокирован: @if($user->ban == 1) Да @else Нет @endif
                </div>
                <div class="admin__user_edit">
                    <a href="/admin/user/edit/{{$user->id}}" class="admin__user_button">Редактировать</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
@endsection