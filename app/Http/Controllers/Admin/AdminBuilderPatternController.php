<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuilderPattern;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminBuilderPatternController extends Controller
{
    public function index()
    {
        $patterns = BuilderPattern::latest()->get();

        return view('admin.builder_patterns.index', compact('patterns'));
    }

    public function create()
    {
        return view('admin.builder_patterns.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'pattern_file' => 'required|image|max:4096',
            'status' => 'boolean',
        ]);

        $data = $request->except(['pattern_file', 'status']);
        $data['status'] = $request->has('status');

        if ($request->hasFile('pattern_file')) {
            $data['image_path'] = '/storage/'.$request->file('pattern_file')->store('builder_patterns', 'public');
        }

        BuilderPattern::create($data);

        return redirect()->route('admin.builder-patterns.index')->with('success', 'Builder Pattern created successfully.');
    }

    public function edit(BuilderPattern $builderPattern)
    {
        return view('admin.builder_patterns.edit', compact('builderPattern'));
    }

    public function update(Request $request, BuilderPattern $builderPattern)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'pattern_file' => 'nullable|image|max:4096',
            'status' => 'boolean',
        ]);

        $data = $request->except(['pattern_file', 'status']);
        $data['status'] = $request->has('status');

        if ($request->hasFile('pattern_file')) {
            // Delete old pattern image if it exists
            if ($builderPattern->image_path) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $builderPattern->image_path));
            }
            $data['image_path'] = '/storage/'.$request->file('pattern_file')->store('builder_patterns', 'public');
        }

        $builderPattern->update($data);

        return redirect()->route('admin.builder-patterns.index')->with('success', 'Builder Pattern updated successfully.');
    }

    public function destroy(BuilderPattern $builderPattern)
    {
        if ($builderPattern->image_path) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $builderPattern->image_path));
        }
        $builderPattern->delete();

        return redirect()->route('admin.builder-patterns.index')->with('success', 'Builder Pattern deleted successfully.');
    }
}
