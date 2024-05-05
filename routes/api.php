<?php

use App\Http\Controllers\NutritionalRestriction;
use App\Http\Controllers\UserActivation;
use App\Http\Controllers\UserList;
use App\Http\Controllers\UserRegistration;
use App\Http\Controllers\UserUpdate;
use Illuminate\Support\Facades\Route;

Route::post('/user', [UserRegistration::class, 'register']);
Route::get('/user', [UserList::class, 'getList']);
Route::put('/user/{id}', [UserUpdate::class, 'update']);
Route::put('/user/{id}/enable', [UserActivation::class, 'enable']);
Route::put('/user/{id}/disable', [UserActivation::class, 'disable']);

Route::get('/nutritional-restriction', [NutritionalRestriction::class, 'getList']);
