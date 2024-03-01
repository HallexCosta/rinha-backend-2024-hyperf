<?php

declare(strict_types=1);


use App\Controller\TransactionController;
use Hyperf\HttpServer\Router\Router;

//Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index');
//Router::addGroup('/clientes/{id:\d+}', function (){
//    Router::post('/transacoes', 'App\Controller\Customer\CustomerController@createTransaction');
//    Router::get('/extrato', 'App\Controller\Customer\CustomerController@listStatementByCustomerId');
//});

//Router::post('/clientes/{id}/transacoes', [\App\Controller\Customer\CustomerController::class, 'index']);
//Router::get('/clientes/{id}/extrato', [\App\Controller\Customer\CustomerController::class, 'show']);


Router::post('/clientes/{id}/transacoes', [TransactionController::class, 'createTransaction']);
Router::get('/clientes/{id}/extrato', [TransactionController::class, 'listStatement']);




//
//Router::get('/favicon.ico', function () {
//    return '';
//});
