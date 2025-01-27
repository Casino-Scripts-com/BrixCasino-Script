<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slots;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use App\Models\Payments;
use App\Models\Withdraws;
use App\Models\Settings;

class SlotsController extends Controller
{
    const PROVIDERS = [
        '26' => 'Pragmatic Play',
        '50' => 'Wazdan',
        '18' => 'EGT',
        '34' => 'Yggdrassil',
        '19' => 'NetEnt',
        '27' => 'Igrosoft',
        '28' => 'Evolution',
        '37' => 'Habanero',
        '20' => 'Microgaming',
        '41' => 'Scientific Games',
        '22' => 'Amatic',
        '42' => 'Kajot',
        '43' => 'Novomatic',
        '44' => 'Ainsworth',
        '45' => 'Apollo',
        '33' => 'Play`n GO',
        '23' => 'Quickspin',
        '47' => 'Aristocrat',
        '48' => 'Apex',
        '49' => 'Merkur',
        '40' => 'Sport'
    ];

    public function getGames(Request $request)
    {
        $show = $request->page * 30 - 30;

        $slots = Slots::orderBy('top', 'desc')->where([
            [function ($query) use ($request) {
                if(($provider = $request->provider)) {
                    $query->where('category_id', $provider)->get();
                }
                if(($search = $request->search)) {
                    $query->where('title', 'like', '%' .$search. '%')->get();
                }
            }]
        ])->where([['status', 1]])->offset($show)->limit(30)->get();

        foreach($slots as $slot) {
            $slot->provider = self::PROVIDERS[$slot->category_id];
        }

        return [
            'games' => $slots
        ];
    }

