<?php

use App\Models\User;
use App\Models\Permission;

$themeData = view('components.themeToggle')->render();
$currentTheme = config('theme.current', 'light');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage Users</title>

    @php
    $currentTheme = $_SESSION['theme'] ?? 'light';
    @endphp

    @if($currentTheme === 'dark')
    @vite('resources/css/Dark.css')
    @else
    @vite('resources/css/Light.css')
    @endif
</head>

<body>
    <h1>User Management</h1>
    <a href="{{ route('guestbook.index') }}"><---- Back to Guestbook</a>

            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('users.permissions.update') }}" method="POST">
                @csrf

                <h2>Permissions</h2>
                <div class="permissions-container">
                    @php
                    $selectedUser = $allUsers->firstWhere('id', request('user'));
                    @endphp

                    @foreach ($allPermissions as $permission)
                    <div class="permission-container">
                        <label>
                            <input type="checkbox"
                                name="permissions[]"
                                value="{{ $permission->id }}">
                            {{ $permission->id }} - {{ $permission->description }}
                        </label>
                    </div>
                    @endforeach
                </div>

                <h2>Users</h2>
                <select name="user" id="userSelect" onchange="this.form.submit()">
                    <option value="">-- Select a User --</option>
                    @foreach ($allUsers as $user)
                    <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                    @endforeach
                </select>

            </form>

</body>

</html>