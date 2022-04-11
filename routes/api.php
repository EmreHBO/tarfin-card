<?php

use App\Facades\LoanFacade;
use App\Http\Controllers\TarfinCardController;
use App\Http\Controllers\TarfinCardTransactionController;
use Illuminate\Support\Facades\Route;

Route::get('createLoan', function (){
    dd(LoanFacade::createLoan());
});

Route::get('deneme', function (){
    return 'deneme';
});


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
