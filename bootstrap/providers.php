<?php

use App\Providers\AangServices\AuthTokenServiceProvider;
use App\Providers\AangServices\CityServiceProvider;
use App\Providers\AangServices\ConsumptionLevelServiceProvider;
use App\Providers\AangServices\HouseServiceProvider;
use App\Providers\AangServices\NutritionalProfileServiceProvider;
use App\Providers\AangServices\NutritionalRestrictionServiceProvider;
use App\Providers\AangServices\PersonHouseServiceProvider;
use App\Providers\AangServices\PersonServiceProvider;
use App\Providers\AangServices\ResidentServiceProvider;
use App\Providers\AangServices\UserServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\KataraServices\GoogleCloudLogWriterServiceProvider;
use App\Providers\KataraServices\InventoryServiceProvider;
use App\Providers\KataraServices\ProductBrandServiceProvider;
use App\Providers\KataraServices\ProductCatalogServiceProvider;
use App\Providers\KataraServices\ProductCategoryServiceProvider;
use App\Providers\KataraServices\ProductPresentationServiceProvider;
use App\Providers\KataraServices\ProductTypeServiceProvider;
use App\Providers\KataraServices\UnitOfMeasurementServiceProvider;
use App\Providers\KataraServices\UserHouseServiceProvider;

return [
    AppServiceProvider::class,

    HouseServiceProvider::class,
    UserServiceProvider::class,
    PersonHouseServiceProvider::class,
    NutritionalRestrictionServiceProvider::class,
    PersonServiceProvider::class,
    ResidentServiceProvider::class,
    NutritionalProfileServiceProvider::class,
    ConsumptionLevelServiceProvider::class,
    CityServiceProvider::class,
    AuthTokenServiceProvider::class,

    UserHouseServiceProvider::class,
    App\Providers\KataraServices\NutritionalProfileServiceProvider::class,
    App\Providers\KataraServices\NutritionalRestrictionServiceProvider::class,
    App\Providers\KataraServices\UserServiceProvider::class,
    App\Providers\KataraServices\ResidentServiceProvider::class,
    App\Providers\KataraServices\ConsumptionLevelServiceProvider::class,
    ProductCategoryServiceProvider::class,
    InventoryServiceProvider::class,
    App\Providers\KataraServices\CityServiceProvider::class,
    ProductCatalogServiceProvider::class,
    UnitOfMeasurementServiceProvider::class,
    ProductBrandServiceProvider::class,
    ProductPresentationServiceProvider::class,
    ProductTypeServiceProvider::class,
    GoogleCloudLogWriterServiceProvider::class,
    App\Providers\KataraServices\AuthTokenServiceProvider::class,

    App\Providers\ZukoServices\ProductCategoryServiceProvider::class,
    App\Providers\ZukoServices\ProductCatalogServiceProvider::class,
    App\Providers\ZukoServices\ProductBrandServiceProvider::class,
    App\Providers\ZukoServices\ProductPresentationServiceProvider::class,
    App\Providers\ZukoServices\ProductTypeServiceProvider::class,

    App\Providers\AzulaServices\InventoryServiceProvider::class,

    App\Providers\TophServices\UnitOfMeasurementServiceProvider::class,

];
