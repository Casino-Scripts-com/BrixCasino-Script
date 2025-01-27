<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use App\Models\Crash;
use App\Models\CrashBets;
use App\Models\Settings;
use App\Models\Profit;
use Carbon\Carbon;

class CrashController extends Controller
{

    public function __construct() {
        $this->game = Crash::orderBy('id', 'desc')->first();
        $this->user = Auth::user();
        $this->settings = Settings::where('id', 1)->first();
        $this->redis = Redis::connection();
    }

    public function index() {
        if(!Auth::check()){
            return redirect('/')->with('error', 'Авторизуйтесь');
        }
        $user = User::where('id', Auth::user()->id)->first();
        if($this->settings->crash_enabled == 1) return redirect('/')->with('error', 'В данный момент режим недоступен!');
        if(is_null($this->game)) $this->game = Crash::create([
			'hash' => $this->getSecret()
 		]);
        $game = [
            'hash' => $this->game->hash,
            'price' => CrashBets::where('round_id', $this->game->id)->sum('price'),
            'bets' => $this->getBets(),
        ];
        $gameStatus = $this->game->status == 0 ? 'false' : 'true';
        $bet = ($user) ? CrashBets::where('user_id', $user->id)->where('round_id', $this->game->id)->where('status', '<>', 2)->orderBy('id', 'desc')->first() : null;
        $history = $this->getHistory();
        if($this->settings->tech_work == 1) return response()->view('errors.techworks', [], 404);
        return view('crash', compact('game', 'bet', 'history', 'gameStatus'));
	}

    public function init()
    {
        return response()->json([
            'id' => $this->game->id,
            'status' => $this->game->status,
            'timer' => '10'
        ]);
    }

    public function newGame()
    {
        $this->game->status = 2;
        $this->game->save();

        $bets = CrashBets::where('round_id', $this->game->id)
                        ->where('withdraw', '>', 0)
                        ->where('status', 0)
                        ->get();

        DB::beginTransaction();
        try {
            foreach($bets as $bet)
            {
                $user = DB::table('users')->where('id', $bet->user_id)->first();
                if(!is_null($user) && $bet->withdraw < $this->game->multiplier)
                {
                    DB::table('users')->where('id', $bet->user_id)->update([
                        'balance' => $user->balance+floor($bet->price*$bet->withdraw)
                    ]);
                }
            }
            DB::commit();
        } catch(Exception $e) {
            DB::rollback();
        }

        $bets = CrashBets::where('round_id', $this->game->id)->get();
        $total = 0;
        foreach($bets as $bet) if($bet->status == 1) $total -= $bet->won-$bet->price; else $total += $bet->price;
        $this->game->profit = $total;
        $this->game->save();

        Profit::create([
			'game' => 'crash',
			'sum' => $total
		]);

        $this->game = Crash::create([
            'hash' => $this->getSecret()
        ]);

        $this->redis->publish('crash', json_encode([
            'type' => 'game',
            'hash' => $this->game->hash,
            'history' => $this->getHistory()
        ]));
		$this->newBank(0);
        return [
            'success' => true,
            'id' => $this->game->id
        ];
    }

    public function createBet(Request $r) {
        $user = User::where('id', Auth::user()->id)->first();
        $bet = preg_replace('/[^0-9.]/', '', $r->bet);
        if($this->game->status > 0) return [
            'success' => false,
            'msg' => 'Ставки в этот раунд закрыты!'
        ];

        if($bet < 1) return [
            'success' => false,
            'msg' => 'Минимальная сумма ставки - 1'
        ];

        if($bet > 1000000) return [
            'success' => false,
            'msg' => 'Максимальная сумма ставки - 1000000'
        ];

        if($bet > $user->balance) return [
            'success' => false,
            'msg' => 'Недостаточно баланса!'
        ];

        DB::beginTransaction();

        try {
            $bet = DB::table('crash_bets')
                        ->where('user_id', $user->id)
                        ->where('round_id', $this->game->id)
                        ->first();

            if(!is_null($bet))
            {
                DB::rollback();
                return [
                    'success' => false,
                    'msg' => 'Вы уже сделали ставку в этом раунде!'
                ];
            }

            DB::table('users')->where('id', $user->id)->update([
                'balance' => $user->balance-$r->get('bet')
            ]);

            DB::table('crash_bets')->insert([
                'user_id' => $user->id,
                'round_id' => $this->game->id,
                'price' => floatval($r->get('bet')),
                'withdraw' => floatval($r->get('withdraw'))
            ]);

            DB::commit();

            // success commit
            $this->redis->publish('crash', json_encode([
                'type' => 'bet',
                'bets' => $this->getBets(),
                'price' => CrashBets::where('round_id', $this->game->id)->sum('price')
            ]));


            $user = User::find($user->id);
			$this->newBank($this->getBank());
            return [
                'success' => true,
                'msg' => 'Ваша ставка одобрена!',
                'bet' => floatval($r->get('bet')),
                'balance' => $user->balance,
            ];
        } catch(Exception $e) {
            DB::rollback();
            return [
                'success' => false,
                'msg' => 'Что-то пошло не так...'
            ];
        }
    }

