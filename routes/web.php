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

Route::get('/', function () {
    return view('home');
});


Route::get('/home', function () {
    return view('welcome');
});


Route::get('/I1q6rsnogK.txt', function () {
    return 'a6e840c8853a3ff9b5b58fe816dbaac0';
});


Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');


use App\Http\Livewire\Users;
use App\Http\Livewire\UsersByRoles;
use App\Http\Livewire\Roles;
use App\Http\Livewire\Permissions;
use App\Http\Livewire\Categories;


use App\Http\Livewire\Programs;
use App\Models\Program;
use App\Http\Livewire\Items;
use App\Jobs\GampQueue;

// 后台配置 'namespace'=>'App\Http\Controllers',  'middleware' =>['auth:sanctum', 'verified', 
// ->group(['prefix'=>'admin', 'as'=>'admin.','middleware' =>['role:super-admin']], function () { 
Route::middleware(['auth:sanctum', 'verified',  'role:super-admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('/users', Users::class)->name('users');
        // list users by role
        // Route::get('/users/{role}', UsersByRoles::class);
        Route::get('/roles', Roles::class)->name('roles');
        Route::get('/permissions', Permissions::class)->name('permissions');
        Route::get('/categories', Categories::class)->name('categories');
        Route::get('/programs', Programs::class)->name('programs');
        Route::get('/items', Items::class)->name('items');
    });

Route::get('search', function() {
    $query = '旷野'; // <-- Change the query for testing.

    $articles = App\Models\Item::search($query)->get();

    return $articles;
});

Route::get('/play', function () {
    return view('play');
});

Route::get('/programs/{program}', function (Program $program) {
    return view('program', ['program'=>$program]);
});

Route::get('/today', function () {
    return view('today');
});

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
Route::get('/ly/audio/{year}/{code}/{day}.mp3', function (Request $request, $year, $code, $day) {
    $ip = $request->header('x-forwarded-for')??$request->ip();
    
    $domain =  'https://txly2.net';
    $domain =  'https://d3ml8yyp1h3hy5.cloudfront.net';
    GampQueue::dispatchAfterResponse($ip, $code, $day, 'audio');
    return redirect()->away("{$domain}/ly/audio/${year}/${code}/${day}.mp3");
    
    $domains = [
        'https://txly2.net', // 0
        'https://lystore.yongbuzhixi.com', // 1
    ];
    $isCnIp = Cache::get('isCnIp.'.$ip, null);
    if(is_null($isCnIp)){
        $response = Http::get("https://ipapi.co/{$ip}/json/");
        if($response->ok()){
            $isCnIp = ($response['country'] == "CN")?1:0;
            Cache::set('isCnIp.'.$ip, $isCnIp);
            Log::debug('Cache', [$response['country'], $ip]);
        }else{
            $isCnIp = 1;
        }
    }
    $domain =  $domains[$isCnIp];
    GampQueue::dispatchAfterResponse($ip, $code, $day, 'audio');
    return redirect()->away("{$domain}/ly/audio/${year}/${code}/${day}.mp3");
});
// LTS audio
Route::get('/ly/audio/{code}/{day}.mp3', function (Request $request, $code, $day) {
    $ip = $request->header('x-forwarded-for')??$request->ip();
    GampQueue::dispatchAfterResponse($ip, $code, $day, 'audio');
    return redirect()->away("https://txly2.net/ly/audio/${code}/${day}.mp3");
});