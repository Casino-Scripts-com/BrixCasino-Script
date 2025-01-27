<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Settings;
use App\Models\User;
use App\Models\WheelBets;
use App\Models\Wheel;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;


class WheelController extends Controller
{


    public function wheel($color, Request $r) {
        DB::beginTransaction();
        $game = Wheel::orderBy('id', 'desc')->lockForUpdate()->first();
        if(!$game) Wheel::create([]);

        $bet = preg_replace('/[^0-9.]/', '', $r->bet);

        if(!Auth::Check()) return response()->json(['error' => 'true', 'message' => 'Авторизуйтесь']);
        if(Auth::User()->ban == 1) return response()->json(['error' => 'true', 'message' => 'Ваш аккаунт заблокирован. Обратитесь в поддержку.']);
        if($game->status == 1 || $game->status == 2) return response()->json(['error' => 'true', 'message' => 'Приём ставок закрыт.<br> Ожидайте начало новой игры']);
        if($color != 'black' && $color != 'yellow' && $color != 'red' && $color != 'green') return response()->json(['error' => 'true', 'message' => 'Ошибка! Попробуйте позже']);
        if(Auth::User()->balance < $bet || !Auth::User()->balance) return response()->json(['error' => 'true', 'message' => 'Недостаточно средств!']);
        if($bet < 1 || !$bet) return response()->json(['error' => 'true', 'message' => 'Ставки от 1 монеты']);
        $bet_client = number_format($bet, 2, '.', ' ');
        //$count_black = WheelBets::where('game_id', $game->id)->where('color', 'black')->where('user_id', Auth::User()->id)->count();
        //$count_red = WheelBets::where('game_id', $game->id)->where('color', 'red')->where('user_id', Auth::User()->id)->count()
        //if($color == 'red' && $count_black > 0 || $color == 'black' && $count_red > 0) return response()->json(['error' => 'true', 'message' => 'Вы не можете одновременно ставить на Черный и Красный']);
        $count_bets = WheelBets::where('game_id', $game->id)->where('user_id', Auth::User()->id)->count();
        if($count_bets >= 2) return response()->json(['error' => 'true', 'message' => 'Вы сделали максимальное число ставок!.<br> Ожидайте начало новой игры']);
        $this->redis = Redis::connection();
        $database = WheelBets::create(['user_id' => Auth::User()->id, 'color' => $color, 'price' => $bet, 'game_id' => $game->id]);
        $total = WheelBets::where('game_id', $game->id)->where('user_id', Auth::User()->id)->where('color', $color)->sum('price');
        $user = User::where('id', Auth::user()->id)->lockForUpdate()->first();
        $user->update([
            'balance' => $user->balance - $bet
        ]);
        $dep_wager = $user->wager;
        $dep_wager -= $game->bet;
        if($dep_wager < 0 or $user->balance < 1){
            $dep_wager = 0;
        }
        $user->wager = $dep_wager;
        $user->save();
        $wheel_data = [
            'type'=> "add_wheel",
            'to' => $color,
            'user_id' => Auth::User()->id,
            'betsum' => $bet_client,
            'total_bet' => $total,
            'color' => [
                'black' => number_format(round(WheelBets::where('game_id', $game->id)->where('color', 'black')->sum('price'), 2), 2, '.', ''),
                'yellow' => number_format(round(WheelBets::where('game_id', $game->id)->where('color', 'yellow')->sum('price'), 2), 2, '.', ''),
                'red' => number_format(round(WheelBets::where('game_id', $game->id)->where('color', 'red')->sum('price'), 2), 2, '.', ''),
                'green' => number_format(round(WheelBets::where('game_id', $game->id)->where('color', 'green')->sum('price'), 2), 2, '.', ''),
            ],
            'html' => "<div class='user__bet' data-userid='".Auth::User()->id."' data-color='$color'> <div class='user__info_bet'> <div class='avatar__user_wheel'><img src='".Auth::User()->avatar."' class='avatar__img' alt='avatar'></div> <span class='user__name_wheel'>".Auth::User()->username."</span> </div> <div class='bet__user'> <span class='bet' id='bet_".$color."_".Auth::User()->id."'>$bet_client</span></div></div>"
        ];
        $this->redis->publish("wheel_timer", json_encode(['status' => 'true', 'time'=>"15"]));
        $this->redis->publish("wheel", json_encode($wheel_data));

        DB::commit();
        return response()->json(['success' => 'true', 'balance' => $user->balance]);
    }
    public function adminwheel($color, Request $r) {
        $game = Wheel::orderBy('id', 'desc')->first();
        if($game->status == 1) return response()->json(['error' => 'true', 'message' => 'Игра уже началась!']);

        $game->update([
            'winner_color' => $color
        ]);

        return response()->json(['success' => 'true']);
    }
    public function getColor($color) {
        $list = [
            [1870.89,	'black',	5],
            [1650.78, 'yellow', 2],
            [1907.51, 'black', 3],
            [1989.83, 'black', 3],
            [1075, 'red', 50],
            [1082, 'green', 50],
            [1668.81, 'black', 5],
            [2001.99, 'black', 3],
            [1234.56, 'yellow', 2],
            [1795.44, 'red', 5],
            [1595.44, 'yellow', 3],
            [1296.44, 'black', 2],
            [1255, 'black', 2],
            [1555, 'yellow', 3],
            [1657, 'black', 2],
            [1663, 'red', 5],
            [1674.99, 'red', 5],
            [1681.99, 'black', 2],
            [1241.98, 'black', 2],
            [1988.62, 'black', 2],
            [2106.98, 'black', 2],
            [2101.98, 'red', 5],
            [1080.50, 'green', 50]
        ];
        $filter = array_filter($list, function($var) use($color) {
            return ($var[1] == $color);
        });
        shuffle($filter);

        $с = $filter[mt_rand(0, count($filter)-1)];
        return $с;
    }

