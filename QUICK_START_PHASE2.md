# Phase 2 Quick Start Guide

## Stock System Overview

### The Flow

```
┌─────────────────────────────────────────────────────────────┐
│                    STOCK MANAGEMENT FLOW                     │
└─────────────────────────────────────────────────────────────┘

1. ADD STOCK (via Receipts)
   ├─ Go to: Receipts → Create Receipt
   ├─ Select: Supplier, Warehouse, Rack
   ├─ Add: Products with quantities
   ├─ Save: Receipt (status: pending)
   └─ Verify: Click "Verify Receipt"
      └─ Creates: Stock records + Movement log

2. MOVE STOCK (via Transfers)
   ├─ Go to: Transfers → Create Transfer
   ├─ Select: From Warehouse/Rack → To Warehouse/Rack
   ├─ Add: Products with quantities
   ├─ Save: Transfer (status: pending)
   └─ Complete: Click "Complete Transfer"
      └─ Updates: Stock records + Movement log

3. VIEW STOCK (Inventory)
   ├─ Inventory → All stocks
   ├─ Inventory → Summary (matrix view)
   ├─ Inventory → By Product
   └─ Inventory → By Warehouse

4. AUDIT TRAIL (Stock Movements)
   ├─ Stock Movements → All movements
   ├─ Stock Movements → Full Ledger
   ├─ Stock Movements → By Product
   └─ Stock Movements → By Type
```

## Database Tables

### stocks
Stores current quantity at each location
```
Product: Steel Rod
├─ Warehouse A, Rack A: 120 units
├─ Warehouse A, Rack B: 80 units
└─ Warehouse B, Rack C: 50 units
```

### stock_movements
Records every change
```
Movement #1: Receipt
├─ Type: receipt
├─ Reference: Receipt #RCP-20260314-00001
├─ User: Admin User
├─ Date: 2026-03-14 10:30:45
└─ Items: 2

Movement #2: Transfer
├─ Type: transfer
├─ Reference: Transfer #TRF-20260314-00001
├─ User: Admin User
├─ Date: 2026-03-14 11:15:30
└─ Items: 1
```

### stock_movement_items
Details of each movement
```
Item #1:
├─ Product: Steel Rod
├─ From: null (external)
├─ To: Warehouse A, Rack A
└─ Qty: 120

Item #2:
├─ Product: Steel Rod
├─ From: Warehouse A, Rack A
├─ To: Warehouse B, Rack C
└─ Qty: 50
```

## Navigation Map

```
SIDEBAR
├─ Dashboard
├─ Products
├─ Categories
├─ Inventory ← NEW
│  ├─ All stocks
│  ├─ Summary
│  ├─ By Product
│  └─ By Warehouse
├─ Stock Movements ← NEW
│  ├─ All movements
│  ├─ Full Ledger
│  ├─ By Product
│  └─ By Type
├─ Warehouses
├─ Receipts
├─ Suppliers
└─ Transfers
```

## Common Tasks

### Task 1: Add 100 Steel Rods to Warehouse A, Rack A

1. Go to **Receipts**
2. Click **Create Receipt**
3. Fill in:
   - Supplier: ABC Supplies
   - Warehouse: Warehouse A
   - Receipt Date: Today
4. Add Item:
   - Product: Steel Rod
   - Quantity: 100
   - Unit Price: $5.00
   - Rack: Rack A
5. Click **Save**
6. Click **Verify Receipt**
7. ✅ Stock created: Steel Rod | Warehouse A | Rack A | 100

### Task 2: Move 50 Steel Rods from Rack A to Rack B

1. Go to **Transfers**
2. Click **Create Transfer**
3. Fill in:
   - From Warehouse: Warehouse A
   - To Warehouse: Warehouse A
   - Transfer Date: Today
4. Add Item:
   - Product: Steel Rod
   - Quantity: 50
   - From Rack: Rack A
   - To Rack: Rack B
5. Click **Save**
6. Click **Complete Transfer**
7. ✅ Stock updated:
   - Rack A: 100 → 50
   - Rack B: 0 → 50

### Task 3: Check Total Stock for Steel Rod

1. Go to **Inventory**
2. Click **Summary**
3. Find: Steel Rod row
4. See: Total stock across all warehouses
5. See: Stock per warehouse

### Task 4: View All Movements for Steel Rod

1. Go to **Stock Movements**
2. Click **By Product**
3. Select: Steel Rod
4. See: All receipts, transfers, adjustments
5. See: Complete history with dates and users

### Task 5: View Full Audit Trail

1. Go to **Stock Movements**
2. Click **Full Ledger**
3. See: All movements in chronological order
4. See: Every product, location, quantity change
5. See: Who made each change and when

## Movement Types

### Receipt (Green Badge)
- Stock coming IN from supplier
- Source: External
- Destination: Warehouse/Rack
- Example: 100 units received

### Transfer (Blue Badge)
- Stock moving between locations
- Source: Warehouse/Rack
- Destination: Warehouse/Rack
- Example: 50 units moved

### Delivery (Red Badge)
- Stock going OUT to customer (future)
- Source: Warehouse/Rack
- Destination: External
- Example: 30 units shipped

### Adjustment (Yellow Badge)
- Manual stock correction
- Source: Any location
- Destination: Any location
- Example: Inventory count correction

## Key Features

✅ **Location-Aware**
- Track stock at warehouse level
- Track stock at rack level
- Know exactly where everything is

✅ **Fully Traceable**
- Every change recorded
- User attribution
- Timestamp accuracy
- Reference to source document

✅ **Multiple Views**
- See all stock
- See by product
- See by warehouse
- See movement history

✅ **Audit Trail**
- Complete movement ledger
- Filter by type
- Filter by product
- Filter by user

## Data Integrity

- ✅ Unique constraint: One stock record per product-warehouse-location
- ✅ Foreign keys: All references validated
- ✅ Stock validation: Prevents negative inventory
- ✅ Automatic logging: Every change recorded
- ✅ User tracking: Who made each change

## Example Scenario

```
Day 1: Receive Stock
├─ Receipt #RCP-001 from ABC Supplies
├─ 100 Steel Rods → Warehouse A, Rack A
├─ 50 Bolts → Warehouse A, Rack B
└─ Movement logged: Receipt

Day 2: Internal Transfer
├─ Transfer #TRF-001
├─ 30 Steel Rods: Warehouse A, Rack A → Warehouse B, Rack C
└─ Movement logged: Transfer

Day 3: Check Inventory
├─ Inventory Summary:
│  ├─ Steel Rod: 70 total (40 in A, 30 in B)
│  └─ Bolts: 50 total (50 in A)
└─ Stock Movements:
   ├─ Receipt #RCP-001 (100 Steel Rods in)
   ├─ Transfer #TRF-001 (30 Steel Rods moved)
   └─ All changes tracked with user and timestamp
```

## Tips

💡 **Always verify receipts** - Stock only appears after verification
💡 **Check summary view** - Quick overview of all stock
💡 **Use ledger for audit** - Complete history of all changes
💡 **Filter by product** - See where each product is stored
💡 **Track by user** - See who made each change

## Status: ✅ READY TO USE

All Phase 2 features are implemented and ready:
- ✅ Stock tracking system
- ✅ Movement ledger
- ✅ Multiple views
- ✅ Full audit trail
- ✅ Complete integration
