<?php

use App\Http\Controllers\CityController;
use App\Http\Controllers\ConsumptionLevelController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryHouseController;
use App\Http\Controllers\LogWriterController;
use App\Http\Controllers\NutritionalProfileController;
use App\Http\Controllers\NutritionalRestrictionController;
use App\Http\Controllers\ProductBrandController;
use App\Http\Controllers\ProductCatalogController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductPresentationController;
use App\Http\Controllers\ProductTypeController;
use App\Http\Controllers\ResidentController;
use App\Http\Controllers\UnitOfMeasurementController;
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
Route::delete('/user/{id}/nutritional-profile/{productCategoryId}', [NutritionalProfileController::class, 'delete']);
Route::get('/user/{id}/houses', [UserHouseController::class, 'list']);
Route::post('/user/{id}/houses', [UserHouseController::class, 'create']);
Route::put('/user/{id}/houses', [UserHouseController::class, 'update']);
Route::put('/user/{id}/houses/{houseId}/enable', [UserHouseController::class, 'enable']);
Route::put('/user/{id}/houses/{houseId}/disable', [UserHouseController::class, 'disable']);
Route::post('/user/{id}/houses/{houseId}/residents', [ResidentController::class, 'create']);
Route::put('/user/{id}/houses/{houseId}/residents/{personId}', [ResidentController::class, 'update']);
Route::get('/user/{id}/houses/{houseId}/residents', [ResidentController::class, 'list']);
Route::get('/user/{id}/houses/{houseId}/residents/{personId}', [ResidentController::class, 'get']);
Route::get('/user/{id}/houses/{houseId}/inventory', [InventoryHouseController::class, 'list']);
Route::post('/user/{id}/houses/{houseId}/inventory', [InventoryHouseController::class, 'store']);
Route::put('/user/{id}/houses/{houseId}/inventory/{inventoryId}', [InventoryHouseController::class, 'update']);
Route::get('/user/{id}/houses/{houseId}/inventory/{inventoryId}', [InventoryHouseController::class, 'get']);
Route::put('/user/{id}/houses/{houseId}/inventory/{inventoryId}/discard', [InventoryHouseController::class, 'discard']);
Route::put('/user/{id}/houses/{houseId}/inventory/{inventoryId}/consume', [InventoryHouseController::class, 'consume']);
Route::delete('/user/{id}/houses/{houseId}/residents/{personId}', [ResidentController::class, 'delete']);

Route::get('/nutritional-restriction', [NutritionalRestrictionController::class, 'list']);

Route::get('/consumption-level', [ConsumptionLevelController::class, 'list']);

Route::get('/product-catalog', [ProductCatalogController::class, 'list']);
Route::post('/product-catalog', [ProductCatalogController::class, 'store']);
Route::get('/product-category', [ProductCategoryController::class, 'list']);
Route::post('/product-category', [ProductCategoryController::class, 'store']);
Route::get('/product-brand', [ProductBrandController::class, 'list']);
Route::post('/product-brand', [ProductBrandController::class, 'store']);
Route::get('/product-type', [ProductTypeController::class, 'list']);
Route::post('/product-type', [ProductTypeController::class, 'store']);
Route::get('/product-presentation', [ProductPresentationController::class, 'list']);
Route::post('/product-presentation', [ProductPresentationController::class, 'store']);

Route::post('/inventory', [InventoryController::class, 'store']);
Route::get('/inventory', [InventoryController::class, 'list']);

Route::get('/city', [CityController::class, 'list']);

Route::get('/unit-of-measurement', [UnitOfMeasurementController::class, 'list']);

Route::post('/log-writer', [LogWriterController::class, 'create']);
