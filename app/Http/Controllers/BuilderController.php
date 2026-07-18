<?php

namespace App\Http\Controllers;

use App\Models\BuilderLogo;
use App\Models\BuilderModel;
use App\Models\BuilderPattern;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BuilderController extends Controller
{
    /**
     * Show the custom 3D jersey builder page with loaded configurations.
     */
    public function index(Request $request, $id = null)
    {
        $productId = $request->query('product_id');
        $categoryId = $request->query('category_id');

        $query = BuilderModel::where('status', true);

        if ($productId) {
            $product = Product::with('categories')->find($productId);
            if ($product) {
                $categoryIds = $product->categories->pluck('id');
                $query->whereIn('category_id', $categoryIds);
            }
        } elseif ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $defaultMapping = [
            'Body' => 'primary',
            'Front' => 'primary',
            'Back' => 'primary',
            'R_Sleeve' => 'secondary',
            'L_Sleeve' => 'secondary',
            'Neck' => 'third',
            'Mesh' => 'third',
        ];

        if ($id) {
            $models = $query->get()->map(function ($model) use ($defaultMapping) {
                $mapping = is_array($model->mapping) && ! empty($model->mapping) ? $model->mapping : $defaultMapping;

                return [
                    'id' => 'M'.$model->id,
                    'name' => strtoupper($model->name),
                    'modelUrl' => $model->model_url,
                    'thumbnail' => $model->thumbnail,
                    'mapping' => $mapping,
                    'layers_metadata' => $model->layers_metadata ?? (object) [],
                ];
            });
            $pagination = null;
        } else {
            // Paginate with 6 models per page for the landing page grid
            $paginator = $query->paginate(6);
            $models = collect($paginator->items())->map(function ($model) use ($defaultMapping) {
                $mapping = is_array($model->mapping) && ! empty($model->mapping) ? $model->mapping : $defaultMapping;

                return [
                    'id' => 'M'.$model->id,
                    'name' => strtoupper($model->name),
                    'modelUrl' => $model->model_url,
                    'thumbnail' => $model->thumbnail,
                    'mapping' => $mapping,
                    'layers_metadata' => $model->layers_metadata ?? (object) [],
                ];
            });
            $pagination = [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ];
        }

        $defaultPatterns = BuilderPattern::where('status', true)->latest()->get()->map(function ($pattern) {
            return [
                'id' => $pattern->id,
                'name' => $pattern->name,
                'imageUrl' => asset($pattern->image_path),
            ];
        });

        $defaultLogos = BuilderLogo::where('status', true)->latest()->get()->map(function ($logo) {
            return [
                'id' => $logo->id,
                'name' => $logo->name,
                'category' => $logo->category ?: 'MISC. LOGOS',
                'imageUrl' => asset($logo->image_path),
            ];
        });

        return Inertia::render('BuilderPage', [
            'id' => $id,
            'dynamicDesigns' => $models,
            'defaultPatterns' => $defaultPatterns,
            'defaultLogos' => $defaultLogos,
            'pagination' => $pagination,
        ]);
    }
}
