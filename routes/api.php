<?php

use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Models\Program;
use App\Http\Resources\ProgramResource;
use App\Models\Item;
use App\Http\Resources\ItemResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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



Route::get('/programs', function (Request $request) {
    return ProgramResource::collection(Program::active()->get());
});

Route::get('/programs/{program}', function (Request $request, Program $program) {
    return ItemResource::collection(Item::where('program_id', $program->id)->orderBy('play_at','desc')->simplePaginate());
});

Route::get('/categories', function (Request $request) {
    return CategoryResource::collection(Category::with('programs')->get());
});