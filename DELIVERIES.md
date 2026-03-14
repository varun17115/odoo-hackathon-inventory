# Deliveries (Outgoing Stock) System

## Overview
The Deliveries system manages outgoing stock to customers with a complete workflow: Create → Pick → Pack → Validate → Ship. Stock is automatically reduced when deliveries are validated.

## Database Schema

### deliveries Table
```
id                  - Primary key
delivery_number     - Unique (DEL-YYYYMMDD-#####)
customer_name       - Customer name
customer_phone      - Customer phone (optional)
customer_email      - Customer email (optional)
delivery_address    - Delivery address
warehouse_id        - Foreign key to warehouses
user_id             - Foreign key to users (created by)
delivery_date       - Scheduled delivery date
status              - Enum: draft, picking, packed, validated, shipped, cancelled
total_amount        - Total delivery value
notes               - Optional notes
created_at          - Timestamp
updated_at          - Timestamp
```

### delivery_items Table
```
id              - Primary key
delivery_id     - Foreign key to deliveries
product_id      - Foreign key to products
rack_id         - Foreign key to racks (source location)
quantity        - Quantity to deliver
unit_price      - Price per unit
total_price     - Total price (quantity × unit_price)
is_picked       - Boolean (item picked from rack)
is_packed       - Boolean (item packed for delivery)
notes           - Optional notes
created_at      - Timestamp
updated_at      - Timestamp
```

## Workflow States

### 1. Draft
- Initial state when delivery is created
- Can edit or delete
- Can start picking
- Can cancel

### 2. Picking
- Items are being picked from racks
- Each item can be marked as picked
- Cannot proceed until all items picked
- Can cancel

### 3. Packed
- All items picked and packed
- Ready for validation
- Can validate (reduces stock)
- Can cancel

### 4. Validated
- Stock has been reduced
- Delivery confirmed
- Can mark as shipped
- Cannot cancel

### 5. Shipped
- Delivery has been shipped
- Final state
- Cannot modify

### 6. Cancelled
- Delivery cancelled
- No stock changes
- Final state

## Workflow Actions

### Create Delivery
1. Enter customer information
2. Select warehouse
3. Add products with quantities and prices
4. Select rack for each item
5. Save as draft

### Start Picking
- Changes status from draft → picking
- Enables item-by-item picking

### Mark Item Picked
- Mark individual items as picked
- Track picking progress
- All items must be picked to proceed

### Pack Items
- Changes status from picking → packed
- Marks all items as packed
- Ready for validation

### Validate Delivery
- Changes status from packed → validated
- **Reduces stock** from specified racks
- Creates StockMovement (type: delivery)
- Logs InventoryLog entries
- **Irreversible action**

### Mark as Shipped
- Changes status from validated → shipped
- Final confirmation
- Delivery complete

### Cancel Delivery
- Available in: draft, picking, packed
- Changes status to cancelled
- No stock changes

## Models

### Delivery
**Relationships:**
- `warehouse()` - BelongsTo Warehouse
- `user()` - BelongsTo User
- `items()` - HasMany DeliveryItem

**Methods:**
- `generateDeliveryNumber()` - Auto-generate DEL-YYYYMMDD-#####
- `getTotalQuantity()` - Sum of all item quantities
- `getStatusBadgeColor()` - Bootstrap color for status
- `getStatusLabel()` - Formatted status label
- `canPick()` - Check if can start picking
- `canPack()` - Check if can pack (all items picked)
- `canValidate()` - Check if can validate (all items packed)
- `canCancel()` - Check if can cancel

### DeliveryItem
**Relationships:**
- `delivery()` - BelongsTo Delivery
- `product()` - BelongsTo Product
- `rack()` - BelongsTo Rack

**Properties:**
- `is_picked` - Boolean flag
- `is_packed` - Boolean flag

## Controller Actions

