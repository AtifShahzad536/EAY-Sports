<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuilderModel;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminBuilderModelController extends Controller
{
    public function index()
    {
        $models = BuilderModel::with('category')->latest()->get();

        return view('admin.builder_models.index', compact('models'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('admin.builder_models.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'boolean',
        ]);

        $data = $request->except(['model_file', 'status']);
        $data['status'] = $request->has('status');
        $mapping = [];
        if ($request->has('mapping_keys') && $request->has('mapping_values')) {
            $keys = $request->input('mapping_keys');
            $values = $request->input('mapping_values');
            foreach ($keys as $index => $key) {
                if (! empty($key) && ! empty($values[$index])) {
                    $mapping[$key] = $values[$index];
                }
            }
        }
        $data['mapping'] = $mapping;

        $layersMetadata = [];
        if ($request->has('layers')) {
            foreach ($request->input('layers') as $meshName => $layerData) {
                $layersMetadata[$meshName] = [
                    'display_name' => $layerData['display_name'] ?? $meshName,
                    'is_locked' => isset($layerData['is_locked']) && ($layerData['is_locked'] == '1' || $layerData['is_locked'] == 'on'),
                    'show_lock' => isset($layerData['show_lock']) && ($layerData['show_lock'] == '1' || $layerData['show_lock'] == 'on'),
                    'merge_parent' => ! empty($layerData['merge_parent']) ? $layerData['merge_parent'] : null,
                ];
            }
        }
        $data['layers_metadata'] = $layersMetadata;

        if ($request->hasFile('model_file')) {
            $data['model_url'] = '/storage/'.$request->file('model_file')->store('builder_models', 'public');
        }

        BuilderModel::create($data);

        return redirect()->route('admin.builder-models.index')->with('success', 'Builder Model created successfully.');
    }

    public function edit(BuilderModel $builderModel)
    {
        $categories = Category::all();

        return view('admin.builder_models.edit', compact('builderModel', 'categories'));
    }

    public function update(Request $request, BuilderModel $builderModel)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'model_file' => 'nullable|file',
        ]);

        $data = $request->except(['model_file', 'status']);
        $data['status'] = $request->has('status');

        $layersMetadata = [];
        if ($request->has('layers')) {
            foreach ($request->input('layers') as $meshName => $layerData) {
                $layersMetadata[$meshName] = [
                    'display_name' => $layerData['display_name'] ?? $meshName,
                    'is_locked' => isset($layerData['is_locked']) && ($layerData['is_locked'] == '1' || $layerData['is_locked'] == 'on'),
                    'show_lock' => isset($layerData['show_lock']) && ($layerData['show_lock'] == '1' || $layerData['show_lock'] == 'on'),
                    'merge_parent' => ! empty($layerData['merge_parent']) ? $layerData['merge_parent'] : null,
                ];
            }
        }
        $data['layers_metadata'] = $layersMetadata;

        if ($request->hasFile('model_file')) {
            if ($builderModel->model_url) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $builderModel->model_url));
            }
            $data['model_url'] = '/storage/'.$request->file('model_file')->store('builder_models', 'public');
        }

        $builderModel->update($data);

        return redirect()->route('admin.builder-models.index')->with('success', 'Builder Model updated successfully.');
    }

    public function destroy(BuilderModel $builderModel)
    {
        if ($builderModel->model_url) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $builderModel->model_url));
        }
        if ($builderModel->thumbnail) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $builderModel->thumbnail));
        }
        $builderModel->delete();

        return redirect()->route('admin.builder-models.index')->with('success', 'Builder Model deleted successfully.');
    }
}
