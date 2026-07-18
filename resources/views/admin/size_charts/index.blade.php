@extends('admin.master')

@section('title', 'Size Charts')

@section('header', 'Size Charts Management')

@section('content')
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Size Charts</h5>
                <a href="{{ route('admin.size-charts.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Add New Size Chart
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Image</th>
                            <th>Sort Order</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sizeCharts as $sizeChart)
                            <tr>
                                <td class="ps-4 fw-medium">#{{ $sizeChart->id }}</td>
                                <td class="fw-bold text-slate-800">{{ $sizeChart->name }}</td>
                                <td><code>{{ $sizeChart->slug }}</code></td>
                                <td>
                                    @if ($sizeChart->image_path)
                                        <a href="{{ asset('storage/' . $sizeChart->image_path) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $sizeChart->image_path) }}" alt="{{ $sizeChart->name }}" style="height: 40px; width: auto; object-fit: contain;" class="rounded border">
                                        </a>
                                    @else
                                        <span class="text-muted small">No image uploaded</span>
                                    @endif
                                </td>
                                <td>{{ $sizeChart->sort_order }}</td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2 pe-4">
                                        <a href="{{ route('admin.size-charts.edit', $sizeChart->id) }}"
                                            class="btn btn-sm btn-outline-primary px-3" title="Edit">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.size-charts.destroy', $sizeChart->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this size chart?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-3" title="Delete">
                                                <i class="bi bi-trash-fill"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No size charts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection
