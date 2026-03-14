@extends('layouts.app')
@section('page-title', 'Users')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <h2>System Users</h2>
        <p>Control access levels and manage team member permissions.</p>
    </div>
    <button class="btn-primary-sm" onclick="openCreateModal()">
        <i class="fas fa-user-plus"></i> Add User
    </button>
</div>

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Joined</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr style="{{ !$user->is_active ? 'opacity:0.55;' : '' }}">
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:34px;height:34px;border-radius:8px;background:#4f46e5;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.75rem;flex-shrink:0;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight:600;color:#0f172a;font-size:0.85rem;">{{ $user->name }}</div>
                            @if($user->id === auth()->id())
                                <div style="font-size:0.68rem;color:#4f46e5;font-weight:700;">You</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td style="font-size:0.82rem;color:#475569;">{{ $user->email }}</td>
                <td>
                    @if($user->role === 'admin')
                        <span class="badge badge-purple">Administrator</span>
                    @else
                        <span class="badge badge-gray">Staff</span>
                    @endif
                </td>
                <td>
                    @if($user->is_active)
                        <span class="badge badge-green"><i class="fas fa-circle" style="font-size:0.4rem;margin-right:4px;"></i>Active</span>
                    @else
                        <span class="badge badge-red">Inactive</span>
                    @endif
                </td>
                <td style="font-size:0.78rem;color:#94a3b8;">{{ $user->created_at->format('M d, Y') }}</td>
                <td style="text-align:right;">
                    <button class="icon-btn edit" onclick='openEditModal(@json($user))' title="Edit">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <button class="icon-btn" style="color:#94a3b8;" onclick='openPasswordModal(@json($user))' title="Reset Password">
                        <i class="fas fa-key"></i>
                    </button>
                    @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('users.toggle-active', $user) }}" style="display:inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="icon-btn toggle" title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                <i class="fas fa-{{ $user->is_active ? 'ban' : 'check-circle' }}"></i>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('users.destroy', $user) }}" style="display:inline;" onsubmit="return confirmDelete(this)">
                            @csrf @method('DELETE')
                            <button type="submit" class="icon-btn delete" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>No users found.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px;">{{ $users->links() }}</div>

{{-- Create Modal --}}
<div id="createModal" style="display:none;" class="modal-overlay" onclick="if(event.target===this)closeModal('createModal')">
    <div class="modal-box">
        <div class="modal-head">
            <h3>Add New User</h3>
            <button class="modal-close" onclick="closeModal('createModal')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" class="form-control" required placeholder="John Doe">
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" class="form-control" required placeholder="user@example.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Role *</label>
                    <select name="role" class="form-control">
                        <option value="staff">Staff</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password *</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn-secondary-sm" onclick="closeModal('createModal')">Cancel</button>
                <button type="submit" class="btn-primary-sm"><i class="fas fa-check"></i> Create</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" style="display:none;" class="modal-overlay" onclick="if(event.target===this)closeModal('editModal')">
    <div class="modal-box">
        <div class="modal-head">
            <h3>Edit User</h3>
            <button class="modal-close" onclick="closeModal('editModal')"><i class="fas fa-times"></i></button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" id="editName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" id="editEmail" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Role *</label>
                    <select name="role" id="editRole" class="form-control">
                        <option value="staff">Staff</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn-secondary-sm" onclick="closeModal('editModal')">Cancel</button>
                <button type="submit" class="btn-primary-sm"><i class="fas fa-check"></i> Update</button>
            </div>
        </form>
    </div>
</div>

{{-- Password Reset Modal --}}
<div id="passwordModal" style="display:none;" class="modal-overlay" onclick="if(event.target===this)closeModal('passwordModal')">
    <div class="modal-box">
        <div class="modal-head">
            <h3>Reset Password</h3>
            <button class="modal-close" onclick="closeModal('passwordModal')"><i class="fas fa-times"></i></button>
        </div>
        <form id="passwordForm" method="POST">
            @csrf
            <input type="hidden" name="_method" value="PATCH">
            <div class="modal-body">
                <p id="passwordUserLabel" style="font-size:0.82rem;color:#64748b;margin-bottom:14px;"></p>
                <div class="form-group">
                    <label class="form-label">New Password *</label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password *</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn-secondary-sm" onclick="closeModal('passwordModal')">Cancel</button>
                <button type="submit" class="btn-danger-sm"><i class="fas fa-key"></i> Reset</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateModal() {
    document.getElementById('createModal').style.display = 'flex';
}
function openEditModal(user) {
    document.getElementById('editForm').action = '/users/' + user.id;
    document.getElementById('editName').value = user.name || '';
    document.getElementById('editEmail').value = user.email || '';
    document.getElementById('editRole').value = user.role || 'staff';
    document.getElementById('editModal').style.display = 'flex';
}
function openPasswordModal(user) {
    document.getElementById('passwordForm').action = '/users/' + user.id + '/reset-password';
    document.getElementById('passwordUserLabel').textContent = 'Resetting password for: ' + user.name;
    document.getElementById('passwordModal').style.display = 'flex';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}
</script>
@endsection
