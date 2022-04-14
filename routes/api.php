<?php

use App\Http\Controllers\TarfinCardController;
use App\Http\Controllers\TarfinCardTransactionController;
use Illuminate\Support\Facades\Route;

Route::apiResource(
    name: 'tarfin-cards',
    controller: TarfinCardController::class
);

Route::apiResource(
    name: 'tarfin-cards.tarfin-card-transactions',
    controller: TarfinCardTransactionController::class
)->only([
    'index',
    'show',
    'store',
])->shallow();
