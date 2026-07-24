@extends('admin.master')

@section('title', 'Add Builder Model')

@section('header', 'Add Builder Model')

@section('content')
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form action="{{ route('admin.builder-models.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">3D Model File (.glb)</label>
                    <input type="file" name="model_file" id="modelFileInput" class="form-control" accept=".glb" required>
                </div>

                <!-- Container for dynamic layers config -->
                <div id="layers-config-section" class="mb-4 d-none shadow-sm p-4 border rounded bg-light">
                    <h5 class="border-bottom pb-2 mb-3 text-dark fw-bold">3D Model Layers & Mesh Customization</h5>
                    <p class="text-muted small">Give layers client-friendly names and toggle Lock to prevent users from altering them in the builder.</p>
                    <div id="layers-list-container" style="max-height: 400px; overflow-y: auto;" class="p-3 border rounded bg-white">
                        <!-- Dynamically filled -->
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="featuredCheck">
                    <label class="form-check-label fw-bold text-primary" for="featuredCheck">★ Show as Main Card on Builder Landing Page (CHOOSE BASIS)</label>
                    <small class="text-muted d-block">If checked, this model will represent its category on the main /builder landing screen.</small>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="status" value="1" class="form-check-input" id="statusCheck" checked>
                    <label class="form-check-label" for="statusCheck">Active</label>
                </div>

                <button type="submit" class="btn btn-primary">Save Model</button>
                <a href="{{ route('admin.builder-models.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

    <!-- Load ThreeJS & GLTFLoader CDN -->
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loader = new THREE.GLTFLoader();
            const fileInput = document.getElementById('modelFileInput');
            const container = document.getElementById('layers-config-section');
            const listContainer = document.getElementById('layers-list-container');
            let meshConfig = {};

            function renderHiddenInputs() {
                // Remove any previous hidden inputs
                const existingHidden = document.querySelectorAll('.dynamic-hidden-input');
                existingHidden.forEach(el => el.remove());

                // Generate hidden inputs for each entry in meshConfig
                Object.keys(meshConfig).forEach(meshName => {
                    const conf = meshConfig[meshName];
                    
                    // Display name input
                    const nameInput = document.createElement('input');
                    nameInput.type = 'hidden';
                    nameInput.className = 'dynamic-hidden-input';
                    nameInput.name = `layers[${meshName}][display_name]`;
                    nameInput.value = conf.display_name;
                    listContainer.appendChild(nameInput);

                    // Lock checkbox input (only if checked)
                    if (conf.is_locked) {
                        const lockInput = document.createElement('input');
                        lockInput.type = 'hidden';
                        lockInput.className = 'dynamic-hidden-input';
                        lockInput.name = `layers[${meshName}][is_locked]`;
                        lockInput.value = '1';
                        listContainer.appendChild(lockInput);

                        const showLockInput = document.createElement('input');
                        showLockInput.type = 'hidden';
                        showLockInput.className = 'dynamic-hidden-input';
                        showLockInput.name = `layers[${meshName}][show_lock]`;
                        showLockInput.value = conf.show_lock !== false ? '1' : '0';
                        listContainer.appendChild(showLockInput);
                    }

                    // Merge parent input (if set)
                    if (conf.merge_parent) {
                        const parentInput = document.createElement('input');
                        parentInput.type = 'hidden';
                        parentInput.className = 'dynamic-hidden-input';
                        parentInput.name = `layers[${meshName}][merge_parent]`;
                        parentInput.value = conf.merge_parent;
                        listContainer.appendChild(parentInput);
                    }
                });
            }

            function renderMeshList() {
                listContainer.innerHTML = '';
                
                // Find all independent / parent meshes (where merge_parent is not set or empty)
                const parentMeshes = Object.keys(meshConfig).filter(m => !meshConfig[m].merge_parent);
                
                if (parentMeshes.length === 0) {
                    listContainer.innerHTML = '<div class="text-muted small text-center py-3">No customizable meshes.</div>';
                    return;
                }

                // List of all independent meshes (to populate dropdowns)
                const independentMeshes = parentMeshes;

                parentMeshes.forEach(function(meshName) {
                    const conf = meshConfig[meshName];
                    
                    // Find children merged into this parent
                    const children = Object.keys(meshConfig).filter(m => meshConfig[m].merge_parent === meshName);
                    
                    // Generate tags for children
                    let childrenHtml = '';
                    if (children.length > 0) {
                        childrenHtml = '<div class="mt-2 d-flex flex-wrap gap-1 align-items-center"><span class="small text-muted me-1">Merged:</span>';
                        children.forEach(child => {
                            childrenHtml += `
                                <span class="badge bg-secondary d-inline-flex align-items-center gap-1 p-2" style="font-size: 0.75rem;">
                                    ${child}
                                    <button type="button" class="btn-close btn-close-white btn-unmerge ms-1" data-child="${child}" style="font-size: 0.5rem; width: 0.5em; height: 0.5em;" title="Unmerge" aria-label="Close"></button>
                                </span>
                            `;
                        });
                        childrenHtml += '</div>';
                    }

                    // Dropdown to merge other meshes into this parent
                    const availableToMerge = independentMeshes.filter(m => m !== meshName);
                    let optionsHtml = '<option value="">+ Merge another mesh here...</option>';
                    availableToMerge.forEach(m => {
                        optionsHtml += `<option value="${m}">${m}</option>`;
                    });

                    const html = `
                        <div class="row align-items-center mb-3 pb-3 border-bottom py-2" id="row-${meshName}">
                            <div class="col-md-3">
                                <strong class="text-secondary small d-block">${meshName}</strong>
                                ${childrenHtml}
                            </div>
                            <div class="col-md-3">
                                <input type="text" data-mesh="${meshName}" class="form-control form-control-sm input-display-name" value="${conf.display_name}" placeholder="Display Name">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select form-select-sm select-add-merge" data-parent="${meshName}">
                                    ${optionsHtml}
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex flex-column gap-1">
                                    <div class="form-check form-switch d-flex align-items-center gap-2">
                                        <input type="checkbox" data-mesh="${meshName}" class="form-check-input check-lock" id="lock-${meshName}" ${conf.is_locked ? 'checked' : ''}>
                                        <label class="form-check-label small text-danger fw-bold" for="lock-${meshName}">🔒 Lock</label>
                                    </div>
                                    <div class="form-check form-switch d-flex align-items-center gap-2 check-show-lock-container ${conf.is_locked ? '' : 'd-none'}" id="show-lock-container-${meshName}">
                                        <input type="checkbox" data-mesh="${meshName}" class="form-check-input check-show-lock" id="show-lock-${meshName}" ${conf.show_lock !== false ? 'checked' : ''}>
                                        <label class="form-check-label small text-muted" style="font-size: 0.75rem;" for="show-lock-${meshName}">Show lock icon on frontend</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    listContainer.insertAdjacentHTML('beforeend', html);
                });

                renderHiddenInputs();
            }

            // Set up event delegation on listContainer
            listContainer.addEventListener('input', function(e) {
                if (e.target.classList.contains('input-display-name')) {
                    const mesh = e.target.dataset.mesh;
                    meshConfig[mesh].display_name = e.target.value;
                    renderHiddenInputs();
                }
            });

            listContainer.addEventListener('change', function(e) {
                if (e.target.classList.contains('check-lock')) {
                    const mesh = e.target.dataset.mesh;
                    meshConfig[mesh].is_locked = e.target.checked;
                    
                    // Show/hide the show-lock switch container dynamically
                    const showLockContainer = document.getElementById(`show-lock-container-${mesh}`);
                    if (showLockContainer) {
                        if (e.target.checked) {
                            showLockContainer.classList.remove('d-none');
                        } else {
                            showLockContainer.classList.add('d-none');
                        }
                    }
                    
                    renderHiddenInputs();
                }
                if (e.target.classList.contains('check-show-lock')) {
                    const mesh = e.target.dataset.mesh;
                    meshConfig[mesh].show_lock = e.target.checked;
                    renderHiddenInputs();
                }
                if (e.target.classList.contains('select-add-merge')) {
                    const parent = e.target.dataset.parent;
                    const child = e.target.value;
                    if (child) {
                        meshConfig[child].merge_parent = parent;
                        renderMeshList();
                    }
                }
            });

            listContainer.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-unmerge');
                if (btn) {
                    const child = btn.dataset.child;
                    meshConfig[child].merge_parent = null;
                    renderMeshList();
                }
            });

            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                listContainer.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary spinner-border-sm" role="status"></div><span class="ms-2">Parsing 3D Mesh layers...</span></div>';
                container.classList.remove('d-none');

                const reader = new FileReader();
                reader.onload = function(evt) {
                    const arrayBuffer = evt.target.result;
                    loader.parse(arrayBuffer, '', function(gltf) {
                        const meshes = [];
                        gltf.scene.traverse(function(child) {
                            if (child.isMesh && child.name) {
                                meshes.push(child.name);
                            }
                        });

                        const uniqueMeshes = Array.from(new Set(meshes));
                        listContainer.innerHTML = '';

                        if (uniqueMeshes.length === 0) {
                            listContainer.innerHTML = '<div class="text-muted small text-center">No customizable meshes found in this model file.</div>';
                            return;
                        }

                        meshConfig = {};
                        uniqueMeshes.forEach(function(meshName) {
                            const friendlyName = meshName.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                            meshConfig[meshName] = {
                                display_name: friendlyName,
                                is_locked: false,
                                show_lock: true,
                                merge_parent: null
                            };
                        });

                        renderMeshList();
                    }, function(error) {
                        console.error('Error parsing GLB:', error);
                        listContainer.innerHTML = '<div class="text-danger small">Error parsing GLB file. Make sure it is a valid glTF file.</div>';
                    });
                };
                reader.readAsArrayBuffer(file);
            });
        });
    </script>
@endsection
