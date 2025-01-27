<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Payments;
use App\Models\Withdraws;
use App\Models\Dice;
use App\Models\Settings;

class DiceController extends Controller
{
    public function dice(Request $r) {
        $settings = Settings::where('id', 1)->first();
        if(!Auth::check()) return response()->json(['type' => 'error', 'msg' => 'Необходимо авторизоваться']);
        if($settings->dice_enabled == 1) return response()->json(['type' => 'error', 'msg' => 'В данный момент режим недоступен!']);
        
        $type = $r->type;
        $bet = $r->bet;
        $per = $r->percent;
        $dep_wager = Auth::user()->wager;
        $balance = Auth::user()->balance;
		
        $user_id = Auth::user()->id;

        if($bet > $balance) return response()->json(['type' => 'error', 'msg' => 'Недостаточно средств']);
        if($bet < 1 || !is_numeric($bet)) return response()->json(['type' => 'error', 'msg' => 'Минимальная сумма ставки - 1 рубль!']);
        if($per < 1 || $per > 90 || !is_numeric($per)) return response()->json(['type' => 'error', 'msg' => 'Введите шанс от 1 до 90']);
        if(is_null($type) || $type != 'min' && $type != 'max') return response()->json(['type' => 'error', 'msg' => 'Не удалось определить тип ставки']);

        $winsum = round(((100 / $per * $bet) - $bet), 2);
        $win_to = $winsum + $bet;

        $userdep = Payments::where('user_id', Auth::user()->id)->where('status', 1)->sum('amount');
        $userwith = Withdraws::where('user_id', Auth::user()->id)->where('status', 1)->sum('amount');
        $profit = Auth::user()->balance - ($userdep - $userwith);
        $chanche = 30;
        if($userwith > $userdep) {
            $chanche = 35;
            if($userwith > ($userdep * 1.2)) {
              $chanche = 40;
            }
        }
        $random = rand(1, 999999);
        if($profit < 0 and $per >= 3 and $per <= 85) {
            $winopen = rand(0,100) < $chanche;
            if($winopen) {
                if($type == 'min') {
                    $random = rand(($per * 10000) - 1, 999999);
                }
                if($type == 'max') {
                    $random = rand(0, 1000000 - ($per * 10000));
                }
            }
        }

        if($type == 'min') {
            $win_numb = ($per * 10000) - 1;
            if($random <= $win_numb) {
                $out = "win";
                $upd_balance = $balance + $winsum;
                if($per <= 80) {
                    $dep_wager -= $winsum;
                }
                $coef = $win_to / $bet;
                $total = $win_to;
            } else {
                $out = "lose";
                $upd_balance = $balance - $bet;
                $dep_wager -= $bet;
                $coef = 0;
                $total = 0;
            }
        } else {
            $win_numb = 1000000 - ($per * 10000);

            if($random >= $win_numb) {
                $out = "win";
                $upd_balance = $balance + $winsum;
                if($per <= 80) {
                    $dep_wager -= $winsum;
                }
                $coef = $win_to / $bet;
                $total = $win_to;
            } else {
                $out = "lose";
                $upd_balance = $balance - $bet;
                $dep_wager -= $bet;
                $coef = 0;
                $total = 0;
            }
        }

        if($upd_balance < 1 or $dep_wager < 0){
			$dep_wager = 0;
		}

        $betback = $bet + $winsum;
        $update = User::where('id', Auth::user()->id)->first();
        $update->update([
            'balance' => $upd_balance,
            'wager' => $dep_wager,
        ]);
        Dice::create([
            'user_id' => $user_id,
            'bet' => $bet,
            'coef' => $coef,
            'type' => $out,
            'win' => $total
        ]);

        return response()->json(['type' => 'success', 'msg' => 'success bet', 'balance' => $upd_balance, 'random' => $random, 'out' => $out, 'cash' => $betback]);
    }
}
