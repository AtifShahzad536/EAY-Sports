@extends('admin.master')

@section('title', 'Edit Page - ' . ucfirst($page))

@section('header', 'Edit ' . ucfirst($page) . ' Page Content')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-10 col-xl-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-bold mb-0">Manage {{ ucfirst($page) }} Content</h5>
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

                    <form action="{{ route('admin.pages.update', $page) }}" method="POST">
                        @csrf

                        <!-- HERO SECTION (Common) -->
                        <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Hero Section Banner</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="hero_title" class="form-label fw-bold">Hero Title</label>
                                    <input type="text" class="form-control" id="hero_title" name="hero[title]" value="{{ old('hero.title', $contents['hero']['title'] ?? '') }}" required>
                                </div>
                                @if($page === 'about')
                                    <div class="col-md-6">
                                        <label for="hero_subtitle" class="form-label fw-bold">Hero Subtitle</label>
                                        <input type="text" class="form-control" id="hero_subtitle" name="hero[subtitle]" value="{{ old('hero.subtitle', $contents['hero']['subtitle'] ?? '') }}" required>
                                    </div>
                                @elseif($page === 'privacy')
                                    <div class="col-md-6">
                                        <label for="hero_last_updated" class="form-label fw-bold">Last Updated Date Text</label>
                                        <input type="text" class="form-control" id="hero_last_updated" name="hero[last_updated]" value="{{ old('hero.last_updated', $contents['hero']['last_updated'] ?? '') }}" required>
                                    </div>
                                @elseif($page === 'terms')
                                    <div class="col-md-6">
                                        <label for="hero_effective_date" class="form-label fw-bold">Effective Date Text</label>
                                        <input type="text" class="form-control" id="hero_effective_date" name="hero[effective_date]" value="{{ old('hero.effective_date', $contents['hero']['effective_date'] ?? '') }}" required>
                                    </div>
                                @endif
                                <div class="col-md-12">
                                    <label for="hero_description" class="form-label fw-bold">Hero Description / Intro Paragraph</label>
                                    <textarea class="form-control" id="hero_description" name="hero[description]" rows="3" required>{{ old('hero.description', $contents['hero']['description'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- PAGE SPECIFIC SECTIONS -->
                        @if($page === 'about')
                            <!-- About Stats -->
                            <div class="card border p-3 mb-4 rounded-3">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Key Statistics (4 slots recommended)</h6>
                                <div id="stats-container" class="link-rows-container">
                                    @foreach($contents['stats'] ?? [] as $index => $stat)
                                        <div class="row g-2 mb-2 link-row align-items-center">
                                            <div class="col-5">
                                                <input type="text" class="form-control form-control-sm" name="stats[{{ $index }}][value]" value="{{ $stat['value'] }}" placeholder="Value (e.g. 50K+)" required>
                                            </div>
                                            <div class="col-5">
                                                <input type="text" class="form-control form-control-sm" name="stats[{{ $index }}][label]" value="{{ $stat['label'] }}" placeholder="Label (e.g. Jerseys Delivered)" required>
                                            </div>
                                            <div class="col-2 text-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger border-0 p-1" onclick="removeRow(this, 'stats')"><i class="bi bi-trash-fill"></i></button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-primary mt-2" onclick="addStatRow()">
                                    <i class="bi bi-plus-circle me-1"></i> Add Statistic
                                </button>
                            </div>

                            <!-- About Values -->
                            <div class="card border p-3 mb-4 rounded-3">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Mission Values (4 slots recommended)</h6>
                                <div id="values-container" class="link-rows-container">
                                    @foreach($contents['values'] ?? [] as $index => $value)
                                        <div class="card bg-light border p-3 mb-2 link-row">
                                            <div class="row g-2 align-items-center mb-2">
                                                <div class="col-md-4">
                                                    <label class="small fw-bold">Lucide Icon Name</label>
                                                    <input type="text" class="form-control form-control-sm" name="values[{{ $index }}][icon]" value="{{ $value['icon'] }}" placeholder="e.g. Shield, Zap, Heart, Award" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="small fw-bold">Value Title</label>
                                                    <input type="text" class="form-control form-control-sm" name="values[{{ $index }}][title]" value="{{ $value['title'] }}" placeholder="e.g. Premium Quality" required>
                                                </div>
                                                <div class="col-md-2 text-end mt-4">
                                                    <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeRow(this, 'values')"><i class="bi bi-trash-fill"></i> Delete</button>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <label class="small fw-bold">Value Description</label>
                                                    <textarea class="form-control form-control-sm" name="values[{{ $index }}][desc]" rows="2" required>{{ $value['desc'] }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-primary mt-2" onclick="addValueRow()">
                                    <i class="bi bi-plus-circle me-1"></i> Add Mission Value
                                </button>
                            </div>

                            <!-- Team Members -->
                            <div class="card border p-3 mb-4 rounded-3">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Team Members</h6>
                                <div id="team-container" class="link-rows-container">
                                    @foreach($contents['team'] ?? [] as $index => $member)
                                        <div class="card bg-light border p-3 mb-2 link-row">
                                            <div class="row g-2 align-items-center mb-2">
                                                <div class="col-md-4">
                                                    <label class="small fw-bold">Full Name</label>
                                                    <input type="text" class="form-control form-control-sm" name="team[{{ $index }}][name]" value="{{ $member['name'] }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="small fw-bold">Role / Job Title</label>
                                                    <input type="text" class="form-control form-control-sm" name="team[{{ $index }}][role]" value="{{ $member['role'] }}" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="small fw-bold">Initials</label>
                                                    <input type="text" class="form-control form-control-sm" name="team[{{ $index }}][initials]" value="{{ $member['initials'] }}" required>
                                                </div>
                                                <div class="col-md-3 text-end mt-4">
                                                    <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeRow(this, 'team')"><i class="bi bi-trash-fill"></i> Delete</button>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <label class="small fw-bold">Tailwind Gradient Colors</label>
                                                    <input type="text" class="form-control form-control-sm" name="team[{{ $index }}][gradient]" value="{{ $member['gradient'] }}" placeholder="e.g. from-indigo-600 to-indigo-500" required>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-primary mt-2" onclick="addTeamRow()">
                                    <i class="bi bi-plus-circle me-1"></i> Add Team Member
                                </button>
                            </div>

                        @elseif($page === 'faq')
                            <!-- FAQs Accordions -->
                            <div class="card border p-3 mb-4 rounded-3">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Frequently Asked Q&As</h6>
                                <div id="faqs-container" class="link-rows-container">
                                    @foreach($contents['faqs'] ?? [] as $index => $faq)
                                        <div class="card bg-light border p-3 mb-2 link-row">
                                            <div class="row g-2 align-items-center mb-2">
                                                <div class="col-md-4">
                                                    <label class="small fw-bold">Category</label>
                                                    <select class="form-select form-select-sm" name="faqs[{{ $index }}][category]" required>
                                                        <option value="general" {{ $faq['category'] === 'general' ? 'selected' : '' }}>General</option>
                                                        <option value="orders" {{ $faq['category'] === 'orders' ? 'selected' : '' }}>Orders & Bulk</option>
                                                        <option value="shipping" {{ $faq['category'] === 'shipping' ? 'selected' : '' }}>Shipping</option>
                                                        <option value="payment" {{ $faq['category'] === 'payment' ? 'selected' : '' }}>Payments</option>
                                                        <option value="returns" {{ $faq['category'] === 'returns' ? 'selected' : '' }}>Returns</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="small fw-bold">Question</label>
                                                    <input type="text" class="form-control form-control-sm" name="faqs[{{ $index }}][q]" value="{{ $faq['q'] }}" required>
                                                </div>
                                                <div class="col-md-2 text-end mt-4">
                                                    <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeRow(this, 'faqs')"><i class="bi bi-trash-fill"></i> Delete</button>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <label class="small fw-bold">Answer</label>
                                                    <textarea class="form-control form-control-sm" name="faqs[{{ $index }}][a]" rows="3" required>{{ $faq['a'] }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-primary mt-2" onclick="addFaqRow()">
                                    <i class="bi bi-plus-circle me-1"></i> Add FAQ
                                </button>
                            </div>

                        @elseif($page === 'privacy' || $page === 'terms')
                            <!-- Policy Sections -->
                            <div class="card border p-3 mb-4 rounded-3">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Policy Document Sections</h6>
                                <div id="sections-container" class="link-rows-container">
                                    @foreach($contents['sections'] ?? [] as $index => $section)
                                        <div class="card bg-light border p-3 mb-2 link-row">
                                            <div class="row g-2 align-items-center mb-2">
                                                <div class="col-md-3">
                                                    <label class="small fw-bold">Section ID (e.g. governance)</label>
                                                    <input type="text" class="form-control form-control-sm" name="sections[{{ $index }}][id]" value="{{ $section['id'] }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="small fw-bold">Lucide Icon Name</label>
                                                    <input type="text" class="form-control form-control-sm" name="sections[{{ $index }}][icon]" value="{{ $section['icon'] }}" placeholder="e.g. Lock, Eye" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="small fw-bold">Title</label>
                                                    <input type="text" class="form-control form-control-sm" name="sections[{{ $index }}][title]" value="{{ $section['title'] }}" required>
                                                </div>
                                                <div class="col-md-2 text-end mt-4">
                                                    <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeRow(this, 'sections')"><i class="bi bi-trash-fill"></i> Delete</button>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <label class="small fw-bold">Content Text</label>
                                                    <textarea class="form-control form-control-sm" name="sections[{{ $index }}][content]" rows="4" required>{{ $section['content'] }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-primary mt-2" onclick="addPolicySectionRow()">
                                    <i class="bi bi-plus-circle me-1"></i> Add Policy Section
                                </button>
                            </div>
                        @endif

                        <!-- Submit -->
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

    <!-- Scripts for dynamic row adding -->
    <script>
        function removeRow(button, type) {
            const row = button.closest('.link-row');
            row.remove();
            reindexRows(type);
        }

        function reindexRows(type) {
            const container = document.getElementById(`${type}-container`);
            const rows = container.getElementsByClassName('link-row');
            Array.from(rows).forEach((row, index) => {
                if (type === 'stats') {
                    const inputs = row.getElementsByTagName('input');
                    inputs[0].name = `stats[${index}][value]`;
                    inputs[1].name = `stats[${index}][label]`;
                } else if (type === 'values') {
                    const inputs = row.getElementsByTagName('input');
                    const textareas = row.getElementsByTagName('textarea');
                    inputs[0].name = `values[${index}][icon]`;
                    inputs[1].name = `values[${index}][title]`;
                    textareas[0].name = `values[${index}][desc]`;
                } else if (type === 'team') {
                    const inputs = row.getElementsByTagName('input');
                    inputs[0].name = `team[${index}][name]`;
                    inputs[1].name = `team[${index}][role]`;
                    inputs[2].name = `team[${index}][initials]`;
                    inputs[3].name = `team[${index}][gradient]`;
                } else if (type === 'faqs') {
                    const selects = row.getElementsByTagName('select');
                    const inputs = row.getElementsByTagName('input');
                    const textareas = row.getElementsByTagName('textarea');
                    selects[0].name = `faqs[${index}][category]`;
                    inputs[0].name = `faqs[${index}][q]`;
                    textareas[0].name = `faqs[${index}][a]`;
                } else if (type === 'sections') {
                    const inputs = row.getElementsByTagName('input');
                    const textareas = row.getElementsByTagName('textarea');
                    inputs[0].name = `sections[${index}][id]`;
                    inputs[1].name = `sections[${index}][icon]`;
                    inputs[2].name = `sections[${index}][title]`;
                    textareas[0].name = `sections[${index}][content]`;
                }
            });
        }

        // About Stats
        function addStatRow() {
            const container = document.getElementById('stats-container');
            const rowCount = container.getElementsByClassName('link-row').length;
            const newRow = document.createElement('div');
            newRow.className = 'row g-2 mb-2 link-row align-items-center';
            newRow.innerHTML = `
                <div class="col-5">
                    <input type="text" class="form-control form-control-sm" name="stats[${rowCount}][value]" placeholder="Value" required>
                </div>
                <div class="col-5">
                    <input type="text" class="form-control form-control-sm" name="stats[${rowCount}][label]" placeholder="Label" required>
                </div>
                <div class="col-2 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger border-0 p-1" onclick="removeRow(this, 'stats')"><i class="bi bi-trash-fill"></i></button>
                </div>
            `;
            container.appendChild(newRow);
        }

        // About Values
        function addValueRow() {
            const container = document.getElementById('values-container');
            const rowCount = container.getElementsByClassName('link-row').length;
            const newRow = document.createElement('div');
            newRow.className = 'card bg-light border p-3 mb-2 link-row';
            newRow.innerHTML = `
                <div class="row g-2 align-items-center mb-2">
                    <div class="col-md-4">
                        <label class="small fw-bold">Lucide Icon Name</label>
                        <input type="text" class="form-control form-control-sm" name="values[${rowCount}][icon]" placeholder="e.g. Shield" required>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">Value Title</label>
                        <input type="text" class="form-control form-control-sm" name="values[${rowCount}][title]" placeholder="e.g. High Quality" required>
                    </div>
                    <div class="col-md-2 text-end mt-4">
                        <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeRow(this, 'values')"><i class="bi bi-trash-fill"></i> Delete</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <label class="small fw-bold">Value Description</label>
                        <textarea class="form-control form-control-sm" name="values[${rowCount}][desc]" rows="2" required></textarea>
                    </div>
                </div>
            `;
            container.appendChild(newRow);
        }

        // Team Members
        function addTeamRow() {
            const container = document.getElementById('team-container');
            const rowCount = container.getElementsByClassName('link-row').length;
            const newRow = document.createElement('div');
            newRow.className = 'card bg-light border p-3 mb-2 link-row';
            newRow.innerHTML = `
                <div class="row g-2 align-items-center mb-2">
                    <div class="col-md-4">
                        <label class="small fw-bold">Full Name</label>
                        <input type="text" class="form-control form-control-sm" name="team[${rowCount}][name]" required>
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold">Role / Job Title</label>
                        <input type="text" class="form-control form-control-sm" name="team[${rowCount}][role]" required>
                    </div>
                    <div class="col-md-2">
                        <label class="small fw-bold">Initials</label>
                        <input type="text" class="form-control form-control-sm" name="team[${rowCount}][initials]" required>
                    </div>
                    <div class="col-md-3 text-end mt-4">
                        <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeRow(this, 'team')"><i class="bi bi-trash-fill"></i> Delete</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <label class="small fw-bold">Tailwind Gradient Colors</label>
                        <input type="text" class="form-control form-control-sm" name="team[${rowCount}][gradient]" placeholder="from-indigo-600 to-indigo-500" required>
                    </div>
                </div>
            `;
            container.appendChild(newRow);
        }

        // FAQs
        function addFaqRow() {
            const container = document.getElementById('faqs-container');
            const rowCount = container.getElementsByClassName('link-row').length;
            const newRow = document.createElement('div');
            newRow.className = 'card bg-light border p-3 mb-2 link-row';
            newRow.innerHTML = `
                <div class="row g-2 align-items-center mb-2">
                    <div class="col-md-4">
                        <label class="small fw-bold">Category</label>
                        <select class="form-select form-select-sm" name="faqs[${rowCount}][category]" required>
                            <option value="general">General</option>
                            <option value="orders">Orders & Bulk</option>
                            <option value="shipping">Shipping</option>
                            <option value="payment">Payments</option>
                            <option value="returns">Returns</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">Question</label>
                        <input type="text" class="form-control form-control-sm" name="faqs[${rowCount}][q]" required>
                    </div>
                    <div class="col-md-2 text-end mt-4">
                        <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeRow(this, 'faqs')"><i class="bi bi-trash-fill"></i> Delete</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <label class="small fw-bold">Answer</label>
                        <textarea class="form-control form-control-sm" name="faqs[${rowCount}][a]" rows="3" required></textarea>
                    </div>
                </div>
            `;
            container.appendChild(newRow);
        }

        // Policy Section
        function addPolicySectionRow() {
            const container = document.getElementById('sections-container');
            const rowCount = container.getElementsByClassName('link-row').length;
            const newRow = document.createElement('div');
            newRow.className = 'card bg-light border p-3 mb-2 link-row';
            newRow.innerHTML = `
                <div class="row g-2 align-items-center mb-2">
                    <div class="col-md-3">
                        <label class="small fw-bold">Section ID (e.g. governance)</label>
                        <input type="text" class="form-control form-control-sm" name="sections[${rowCount}][id]" required>
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold">Lucide Icon Name</label>
                        <input type="text" class="form-control form-control-sm" name="sections[${rowCount}][icon]" placeholder="e.g. Lock" required>
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold">Title</label>
                        <input type="text" class="form-control form-control-sm" name="sections[${rowCount}][title]" required>
                    </div>
                    <div class="col-md-2 text-end mt-4">
                        <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeRow(this, 'sections')"><i class="bi bi-trash-fill"></i> Delete</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <label class="small fw-bold">Content Text</label>
                        <textarea class="form-control form-control-sm" name="sections[${rowCount}][content]" rows="4" required></textarea>
                    </div>
                </div>
            `;
            container.appendChild(newRow);
        }
    </script>
@endsection
