# Stock Movement Ledger - Phase 2

## Overview
The Stock Movement Ledger provides complete traceability of all stock changes. Every movement is recorded with full audit trail including who made the change, when, and what was moved.

## Database Schema

### stock_movements Table
```
id              - Primary key
movement_type   - Enum: receipt, delivery, transfer, adjustment
reference_type  - String: Receipt, Transfer, Delivery, Manual
reference_id    - Foreign key to source document (nullable)
created_by      - Foreign key to users
notes           - Optional notes
created_at      - Timestamp
updated_at      - Timestamp
```

### stock_movement_items Table
```
id                          - Primary key
movement_id                 - Foreign key to stock_movements
product_id                  - Foreign key to products
source_location_id          - Foreign key to racks (nullable)
destination_location_id     - Foreign key to racks (nullable)
quantity                    - Quantity moved
created_at                  - Timestamp
updated_at                  - Timestamp
```

## Movement Types

### 1. Receipt
- **When**: Stock received from supplier
- **Source**: Null (external)
- **Destination**: Warehouse/Rack
- **Reference**: Receipt ID
- **Example**: 100 Steel Rods received from ABC Supplies → Warehouse A, Rack A

### 2. Transfer
- **When**: Stock moved between locations
- **Source**: Warehouse/Rack
- **Destination**: Warehouse/Rack
- **Reference**: Transfer ID
- **Example**: 50 Steel Rods moved from Warehouse A, Rack A → Warehouse B, Rack C

### 3. Delivery
- **When**: Stock shipped to customer (future feature)
- **Source**: Warehouse/Rack
- **Destination**: Null (external)
- **Reference**: Delivery ID
- **Example**: 30 Steel Rods shipped to Customer X

### 4. Adjustment
- **When**: Manual stock correction
- **Source**: Null or current location
- **Destination**: Null or new location
- **Reference**: Null (manual)
- **Example**: Stock count discrepancy corrected

## Models

### StockMovement
**Relationships:**
- `user()` - BelongsTo User (created_by)
- `items()` - HasMany StockMovementItem

**Factory Methods:**
- `createReceipt($receiptId, $userId, $notes)` - Create receipt movement
- `createTransfer($transferId, $userId, $notes)` - Create transfer movement
- `createDelivery($deliveryId, $userId, $notes)` - Create delivery movement
- `createAdjustment($userId, $notes)` - Create adjustment movement

**Helper Methods:**
- `getMovementTypeBadgeColor()` - Returns Bootstrap color class
- `getMovementTypeLabel()` - Returns formatted label

### StockMovementItem
**Relationships:**
- `movement()` - BelongsTo StockMovement
- `product()` - BelongsTo Product
- `sourceLocation()` - BelongsTo Rack
- `destinationLocation()` - BelongsTo Rack

## Controllers

### StockMovementController
**Routes:**
- `GET /stock-movements` - List all movements (paginated)
- `GET /stock-movements/{movement}` - View movement details
- `GET /stock-movements/ledger` - Full ledger view (all movements)
- `GET /stock-movements/product/{product}` - Movements for specific product
- `GET /stock-movements/type/{type}` - Movements by type (receipt, transfer, etc.)

**Methods:**
- `index()` - Paginated list of movements
- `show($movement)` - Movement details with items
- `byProduct($productId)` - Product movement history
- `byType($type)` - Filter by movement type
- `ledger()` - Complete audit trail

## Views

### 1. Stock Movements Index
- List of all movements
- Shows: Date, Type, Reference, User, Item Count
- Paginated (20 per page)
- Links to detail view

### 2. Movement Details
- Full movement information
- Movement type and reference
- Created by and timestamp
- All items in movement with locations
- Source and destination tracking

### 3. Full Ledger
- Complete audit trail
- One row per item
- Shows: Date, Type, Reference, Product, From, To, Qty, User
- Compact view for analysis
- No pagination (all records)

### 4. By Product
- Movement history for specific product
- All locations where product has been
- Complete traceability
- Paginated

### 5. By Type
- Filter movements by type
- Receipt, Transfer, Delivery, or Adjustment
- Detailed view of each movement type
- Paginated

## Integration Points

### Receipt Verification
```php
$movement = StockMovement::createReceipt($receipt->id, Auth::id(), $receipt->notes);

foreach ($receipt->items as $item) {
    StockMovementItem::create([
        'movement_id' => $movement->id,
        'product_id' => $item->product_id,
        'source_location_id' => null,
        'destination_location_id' => $item->rack_id,
        'quantity' => $item->quantity,
    ]);
}
```

### Transfer Completion
```php
$movement = StockMovement::createTransfer($transfer->id, Auth::id(), $transfer->notes);

foreach ($transfer->items as $item) {
    StockMovementItem::create([
        'movement_id' => $movement->id,
        'product_id' => $item->product_id,
        'source_location_id' => $item->from_rack_id,
        'destination_location_id' => $item->to_rack_id,
        'quantity' => $item->quantity,
    ]);
}
```

## Audit Trail Features

### Complete Traceability
- Every stock change is recorded
- User who made the change
- Exact timestamp
- Source and destination
- Reference to original document

### Movement Types
- **Receipt** (green) - Stock incoming
- **Transfer** (blue) - Stock moving
- **Delivery** (red) - Stock outgoing
- **Adjustment** (yellow) - Manual correction

### Query Examples

**Get all movements for a product:**
```php
$movements = StockMovement::whereHas('items', function ($query) use ($productId) {
    $query->where('product_id', $productId);
})->get();
```

**Get movements by type:**
```php
$receipts = StockMovement::where('movement_type', 'receipt')->get();
```

**Get movements by user:**
```php
$userMovements = StockMovement::where('created_by', $userId)->get();
```

**Get movements in date range:**
```php
$movements = StockMovement::whereBetween('created_at', [$start, $end])->get();
```

## Navigation
- Sidebar: "Stock Movements" link
- From Inventory: View product movements
- From Receipts: View receipt movements
- From Transfers: View transfer movements

## Compliance & Audit
- All movements logged automatically
- No manual deletion of movements
- Immutable audit trail
- User attribution
- Timestamp accuracy
- Reference tracking

## Future Enhancements
- Export ledger to CSV/PDF
- Advanced filtering and search
- Movement reconciliation
- Stock variance analysis
- Automated alerts for discrepancies
- Movement approval workflow
- Batch movement operations
