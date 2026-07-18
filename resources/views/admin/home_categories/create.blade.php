@extends('admin.master')

@section('title', 'Create Category Card')

@section('header', 'Create Category Card')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-8 col-xl-12 col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-bold">New Category Card</h5>
                        <a href="{{ route('admin.home-categories.index') }}"
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

                    <form action="{{ route('admin.home-categories.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">

                            <!-- Category Name -->
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-bold">
                                    Category Name <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('name') is-invalid @enderror"
                                        id="name"
                                        name="name"
                                        required>
                                    <option value="" disabled {{ old('name') === null ? 'selected' : '' }}>Select Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->name }}" {{ old('name') == $cat->name ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                        @foreach($cat->subcategories as $child)
                                            <option value="{{ $child->name }}" {{ old('name') == $child->name ? 'selected' : '' }}>
                                                &nbsp;&nbsp;— {{ $child->name }}
                                            </option>
                                            @foreach($child->subcategories as $subchild)
                                                <option value="{{ $subchild->name }}" {{ old('name') == $subchild->name ? 'selected' : '' }}>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;— {{ $subchild->name }}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                </select>
                                <small class="text-muted">Select an existing category or subcategory.</small>
                            </div>

                            <!-- Count / Subtitle -->
                            <div class="col-md-6">
                                <label for="count" class="form-label fw-bold">
                                    Subtitle / Count <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="count"
                                       name="count"
                                       value="{{ old('count') }}"
                                       required>
                                <small class="text-muted">Example: 50+ Designs, 30+ Options</small>
                            </div>

                            <!-- Gradient Classes -->
                            <div class="col-md-12">
                                <label for="gradient_preset" class="form-label fw-bold">
                                    Card Gradient Color Preset <span class="text-danger">*</span>
                                </label>
                                <select id="gradient_preset" class="form-select mb-3 shadow-sm border-2">
                                    <option value="from-[#0EA5E9]/70 via-[#0284C7]/60 to-[#1D4ED8]/80">Jerseys Blue (Default)</option>
                                    <option value="from-[#EC4899]/70 via-[#C026D3]/60 to-[#9333EA]/80">T-Shirts Pink</option>
                                    <option value="from-[#10B981]/70 via-[#059669]/60 to-[#047857]/80">Hoodies Green</option>
                                    <option value="from-[#FB923C]/70 via-[#F97316]/60 to-[#DC2626]/80">Shorts Orange</option>
                                    <option value="from-slate-800/80 via-slate-900/70 to-black/80">Elegant Dark Gray</option>
                                    <option value="custom">Custom Color Gradient (For Technical Users)</option>
                                </select>

                                <div id="custom_gradient_container" style="display: none;" class="p-3 bg-light border rounded mb-3">
                                    <label for="gradient" class="form-label fw-bold text-indigo">
                                        Custom Tailwind Gradient Classes
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="gradient"
                                           name="gradient"
                                           value="{{ old('gradient', 'from-[#0EA5E9]/70 via-[#0284C7]/60 to-[#1D4ED8]/80') }}"
                                           placeholder="e.g. from-indigo-500/70 via-purple-500/60 to-pink-500/80"
                                           required>
                                    <small class="text-muted d-block mt-2">
                                        Type custom Tailwind classes. Make sure they use layout gradients like <code>from-... via-... to-...</code>
                                    </small>
                                </div>
                            </div>

                            <!-- Option 1: Upload File -->
                            <div class="col-md-12 mt-4">
                                <label for="image_file" class="form-label fw-bold">
                                    Upload Category Image
                                </label>
                                <input type="file"
                                       class="form-control"
                                       id="image_file"
                                       name="image_file"
                                       accept="image/*">
                                <small class="text-muted">Recommended: vertical aspect ratio.</small>
                                
                                <!-- Selected Image Preview -->
                                <div id="image-preview-container" class="mt-3" style="display: none;">
                                    <label class="form-label fw-bold d-block">Selected Image Preview:</label>
                                    <div class="rounded border overflow-hidden mt-2" style="width: 150px; height: 180px;">
                                        <img id="image-preview" src="" alt="Selected Preview" class="w-100 h-100 object-cover">
                                    </div>
                                    <div class="text-muted small mt-1"><i class="bi bi-check-circle-fill text-success me-1"></i> Optimized — ready to save</div>
                                </div>
                            </div>

                            <div class="col-12 py-1 text-center">
                                <span class="badge bg-secondary px-3 py-2">OR</span>
                            </div>

                            <!-- Option 2: Image URL -->
                            <div class="col-md-12">
                                <label for="image_url" class="form-label fw-bold">
                                    Image URL
                                </label>
                                <input type="url"
                                       class="form-control"
                                       id="image_url"
                                       name="image_url"
                                       value="{{ old('image_url') }}"
                                       placeholder="https://images.unsplash.com/...">
                                <small class="text-muted">Or paste an online direct image link (e.g. Unsplash).</small>
                            </div>

                            <!-- Order -->
                            <div class="col-md-6 mt-4">
                                <label for="order" class="form-label fw-bold">
                                    Order <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control"
                                       id="order"
                                       name="order"
                                       value="{{ old('order', '1') }}"
                                       required>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mt-4">
                                <label for="status" class="form-label fw-bold">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <!-- Submit -->
                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="bi bi-plus-circle me-1"></i> Create Category Card
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const presetSelect = document.getElementById('gradient_preset');
        const gradientInput = document.getElementById('gradient');
        const customContainer = document.getElementById('custom_gradient_container');

        function updateState() {
            const currentValue = gradientInput.value;
            let matched = false;
            
            for (let option of presetSelect.options) {
                if (option.value === currentValue) {
                    presetSelect.value = currentValue;
                    matched = true;
                    break;
                }
            }
            
            if (!matched && currentValue) {
                presetSelect.value = 'custom';
                customContainer.style.display = 'block';
            } else {
                customContainer.style.display = 'none';
            }
        }

        presetSelect.addEventListener('change', function () {
            if (this.value === 'custom') {
                customContainer.style.display = 'block';
            } else {
                customContainer.style.display = 'none';
                gradientInput.value = this.value;
            }
        });

        // Initialize state
        updateState();

        // ── Image Preview and Client-side Compression ──
        const imageFileInput = document.getElementById('image_file');
        const previewContainer = document.getElementById('image-preview-container');
        const form = document.querySelector('form');

        function compressImage(file, maxWidth = 1200, maxHeight = 1200, quality = 0.75) {
            return new Promise((resolve) => {
                if (!file.type.startsWith('image/')) {
                    return resolve(file);
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        const canvas = document.createElement('canvas');
                        let width = img.width;
                        let height = img.height;

                        if (width > height) {
                            if (width > maxWidth) {
                                height = Math.round((height * maxWidth) / width);
                                width = maxWidth;
                            }
                        } else {
                            if (height > maxHeight) {
                                width = Math.round((width * maxHeight) / height);
                                height = maxHeight;
                            }
                        }

                        canvas.width = width;
                        canvas.height = height;

                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob((blob) => {
                            if (blob) {
                                const compressedFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                });
                                resolve(compressedFile);
                            } else {
                                resolve(file);
                            }
                        }, 'image/jpeg', quality);
                    };
                    img.onerror = () => resolve(file);
                    img.src = e.target.result;
                };
                reader.onerror = () => resolve(file);
                reader.readAsDataURL(file);
            });
        }

        if (imageFileInput) {
            imageFileInput.addEventListener('change', async function () {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    previewContainer.innerHTML = '<div class="text-info small mt-2"><i class="spinner-border spinner-border-sm me-1"></i> Optimizing image...</div>';
                    previewContainer.style.display = 'block';

                    const compressed = await compressImage(file);
                    
                    const dt = new DataTransfer();
                    dt.items.add(compressed);
                    this.files = dt.files;

                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewContainer.innerHTML = `
                            <label class="form-label fw-bold d-block">Selected Image Preview:</label>
                            <div class="rounded border overflow-hidden mt-2" style="width: 150px; height: 180px;">
                                <img src="${e.target.result}" alt="Selected Preview" class="w-100 h-100 object-cover">
                            </div>
                            <div class="text-muted small mt-1"><i class="bi bi-check-circle-fill text-success me-1"></i> Optimized — ready to save</div>
                        `;
                    };
                    reader.readAsDataURL(compressed);
                } else {
                    previewContainer.style.display = 'none';
                    previewContainer.innerHTML = '';
                }
            });
        }

        // Limit post size check
        if (form) {
            form.addEventListener('submit', function (e) {
                let totalSize = 0;
                if (imageFileInput && imageFileInput.files && imageFileInput.files.length > 0) {
                    totalSize += imageFileInput.files[0].size;
                }
                const totalMB = totalSize / (1024 * 1024);
                if (totalMB > 20) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Upload Limit Exceeded',
                        text: `The total size of selected image (${totalMB.toFixed(2)} MB) exceeds the upload limit (20 MB). Please choose a compressed file.`,
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
</script>
@endpush
