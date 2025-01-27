<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use http\Env\Response;
use App\Models\User;
use App\Models\Promocodes;
use App\Models\PromoLog;
use App\Models\Withdraws;
use App\Models\Payments;
use App\Models\Settings;
use App\Models\Dice;
use App\Models\Mines;
use App\Models\WheelBets;
use App\Models\Jackpot;
use App\Models\JackpotBets;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Datatables;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class MainController extends Controller
{
    function checkLvl($pays, $lvl){
        $lvls = [0, 250, 500, 1500, 2000, 3000, 4500, 7500, 10000, 15000, 25000];
       
       if ($lvls[0] <= $pays and $pays <= $lvls[1]){
            $gift = 0;
            $newlvl = 1;
       }
       
       if ($lvls[1] <= $pays and $pays <= $lvls[2]){
            $gift = 50;
            $newlvl = 2;
       }
       
       if ($lvls[2] <= $pays and $pays <= $lvls[3]){
            $gift = 75;
            $newlvl = 3;
       }
       
       if ($lvls[3] <= $pays and $pays <= $lvls[4]){
            $gift = 100;
            $newlvl = 4;
       }
       
       if ($lvls[4] <= $pays and $pays <= $lvls[5]){
            $gift = 150;
            $newlvl = 5;
       }
       
       if ($lvls[5] <= $pays and $pays <= $lvls[6]){
            $gift = 200;
            $newlvl = 6;
       }
       
       if ($lvls[6] <= $pays and $pays <= $lvls[7]){
            $gift = 250;
            $newlvl = 7;
       }
       
       if ($lvls[7] <= $pays and $pays <= $lvls[8]){
            $gift = 350;
            $newlvl = 8;
       }
       
       if ($lvls[8] <= $pays and $pays <= $lvls[9]){
            $gift = 500;
           $newlvl = 9;
       }
       
       if ($lvls[9] <= $pays and $pays <= $lvls[10]){
            $gift = 1000;
            $newlvl = 10;
       }
       
       if ($lvls[10] <= $pays){
            $newlvl = 10;
       }
       
       if($newlvl > $lvl){
               $newlvl = $newlvl;
       }else{
           $newlvl = $lvl;
       }
        
        return [$newlvl, $gift];
    }

    public function referral($code) {
        $ref = User::where('ref_code', $code)->first();
        if(!$ref) return redirect('/')->with('error', 'Реф.ссылка недействительна');

        return redirect('/')->withCookie(cookie()->forever('ref', $code));
    }

    public function profile() {
        if(!Auth::check()) return redirect('/')->with('error', 'Авторизуйтесь');
        $bet_dice = Dice::where('user_id', Auth::user()->id)->sum('bet');
        $bet_mines = Mines::where('user_id', Auth::user()->id)->sum('bet');
        $bet_wheel = WheelBets::where('user_id', Auth::user()->id)->sum('price');
        $bet_jackpot = JackpotBets::where('user_id', Auth::user()->id)->sum('sum');
        $sumbet = $bet_dice + $bet_mines + $bet_wheel + $bet_jackpot;

        $win_dice = Dice::where('user_id', Auth::user()->id)->max('win');
        $win_mines = Mines::where('user_id', Auth::user()->id)->max('win');
        $win_wheel = WheelBets::where('user_id', Auth::user()->id)->max('win_sum');
        $win_jackpot = Jackpot::where('winner_id', Auth::user()->id)->max('winner_sum');
        $maxwin = max($win_dice, $win_mines, $win_wheel, $win_jackpot);

        $sumgame_dice =  Dice::where('user_id', Auth::user()->id)->count();
        $sumgame_mines =  Mines::where('user_id', Auth::user()->id)->count();
        $sumgame_wheel =  WheelBets::where('user_id', Auth::user()->id)->count();
        $sumgame_jackpot =  JackpotBets::where('user_id', Auth::user()->id)->count();
        $sumgame = $sumgame_dice + $sumgame_mines + $sumgame_wheel + $sumgame_jackpot;

        $user_pays = Payments::where('user_id', Auth::user()->id)->where('status', 1)->sum('amount');
        $rank = Auth::user()->rank;
        $gift = [0, 0, 50, 75, 100, 150, 200, 250, 350, 500, 1000];
        if($rank >= 10) {
            $user_gift = 0;
        } else {
            $user_gift = $gift[Auth::user()->rank + 1];
        }
        if($rank == 1){
            $max_pays = 250;
        }
        if($rank == 2){
            $max_pays = 500;
        }
        if($rank == 3){
            $max_pays = 1500;
        }
        if($rank == 4){
            $max_pays = 2000;
        }
        if($rank == 5){
            $max_pays = 3000;
        }
        if($rank == 6){
            $max_pays = 4500;
        }
        if($rank == 7){
            $max_pays = 7500;
        }
        if($rank == 8){
            $max_pays = 10000;
        }
        if($rank == 9){
            $max_pays = 15000;
        }
        if($rank == 10){
            $max_pays = 25000;
        }

        if($user_pays >= 25000) {
            $max_pays = '∞';
        }

        return view('profile', compact('sumbet', 'maxwin', 'sumgame', 'max_pays', 'user_pays', 'user_gift'));
    }

    public function loadTable(Request $r){
        if(!Auth::check()) return response()->json(['error' => 'true', 'message' => 'Пройдите авторизацию']);
        $user = User::where(['id' => Auth::user()->id])->first();
        
        $historyType = $r->historyType;
        $nPage = $r->nPage * 10;
        
        if($historyType == 0){
            $history = Withdraws::where(['user_id' => Auth::user()->id])->skip($nPage)->orderBy('id', 'desc')->take(10)->get();
        }else{
            $history = Payments::where(['user_id' => Auth::user()->id])->skip($nPage)->orderBy('id', 'desc')->take(10)->get();
        }
        
        if($historyType == 0){
            $count_history = Withdraws::where(['user_id' => Auth::user()->id])->count();
        }else{
            $count_history = Payments::where(['user_id' => Auth::user()->id])->count();
        }
        
        if($count_history > 10){
            $morePage = 1;
        }else{
            $morePage = 0;
        }
        
        if ($nPage == 0){
            $lastPage = 0;
        }else{
            $lastPage = 1;
        }
        
        if (count($history) < 10){
             $nextPage = 0;
        }else{
            $nextPage = 1; 
        }
        
        return response()->json(['nextPage' => $nextPage, 'success' => 'true', 'morePage' => $morePage, 'history' => $history, 'lastPage' => $lastPage]);
        
    }

    public function daily() {
        if(!Auth::check()) return response()->json(['error' => 'true', 'message' => 'Авторизуйтесь']);

        $user = User::where(['id' => Auth::user()->id])->first();
        // if(!$this->isSubscribed($user->vk_id)) return response()->json(['error' => 'true', 'message' => 'Подпишитесь на нашу группу ВКонтакте!']);
        if($user->tg_id == NULL) return response()->json(['error' => 'true', 'message' => 'Привяжите свой Telegram-аккаунт!']);
        $to_next = 86400 - (time() - $user->bonus_time);
        if(time() - $user->bonus_time < 86400) return response()->json(['error' => 'true', 'message' => 'Следующий бонус через: '.$this->timeEncode($to_next)]);
        if($user->balance > 1) return response()->json(['error' => 'true', 'message' => 'Ваш баланс больше 0']);
        $bonus_sum = [0, 300, 500, 700, 1000, 1000, 1500, 1700, 2000, 2500, 5000];
        $bonus = round(rand(100,$bonus_sum[$user->rank])/100,2);
        $user->balance+=$bonus;
        $user->bonus_time=time();
        $user->save();
        
        return response()->json(['success' => 'true', 'balance' => $user->balance, 'bonus' => $bonus]);
    }

    public function promo(Request $r) {

        if(!Auth::check()) return response()->json(['error' => 'true', 'message' => 'Авторизуйтесь!']);
        DB::beginTransaction();
        $user = User::where(['id' => Auth::user()->id])->lockForUpdate()->first();
        $code = strtolower(htmlspecialchars($r->get('code')));
        if(!$code) return response()->json(['error' => 'true', 'message' => 'Введите промокод!']);
        if($user->tg_id == NULL) return response()->json(['error' => 'true', 'message' => 'Привяжите свой Telegram-аккаунт!']);
        if($user->balance > 5) return response()->json(['error' => 'true', 'message' => 'Ваш баланс больше 5!']);
        $promocode = Promocodes::where('name', $code)->first();
        if(!$promocode) return response()->json(['error' => 'true', 'message' => 'Промокода не существует!']);
        $check = Promolog::where('user_id', $user->id)->where('name', $code)->first();
        $check_day = Promolog::where('user_id', $user->id)->where('created_at','>',Carbon::today())->count();
        $active_with = Withdraws::where('user_id', $user->id)->where('status', 0)->count();
        if($active_with >= 1) return response()->json(['error' => 'true', 'message' => 'У вас имеются активные выводы!']);
        if($check) return response()->json(['error' => 'true', 'message' => 'Вы уже активировали этот промокод!']);
        if($promocode->activate >= $promocode->activate_limit) return response()->json(['error' => 'true', 'message' => 'Превышено кол-во активаций промокода!']);
        $promo_x = [0, 1, 1, 2, 3, 4, 4, 5, 5, 6, 7];
        $promocode->activate+=1;
        $promocode->save();
        $user->balance+=$promocode->sum * $promo_x[$user->rank];
        $user->wager += $promocode->sum * 3;
        $user->save();

        PromoLog::insert([
            'user_id' => $user->id,
            'sum' => $promocode->sum,
            'name' => $code
        ]);

        DB::commit();
        return response()->json(['success' => 'true', 'message' => 'Промокод '.$code.' успешно активирован!']);
    }

    public function vk_bonus() {
        if(!Auth::check()) return response()->json(['error' => 'true', 'message' => 'Вы не авторизованы']);
        $user = User::where('id', Auth::user()->id)->first();
        if(!$this->isSubscribed($user->vk_id)) return response()->json(['error' => 'true', 'message' => 'Вы не подписались на нашу группу!']);
        if($user->vk_bonus !== 0) return response()->json(['error' => 'true', 'message' => 'Вы уже получали данный бонус!']);
        $user->balance += 10;
        $user->vk_bonus = 1;
        $user->save();
        return response()->json(['success' => 'true', 'balance' => $user->balance]);
    }

    public static function isSubscribed($id) {
        try {
            $arr = json_decode(file_get_contents("https://api.vk.com/method/groups.isMember?access_token=vk1.a.PKcK-MN4L1JSrqAN7B2tk9DoE5WbrpVhIIFmmQFptlJhx6ibpuynobzBAzxvkwLzjcLflPcJXd17dnfonPU5XaU8tZWhQXcWYcoR-deT76rdgpRySeP0L5loOVAiqsyJHfhbEJMBxPMbXjl6nB5p-6E-cx-spfWZxNaXnIxpl7iVQ&group_id=219123736&user_id=". $id . "&v=5.103"), true);
            return isset($arr['error']) ? false : ($arr['response'] == 1 ? true : false);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function pay($system, Request $r) {
        $settings = Settings::orderBy('id', 'desc')->first();
        $size = preg_replace('/[^0-9.]/', '', $r->size);
        if(!Auth::check()) return redirect('/')->with(['error', 'Авторизуйтесь']);
        if($system == NULL) return response()->json(['error' => 'true', 'message' => 'Выбирите платёжную систему']);
        if($size < $settings->min_payment) return response()->json(['error' => 'true', 'message' => 'Минимальная сумма пополнения '.$settings->min_payment.' рублей']);
        if($size > $settings->max_payment) return response()->json(['error', 'Максимальная сумма пополнения 25.000 рублей']);
        if($system == 'crypto') return response()->json(['error', 'Временно недоступно']);

        return response()->json(['success' => 'true', 'redirect' => $this->createLink($system, $r->size)]);
    }

    public function createLink($system, $size) {
        $settings = Settings::orderBy('id', 'desc')->first();
        $deposit = Payments::create([
            'user_id' => Auth::user()->id,
            'amount' => $size,
            'system' => $system
        ]);

        if($system == 'qiwi') {
            $payment_method = 2;
            $method = 'qiwi';
        }
        if($system == 'card') {
            $payment_method = 1;
            $method = 'card';
        }
        if($system == 'yoomoney') {
            $payment_method = 3;
            $method = 'yandexmoney';
        }
        if($system == 'sbp') {
            $payment_method = 7;
            $method = 'sbp';
        }

        if($system == 'fkwallet') {
            $merchant_id = '';
            $secret_word = '^*]iX6MN';
            $order_id = $deposit->id;
            $order_amount = $size;
            $currency = 'RUB';
            $sign = md5($merchant_id.':'.$order_amount.':'.$secret_word.':'.$currency.':'.$order_id);

            return 'https://pay.freekassa.ru/?m=38545&oa='.$order_amount.'&i=&currency=RUB&em=&phone=&o='.$order_id.'&pay=PAY&s='.$sign;
        }

        if($system == 'crypto') {
            $shop_id = "";
            $amount = $size;
            $order_id = $deposit->id;
            $currency = "RUB";
            $apikey = ".eyJpZCI6NjQ0OCwiZXhwIjo4ODAxoFmwR_0TjabkHiCQvqH8Dc8Gt25MV8TPMLYAWQ";

            $data = array(
                'shop_id'=>$shop_id,
                'amount'=>$amount,
                'order_id'=>$order_id,
                'currency'=>$currency
            );

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $headers = array();
            $headers[] = "Authorization: Token ".$apikey;

            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_URL, 'https://api.cryptocloud.plus/v1/invoice/create');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($curl);
            curl_close($curl);

            $callback = json_decode($result, true);

            return $callback['pay_url'];
        }

        if($system != 'fkwallet') {
            $project_id = 1192;
            $API_KEY = 'e4928e8e27a44a194e031d1d24889e5d';
            $currency = 1;
            $order_id = $deposit->id;
            $amount = $size;

            $sign = md5( $API_KEY . $project_id . $order_id . $amount . $currency . $API_KEY);

            return 'https://rubpay.online/pay/create?project_id='.$project_id.'&payment_method='.$payment_method.'&amount='.$amount.'&cy=1&order_id='.$order_id.'&trader_id=&sign='.$sign;
        }
    }

    public function fail() {
        return redirect('/')->with('error', 'Ошибка при пополнении баланса');
    }

    public function checkPaymentCc(Request $r) {
        $order_id = $r->order_id;
        $payment = Payments::where('id', $order_id)->first();
        $status = $r->status;

        if($status == 'success') {
            if(!$payment) return 'Платеж не найден';

            $user = User::where('id', $payment->user_id)->first();

            if($user->referred_by != NULL) {
                $refer = User::where('ref_code', $user->referred_by)->first();
                if(!$refer) return 'Ошибка: рефер не найден';
                
                if($refer->youtuber == 0) {
                    $refer->balance+=($payment->amount/100)*10;
                    $refer->ref_money +=($payment->amount/100)*10;
                    $refer->save();
                }
                if($refer->youtuber == 1) {
                    $refer->balance+=($payment->amount/100)*20;
                    $refer->ref_money +=($payment->amount/100)*20;
                    $refer->save();
                }
            }
            $dep_wager = $user->wager + $payment->amount;
            
            $user->wager = $dep_wager;
            $user->balance += $payment->amount;
            $user->save();
            $payment->status = 1;
            $payment->save();

            die('OK');
        }
    }

    public function checkPaymentRp(Request $r) {
        $order_id = $r->order_id;
        $payment = Payments::where('id', $order_id)->first();
        $project_id = 1192;
        $API_KEY = 'e4928e8e27a44a19431d1d24889e5d';
        $amount = $r->amount;
        $status = $r->status;
        $currency = 1;
        $payment_id = $r->payment_id;
        $hash = md5( $project_id . $order_id . $payment_id . $amount . $currency . $status . $API_KEY);

        if($r->hash != $hash) {
            die('Неверный hash');
        }

        if($r->hash == $hash) {
            $payment = Payments::where('id', $order_id)->first();
            if(!$payment) return 'Платеж не найден';

            $user = User::where('id', $payment->user_id)->first();

            if($user->referred_by != NULL) {
                $refer = User::where('ref_code', $user->referred_by)->first();
                if(!$refer) return 'Ошибка: рефер не найден';
                
                if($refer->youtuber == 0) {
                    $refer->balance+=($payment->amount/100)*10;
                    $refer->ref_money +=($payment->amount/100)*10;
                    $refer->save();
                }
                if($refer->youtuber == 1) {
                    $refer->balance+=($payment->amount/100)*20;
                    $refer->ref_money +=($payment->amount/100)*20;
                    $refer->save();
                }
            }
            
            $payment->status = 1;
            $payment->save();

            $user_pays = Payments::where('user_id', $user->id)->where('status', 1)->sum('amount');
            $pays = $user_pays;
            $lvl = $user->rank;
            
            $res = self::checkLvl($pays, $lvl);
            $rank = $res[0];
            $gift = $res[1];
            
            $user->wager += $payment->amount;
            $user->balance += $amount;
            $user->save();
            if($rank > $lvl) {
                $user->rank = $rank;
                $user->balance += $gift;
                $user->save();
            }

            die('OK');
        }
    }

    public function successWithdrawRp(Request $r) {
        $order_id = $r->order_id;
        $project_id = 1;
        $API_KEY = 'e4928e8e27a4194e031d1d24889e5d';
        $amount = $r->amount;
        $status = $r->status;
        $currency = 1;
        $payment_id = $r->payment_id;
        $hash = md5( $project_id . $order_id . $payment_id . $amount . $currency . $status . $API_KEY);

        if($r->hash != $hash) {
            die('Неверный hash');
        }

        if($r->hash == $hash) {
            $withdraw = Withdraws::where('id', $order_id)->first();
            $user = User::where('id', $withdraw->user_id)->first();
            if(!$withdraw) return 'Вывод не найден';
            
            if($status == 2) {
                if($user->tg_id != NULL) {
                    $with_text = "*Ваш вывод обработан!*
Система: `$withdraw->system` 
Сумма: `$withdraw->amount`";
                    $ch = curl_init();
                        curl_setopt_array(
                            $ch,
                            array(
                                CURLOPT_URL => 'https://api.telegram.org/bot6093661392:AAGc7YpzY1u4Infv1IFbkdSB4k1bXz9zOXw/sendMessage',
                                CURLOPT_POST => TRUE,
                                CURLOPT_RETURNTRANSFER => TRUE,
                                CURLOPT_TIMEOUT => 10,
                                CURLOPT_POSTFIELDS => array(
                                    'chat_id' => $user->tg_id,
                                    'text' => $with_text,
                                    'parse_mode' => 'MarkDown',
                                ),
                            )
                        );
                        curl_exec($ch);
                }
                $withdraw->status = 1;
                $withdraw->save();
                die('OK');
            }
        }
    }

    public function checkPaymentFk(Request $r) {
        $merchant_id = '';
        $merchant_secret = '';
        // $amount = $r->amount;
        // $merchant_orderid = $r->merchant_order_id;
        // $rsign = $r->sign;

        $sign = md5($merchant_id.':'.$_REQUEST['AMOUNT'].':'.$merchant_secret.':'.$_REQUEST['MERCHANT_ORDER_ID']);

        if ($sign != $_REQUEST['SIGN']) die('wrong sign');
        
        if($sign == $_REQUEST['SIGN']) {
            $payment = Payments::where('id', $_REQUEST['MERCHANT_ORDER_ID'])->first();
            if(!$payment) return 'Платеж не найден';

            $user = User::where('id', $payment->user_id)->first();

            if($user->referred_by != NULL) {
                $refer = User::where('ref_code', $user->referred_by)->first();
                if(!$refer) return 'Ошибка: рефер не найден';

                $refer->balance+=($payment->amount/100)*10;
                $refer->ref_money +=($payment->amount/100)*10;
                $refer->save();
            }

            $payment->status = 1;
            $payment->save();

            $user_pays = Payments::where('user_id', $user->id)->where('status', 1)->sum('amount');
            $pays = $user_pays;
            $lvl = $user->rank;
            
            $res = self::checkLvl($pays, $lvl);
            $rank = $res[0];
            $gift = $res[1];
            
            $user->wager += $payment->amount;
            $user->balance+= $payment->amount;
            $user->save();
            if($rank > $lvl) {
                $user->rank = $rank;
                $user->balance += $gift;
                $user->save();
            }

            die('YES');
        }    
    }

    public function success() {
        return redirect('/')->with('success', 'Баланс был успешно пополнен');
    }

    public function withdraw(Request $r) {
        $settings = Settings::where('id', 1)->first();
        if(!Auth::check()) return response()->json(['error' => 'true', 'message' => 'Авторизуйтесь!']);
        if(Auth::user()->ban == 1) return response()->json(['type' => 'error', 'msg' => 'Вы заблокированы, обратитесь в поддержку']);
        DB::beginTransaction();
        $user = User::where(['id' => Auth::user()->id])->lockForUpdate()->first();
        $amount = $r->get('amount');
        $number = $r->get('number');
        $system = $r->get('system');
        $final_amount = $r->get('final_amount');
        if(!$amount) return response()->json(['error' => 'true', 'message' => 'Введите сумму!']);
        if(!$system) return response()->json(['error' => 'true', 'message' => 'Выберите систему!']);
        if(!$number) return response()->json(['error' => 'true', 'message' => 'Введите номер для вывода!']);
        if($user->balance < $amount) return response()->json(['error' => 'true', 'message' => 'Недостаточно средств!']);
        // $checkdeposit = Payments::query()->where([['created_at', '>=', \Carbon\Carbon::today()->subDays(7)], ['status', 1], ['user_id', Auth::User()->id]])->sum('amount');
        // if($checkdeposit < 150) return response()->json(['error' => 'true', 'message' => 'Для вывода нужен депозит 150 рублей за 7 дней!']);
        if($user->wager != 0) return response()->json(['error' => 'true', 'message' => 'Вам осталось отыграть - '.$user->wager]);

        $user->balance -= $amount;
        $user->save();

        if($system == 'qiwi') {
            $comission = 5;
        } elseif($system == 'card') {
            $comission = 5;
        } elseif($system == 'yoomoney') {
            $comission = 3;
        } elseif($system == 'piastrix') {
            $comission = 2;
        } elseif($system == 'fkwallet') {
            $comission = 3;
        }

        $comamount = $amount - ($amount * $comission / 100);

        Withdraws::insert([
            'user_id' => $user->id,
            'system' => $system,
            'number' => $number,
            'sum' => $amount,
            'amount' => $comamount
        ]);

        DB::commit();

        return response()->json(['success' => 'true', 'balance' => $user->balance]);
    }

    public function user_cancel($id) {
        $withdraw = Withdraws::where('id', $id)->first();
        $user = User::where('id', Auth::user()->id)->first();
        if($withdraw->status == 1 || $withdraw->status == 2) return redirect()->back()->with('error', 'Выплата отменена или уже обработана');
        if($withdraw->status == 3) return redirect()->back()->with('error', 'Данный вывод на финальной стадии');
        if($withdraw->user_id != Auth::user()->id) return redirect()->back()->with('error', 'Данная выплата не ваша!');

        $user->balance+=$withdraw->sum;
        $user->save();
        $withdraw->status=2;
        $withdraw->save();
        
        return redirect()->back()->with('success', 'Вы успешно отменили вывод!');
    }

    public function userCard(Request $r) {
        if(Auth::check()) {
            $id = $r->get('id');
            User::where('id', Auth::user()->id)->update([
                'videocard' => $id
            ]);
        }
    }

    public function getWord($number, $type) {
        if($type == 'coin') $suffix = array("монету", "монеты", "монет");
        if($type == 'min') $suffix = array("минуту", "минуты", "минут");
        if($type == 'sec') $suffix = array("секунду", "секунды", "секунд");
        if($type == 'hours') $suffix = array("час", "часа", "часов");

        $keys = array(2, 0, 1, 1, 1, 2);
        $mod = $number % 100;
        $suffix_key = ($mod > 7 && $mod < 20) ? 2: $keys[min($mod % 10, 5)];
        return $suffix[$suffix_key];
    }

    public function timeEncode($init) {
        $hours = floor($init / 3600);
        $minutes = floor(($init / 60) % 60);
        $seconds = $init % 60;
        if($hours >= 1 && $minutes == 0) return $hours.' '.$this->getWord($hours, 'hours');
        if($hours >= 1) return $hours.' '.$this->getWord($hours, 'hours').' '.$minutes.' '.$this->getWord($minutes, 'min');
        if($minutes <= 59 && $minutes > 0 && $seconds > 0) return $minutes.' '.$this->getWord($minutes, 'min').' '.$seconds.' '.$this->getWord($seconds, 'sec');
        if($minutes <= 59 && $minutes > 0) return $minutes.' '.$this->getWord($minutes, 'min');
        return $seconds.' '.$this->getWord($seconds, 'sec');
    }
}