    public function roundToTheNearestAnything($value, $roundTo) {
		$mod = $value%$roundTo;
		return $value+($mod<($roundTo/2)?-$mod:$roundTo-$mod);
	}

	public function random_float($min, $max, $includeMax) {
		return $min + \mt_rand(0, (\mt_getrandmax() - ($includeMax ? 0 : 1))) / \mt_getrandmax() * ($max - $min);
	}

	private function getUser() {
        $user = User::where('fake', 1)->inRandomOrder()->first();
        return $user;
    }

    private function getHistory()
    {
        $list = Crash::select('multiplier', 'hash')->where('status', 2)->orderBy('id', 'desc')->limit(14)->get();
        for($i = 0; $i < count($list); $i++) $list[$i]->color = $this->getColor($list[$i]->multiplier);
        return $list;
    }

    private function getColor($float)
    {
        return $this->getNumberColor($float);
    }

    public function getBank()
    {
        $crash = CrashBets::where('round_id', $this->game->id)->sum('price');
        return $crash ? $crash : 0;
    }

    public function newBank($sum) {
		$this->redis->publish('updateBank', json_encode([
            'game'    => 'crash',
            'sum' => $sum
        ]));
	}

    public function lastBet() {
        $bet = CrashBets::where('user_id', $this->user->id)->orderBy('id', 'desc')->first();
        return (is_null($bet)) ? 0 : $bet->price;
    }

    public function crashGet() {
        if(Auth::guest()) return response()->json(['status' => '0']);
        $user = User::where('id', Auth::User()->id)->first();
        $game = Crash::orderBy('id', 'desc')->first();
        $bet = CrashBets::where('round_id', $game->id)->where('status', 0)->where('user_id', $user->id);
        if(!$bet) return response()->json(['status' => '0']);
  
        
        return response()->json(['status' => 1]);
    }

