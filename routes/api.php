<?php

use App\Http\Controllers\ControleurApi;

use Illuminate\Support\Facades\Route;

Route::get('/test_api', [ControleurApi::class, 'test_api']);
Route::any('', [ControleurApi::class, 'test_api']);

Route::post('/connexion', [ControleurApi::class,'connexion']);
Route::post('check_token' , [ControleurApi::class,'check_token']);
Route::post('profile_info' , [ControleurApi::class,'check_token']);
Route::post('/deconnexion' , [ControleurApi::class,'deconnexion']);

Route::post('/emprunts' , [ControleurApi::class,'get_emprunts']);
Route::post('/renouveler_emprunts' , [ControleurApi::class,'renouveler_emprunts']);

Route::post("/update_profile", [ControleurApi::class, 'update_profile']);
Route::post("/change_password", [ControleurApi::class, 'change_password']);