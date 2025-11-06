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
use App\Services\GraphQLClient;

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
// Route::get('/update', function () {
//     return Artisan::call('ly:update');
// });
// // /sync/2022-12-31
// Route::get('/sync/{date}', function ($date) {
//     return Artisan::call('ly:sync '. $date);
// });

Route::group(['middleware' => ['track.api']], function () {
    
    /**
     * 获取分类列表
     */
    Route::get('/categories', function (GraphQLClient $client) {
        $data = $client->getCategories();

        if (isset($data['error'])) {
            return response()->json($data, 500);
        }

        return response()->json(['data' => $data]);
    });

    /**
     * 获取所有节目
     */
    Route::get('/programs', function (GraphQLClient $client) {
        $data = $client->getPrograms();

        if (isset($data['error'])) {
            return response()->json($data, 500);
        }

        return response()->json(['data' => $data]);
    });

    /**
     * 获取今天的项目
     */
    Route::get('/today', function (GraphQLClient $client) {
        $data = $client->getTodayItems();

        if (is_array($data) && isset($data['error'])) {
            return response()->json($data, 500);
        }

        return response()->json(['data' => $data]);
    });

    /**
     * 获取单个节目详情
     */
    Route::get('/program/{code}', function (Request $request, GraphQLClient $client, $code) {
        $data = $client->getProgramByCode($code);

        if (is_array($data) && isset($data['error'])) {
            return response()->json($data, 500);
        }

        if (!$data) {
            return response()->json(['error' => 'Program not found'], 404);
        }

        return response()->json($data['ly_items'] ?? []);
    });

});


    // Route::get('/program/{code}/{date}', function (Request $request, $code, $date) {
        // $program = Program::whereAlias($code)->firstOrFail();
        // return ItemResource::collection(Item::where('program_id', $program->id)->where('play_at', $date)->get());
    // });