    private function getSecret()
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz1234567890';
        $str = '';
        for($i = 0; $i < 22; $i++) $str.=$chars[mt_rand(0, mb_strlen($chars)-1)];
        $game = Crash::where('hash', $str)->first();
        if($game) return $this->getSecret();
        return $str;
    }

    private function getNumberColor($n)
    {
        if($n > 8.49) return 'rgb(252, 253, 120)';
        if($n > 6.49) return '#91b447';
        if($n > 2.99) return 'rgb(255, 105, 234)';
        if($n > 1.99) return 'rgb(155, 55, 196)';
        if($n < 1.20) return '#fe4747';
        return 'rgb(104, 165, 254)';
    }

    public function startSlider()
    {
        if($this->game->status == 1) return $this->game->multiplier;
        $this->game->status = 1;
        $this->game->save();

        $list = [2.28, 13.37];

        $this->game->multiplier = $this->getFloat();
        $this->game->save(); // да да да, двойное сохранение, я знаю йопта, просто тут у меня нет транзакции!!!

        // тут невзъебенные вычисления
        return $this->game->multiplier;
    }

    public function getFloat() {
        $profit = Profit::where('created_at', '>=', Carbon::today())->sum('sum');
        $betsPrice = CrashBets::where('round_id', $this->game->id)->sum('price');
        $betsCount = CrashBets::where('round_id', $this->game->id)->count();
        if($betsCount >= 2) {
            if($profit != 0) $percent = ($betsPrice/abs($profit))*100; else $percent = 0;
        } else $percent = 0;

        // get last one
        $lastZero = Crash::where('multiplier', 1)->orderBy('id', 'desc')->first();
        if((is_null($lastZero) || ($this->game->id-$lastZero->id) >= mt_rand(2, 4)) && $percent >= 20) return 1;

        $list = [];
        for($i = 0; $i < 37; $i++) $list[] = 1;
        for($i = 0; $i < 9; $i++) $list[] = 2;
        for($i = 0; $i < 8; $i++) $list[] = 3;
        for($i = 0; $i < 7; $i++) $list[] = 4;
        for($i = 0; $i < 6; $i++) $list[] = 5;
        for($i = 0; $i < 5; $i++) $list[] = 6;
        for($i = 0; $i < 5; $i++) $list[] = 7;
        for($i = 0; $i < 4; $i++) $list[] = 8;
        for($i = 0; $i < 4; $i++) $list[] = 9;
        for($i = 0; $i < 3; $i++) $list[] = 10;
        for($i = 0; $i < 2; $i++) $list[] = mt_rand(11,100);
        for($i = 0; $i < 1; $i++) $list[] = 499;
        shuffle($list);

		if($this->game->multiplier) return $this->game->multiplier;
        $m = $list[mt_rand(0, count($list)-1)];

        if($m > 1) $m = mt_rand(1, $m);
        if($betsCount >= 1) {
            if($profit < ($profit + ($profit/2) * 1.2) && mt_rand(1, 10) > 6) return '1.'.mt_rand(0,4).mt_rand(0,9);
            if($m == 1 && $profit < ($profit + ($profit/2) * 1.2)) return $list[0].'.0'.mt_rand(0,9);
        }

        return $m.'.'.mt_rand(0,9).mt_rand(1,9);
    }

    private function isTrue($chance)
    {
        $list = [];
        for($i = 0; $i < $chance; $i++) $list[] = true;
        for($i = 0; $i < (100-$chance); $i++) $list[] = false;
        shuffle($list);
        return $list[mt_rand(0, count($list)-1)];
    }

    public function Cashout() {
        $user = User::where('id', Auth::user()->id)->first();
        if($this->game->status == 0) return [
            'success' => false,
            'msg' => 'Дождитесь начала раунда!'
        ];

        if($this->game->status == 3) return [
            'success' => false,
            'msg' => 'Этот раунд уже закрыт!'
        ];

        $bet = CrashBets::where('user_id', $user->id)->where('round_id', $this->game->id)->first();
        if(is_null($bet)) return [
            'success' => false,
            'msg' => 'Вы не делали ставку в этом раунде!'
        ];

        if($bet->status == 1) return [
            'success' => false,
            'msg' => 'Вы уже вывели свою ставку!'
        ];

        DB::beginTransaction();

        try {
            $cashout = floatval($this->redis->get('cashout'));
            if($cashout == 0)
            {
                DB::rollback();
                return [
                    'success' => false,
                    'msg' => 'Вы не можете вывести ставку! Раунд еще не начался, либо уже закончился...'
                ];
            }

            $float = floatval($this->redis->get('float'));
            if($bet->withdraw > 0 && $bet->withdraw < $float && $bet->withdraw < $this->game->multiplier) $float = $bet->withdraw;
            if($float <= 0 && $bet->withdraw < $float && $bet->withdraw < $this->game->multiplier) $float = $bet->withdraw;
            if($float <= 0)
            {
                DB::rollback();
                return [
                    'success' => false,
                    'msg' => 'Что-то пошло не так! Умножитель равен нулю!'
                ];
            }
            $user->update([
                'balance' => $user->balance+($bet->price*$float)
            ]);

            DB::table('crash_bets')
                    ->where('id', $bet->id)
                    ->update([
                        'withdraw' => $float,
                        'won' => $bet->price*$float,
                        'status' => 1
                    ]);

            DB::commit();

            // success commit
            $this->redis->publish('crash', json_encode([
                'type' => 'bet',
                'bets' => $this->getBets(),
                'price' => CrashBets::where('round_id', $this->game->id)->sum('price')
            ]));

            $user = User::find($user->id);

            return [
                'success' => true,
                'msg' => 'Вы успешно забрали ставку!',
                'balance' => $user->balance,
                'won_sum' => ($bet->price*$float),
                'float' => $float
            ];

        } catch(Exception $e) {
            DB::rollback();
            return [
                'success' => false,
                'msg' => 'Что-то пошло нет так...'
            ];
        }
    }

    private function getBets()
    {
        $list = CrashBets::where('round_id',  $this->game->id)->orderBy('id', 'desc')->get();
        $bets = [];
        foreach($list as $bet)
        {
            $user = User::where('id', $bet->user_id)->first();
            if(!is_null($user)) $bets[] = [
                'user' => [
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                    'rank' => $user->rank
                ],
                'price' => $bet->price,
                'withdraw' => number_format($bet->withdraw, 2, '.', '.'),
                'color' => $this->getNumberColor($bet->withdraw),
                'won' => $bet->won,
                'status' => $bet->status
            ];
        }

        return $bets;
    }
}
