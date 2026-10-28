<?php

use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
        Modules\Users\Providers\RouteServiceProvider::class,
        
];
