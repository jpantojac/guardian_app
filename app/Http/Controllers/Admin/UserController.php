<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        
        $users = $query->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,moderator,analyst,user',
            'is_active' => 'required|boolean'
        ]);

        // Prevent admin from deactivating themselves
        if ($user->id === auth()->id() && !$request->is_active) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        // Prevent admin from changing their own role to lower one
        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->with('error', 'No puedes quitarte el rol de administrador.');
        }

        $user->update([
            'role' => $request->role,
            'is_active' => $request->is_active
        ]);

        return back()->with('success', 'Usuario actualizado correctamente.');
    }
}
