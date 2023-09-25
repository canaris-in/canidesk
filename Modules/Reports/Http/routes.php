<?php

Route::group(['middleware' => 'web', 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\Reports\Http\Controllers'], function()
{
    Route::get('/reports/tickets', ['uses' => 'ReportsController@conversationsReport', 'middleware' => ['auth', 'roles'], 'roles' => ['user', 'admin','ithead','ticketEngineer','ticketCoordinator']])->name('reports.conversations');
    Route::get('/reports/productivity', ['uses' => 'ReportsController@productivityReport', 'middleware' => ['auth', 'roles'], 'roles' => ['user', 'admin','ithead','ticketEngineer','ticketCoordinator']])->name('reports.productivity');
    Route::get('/reports/satisfaction', ['uses' => 'ReportsController@satisfactionReport', 'middleware' => ['auth', 'roles'], 'roles' => ['user', 'admin','ithead','ticketEngineer','ticketCoordinator']])->name('reports.satisfaction');
    Route::get('/reports/time-tracking', ['uses' => 'ReportsController@timeReport', 'middleware' => ['auth', 'roles'], 'roles' => ['user', 'admin','ithead','ticketEngineer','ticketCoordinator']])->name('reports.time');
    Route::post('/reports/ajax', ['uses' => 'ReportsController@ajax', 'middleware' => ['auth', 'roles'], 'roles' => ['user', 'admin','ithead','ticketEngineer','ticketCoordinator'], 'laroute' => true])->name('reports.ajax');
    Route::get('/reports/export/', 'ReportsController@exportActiveAndClosedTickets')->name('reports.export');
});