    public function close() {
        $games = Wheel::orderBy('id', 'desc')->first();
        $settings = Settings::orderBy('id', 'desc')->first();
        $bets = WheelBets::where('game_id', $games->id)->count();

        $games->status = 1;
        $games->save();
        
        if($games->winner_color == 'red') {
            $wcolor = 'red';
            $rotate = $this->getColor($wcolor);
            $games->winner_color = $rotate[1];
            $games->save();

            return response()->json(['success' => 'true', 'rotate' => $rotate, 'gameid' => $games->id]);
        }
        
        if($games->winner_color == 'yellow') {
            $wcolor = 'yellow';
            $rotate = $this->getColor($wcolor);
            $games->winner_color = $rotate[1];
            $games->save();
            
            return response()->json(['success' => 'true', 'rotate' => $rotate, 'gameid' => $games->id]);
        }

        if($games->winner_color == 'black') {

            $wcolor = 'black';
            $rotate = $this->getColor($wcolor);
            $games->winner_color = $rotate[1];
            $games->save();
    
            return response()->json(['success' => 'true', 'rotate' => $rotate, 'gameid' => $games->id]);
        }

        if($games->winner_color == 'green') {
            $wcolor = 'green';
            $rotate = $this->getColor($wcolor);
            $games->winner_color = $rotate[1];
            $games->save();
    
            return response()->json(['success' => 'true', 'rotate' => $rotate, 'gameid' => $games->id]);
        }  

        if($games->winner_color == NULL) {
            $wingreen = rand(0, 100) > 99;
            $win = rand(0, 6) == 3;
            if($win) {
                    $bets_black = WheelBets::where('game_id', $games->id)->where('color', 'black')->sum('price') * 2;
                    $bets_yellow = WheelBets::where('game_id', $games->id)->where('color', 'yellow')->sum('price') * 3;
                    $bets_red = WheelBets::where('game_id', $games->id)->where('color', 'red')->sum('price') * 5;
                    $wincolors =[$bets_black,$bets_yellow,$bets_red];
                    shuffle($wincolors);

                    $wincolor = min($wincolors);
    
                    if($wincolor == $bets_black) {
                        $wcolor = 'black';
                        $rotate = $this->getColor($wcolor);
                        $games->winner_color = $rotate[1];
                        $games->save();
    
                        return response()->json(['success' => 'true', 'rotate' => $rotate, 'gameid' => $games->id]);
                    } 
                    if($wincolor == $bets_yellow) {
                        $wcolor = 'yellow';
                        $rotate = $this->getColor($wcolor);
                        $games->winner_color = $rotate[1];
                        $games->save();
    
                        return response()->json(['success' => 'true', 'rotate' => $rotate, 'gameid' => $games->id]);
                    } 
                    if($wincolor == $bets_red) {
                        $wcolor = 'red';
                        $rotate = $this->getColor($wcolor);
                        $games->winner_color = $rotate[1];
                        $games->save();
    
                        return response()->json(['success' => 'true', 'rotate' => $rotate, 'gameid' => $games->id]);
                    } 
                }
                if(!$win) {
                    if($games->winner_color == NULL) {  
                        $color = [];
                        for($i = 0; $i < 49.5; $i++) $color[] = 'black';
                        for($i = 0; $i < 26; $i++) $color[] = 'yellow';
                        for($i = 0; $i < 24; $i++) $color[] = 'red';
                        for($i = 0; $i < 0.5; $i++) $color[] = 'green';
                        shuffle($color);
            
                        $wcolor = $color[mt_rand(0, count($color)-1)];
            
                        $rotate = $this->getColor($wcolor);
                        $games->winner_color = $rotate[1];
                        $games->save();
            
                        return response()->json(['success' => 'true', 'rotate' => $rotate, 'gameid' => $games->id]);
                    }
            }
        } else {
            $color = [];
            for($i = 0; $i < 50.5; $i++) $color[] = 'black';
            for($i = 0; $i < 26; $i++) $color[] = 'yellow';
            for($i = 0; $i < 23; $i++) $color[] = 'red';
            for($i = 0; $i < 0.5; $i++) $color[] = 'green';
            shuffle($color);
            
            $wcolor = $color[mt_rand(0, count($color)-1)];
            
            $rotate = $this->getColor($wcolor);
            $games->winner_color = $rotate[1];
            $games->save();
            
            return response()->json(['success' => 'true', 'rotate' => $rotate, 'gameid' => $games->id]);
        }

    }
    public function end() {
        $games = Wheel::orderBy('id', 'desc')->first();
        $games->status = 2;
        $games->save();
        $last_color = $games->winner_color;
        $this->sendWin($games->id,$last_color);
        $start = Wheel::where('status', '0')->count();
        if($start == 0) Wheel::create([]);
        return response()->json(['success' => 'true', 'color' => $last_color]);
    }
    public function sendWin($game_id, $color) {
        $game = Wheel::orderBy('id', 'desc')->first();
        $profit = Settings::orderBy('id', 'desc')->first();


        $bets = WheelBets::select(DB::raw('SUM(price) as price'), 'user_id', 'balance')->where('game_id', $game_id)->where('color', $color)->groupBy('user_id', 'balance')->get();
        if($color == 'black')
            $multiplier = 2;
        if($color == 'yellow')
            $multiplier = 3;
        if($color == 'red')
            $multiplier = 5;
        if($color == 'green')
            $multiplier = 50;

        $profit_collect = 0; // высчитываем профиты

        $betUsers = WheelBets::where('game_id', $game_id)->where('color', $color)->get();
        $loseUsers = WheelBets::where('game_id', $game_id)->where('color', '!=', $color)->get();
        foreach($betUsers as $b) { // выигрыши. Выситываем профит
            $user = User::where(['id' => $b->user_id])->first();
            $user->balance += $b->price*$multiplier;
            $user->save();
            $b->win = 1;
            $b->win_sum = ($b->price*$multiplier)-$b->price;
            $b->save();
            $profit_collect -= $b->price*$multiplier-$b->price;

            $avatar = $user->avatar;
            $username = $user->username;
            $coef = $multiplier;
            $bet = $b->price;
            $win = $b->price*$multiplier;
        }

        foreach($loseUsers as $l) {
            $user = User::where(['id' => $l->user_id])->first();
            $avatar = $user->avatar;
            $username = $user->username;
            $bet = $l->price;

            $profit_collect += $l->price;
        }
    }
    public function getWheel() {

    }
    public function infoWheel(Request $r) {
        $game_id = preg_replace('/[^0-9.]/', '', $r->game_id);
        $count = WheelBets::where('game_id', $game_id)->count();
        if($count < 1) return response()->json(['error' => 'true', 'message' => 'Игра не найдена']);
        if(!$game_id) return response()->json(['error' => 'true', 'message' => 'Не указан один из параметров']);
        if(!Auth::check()) return response()->json(['error' => 'true', 'message' => 'XSRF токен истёк. Обновите страницу']);
        $user_id = Auth::User()->id;
        $balance = Auth::User()->balance;
        $win = WheelBets::where('game_id', $game_id)->where('user_id', $user_id)->where('win', 1)->get();
        $lose = WheelBets::where('game_id', $game_id)->where('user_id', $user_id)->where('win', 0)->get();
        $winarr = [];
        $losearr = [];
        foreach($win as $w) {
            $winarr[] = $w->id;
        }
        foreach($lose as $l) {
            $losearr[] = $l->id;
        }
        return response()->json(['success' => 'true', 'winarr' => $winarr, 'losearr' => $losearr, 'balance' => $balance]);
    }
}
