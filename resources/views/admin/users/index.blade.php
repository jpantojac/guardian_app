@extends('admin.layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Gestión de Usuarios</h1>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 border-b">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex gap-2">
            <input type="text" name="search" placeholder="Buscar por nombre o correo..." value="{{ request('search') }}" class="px-3 py-2 border rounded-md w-full max-w-sm">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Buscar</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 border-b">
                    <th class="p-4 font-semibold">Usuario</th>
                    <th class="p-4 font-semibold">Correo</th>
                    <th class="p-4 font-semibold">Rol</th>
                    <th class="p-4 font-semibold">Estado</th>
                    <th class="p-4 font-semibold">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-4 font-medium text-gray-800">{{ $user->name }}</td>
                    <td class="p-4 text-gray-600">{{ $user->email }}</td>
                    <td class="p-4">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                            {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : ($user->role === 'moderator' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="p-4">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="p-4">
                        <button onclick="openEditModal({{ $user->id }}, '{{ $user->role }}', {{ $user->is_active ? 'true' : 'false' }})" class="text-indigo-600 hover:text-indigo-900 font-medium">Editar</button>
                        
                        <!-- Edit Modal form (hidden per user) -->
                        <form id="edit-form-{{ $user->id }}" action="{{ route('admin.users.update', $user) }}" method="POST" class="hidden">
                            @csrf
                            @method('PUT')
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4">
        {{ $users->links() }}
    </div>
</div>

<!-- Global Edit Modal -->
<div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Editar Usuario</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Rol</label>
                <select id="modal-role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm px-3 py-2 border">
                    <option value="admin">Admin</option>
                    <option value="moderator">Moderator</option>
                    <option value="analyst">Analyst</option>
                    <option value="user">User</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Estado</label>
                <select id="modal-status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm px-3 py-2 border">
                    <option value="1">Activo</option>
                    <option value="0">Desactivado</option>
                </select>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <button onclick="closeModal()" class="px-4 py-2 border rounded-md text-gray-600 hover:bg-gray-50">Cancelar</button>
            <button onclick="submitEdit()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Guardar</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentUserId = null;

    function openEditModal(id, role, isActive) {
        currentUserId = id;
        document.getElementById('modal-role').value = role;
        document.getElementById('modal-status').value = isActive ? '1' : '0';
        document.getElementById('edit-modal').classList.remove('hidden');
        document.getElementById('edit-modal').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('edit-modal').classList.add('hidden');
        document.getElementById('edit-modal').classList.remove('flex');
        currentUserId = null;
    }

    function submitEdit() {
        if (!currentUserId) return;
        
        const form = document.getElementById('edit-form-' + currentUserId);
        const role = document.getElementById('modal-role').value;
        const status = document.getElementById('modal-status').value;

        // Append hidden inputs
        const roleInput = document.createElement('input');
        roleInput.type = 'hidden';
        roleInput.name = 'role';
        roleInput.value = role;
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'is_active';
        statusInput.value = status;

        form.appendChild(roleInput);
        form.appendChild(statusInput);
        form.submit();
    }
</script>
@endpush
@endsection
