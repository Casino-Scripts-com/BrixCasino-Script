<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Promocodes;
use App\Models\PromoLog;
use App\Models\Payments;
use App\Models\Withdraws;
use App\Models\Settings;
use App\Models\Dice;
use App\Models\Mines;
use App\Models\Wheel;
use App\Models\WheelBets;
use App\Models\Crash;
use App\Models\CrashBets;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Datatables;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function loadDice(Request $r){
        $user = User::where(['id' => $r->user_id])->first();
        
      
        $page = $r->page - 1;
        $nPage = $page * 10;
        
       
        $history = Games::where(['user_id' => $user->id, 'game' => 'DICE'])->skip($nPage)->orderBy('id', 'desc')->take(10)->get();
       
        $count_history = Games::where(['user_id' => $user->id, 'game' => 'DICE'])->count();
        
        
        return response()->json(['success' => 'true', 'history' => $history]);
        
    }

    public function index() {
        $users_balance = User::sum('balance');
        $users = User::count();
        $new_users = User::where('created_at','>=',Carbon::today())->count();

        $pay_today = Payments::where('created_at','>',Carbon::today())->where('status', 1)->sum('amount');
        $pay_week = Payments::where('created_at', '>=', Carbon::now()->subDays(7))->where('status', 1)->sum('amount');
        $pay_all = Payments::where('status', 1)->sum('amount');

        $with_today = Withdraws::where('created_at','>',Carbon::today())->where('status', 1)->sum('amount');
        $with_week = Withdraws::where('created_at', '>=', Carbon::now()->subDays(7))->where('status', 1)->sum('amount');
        $with_all = Withdraws::where('status', 1)->sum('amount');

        $last_pay = Payments::where('status', 1)->orderBy('id', 'desc')->paginate(5);
        $last_withdraws = Withdraws::where('status', 1)->orderBy('id', 'desc')->paginate(5);

        return view('admin.index', compact('users_balance', 'pay_today', 'pay_week', 'pay_all', 'last_pay', 'last_withdraws', 'users', 'new_users', 'with_today', 'with_week', 'with_all'));
    }

    public function rp_balance() {
        $project_id = 1106;
        $API_KEY = 'e4928e8e27a44a194e031d1d24889e5d';
        $sign = md5( $API_KEY . $project_id . $API_KEY);

        $data = array(
            'project_id'=>$project_id,
            'sign'=>$sign
        );
        $querybuild = http_build_query($data, '', '&');

        $ch = curl_init('https://rubpay.online/api/project/balance?'.$querybuild);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $result = json_decode(curl_exec($ch));
        curl_close($ch);

        return response()->json(['success' => 'true', 'balance' => $result->balance->amount]);
    }

    public function users() {
        $userget = User::paginate(25, ['*'], 'users');
        return view('admin.users', compact('userget'));
    }

    public function searchUser(Request $r) {
        if($r->ajax()) {
                $output="";
                $user = User::where('id','LIKE','%'.$r->userid."%")->orwhere('username', 'LIKE','%'.$r->userid."%")->paginate(10);
                if($user) {
                    foreach ($user as $key => $user) {
                        $output.='<div class="admin__user">'.
                                    '<div class="admin__user_info">'.
                                        '<img src="'.$user->avatar.'">'.
                                        '<span>'.$user->username.'</span>'.
                                    '</div>'.
                                    '<div class="admin__user_ip">'.
                                        'IP: '.$user->ip.
                                    '</div>'.
                                    '<div class="admin__user_balance">'.
                                        'Баланс: '.$user->balance. '₽'.
                                    '</div>'.
                                    '<div class="admin__user_block">
                                        Заблокирован: '.$user->ban.
                                    '</div>'.
                                    '<div class="admin__user_edit">
                                        <a href="/admin/user/edit/'.$user->id.'" class="admin__user_button">Редактировать</a>
                                    </div>
                                </div>';
                    }
                    return Response($output);
                }
                if(!$user) return response()->json(['error' => 'true', 'message' => 'Пользователь не найден']);
            }
    }

    public function sortUser(Request $r) {
        if($r->ajax()) {
                $output="";
                $user = User::orderBy('balance', 'desc')->get();
                if($user) {
                    foreach ($user as $key => $user) {
                        $output.='<div class="admin__user">'.
                                    '<div class="admin__user_info">'.
                                        '<img src="'.$user->avatar.'">'.
                                        '<span>'.$user->username.'</span>'.
                                    '</div>'.
                                    '<div class="admin__user_ip">'.
                                        'IP: '.$user->ip.
                                    '</div>'.
                                    '<div class="admin__user_balance">'.
                                        'Баланс: '.$user->balance. '₽'.
                                    '</div>'.
                                    '<div class="admin__user_block">
                                        Заблокирован: '.$user->ban.
                                    '</div>'.
                                    '<div class="admin__user_edit">
                                        <a href="/admin/user/edit/'.$user->id.'" class="admin__user_button">Редактировать</a>
                                    </div>
                                </div>';
                    }
                    return response()->json(['success' => 'true', 'data' => $output]);
                }
                if(!$user) return response()->json(['error' => 'true', 'message' => 'Пользователь не найден']);
            }
    }

    public function manageUser($id, Request $r) {
        $user = User::where('id', $id)->first();
        $multiacc = User::where('ip', $user->ip)->where('id', '!=', $user->id)->get();
        // $multi_videocard = User::where('videocard', $user->videocard)->where('id', '!=', $user->id)->get();
        $user_pay = Payments::where('user_id', $id)->orderBy('id', 'desc')->paginate(10, ['*'], 'pay');
        $user_with = Withdraws::where('user_id', $id)->orderBy('id', 'desc')->paginate(10, ['*'], 'with');

        $dep = Payments::where('user_id', $user->id)->where('status', 1)->sum('amount');
        $with = Withdraws::where('user_id', $user->id)->where('status', 1)->sum('amount');
        $profit_user = $dep - $with;

        $user_ref = User::where('ref_code', '=', $user->referred_by)->get('id');
        if($user->referred_by != null) $user_ref = $user_ref[0]['id'];
        if($user->referred_by == null) $user_ref = 'Нет реферала';
        $user_refs = User::where('referred_by', '=', $user->ref_code)->paginate(10);
        $count_reft = User::where('referred_by', '=', $user->ref_code)->count();
        $referrals = [];
        foreach($user_refs as $itm) {
            $user_ref_dep = Payments::where('user_id', $itm->id)->where('status', 1)->sum('amount');
            $referrals[] = [
                'user_id' => $itm->id,
                'username' => $itm->username,
                'avatar' => $itm->avatar,
                'pay' => $user_ref_dep,
                'date' => $itm->created_at->format('H:i:s d.m.Y')
            ];
        }

        $wallets_user = [];
        $wallets = Withdraws::where('user_id', $id)->get();
        foreach ($wallets as $wallet) {
            $wallets_user[] = $wallet->number;
        }
        $wallets_user = array_unique($wallets_user);
        
        $mults = [];
        
        $wallets_other = Withdraws::whereIn('number', $wallets_user)->where('user_id', '!=', $id)->get();
        foreach ($wallets_other as $wallet_other) {
            
            $mults[] = $wallet_other->user_id;
    
        }
        foreach ($multiacc as $ab){
            $mults[] = $ab->id;
        }
        // foreach($multi_videocard as $videocard) {
        //     $mults[] = $videocard->id;
        // }
   
        $mults_new = array_unique($mults);
        $multiacc = User::whereIn('id', $mults_new)->get();
        $promolog = PromoLog::where('user_id', $user->id)->orderBy('id', 'desc')->get();

        $usergame_dice = Dice::where('user_id', $user->id)->orderBy('id', 'desc')->paginate(10, ['*'], 'dice');
        $usergame_mines = Mines::where('user_id', $user->id)->orderBy('id', 'desc')->paginate(10, ['*'], 'mines');
        $usergame_wheel = WheelBets::where('user_id', $user->id)->orderBy('id', 'desc')->paginate(10, ['*'], 'wheel');
        $usergame_jackpot = WheelBets::where('user_id', $user->id)->orderBy('id', 'desc')->paginate(10, ['*'], 'jackpot');
        $usergame_crash = CrashBets::where('user_id', $user->id)->orderBy('id', 'desc')->paginate(10, ['*'], 'jackpot');

        $game_crash = [];
        foreach($usergame_crash as $itm) {
            $crash = Crash::where('id', $itm->round_id)->first();
            $game_crash[] = [
                'id' => $itm->id,
                'round_id' => $itm->round_id,
                'multiplier' => $crash->multiplier,
                'price' => $itm->price,
                'withdraw' => $itm->withdraw,
                'won' => $itm->won, 
                'created_at' => $itm->created_at->format('H:i:s d.m.Y')
            ];
        }

        return view('admin/manage_user', compact('user', 'user_pay', 'user_with', 'user_ref', 'profit_user', 'multiacc', 'promolog', 'usergame_dice', 'usergame_mines', 'usergame_jackpot', 'game_crash', 'referrals', 'count_reft', 'usergame_wheel', 'user_refs'));
    }

    public function userMultiaccounts($id, Request $r) {
        $user = User::where('id', $id)->first();
        $multiacc = User::where('ip', $user->ip)->where('id', '!=', $user->id)->get();
        $wallets_user = [];
        $wallets = Withdraws::where('user_id', $id)->get();
        foreach ($wallets as $wallet) {
            $wallets_user[] = $wallet->number;
        }
        $wallets_user = array_unique($wallets_user);
        
        $mults = [];
        
        $wallets_other = Withdraws::whereIn('number', $wallets_user)->where('user_id', '!=', $id)->get();
        foreach ($wallets_other as $wallet_other) {
            
            $mults[] = $wallet_other->user_id;
    
        }
        foreach ($multiacc as $ab){
            $mults[] = $ab->id;
        }
   
        $mults_new = array_unique($mults);
        $multiacc = User::whereIn('id', $mults_new)->get();
        $multiaccount = [];
        foreach($multiacc as $multi) {
            if($multi->ip == $user->ip) {
                $lvl = 'c3';
            }else {
                $lvl = 'c2';
            }
            $multiaccount[$multi->id] = [
                    'id' => $multi->id,
                    'user_link' => 'https://vk.com/id'.$multi->vk_id,
                    'username' => $multi->username,
                    'by' => [$lvl]
            ];
        }
        $data = [
            "multiaccounts" => $multiaccount
        ];

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function banUser($id, Request $r) {
        $user = User::where('id', $id)->first();
        if($user->ban == 1) return redirect()->back()->with('success', 'Аккаунт уже заблокирован');
        User::where('id', $id)->update([
            'ban' => 1
        ]);

        return redirect()->back()->with('success', 'Аккаунт заблокирован!');
    }

    public function unbanUser($id, Request $r) {
        $user = User::where('id', $id)->first();
        if($user->ban == 0) return redirect()->back()->with('success', 'Аккаунт не заблокирован!');
        User::where('id', $id)->update([
            'ban' => 0
        ]);

        return redirect()->back()->with('success', 'Аккаунт разблокирован!');
    }

    public function userSave(Request $r) {
        User::where('id', $r->get('id'))->update([
            'balance' => $r->get('balance'),
            'admin' => $r->get('role'),
            'ban' => $r->get('ban')
        ]);

        return redirect('/admin/user/edit/'.$r->get('id'))->with('success', 'Инфорация обновлена!');
    }

    public function withdraws() {
        $list = Withdraws::where('status', 0)->get();
        $withdraws = [];
        foreach($list as $itm) {
            $user = User::where('id', $itm->user_id)->first();
            $time = $itm->created_at->diffInMinutes(Carbon::now()).' минут назад';
            if($itm->created_at->diffInMinutes(Carbon::now()) > 60) {
                $time = $itm->created_at->diffInHours(Carbon::now()).' часов назад';
            } elseif($itm->created_at->diffInHours(Carbon::now()) >= 24){
                $time = $itm->created_at->diffInDays(Carbon::now()).' дней назад';
            }
            $withdraws[] = [
                'id' => $itm->id,
                'user_id' => $user->id,
                'ip' => $user->ip,
                'username' => $user->username,
                'avatar' => $user->avatar,
                'rank' => $user->rank,
                'system' => $itm->system,
                'number' => $itm->number,
                'amount' => $itm->amount,
                'status' => $itm->status,
                'time' => $time
            ];
        }

        return view('admin/withdraws', compact('withdraws'));
    }

    public function fk_withdraw($id) {
        $withdraw = Withdraws::where('id', $id)->first();

        if($withdraw->system == 'qiwi') {
            $system = 63;
            $com = 1;
            $perc = 4;
        }
        if($withdraw->system == 'fkwallet') {
            $system = 133;
            $com = 0;
            $perc = 0;
        }
        if($withdraw->system == 'card') {
            $system = 94;
            $com = 0;
            $perc = 4;
        }

        if($withdraw->status == 1 || $withdraw->status == 2) return redirect()->route('admin.withdraws')->with('error', 'Выплата отменена или уже обработана');

        $data = array(
            'wallet_id'=>'',
            'purse'=>$withdraw->number,
            'amount'=>$withdraw->amount,
            'desc'=>'Withdraw ZUBRIX '.$withdraw->id,
            'currency'=>$system,
            'sign'=>md5(''.$system.$withdraw->amount.$withdraw->number.'api'),
            'action'=>'cashout',
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fkwallet.com/api_v1.php');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = trim(curl_exec($ch));
        $c_errors = curl_error($ch);
        curl_close($ch);

        $json = json_decode($result, true);
        if($json['status'] == 'error') {
            if($json['desc'] == 'Balance too low') {
                $desc = 'Не хватает баланса';
                return redirect()->route('admin.withdraws')->with('error', $desc);
            } elseif($json['desc'] == 'Cant make payment to anonymous wallets') {
                $desc = 'Данный пользователь использует анонимный кошелек!.';
                return redirect()->route('admin.withdraws')->with('error', $desc);
            } elseif($json['desc'] == 'РЎР»РёС€РєРѕРј С‡Р°СЃС‚С‹Рµ Р·Р°РїСЂРѕСЃС‹ Рє API') {
                $desc = 'Неизвестная ошибка!.';
                return redirect()->route('admin.withdraws')->with('error', $desc);
            } else {
                return redirect()->route('admin.withdraws')->with('error', $json['desc']);
            }

        }

        if($json['status'] == 'info') {
            $withdraw->status = 1;
            $withdraw->save();
            return redirect()->route('admin.withdraws')->with('success', 'Вывод отправлен');
        }
    }

    public function rp_withdraw($id) {
        $withdraw = Withdraws::where('id', $id)->first();
        if($withdraw->system == 'qiwi') {
            $payment_method = 5;
        }
        if($withdraw->system == 'card') {
            $payment_method = 1;
        }
        if($withdraw->system == 'yoomoney') {
            $payment_method = 6;
        }
        if($withdraw->system == 'piastrix') {
            $payment_method = 9;
        }
        if($withdraw->system == 'test') {
            $payment_method = 0;
        }
        if($withdraw->status == 1 || $withdraw->status == 2) return redirect()->route('admin.withdraws')->with('error', 'Выплата отменена или уже обработана');

        $project_id = 1106;
        $API_KEY = 'e4928e8e27a44a194e031d1d24889e5d';
        $currency = 1;
        $order_id = $withdraw->id;
        $amount = $withdraw->amount;
        $wallet = $withdraw->number;
        $withdraw_type = 1;
        $sign = md5( $API_KEY . $project_id . $amount . $order_id . $currency . $payment_method . $wallet . $withdraw_type . $API_KEY);

        $data = array(
            'project_id'=>$project_id,
            'amount'=>$withdraw->amount,
            'order_id'=>$order_id,
            'cy'=>1,
            'payment_method'=>$payment_method,
            'wallet'=>$withdraw->number,
            'withdraw_type'=>1,
            'sign'=>$sign
        );
        $querybuild = http_build_query($data, '', '&');

        $ch = curl_init('https://rubpay.online/pay/withdraw?'.$querybuild);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $result = json_decode(curl_exec($ch));
        curl_close($ch);
        
        if($result->result == '0') {
            return redirect()->back()->with('error', $result->error);
        }

        if($result->result == '1') {
            $withdraw->status = 3;
            $withdraw->save();

            $user = User::where('id', $withdraw->user_id)->first();

            return redirect()->back()->with('success', 'Вывод отправлен');
        }
    }
    
    public function cancel_withdraw($id) {
        $withdraw = Withdraws::where('id', $id)->first();
        $user = User::where('id', $withdraw->user_id)->first();
        if($withdraw->status == 1 || $withdraw->status == 2) return redirect()->route('admin.withdraws')->with('error', 'Выплата отменена или уже обработана');

        $user->balance+=$withdraw->sum;
        $user->save();
        $withdraw->status=2;
        $withdraw->save();
        
        return redirect()->route('admin.withdraws')->with('success', 'Вывод отменён, на баланс возвращено '.$withdraw->amount.'р');
    }

    public function promocodes() {
        $promocodes = Promocodes::orderby('id', 'desc')->get();

        return view('admin/promocodes', compact('promocodes'));
    }

    public function promoNew(Request $r) {
        $code = $r->get('code');
        $amount = $r->get('amount');
        $count_use = $r->get('count_use');
        $have = Promocodes::where('name', $code)->first();
        if(!$code) return redirect()->route('admin.promocodes')->with('error', 'Поле "Код" не может быть пустым!');
        if(!$amount) return redirect()->route('admin.promocodes')->with('error', 'Поле "Сумма" не может быть пустым!');
        if(!$count_use) $count_use = 0;
        if($have) return redirect()->route('admin.promocodes')->with('error', 'Такой код уже существует');

        Promocodes::create([
            'name' => $code,
            'sum' => $amount,
            'activate_limit' => $count_use,
            'type' => 0
        ]);

        return redirect()->route('admin.promocodes')->with('success', 'Промокод создан!');
    }

    public function promoDelete($id) {
        Promocodes::where('id', $id)->delete();

        return redirect()->route('admin.promocodes')->with('success', 'Промокод успешно удалён!');
    }

    public function settings() {
        $settings = Settings::where('id', 1)->first();
        return view('admin/settings', compact('settings'));
    }

    public function gameEnabled($game) {
        $settings = Settings::where('id', 1)->first();
        $game_enabled = $game.'_enabled';
        if($game_enabled == 'dice_enabled') {
            if($settings->dice_enabled == 0) {
                $game_number = 0;
                $enabled = 1;
            } else {
                $enabled = 0;
                $game_number = 1;
            }
        }
        if($game_enabled == 'mines_enabled') {
            if($settings->mines_enabled == 0) {
                $game_number = 0;
                $enabled = 1;
            } else {
                $game_number = 1;
                $enabled = 0;
            }
        }
        if($game_enabled == 'wheel_enabled') {
            if($settings->wheel_enabled == 0) {
                $game_number = 0;
                $enabled = 1;
            } else {
                $game_number = 1;
                $enabled = 0;
            }
        }
        if($game_enabled == 'jackpot_enabled') {
            if($settings->jackpot_enabled == 0) {
                $game_number = 0;
                $enabled = 1;
            } else {
                $game_number = 1;
                $enabled = 0;
            }
        }
        if($game_enabled == 'crash_enabled') {
            if($settings->coin_enabled == 0) {
                $game_number = 0;
                $enabled = 1;
            } else {
                $game_number = 1;
                $enabled = 0;
            }
        }
        Settings::where($game_enabled, $game_number)->update([
            $game_enabled => $enabled
        ]);
        if($enabled == 1) {
            $active = 'active';
        } else {
            $active = 'no_active';
        }
        return response()->json(['success' => 'true', 'active' => $active]);
    }

    public function settingsSave(Request $r) {
        
        Settings::where('id', 1)->update([
            'min_withdraw' => $r->get('min_with'),
            'min_payment' => $r->get('min_pay'),
            'tech_work' => $r->get('tech')
        ]);

        return redirect('/admin/settings')->with('success', 'Настройки сохранены');
    }

    public function stopWheel() {
        $game = Wheel::orderBy('id', 'desc')->first();
        $bets = WheelBets::where('game_id', $game->id)->count();

        if($game->status == 2 || $game->status == 0) return response()->json(['type' => 'error', 'msg' => 'Игра еще не запущена, глобальная остановка не требуется']);

        $this->redis = Redis::connection();
        $this->redis->publish("admin", json_encode(['type' => 'stopWheel']));

        return response()->json(['type' => 'success', 'msg' => 'Wheel перезапущен']);
    }
    
    public function restartWheel() {
        $game = Wheel::orderBy('id', 'desc')->first();
        $bets = WheelBets::where('game_id', $game->id)->count();

        if($bets == 0) return response()->json(['type' => 'error', 'msg' => 'Ставок нет, перезапуск не требуется']);
        if($game->status == 1) return response()->json(['type' => 'error', 'msg' => 'Игра уже запущена, выполните остановку прокрутки']);

        $this->redis = Redis::connection();
        $this->redis->publish("admin", json_encode(['type' => 'reloadWheel']));

        return response()->json(['type' => 'success', 'msg' => 'Wheel перезапущен']);
    }
}
