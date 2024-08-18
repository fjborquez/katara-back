<?php

return [
    App\Providers\AppServiceProvider::class,

    App\Providers\AangServices\HouseServiceProvider::class,
    App\Providers\AangServices\UserServiceProvider::class,
    App\Providers\AangServices\PersonHouseServiceProvider::class,
    App\Providers\AangServices\NutritionalRestrictionServiceProvider::class,
    App\Providers\AangServices\PersonServiceProvider::class,
    App\Providers\AangServices\ResidentServiceProvider::class,
    App\Providers\AangServices\NutritionalProfileServiceProvider::class,
    App\Providers\AangServices\ConsumptionLevelServiceProvider::class,

    App\Providers\KataraServices\UserHouseServiceProvider::class,
    App\Providers\KataraServices\NutritionalProfileServiceProvider::class,
    App\Providers\KataraServices\NutritionalRestrictionServiceProvider::class,
    App\Providers\KataraServices\UserServiceProvider::class,
    App\Providers\KataraServices\ResidentServiceProvider::class,
    App\Providers\KataraServices\ConsumptionLevelServiceProvider::class,
    App\Providers\KataraServices\ProductCategoryServiceProvider::class,

    App\Providers\ZukoServices\ProductCategoryServiceProvider::class,

];
