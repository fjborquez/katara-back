<?php

use App\Http\Controllers\UserRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/user-registration', [UserRegistration::class, 'register']);
