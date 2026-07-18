@extends('admin.master')

@section('title', 'Customer Registration Applications')

@section('header', 'Retail Customer Applications')

@section('content')
    <!-- Filter Tabs -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="btn-group" role="group">
            <a href="{{ route('admin.customer-applications.index', ['status' => 'pending']) }}" 
               class="btn btn-sm {{ $status === 'pending' ? 'btn-primary' : 'btn-outline-secondary' }} px-4 py-2">
                Pending Reviews
            </a>
            <a href="{{ route('admin.customer-applications.index', ['status' => 'active']) }}" 
               class="btn btn-sm {{ $status === 'active' ? 'btn-primary' : 'btn-outline-secondary' }} px-4 py-2">
                Approved Customers
            </a>
            <a href="{{ route('admin.customer-applications.index', ['status' => 'rejected']) }}" 
               class="btn btn-sm {{ $status === 'rejected' ? 'btn-primary' : 'btn-outline-secondary' }} px-4 py-2">
                Rejected Applications
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Applications Log Card -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                      <th class="ps-4 py-3">Customer Name</th>
                      <th class="py-3">Email Address</th>
                      <th class="py-3">Status</th>
                      <th class="py-3">Registration Date</th>
                      <th class="text-end pe-4 py-3">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($applications as $app)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-dark">{{ $app->name }} {{ $app->last_name }}</div>
                            </td>
                            <td>
                                <div class="text-dark small">{{ $app->email }}</div>
                            </td>
                            <td>
                                @if($app->status === 'pending')
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2 text-uppercase font-bold" style="font-size: 10px;">Pending</span>
                                @elseif($app->status === 'active')
                                    <span class="badge bg-success text-white rounded-pill px-3 py-2 text-uppercase font-bold" style="font-size: 10px;">Approved</span>
                                @else
                                    <span class="badge bg-danger text-white rounded-pill px-3 py-2 text-uppercase font-bold" style="font-size: 10px;">Rejected</span>
                                @endif
                            </td>
                            <td class="text-secondary small">
                                {{ $app->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.customer-applications.show', $app->id) }}" 
                                   class="btn btn-sm btn-light border fw-semibold">
                                    Review Application <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-2 d-block mb-3 text-secondary opacity-50"></i>
                                <span class="fw-bold">No customer registration applications found matching status "{{ $status }}".</span>
                            </td>
                        </tr>
                    @endforelse
                  </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $applications->links() }}
    </div>
@endsection
