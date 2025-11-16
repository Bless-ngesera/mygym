<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Instructor;
use Illuminate\Support\Facades\Storage;

class InstructorController extends Controller
{
    public function index()
    {
        $items = Instructor::latest()->paginate(20);
        return view('admin.instructors.index', compact('items'));
    }

    public function create()
    {
        return view('admin.instructors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:instructors,email',
            'phone'=>'nullable|string',
            'specialty'=>'nullable|string',
            'experience'=>'nullable|integer|min:0',
            'photo'=>'nullable|image|max:2048',
        ]);
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('instructors','public');
            $data['photo'] = $path;
        }
        Instructor::create($data);
        return redirect()->route('admin.instructors.index')->with('success','Instructor registered');
    }

    public function show(Instructor $instructor)
    {
        return view('admin.instructors.show', compact('instructor'));
    }

    public function edit(Instructor $instructor)
    {
        return view('admin.instructors.edit', compact('instructor'));
    }

    public function update(Request $request, Instructor $instructor)
    {
        $data = $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:instructors,email,'.$instructor->id,
            'phone'=>'nullable|string',
            'specialty'=>'nullable|string',
            'experience'=>'nullable|integer|min:0',
            'photo'=>'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('instructors','public');
            $data['photo'] = $path;
            if ($instructor->photo) {
                Storage::disk('public')->delete($instructor->photo);
            }
        }

        $instructor->update($data);
    }

    public function destroy(Instructor $instructor)
    {
        if ($instructor->photo) {
            Storage::disk('public')->delete($instructor->photo);
        }
        $instructor->delete();
        return redirect()->route('admin.instructors.index')->with('success','Deleted');
    }
}
