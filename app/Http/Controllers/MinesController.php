<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mines;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use App\Models\Payments;
use App\Models\Withdraws;
use App\Models\Settings;

class MinesController extends Controller
{
    protected $coef = [
        2 => [1.09,1.19,1.3,1.43,1.58,1.75,1.96,2.21,2.5,2.86,3.3,3.85,4.55,5.45,6.67,8.33,10.71,14.29,20,30,50,100,300],
        3 => [1.14,1.3,1.49,1.73,2.02,2.37,2.82,3.38,4.11,5.05,6.32,8.04,10.45,13.94,19.17,27.38,41.07,65.71,115,230,575,2300],
        4 => [1.19,1.43,1.73,2.11,2.61,3.26,4.13,5.32,6.95,9.27,12.64,17.69,25.56,38.33,60.24,100.4,180.71,361.43,843.33,2530,12650],
        5 => [1.25,1.58,2.02,2.61,3.43,4.57,6.2,8.59,12.16,17.69,26.54,41.28,67.08,115,210.83,421.67,948.75,2530,8855,53130],
        6 => [1.32,1.75,2.37,3.26,4.57,6.53,9.54,14.31,22.12,35.38,58.97,103.21,191.67,383.33,843.33,2108.33],
        7 => [1.39,1.96,2.82,4.13,6.2,9.54,15.1,24.72,42.02,74.7,140.06,280.13,606.94,1456.67,4005.83,13352.78],
        8 => [1.47,2.21,3.38,5.32,8.59,14.31,24.72,44.49,84.04,168.08,360.16,840.38,2185,6555,24035,120175,1081575],
        9 => [1.56,2.5,4.11,6.95,12.16,22.12,42.02,84.04,178.58,408.19,1020.47,2857.31,9286.25,37145,204297.5,2042975],
        10 => [1.67,2.86,5.05,9.27,17.69,35.38,74.7,168.08,408.19,1088.5,3265.49,11429.23,49526.67,297160,3268760],
        11 => [1.79,3.3,6.32,12.64,26.54,58.97,140.06,360.16,1020.47,3265.49,12245.6,57146.15,371450,4457400],
        12 => [1.92,3.85,8.04,17.69,41.28,103.21,280.13,840.38,2857.31,11429.23,57146.15,400023.08,5200300],
        13 => [2.08,4.55,10.45,25.56,67.08,191.67,606.94,2185,9286.25,49526.67,371450,5200300],
        14 => [2.27,5.45,13.94,38.33,115,383.33,1456.67,6555,37145,297160,4457400],
        15 => [2.5,6.67,19.17,60.24,210.83,843.33,4005.83,24035,204297.5,3268760],
        16 => [2.78,8.33,27.38,100.4,421.67,2108.33,13352.78,120175,2042975],
        17 => [3.13,10.71,41.07,180.71,948.75,6325,60087.5,1081575],
        18 => [3.57,14.29,65.71,361.43,2530,25300,480700],
        19 => [4.17,20,115,843.33,8855,177100],
        20 => [5,30,230,2530,53130],
        21 => [6.25,50,575,12650],
        22 => [8.33,100,2300],
        23 => [12.5,300],
        24 => [25]
      ];

      public function get() {
        if(Auth::guest()) return response()->json(['status' => '0']);
        $user = User::where('id', Auth::User()->id)->first();
        $game = Mines::where('user_id', $user->id)->where('onOff', 1)->first();
        if(!$game) return response()->json(['status' => '0']);
  
        $click = unserialize($game->click);
        $step = $game->step;
        $bombs = $game->bombs;
        if($step != 0) {
        $profit = round($game->bet * $this->coef[$bombs][$step-1], 2);
        } else $profit = "0.00";
        $nextX = $this->coef[$bombs][$step] ?? $this->coef[$bombs][$step-1];
        return response()->json(['status' => 1, 'click' => $click, 'coef' => $profit, 'next' => $nextX]);
      }

