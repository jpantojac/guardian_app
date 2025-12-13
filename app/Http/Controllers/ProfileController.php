<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'profile_photo' => ['nullable', 'image', 'max:2048'], // 2MB Max
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::delete($user->profile_photo_path);
            }
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        $user->name = $request->name;

        if ($request->filled('password')) {
            $user->password = $request->password;
        }

        $user->save();

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function incidents(Request $request)
    {
        $query = auth()->user()->incidents()->select('*', \Illuminate\Support\Facades\DB::raw('ST_Y(location) as latitude, ST_X(location) as longitude'))->with(['category', 'photos']);

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $incidents = $query->latest()->get();

        $categories = \App\Models\Category::select('id', 'name')->get();

        return response()->json([
            'incidents' => $incidents,
            'categories' => $categories
        ]);
    }
}
