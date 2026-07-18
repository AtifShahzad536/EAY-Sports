@extends('admin.master')

@section('title', 'Edit Size Chart')

@section('header', 'Edit Size Chart')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-8 col-xl-12 col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-bold">Edit Size Chart: {{ $sizeChart->name }}</h5>
                        <a href="{{ route('admin.size-charts.index') }}"
                           class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </a>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.size-charts.update', $sizeChart->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">

                            <!-- Name -->
                            <div class="col-md-12">
                                <label for="name" class="form-label fw-bold">
                                    Name / Label <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $sizeChart->name) }}"
                                       required>
                                <small class="text-muted">The name shown on the tab (e.g. Adult Men's, Women's, Youth, etc.).</small>
                            </div>

                            <!-- Slug -->
                            <div class="col-md-12">
                                <label for="slug" class="form-label fw-bold">
                                    Slug / Identifier <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="slug"
                                       name="slug"
                                       value="{{ old('slug', $sizeChart->slug) }}"
                                       required>
                                <small class="text-muted">Unique identifier used internally (e.g. mens, womens).</small>
                            </div>

                            <!-- Current Image -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold d-block">Current Image</label>
                                @if($sizeChart->image_path)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $sizeChart->image_path) }}" alt="{{ $sizeChart->name }}" style="max-height: 150px; width: auto; object-fit: contain;" class="rounded border p-1 bg-light">
                                    </div>
                                @else
                                    <span class="text-muted small">No image uploaded</span>
                                @endif
                            </div>

                            <!-- Image File -->
                            <div class="col-md-12">
                                <label for="image_file" class="form-label fw-bold">
                                    Replace Image (Optional)
                                </label>
                                <input type="file"
                                       class="form-control"
                                       id="image_file"
                                       name="image_file"
                                       accept="image/*">
                                <small class="text-muted">Upload a new image only if you want to replace the current one.</small>
                            </div>

                            <!-- Sort Order -->
                            <div class="col-md-6 mt-4">
                                <label for="sort_order" class="form-label fw-bold">
                                    Sort Order <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control"
                                       id="sort_order"
                                       name="sort_order"
                                       value="{{ old('sort_order', $sizeChart->sort_order) }}"
                                       required>
                                <small class="text-muted">Lower values appear first in tabs.</small>
                            </div>

                            <!-- Submit -->
                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="bi bi-check-circle me-1"></i> Update Size Chart
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