### DeliveryController
**Routes:**
- `GET /deliveries` - List all deliveries
- `GET /deliveries/create` - Create form
- `POST /deliveries` - Store new delivery
- `GET /deliveries/{delivery}` - View details
- `POST /deliveries/{delivery}/start-picking` - Start picking
- `POST /deliveries/{delivery}/items/{item}/mark-picked` - Mark item picked
- `POST /deliveries/{delivery}/start-packing` - Pack items
- `POST /deliveries/{delivery}/validate` - Validate & reduce stock
- `POST /deliveries/{delivery}/ship` - Mark as shipped
- `POST /deliveries/{delivery}/cancel` - Cancel delivery
- `DELETE /deliveries/{delivery}` - Delete draft delivery

## Stock Integration

### When Validated
```php
// For each delivery item:
1. Find stock record (product + warehouse + rack)
2. Reduce quantity: $stock->removeStock($quantity)
3. Create StockMovement (type: delivery)
4. Create StockMovementItem (source: rack, destination: null)
5. Create InventoryLog (type: out)
```

### Stock Movement Record
```
Movement Type: delivery
Reference: Delivery #DEL-20260314-00001
Items:
├─ Product: Chair
├─ From: Warehouse A, Rack A
├─ To: null (external - customer)
└─ Quantity: 10
```

## Views

### 1. Deliveries Index
- List all deliveries
- Shows: Number, Customer, Warehouse, Date, Items, Total, Status
- Create button
- View details link

### 2. Create Delivery
- Customer information form
- Delivery details (warehouse, date)
- Dynamic item addition
- Auto-populate prices from products
- Rack selection per item
- Stock validation on submit

### 3. Delivery Details
- Customer information
- Delivery details
- Items table with pick/pack status
- Workflow action buttons
- Status-dependent actions

## Example Workflow

```
Day 1: Create Delivery
├─ Customer: John Doe
├─ Address: 123 Main St
├─ Warehouse: Warehouse A
├─ Items:
│  ├─ 10 Chairs @ $50 from Rack A
│  └─ 5 Tables @ $100 from Rack B
├─ Total: $1,000
└─ Status: Draft

Day 2: Start Picking
├─ Status: Draft → Picking
├─ Pick 10 Chairs from Rack A ✓
├─ Pick 5 Tables from Rack B ✓
└─ All items picked

Day 2: Pack Items
├─ Status: Picking → Packed
└─ All items packed ✓

Day 2: Validate
├─ Status: Packed → Validated
├─ Stock reduced:
│  ├─ Chairs: Rack A (50 → 40)
│  └─ Tables: Rack B (20 → 15)
├─ Movement logged: Delivery #DEL-001
└─ Inventory logs created

Day 3: Ship
├─ Status: Validated → Shipped
└─ Delivery complete
```

## Status Badge Colors

- **Draft** (gray) - Not started
- **Picking** (blue) - In progress
- **Packed** (primary) - Ready to validate
- **Validated** (green) - Stock reduced
- **Shipped** (dark) - Complete
- **Cancelled** (red) - Cancelled

## Validation Rules

### Create Delivery
- Customer name required
- Delivery address required
- Warehouse required
- At least one item required
- Stock availability checked

### Start Picking
- Must be in draft status

### Mark Item Picked
- Delivery must be in picking status

### Pack Items
- All items must be picked

### Validate
- All items must be packed
- Stock availability re-checked
- Reduces stock (irreversible)

### Ship
- Must be validated first

### Cancel
- Only draft, picking, or packed
- Cannot cancel after validation

## Navigation
- Sidebar: "Deliveries" link
- From Stock Movements: View delivery movements
- From Inventory: Check stock before delivery

## Key Features

✅ **Complete Workflow**
- Draft → Picking → Packed → Validated → Shipped
- Status-based actions
- Progress tracking

✅ **Item-Level Tracking**
- Pick status per item
- Pack status per item
- Rack assignment

✅ **Stock Integration**
- Automatic stock reduction
- Movement logging
- Inventory audit trail

✅ **Customer Management**
- Customer information
- Delivery address
- Contact details

✅ **Validation**
- Stock availability check
- Prevent overselling
- Confirmation prompts

## Future Enhancements
- Partial deliveries
- Delivery tracking
- Customer portal
- Delivery notes/signatures
- Barcode scanning for picking
- Packing slip generation
- Shipping label integration
- Delivery route optimization
