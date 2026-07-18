@extends('admin.master')

@section('title', 'Footer Settings')

@section('header', 'Footer Settings')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-10 col-xl-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-bold mb-0">Manage Footer Configurations</h5>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.footer-settings.update') }}" method="POST">
                        @csrf

                        <!-- Section 1: Brand & Contact Info -->
                        <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                <i class="bi bi-envelope-at me-2 text-primary"></i>Contact Information & Description
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="footer_description" class="form-label fw-bold">Brand Description</label>
                                    <textarea class="form-control" id="footer_description" name="footer_description" rows="3" required>{{ old('footer_description', $footerSettings['footer_description']) }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label for="footer_email" class="form-label fw-bold">Contact Email</label>
                                    <input type="email" class="form-control" id="footer_email" name="footer_email" value="{{ old('footer_email', $footerSettings['footer_email']) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="footer_phone" class="form-label fw-bold">Contact Phone</label>
                                    <input type="text" class="form-control" id="footer_phone" name="footer_phone" value="{{ old('footer_phone', $footerSettings['footer_phone']) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="footer_address" class="form-label fw-bold">Office Address</label>
                                    <input type="text" class="form-control" id="footer_address" name="footer_address" value="{{ old('footer_address', $footerSettings['footer_address']) }}" required>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Social Media URLs -->
                        <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                <i class="bi bi-share me-2 text-primary"></i>Social Media Links
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="social_facebook" class="form-label fw-bold"><i class="bi bi-facebook me-2 text-primary"></i>Facebook URL</label>
                                    <input type="url" class="form-control" id="social_facebook" name="social_facebook" value="{{ old('social_facebook', $footerSettings['social_facebook']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="social_twitter" class="form-label fw-bold"><i class="bi bi-twitter me-2 text-info"></i>Twitter / X URL</label>
                                    <input type="url" class="form-control" id="social_twitter" name="social_twitter" value="{{ old('social_twitter', $footerSettings['social_twitter']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="social_instagram" class="form-label fw-bold"><i class="bi bi-instagram me-2 text-danger"></i>Instagram URL</label>
                                    <input type="url" class="form-control" id="social_instagram" name="social_instagram" value="{{ old('social_instagram', $footerSettings['social_instagram']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="social_linkedin" class="form-label fw-bold"><i class="bi bi-linkedin me-2 text-primary"></i>LinkedIn URL</label>
                                    <input type="url" class="form-control" id="social_linkedin" name="social_linkedin" value="{{ old('social_linkedin', $footerSettings['social_linkedin']) }}">
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Navigation Links Management -->
                        <div class="row">
                            <!-- Column 1: Company Links -->
                            <div class="col-lg-4 col-md-12 mb-4">
                                <div class="card border h-100 p-3 rounded-3">
                                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                        Company Menu
                                    </h6>
                                    <div id="company-links-container" class="link-rows-container">
                                        @foreach($footerSettings['company_links'] as $index => $link)
                                            <div class="row g-2 mb-2 link-row align-items-center">
                                                <div class="col-5">
                                                    <input type="text" class="form-control form-control-sm" name="company_links[{{ $index }}][label]" value="{{ $link['label'] }}" placeholder="Label" required>
                                                </div>
                                                <div class="col-5">
                                                    <input type="text" class="form-control form-control-sm" name="company_links[{{ $index }}][href]" value="{{ $link['href'] }}" placeholder="URL (e.g. /about)" required>
                                                </div>
                                                <div class="col-2 text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-danger border-0 p-1 delete-row-btn" onclick="removeLinkRow(this)"><i class="bi bi-trash-fill"></i></button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn btn-xs btn-outline-primary mt-2" onclick="addLinkRow('company')">
                                        <i class="bi bi-plus-circle me-1"></i> Add Link
                                    </button>
                                </div>
                            </div>

                            <!-- Column 2: Products Links -->
                            <div class="col-lg-4 col-md-12 mb-4">
                                <div class="card border h-100 p-3 rounded-3">
                                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                        Products Menu
                                    </h6>
                                    <div id="products-links-container" class="link-rows-container">
                                        @foreach($footerSettings['products_links'] as $index => $link)
                                            <div class="row g-2 mb-2 link-row align-items-center">
                                                <div class="col-5">
                                                    <input type="text" class="form-control form-control-sm" name="products_links[{{ $index }}][label]" value="{{ $link['label'] }}" placeholder="Label" required>
                                                </div>
                                                <div class="col-5">
                                                    <input type="text" class="form-control form-control-sm" name="products_links[{{ $index }}][href]" value="{{ $link['href'] }}" placeholder="URL (e.g. /products)" required>
                                                </div>
                                                <div class="col-2 text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-danger border-0 p-1 delete-row-btn" onclick="removeLinkRow(this)"><i class="bi bi-trash-fill"></i></button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn btn-xs btn-outline-primary mt-2" onclick="addLinkRow('products')">
                                        <i class="bi bi-plus-circle me-1"></i> Add Link
                                    </button>
                                </div>
                            </div>

                            <!-- Column 3: Support Links -->
                            <div class="col-lg-4 col-md-12 mb-4">
                                <div class="card border h-100 p-3 rounded-3">
                                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                        Support Menu
                                    </h6>
                                    <div id="support-links-container" class="link-rows-container">
                                        @foreach($footerSettings['support_links'] as $index => $link)
                                            <div class="row g-2 mb-2 link-row align-items-center">
                                                <div class="col-5">
                                                    <input type="text" class="form-control form-control-sm" name="support_links[{{ $index }}][label]" value="{{ $link['label'] }}" placeholder="Label" required>
                                                </div>
                                                <div class="col-5">
                                                    <input type="text" class="form-control form-control-sm" name="support_links[{{ $index }}][href]" value="{{ $link['href'] }}" placeholder="URL (e.g. /faq)" required>
                                                </div>
                                                <div class="col-2 text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-danger border-0 p-1 delete-row-btn" onclick="removeLinkRow(this)"><i class="bi bi-trash-fill"></i></button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn btn-xs btn-outline-primary mt-2" onclick="addLinkRow('support')">
                                        <i class="bi bi-plus-circle me-1"></i> Add Link
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-end border-top pt-4 mt-3">
                            <button type="submit" class="btn btn-primary px-5 py-2.5 fw-bold rounded-3 shadow-sm">
                                <i class="bi bi-check-circle me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Script for Dynamic link row editing -->
    <script>
        function addLinkRow(type) {
            const container = document.getElementById(`${type}-links-container`);
            const rowCount = container.getElementsByClassName('link-row').length;
            
            const newRow = document.createElement('div');
            newRow.className = 'row g-2 mb-2 link-row align-items-center';
            newRow.innerHTML = `
                <div class="col-5">
                    <input type="text" class="form-control form-control-sm" name="${type}_links[${rowCount}][label]" placeholder="Label" required>
                </div>
                <div class="col-5">
                    <input type="text" class="form-control form-control-sm" name="${type}_links[${rowCount}][href]" placeholder="URL" required>
                </div>
                <div class="col-2 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger border-0 p-1 delete-row-btn" onclick="removeLinkRow(this)"><i class="bi bi-trash-fill"></i></button>
                </div>
            `;
            container.appendChild(newRow);
            reindexRows(type);
        }

        function removeLinkRow(button) {
            const row = button.closest('.link-row');
            const container = row.closest('.link-rows-container');
            const type = container.id.split('-')[0]; // company, products, support
            row.remove();
            reindexRows(type);
        }

        function reindexRows(type) {
            const container = document.getElementById(`${type}-links-container`);
            const rows = container.getElementsByClassName('link-row');
            Array.from(rows).forEach((row, index) => {
                const inputs = row.getElementsByTagName('input');
                inputs[0].name = `${type}_links[${index}][label]`;
                inputs[1].name = `${type}_links[${index}][href]`;
            });
        }
    </script>
@endsection
