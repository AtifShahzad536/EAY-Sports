@extends('admin.master')

@section('title', 'Add Builder Pattern')

@section('header', 'Add Builder Pattern')

@section('content')
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form action="{{ route('admin.builder-patterns.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">Pattern Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Camouflage, Vertical Stripes" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Pattern Image File (PNG, JPG, SVG, WebP)</label>
                    <input type="file" name="pattern_file" class="form-control" accept="image/*" required>
                    <div class="form-text text-muted">Upload a high-quality pattern image. PNG or SVG with transparent background is recommended for custom color overlays.</div>
                </div>

                <div class="mb-4 form-check form-switch">
                    <input type="checkbox" name="status" value="1" class="form-check-input" id="statusCheck" checked>
                    <label class="form-check-label fw-bold" for="statusCheck">Active (Visible in Builder)</label>
                </div>

                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> Save Pattern
                </button>
                <a href="{{ route('admin.builder-patterns.index') }}" class="btn btn-secondary px-4">Cancel</a>
            </form>
        </div>
    </div>
@endsection
