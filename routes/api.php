<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\PokemonController;
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
//other way to make a route (PS. If api no need to name youre route)
// Route::controller(AuthController::class)->group(function(){
//     Route::post('login', 'signin');
//     Route::post('register', 'signup');
// });

Route::post('login', [AuthController::class, 'signin']);
Route::post('register', [AuthController::class, 'signup']);

Route::middleware('auth:sanctum')->group(function () {  //to check if authenticated of if account is log in
    Route::resource('pokemon', PokemonController::class);
    Route::get('pokemons/search', [PokemonController::class, 'search']);
    Route::post('logout', [AuthController::class, 'logout']);
});

