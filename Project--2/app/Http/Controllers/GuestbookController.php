<?php

namespace App\Http\Controllers;

use App\Models\guestbook_Model;
use App\Models\GuestCategory;
use Illuminate\Http\Request;
use App\Models\log;

class GuestbookController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->query('name');
        $startDate = $request->query('startdate');
        $endDate = $request->query('enddate');
        $withPicture = $request->query('picture');
        $FillterCategory = $request->query('guest_category_id');
        $due_date = $request->query('due');
        $query = guestbook_Model::query();

        if ($name) {
            $query->where('name', 'like', '%' . $name . '%')->orWhere('tag', 'like', '%' . $name . '%');
        }

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]);
        } elseif ($startDate) {
            $query->where('created_at', '>=', $startDate . ' 00:00:00');
        } elseif ($endDate) {
            $query->where('created_at', '<=', $endDate . ' 23:59:59');
        }

        if ($withPicture) {
            $query->whereNotNull('image');
        }

        if ($FillterCategory) {
            $query->where('guest_category_id', $FillterCategory);
        }



        switch ($due_date) {
            case 'createdAt':
                $entries = $query->orderBy('created_at', 'desc')->paginate(10);
                break;
            case 'accending':
                $entries = $query->orderBy('due_date', 'asc')->paginate(10);
                break;
            case 'deccending':
                $entries = $query->orderBy('due_date', 'desc')->paginate(10);
                break;
            default:
                $entries = $query->orderBy('created_at', 'desc')->paginate(10);
                break;
        }



        $entries->appends($request->all());

        $categories = GuestCategory::all();

        log::create([
            'action' => 'Viewed Guestbook Entries',
            'details' => 'User viewed guestbook entries with filters: ' . json_encode($request->all())
        ]);

        return view('guestbook.index', compact('entries'), compact('categories'));
    }

    public function create()
    {
        return view('guestbook.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'max:255', 'regex:/@/'],
            'message' => 'required|string|max:1000',
            'tag' => 'string|max:1000|regex:/#/',
            'categoryid' => 'required|exists:guest_categories,id',
            'image' => 'nullable|mimes:png,jpg,jpeg,gif|image|max:2048',
            'due_date' => 'required|date',
            'rate' => 'required|in:happy,smile,neutral,sad,angry',
        ]);

        $validated['guest_category_id'] = $validated['categoryid'];
        unset($validated['categoryid']);

        if ($request->hasFile('image')) {
            $validated['image'] = file_get_contents($request->file('image')->getRealPath());
        }

        guestbook_Model::create($validated);

        log::create([
            'action' => 'Created Guestbook Entry',
            'details' => 'User ' . $validated['name'] . ' created a new guestbook entry.'
        ]);

        return redirect()->route('guestbook.index')->with('success', 'Your message has been posted!');
    }


    public function edit($id)
    {

        $entry = guestbook_Model::findOrFail($id);
        $categories = GuestCategory::whereNull('deleted_at')->orderBy('name')->get();

        return view('guestbook.edit', compact('entry'), compact('categories'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'max:255', 'regex:/@/'],
            'message' => 'required|string|max:1000',
            'tag' => 'string|max:1000|regex:/#/',
            'categoryid' => 'required|exists:guest_categories,id',
            'image' => 'nullable|image|max:2048|mimes:png,jpg,jpeg,gif',
            'due_date' => 'required|date',
            'rate' => 'required|in:happy,smile,neutral,sad,angry',
        ]);

        $validated['guest_category_id'] = $validated['categoryid'];
        unset($validated['categoryid']);

        $entry = guestbook_Model::findOrFail($id);

        if ($request->hasFile('image')) {
            $validated['image'] = file_get_contents($request->file('image')->getRealPath());
        }

        if ($request->has('clearImage')) {
            $entry->image = null;
        }

        log::create([
            'action' => 'Updated Guestbook Entry',
            'details' => 'User ' . $validated['name'] . ' updated guestbook entry ID ' . $id
        ]);

        $entry->update($validated);
        return redirect()->route('guestbook.index')->with('success', 'Entry updated successfully!');
    }

    public function SoftDelete($id)
    {
        $entry = guestbook_Model::findOrFail($id);
        $entry->delete();

        log::create([
            'action' => 'Deleted Guestbook Entry',
            'details' => 'User deleted guestbook entry ID ' . $id . '.'
        ]);

        return redirect()->route('guestbook.index')
            ->with('success', 'Entry deleted successfully!');
    }


    public function stats()
    {
        $entries = guestbook_Model::all();
        return view('guestbook.stats', compact('entries'));
    }

    public function manageUser()
    {
        return view('guestbook.manageUser');
    }
}
