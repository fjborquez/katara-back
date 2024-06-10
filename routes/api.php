<?php

use App\Http\Controllers\HouseActivation;
use App\Http\Controllers\NutritionalProfile;
use App\Http\Controllers\NutritionalRestriction;
use App\Http\Controllers\PersonHouseCreate;
use App\Http\Controllers\PersonHouseUpdate;
use App\Http\Controllers\ResidentList;
use App\Http\Controllers\UserActivation;
use App\Http\Controllers\UserGet;
use App\Http\Controllers\UserHousesCreate;
use App\Http\Controllers\UserHousesGet;
use App\Http\Controllers\UserHouseUpdate;
use App\Http\Controllers\UserList;
use App\Http\Controllers\UserRegistration;
use App\Http\Controllers\UserUpdate;
use Illuminate\Support\Facades\Route;

Route::post('/user', [UserRegistration::class, 'register']);
Route::get('/user', [UserList::class, 'getList']);
Route::put('/user/{id}', [UserUpdate::class, 'update']);
Route::get('/user/{id}', [UserGet::class, 'getUser']);
Route::put('/user/{id}/enable', [UserActivation::class, 'enable']);
Route::put('/user/{id}/disable', [UserActivation::class, 'disable']);
Route::get('/user/{id}/nutritional-profile', [NutritionalProfile::class, 'get']);
Route::get('/user/{id}/houses', [UserHousesGet::class, 'getAll']);
Route::post('/user/{id}/houses', [UserHousesCreate::class, 'create']);
Route::put('/user/{id}/houses', [UserHouseUpdate::class, 'update']);
Route::put('/user/{id}/houses/{houseId}/enable', [HouseActivation::class, 'enable']);
Route::put('/user/{id}/houses/{houseId}/disable', [HouseActivation::class, 'disable']);
Route::post('/user/{id}/houses/{houseId}/residents', [PersonHouseCreate::class, 'create']);
Route::put('/user/{id}/houses/{houseId}/residents/{personId}', [PersonHouseUpdate::class, 'update']);
Route::get('/user/{id}/houses/{houseId}/residents', [ResidentList::class, 'getList']);

Route::get('/nutritional-restriction', [NutritionalRestriction::class, 'getList']);
