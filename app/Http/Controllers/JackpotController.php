<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\Wheel;
use App\Models\WheelBets;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Jackpot;
use App\Models\JackpotBets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class JackpotController extends Controller
{
    public function __construct(Request $r)
    {
        $this->game = Jackpot::orderBy('game_id', 'desc')->first();
        $this->redis = Redis::connection();
        view()->share('bets', $this->getGameBets());
        view()->share('game', $this->getGame());
        view()->share('time', $this->getTime());
        view()->share('chances', $this->getChancesOfGame($this->game->game_id));
    }

    public static function getPriceJackpot()
    {
        $jackpot_r1 = Jackpot::select('price')->orderBy('id', 'desc')->first();
        if (!is_null($jackpot_r1)) $jackpot = $jackpot_r1->price; else $jackpot = 0;
        return $jackpot;
    }

    public function index(Request $r)
    {
        $settings = Settings::orderBy('id', 'desc')->first();
        if($settings->tech_work == 1) return response()->view('errors.techworks', [], 404);
        if($settings->jackpot_enabled == 1) return redirect('/')->with('error', 'В данный момент режим недоступен!');
        $history = Jackpot::where('status', 3)->orderBy('game_id', 'desc')->limit(10)->get();
        $bets = $this->getBets();
        
        return view('jackpot', compact('settings', 'history', 'bets'));
    }

    private function getBets() {
        $game = Jackpot::orderBy('id', 'desc')->first();
        $bets = JackpotBets::where('jackpot_bets.game_id', $this->game->id)
            ->select('jackpot_bets.user_id', DB::raw('SUM(jackpot_bets.sum) as sum'), 'users.username', 'users.avatar', 'users.rank', 'jackpot_bets.color', 'jackpot_bets.from', 'jackpot_bets.to')
            ->join('users', 'users.id', '=', 'jackpot_bets.user_id')
            ->groupBy('jackpot_bets.user_id', 'jackpot_bets.color', 'jackpot_bets.from', 'jackpot_bets.to')
            ->orderBy('sum', 'desc')
            ->get();
        return $bets;
    }

    public function newGame(Request $r)
    {

        $hash = bin2hex(random_bytes(16));

        $game = Jackpot::create([
            'game_id' => $this->game->game_id + 1,
            'hash' => $hash
        ]);

        $countGame = Jackpot::where(['status' => 3])->count();
        if ($countGame > 10) Jackpot::where(['status' => 3])->orderBy('id', 'asc')->limit(1)->delete();

        Jackpot::where('updated_at', '>=', Carbon::today()->addDays(2))->delete();
        JackpotBets::where('created_at', '>=', Carbon::today()->addDays(2))->delete();

        return response()->json([
            'game' => [
                'id' => $game->game_id,
                'price' => $game->price,
                'hash' => $hash,
            ],
            'allBank' => round($this->getPriceJackpot(), 2),
            'time' => $this->getTime()
        ]);
    }

    public function newBet(Request $r)
    {   
        $settings = Settings::orderBy('id', 'desc')->first();
        if($settings->jackpot_enabled == 1) return response()->json(['type' => 'error', 'msg' => 'В данный момент режим недоступен!']);
        $sum = preg_replace('/[^0-9.]/', '', $r->get('sum'));
        $moneytick = preg_replace('/[^0-9.]/', '', $sum);

        $userbets = JackpotBets::where('game_id', $this->game->game_id)->where('user_id', Auth::user()->id)->count();
        $usersum = JackpotBets::where('game_id', $this->game->game_id)->where('user_id', Auth::user()->id)->sum('sum');
        $bets = JackpotBets::where('game_id', $this->game->game_id)->get();

        if ($userbets >= 3) {
            return response()->json([
                'message' => 'Вы не можете сделать больше 3 ставок за одну игру!',
                'error' => 'true'
            ]);
        }

        if ($this->game->status == 2 || $this->game->status == 3) {
            return response()->json([
                'message' => 'Ставки в эту игру закрыты!',
                'error' => 'true'
            ]);
        }
        if (!$moneytick) {
            return response()->json([
                'message' => 'Вы не ввели сумму ставки!',
                'error' => 'true'
            ]);
        }
        if ($moneytick > Auth::user()->balance) {
            return response()->json([
                'message' => 'Не хватает монет для ставки!',
                'error' => 'true'
            ]);
        }
        if ($moneytick < 1) {
            return response()->json([
                'message' => 'Минимальная ставка - 1 рубль',
                'error' => 'true'
            ]);
        }

        $getcolor = $this->getColor();
        foreach ($bets as $check) {
            if ($check->color == $getcolor) {
                $getcolor = $this->getColor();
            }
            if ($check->user_id == Auth::user()->id) {
                $getcolor = $check->color;
            }
        }

        $ticketFrom = 1;
        DB::beginTransaction();
        $lastBet = JackpotBets::where('game_id', $this->game->game_id)->orderBy('id', 'desc')->lockForUpdate()->first();
        if ($lastBet) $ticketFrom = $lastBet->to;
        $ticketTo = $ticketFrom + floor($moneytick * 10);

        $bet = new JackpotBets();
        $bet->game_id = $this->game->game_id;
        $bet->user()->associate(Auth::user());
        $bet->sum = $moneytick;
        $bet->from = $ticketFrom;
        $bet->to = $ticketTo;
        $bet->color = $getcolor;
        $bet->save();

        $user = User::where('id', Auth::user()->id)->lockForUpdate()->first();
        $user->balance -= $moneytick;
        $user->save();

        $infos = JackpotBets::where('game_id', $this->game->game_id)->orderBy('id', 'desc')->get();

        $this->game->price = $infos->sum('sum');
        $this->game->save();

        $info = [];

        foreach ($infos as $bet) {
            $user = $this->findUser($bet->user_id);
            $info[] = [
                'user_id' => $bet->user_id,
                'avatar' => $user->avatar,
                'rank' => $user->rank,
                'username' => $user->username,
                'sum' => $bet->sum,
                'color' => $bet->color,
                'from' => $bet->from,
                'to' => $bet->to,
                'chance' => $this->getChanceByUser($user->id, $this->game->game_id)
            ];
        }

        $this->redis->publish('jackpot.newBet', json_encode([
            'bets' => $info,
            'game' => [
                'price' => round($this->game->price, 2)
            ],
            'allBank' => round($this->getPriceJackpot(), 2),
            'chances' => $this->getChancesOfGame($this->game->game_id)
        ]));


        if (count($this->getChancesOfGame($this->game->game_id)) >= 2) {
            if ($this->game->status < 1) {
                $this->game->status = 1;
                $this->game->save();
                $this->StartTimer();
            }
        }

        DB::commit();

        return response()->json([
            'message' => 'Ваша ставка одобрена!',
            'success' => 'true',
            'balance' => $user->balance
        ]);
    }

    public function getSlider(Request $r)
    {

        # Поиск победителя
        $tickets = JackpotBets::where('game_id', $this->game->game_id)->orderBy('id', 'desc')->first();
        $tickets = $tickets->to;
        $winTicket = mt_rand(1, $tickets);

        $bets = JackpotBets::where('game_id', $this->game->game_id)->orderBy('id', 'desc')->get();
        foreach ($bets as $bet) if (($bet->from <= $winTicket) && ($bet->to >= $winTicket)) $winBet = $bet;
        if (is_null($winBet)) return ['success' => false];

        $winner = User::where('id', $winBet->user_id)->first();
        if (is_null($winner)) return ['success' => false];

        $users = $this->getChancesOfGame($this->game->game_id);

        if ($this->game->winner_id) {
            // Подкрутка.
            $winner2 = User::where('id', $this->game->winner_id)->first();
            // Поиск билетов юзера
            $bets = JackpotBets::where('game_id', $this->game->game_id)->where('user_id', $winner2->id)->get();
            $bet = $bets[mt_rand(0, count($bets) - 1)];
            $winTicket2 = mt_rand($bet->from, $bet->to);

            foreach ($bets as $bet) if (($bet->from <= $winTicket2) && ($bet->to >= $winTicket2)) $winBet = $bet;
            if (is_null($winBet)) return ['success' => false];

            $winTicket = $winTicket2;
            $winner = $winner2;
        }

        $members = [];
        foreach ($users as $user) {
            for ($i = 0; $i < ceil($user['chance']); $i++) {
                $members[] = [
                    'avatar' => $user['avatar'],
                    'color' => $user['color']
                ];
            }
        }

        shuffle($members);

        $win = [
            'avatar' => $winner->avatar,
            'color' => $winBet->color
        ];

        $members[80] = $win;

        $this->game->winner_id = $winner->id;
        $this->game->winner_chance = $this->getChanceByUser($winner->id, $this->game->game_id);
        $this->game->winner_ticket = $winTicket;
        $this->game->winner_username = $winner->username;
        $this->game->winner_avatar = $winner->avatar;
        $this->game->save();

        $this->game->winner_sum = $this->sendMoney($this->game->game_id);
        $this->game->save();

        return response()->json([
            'members' => $members,
            'hash' => $this->game->hash,
            'winner' => [
                'username' => $winner->username,
                'avatar' => $winner->avatar,
                'sum' => round($this->game->winner_sum, 2),
                'chance' => $this->getChanceByUser($winner->id, $this->game->game_id),
                'ticket' => $this->game->winner_ticket
            ],
            'ml' => mt_rand(7043, 7116),
            'game' => [
                'price' => round($this->game->price, 2)
            ]
        ]);
    }

    public function sendMoney($game_id)
    {
        $game = Jackpot::where('status', 2)->where('game_id', $game_id)->first();
        $all_bets = JackpotBets::where('game_id', $game->game_id)->sum('sum');
        $w_bet = JackpotBets::where('game_id', $game->game_id)->where('user_id', $game->winner_id)->sum('sum');
        $sum = round($w_bet + (($all_bets - $w_bet) - ($all_bets - $w_bet) / 100 * 10), 2);
        $user = User::where(['id' => $game->winner_id])->first();
        if (!is_null($user)) {
            $user->balance += $sum;
            $user->save();
        } else {
            $sum = $game->price;
        }

        $game->status = 3;
        $game->save();

        return $sum;
    }


    public function getStatus(Request $r)
    {
        $game = Jackpot::orderBy('game_id', 'desc')->first();
        $min = floor(20 / 60);
        $sec = floor(20 - ($min * 60));

        if (count($this->getChancesOfGame( $game->game_id)) >= 2) {
            if ($game->status < 1) {
                $game->status = 1;
                $game->save();
                $this->StartTimer();
            }
        }

        return response()->json([
            'id' => $game->game_id,
            'min' => $min,
            'sec' => $sec,
            'time' => 20,
            'status' => $game->status
        ]);
    }

    public function setStatus(Request $r)
    {
        $status = $r->get('status');

        Jackpot::where('game_id', $this->game->game_id)->update([
            'status' => $status
        ]);

        return [
            'msg' => 'Статус изменен на ' . $status . '!',
            'type' => 'success'
        ];
    }

    private function getColor()
    {
        $color = str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
        return $color;
    }

    private function getGameBets()
    {
        if (is_null($this->game)) $this->game = Jackpot::create([
            'game_id' => 1,
            'hash' => bin2hex(random_bytes(16))
        ]);
        $bets = JackpotBets::where('game_id', $this->game->game_id)->orderBy('id', 'desc')->get();

        foreach ($bets as $key => $bet) {
            $user = User::where('id', $bet->user_id)->first();
            $bets[$key]->username = $user->username;
            $bets[$key]->avatar = $user->avatar;
            $bets[$key]->rank = $user->rank;
            $bets[$key]->chance = $this->getChanceByUser($user->id, $this->game->game_id);
        }
        return $bets;
    }

    private function getGame()
    {
        $game = Jackpot::orderBy('game_id', 'desc')->first();
        return $game;
    }

    public function getTime()
    {
        $min = floor(20 / 60);
        $sec = floor(20 - ($min * 60));

        if ($min == 0) $min = '00';
        if ($sec == 0) $sec = '00';
        if (($min > 0) && ($min < 10)) $min = '0' . $min;
        if (($sec > 0) && ($sec < 10)) $sec = '0' . $sec;
        return [$min, $sec, 20];
    }

    private function StartTimer()
    {
        $min = floor(20 / 60);
        $sec = floor(20 - ($min * 60));
        $this->redis->publish('jackpot.timer', json_encode([
            'min' => $min,
            'sec' => $sec,
            'time' => 20
        ]));
    }

    private function findUser($id)
    {
        $user = User::where('id', $id)->first();
        return $user;
    }

    private function getChanceByUser($user, $game)
    {
        $chance = 0;
        if (!is_null($user)) {
            $value = JackpotBets::where('game_id', $game)->where('user_id', $user)->sum('sum');
            $game = Jackpot::where(['game_id' => $game])->first();
            $chance = round(($value / $game->price) * 100);
        }
        return $chance;
    }

    public static function getChancesOfGame($gameid)
    {
        $game = Jackpot::where('game_id', $gameid)->first();
        $users = [];
        if (!$game) return;
        $bets = JackpotBets::where('game_id', $game->game_id)->orderBy('game_id', 'desc')->get();
        foreach ($bets as $bet) {
            $find = 0;
            foreach ($users as $user) if ($user == $bet->user_id) $find++;
            if ($find == 0) $users[] = $bet->user_id;
        }

        // get chances
        $chances = [];
        foreach ($users as $user) {
            $user = User::where('id', $user)->first();
            $value = JackpotBets::where('game_id', $game->game_id)->where('user_id', $user->id)->sum('sum');
            $colors = JackpotBets::where('game_id', $game->game_id)->where('user_id', $user->id)->get();
            $chance = round(($value / $game->price) * 100);
            foreach ($colors as $cl) {
                $color = $cl->color;
                $betid = $cl->id;
            }
            $chances[] = [
                'id' => $user->id,
                'username' => $user->username,
                'avatar' => $user->avatar,
                'sum' => $value,
                'color' => $color,
                'chance' => round($chance, 2)
            ];
        }

        usort($chances, function ($a, $b) {
            return ($b['chance'] - $a['chance']);
        });

        return $chances;
    }

    public function history()
    {
        $games = Jackpot::where('status', 3)->where('updated_at', '>=', Carbon::today())->orderBy('game_id', 'desc')->get();
        if (is_null($games)) return redirect()->route('jackpot');
        $history = [];
        foreach ($games as $game) {
            $winner = User::where('id', $game->winner_id)->first();
            if (isset($winner)) {
                $history[] = [
                    'game_id' => $game->game_id,
                    'winner_id' => $game->winner_id,
                    'winner_name' => $winner->username,
                    'winner_avatar' => $winner->avatar,
                    'winner_chance' => $game->winner_chance,
                    'winner_sum' => $game->winner_sum,
                    'winner_ticket' => $game->winner_ticket,
                    'hash' => $game->hash,
                    'data' => Carbon::parse($game->updated_at)->diffForHumans(),
                    'price' => $game->price,
                    'bets' => $this->getChancesOfGame( $game->game_id)
                ];
            }
        }

        return view('jackpotHistory', compact( 'history'));
    }

    public function gameHistory($id)
    {
        $history = Jackpot::where('game_id', $id)->first();
        if (is_null($history)) return redirect()->route('jackpotHistory');
        $historyBets = JackpotBets::where('game_id', $id)->get();
        $historyChance = $this->getChancesOfGame($id);

        foreach ($historyBets as $key => $bet) {
            $user = User::where('id', $bet->user_id)->first();
            $value = JackpotBets::where('game_id', $id)->where('user_id', $user->id)->sum('sum');
            $chance = round(($value / $history->price) * 100);
            $historyBets[$key]->username = $user->username;
            $historyBets[$key]->avatar = $user->avatar;
            $historyBets[$key]->chance = $chance;
        }

        $users = $this->getChancesOfGame($id);
        $winner = User::where('id', $history->winner_id)->first();
        if (is_null($winner)) return ['success' => false];

        $winBet = JackpotBets::where('game_id', $id)->where('user_id', $winner->id)->first();

        $members = [];
        foreach ($users as $user) {
            for ($i = 0; $i < ceil($user['chance']); $i++) {
                $members[] = [
                    'avatar' => $user['avatar'],
                    'color' => $user['color']
                ];
            }
        }

        shuffle($members);

        $win = [
            'avatar' => $winner->avatar,
            'color' => $winBet->color
        ];

        $members[80] = $win;
        return view('pages.gameHistory', compact('history', 'historyBets', 'historyChance', 'members', 'winner'));
    }
}
