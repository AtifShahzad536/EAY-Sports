@extends('admin.master')

@section('title', 'Edit Builder Logo')

@section('header', 'Edit Builder Logo')

@section('content')
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form action="{{ route('admin.builder-logos.update', $builderLogo->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-bold">Logo Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $builderLogo->name }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Category (Optional)</label>
                    <input type="text" name="category" class="form-control" value="{{ $builderLogo->category }}" placeholder="e.g. Flags & Symbols, Birds, Wolves & Dogs">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold d-block">Current Logo Image</label>
                    <div class="border rounded-3 p-3 bg-light d-inline-block mb-2" style="max-width: 200px;">
                        @if($builderLogo->image_path)
                            <img src="{{ asset($builderLogo->image_path) }}" alt="{{ $builderLogo->name }}" class="img-fluid rounded-2" style="max-height: 150px; object-fit: contain;">
                        @else
                            <span class="text-muted">No Image</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Replace Logo Image (Optional)</label>
                    <input type="file" name="logo_file" class="form-control" accept="image/*">
                    <div class="form-text text-muted">Leave empty to keep current image. Uploading a new image will replace the existing one.</div>
                </div>

                <div class="mb-4 form-check form-switch">
                    <input type="checkbox" name="status" value="1" class="form-check-input" id="statusCheck" {{ $builderLogo->status ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="statusCheck">Active (Visible in Builder)</label>
                </div>

                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> Update Logo
                </button>
                <a href="{{ route('admin.builder-logos.index') }}" class="btn btn-secondary px-4">Cancel</a>
            </form>
        </div>
    </div>
@endsection
