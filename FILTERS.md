# Filters & Search - Phase 5

## Overview
Comprehensive filtering system implemented across all major modules: Products, Receipts, Deliveries, Transfers, and Inventory.

## Implemented Filters

### 1. Products (`/products`)

**Filters Available:**
- **Search** - Name or SKU (text input)
- **Category** - Filter by product category (dropdown)
- **Status** - Active/Inactive (dropdown)

**Controller Logic:**
```php
// Search by name or SKU
if (request('search')) {
    $query->where(function($q) {
        $q->where('name', 'like', '%' . request('search') . '%')
          ->orWhere('sku', 'like', '%' . request('search') . '%');
    });
}

// Filter by category
if (request('category_id')) {
    $query->where('category_id', request('category_id'));
}

// Filter by status
if (request('is_active') !== null) {
    $query->where('is_active', request('is_active'));
}
```

**Use Cases:**
- Find all products in "Electronics" category
- Search for product by SKU
- Show only active products
- Find products by name

### 2. Receipts (`/receipts`)

**Filters Available:**
- **Search** - Receipt number (text input)
- **Status** - Pending/Verified (dropdown)
- **Warehouse** - Filter by warehouse (dropdown)
- **Supplier** - Filter by supplier (dropdown)

**Controller Logic:**
```php
// Search by receipt number
if (request('search')) {
    $query->where('receipt_number', 'like', '%' . request('search') . '%');
}

// Filter by status
if (request('status')) {
    $query->where('status', request('status'));
}

// Filter by warehouse
if (request('warehouse_id')) {
    $query->where('warehouse_id', request('warehouse_id'));
}

// Filter by supplier
if (request('supplier_id')) {
    $query->where('supplier_id', request('supplier_id'));
}
```

**Use Cases:**
- Show all pending receipts (status = pending)
- Show all receipts in Warehouse A
- Find receipts from specific supplier
- Search receipt by number

### 3. Deliveries (`/deliveries`)

**Filters Available:**
- **Search** - Delivery number or customer name (text input)
- **Status** - Draft/Picking/Packed/Validated/Shipped/Cancelled (dropdown)
- **Warehouse** - Filter by warehouse (dropdown)

**Controller Logic:**
```php
// Search by delivery number or customer name
if (request('search')) {
    $query->where(function($q) {
        $q->where('delivery_number', 'like', '%' . request('search') . '%')
          ->orWhere('customer_name', 'like', '%' . request('search') . '%');
    });
}

// Filter by status
if (request('status')) {
    $query->where('status', request('status'));
}

// Filter by warehouse
if (request('warehouse_id')) {
    $query->where('warehouse_id', request('warehouse_id'));
}
```

**Use Cases:**
- Show all deliveries where status != shipped (pending deliveries)
- Show all deliveries in Warehouse A
- Find delivery by customer name
- Show only validated deliveries

### 4. Transfers (`/transfers`)

**Filters Available:**
- **Search** - Transfer number (text input)
- **Status** - Pending/Completed/Cancelled (dropdown)
- **From Warehouse** - Source warehouse (dropdown)
- **To Warehouse** - Destination warehouse (dropdown)

**Controller Logic:**
```php
// Search by transfer number
if (request('search')) {
    $query->where('transfer_number', 'like', '%' . request('search') . '%');
}

// Filter by status
if (request('status')) {
    $query->where('status', request('status'));
}

// Filter by from warehouse
if (request('from_warehouse_id')) {
    $query->where('from_warehouse_id', request('from_warehouse_id'));
}

// Filter by to warehouse
if (request('to_warehouse_id')) {
    $query->where('to_warehouse_id', request('to_warehouse_id'));
}
```

**Use Cases:**
- Show all pending transfers
- Show transfers from Warehouse A to Warehouse B
- Find transfer by number
- Show completed transfers

### 5. Inventory (`/inventory`)

**Filters Available:**
- **Warehouse** - Filter by warehouse (dropdown)
- **Product** - Filter by product (dropdown)

