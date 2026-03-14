# Dashboard - Phase 4

## Overview
The Dashboard provides a comprehensive overview of the inventory management system with real-time KPIs, alerts, and recent activity.

## KPI Cards (Top Row)

### 1. Total Products (Blue)
- **Query**: `Product::where('is_active', true)->count()`
- **Shows**: Number of active products in the system
- **Action**: Links to Products page
- **Icon**: Box

### 2. Low Stock Items (Yellow/Warning)
- **Query**: `Stock::where('quantity', '<', 10)->count()`
- **Shows**: Number of stock records with quantity below 10
- **Action**: Links to Inventory page
- **Icon**: Exclamation Triangle
- **Alert**: Visual warning indicator

### 3. Pending Receipts (Cyan/Info)
- **Query**: `Receipt::where('status', 'pending')->count()`
- **Shows**: Receipts awaiting verification
- **Action**: Links to Receipts page
- **Icon**: File Invoice

### 4. Pending Deliveries (Red/Danger)
- **Query**: `Delivery::whereNotIn('status', ['shipped', 'cancelled'])->count()`
- **Shows**: Deliveries in progress (draft, picking, packed, validated)
- **Action**: Links to Deliveries page
- **Icon**: Truck

### 5. Internal Transfers (Green/Success)
- **Query**: `Transfer::where('status', 'pending')->count()`
- **Shows**: Transfers awaiting completion
- **Action**: Links to Transfers page
- **Icon**: Exchange

## Additional Stats (Second Row)

### Total Categories
- Count of all product categories
- Icon: Tags
- Color: Primary

### Active Warehouses
- Count of active warehouses
- Icon: Warehouse
- Color: Info

### Total Stock Quantity
- Sum of all stock quantities across all locations
- Icon: Cubes
- Color: Success
- Format: Number with commas

## Widgets

### 1. Low Stock Alert (Left Column)
**Purpose**: Immediate visibility of items needing restock

**Data Shown**:
- Product name and SKU
- Warehouse location
- Rack location
- Current quantity (highlighted in warning color)

**Query**:
```php
Stock::with(['product', 'warehouse', 'location'])
    ->where('quantity', '<', 10)
    ->orderBy('quantity', 'asc')
    ->limit(10)
    ->get()
```

**Features**:
- Sorted by quantity (lowest first)
- Limited to 10 items
- Direct links to product details
- Color-coded warnings

### 2. Recent Stock Movements (Right Column)
**Purpose**: Track recent inventory activity

**Data Shown**:
- Movement type (receipt, delivery, transfer, adjustment)
- Reference number
- Number of items
- Time ago

**Query**:
```php
StockMovement::with(['user', 'items.product'])
    ->latest()
    ->limit(10)
    ->get()
```

**Features**:
- Color-coded badges by type
- Clickable references
- Relative timestamps
- Shows item count

### 3. Recent Receipts (Bottom Left)
**Purpose**: Monitor incoming stock

**Data Shown**:
- Receipt number
- Supplier name
- Status (pending/verified)
- Receipt date

**Query**:
```php
Receipt::with(['supplier', 'warehouse'])
    ->latest()
    ->limit(5)
    ->get()
```

**Features**:
- Status badges
- Clickable receipt numbers
- Supplier information
- Date formatting

### 4. Recent Deliveries (Bottom Right)
**Purpose**: Track outgoing orders

**Data Shown**:
- Delivery number
- Customer name
- Status (draft/picking/packed/validated/shipped)
- Delivery date

**Query**:
```php
Delivery::latest()
    ->limit(5)
    ->get()
```

**Features**:
- Status badges with colors
- Clickable delivery numbers
- Customer information
- Date formatting

### 5. Stock by Warehouse (Bottom Full Width)
**Purpose**: Overview of stock distribution

**Data Shown**:
- Warehouse name
- Number of stock records
- Total quantity in warehouse

**Query**:
```php
Warehouse::where('is_active', true)
    ->withCount('stocks')
    ->get()
    ->map(function ($warehouse) {
        return [
            'name' => $warehouse->name,
            'count' => $warehouse->stocks_count,
            'total_quantity' => Stock::where('warehouse_id', $warehouse->id)->sum('quantity'),
        ];
    })
```

**Features**:
- Grid layout (4 per row)
- Light background cards
- Formatted numbers
- Quick comparison

## Color Scheme

### KPI Cards
- **Primary (Blue)**: Total Products
- **Warning (Yellow)**: Low Stock
- **Info (Cyan)**: Pending Receipts
- **Danger (Red)**: Pending Deliveries
- **Success (Green)**: Transfers

### Status Badges
- **Success (Green)**: Verified, Validated, Completed
- **Warning (Yellow)**: Pending, Draft
- **Info (Blue)**: Picking, In Progress
- **Danger (Red)**: Cancelled, Error
- **Dark**: Shipped, Final

## User Experience

### Quick Actions
- Each KPI card has a "View" button
- Direct navigation to relevant sections
- One-click access to details

### Visual Hierarchy
1. KPIs at top (most important)
2. Additional stats below
3. Detailed widgets in grid
4. Warehouse overview at bottom

### Responsive Design
- Cards stack on mobile
- Tables scroll horizontally
- Grid adjusts to screen size

### Real-time Data
- All data fetched on page load
- No caching (always current)
- Refresh page for updates

## Performance Considerations

### Optimized Queries
- Uses `with()` for eager loading
- Limits result sets (5-10 items)
- Indexed columns for fast queries
- Aggregations done in database

### Caching Opportunities (Future)
- Cache KPI counts for 5 minutes
- Cache warehouse stats for 10 minutes
- Invalidate on stock changes

## Navigation Flow

```
Dashboard
├─ Total Products → Products Page
├─ Low Stock → Inventory Page
├─ Pending Receipts → Receipts Page
├─ Pending Deliveries → Deliveries Page
├─ Transfers → Transfers Page
├─ Low Stock Alert → Product Details
├─ Recent Movements → Movement Details
├─ Recent Receipts → Receipt Details
└─ Recent Deliveries → Delivery Details
```

## Business Value

### At-a-Glance Insights
- Immediate visibility of critical metrics
- No need to navigate multiple pages
- Quick decision making

### Proactive Alerts
- Low stock warnings
- Pending action items
- Recent activity monitoring

### Operational Efficiency
- Identify bottlenecks (pending items)
- Track inventory flow
- Monitor warehouse utilization

## Future Enhancements

### Charts & Graphs
- Stock trends over time
- Movement type distribution
- Warehouse comparison chart
- Product category breakdown

### Filters
- Date range selector
- Warehouse filter
- Product category filter

### Drill-down
- Click on stats to see details
- Interactive charts
- Export capabilities

### Notifications
- Real-time alerts
- Email notifications
- Low stock warnings

### Customization
- User preferences
- Widget arrangement
- Metric thresholds
- Custom KPIs

## Testing Checklist

✅ All KPI counts accurate
✅ Low stock threshold working (< 10)
✅ Pending receipts filtered correctly
✅ Pending deliveries exclude shipped/cancelled
✅ Pending transfers show only pending
✅ Recent movements display correctly
✅ Low stock products sorted by quantity
✅ All links navigate correctly
✅ Status badges show correct colors
✅ Warehouse stats calculate correctly
✅ Responsive on mobile
✅ No N+1 query issues

## Success Criteria

✅ Dashboard loads in < 2 seconds
✅ All KPIs visible without scrolling
✅ Low stock items highlighted
✅ Recent activity shows last 10 items
✅ All navigation links work
✅ Data refreshes on page reload
✅ Visual hierarchy clear
✅ Color coding consistent
