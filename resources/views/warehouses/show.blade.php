@extends('layouts.app')

@section('title', $warehouse->name . ' - Invento Market')
@section('page-title', $warehouse->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('warehouses.index') }}" class="text-blue-600 hover:text-blue-800">← Back to Warehouses</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Warehouse Details -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">{{ $warehouse->name }}</h2>
                <p class="text-gray-600 mt-1">{{ $warehouse->location ?? 'No location specified' }}</p>
            </div>
            <div class="flex gap-2">
                <button class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition" data-bs-toggle="modal" data-bs-target="#warehouseModal" onclick="editWarehouse({{ $warehouse->id }}, '{{ $warehouse->name }}', '{{ $warehouse->location }}', '{{ $warehouse->description }}', '{{ $warehouse->manager_name }}', '{{ $warehouse->manager_phone }}')">
                    Edit
                </button>
                <form method="POST" action="{{ route('warehouses.destroy', $warehouse) }}" class="inline" onsubmit="return confirmDelete(this)">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-8">
            <div>
                <p class="text-gray-600 text-sm">Manager</p>
                <p class="text-lg font-semibold text-gray-800">{{ $warehouse->manager_name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Phone</p>
                <p class="text-lg font-semibold text-gray-800">{{ $warehouse->manager_phone ?? 'N/A' }}</p>
            </div>
        </div>

        @if($warehouse->description)
            <div class="mb-8">
                <p class="text-gray-600 text-sm">Description</p>
                <p class="text-gray-800">{{ $warehouse->description }}</p>
            </div>
        @endif

        <div class="border-t pt-6">
            <p class="text-gray-600 text-sm">Status</p>
            <div class="mt-2">
                @if($warehouse->is_active)
                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Active</span>
                @else
                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">Inactive</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Stats</h3>
        <div class="space-y-4">
            <div>
                <p class="text-gray-600 text-sm">Total Racks</p>
                <p class="text-3xl font-bold text-blue-600">{{ $warehouse->racks->count() }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Active Racks</p>
                <p class="text-3xl font-bold text-green-600">{{ $warehouse->racks->where('is_active', true)->count() }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Total Capacity</p>
                <p class="text-3xl font-bold text-purple-600">{{ $warehouse->racks->sum('capacity') ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Racks Section -->
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-800">Racks</h3>
        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" data-bs-toggle="modal" data-bs-target="#rackModal" onclick="resetRackForm()">
            + Add Rack
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Capacity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($warehouse->racks as $rack)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $rack->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $rack->location ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $rack->capacity ?? 'Unlimited' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $rack->description ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($rack->is_active)
                                <span class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex gap-2">
                                <button class="text-yellow-600 hover:text-yellow-800" data-bs-toggle="modal" data-bs-target="#rackModal" onclick="editRack({{ $rack->id }}, '{{ $rack->name }}', '{{ $rack->location }}', '{{ $rack->description }}', {{ $rack->capacity ?? 'null' }})">Edit</button>
                                <form method="POST" action="{{ route('racks.destroy', [$warehouse, $rack]) }}" class="inline" onsubmit="return confirmDelete(this)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No racks found. <button class="text-blue-600 hover:text-blue-800" data-bs-toggle="modal" data-bs-target="#rackModal" onclick="resetRackForm()">Create one</button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Warehouse Modal -->
<div class="modal fade" id="warehouseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Warehouse</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="warehouseForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="warehouse_name" class="form-label">Warehouse Name *</label>
                        <input type="text" class="form-control" id="warehouse_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="warehouse_location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="warehouse_location" name="location">
                    </div>
                    <div class="mb-3">
                        <label for="warehouse_description" class="form-label">Description</label>
                        <textarea class="form-control" id="warehouse_description" name="description" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="warehouse_manager" class="form-label">Manager Name</label>
                            <input type="text" class="form-control" id="warehouse_manager" name="manager_name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="warehouse_phone" class="form-label">Manager Phone</label>
                            <input type="text" class="form-control" id="warehouse_phone" name="manager_phone">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Warehouse</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Rack Modal -->
<div class="modal fade" id="rackModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rackModalTitle">Add Rack</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rackForm" method="POST" action="{{ route('racks.store', $warehouse) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rack_name" class="form-label">Rack Name *</label>
                        <input type="text" class="form-control" id="rack_name" name="name" placeholder="e.g., Rack A, Rack B" required>
                    </div>
                    <div class="mb-3">
                        <label for="rack_location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="rack_location" name="location" placeholder="e.g., Aisle 1, Section 2">
                    </div>
                    <div class="mb-3">
                        <label for="rack_capacity" class="form-label">Capacity</label>
                        <input type="number" class="form-control" id="rack_capacity" name="capacity" min="1" placeholder="e.g., 100">
                    </div>
                    <div class="mb-3">
                        <label for="rack_description" class="form-label">Description</label>
                        <textarea class="form-control" id="rack_description" name="description" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Rack</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetRackForm() {
    document.getElementById('rackForm').reset();
    document.getElementById('rackForm').action = '{{ route('racks.store', $warehouse) }}';
    document.getElementById('rackForm').method = 'POST';
    document.getElementById('rackModalTitle').textContent = 'Add Rack';
    document.querySelector('#rackForm input[name="_method"]')?.remove();
}

function editRack(id, name, location, description, capacity) {
    document.getElementById('rackForm').reset();
    document.getElementById('rack_name').value = name;
    document.getElementById('rack_location').value = location;
    document.getElementById('rack_description').value = description;
    if (capacity) document.getElementById('rack_capacity').value = capacity;
    document.getElementById('rackModalTitle').textContent = 'Edit Rack';
    
    document.querySelector('#rackForm input[name="_method"]')?.remove();
    
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'PUT';
    document.getElementById('rackForm').appendChild(methodInput);
    
    document.getElementById('rackForm').action = `/warehouses/{{ $warehouse->id }}/racks/${id}`;
}

function editWarehouse(id, name, location, description, manager_name, manager_phone) {
    document.getElementById('warehouse_name').value = name;
    document.getElementById('warehouse_location').value = location;
    document.getElementById('warehouse_description').value = description;
    document.getElementById('warehouse_manager').value = manager_name;
    document.getElementById('warehouse_phone').value = manager_phone;
    document.getElementById('warehouseForm').action = `/warehouses/${id}`;
}
</script>
@endsection