      public function create(Request $r) {
        $settings = Settings::where('id', 1)->first();
        if(Auth::user()->ban == 1) return response()->json(['type' => 'error', 'msg' => 'Вы заблокированы, обратитесь в поддержку']);
        if(Auth::guest()) return response()->json(['error' => 'true', 'msg' => 'Пройдите авторизацию']);
        if(!in_array($r->bomb, [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) return response()->json(['error' => 'true', 'msg' => 'Укажите кол-во бомб [2-24]']);
        if($settings->mines_enabled == 1) return response()->json(['type' => 'error', 'msg' => 'В данный момент режим недоступен!']);
        $bet = $r->bet;
        if($bet < 1 || !$bet || !is_numeric($bet)) return response()->json(['error' => 'true', 'msg' => 'Сумма ставки от 1 руб']);
  
        DB::beginTransaction();
        $user = User::where('id', Auth::User()->id)->lockForUpdate()->first();
        $game = Mines::where('user_id', $user->id)->where('onOff', 1)->count();
        if(Auth::User()->balance < $bet) return response()->json(['error' => 'true', 'msg' => 'Недостаточно средств!']);
        if($game >= 1) return response()->json(['error' => 'true', 'msg' => 'У вас уже есть активная игра']);
        // генерируем ячейки с бомбами и т.п
        $resultmines = range(1,25);
        shuffle($resultmines);
        $resultmines = array_slice($resultmines, 0, $r->bomb);
        $resultmines = serialize($resultmines);
  
        $click = [];
        $click = serialize($click);
        Mines::create([
          'user_id' => $user->id,
          'bet' => $bet,
          'onOff' => 1,
          'step' => 0,
          'result' => 1,
          'win' => 0,
          'mines' => $resultmines,
          'click' => $click,
          'bombs' => $r->bomb,
          'can_open' => 25-$r->bomb
        ]);
        
        $user->balance -= $bet;
        $user->save();
  
        DB::commit();
        return response()->json(['success' => 'true', 'msg' => 'Игра создана! Удачи!', 'balance' => $user->balance]);
      }

      public function open(Request $r) {
        if(Auth::guest()) return response()->json(['error' => 'true', 'msg' => 'Пройдите авторизацию']);
        $game = Mines::where('user_id', Auth::User()->id)->where('onOff', 1)->first();
        if(!$game) return response()->json(['error' => 'true', 'msg' => 'У вас нет активных игр']);
        $user = User::where('id', Auth::User()->id)->first();
  
        $true_arr = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25];
        if(!in_array($r->open, $true_arr)) return response()->json(['error' => 'true', 'msg' => 'Ошибка! Нажмите на нормальную клетку ;)']);
        if($game->can_open < 1) return response()->json(['error' => 'true', 'msg' => 'Вы открыли все кристаллы! Заберите выигрыш', 'noend' => '1']);
        // antiminus baby
        $userdep = Payments::where('user_id', Auth::user()->id)->where('status', '!=', 2)->sum('amount');
        $userwith = Withdraws::where('user_id', Auth::user()->id)->where('status', '!=', 2)->sum('amount');
        $with_today = Withdraws::where('status', '!=', 2)->sum('amount');
        $pay_today = Payments::where('status', '!=', 0)->sum('amount');
        $profit = ($userdep - $userwith) - $user->balance;
        $chanche = 10;
        if(($with_today + $user->balance) > ($pay_today * 1.2)) {
          $chanche = 15;
        } else {
          if($user->balance < 0.99) {
            $chanche = 15;
          }
          if($userwith > $userdep) {
            $chanche = 15;
            if($userwith > ($userdep * 1.2)) {
              $chanche = 20;
            }
          }
        }
        if($profit <= 0) {
          $winopen = rand(0,100) < $chanche;
          if($winopen && !in_array($r->open, unserialize($game->mines))) {
            $mines_select = unserialize($game->mines); // получаем массив с бомбами
            array_splice($mines_select, -1, 1, $r->open); // заменяем одно из значений на значение клика
            $game->mines = serialize($mines_select); // формируем
            $game->save(); // сохраняем
          }
        } elseif(($with_today + $user->balance) > ($pay_today * 1.4)) {
          $winopen = rand(0,100) < $chanche * 1.3;
          if($winopen && !in_array($r->open, unserialize($game->mines))) {
            $mines_select = unserialize($game->mines); // получаем массив с бомбами
            array_splice($mines_select, -1, 1, $r->open); // заменяем одно из значений на значение клика
            $game->mines = serialize($mines_select); // формируем
            $game->save(); // сохраняем
          }
        }
        // antiminus end ))0

        if(in_array($r->open, unserialize($game->click))) return response()->json(['error' => 'true', 'msg' => 'Ошибка! Вы уже открывали эту ячейку', 'noend' => '1']);
        $bombs = unserialize($game->mines);
        if(in_array($r->open, $bombs)) {
          Mines::where('user_id', $user->id)->where('onOff', 1)->update([
            'onOff' => 2,
            'result' => 0
          ]);

          $dep_wager = $user->wager;
          $dep_wager -= $game->bet;
          if($dep_wager < 0 or $user->balance < 1){
            $dep_wager = 0;
          }
          $user->wager = $dep_wager;
          $user->save();
          
          return response()->json(['error' => 'true', 'msg' => 'Упс! Вы подорвались на бомбе', 'bombs' => $bombs]);
        }
        $clicks = unserialize($game->click);
        $clicks[] = $r->open;
        $clicks = serialize($clicks);
        $step = $game->step+1;
        $profit = round($game->bet * $this->coef[$game->bombs][$step-1], 2);
  
        $nextX = $this->coef[$game->bombs][$step] ?? $this->coef[$game->bombs][$step-1];
        Mines::where('user_id', $user->id)->where('onOff', 1)->update([
          'step' => $game->step+1,
          'click' => $clicks,
          'can_open' => $game->can_open-1
        ]);
        return response()->json(['success' => 'true', 'msg' => '', 'coef' => $profit, 'next' => $nextX, 'step' => $game->step+1]);
      }

      public function take() {
        if(Auth::guest()) return response()->json(['error' => 'true', 'msg' => 'Пройдите авторизацию']);
        DB::beginTransaction();
        $game = Mines::where('user_id', Auth::User()->id)->where('onOff', 1)->lockForUpdate()->first();
        if(!$game) return response()->json(['error' => 'true', 'msg' => 'У вас нет активных игр']);
        if($game->step == 0) return response()->json(['error' => 'true', 'msg' => 'Откройте хотя бы 1 ячейку']);
        $user = User::where('id', Auth::User()->id)->lockForUpdate()->first();
  
        $win = round($game->bet * $this->coef[$game->bombs][$game->step-1], 2);

        if($user->wager > 0 and $this->coef[$game->bombs][$game->step-1] >= 1.3){
          $dep_wager = $user->wager;
          $dep_wager -= $game->bet;
          if($dep_wager < 0 or $user->balance < 1){
            $dep_wager = 0;
          }
          $user->wager = $dep_wager;
          $user->save();
        }
        $user->balance += $win;
        $user->save();
        Mines::where('user_id', $user->id)->where('onOff', 1)->update([
          'onOff' => 2,
          'win' => $win
        ]);
  
        DB::commit();
        return response()->json(['success' => 'true', 'msg' => 'Вы забрали '.$win.' руб', 'balance' => $user->balance, 'bombs' => unserialize($game->mines)]);
      }
}
