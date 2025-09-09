<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $allUsers = User::with('permissions')->get(); // Load permissions with users
        $allPermissions = Permission::all();

        return view('guestbook.manageUser', compact('allUsers', 'allPermissions'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'user' => 'required|exists:users,id',
            'permissions' => 'array', // can be empty
            'permissions.*' => 'exists:permissions,id',
        ]);

        $userId = $request->user;
        $selectedPermissions = $request->permissions ?? [];

        try {
            // First, delete all existing permissions for this user
            DB::table('user_permissions')->where('user_id', $userId)->delete();

            // If permissions are selected, insert them
            if (!empty($selectedPermissions)) {
                $insertData = [];
                foreach ($selectedPermissions as $permissionId) {
                    $insertData[] = [
                        'user_id' => $userId,
                        'permission_id' => $permissionId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                DB::table('user_permissions')->insert($insertData);
            }

            $userName = User::find($userId)->name;
            return redirect()->route('guestbook.manageUser')->with('success', "Permissions updated successfully for {$userName}!");
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating permissions: ' . $e->getMessage());
        }
    }
}
