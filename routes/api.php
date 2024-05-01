<?php

use App\Http\Controllers\UserList;
use App\Http\Controllers\UserRegistration;
use App\Http\Controllers\UserUpdate;
use Illuminate\Support\Facades\Route;

Route::post('/user-registration', [UserRegistration::class, 'register']);
Route::get('/user-list', [UserList::class, 'getList']);
Route::put('/user-update/{id}', [UserUpdate::class, 'update']);
