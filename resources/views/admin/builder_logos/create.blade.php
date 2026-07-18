@extends('admin.master')

@section('title', 'Add Builder Logo')

@section('header', 'Add Builder Logo')

@section('content')
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form action="{{ route('admin.builder-logos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">Logo Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Eagle, USA Flag, Wolf Head" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Category (Optional)</label>
                    <input type="text" name="category" class="form-control" placeholder="e.g. Flags & Symbols, Birds, Wolves & Dogs">
                    <div class="form-text text-muted">Group logos in the builder sidebar. Leave blank for a general category.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Logo Image File (PNG, JPG, SVG, WebP)</label>
                    <input type="file" name="logo_file" class="form-control" accept="image/*" required>
                    <div class="form-text text-muted">Upload a high-quality logo image. PNG or SVG with transparent background is recommended.</div>
                </div>

                <div class="mb-4 form-check form-switch">
                    <input type="checkbox" name="status" value="1" class="form-check-input" id="statusCheck" checked>
                    <label class="form-check-label fw-bold" for="statusCheck">Active (Visible in Builder)</label>
                </div>

                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> Save Logo
                </button>
                <a href="{{ route('admin.builder-logos.index') }}" class="btn btn-secondary px-4">Cancel</a>
            </form>
        </div>
    </div>
@endsection
