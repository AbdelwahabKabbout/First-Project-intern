<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\log;


class logController extends Controller
{
    public function index()
    {
        $logs = log::orderBy('created_at', 'desc');
        return view('logs.index', compact('logs'));
    }

    public function create()
    {
        return view('logs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|string|max:255',
            'details' => 'nullable|string|max:1000',
        ]);

        log::create($validated);
    }
}