**Controller Logic:**
```php
// Filter by warehouse
if (request('warehouse_id')) {
    $query->where('warehouse_id', request('warehouse_id'));
}

// Filter by product
if (request('product_id')) {
    $query->where('product_id', request('product_id'));
}
```

**Use Cases:**
- Show all stock in Warehouse A
- Show stock for specific product
- View inventory by location

## UI Components

### Filter Bar Design

**Receipts, Transfers, Products (Tailwind):**
```html
<div class="bg-white rounded-lg shadow p-4 mb-4">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Filter inputs -->
        <button type="submit">Filter</button>
        <a href="...">Clear</a>
    </form>
</div>
```

**Deliveries (Bootstrap):**
```html
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <!-- Filter inputs -->
            <button type="submit">Filter</button>
            <a href="...">Clear</a>
        </form>
    </div>
</div>
```

### Common Features

1. **Clear Button** - Only shows when filters are active
2. **Auto-preserve** - Filter values preserved after submit
3. **Responsive** - Grid layout adapts to screen size
4. **Icons** - Font Awesome icons for visual clarity

## Filter Combinations

### Example Queries

**1. Show all pending receipts in Warehouse A:**
```
/receipts?status=pending&warehouse_id=1
```

**2. Show all deliveries where status != shipped:**
```
/deliveries?status=draft
/deliveries?status=picking
/deliveries?status=packed
/deliveries?status=validated
```

**3. Show all products in Electronics category:**
```
/products?category_id=2
```

**4. Show all transfers from Warehouse A:**
```
/transfers?from_warehouse_id=1
```

**5. Show all active products with "Steel" in name:**
```
/products?search=Steel&is_active=1
```

## Performance Considerations

### Indexed Columns
All filter columns are indexed for fast queries:
- `status` columns
- `warehouse_id` columns
- `category_id` column
- `supplier_id` column

### Query Optimization
- Uses `where()` clauses (indexed)
- Eager loading with `with()`
- Pagination (15 items per page)
- No N+1 queries

### Caching Opportunities (Future)
- Cache dropdown options (categories, warehouses, suppliers)
- Cache for 10 minutes
- Invalidate on create/update/delete

## User Experience

### Workflow
1. User selects filters
2. Clicks "Filter" button
3. Page reloads with filtered results
4. Filter values preserved in form
5. "Clear" button appears
6. Click "Clear" to reset

### Visual Feedback
- Selected values highlighted
- Clear button only when needed
- Result count visible
- Empty state messages

## Testing Checklist

✅ Products filters work
✅ Receipts filters work
✅ Deliveries filters work
✅ Transfers filters work
✅ Inventory filters work
✅ Search functionality works
✅ Multiple filters combine correctly
✅ Clear button resets all filters
✅ Filter values preserved after submit
✅ Pagination works with filters
✅ No errors with empty results
✅ Dropdown options load correctly

## Future Enhancements

### Advanced Filters
- Date range filters
- Price range filters
- Quantity range filters
- Multiple category selection
- Multiple warehouse selection

### Search Improvements
- Full-text search
- Fuzzy matching
- Search suggestions
- Recent searches

### UI Improvements
- Collapsible filter panel
- Save filter presets
- Export filtered results
- Filter count badges

### Performance
- AJAX filtering (no page reload)
- Infinite scroll
- Real-time search
- Filter result preview

## API Endpoints (Future)

```
GET /api/products?category_id=1&is_active=1
GET /api/receipts?status=pending&warehouse_id=1
GET /api/deliveries?status=draft
GET /api/transfers?from_warehouse_id=1&to_warehouse_id=2
```

## Success Criteria

✅ All major modules have filters
✅ Filters work independently
✅ Filters combine correctly
✅ Clear button resets all
✅ Values preserved after submit
✅ Performance acceptable (< 1s)
✅ Mobile responsive
✅ Intuitive UI
