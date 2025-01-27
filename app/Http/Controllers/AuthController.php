<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class AuthController extends Controller
{
    public function auth(Request $r) {
        return Socialite::driver('vkontakte')->redirect();
    }

    public function callback() {
        $user = json_decode(json_encode(Socialite::driver('vkontakte')->user()));
        if(isset($user->returnUrl)) return redirect('/');
        $user = $user->user;
        $user = $this->createOrGetUser($user);
        Auth::login($user, true);
        return redirect()->intended('/');
    }
    public function createOrGetUser($user) {
        if ('vkontakte') {
            $u = User::where('vk_id', $user->id)->first();
            if ($u) {
                $username = $user->first_name.' '.$user->last_name;
                    User::where('vk_id', $user->id)->update([
                        'username' => $username,
                        'avatar' => $user->photo_200,
                        'ip' => request()->ip()
                    ]);
                $user = $u;
            } else {
                $username = $user->first_name.' '.$user->last_name;
                $user = User::create([
                    'username' => $username,
                    'avatar' => $user->photo_200,
                    'ip' => request()->ip(),
                    'vk_id' => $user->id,
                    'unique_id' => \Str::random(8),
                    'ref_code' => bin2hex(random_bytes(5)),
                    'referred_by' => is_null(Cookie::get('ref')) ? null : Cookie::get('ref'),
                    'date' =>  date("d.m.Y")
                ]);
            }
        }
        return $user;
    }

    public function login(Request $r) {
        $email = $r->email;
        $password = $r->password;

        $user = User::where(['username' => $email, 'password' => $password])->first();

        if($user) {
            Auth::login($user);
        } elseif(!$user) {
            return response()->json(['error' => 'true', 'message' => 'Неверные данные!']);
        }

        return response()->json(['success' => 'true']);
    }

    public function register(Request $r) {
        $email = $r->email;
        $password = $r->password;

        $search_user = User::where('username', $email)->first();

        if($search_user) {
            return response()->json(['error' => 'true', 'message' => 'Почта уже занята!']);
        }

        if(strlen($password) < 8) {
            return response()->json(['error' => 'true', 'message' => 'Пароль должен быть от 8 символов']);
        }

        $user = User::create([
            'username' => $email,
            'password' => $password,
            'avatar' => '/assets/images/profile.jpg',
            'ip' => request()->ip(),
            'vk_id' => NULL,
            'unique_id' => \Str::random(8),
            'ref_code' => bin2hex(random_bytes(5)),
            'referred_by' => is_null(Cookie::get('ref')) ? null : Cookie::get('ref'),
            'date' =>  date("d.m.Y")
        ]);

        Auth::login($user);

        return response()->json(['success' => 'true']);
    }

    public function logout() {
        Cache::flush();
        Auth::logout();
        Session::flush();
        return redirect()->intended('/');
    }
}
