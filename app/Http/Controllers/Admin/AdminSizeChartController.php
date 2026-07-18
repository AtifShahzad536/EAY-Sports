<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SizeChart;
use App\Traits\FlashNotifications;
use App\Traits\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminSizeChartController extends Controller
{
    use FlashNotifications, ImageOptimizer;

    /**
     * Display a listing of size charts.
     */
    public function index()
    {
        $sizeCharts = SizeChart::orderBy('sort_order', 'asc')->get();

        return view('admin.size_charts.index', compact('sizeCharts'));
    }

    /**
     * Show the form for creating a new size chart.
     */
    public function create()
    {
        return view('admin.size_charts.create');
    }

    /**
     * Store a newly created size chart in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:size_charts,slug',
            'image_file' => 'required|image|max:10240', // Max 10MB
            'sort_order' => 'required|integer',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);

        // Ensure slug is unique
        $originalSlug = $slug;
        $count = 1;
        while (SizeChart::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        $imagePath = $this->optimizeAndSave($request->file('image_file'), 'size_charts', true);

        SizeChart::create([
            'name' => $request->name,
            'slug' => $slug,
            'image_path' => $imagePath,
            'sort_order' => $request->sort_order,
        ]);

        $this->successNotification('Size Chart created successfully.');

        return redirect()->route('admin.size-charts.index');
    }

    /**
     * Show the form for editing the specified size chart.
     */
    public function edit($id)
    {
        $sizeChart = SizeChart::findOrFail($id);

        return view('admin.size_charts.edit', compact('sizeChart'));
    }

    /**
     * Update the specified size chart in storage.
     */
    public function update(Request $request, $id)
    {
        $sizeChart = SizeChart::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:size_charts,slug,'.$sizeChart->id,
            'image_file' => 'nullable|image|max:10240',
            'sort_order' => 'required|integer',
        ]);

        $slug = Str::slug($request->slug);

        $data = [
            'name' => $request->name,
            'slug' => $slug,
            'sort_order' => $request->sort_order,
        ];

        if ($request->hasFile('image_file')) {
            // Delete old image
            if ($sizeChart->image_path) {
                Storage::disk('public')->delete($sizeChart->image_path);
            }

            $data['image_path'] = $this->optimizeAndSave($request->file('image_file'), 'size_charts', true);
        }

        $sizeChart->update($data);

        $this->successNotification('Size Chart updated successfully.');

        return redirect()->route('admin.size-charts.index');
    }

    /**
     * Remove the specified size chart from storage.
     */
    public function destroy($id)
    {
        $sizeChart = SizeChart::findOrFail($id);

        if ($sizeChart->image_path) {
            Storage::disk('public')->delete($sizeChart->image_path);
        }

        $sizeChart->delete();

        $this->successNotification('Size Chart deleted successfully.');

        return redirect()->route('admin.size-charts.index');
    }
}
