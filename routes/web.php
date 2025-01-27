<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\SlotsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MinesController;
use App\Http\Controllers\WheelController;
use App\Http\Controllers\DiceController;
use App\Http\Controllers\JackpotController;
use App\Http\Controllers\CrashController;
use App\Http\Middleware\Access;
use App\Http\Middleware\UserBanned;

Route::group(['prefix' => 'slots'], function () {
    Route::any('/getGames', [SlotsController::class, 'getGames']);
    Route::any('/getUrl', [SlotsController::class, 'getGameURI']);
    Route::any('/getUrl', [SlotsController::class, 'getGameURI']);
    Route::any('/callback231', [SlotsController::class, 'callback']);
    Route::get('/parse', [SlotsController::class, 'parseSlots']);
});

Route::group(['middleware' => 'guest'], function() {
    Route::get('/auth/vk', [AuthController::class, 'auth'])->name('vk.auth');
    Route::get('/auth/vk/callback', [AuthController::class, 'callback']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::get('/slots', [PagesController::class, 'slots'])->name('slots');
});

Route::group(['middleware' => 'banned'], function() {
    Route::get('/', [PagesController::class,'index'])->name('index');
    Route::get('/dice', [PagesController::class, 'dice'])->name('dice');
    Route::get('/mines', [PagesController::class, 'mines'])->name('mines');
    Route::get('/wheel', [PagesController::class, 'wheel'])->name('wheel');
    Route::get('/jackpot', [JackpotController::class, 'index'])->name('jackpot');
    Route::get('/crash', [CrashController::class, 'index'])->name('crash');
    Route::get('/parther/{id}', [MainController::class, 'referral']);
    Route::get('/rules', [PagesController::class,'rules'])->name('rules');
    Route::get('/privacy', [PagesController::class,'privacy'])->name('privacy');
    Route::get('/slots', [PagesController::class, 'slots'])->name('slots');

    Route::group(['middleware' => 'auth', 'middleware' => 'banned'], function() {
        Route::post('/user/daily', [MainController::class, 'daily']);
        Route::post('/user/promo', [MainController::class, 'promo']);
        Route::post('/user/withdraw', [MainController::class, 'withdraw']);
        Route::post('/user/card', [MainController::class, 'userCard']);
        Route::post('/user/vk_bonus', [MainController::class, 'vk_bonus']);
        Route::post('/user/wheel/status', [WheelController::class, 'infoWheel']);

        Route::get('/bonus', [PagesController::class,'bonus'])->name('bonus');
        Route::get('/referrals', [PagesController::class, 'referrals'])->name('referrals');
        Route::get('/profile', [MainController::class, 'profile'])->name('profile');

        Route::get('/wallet/pay', [PagesController::class, 'deposit'])->name('pay');
        Route::get('/wallet/withdraw', [PagesController::class, 'withdraw'])->name('withdraw');
        Route::get('/wallet/history', [PagesController::class, 'history'])->name('history');
        Route::get('/wallet/cancel/withdraw/{id}', [MainController::class, 'user_cancel']);
        Route::post('/load/table', [MainController::class, 'loadTable']);

        Route::group(['prefix' => '/dice'], function () {
            Route::post('/bet', [DiceController::class, 'dice']);
        });
        
        Route::group(['prefix' => '/mines'], function () {
            Route::post('/open', [MinesController::class, 'open']);
            Route::post('/create', [MinesController::class, 'create']);
            Route::post('/get', [MinesController::class, 'get']);
            Route::post('/take', [MinesController::class, 'take']);
        });

        Route::group(['prefix' => 'crash'], function() {
            Route::post('/addBet', [CrashController::class, 'createBet']);
            Route::post('/last', [CrashController::class, 'lastBet']);
            Route::post('/cashout', [CrashController::class, 'Cashout']);
            Route::post('/get', [CrashController::class, 'crashGet']);
        });

        Route::post('/wallet/pay/{system}', [MainController::class, 'pay']);

        Route::get('/logout', [AuthController::class, 'logout']);
    });

    Route::post('/load/dice', [AdminController::class, 'loadDice']); 
});

Route::group(['prefix' => '/payment'], function () {
    Route::post('/fk/handle', [MainController::class, 'checkPaymentFk']);
    Route::post('/rp/handle', [MainController::class, 'checkPaymentRp']);
    Route::post('/cc/handle', [MainController::class, 'checkPaymentCc']);
    Route::get('/fail', [MainController::class, 'fail']);
    Route::get('/success', [MainController::class, 'success']);
});
Route::group(['prefix' => '/withdraw'], function () {
    Route::post('/rp/handle', [MainController::class, 'successWithdrawRp']);
});

Route::group(['prefix' => '/api'], function() {
    Route::group(['prefix' => 'wheel'], function() {
        Route::post('/start', [WheelController::class, 'start']);
        Route::post('/open', [WheelController::class, 'open']);
        Route::post('/close', [WheelController::class, 'close']);
        Route::post('/end', [WheelController::class, 'end']);
        Route::post('/get', [WheelController::class, 'getWheel']);
        Route::post('/bet/{color}', [WheelController::class, 'wheel']);
        Route::post('/admin/bet/{color}', [WheelController::class, 'adminwheel']);
    });
    Route::group(['prefix' => 'jackpot'], function() {
        Route::post('/newGame', [JackpotController::class, 'newGame']);
        Route::post('/getSlider', [JackpotController::class, 'getSlider']);
        Route::post('/getStatus', [JackpotController::class, 'getStatus']);
        Route::post('/setStatus', [JackpotController::class, 'setStatus']);
        Route::post('/newBet', [JackpotController::class, 'newBet']);
    });
    Route::group(['prefix' => 'crash'], function() {
        Route::post('/init', [CrashController::class, 'init']);
		Route::post('/slider', [CrashController::class, 'startSlider']);
		Route::post('/newGame', [CrashController::class, 'newGame']);
    });
});

Route::group(['prefix' => '/admin', 'middleware' => 'auth', 'middleware' => 'access:admin'], function () {
    Route::get('/', [AdminController::class,'index'])->name('admin.index');
    Route::get('/users', [AdminController::class,'users'])->name('admin.users');
    Route::get('/users/search', [AdminController::class,'searchUser']);
    Route::post('/users/sort', [AdminController::class,'sortUser']);
    Route::post('/user/ref/sort', [AdminController::class,'sortRefPay']);
    Route::get('/user/edit/{id}', [AdminController::class, 'manageUser'])->name('admin.user');
    Route::get('/user/ban/{id}', [AdminController::class, 'banUser']);
    Route::get('/user/unban/{id}', [AdminController::class, 'unbanUser']);
    Route::post('/user/{id}/multiaccounts', [AdminController::class, 'userMultiaccounts']);
    Route::post('/user/save', [AdminController::class, 'userSave']);

    Route::get('/withdraws', [AdminController::class,'withdraws'])->name('admin.withdraws');
    Route::get('/balance/rp', [AdminController::class,'rp_balance']);
    Route::get('/cancel/withdraw/{id}', [AdminController::class,'cancel_withdraw']);
    Route::get('/withdraw/fk/{id}', [AdminController::class, 'fk_withdraw']);
    Route::get('/withdraw/rp/{id}', [AdminController::class, 'rp_withdraw']);

    Route::get('/promocodes', [AdminController::class,'promocodes'])->name('admin.promocodes');
    Route::post('promocodes/new', [AdminController::class, 'promoNew']);
    Route::get('/promocodes/delete/{id}', [AdminController::class, 'promoDelete']);

    Route::get('/settings', [AdminController::class,'settings'])->name('admin.settings');
    Route::post('/setting/save', [AdminController::class, 'settingsSave']);
    Route::post('/enabled/{game}', [AdminController::class,'gameEnabled']);

    Route::post('/restartWheel', [AdminController::class, 'restartWheel']);
    Route::post('/stopWheel', [AdminController::class, 'stopWheel']);
});
