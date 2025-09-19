<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\sellsController;
use App\Http\Controllers\TicketController;
use App\Models\Catalogo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Models\Inventory;

Route::get('/', function () {
    return view('welcome');
})->name('index')->middleware('auth');

// Log-in routes
Route::get('/login', [LoginController::class, 'index'])->name(name: 'login');

Route::post('/inicia-sesion', [LoginController::class, 'login'])->name(name: 'inicia-sesion');
Route::get('/logout', [LoginController::class, 'logout'])->name(name: 'logout');


// Routes for inventory
Route::get('/inventory', [InventoryController::class, 'stock'])->name('inventory')->middleware('auth');

Route::post('/addProduct', [InventoryController::class, 'store'])->name('addProduct')->middleware('auth');

Route::post('/reduceProduct', [InventoryController::class, 'reduce'])->name('reduceProduct')->middleware('auth');

Route::get('/inventario/{producto}', action: [InventoryController::class, 'getInventario'])->name('inventario.get');

// Routes for catalog
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog')->middleware('auth');

Route::post('/addCatalog', [CatalogController::class, 'store'])->name('addCatalog')->middleware('auth');

Route::delete('/deleteCatalog/{id}',[CatalogController::class, 'destroy'])->name('deleteCatalog')->middleware('auth');

Route::put('updateCatalog/{id}',[CatalogController::class, 'update'])->name('editCatalog')->middleware('auth');

// Routes for plan
Route::get('/plan',[PlanController::class,'index'])->name('plan')->middleware('auth');

// Route for sells (this codes are not more used in this version, because sells is another system)
// Route::get('/sellsIndex',[sellsController::class,'index'])->name('sells')->middleware('auth');

// Route::get('/newsell',[sellsController::class,'newsell'])->name('newsell')->middleware('auth');

// Route::get('/showSell/{sellId}',[sellsController::class,'show'])->name('showSell')->middleware('auth');

// Route::post('/storeSell',[sellsController::class,'store'])->name('storeSell')->middleware('auth');

// Route::get('/deleteSell/{sellId}',[sellsController::class,'destroy'])->name('deleteSell')->middleware('auth');

// // Ticket
// Route::get('/ticket/{sellId}', action: [TicketController::class, 'generateTicket'])->name('ticket')->middleware('auth');

// Route::get('/corte', action: [TicketController::class, 'generateCorte'])->name('corte')->middleware('auth');
