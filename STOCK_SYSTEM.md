# Stock System Implementation - Phase 2

## Overview
The Stock system replaces the previous Inventory model with a granular, location-aware stock tracking system. Each stock record tracks quantity at a specific product-warehouse-location combination.

## Database Schema

### Stocks Table
```
id              - Primary key
product_id      - Foreign key to products
warehouse_id    - Foreign key to warehouses
location_id     - Foreign key to racks (nullable)
quantity        - Current quantity in stock
created_at      - Timestamp
updated_at      - Timestamp

Unique Constraint: (product_id, warehouse_id, location_id)
```

### Example Records
```
Steel Rod | Warehouse A | Rack A | 120
Steel Rod | Warehouse A | Rack B | 80
Steel Rod | Warehouse B | Rack C | 50
Bolt Box | Warehouse A | Rack A | 500
```

## Models

### Stock Model (`app/Models/Stock.php`)
**Relationships:**
- `product()` - BelongsTo Product
- `warehouse()` - BelongsTo Warehouse
- `location()` - BelongsTo Rack (via location_id)

**Key Methods:**
- `getOrCreate($productId, $warehouseId, $locationId)` - Get or create stock record
- `addStock($quantity)` - Increment quantity
- `removeStock($quantity)` - Decrement quantity (throws exception if insufficient)
- `getWarehouseTotal($productId, $warehouseId)` - Sum quantity across all locations in warehouse
- `getProductTotal($productId)` - Sum quantity across all warehouses

## Controllers

### InventoryController (`app/Http/Controllers/InventoryController.php`)
**Routes:**
- `GET /inventory` - List all stock records (paginated)
- `GET /inventory/summary` - Summary view by product and warehouse
- `GET /inventory/warehouse/{warehouse}` - Stock by warehouse
- `GET /inventory/product/{product}` - Stock by product

**Methods:**
- `index()` - All stocks with pagination
- `byWarehouse($warehouseId)` - Stocks in specific warehouse
- `byProduct($productId)` - All locations for a product
- `summary()` - Matrix view of products vs warehouses

## Views

### 1. Inventory Index (`resources/views/inventory/index.blade.php`)
- Lists all stock records
- Shows: Product, SKU, Warehouse, Location, Quantity
- Paginated (20 per page)
- Links to product detail view

### 2. Inventory Summary (`resources/views/inventory/summary.blade.php`)
- Matrix view: Products × Warehouses
- Shows total stock per product
- Shows stock per warehouse for each product
- Quick overview of inventory distribution

### 3. By Product (`resources/views/inventory/by-product.blade.php`)
- Product details (name, SKU, price)
- Stock value calculation
- All locations where product is stored
- Links to warehouse view

### 4. By Warehouse (`resources/views/inventory/by-warehouse.blade.php`)
- Warehouse details (manager, location, racks)
- All products in warehouse
- Stock by location (rack)
- Links to product view

## Integration with Existing Features

### Receipts (Incoming Stock)
When a receipt is verified:
```php
$stock = Stock::getOrCreate($productId, $warehouseId, $rackId);
$stock->addStock($quantity);
```

### Transfers (Internal Moves)
When a transfer is completed:
```php
// Remove from source
$sourceStock->removeStock($quantity);

// Add to destination
$destStock = Stock::getOrCreate($productId, $toWarehouseId, $toRackId);
$destStock->addStock($quantity);
```

## Key Features

1. **Location-Aware Tracking**
   - Track stock at product-warehouse-rack level
   - Support for warehouse-level stock (location_id = null)

2. **Automatic Record Creation**
   - `getOrCreate()` creates records on first use
   - No manual setup needed

3. **Stock Validation**
   - `removeStock()` throws exception if insufficient
   - Prevents negative stock

4. **Aggregation Methods**
   - Get warehouse totals
   - Get product totals across all warehouses

5. **Audit Trail**
   - InventoryLog tracks all movements
   - References receipts and transfers

## Usage Examples

### Get total stock for a product
```php
$total = Stock::getProductTotal($productId);
```

### Get stock in specific warehouse
```php
$warehouseTotal = Stock::getWarehouseTotal($productId, $warehouseId);
```

### Add stock from receipt
```php
$stock = Stock::getOrCreate($productId, $warehouseId, $rackId);
$stock->addStock(100);
```

### Transfer stock between locations
```php
$source = Stock::where('product_id', $productId)
    ->where('warehouse_id', $fromWarehouse)
    ->where('location_id', $fromRack)
    ->first();

$source->removeStock(50);

$dest = Stock::getOrCreate($productId, $toWarehouse, $toRack);
$dest->addStock(50);
```

## Navigation
- Sidebar: "Inventory" link
- Dashboard: Can add inventory stats
- Products: Can show stock levels
- Warehouses: Can show warehouse inventory

## Future Enhancements
- Stock adjustment interface (manual corrections)
- Low stock alerts
- Stock forecasting
- Batch operations
- Export/Import functionality
- Stock history/audit log viewer
