<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/sign-in/{user}', 'Auth\LoginController@signIn')->name('sign-in');
Route::get('/', 'HomeController@index')->name('home');
Route::get('/referral/{username}', 'Auth\RegisterController@referal')->name('referal');

Auth::routes();

Route::group(['middleware' => ['auth','block-user','log-activity']], function() {
    Route::get('/home', 'HomeController@index')->name('home');
	Route::get('/count_down', 'HomeController@count_down')->name('count_down');

    // user
	Route::group(['prefix' => 'user', 'as' => 'user.'], function() {
        Route::get('/profile', ['as' => 'profile', 'uses' => 'UserController@profile']);
        Route::get('/profile/edit', ['as' => 'profile.edit', 'uses' => 'UserController@edit_profile']);
 		Route::post('/inputData', ['as' => 'inputData', 'uses' => 'UserController@inputData']);
        Route::post('/update/password', ['as' => 'updatePassword', 'uses' => 'UserController@updatePassword']);
        Route::post('/update/password/trx', ['as' => 'updatePasswordtrx', 'uses' => 'UserController@updatePasswordtrx']);
        Route::get('/create', ['as' => 'index', 'uses' => 'UserController@index'])->middleware(['permission:administrator']);
        Route::post('/create', ['as' => 'create', 'uses' => 'UserController@create'])->middleware(['permission:administrator']);
        Route::get('/list/{role}', ['as' => 'list', 'uses' => 'UserController@list'])->middleware(['permission:administrator']);
        Route::get('/edit/{id}', ['as' => 'edit', 'uses' => 'UserController@edit'])->middleware(['permission:administrator']);
        Route::post('/updateData/{id}', ['as' => 'updateData', 'uses' => 'UserController@updateData'])->middleware(['permission:administrator']);
        Route::get('/get_user', ['as' => 'get_user', 'uses' => 'UserController@getUsername']);
        Route::get('/searchUser', ['as' => 'searchUser', 'uses' => 'UserController@searchUser']);
        Route::get('/block_unclock/{id}', ['as' => 'block_unclock', 'uses' => 'UserController@block_unclock'])->middleware(['permission:administrator']);
        Route::get('/list_sponsor', ['as' => 'list_sponsor', 'uses' => 'UserController@list_sponsor'])->middleware(['permission:administrator']);
        Route::get('/list_wallet', ['as' => 'list_wallet', 'uses' => 'UserController@wallets'])->middleware(['permission:administrator']);
        Route::get('/list/donwline', ['as' => 'list_donwline', 'uses' => 'UserController@list_donwline']);
        Route::get('/donwline/{id}', ['as' => 'list_donwline_user', 'uses' => 'UserController@list_donwline_user']);
        Route::get('/resetPin', ['as' => 'resetPin', 'uses' => 'UserController@resetPin']);
        Route::get('/bank', ['as' => 'bank', 'uses' => 'UserController@viewBank']);
        Route::post('/bank/store', ['as' => 'bank.save', 'uses' => 'UserController@saveBank']);
        Route::get('/resetQuestion/{id}', ['as' => 'resetQuestion', 'uses' => 'UserController@resetQuestion'])->middleware(['permission:administrator']);
    });

    // balance
    Route::group(['prefix' => 'balance', 'as' => 'balance.'], function() {
        Route::get('/', ['as' => 'index', 'uses' => 'BalanceController@index'])->middleware(['permission:administrator']);
        Route::get('/{wallet}', ['as' => 'wallet', 'uses' => 'BalanceController@wallet']);
        Route::get('/{wallet}/{id}', ['as' => 'wallet_member', 'uses' => 'BalanceController@wallet_member'])->middleware(['permission:administrator']);
    });

    // transfer
    Route::group(['prefix' => 'transfer', 'as' => 'transfer.'], function() {
        Route::get('/{type}', ['as' => 'wallet', 'uses' => 'TransferController@index']);
        Route::post('/send/{type}', ['as' => 'send', 'uses' => 'TransferController@send']);
        Route::get('/check/user', ['as' => 'check', 'uses' => 'TransferController@check']);
    });

    // program
    Route::group(['prefix' => 'program', 'as' => 'program.'], function() {
        Route::get('/invest', ['as' => 'index', 'uses' => 'ProgramController@index'])->middleware(['count-down']);
        Route::get('/invest/admin', ['as' => 'by_admin', 'uses' => 'ProgramController@by_admin'])->middleware(['permission:administrator']);
        Route::post('/register', ['as' => 'register', 'uses' => 'ProgramController@register']);
        Route::post('/register/by_admin', ['as' => 'register_byadmin', 'uses' => 'ProgramController@register_byadmin'])->middleware(['permission:administrator']);
        Route::get('/history', ['as' => 'history', 'uses' => 'ProgramController@history'])->middleware(['count-down']);
        Route::get('/list/{regby}', ['as' => 'list', 'uses' => 'ProgramController@list_program'])->middleware(['permission:administrator']);
        Route::get('/invest/avcoin', ['as' => 'list_av', 'uses' => 'ProgramController@list_av'])->middleware(['permission:administrator']);
        Route::get('/profit_capital/{type}/{desc}/{id}', ['as' => 'profit_capital', 'uses' => 'ProgramController@profit_capital'])->middleware(['permission:administrator']);
        Route::get('/plan/{type}', ['as' => 'plan', 'uses' => 'ProgramController@getPlan']);
        Route::get('/move/plan/{id}', ['as' => 'moveProgram', 'uses' => 'ProgramController@moveProgram']);
    });

    // convert
    Route::group(['prefix' => 'convert', 'as' => 'convert.'], function() {
        Route::get('/', ['as' => 'index', 'uses' => 'ConvertController@index']);
        Route::post('/send', ['as' => 'send', 'uses' => 'ConvertController@send']);
        Route::get('/history', ['as' => 'history', 'uses' => 'ConvertController@history']);
        Route::get('/list', ['as' => 'list', 'uses' => 'ConvertController@list'])->middleware(['permission:administrator']);
        Route::get('/voucher', ['as' => 'voucher', 'uses' => 'ConvertController@voucher']);
        Route::post('/sendVoucher', ['as' => 'sendVoucher', 'uses' => 'ConvertController@sendVoucher']);
        Route::get('/history/voucher', ['as' => 'history_voucher', 'uses' => 'ConvertController@history_voucher']);
        Route::get('/list/voucher', ['as' => 'list_voucher', 'uses' => 'ConvertController@list_voucher'])->middleware(['permission:administrator']);
        Route::get('/topup', ['as' => 'topup', 'uses' => 'ConvertController@topup']);
        Route::post('/sendTopup', ['as' => 'sendTopup', 'uses' => 'ConvertController@sendTopup']);
        Route::get('/history/topup', ['as' => 'history_topup', 'uses' => 'ConvertController@history_topup']);
        Route::get('/list/topup', ['as' => 'list_topup', 'uses' => 'ConvertController@list_topup'])->middleware(['permission:administrator']);
        Route::get('/check/{username}', ['as' => 'checkAccount', 'uses' => 'ConvertController@checkAccount']);
    });

    // bonus
    Route::group(['prefix' => 'bonus', 'as' => 'bonus.'], function() {
        Route::get('/pasif', ['as' => 'pasif', 'uses' => 'BonusController@pasif']);
        Route::get('/max_profit', ['as' => 'max', 'uses' => 'BonusController@max'])->middleware(['permission:administrator']);
        Route::get('/active/{type}', ['as' => 'active', 'uses' => 'BonusController@active']);
        Route::get('/list/{type}', ['as' => 'list', 'uses' => 'BonusController@list'])->middleware(['permission:administrator']);
    });

    // withdraw
    Route::group(['prefix' => 'withdraw', 'as' => 'withdraw.'], function() {
        Route::get('/', ['as' => 'index', 'uses' => 'WithdrawController@index']);
        Route::post('/send', ['as' => 'send', 'uses' => 'WithdrawController@send']);
        Route::get('/usdt', ['as' => 'usdt', 'uses' => 'WithdrawController@usdt']);
        Route::post('/sendUsdt', ['as' => 'sendUsdt', 'uses' => 'WithdrawController@sendUsdt']);
        Route::get('/history', ['as' => 'history', 'uses' => 'WithdrawController@history']);
        Route::get('/list/{type}', ['as' => 'list', 'uses' => 'WithdrawController@list'])->middleware(['permission:administrator']);
        Route::post('/accept/{id}', ['as' => 'accept', 'uses' => 'WithdrawController@accept'])->middleware(['permission:administrator']);
        Route::get('/reject/{id}', ['as' => 'reject', 'uses' => 'WithdrawController@reject'])->middleware(['permission:administrator']);
    });

    // avcoin
    Route::group(['prefix' => 'av', 'as' => 'avcoin.'], function() {
        Route::get('/', ['as' => 'index', 'uses' => 'CoinController@index']);
        Route::post('/send', ['as' => 'send', 'uses' => 'CoinController@send']);
        Route::post('/sendAdmin', ['as' => 'sendAdmin', 'uses' => 'CoinController@sendFromAdmin'])->middleware(['permission:administrator']);
        Route::get('/list', ['as' => 'list', 'uses' => 'CoinController@list'])->middleware(['permission:administrator']);
        Route::get('/checkAddress', ['as' => 'checkAddress', 'uses' => 'CoinController@checkAddress']);
        Route::get('/explorer', ['as' => 'explorer', 'uses' => 'CoinController@explorer']);
        Route::get('/explorer/block/{block}', ['as' => 'block', 'uses' => 'CoinController@block']);
        Route::get('/explorer/hash/{hash}', ['as' => 'hash', 'uses' => 'CoinController@hash']);
        Route::get('/explorer/address/{address}', ['as' => 'address', 'uses' => 'CoinController@address']);
        Route::get('/explorer/search', ['as' => 'search', 'uses' => 'CoinController@search']);
    });

    // marketplace
	Route::group(['prefix' => 'marketplace', 'as' => 'marketplace.'], function() {
   		Route::get('/', ['as' => 'index', 'uses' => 'MarketplaceController@index']);
	});

    // metatrader
	Route::group(['prefix' => 'metatrader', 'as' => 'metatrader.'], function() {
        Route::get('/', ['as' => 'index', 'uses' => 'MetatraderController@index']);
        Route::get('/list', ['as' => 'list', 'uses' => 'MetatraderController@list'])->middleware(['permission:metatrader']);
        Route::post('/store', ['as' => 'store', 'uses' => 'MetatraderController@store'])->middleware(['permission:metatrader']);
        Route::post('/update/{id}', ['as' => 'update', 'uses' => 'MetatraderController@update'])->middleware(['permission:metatrader']);
   		Route::get('/delete/{id}', ['as' => 'delete', 'uses' => 'MetatraderController@delete'])->middleware(['permission:metatrader']);
	});

    // Tree / team
    Route::group(['prefix' => 'team', 'as' => 'team.'], function() {
        Route::get('/', ['as' => 'index', 'uses' => 'TeamController@index']);
        Route::get('/getSponsorTree/{user_id}/{page}', ['as' => 'getSponsorTree', 'uses' => 'TeamController@getSponsorTree']);
        Route::get('/network', ['as' => 'network', 'uses' => 'TreeController@index'])->middleware(['count-down']);
        Route::get('/sponsor_tree/{position}', ['as' => 'sponsor_tree', 'uses' => 'TreeController@sponsor_tree']);
        Route::get('/downline_tree/{user_id}', ['as' => 'downline_tree', 'uses' => 'TreeController@downline_tree']);
        Route::get('/getTree/{user_id}', ['as' => 'getTree', 'uses' => 'TreeController@getDataTree']);
        Route::post('/registerTree', ['as' => 'registerTree', 'uses' => 'TreeController@registerTree']);
        Route::get('/viewRegister/network', ['as' => 'viewRegister', 'uses' => 'TreeController@viewRegister']);
        Route::post('/saveUserTree/network', ['as' => 'saveUserTree', 'uses' => 'TreeController@saveUserTree']);
        Route::get('/register/tree/{upline}/{id}/{amount}', ['as' => 'insertTree', 'uses' => 'TreeController@insertTree']);
    });

    // setting
    Route::group(['prefix' => 'setting', 'as' => 'setting.'], function() {
        Route::get('/', ['as' => 'index', 'uses' => 'SettingController@index'])->middleware(['permission:administrator']);
        Route::post('/update', ['as' => 'update', 'uses' => 'SettingController@update'])->middleware(['permission:administrator']);
        Route::get('/ico', ['as' => 'ico', 'uses' => 'SettingController@ico'])->middleware(['permission:administrator']);
        Route::post('/ico/update', ['as' => 'updateIco', 'uses' => 'SettingController@updateIco'])->middleware(['permission:administrator']);
        Route::get('/package', ['as' => 'package', 'uses' => 'SettingController@package'])->middleware(['permission:administrator']);
        Route::post('/package/update', ['as' => 'updatePackage', 'uses' => 'SettingController@updatePackage'])->middleware(['permission:administrator']);
        Route::get('/bank', ['as' => 'bank', 'uses' => 'SettingController@listBank'])->middleware(['permission:administrator']);
        Route::post('/bank/update', ['as' => 'updateBank', 'uses' => 'SettingController@updateBank'])->middleware(['permission:administrator']);
    });

    // asset
	Route::group(['prefix' => 'asset', 'as' => 'asset.'], function() {
   		Route::get('/', ['as' => 'index', 'uses' => 'AssetController@index']);
   		Route::get('/getDataChart', ['as' => 'getDataChart', 'uses' => 'AssetController@getDataChart']);
   		Route::get('/chart/receive', ['as' => 'receiveAv', 'uses' => 'AssetController@getChartReceiveAv']);
   		Route::get('/chart/buy', ['as' => 'buyAv', 'uses' => 'AssetController@getChartBuyAv']);
	});

    // product
    Route::group(['prefix' => 'product', 'as' => 'product.'], function() {
        Route::get('/my', ['as' => 'myProduct', 'uses' => 'ProductController@myProduct']);
        Route::get('/list', ['as' => 'index', 'uses' => 'ProductController@index'])->middleware(['permission:administrator']);
        Route::get('/detail/{id}', ['as' => 'show', 'uses' => 'ProductController@show']);
        Route::get('/create', ['as' => 'create', 'uses' => 'ProductController@create']);
        Route::post('/store', ['as' => 'store', 'uses' => 'ProductController@store']);
        Route::get('/edit/{id}', ['as' => 'edit', 'uses' => 'ProductController@edit']);
        Route::post('/update/{id}', ['as' => 'update', 'uses' => 'ProductController@update']);
        Route::get('/enableProduct/{id}', ['as' => 'enableProduct', 'uses' => 'ProductController@enableProduct'])->middleware(['permission:administrator']);
        Route::get('/publishProduct/{id}', ['as' => 'publishProduct', 'uses' => 'ProductController@publishProduct']);
        Route::get('/action/{type}/{id}', ['as' => 'likeOrDislike', 'uses' => 'ProductController@likeOrDislike']);

        // category
        Route::group(['prefix' => 'category', 'as' => 'category.','middleware'=>'permission:administrator'], function() {
            Route::get('/', ['as' => 'index', 'uses' => 'ProductCategoryController@index']);
            Route::get('/show/{id}', ['as' => 'show', 'uses' => 'ProductCategoryController@show']);
            Route::post('/store', ['as' => 'store', 'uses' => 'ProductCategoryController@store']);
            Route::post('/update/{id}', ['as' => 'update', 'uses' => 'ProductCategoryController@update']);
            Route::get('/delete/{id}', ['as' => 'delete', 'uses' => 'ProductCategoryController@delete']);
        });
    });

    // address
	Route::group(['prefix' => 'address', 'as' => 'address.'], function() {
        Route::get('/province', ['as' => 'province', 'uses' => 'AddressController@province']);
        Route::get('/district/{id}', ['as' => 'district', 'uses' => 'AddressController@district']);
        Route::get('/subdistrict/{id}', ['as' => 'subdistrict', 'uses' => 'AddressController@subdistrict']);
    });

    // voucher
	Route::group(['prefix' => 'voucher', 'as' => 'voucher.'], function() {
        Route::get('/', ['as' => 'index', 'uses' => 'VoucherController@index'])->middleware(['role:super_admin']);
    });

    // portofolio
	Route::group(['prefix' => 'portofolio', 'as' => 'portofolio.'], function() {
        Route::get('/', ['as' => 'index', 'uses' => 'PortofolioController@index']);
    });

    // video
	Route::group(['prefix' => 'videos', 'as' => 'video.'], function() {
        Route::get('/', ['as' => 'index', 'uses' => 'VideoController@index']);
        Route::post('/store', ['as' => 'store', 'uses' => 'VideoController@store']);
        Route::post('/update/{id}', ['as' => 'update', 'uses' => 'VideoController@update']);
        Route::get('/delete/{if}', ['as' => 'delete', 'uses' => 'VideoController@delete']);
    });

    // question
	Route::group(['prefix' => 'question', 'as' => 'question.'], function() {
        Route::get('/', ['as' => 'index', 'uses' => 'QuestionController@index'])->middleware(['permission:administrator']);
        Route::post('/store', ['as' => 'store', 'uses' => 'QuestionController@store'])->middleware(['permission:administrator']);
        Route::post('/update/{id}', ['as' => 'update', 'uses' => 'QuestionController@update'])->middleware(['permission:administrator']);
        Route::get('/answer', ['as' => 'viewAnswer', 'uses' => 'QuestionController@viewAnswer']);
        Route::post('/answer', ['as' => 'answer', 'uses' => 'QuestionController@answer']);
    });
});
