<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ReorderRuleController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : view('welcome');
});

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout',   [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password',  [PasswordResetController::class, 'showForgotForm'])->name('password.forgot');
Route::post('/forgot-password', [PasswordResetController::class, 'sendOtp'])->name('password.send-otp');
Route::get('/verify-otp',  [PasswordResetController::class, 'showVerifyOtpForm'])->name('password.verify-otp');
Route::post('/verify-otp', [PasswordResetController::class, 'verifyOtp'])->name('password.verify-otp.post');
Route::get('/reset-password',  [PasswordResetController::class, 'showResetForm'])->name('password.reset-form');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.reset');

// ── Authenticated (all roles) ─────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (own account)
    Route::get('/profile',           [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile',           [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password',  [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Products — view only for staff, mutations locked to admin below
    Route::get('/products',          [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}',[ProductController::class, 'show'])->name('products.show');

    // Categories — read only for staff
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

    // Warehouses — read only for staff
    Route::get('/warehouses',             [WarehouseController::class, 'index'])->name('warehouses.index');
    Route::get('/warehouses/{warehouse}', [WarehouseController::class, 'show'])->name('warehouses.show');

    // Suppliers — read only for staff
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');

    // Inventory
    Route::get('/inventory',                        [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/summary',                [InventoryController::class, 'summary'])->name('inventory.summary');
    Route::get('/inventory/warehouse/{warehouse}',  [InventoryController::class, 'byWarehouse'])->name('inventory.by-warehouse');
    Route::get('/inventory/product/{product}',      [InventoryController::class, 'byProduct'])->name('inventory.by-product');

    // Stock Movements
    Route::get('/stock-movements',                  [StockMovementController::class, 'index'])->name('stock-movements.index');
    Route::get('/stock-movements/ledger',           [StockMovementController::class, 'ledger'])->name('stock-movements.ledger');
    Route::get('/stock-movements/product/{product}',[StockMovementController::class, 'byProduct'])->name('stock-movements.by-product');
    Route::get('/stock-movements/type/{type}',      [StockMovementController::class, 'byType'])->name('stock-movements.by-type');
    Route::get('/stock-movements/{movement}',       [StockMovementController::class, 'show'])->name('stock-movements.show');

    // Receipts — staff can create & view, admin can verify/delete
    Route::get('/receipts',           [ReceiptController::class, 'index'])->name('receipts.index');
    Route::get('/receipts/create',    [ReceiptController::class, 'create'])->name('receipts.create');
    Route::post('/receipts',          [ReceiptController::class, 'store'])->name('receipts.store');
    Route::get('/receipts/{receipt}', [ReceiptController::class, 'show'])->name('receipts.show');

    // Deliveries — staff can create & progress, admin can cancel
    Route::get('/deliveries',          [DeliveryController::class, 'index'])->name('deliveries.index');
    Route::get('/deliveries/create',   [DeliveryController::class, 'create'])->name('deliveries.create');
    Route::post('/deliveries',         [DeliveryController::class, 'store'])->name('deliveries.store');
    Route::get('/deliveries/{delivery}',[DeliveryController::class, 'show'])->name('deliveries.show');
    Route::post('/deliveries/{delivery}/start-picking',            [DeliveryController::class, 'startPicking'])->name('deliveries.start-picking');
    Route::post('/deliveries/{delivery}/items/{item}/mark-picked', [DeliveryController::class, 'markItemPicked'])->name('deliveries.mark-picked');
    Route::post('/deliveries/{delivery}/start-packing',            [DeliveryController::class, 'startPacking'])->name('deliveries.start-packing');
    Route::post('/deliveries/{delivery}/validate',                 [DeliveryController::class, 'validate'])->name('deliveries.validate');
    Route::post('/deliveries/{delivery}/ship',                     [DeliveryController::class, 'ship'])->name('deliveries.ship');

    // Transfers — staff can create & complete
    Route::get('/transfers',           [TransferController::class, 'index'])->name('transfers.index');
    Route::get('/transfers/create',    [TransferController::class, 'create'])->name('transfers.create');
    Route::post('/transfers',          [TransferController::class, 'store'])->name('transfers.store');
    Route::get('/transfers/{transfer}',[TransferController::class, 'show'])->name('transfers.show');
    Route::get('/transfers/{transfer}/edit', [TransferController::class, 'edit'])->name('transfers.edit');
    Route::put('/transfers/{transfer}',      [TransferController::class, 'update'])->name('transfers.update');
    Route::post('/transfers/{transfer}/complete', [TransferController::class, 'complete'])->name('transfers.complete');

    // Alerts — read only
    Route::get('/alerts',       [AlertController::class, 'index'])->name('alerts.index');
    Route::get('/alerts/count', [AlertController::class, 'count'])->name('alerts.count');

    // API: stock qty lookup
    Route::get('/api/stock', function (\Illuminate\Http\Request $request) {
        $stock = \App\Models\Stock::where('product_id', $request->product_id)
            ->where('warehouse_id', $request->warehouse_id)
            ->when($request->filled('rack_id'), fn($q) => $q->where('location_id', $request->rack_id))
            ->when(!$request->filled('rack_id'), fn($q) => $q->whereNull('location_id'))
            ->first();
        return response()->json(['quantity' => $stock?->quantity ?? 0]);
    })->name('api.stock');

    // ── Admin / Manager only ──────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {

        // Products — mutations
        Route::post('/products',           [ProductController::class, 'store'])->name('products.store');
        Route::put('/products/{product}',  [ProductController::class, 'update'])->name('products.update');
        Route::patch('/products/{product}',[ProductController::class, 'update']);
        Route::delete('/products/{product}',[ProductController::class, 'destroy'])->name('products.destroy');

        // Categories — full CRUD
        Route::post('/categories',              [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}',    [CategoryController::class, 'update'])->name('categories.update');
        Route::patch('/categories/{category}',  [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Suppliers — full CRUD
        Route::post('/suppliers',             [SupplierController::class, 'store'])->name('suppliers.store');
        Route::put('/suppliers/{supplier}',   [SupplierController::class, 'update'])->name('suppliers.update');
        Route::patch('/suppliers/{supplier}', [SupplierController::class, 'update']);
        Route::delete('/suppliers/{supplier}',[SupplierController::class, 'destroy'])->name('suppliers.destroy');

        // Warehouses — full CRUD
        Route::post('/warehouses',                                    [WarehouseController::class, 'store'])->name('warehouses.store');
        Route::put('/warehouses/{warehouse}',                         [WarehouseController::class, 'update'])->name('warehouses.update');
        Route::delete('/warehouses/{warehouse}',                      [WarehouseController::class, 'destroy'])->name('warehouses.destroy');
        Route::post('/warehouses/{warehouse}/racks',                  [WarehouseController::class, 'storeRack'])->name('racks.store');
        Route::put('/warehouses/{warehouse}/racks/{rack}',            [WarehouseController::class, 'updateRack'])->name('racks.update');
        Route::delete('/warehouses/{warehouse}/racks/{rack}',         [WarehouseController::class, 'destroyRack'])->name('racks.destroy');

        // Receipts — verify & delete
        Route::post('/receipts/{receipt}/verify', [ReceiptController::class, 'verify'])->name('receipts.verify');
        Route::delete('/receipts/{receipt}',      [ReceiptController::class, 'destroy'])->name('receipts.destroy');

        // Deliveries — cancel
        Route::post('/deliveries/{delivery}/cancel', [DeliveryController::class, 'cancel'])->name('deliveries.cancel');
        Route::delete('/deliveries/{delivery}',      [DeliveryController::class, 'destroy'])->name('deliveries.destroy');

        // Transfers — cancel & delete
        Route::post('/transfers/{transfer}/cancel', [TransferController::class, 'cancel'])->name('transfers.cancel');
        Route::delete('/transfers/{transfer}',      [TransferController::class, 'destroy'])->name('transfers.destroy');

        // Reorder Rules
        Route::get('/reorder-rules/triggered', [ReorderRuleController::class, 'triggered'])->name('reorder-rules.triggered');
        Route::resource('reorder-rules', ReorderRuleController::class)->except(['create', 'edit', 'show']);

        // Stock Adjustments
        Route::get('/adjustments',           [StockAdjustmentController::class, 'index'])->name('adjustments.index');
        Route::get('/adjustments/create',    [StockAdjustmentController::class, 'create'])->name('adjustments.create');
        Route::post('/adjustments',          [StockAdjustmentController::class, 'store'])->name('adjustments.store');
        Route::get('/adjustments/{adjustment}', [StockAdjustmentController::class, 'show'])->name('adjustments.show');

        // User Management
        Route::get('/users',                              [UserController::class, 'index'])->name('users.index');
        Route::post('/users',                             [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}',                       [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/reset-password',      [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::patch('/users/{user}/toggle-active',       [UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::delete('/users/{user}',                    [UserController::class, 'destroy'])->name('users.destroy');
    });
});