    public function parseSlots() {
        $data = [
            "cmd" => "gamesList",
            "hall" => "",
            "key" => "",
            "language" => "ru",
            "cdnUrl" => 'https://cdn.lvslot.net/'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://tbs2api.dark-a.com/API/');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = json_decode(curl_exec($ch));
        curl_close($ch);
        
        foreach($output->content->gameList as $game) {
            $slot = Slots::where('slot_code', $game->id)->first();
            if(!$slot) continue;

            $slot->update(['image' => $game->img]);
        }

        return 'OK';
    }

    public function getGameURI(Request $request)
    {
        $slot = Slots::where('slot_code', $request->id)->first();
        $user = User::where('id', Auth::id())->first();

        if(!$slot) {
            return ['error' => 'Игра не найдена'];
        }

        if(!$user) {
            return ['error' => 'Авторизуйтесь'];
        }

        //$user_deps = Payments::where('status', 1)->where('user_id', $user->id)->sum('sum');

        //if($user_deps < 100) return ['error' => 'Необходимо пополнить 100р'];

        $data = [
            "cmd" => "openGame",
            "hall" => "",
            "key" => "",
            "language" => "ru",
            "continent" => "eur",
            "login" => $user->id,
            "cdnUrl" => "https://cdn.lvslot.net/",
            "domain" => "https://Untitled-5.png",
            "exitUrl" => "https://Untitled-5.png/slots",
            "demo" => "0",
            "gameId" => $slot->slot_code
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://tbs2api.dark-a.com/API/openGame/');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);

        $output = json_decode($output, true);

        $user->api_token = $output['content']['gameRes']['sessionId'];
        $user->save();
        $url = $output['content']['game']['url'];

        return [
            'url' => $url,
            'image' => $slot->image,
            'name' => $slot->title
        ];
    }

    public function callback(Request $request) {
        header("Connection: close");
        
        switch ($request->cmd)
        {
            case 'getBalance':
                $login = $request->login;
                $data = $this->getBalance($login);
                echo json_encode($data);
            break;  

            case 'writeBet':
                $bet = $request->bet;
                $win = $request->win;
                $session = $request->login;
                $key = $request->key;
                $data = $this->writeBet($bet, $win, $session, $key);
                echo json_encode($data);
            break;
 
            default :
                throw new \Exception("Unknown cmd");
        }               
    

    }
    public function getBalance($login) 
    {
        if ($login) {
            $user = User::lockForUpdate()->where('id', $login)->first();
            return [
                "status"   => "success",
                "error"    => "",
                "login"    => $login,
                "balance"  => number_format($user->balance, 2, '.', ''),
                "currency" => "RUB"            
            ];
        }
        else {
            \Log::info($login);
        }
    }
    public function writeBet($bet, $win, $session, $key)
    {   
        if ($key != '') {
            return [
                'hack' => true
            ];
        }
        if ($bet == null || $win == null || $session == null) {
            return [
                'status' => 'fail',
                'error' => 'object_is_null'
            ];
        }
        $user = User::lockForUpdate()->where('id', $session)->first();

        if(!$user) {
            return [
                'status' => 'fail',
                'error'  => 'user_not_found'
            ];
        }
        
        if($user->balance < $bet) {
            return [
                'status' => 'fail',
                'error'  => 'fail_balance'
            ];
        }
        if($win - $bet > 0 or $bet == 0) {
			$user->balance += $win;
        }
        else {
			$user->balance -= $bet;
        }
        $user->save();
        
        return [
            "status"      => "success",
            "error"       => "",
            "login"       => $user->id,
            "balance"     => number_format($user->balance, 2, '.', ''),
            "currency"    => "RUB",
            "operationId" => time()
        ];
    }

    private function trxCancel($data) {
        return response()->json(['status' => 200]);
    }

    private function trxComplete($data) {
        return response()->json(['status' => 200]);
    }

    private function checkSession($data) {
        if(!$data->session) return response()->json(['status' => 404, 'method' => 'check.session', 'message' => 'Unknown session']);
        $user = User::where('api_token', $data->session)->first();
        if(!$user) return response()->json(['status' => 404, 'method' => 'check.session', 'message' => 'Unknown user']);

        return response()->json(['status' => 200, 'method' => 'check.session', 'response' => ['id_player' => $user->id, 'id_group' => 'default', 'balance' => round($user->type_balance == 0 ? $user->balance * 100 : $user->demo_balance * 100)]]);
    }

    private function checkBalance($data) {
        if(!$data->session) return response()->json(['status' => 404, 'method' => 'check.balance', 'message' => 'Unknown session']);
        $user = User::where('api_token', $data->session)->first();
        if(!$user) return response()->json(['status' => 404, 'method' => 'check.balance', 'message' => 'Unknown user']);

        return response()->json(['status' => 200, 'method' => 'check.balance', 'response' => ['currency' => 'RUB', 'balance' => round($user->type_balance == 0 ? $user->balance * 100 : $user->demo_balance * 100)]]);
    }

    public function userBet($data) {
        if(!$data->session) return response()->json(['status' => 404, 'method' => 'withdraw.bet', 'message' => 'Unknown session']);
        $user = User::where('api_token', $data->session)->first();
        if(!$user) return response()->json(['status' => 404, 'method' => 'withdraw.bet', 'message' => 'Unknown user']);

        if($user->type_balance == 0) {
            if($user->balance < ($data->amount / 100)) return response()->json(['status' => 404, 'method' => 'withdraw.bet', 'message' => 'Fail balance']);
        } else {
            if($user->demo_balance < ($data->amount / 100)) return response()->json(['status' => 404, 'method' => 'withdraw.bet', 'message' => 'Fail balance']);
        }

        $wager = ($user->sum_to_withdraw - $data->amount / 100) < 0 ? 0 : $user->sum_to_withdraw - $data->amount / 100;

        if($user->type_balance == 0) {
            $user->balance -= $data->amount / 100;
            $user->sum_to_withdraw = $wager;
        } else {
            $user->demo_balance -= $data->amount / 100;
        }
        $user->save();

        return response()->json(['status' => 200, 'method' => 'withdraw.bet', 'response' => ['currency' => 'RUB', 'balance' => round($user->type_balance == 0 ? $user->balance * 100 : $user->demo_balance * 100)]]);
    }

    public function userWin($data) {
        if(!$data->session) return response()->json(['status' => 404, 'method' => 'deposit.win', 'message' => 'Unknown session']);
        $user = User::where('api_token', $data->session)->first();
        if(!$user) return response()->json(['status' => 404, 'method' => 'deposit.win', 'message' => 'Unknown user']);

        if($user->type_balance == 0) {
            $user->balance += $data->amount / 100;
        } else {
            $user->demo_balance += $data->amount / 100;
        }
        $user->save();

        return response()->json(['status' => 200, 'method' => 'deposit.win', 'response' => ['currency' => 'RUB', 'balance' => round($user->type_balance == 0 ? $user->balance * 100  : $user->demo_balance * 100)]]);
    }
}
