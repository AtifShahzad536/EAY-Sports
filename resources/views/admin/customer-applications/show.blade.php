@extends('admin.master')

@section('title', 'Review Customer Application')

@section('header', 'Review Customer Registration Application')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.customer-applications.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-chevron-left me-1"></i> Back to Applications
        </a>
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

    <div class="row g-4">
        <!-- Main Application Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
                <h5 class="fw-bold border-bottom pb-3 mb-4 text-dark uppercase tracking-wide">Customer Profile</h5>
                
                <div class="row g-4">
                    <!-- Customer Name -->
                    <div class="col-md-6">
                        <small class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Full Name</small>
                        <h6 class="fw-bold text-dark mb-0">{{ $application->name }} {{ $application->last_name }}</h6>
                    </div>

                    <!-- Email -->
                    <div class="col-md-6">
                        <small class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Email Address</small>
                        <h6 class="fw-bold text-indigo-700 mb-0">{{ $application->email }}</h6>
                    </div>
                    
                    @if($application->phone)
                        <!-- Phone -->
                        <div class="col-md-6">
                            <small class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Phone Number</small>
                            <h6 class="fw-bold text-dark mb-0">{{ $application->phone }}</h6>
                        </div>
                    @endif

                    <!-- Submission Date -->
                    <div class="col-md-6">
                        <small class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Submission Date</small>
                        <h6 class="fw-bold text-dark mb-0">{{ $application->created_at->format('M d, Y h:i A') }}</h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Panel -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
                <h5 class="fw-bold border-bottom pb-3 mb-4 text-dark uppercase tracking-wide">Application Process</h5>

                @if($application->status === 'pending')
                    <div class="space-y-4">
                        <div class="alert alert-info border-0 rounded-3 text-xs fw-semibold mb-4 leading-relaxed">
                            <i class="bi bi-info-circle-fill me-1"></i> Please review customer details before approval. Approving will activate their account.
                        </div>

                        <!-- Approve trigger -->
                        <form action="{{ route('admin.customer-applications.approve', $application->id) }}" method="POST" class="mb-4">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-check-lg fs-5"></i> Approve & Activate Account
                            </button>
                        </form>

                        <!-- Reject trigger -->
                        <form action="{{ route('admin.customer-applications.reject', $application->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100 py-2.5 fw-bold d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-x-lg fs-5"></i> Reject Application
                            </button>
                        </form>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="mb-3">
                            @if($application->status === 'active')
                                <i class="bi bi-check-circle-fill text-success fs-1"></i>
                                <h6 class="fw-bold text-dark mt-2 uppercase tracking-wide">Approved & Active</h6>
                                <p class="text-muted small mt-1">This user was approved on {{ $application->approved_at ? $application->approved_at->format('M d, Y') : 'N/A' }}</p>
                            @else
                                <i class="bi bi-x-circle-fill text-danger fs-1"></i>
                                <h6 class="fw-bold text-dark mt-2 uppercase tracking-wide">Rejected</h6>
                                <p class="text-muted small mt-1">This application was rejected.</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
