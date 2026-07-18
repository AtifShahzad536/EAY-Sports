<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuilderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminBuilderLogoController extends Controller
{
    public function index()
    {
        $logos = BuilderLogo::latest()->get();

        return view('admin.builder_logos.index', compact('logos'));
    }

    public function create()
    {
        return view('admin.builder_logos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'logo_file' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:4096',
            'status' => 'boolean',
        ]);

        $data = $request->except(['logo_file', 'status']);
        $data['status'] = $request->has('status');

        if ($request->hasFile('logo_file')) {
            $data['image_path'] = '/storage/'.$request->file('logo_file')->store('builder_logos', 'public');
        }

        BuilderLogo::create($data);

        return redirect()->route('admin.builder-logos.index')->with('success', 'Builder Logo created successfully.');
    }

    public function edit(BuilderLogo $builderLogo)
    {
        return view('admin.builder_logos.edit', compact('builderLogo'));
    }

    public function update(Request $request, BuilderLogo $builderLogo)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'logo_file' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:4096',
            'status' => 'boolean',
        ]);

        $data = $request->except(['logo_file', 'status']);
        $data['status'] = $request->has('status');

        if ($request->hasFile('logo_file')) {
            if ($builderLogo->image_path) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $builderLogo->image_path));
            }
            $data['image_path'] = '/storage/'.$request->file('logo_file')->store('builder_logos', 'public');
        }

        $builderLogo->update($data);

        return redirect()->route('admin.builder-logos.index')->with('success', 'Builder Logo updated successfully.');
    }

    public function destroy(BuilderLogo $builderLogo)
    {
        if ($builderLogo->image_path) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $builderLogo->image_path));
        }
        $builderLogo->delete();

        return redirect()->route('admin.builder-logos.index')->with('success', 'Builder Logo deleted successfully.');
    }
}
