<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\RoleController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\CompanyController;
use App\Http\Controllers\Web\BranchController;
use App\Http\Controllers\Web\EmployeeController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\BrandController;
use App\Http\Controllers\Web\WarehouseController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\InventoryController;
use App\Http\Controllers\Web\ComboController;
use App\Http\Controllers\Web\DefectiveProductController;
use App\Http\Controllers\Web\ClientController;
use App\Http\Controllers\Web\SupplierController;
use App\Http\Controllers\Web\CashBoxController;
use App\Http\Controllers\Web\SaleController;
use App\Http\Controllers\Web\Sale2Controller;
use App\Http\Controllers\Web\PurchaseController;

use Illuminate\Support\Facades\Artisan;


Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return 'Storage link has been created successfully!';
});

Route::get('/config-cache', function () {
    Artisan::call('config:cache');
    return 'Configuration cache has been created successfully!';
});

Route::get('/config-clear', function () {
    Artisan::call('config:clear');
    return 'Configuration cache has been cleared successfully!';
});

Route::get('/route-clear', function () {
    Artisan::call('route:clear');
    return 'Route cache has been cleared successfully!';
});

Route::get('/view-clear', function () {
    Artisan::call('view:clear');
    return 'View cache has been cleared successfully!';
});

Route::get('/cache-clear', function () {
    Artisan::call('cache:clear');
    return 'Application cache has been cleared successfully!';
});

Route::get('/route-cache', function () {
    Artisan::call('route:cache');
    return 'Route cache has been created successfully!';
});

Route::get('/view-cache', function () {
    Artisan::call('view:cache');
    return 'View cache has been created successfully!';
});
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('companies', CompanyController::class)->names('companies');
    Route::resource('users', UserController::class)->names('users');
    Route::resource('branches', BranchController::class)->names('branches');
    Route::resource('employees', EmployeeController::class)->names('employees');
    Route::resource('categories', CategoryController::class)->names('categories');
    Route::resource('brands', BrandController::class)->names('brands');
    Route::resource('warehouses', WarehouseController::class)->names('warehouses');
    Route::resource('products', ProductController::class)->names('products');
    Route::post('products/search-barcode', [ProductController::class, 'searchByBarcode'])->name('products.search-barcode');
    
    // Rutas específicas de inventory ANTES del resource
    Route::get('inventory/import/form', [InventoryController::class, 'importForm'])->name('inventory.import.form');
    Route::post('inventory/import', [InventoryController::class, 'import'])->name('inventory.import');
    Route::get('inventory/import/template', [InventoryController::class, 'downloadTemplate'])->name('inventory.import.template');
    Route::get('inventory/pdf', [InventoryController::class, 'pdf'])->name('inventory.pdf');
    Route::get('inventory/stock-report', [InventoryController::class, 'stockReport'])->name('inventory.stock-report');
    
    // Resource de inventory DESPUÉS de las rutas específicas
    Route::resource('inventory', InventoryController::class)->names('inventory');
    Route::resource('combos', ComboController::class)->names('combos');
    Route::resource('defective-products', DefectiveProductController::class)->names('defective-products');
    Route::resource('clients', ClientController::class)->names('clients');
    Route::resource('suppliers', SupplierController::class)->names('suppliers');
    

    
    // Rutas para el sistema de caja
    Route::resource('cash-boxes', CashBoxController::class)->names('cash-boxes');
    Route::get('cash-boxes/{cashBox}/sessions', [CashBoxController::class, 'sessions'])->name('cash-boxes.sessions');
    Route::get('cash-boxes/{cashBox}/open-session', [CashBoxController::class, 'openSession'])->name('cash-boxes.open-session');
    Route::post('cash-boxes/{cashBox}/sessions', [CashBoxController::class, 'storeSession'])->name('cash-boxes.store-session');
    Route::get('sessions/{session}/details', [CashBoxController::class, 'sessionDetails'])->name('cash-boxes.session-details');
    Route::get('sessions/{session}/pdf', [CashBoxController::class, 'sessionPdf'])->name('cash-boxes.session-pdf');
    Route::get('sessions/{session}/close', [CashBoxController::class, 'closeSession'])->name('cash-boxes.close-session');
    Route::put('sessions/{session}', [CashBoxController::class, 'updateSession'])->name('cash-boxes.update-session');
    Route::get('sessions/{session}/movements', [CashBoxController::class, 'movements'])->name('cash-boxes.movements');
    Route::get('sessions/{session}/movements/create', [CashBoxController::class, 'createMovement'])->name('cash-boxes.create-movement');
    Route::post('sessions/{session}/movements', [CashBoxController::class, 'storeMovement'])->name('cash-boxes.store-movement');
    Route::get('sessions/{session}/balance', [CashBoxController::class, 'balance'])->name('cash-boxes.balance');

    // Rutas para ventas
    Route::resource('sales', SaleController::class)->names('sales');
    Route::patch('sales/{sale}/anular', [SaleController::class, 'anular'])->name('sales.anular');
    Route::get('sales/{sale}/ticket', [SaleController::class, 'ticket'])->name('sales.ticket');
    Route::post('sales/{sale}/enviar-nota-sunat', [SaleController::class, 'enviarNotaASunat'])->name('sales.enviar-nota-sunat');

    // Rutas para venta 2
    Route::get('sales2', [Sale2Controller::class, 'index'])->name('sales2.index');
    Route::get('sales2/create', [Sale2Controller::class, 'create'])->name('sales2.create');
    Route::get('sales2/{sale}', [Sale2Controller::class, 'show'])->name('sales2.show');

    // Rutas para compras
    Route::resource('purchases', PurchaseController::class)->names('purchases');
    Route::patch('purchases/{purchase}/anular', [PurchaseController::class, 'anular'])->name('purchases.anular');
    Route::get('purchases/{purchase}/descargar-comprobante', [PurchaseController::class, 'descargarComprobante'])->name('purchases.descargar-comprobante');

    // Rutas para series de comprobantes dentro de sucursales
    Route::post('branches/{branch}/series', [BranchController::class, 'storeSeries'])->name('branches.series.store');
    Route::put('branches/{branch}/series/{series}', [BranchController::class, 'updateSeries'])->name('branches.series.update');
    Route::delete('branches/{branch}/series/{series}', [BranchController::class, 'destroySeries'])->name('branches.series.destroy');

    // Rutas para roles y permisos
    Route::resource('roles', RoleController::class)->names('roles');
    Route::post('roles/assign', [RoleController::class, 'assignRole'])->name('roles.assign');
    Route::post('roles/remove', [RoleController::class, 'removeRole'])->name('roles.remove');
});

require __DIR__ . '/auth.php';
