<?php

use App\Http\Controllers\ConsumptionLevelController;
use App\Http\Controllers\NutritionalProfileController;
use App\Http\Controllers\NutritionalRestrictionController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ResidentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserHouseController;
use Illuminate\Support\Facades\Route;

Route::post('/user', [UserController::class, 'create']);
Route::get('/user', [UserController::class, 'list']);
Route::put('/user/{id}', [UserController::class, 'update']);
Route::get('/user/{id}', [UserController::class, 'get']);
Route::put('/user/{id}/enable', [UserController::class, 'enable']);
Route::put('/user/{id}/disable', [UserController::class, 'disable']);
Route::get('/user/{id}/nutritional-profile', [NutritionalProfileController::class, 'get']);
Route::get('/user/{id}/houses', [UserHouseController::class, 'list']);
Route::post('/user/{id}/houses', [UserHouseController::class, 'create']);
Route::put('/user/{id}/houses', [UserHouseController::class, 'update']);
Route::put('/user/{id}/houses/{houseId}/enable', [UserHouseController::class, 'enable']);
Route::put('/user/{id}/houses/{houseId}/disable', [UserHouseController::class, 'disable']);
Route::post('/user/{id}/houses/{houseId}/residents', [ResidentController::class, 'create']);
Route::put('/user/{id}/houses/{houseId}/residents/{personId}', [ResidentController::class, 'update']);
Route::get('/user/{id}/houses/{houseId}/residents', [ResidentController::class, 'list']);
Route::get('/user/{id}/houses/{houseId}/residents/{personId}', [ResidentController::class, 'get']);
Route::delete('/user/{id}/houses/{houseId}/residents/{personId}', [ResidentController::class, 'delete']);

Route::get('/nutritional-restriction', [NutritionalRestrictionController::class, 'list']);

Route::get('/consumption-level', [ConsumptionLevelController::class, 'list']);

Route::get('/product-category', [ProductCategoryController::class, 'list']);
