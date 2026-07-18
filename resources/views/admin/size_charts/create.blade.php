@extends('admin.master')

@section('title', 'Create Size Chart')

@section('header', 'Create Size Chart')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-8 col-xl-12 col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-bold">New Size Chart</h5>
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

                    <form action="{{ route('admin.size-charts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

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
                                       value="{{ old('name') }}"
                                       placeholder="e.g. Adult Men's"
                                       required>
                                <small class="text-muted">The name shown on the tab (e.g. Adult Men's, Women's, Youth, etc.).</small>
                            </div>

                            <!-- Slug (optional) -->
                            <div class="col-md-12">
                                <label for="slug" class="form-label fw-bold">
                                    Slug / Identifier (Optional)
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="slug"
                                       name="slug"
                                       value="{{ old('slug') }}"
                                       placeholder="e.g. mens">
                                <small class="text-muted">Unique identifier used internally (e.g. mens, womens). Will be generated from Name if left empty.</small>
                            </div>

                            <!-- Image File -->
                            <div class="col-md-12">
                                <label for="image_file" class="form-label fw-bold">
                                    Size Chart Image <span class="text-danger">*</span>
                                </label>
                                <input type="file"
                                       class="form-control"
                                       id="image_file"
                                       name="image_file"
                                       accept="image/*"
                                       required>
                                <small class="text-muted">Upload size chart image (JPEG, PNG, WebP etc.).</small>
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
                                       value="{{ old('sort_order', '0') }}"
                                       required>
                                <small class="text-muted">Lower values appear first in tabs.</small>
                            </div>

                            <!-- Submit -->
                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="bi bi-plus-circle me-1"></i> Create Size Chart
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
