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
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $userId = $request->user;
        $selectedPermissions = $request->permissions ?? [];


        DB::table('user_permissions')->where('user_id', $userId)->delete();


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
    }
}
