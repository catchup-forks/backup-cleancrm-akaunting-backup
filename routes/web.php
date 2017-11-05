<?php
/**
 * @package     Akaunting
 * @copyright   2017 Akaunting. All rights reserved.
 * @license     GNU GPL version 3; see LICENSE.txt
 * @link        https://akaunting.com
 */
Route::group(['middleware' => 'language'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::group(['prefix' => 'uploads'], function () {
            Route::get('{folder}/{file}', 'Common\Uploads@get');
            Route::get('{folder}/{file}/download', 'Common\Uploads@download');
        });
        Route::group(['middleware' => ['adminmenu', 'permission:read-admin-panel']], function () {
            Route::get('/', 'Dashboard\Dashboard@index');
            Route::group(['prefix' => 'search'], function () {
                Route::get('search/search', 'Search\Search@search');
                Route::resource('search', 'Search\Search');
            });
            Route::group(['prefix' => 'items'], function () {
                Route::get('items/autocomplete', 'Items\Items@autocomplete');
                Route::post('items/totalItem', 'Items\Items@totalItem');
                Route::resource('items', 'Items\Items');
            });
            Route::group(['prefix' => 'auth'], function () {
                Route::get('logout', 'Auth\Login@destroy')->name('logout');
                Route::get('users/{user}/read-bills', 'Auth\Users@readUpcomingBills');
                Route::get('users/{user}/read-invoices', 'Auth\Users@readOverdueInvoices');
                Route::resource('users', 'Auth\Users');
                Route::resource('roles', 'Auth\Roles');
                Route::resource('permissions', 'Auth\Permissions');
            });
            Route::group(['prefix' => 'companies'], function () {
                Route::get('companies/{company}/set', 'Companies\Companies@set');
                Route::resource('companies', 'Companies\Companies');
            });
            Route::group(['prefix' => 'incomes'], function () {
                Route::get('customers/currency', 'Incomes\Customers@currency');
                Route::resource('customers', 'Incomes\Customers');
                Route::get('invoices/{id}/print', 'Incomes\Invoices@printInvoice');
                Route::get('invoices/{id}/pdf', 'Incomes\Invoices@pdfInvoice');
                Route::post('invoices/payment', 'Incomes\Invoices@payment');
                Route::delete('invoices/payment/{payment}', 'Incomes\Invoices@paymentDestroy');
                Route::resource('invoices', 'Incomes\Invoices');
                Route::resource('revenues', 'Incomes\Revenues');
            });
            Route::group(['prefix' => 'expenses'], function () {
                Route::resource('payments', 'Expenses\Payments');
                Route::get('bills/{id}/print', 'Expenses\Bills@printBill');
                Route::get('bills/{id}/pdf', 'Expenses\Bills@pdfBill');
                Route::post('bills/payment', 'Expenses\Bills@payment');
                Route::delete('bills/payment/{payment}', 'Expenses\Bills@paymentDestroy');
                Route::resource('bills', 'Expenses\Bills');
                Route::get('vendors/currency', 'Expenses\Vendors@currency');
                Route::resource('vendors', 'Expenses\Vendors');
            });
            Route::group(['prefix' => 'banking'], function () {
                Route::resource('accounts', 'Banking\Accounts');
                Route::resource('transactions', 'Banking\Transactions');
                Route::resource('transfers', 'Banking\Transfers');
            });
            Route::group(['prefix' => 'reports'], function () {
                Route::resource('income-summary', 'Reports\IncomeSummary');
                Route::resource('expense-summary', 'Reports\ExpenseSummary');
                Route::resource('income-expense-summary', 'Reports\IncomeExpenseSummary');
            });
            Route::group(['prefix' => 'settings'], function () {
                Route::resource('categories', 'Settings\Categories');
                Route::get('currencies/currency', 'Settings\Currencies@currency');
                Route::resource('currencies', 'Settings\Currencies');
                Route::get('settings', 'Settings\Settings@edit');
                Route::patch('settings', 'Settings\Settings@update');
                Route::resource('taxes', 'Settings\Taxes');
                Route::get('apps/{alias}', 'Settings\Modules@edit');
                Route::patch('apps/{alias}', 'Settings\Modules@update');
            });
            Route::group(['prefix' => 'apps'], function () {
                Route::resource('token', 'Modules\Token');
                Route::resource('home', 'Modules\Home');
                Route::get('categories/{alias}', 'Modules\Tiles@category');
                Route::get('paid', 'Modules\Tiles@paid');
                Route::get('new', 'Modules\Tiles@new');
                Route::get('free', 'Modules\Tiles@free');
                Route::post('steps', 'Modules\Item@steps');
                Route::post('download', 'Modules\Item@download');
                Route::post('unzip', 'Modules\Item@unzip');
                Route::post('install', 'Modules\Item@install');
                Route::get('{alias}/uninstall', 'Modules\Item@uninstall');
                Route::get('{alias}/enable', 'Modules\Item@enable');
                Route::get('{alias}/disable', 'Modules\Item@disable');
                Route::get('{alias}', 'Modules\Item@show');
            });
            Route::group(['prefix' => 'install'], function () {
                Route::get('updates/changelog', 'Install\Updates@changelog');
                Route::get('updates/check', 'Install\Updates@check');
                Route::get('updates/update/{alias}/{version}', 'Install\Updates@update');
                Route::get('updates/post/{alias}/{old}/{new}', 'Install\Updates@post');
                Route::resource('updates', 'Install\Updates');
            });
        });
        Route::group(['middleware' => ['customermenu', 'permission:read-customer-panel']], function () {
            Route::group(['prefix' => 'customers'], function () {
                Route::get('/', 'Customers\Dashboard@index');
                Route::get('invoices/{id}/print', 'Customers\Invoices@printInvoice');
                Route::get('invoices/{id}/pdf', 'Customers\Invoices@pdfInvoice');
                Route::post('invoices/payment', 'Customers\Invoices@payment');
                Route::resource('invoices', 'Customers\Invoices');
                Route::resource('payments', 'Customers\Payments');
                Route::resource('transactions', 'Customers\Transactions');
                Route::get('logout', 'Auth\Login@destroy')->name('customer_logout');
            });
        });
    });
    Route::group(['middleware' => 'guest'], function () {
        Route::group(['prefix' => 'auth'], function () {
            Route::get('login', 'Auth\Login@create')->name('login');
            Route::post('login', 'Auth\Login@store');
            Route::get('forgot', 'Auth\Forgot@create')->name('forgot');
            Route::post('forgot', 'Auth\Forgot@store');
            //Route::get('reset', 'Auth\Reset@create');
            Route::get('reset/{token}', 'Auth\Reset@create')->name('reset');
            Route::post('reset', 'Auth\Reset@store');
        });
        Route::group(['middleware' => 'install'], function () {
            Route::group(['prefix' => 'install'], function () {
                Route::get('/', 'Install\Requirements@show');
                Route::get('requirements', 'Install\Requirements@show');
                Route::get('language', 'Install\Language@create');
                Route::post('language', 'Install\Language@store');
                Route::get('database', 'Install\Database@create');
                Route::post('database', 'Install\Database@store');
                Route::get('settings', 'Install\Settings@create');
                Route::post('settings', 'Install\Settings@store');
            });
        });
    });
});