<?php

use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Models\Program;
use App\Http\Resources\ProgramResource;
use App\Models\Item;
use App\Http\Resources\ItemResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// 手动调用，更新节目
// TODO（如果0点自动获取节目出现问题，每天5点检查一遍）
// TODO 使用vdata API
Route::get('/update', function () {
    return Artisan::call('ly:update');
});
// /sync/2022-12-31
Route::get('/sync/{date}', function ($date) {
    return Artisan::call('ly:sync '. $date);
});

Route::group(['middleware' => ['track.api']], function () {
    Route::get('/programs', function (Request $request) {
        return ProgramResource::collection(Program::active()->get());
    });

    Route::get('/today', function (Request $request) {
        return ItemResource::collection(Item::where('play_at', now()->format('Y-m-d 00:00:00'))->inRandomOrder()->get());
    });

    Route::get('/program/{code}', function (Request $request, $code) {
        $program = Program::whereAlias($code)->firstOrFail();
        return ItemResource::collection(Item::where('program_id', $program->id)->orderBy('play_at','desc')->simplePaginate(31));
    });

    Route::get('/program/{code}/{date}', function (Request $request, $code, $date) {
        $program = Program::whereAlias($code)->firstOrFail();
        return ItemResource::collection(Item::where('program_id', $program->id)->where('play_at', $date)->get());
    });

    Route::get('/categories', function (Request $request) {
        return CategoryResource::collection(Category::with('programs')->get());
    });

});