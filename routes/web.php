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
use App\Jobs\InfluxQueue;
Route::get('/ly/audio/{year}/{code}/{day}.mp3', function (Request $request, $year, $code, $day) {
    $ip = $request->header('x-forwarded-for')??$request->ip();
    $domain =  'https://d3ml8yyp1h3hy5.cloudfront.net';
    $domain =  'https://729lyprog.net';
    $domain =  'https://txly2.net';
    // GampQueue::dispatchAfterResponse($ip, $code, $day, 'audio');

    $url = $request->url();
    $target = basename($url); //cc201221.mp3
    
    $tags = [];
    // 一些直播的節目，直接使用官網的連結
    if(in_array($code, ['cc','dy','gf'])){
        $domain =  'https://lpyy729.net';
    }
    $tags['metric'] = 'lyOpen';
    $tags['host'] = $domain;
    $tags['keyword'] = $code;

    $fields = [];
    $fields['count'] = 1;
    $fields['target'] = $target;
    $fields['ip'] = $ip;

    $protocolLine = [
        'name' => 'click',
        'tags' => $tags,
        'fields' => $fields
    ];
    // InfluxQueue::dispatchAfterResponse($protocolLine);
    return redirect()->away("{$domain}/ly/audio/${year}/${code}/${day}.mp3");
});

// LTS audio
Route::get('/ly/audio/{code}/{day}.mp3', function (Request $request, $code, $day) {
    $ip = $request->header('x-forwarded-for')??$request->ip();
    $domain =  'https://d3ml8yyp1h3hy5.cloudfront.net';
    $domain =  'https://729lyprog.net';
    $domain =  'https://txly2.net';
    $url = $request->url();
    $target = basename($url); //cc201221.mp3
    
    $tags = [];
    $tags['metric'] = 'lyOpen';
    $tags['type'] = 'lts';
    $tags['host'] = $domain;
    $tags['keyword'] = $code;

    $fields = [];
    $fields['count'] = 1;
    $fields['target'] = $target;
    $fields['ip'] = $ip;

        $protocolLine = [
        'name' => 'click',
        'tags' => $tags,
        'fields' => $fields
    ];
    // InfluxQueue::dispatchAfterResponse($protocolLine);
    return redirect()->away("{$domain}/lts/${code}/${day}.mp3");
});