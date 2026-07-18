@php use Carbon\Carbon; @endphp
@extends('admin.master')

@section('title', 'Builder Patterns')

@section('header', 'Builder Patterns Management')

@section('content')
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <h5 class="card-title mb-0">Patterns List</h5>
                <div class="d-flex flex-column flex-md-row gap-2">
                    <a href="{{ route('admin.builder-patterns.create') }}" class="btn btn-primary text-nowrap">
                        <i class="bi bi-plus-circle me-1"></i> Add New Pattern
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Preview</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patterns as $pattern)
                            <tr>
                                <td class="ps-4 fw-medium">#{{ $pattern->id }}</td>
                                <td>
                                    <div class="border rounded-2 bg-light d-flex align-items-center justify-content-center overflow-hidden" style="width: 50px; height: 50px;">
                                        @if($pattern->image_path)
                                            <img src="{{ asset($pattern->image_path) }}" alt="{{ $pattern->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                        @else
                                            <span class="text-muted small">No Image</span>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $pattern->name }}</td>
                                <td>
                                    @if($pattern->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="small">
                                    {{ Carbon::parse($pattern->created_at)->isoFormat('LLL') }}
                                </td>
                                <td>
                                    <div class="d-flex flex-column flex-md-row justify-content-end gap-2">
                                        <a href="{{ route('admin.builder-patterns.edit', $pattern->id) }}"
                                            class="btn btn-sm btn-outline-primary px-3 text-nowrap" title="Edit">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.builder-patterns.destroy', $pattern->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this pattern?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-danger px-3 w-100 text-nowrap" title="Delete">
                                                <i class="bi bi-trash-fill"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No builder patterns found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection
