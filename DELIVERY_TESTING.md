# Delivery System Testing Guide

## Stock Reduction Fix

### Issue
Stock was not being reduced after delivery validation because the query wasn't handling `null` rack_id properly.

### Fix Applied
Updated `DeliveryController::validate()` method to properly query stock records:

```php
// Before (WRONG):
$stock = Stock::where('product_id', $item->product_id)
    ->where('warehouse_id', $delivery->warehouse_id)
    ->where('location_id', $item->rack_id)  // This fails when rack_id is null
    ->first();

// After (CORRECT):
$query = Stock::where('product_id', $item->product_id)
    ->where('warehouse_id', $delivery->warehouse_id);

if ($item->rack_id) {
    $query->where('location_id', $item->rack_id);
} else {
    $query->whereNull('location_id');
}

$stock = $query->first();
```

### Error Handling
- Added try-catch block
- Throws exception if stock not found
- Throws exception if insufficient stock (from Stock model)
- Shows error message to user via SweetAlert

## SweetAlert Integration

### All Delivery Views Now Use SweetAlert

#### 1. Index Page (`deliveries/index.blade.php`)
- Success messages
- Error messages
- Auto-dismiss after 3 seconds

#### 2. Show Page (`deliveries/show.blade.php`)
- **Start Picking** - Confirmation dialog
- **Pack Items** - Confirmation dialog
- **Validate** - Warning dialog with emphasis on stock reduction
- **Ship** - Confirmation dialog
- **Cancel** - Warning dialog
- **Delete** - Error dialog
- Success/Error feedback after actions

#### 3. Create Page (`deliveries/create.blade.php`)
- Validation errors shown in SweetAlert
- Better error visibility

## Testing Steps

### Test 1: Create Delivery with Stock
1. Go to Receipts → Create Receipt
2. Add 100 units of Product A to Warehouse A, Rack A
3. Verify receipt (creates stock)
4. Go to Deliveries → Create Delivery
5. Add 10 units of Product A from Warehouse A, Rack A
6. Save delivery
7. ✅ Should create successfully

### Test 2: Complete Workflow
1. Open delivery from Test 1
2. Click "Start Picking"
   - ✅ SweetAlert confirmation
   - ✅ Status changes to "Picking"
3. Click "Pick" for each item
   - ✅ Item marked as picked
4. Click "Pack Items"
   - ✅ SweetAlert confirmation
   - ✅ Status changes to "Packed"
5. Click "Validate & Update Stock"
   - ✅ SweetAlert warning about stock reduction
   - ✅ Status changes to "Validated"
   - ✅ Stock reduced by 10 (100 → 90)
6. Check Inventory
   - ✅ Product A shows 90 units
7. Check Stock Movements
   - ✅ Delivery movement logged
8. Click "Mark as Shipped"
   - ✅ Status changes to "Shipped"

### Test 3: Stock Reduction Verification
1. Before validation, check stock:
   - Go to Inventory
   - Find Product A in Warehouse A, Rack A
   - Note quantity (e.g., 90)
2. Validate delivery with 10 units
3. After validation, check stock again:
   - ✅ Quantity should be 80 (90 - 10)
4. Check Stock Movements:
   - ✅ Should show delivery movement
   - ✅ Type: delivery (red badge)
   - ✅ From: Rack A
   - ✅ To: null (external)
   - ✅ Quantity: 10

### Test 4: Insufficient Stock
1. Create delivery with 100 units
2. Only have 80 units in stock
3. Try to validate
4. ✅ Should show error: "Insufficient stock. Available: 80, Requested: 100"
5. ✅ Status remains "Packed"
6. ✅ Stock unchanged

### Test 5: Stock Not Found
1. Create delivery for Product B
2. Product B has no stock in selected rack
3. Try to validate
4. ✅ Should show error: "Stock not found for product ID X"
5. ✅ Status remains "Packed"

### Test 6: Cancel Delivery
1. Create delivery (status: draft)
2. Click "Cancel"
   - ✅ SweetAlert warning
   - ✅ Status changes to "Cancelled"
   - ✅ No stock changes
3. Try to validate cancelled delivery
   - ✅ Buttons disabled/hidden

### Test 7: Delete Draft
1. Create delivery (status: draft)
2. Click "Delete"
   - ✅ SweetAlert error dialog
   - ✅ Delivery deleted
   - ✅ Redirected to index
3. Try to delete validated delivery
   - ✅ Button not shown

## SweetAlert Dialogs

### Success (Green)
- Delivery created
- Picking started
- Items packed
- Delivery validated
- Delivery shipped

### Error (Red)
- Validation errors
- Insufficient stock
- Stock not found
- Cannot perform action

### Warning (Yellow)
- Validate delivery (stock reduction)
- Cancel delivery

### Question (Blue)
- Start picking
- Pack items
- Mark as shipped

### Delete (Red)
- Delete delivery

## Expected Behavior

### Stock Reduction
- ✅ Only happens on "Validate"
- ✅ Reduces from correct rack
- ✅ Handles null rack_id
- ✅ Throws error if insufficient
- ✅ Throws error if not found
- ✅ Creates stock movement
- ✅ Creates inventory log

### Workflow
- ✅ Draft → Picking → Packed → Validated → Shipped
- ✅ Cannot skip steps
- ✅ Cannot go backwards
- ✅ Cancel only before validation
- ✅ Delete only in draft

### UI/UX
- ✅ All confirmations use SweetAlert
- ✅ Clear warning for stock reduction
- ✅ Auto-dismiss success messages
- ✅ Error messages stay until dismissed
- ✅ Status badges color-coded

## Common Issues

### Issue: Stock not reducing
**Cause**: rack_id query not handling null
**Fix**: Applied in DeliveryController::validate()
**Test**: Create delivery without rack, validate, check stock

### Issue: "Stock not found" error
**Cause**: No stock record exists for product-warehouse-rack combination
**Solution**: Create receipt first to add stock

### Issue: "Insufficient stock" error
**Cause**: Trying to deliver more than available
**Solution**: Reduce delivery quantity or add more stock

## Database Verification

### Check Stock Before/After
```sql
SELECT * FROM stocks 
WHERE product_id = X 
AND warehouse_id = Y 
AND location_id = Z;
```

### Check Stock Movements
```sql
SELECT * FROM stock_movements 
WHERE movement_type = 'delivery' 
ORDER BY created_at DESC;
```

### Check Movement Items
```sql
SELECT smi.*, p.name 
FROM stock_movement_items smi
JOIN products p ON smi.product_id = p.id
WHERE movement_id = X;
```

## Success Criteria

✅ Stock reduces correctly on validation
✅ All actions use SweetAlert
✅ Proper error handling
✅ Stock movements logged
✅ Workflow enforced
✅ Cannot oversell
✅ Clear user feedback
