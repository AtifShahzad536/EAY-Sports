@extends('admin.master')

@section('title', 'Edit Builder Pattern')

@section('header', 'Edit Builder Pattern')

@section('content')
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form action="{{ route('admin.builder-patterns.update', $builderPattern->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-bold">Pattern Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $builderPattern->name }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold d-block">Current Pattern Image</label>
                    <div class="border rounded-3 p-3 bg-light d-inline-block mb-2" style="max-width: 200px;">
                        @if($builderPattern->image_path)
                            <img src="{{ asset($builderPattern->image_path) }}" alt="{{ $builderPattern->name }}" class="img-fluid rounded-2" style="max-height: 150px; object-fit: contain;">
                        @else
                            <span class="text-muted">No Image</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Replace Pattern Image (Optional)</label>
                    <input type="file" name="pattern_file" class="form-control" accept="image/*">
                    <div class="form-text text-muted">Leave empty to keep current image. Uploading a new image will replace the existing one.</div>
                </div>

                <div class="mb-4 form-check form-switch">
                    <input type="checkbox" name="status" value="1" class="form-check-input" id="statusCheck" {{ $builderPattern->status ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="statusCheck">Active (Visible in Builder)</label>
                </div>

                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> Update Pattern
                </button>
                <a href="{{ route('admin.builder-patterns.index') }}" class="btn btn-secondary px-4">Cancel</a>
            </form>
        </div>
    </div>
@endsection
