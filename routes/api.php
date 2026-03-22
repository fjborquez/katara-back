<?php

use App\Http\Controllers\CityController;
use App\Http\Controllers\ConsumptionLevelController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryHouseController;
use App\Http\Controllers\LogWriterController;
use App\Http\Controllers\NutritionalProfileController;
use App\Http\Controllers\NutritionalRestrictionController;
use App\Http\Controllers\AuthTokenController;
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

Route::get('/', function () {
    return 'Hello World';
});

Route::middleware(['isValid', 'isAdmin'])->post('/user', [UserController::class, 'create']);
Route::middleware(['isValid', 'isAdmin'])->get('/user', [UserController::class, 'list']);
Route::middleware(['isValid', 'isAdmin'])->put('/user/{id}', [UserController::class, 'update']);
Route::middleware(['isValid', 'isAdmin'])->get('/user/{id}', [UserController::class, 'get']);
Route::middleware(['isValid', 'isAdmin'])->put('/user/{id}/enable', [UserController::class, 'enable']);
Route::middleware(['isValid', 'isAdmin'])->put('/user/{id}/disable', [UserController::class, 'disable']);
Route::middleware(['isValid', 'isAdmin'])->get('/user/{id}/nutritional-profile', [NutritionalProfileController::class, 'get']);
Route::middleware(['isValid', 'isAdmin'])->delete('/user/{id}/nutritional-profile/{productCategoryId}', [NutritionalProfileController::class, 'delete']);
Route::middleware(['isValid', 'isAdmin'])->get('/user/{id}/houses', [UserHouseController::class, 'list']);
Route::middleware(['isValid', 'isAdmin'])->post('/user/{id}/houses', [UserHouseController::class, 'create']);
Route::middleware(['isValid', 'isAdmin'])->put('/user/{id}/houses', [UserHouseController::class, 'update']);
Route::middleware(['isValid', 'isAdmin'])->put('/user/{id}/houses/{houseId}/enable', [UserHouseController::class, 'enable']);
Route::middleware(['isValid', 'isAdmin'])->put('/user/{id}/houses/{houseId}/disable', [UserHouseController::class, 'disable']);
Route::middleware(['isValid', 'isAdmin'])->post('/user/{id}/houses/{houseId}/residents', [ResidentController::class, 'create']);
Route::middleware(['isValid', 'isAdmin'])->put('/user/{id}/houses/{houseId}/residents/{personId}', [ResidentController::class, 'update']);
Route::middleware(['isValid', 'isAdmin'])->get('/user/{id}/houses/{houseId}/residents', [ResidentController::class, 'list']);
Route::middleware(['isValid', 'isAdmin'])->get('/user/{id}/houses/{houseId}/residents/{personId}', [ResidentController::class, 'get']);
Route::middleware(['isValid', 'isAdmin'])->get('/user/{id}/houses/{houseId}/inventory', [InventoryHouseController::class, 'list']);
Route::middleware(['isValid', 'isAdmin'])->post('/user/{id}/houses/{houseId}/inventory', [InventoryHouseController::class, 'store']);
Route::middleware(['isValid', 'isAdmin'])->put('/user/{id}/houses/{houseId}/inventory/{inventoryId}', [InventoryHouseController::class, 'update']);
Route::middleware(['isValid', 'isAdmin'])->get('/user/{id}/houses/{houseId}/inventory/{inventoryId}', [InventoryHouseController::class, 'get']);
Route::middleware(['isValid', 'isAdmin'])->put('/user/{id}/houses/{houseId}/inventory/{inventoryId}/discard', [InventoryHouseController::class, 'discard']);
Route::middleware(['isValid', 'isAdmin'])->put('/user/{id}/houses/{houseId}/inventory/{inventoryId}/consume', [InventoryHouseController::class, 'consume']);
Route::middleware(['isValid', 'isAdmin'])->delete('/user/{id}/houses/{houseId}/residents/{personId}', [ResidentController::class, 'delete']);

Route::middleware(['isValid', 'isAdmin'])->get('/nutritional-restriction', [NutritionalRestrictionController::class, 'list']);

Route::middleware(['isValid', 'isAdmin'])->get('/consumption-level', [ConsumptionLevelController::class, 'list']);

Route::middleware(['isValid', 'isAdmin'])->get('/product-catalog', [ProductCatalogController::class, 'list']);
Route::middleware(['isValid', 'isAdmin'])->post('/product-catalog', [ProductCatalogController::class, 'store']);
Route::middleware(['isValid', 'isAdmin'])->get('/product-category', [ProductCategoryController::class, 'list']);
Route::middleware(['isValid', 'isAdmin'])->post('/product-category', [ProductCategoryController::class, 'store']);
Route::middleware(['isValid', 'isAdmin'])->get('/product-brand', [ProductBrandController::class, 'list']);
Route::middleware(['isValid', 'isAdmin'])->post('/product-brand', [ProductBrandController::class, 'store']);
Route::middleware(['isValid', 'isAdmin'])->get('/product-type', [ProductTypeController::class, 'list']);
Route::middleware(['isValid', 'isAdmin'])->post('/product-type', [ProductTypeController::class, 'store']);
Route::middleware(['isValid', 'isAdmin'])->get('/product-presentation', [ProductPresentationController::class, 'list']);
Route::middleware(['isValid', 'isAdmin'])->post('/product-presentation', [ProductPresentationController::class, 'store']);

Route::middleware(['isValid', 'isAdmin'])->post('/inventory', [InventoryController::class, 'store']);
Route::middleware(['isValid', 'isAdmin'])->get('/inventory', [InventoryController::class, 'list']);

Route::middleware(['isValid', 'isAdmin'])->get('/city', [CityController::class, 'list']);

Route::middleware(['isValid', 'isAdmin'])->get('/unit-of-measurement', [UnitOfMeasurementController::class, 'list']);

Route::post('/log-writer', [LogWriterController::class, 'create']);
Route::post('/auth/token', [AuthTokenController::class, 'create']);
