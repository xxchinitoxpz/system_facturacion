<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\DespatchController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\PeruApiController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [RegisterController::class, 'store']);  

Route::post('login', [AuthController::class, 'login']);

// Rutas de la API de Perú (públicas)
Route::prefix('peru')->group(function () {
    Route::post('consultar-dni', [PeruApiController::class, 'consultarDNI']);
    Route::post('consultar-ruc', [PeruApiController::class, 'consultarRUC']);
    Route::post('informacion-persona', [PeruApiController::class, 'obtenerInformacionPersona']);
    Route::post('informacion-empresa', [PeruApiController::class, 'obtenerInformacionEmpresa']);
    Route::post('validar-dni', [PeruApiController::class, 'validarDNI']);
    Route::post('validar-ruc', [PeruApiController::class, 'validarRUC']);
});

// Rutas para búsqueda de productos (públicas para el frontend)
Route::get('buscar-productos', [ProductController::class, 'buscar']);
Route::get('productos/{producto}/presentaciones', [ProductController::class, 'presentaciones']);
Route::get('productos/stock-disponible', [ProductController::class, 'stockDisponible']);
Route::get('consultar-documento/{documento}', [PeruApiController::class, 'consultarDocumento']);
Route::get('consultar-proveedor/{documento}', [PeruApiController::class, 'consultarProveedor']);

// Rutas protegidas con middleware auth:api
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
    //invoices
    Route::post('invoices/send', [InvoiceController::class, 'send']);
    Route::post('invoices/xml', [InvoiceController::class, 'xml']);
    Route::post('invoices/pdf', [InvoiceController::class, 'pdf']);
    //notes
    Route::post('notes/send', [NoteController::class, 'send']);
    Route::post('notes/xml', [NoteController::class, 'xml']);
    Route::post('notes/pdf', [NoteController::class, 'pdf']);
    //despatches
    Route::post('despatches/send', [DespatchController::class, 'send']);
    Route::post('despatches/xml', [DespatchController::class, 'xml']);
    Route::post('despatches/pdf', [DespatchController::class, 'pdf']);
});


