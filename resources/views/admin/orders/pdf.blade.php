<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Invoice #{{ 1000 + $order->id }}</title>
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Three.js Libraries for 3D Renderings -->
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/RGBELoader.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/geometries/DecalGeometry.js"></script>

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 30px;
            font-size: 12.5px;
            line-height: 1.45;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .invoice-container {
            max-width: 950px;
            margin: 0 auto;
        }

        /* Header Style */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .company-branding {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .company-logo-img {
            height: 60px;
            width: auto;
            object-fit: contain;
        }

        .company-details {
            color: #64748b;
            font-size: 11px;
            line-height: 1.4;
        }

        .company-name {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 3px 0;
            letter-spacing: -0.5px;
        }

        .invoice-meta {
            text-align: right;
        }

        .invoice-title {
            font-size: 24px;
            font-weight: 800;
            color: #4f46e5; /* Premium Indigo */
            margin: 0 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .meta-item {
            font-size: 11.5px;
            margin-bottom: 3px;
            color: #475569;
        }

        .meta-item strong {
            color: #0f172a;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 3px 9px;
            border-radius: 50px;
            margin-top: 4px;
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .status-pending { background-color: #fef3c7; color: #d97706; border-color: #fde68a; }
        .status-processing { background-color: #dbeafe; color: #2563eb; border-color: #bfdbfe; }
        .status-shipped { background-color: #dcfce7; color: #16a34a; border-color: #bbf7d0; }
        .status-cancelled { background-color: #fee2e2; color: #dc2626; border-color: #fca5a5; }

        /* Billing / Shipping section */
        .info-section {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 25px;
        }

        .info-col {
            flex: 1;
            background-color: #f8fafc;
            border-radius: 6px;
            padding: 12px 16px;
            border: 1px solid #f1f5f9;
        }

        .info-heading {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            margin-top: 0;
            margin-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }

        .info-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .info-text {
            color: #475569;
            line-height: 1.4;
            font-size: 11.5px;
        }

        /* 3D Renderings Row */
        .model-pictures-row {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .model-picture-box {
            flex: 1;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            background-color: #f8fafc;
            text-align: center;
        }

        .model-picture-title {
            font-size: 10px;
            font-weight: 800;
            color: #64748b;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }

        .print-canvas-container {
            width: 100%;
            height: 260px;
            background: #eef2f3;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
            position: relative;
            overflow: hidden;
        }

        .canvas-loader {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 11px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Material Specs Section */
        .material-specs-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 25px;
            color: #1e3a8a;
        }

        .material-specs-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 6px;
        }

        .material-specs-list {
            margin: 0;
            padding-left: 20px;
            font-size: 11.5px;
        }

        .material-specs-list li {
            margin-bottom: 3px;
        }

        /* Items Table */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .invoice-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 10.5px;
            letter-spacing: 0.5px;
            padding: 10px 14px;
            text-align: left;
        }

        .invoice-table th:first-child {
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }

        .invoice-table th:last-child {
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
            text-align: right;
        }

        .invoice-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .invoice-table tr:last-child td {
            border-bottom: 2px solid #cbd5e1;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .product-thumb {
            width: 38px;
            height: 38px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            object-fit: cover;
        }

        .product-name {
            font-weight: 700;
            color: #0f172a;
            font-size: 12.5px;
        }

        .product-id {
            color: #64748b;
            font-size: 10.5px;
            margin-top: 1px;
        }

        /* Spec Badges style */
        .specs-container {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .spec-item {
            font-size: 10px;
            font-weight: 600;
            padding: 2.5px 6px;
            border-radius: 4px;
            display: inline-block;
            width: fit-content;
        }

        .spec-size { background-color: #f5f3ff; color: #4f46e5; border: 1px solid #ddd6fe; }
        .spec-name { background-color: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
        .spec-number { background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .spec-design { background-color: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }

        /* Meshes Detail Table */
        .meshes-detail-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .meshes-detail-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #0f172a;
            margin-bottom: 8px;
            border-bottom: 1.5px solid #cbd5e1;
            padding-bottom: 4px;
        }

        .mesh-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .mesh-row:nth-child(even) {
            background-color: #f8fafc;
        }

        .mesh-name {
            font-weight: 600;
            color: #334155;
            font-size: 11.5px;
        }

        .mesh-customs {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            justify-content: flex-end;
            max-width: 70%;
        }

        .custom-badge {
            font-size: 9.5px;
            font-weight: 500;
            padding: 2px 6px;
            border-radius: 3px;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .color-preview {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            border: 1px solid #94a3b8;
        }

        /* Download Button link */
        .download-btn {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 2px 6px;
            border-radius: 4px;
            background-color: #4f46e5;
            color: #ffffff !important;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            transition: all 0.15s ease;
        }

        .download-btn:hover {
            background-color: #4338ca;
        }

        /* Bottom Layout */
        .bottom-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 40px;
            page-break-inside: avoid;
        }

        .notes-col {
            flex: 1.2;
        }

        .totals-col {
            flex: 0.8;
            background-color: #f8fafc;
            border-radius: 6px;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
        }

        .notes-title {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            margin-bottom: 6px;
        }

        .notes-content {
            background-color: #fafafa;
            border: 1px dashed #cbd5e1;
            border-radius: 4px;
            padding: 10px;
            font-style: italic;
            color: #475569;
            font-size: 11.5px;
            line-height: 1.4;
            margin-bottom: 12px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 11.5px;
            color: #475569;
        }

        .total-row.grand-total {
            border-top: 2px solid #e2e8f0;
            padding-top: 8px;
            margin-top: 8px;
            font-size: 14.5px;
            font-weight: 800;
            color: #4f46e5;
        }

        .signature-section {
            margin-top: 50px;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
            display: flex;
            justify-content: space-between;
            color: #64748b;
            font-size: 10.5px;
            page-break-inside: avoid;
        }

        .signature-line {
            width: 180px;
            border-bottom: 1px solid #cbd5e1;
            margin-bottom: 4px;
            height: 35px;
        }

        /* Print Settings */
        @media print {
            body {
                padding: 15px;
                font-size: 11.5px;
            }
            .no-print {
                display: none !important;
            }
            .print-canvas-container {
                border-color: #94a3b8;
            }
            @page {
                size: portrait;
                margin: 15mm;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Invoice Header -->
        <div class="invoice-header">
            <div class="company-branding">
                <img src="/Eay-logo.png" alt="EAY Sports Logo" class="company-logo-img" />
                <div>
                    <h1 class="company-name">EAY Sports</h1>
                    <div class="company-details">
                        Elite Athletic Apparel & Storefront Orders Portal<br>
                        wholesale@eaysports.com | www.eaysports.com
                    </div>
                </div>
            </div>
            
            <div class="invoice-meta">
                <h2 class="invoice-title">Invoice</h2>
                <div class="meta-item">Order ID: <strong>#{{ 1000 + $order->id }}</strong></div>
                <div class="meta-item">Date Placed: <strong>{{ $order->created_at->format('M d, Y H:i') }}</strong></div>
                <div>
                    @php
                        $statusClass = '';
                        $status = strtolower($order->status);
                        if ($status === 'pending') $statusClass = 'status-pending';
                        elseif ($status === 'processing') $statusClass = 'status-processing';
                        elseif ($status === 'shipped' || $status === 'delivered') $statusClass = 'status-shipped';
                        elseif ($status === 'cancelled') $statusClass = 'status-cancelled';
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $order->status ?: 'Pending' }}</span>
                </div>
            </div>
        </div>

        <!-- Info Columns -->
        <div class="info-section">
            <div class="info-col">
                <h3 class="info-heading">Bill To / Customer</h3>
                <div class="info-name">{{ $order->billing_name ?: 'Retail Customer' }}</div>
                <div class="info-text">
                    Email: {{ $order->billing_email ?: 'N/A' }}<br>
                    Phone: {{ $order->phone ?: 'N/A' }}
                </div>
            </div>
            
            <div class="info-col">
                <h3 class="info-heading">Ship To / Delivery Destination</h3>
                <div class="info-name">Delivery Address</div>
                <div class="info-text">
                    {{ $order->shipping_address }}<br>
                    {{ $order->city }}, {{ $order->zip_code }} {{ $order->country }}
                </div>
            </div>
        </div>

        <!-- 3D Previews Section -->
        @foreach($order->items as $item)
            @if($item->model_url)
                <div class="model-pictures-row">
                    <div class="model-picture-box">
                        <div class="model-picture-title">{{ $item->product_name }} - Front View Preview</div>
                        <div id="three-front-{{ $item->id }}" class="print-canvas-container">
                            <div class="canvas-loader">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                Rendering Front View...
                            </div>
                        </div>
                    </div>
                    <div class="model-picture-box">
                        <div class="model-picture-title">{{ $item->product_name }} - Back View Preview</div>
                        <div id="three-back-{{ $item->id }}" class="print-canvas-container">
                            <div class="canvas-loader">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                Rendering Back View...
                            </div>
                        </div>
                    </div>
                </div>

                <div class="model-pictures-row" style="margin-top: -15px;">
                    <div class="model-picture-box">
                        <div class="model-picture-title">{{ $item->product_name }} - Left Side View Preview</div>
                        <div id="three-left-{{ $item->id }}" class="print-canvas-container">
                            <div class="canvas-loader">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                Rendering Left View...
                            </div>
                        </div>
                    </div>
                    <div class="model-picture-box">
                        <div class="model-picture-title">{{ $item->product_name }} - Right Side View Preview</div>
                        <div id="three-right-{{ $item->id }}" class="print-canvas-container">
                            <div class="canvas-loader">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                Rendering Right View...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Material Finish Specs Block -->
                <div class="material-specs-box">
                    <div class="material-specs-title">
                        <i class="bi bi-info-circle-fill me-1"></i> Custom Fabric & Material Specifications
                    </div>
                    <ul class="material-specs-list">
                        <li>
                            <strong>Selected Material Finish:</strong> 
                            <span style="text-transform: uppercase; font-weight: 700;">
                                {{ $item->savedDesign->design_data['materialFinish'] ?? 'Matte' }}
                            </span>
                        </li>
                        <li>
                            <strong>Physical Properties:</strong> 
                            @php
                                $finish = strtolower($item->savedDesign->design_data['materialFinish'] ?? 'matte');
                                if ($finish === 'gloss') {
                                    echo 'High reflection sheen, Gloss base coating (Roughness: 10%, Metalness: 0%)';
                                } elseif ($finish === 'metallic') {
                                    echo 'Reflective metallic luster, Chromed metal overlay (Roughness: 20%, Metalness: 80%)';
                                } else {
                                    echo 'Standard anti-glare mesh, Matte fabric dye (Roughness: 80%, Metalness: 0%)';
                                }
                            @endphp
                        </li>
                        @if(isset($item->savedDesign->design_data['globalPattern']) && $item->savedDesign->design_data['globalPattern'] !== 'none')
                            <li>
                                <strong>Global Fabric Base Pattern:</strong> 
                                <span style="text-transform: uppercase; font-weight: 600;">
                                    {{ $item->savedDesign->design_data['globalPattern'] }}
                                </span>
                            </li>
                        @endif
                    </ul>
                </div>

                <!-- Mesh-by-Mesh Customizations Detail Table -->
                <div class="meshes-detail-section">
                    <div class="meshes-detail-title">
                        <i class="bi bi-layout-three-columns me-1"></i> Submesh Component & Decoration Details
                    </div>
                    <div style="border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden;">
                        @if(isset($item->layers_metadata) && count($item->layers_metadata) > 0)
                            @foreach($item->layers_metadata as $meshId => $meta)
                                @if(!empty($meta['merge_parent']))
                                    @continue
                                @endif
                                @php
                                    $stateKey = $meta['merge_parent'] ?? $meshId;
                                    $state = $item->savedDesign->design_data['meshStates'][$stateKey] 
                                          ?? $item->savedDesign->design_data['meshStates'][$stateKey . '.obj'] 
                                          ?? $item->savedDesign->design_data['meshStates'][str_replace('.obj', '', $stateKey)] 
                                          ?? [];
                                    $color = $state['color'] ?? '#ffffff';
                                    $displayName = $meta['display_name'] ?? $meta['display'] ?? ucwords(str_replace('_', ' ', str_replace('.obj', '', $meshId)));
                                @endphp
                                <div class="mesh-row">
                                    <span class="mesh-name">{{ $displayName }}</span>
                                    <div class="mesh-customs">
                                        <span class="custom-badge">
                                            <span class="color-preview" style="background-color: {{ $color }}"></span>
                                            Color: {{ $color }}
                                        </span>
                                        
                                        @if(isset($state['pUrl']) && $state['pUrl'])
                                            <span class="custom-badge" style="color: #4f46e5; border-color: #ddd6fe; background-color: #f5f3ff;">
                                                <i class="bi bi-grid-3x3-gap"></i> Texture Overlay
                                            </span>
                                        @endif

                                        <!-- Check for decals placed on this specific mesh or merged siblings -->
                                        @php
                                            $meshDecals = array_filter($item->savedDesign->design_data['decals'] ?? [], function($d) use ($meshId, $meta, $item) {
                                                $decalMeshId = $d['meshId'];
                                                if ($decalMeshId === $meshId || str_replace('.obj', '', $decalMeshId) === str_replace('.obj', '', $meshId)) {
                                                    return true;
                                                }
                                                $dMeta = $item->layers_metadata[$decalMeshId] 
                                                      ?? $item->layers_metadata[$decalMeshId . '.obj'] 
                                                      ?? $item->layers_metadata[str_replace('.obj', '', $decalMeshId)] 
                                                      ?? [];
                                                $mParent = $meta['merge_parent'] ?? null;
                                                $dParent = $dMeta['merge_parent'] ?? null;
                                                
                                                if ($mParent && ($mParent === $dParent || $mParent === $decalMeshId)) {
                                                    return true;
                                                }
                                                if ($dParent && ($dParent === $meshId || $dParent === $mParent)) {
                                                    return true;
                                                }
                                                return false;
                                            });
                                        @endphp

                                        @foreach($meshDecals as $decal)
                                            @if(($decal['type'] ?? 'text') === 'text')
                                                <span class="custom-badge" style="color: #be123c; border-color: #fecdd3; background-color: #fff1f2;">
                                                    <i class="bi bi-fonts"></i> Text: "{{ $decal['text'] ?? '' }}"
                                                </span>
                                            @elseif(($decal['type'] ?? 'text') === 'image')
                                                <span class="custom-badge" style="color: #047857; border-color: #a7f3d0; background-color: #ecfdf5;">
                                                    <i class="bi bi-image"></i> Graphic: {{ $decal['text'] ?? 'Logo Layer' }}
                                                </span>
                                                @if(isset($decal['imageUrl']))
                                                    <a href="{{ $decal['imageUrl'] }}" download="logo_mesh_{{ $loop->iteration }}.png" target="_blank" class="download-btn no-print">
                                                        <i class="bi bi-download"></i> Download Logo
                                                    </a>
                                                @endif
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @elseif(isset($item->savedDesign->design_data['meshStates']))
                            @foreach($item->savedDesign->design_data['meshStates'] as $meshId => $state)
                                @php
                                    $displayName = ucwords(str_replace('_', ' ', str_replace('.obj', '', $meshId)));
                                @endphp
                                <div class="mesh-row">
                                    <span class="mesh-name">{{ $displayName }}</span>
                                    <div class="mesh-customs">
                                        @if(isset($state['color']))
                                            <span class="custom-badge">
                                                <span class="color-preview" style="background-color: {{ $state['color'] }}"></span>
                                                Color: {{ $state['color'] }}
                                            </span>
                                        @endif
                                        
                                        @if(isset($state['pUrl']) && $state['pUrl'])
                                            <span class="custom-badge" style="color: #4f46e5; border-color: #ddd6fe; background-color: #f5f3ff;">
                                                <i class="bi bi-grid-3x3-gap"></i> Texture Overlay
                                            </span>
                                        @endif

                                        @php
                                            $meshDecals = array_filter($item->savedDesign->design_data['decals'] ?? [], function($d) use ($meshId) {
                                                return $d['meshId'] === $meshId || str_replace('.obj', '', $d['meshId']) === str_replace('.obj', '', $meshId);
                                            });
                                        @endphp

                                        @foreach($meshDecals as $decal)
                                            @if(($decal['type'] ?? 'text') === 'text')
                                                <span class="custom-badge" style="color: #be123c; border-color: #fecdd3; background-color: #fff1f2;">
                                                    <i class="bi bi-fonts"></i> Text: "{{ $decal['text'] ?? '' }}"
                                                </span>
                                            @elseif(($decal['type'] ?? 'text') === 'image')
                                                <span class="custom-badge" style="color: #047857; border-color: #a7f3d0; background-color: #ecfdf5;">
                                                    <i class="bi bi-image"></i> Graphic: {{ $decal['text'] ?? 'Logo Layer' }}
                                                </span>
                                                @if(isset($decal['imageUrl']))
                                                    <a href="{{ $decal['imageUrl'] }}" download="logo_mesh_{{ $loop->iteration }}.png" target="_blank" class="download-btn no-print">
                                                        <i class="bi bi-download"></i> Download Logo
                                                    </a>
                                                @endif
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="p-3 text-muted text-center">No mesh-level states customized.</div>
                        @endif
                    </div>
                </div>
            @endif
        @endforeach

        <!-- Invoice Table -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th style="width: 35%">Product</th>
                    <th style="width: 35%">Custom Specifications</th>
                    <th style="width: 10%">Quantity</th>
                    <th style="width: 10%">Unit Price</th>
                    <th style="width: 10%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            <div class="product-info">
                                <img src="{{ $item->image ?: 'https://images.unsplash.com/photo-1551280857-2b9bbe52acf4?w=100&h=100&fit=crop&q=80' }}" alt="{{ $item->product_name }}" class="product-thumb">
                                <div>
                                    <div class="product-name">{{ $item->product_name }}</div>
                                    <div class="product-id">Product ID: #{{ $item->product_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="specs-container">
                                @if($item->size)
                                    <span class="spec-item spec-size">Size: {{ $item->size }}</span>
                                @endif
                                @if($item->custom_name)
                                    <span class="spec-item spec-name">Name: {{ strtoupper($item->custom_name) }}</span>
                                @endif
                                @if($item->custom_number)
                                    <span class="spec-item spec-number">Number: {{ $item->custom_number }}</span>
                                @endif
                                @if($item->savedDesign)
                                    <span class="spec-item spec-design">3D Design: {{ $item->savedDesign->name }}</span>
                                @endif
                            </div>
                        </td>
                        <td style="font-weight: 600;">{{ $item->quantity }} units</td>
                        <td>${{ number_format($item->price, 2) }}</td>
                        <td style="text-align: right; font-weight: 700; color: #0f172a;">
                            ${{ number_format($item->price * $item->quantity, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Bottom Layout -->
        <div class="bottom-section">
            <div class="notes-col">
                <!-- Special Custom Requests -->
                @if($order->notes)
                    <div class="notes-title">Customer Order Notes</div>
                    <div class="notes-content">
                        "{{ $order->notes }}"
                    </div>
                @endif

                <!-- Admin Internal Note -->
                @if($order->admin_note)
                    <div class="notes-title" style="color: #be123c;">Admin Processing / Production Note</div>
                    <div class="notes-content" style="background-color: #fff1f2; border-color: #fecdd3; color: #9f1239; font-style: normal;">
                        {{ $order->admin_note }}
                    </div>
                @endif
            </div>

            <!-- Financial Summary -->
            <div class="totals-col">
                <div class="total-row">
                    <span>Total Quantity:</span>
                    <strong>{{ $order->items->sum('quantity') }} units</strong>
                </div>
                <div class="total-row">
                    <span>Subtotal:</span>
                    <strong>${{ number_format($order->subtotal, 2) }}</strong>
                </div>
                <div class="total-row">
                    <span>Shipping & Handling:</span>
                    <strong>${{ number_format($order->shipping, 2) }}</strong>
                </div>
                <div class="total-row">
                    <span>Tax (8%):</span>
                    <strong>${{ number_format($order->tax, 2) }}</strong>
                </div>
                <div class="total-row grand-total">
                    <span>Grand Total:</span>
                    <span>${{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div>
                <div class="signature-line"></div>
                Prepared By (EAY Sports Representative)
            </div>
            <div>
                <div class="signature-line"></div>
                Accepted By (Customer Signature)
            </div>
        </div>
    </div>

    <!-- 3D Loading & Print Automation script -->
    <script>
        (function() {
            // Count total viewers to render
            const itemIds = [];
            @foreach($order->items as $item)
                @if($item->model_url)
                    itemIds.push({
                        id: '{{ $item->id }}',
                        url: '{{ $item->model_url }}',
                        meshStates: @json($item->savedDesign->design_data['meshStates'] ?? []),
                        layersMetadata: @json($item->layers_metadata ?? []),
                        decals: @json($item->savedDesign->design_data['decals'] ?? []),
                        finish: '{{ $item->savedDesign->design_data['materialFinish'] ?? 'matte' }}',
                        preset: '{{ $item->savedDesign->design_data['lightingPreset'] ?? 'city' }}'
                    });
                @endif
            @endforeach

            const totalViewers = itemIds.length * 4;
            let loadedViewers = 0;

            function checkAllViewersLoaded() {
                loadedViewers++;
                if (loadedViewers >= totalViewers) {
                    // Small delay to ensure the canvas has fully finished rendering its double buffer
                    setTimeout(function() {
                        window.print();
                        window.onafterprint = function() {
                            window.close();
                        };
                    }, 1500);
                }
            }

            if (totalViewers === 0) {
                window.print();
                window.onafterprint = function() {
                    window.close();
                };
                return;
            }

            // Initialize all Three.js containers
            itemIds.forEach(function(item) {
                init3DPreview(item, 'front');
                init3DPreview(item, 'back');
                init3DPreview(item, 'left');
                init3DPreview(item, 'right');
            });

            function init3DPreview(item, side) {
                const containerId = 'three-' + side + '-' + item.id;
                const container = document.getElementById(containerId);
                if (!container) return;

                const scene = new THREE.Scene();
                scene.background = new THREE.Color('#eef2f3');

                const camera = new THREE.PerspectiveCamera(40, container.clientWidth / container.clientHeight, 0.1, 100);
                // Adjust camera position based on side
                if (side === 'front') {
                    camera.position.set(0, 0.4, 2.5);
                } else if (side === 'back') {
                    camera.position.set(0, 0.4, -2.5);
                } else if (side === 'left') {
                    camera.position.set(-2.5, 0.4, 0);
                } else if (side === 'right') {
                    camera.position.set(2.5, 0.4, 0);
                }

                const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, preserveDrawingBuffer: true });
                renderer.setSize(container.clientWidth, container.clientHeight);
                renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
                renderer.outputEncoding = THREE.sRGBEncoding;
                renderer.toneMapping = THREE.ACESFilmicToneMapping;
                renderer.toneMappingExposure = 1.0;
                renderer.physicallyCorrectLights = true;
                container.appendChild(renderer.domElement);

                // Setup lighting and environment map
                const pmremGenerator = new THREE.PMREMGenerator(renderer);
                pmremGenerator.compileEquirectangularShader();

                const presetUrls = {
                    city: 'https://rawcdn.githack.com/pmndrs/3d-assets/aa6948a84141cf759e5f212d62b1f33f954ec575/hdri/potsdamer_platz_1k.hdr',
                    studio: 'https://rawcdn.githack.com/pmndrs/3d-assets/aa6948a84141cf759e5f212d62b1f33f954ec575/hdri/studio_small_03_1k.hdr'
                };
                const hdriUrl = presetUrls[item.preset] || presetUrls.city;

                new THREE.RGBELoader().load(hdriUrl, function(texture) {
                    const envMap = pmremGenerator.fromEquirectangular(texture).texture;
                    scene.environment = envMap;
                    texture.dispose();
                    pmremGenerator.dispose();
                });

                 const intensityFactor = (item.preset === 'night') ? 0.2 : (item.preset === 'studio') ? 1.5 : 1.0;

                 const ambientLight = new THREE.AmbientLight('#ffffff', 0.8 * intensityFactor);
                 scene.add(ambientLight);

                 const keyLight = new THREE.DirectionalLight('#ffffff', 0.8 * intensityFactor);
                 keyLight.position.set(5, 5, 5);
                 scene.add(keyLight);

                 const fillLight = new THREE.DirectionalLight('#ffffff', 0.4 * intensityFactor);
                 fillLight.position.set(-5, 5, 5);
                 scene.add(fillLight);

                 const backLight = new THREE.DirectionalLight('#ffffff', 0.3 * intensityFactor);
                 backLight.position.set(0, 5, -5);
                 scene.add(backLight);

                const controls = new THREE.OrbitControls(camera, renderer.domElement);
                controls.enableDamping = false;
                controls.enableZoom = false;
                controls.enablePan = false;
                controls.enableRotate = false;

                const loader = new THREE.GLTFLoader();
                loader.load(item.url, function(gltf) {
                    const loaderLabel = container.querySelector('.canvas-loader');
                    if (loaderLabel) loaderLabel.remove();

                    const model = gltf.scene;
                    const scale = 1.8;
                    model.scale.set(scale, scale, scale);

                    model.updateMatrixWorld(true);
                    const scaledBox = new THREE.Box3().setFromObject(model);
                    const scaledCenter = scaledBox.getCenter(new THREE.Vector3());
                    model.position.sub(scaledCenter);

                    const meshesMap = {};
                    model.traverse(function(node) {
                        if (node.isMesh) {
                            meshesMap[node.name] = node;
                            meshesMap[node.name + '.obj'] = node;
                            node.material.side = THREE.DoubleSide;

                            const meta = item.layersMetadata[node.name] || item.layersMetadata[node.name + '.obj'] || {};
                            const stateKey = meta.merge_parent || node.name;
                            const parentMeta = item.layersMetadata[stateKey] || {};
                            const isLocked = meta.is_locked || parentMeta.is_locked;
                            if (isLocked) {
                                node.material.side = THREE.DoubleSide;
                                return;
                            }

                            const state = item.meshStates[stateKey] || item.meshStates[stateKey + '.obj'];
                            if (state) {
                                const color = state.color || '#ffffff';
                                const mat = node.material.clone();
                                mat.color.set(color).convertSRGBToLinear();
                                mat.roughness = item.finish === 'gloss' ? 0.1 : item.finish === 'metallic' ? 0.2 : 0.8;
                                mat.metalness = item.finish === 'metallic' ? 0.8 : 0.0;
                                mat.side = THREE.DoubleSide;

                                mat.onBeforeCompile = function(shader) {
                                    shader.fragmentShader = shader.fragmentShader.replace(
                                        'vec4 diffuseColor = vec4( diffuse, opacity );',
                                        'vec3 finalColor = diffuse;\n' +
                                        'if (!gl_FrontFacing) {\n' +
                                        '  finalColor = vec3(0.92, 0.92, 0.92);\n' +
                                        '}\n' +
                                        'vec4 diffuseColor = vec4( finalColor, opacity );'
                                    );
                                };

                                if (state.pUrl) {
                                    new THREE.TextureLoader().load(state.pUrl, function(tex) {
                                        tex.wrapS = tex.wrapT = THREE.RepeatWrapping;
                                        tex.repeat.set(6, 6);
                                        mat.map = tex;
                                        mat.needsUpdate = true;
                                    });
                                }

                                if (state.isGrad && state.grad1 && state.grad2) {
                                    const canvas = document.createElement('canvas');
                                    canvas.width = 16;
                                    canvas.height = 256;
                                    const ctx = canvas.getContext('2d');
                                    const grad = ctx.createLinearGradient(0, 0, 0, 256);
                                    grad.addColorStop(0, state.grad1);
                                    grad.addColorStop(1, state.grad2);
                                    ctx.fillStyle = grad;
                                    ctx.fillRect(0, 0, 16, 256);
                                    mat.map = new THREE.CanvasTexture(canvas);
                                }
                                node.material = mat;
                            }
                        }
                    });

                    scene.add(model);
                    model.updateMatrixWorld(true);

                    // Add Decals to the Submeshes
                    let decalsRemaining = item.decals.length;
                    
                    if (decalsRemaining === 0) {
                        renderer.render(scene, camera);
                        checkAllViewersLoaded();
                    }

                    item.decals.forEach(function(d) {
                        if (!d.worldPoint || !d.worldNormal) {
                            decalsRemaining--;
                            if (decalsRemaining === 0) {
                                renderer.render(scene, camera);
                                checkAllViewersLoaded();
                            }
                            return;
                        }

                        // Find all meshes in the same merged group
                        const targetMeshes = [];
                        model.traverse(function(m) {
                            if (m.isMesh) {
                                const mMeta = item.layersMetadata[m.name] || item.layersMetadata[m.name + '.obj'] || {};
                                const dMeta = item.layersMetadata[d.meshId] || item.layersMetadata[d.meshId + '.obj'] || {};
                                
                                let isTarget = false;
                                if (m.name === d.meshId || m.name + '.obj' === d.meshId) {
                                    isTarget = true;
                                } else if (mMeta.merge_parent === d.meshId || mMeta.merge_parent + '.obj' === d.meshId) {
                                    isTarget = true;
                                } else if (dMeta.merge_parent === m.name || dMeta.merge_parent + '.obj' === m.name) {
                                    isTarget = true;
                                } else if (mMeta.merge_parent && mMeta.merge_parent === dMeta.merge_parent) {
                                    isTarget = true;
                                }
                                
                                if (isTarget) {
                                    targetMeshes.push(m);
                                }
                            }
                        });

                        if (targetMeshes.length === 0) {
                            decalsRemaining--;
                            if (decalsRemaining === 0) {
                                renderer.render(scene, camera);
                                checkAllViewersLoaded();
                            }
                            return;
                        }

                        try {
                             let canvas;
                             if (d.type === 'pattern' && d.imageUrl) {
                                 canvas = document.createElement('canvas');
                                 canvas.width = 1024;
                                 canvas.height = 1024;
                                 const ctx = canvas.getContext('2d');
                                 ctx.clearRect(0, 0, 1024, 1024);
                                 const img = new Image();
                                 if (d.imageUrl && !d.imageUrl.startsWith('data:')) {
                                     img.crossOrigin = 'anonymous';
                                 }
                                 img.onload = function() {
                                     ctx.clearRect(0, 0, 1024, 1024);
                                     ctx.drawImage(img, 0, 0, 1024, 1024);
                                     try {
                                         var imgData = ctx.getImageData(0, 0, 1024, 1024);
                                         var data = imgData.data;
                                         var hasAlphaChannel = false;
                                         for (var i = 3; i < data.length; i += 4) {
                                             if (data[i] < 220) { hasAlphaChannel = true; break; }
                                         }
                                         var isOriginal = !d.color || d.color === 'original';
                                         var tintR = 255, tintG = 255, tintB = 255;
                                         if (!isOriginal && d.color) {
                                             var tc = new THREE.Color(d.color);
                                             tintR = Math.round(tc.r * 255);
                                             tintG = Math.round(tc.g * 255);
                                             tintB = Math.round(tc.b * 255);
                                         }
                                          if (hasAlphaChannel) {
                                              var isInvertedPattern = d.imageUrl.indexOf('checker.png') !== -1 || d.imageUrl.indexOf('checker') !== -1;
                                              if (isInvertedPattern) {
                                                  for (var i = 0; i < data.length; i += 4) {
                                                      var alpha = data[i + 3];
                                                      if (alpha > 50) {
                                                          data[i + 3] = 0; // Transparent background
                                                      } else {
                                                          data[i] = tintR;
                                                          data[i + 1] = tintG;
                                                          data[i + 2] = tintB;
                                                          data[i + 3] = 255; // Opaque pattern
                                                      }
                                                  }
                                              } else {
                                                  if (!isOriginal) {
                                                      for (var i = 0; i < data.length; i += 4) {
                                                          if (data[i + 3] > 0) {
                                                              data[i] = tintR;
                                                              data[i + 1] = tintG;
                                                              data[i + 2] = tintB;
                                                          }
                                                      }
                                                  }
                                              }
                                          } else {
                                             var r0 = data[0], g0 = data[1], b0 = data[2];
                                             var isBgWhite = (r0 + g0 + b0) / 3 > 127;
                                             for (var i = 0; i < data.length; i += 4) {
                                                 var r = data[i], g = data[i+1], b = data[i+2], alpha = data[i+3];
                                                 if (alpha === 0) continue;
                                                 var val = (r + g + b) / 3;
                                                 if (isBgWhite) {
                                                     var alphaFactor;
                                                     if (val > 235) { alphaFactor = 0; }
                                                     else if (val < 180) { alphaFactor = 255; }
                                                     else { alphaFactor = Math.round(255 * (1.0 - (val - 180) / (235 - 180))); }
                                                     data[i + 3] = alphaFactor;
                                                     if (!isOriginal) { data[i] = tintR; data[i+1] = tintG; data[i+2] = tintB; }
                                                 } else {
                                                     var alphaFactor;
                                                     if (val < 20) { alphaFactor = 0; }
                                                     else if (val > 75) { alphaFactor = 255; }
                                                     else { alphaFactor = Math.round(255 * ((val - 20) / (75 - 20))); }
                                                     data[i + 3] = alphaFactor;
                                                     if (!isOriginal) { data[i] = tintR; data[i+1] = tintG; data[i+2] = tintB; }
                                                 }
                                             }
                                         }
                                         ctx.putImageData(imgData, 0, 0);
                                     } catch(e) {
                                         console.error("Pattern processing error:", e);
                                         ctx.clearRect(0, 0, 1024, 1024);
                                         ctx.drawImage(img, 0, 0, 1024, 1024);
                                     }
                                     decalTex.needsUpdate = true;
                                     decalsRemaining--;
                                     if (decalsRemaining === 0) {
                                         setTimeout(function() {
                                             renderer.render(scene, camera);
                                             checkAllViewersLoaded();
                                         }, 100);
                                     }
                                 };
                                 img.src = d.imageUrl;
                             } else if (d.type === 'image' && d.imageUrl) {
                                 canvas = document.createElement('canvas');
                                 canvas.width = 512;
                                 canvas.height = 512;
                                 const ctx = canvas.getContext('2d');
                                 ctx.clearRect(0, 0, 512, 512);
                                 const img = new Image();
                                 if (d.imageUrl && !d.imageUrl.startsWith('data:')) {
                                     img.crossOrigin = 'anonymous';
                                 }
                                 img.onload = function() {
                                     ctx.clearRect(0, 0, 512, 512);
                                     const scaleFactor = Math.min(512 / img.width, 512 / img.height) * 0.85;
                                     const w = img.width * scaleFactor;
                                     const h = img.height * scaleFactor;
                                     ctx.drawImage(img, (512 - w) / 2, (512 - h) / 2, w, h);
                                     decalTex.needsUpdate = true;
                                     decalsRemaining--;
                                     if (decalsRemaining === 0) {
                                         setTimeout(function() {
                                             renderer.render(scene, camera);
                                             checkAllViewersLoaded();
                                         }, 100);
                                     }
                                 };
                                 img.src = d.imageUrl;
                             } else {
                                canvas = document.createElement('canvas');
                                canvas.width = 1024;
                                canvas.height = 256;
                                const ctx = canvas.getContext('2d');
                                ctx.clearRect(0, 0, 1024, 256);
                                
                                const fontSize = 120;
                                ctx.font = 'bold ' + fontSize + 'px ' + (d.font || 'Arial');
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'middle';
                                
                                const x = 512;
                                const y = 128;
                                
                                const drawPass = function(isStroke, strokeWidth, strokeColor) {
                                    ctx.fillStyle = strokeColor || d.color || '#ffffff';
                                    ctx.strokeStyle = strokeColor;
                                    ctx.lineWidth = strokeWidth * 2;
                                    ctx.lineJoin = 'round';
                                    ctx.lineCap = 'round';
                                    
                                    if (d.effect === 'arch') {
                                        const intensity = d.effectIntensity || 0.5;
                                        const radius = 400 / intensity;
                                        const characters = d.text.split('');
                                        const totalWidth = ctx.measureText(d.text).width;
                                        const anglePerPixel = 1 / radius;
                                        const totalAngle = totalWidth * anglePerPixel;
                                        
                                        let currentAngle = -totalAngle / 2;
                                        
                                        ctx.save();
                                        ctx.translate(x, y + radius - 20);
                                        characters.forEach(function(char) {
                                            const charWidth = ctx.measureText(char).width;
                                            const charAngle = charWidth * anglePerPixel;
                                            ctx.save();
                                            ctx.rotate(currentAngle + charAngle / 2);
                                            if (isStroke) ctx.strokeText(char, 0, -radius);
                                            else ctx.fillText(char, 0, -radius);
                                            ctx.restore();
                                            currentAngle += charAngle;
                                        });
                                        ctx.restore();
                                    } else {
                                        if (isStroke) ctx.strokeText(d.text, x, y - 10);
                                        else ctx.fillText(d.text, x, y - 10);
                                    }
                                };
                                
                                if (d.outline2Width > 0) {
                                    drawPass(true, (d.outline1Width || 0) + (d.outline2Width || 0), d.outline2Color);
                                }
                                if (d.outline1Width > 0) {
                                    drawPass(true, d.outline1Width, d.outline1Color);
                                }
                                drawPass(false);
                            }
                            
                            const decalTex = new THREE.CanvasTexture(canvas);
                            decalTex.colorSpace = THREE.SRGBColorSpace;
                            
                            if (d.type === 'pattern') {
                                decalTex.wrapS = THREE.RepeatWrapping;
                                decalTex.wrapT = THREE.RepeatWrapping;
                            }
                            
                            const point = new THREE.Vector3().fromArray(d.worldPoint);
                            const normal = new THREE.Vector3().fromArray(d.worldNormal);
                            
                            const up = Math.abs(normal.y) < 0.95 ? new THREE.Vector3(0, 1, 0) : new THREE.Vector3(1, 0, 0);
                            const right = new THREE.Vector3().crossVectors(up, normal).normalize();
                            const newUp = new THREE.Vector3().crossVectors(normal, right).normalize();
                            const m4 = new THREE.Matrix4().makeBasis(right, newUp, normal);
                            const rotation = d.rotation || 0;
                            const mRotation = new THREE.Matrix4().makeRotationZ(rotation);
                            m4.multiply(mRotation);
                            const orientation = new THREE.Euler().setFromRotationMatrix(m4);
                            
                            var sx, sy, decalSize;
                            if (d.type === 'pattern') {
                                sx = d.patternCoverageX || (d.decalScaleX !== undefined ? d.decalScaleX : 1.0);
                                sy = d.patternCoverageY || (d.decalScaleY !== undefined ? d.decalScaleY : 1.0);
                                decalSize = new THREE.Vector3(sx, sy, 3.0);
                            } else if (d.type === 'image') {
                                sx = d.decalScaleX !== undefined ? d.decalScaleX : (d.decalScale || 0.15);
                                sy = d.decalScaleY !== undefined ? d.decalScaleY : (d.decalScale || 0.15);
                                decalSize = new THREE.Vector3(sx, sy, 0.3);
                            } else {
                                sx = d.decalScaleX !== undefined ? d.decalScaleX : (d.decalScale || 0.15);
                                sy = d.decalScaleY !== undefined ? d.decalScaleY : (d.decalScale || 0.15);
                                decalSize = new THREE.Vector3(sx, sy * 0.25, 0.3);
                            }
                                
                            const DecalGeo = THREE.DecalGeometry || window.DecalGeometry;
                            
                            targetMeshes.forEach(function(targetMesh) {
                                try {
                                    const geo = new DecalGeo(targetMesh, point, orientation, decalSize);
                                    
                                    // Align geometry to submesh local space
                                    const localMatrix = targetMesh.matrixWorld.clone().invert();
                                    geo.applyMatrix4(localMatrix);

                                    var mat;
                                    if (d.type === 'pattern') {
                                        var repeatFactor = 8.0 / Math.max(0.05, d.decalScale || 0.8);
                                        var quaternion = new THREE.Quaternion().setFromEuler(orientation);
                                        var targetMeshMatrixWorldInv = targetMesh.matrixWorld.clone().invert();
                                        var decalMatrixWorld = new THREE.Matrix4().compose(point, quaternion, decalSize);
                                        var decalMatrixLocal = targetMeshMatrixWorldInv.clone().multiply(decalMatrixWorld);
                                        var decalMatrixLocalInv = decalMatrixLocal.clone().invert();
                                        
                                        mat = new THREE.ShaderMaterial({
                                            uniforms: {
                                                map: { value: decalTex },
                                                uRepeat: { value: repeatFactor },
                                                uDecalMatrixInv: { value: decalMatrixLocalInv },
                                                uDecalSize: { value: decalSize },
                                                uFadeTop: { value: d.pFadeTop || 0.0 },
                                                uFadeBottom: { value: d.pFadeBottom || 0.0 },
                                                uFadeLeft: { value: d.pFadeLeft || 0.0 },
                                                uFadeRight: { value: d.pFadeRight || 0.0 },
                                            },
                                            vertexShader: [
                                                'uniform mat4 uDecalMatrixInv;',
                                                'varying vec2 vUv;',
                                                'varying vec3 vDecalLocalPos;',
                                                'varying vec3 vDecalLocalNormal;',
                                                'void main() {',
                                                '  vUv = uv;',
                                                '  vDecalLocalPos = (uDecalMatrixInv * vec4(position, 1.0)).xyz;',
                                                '  vDecalLocalNormal = normalize((uDecalMatrixInv * vec4(normal, 0.0)).xyz);',
                                                '  gl_Position = projectionMatrix * viewMatrix * modelMatrix * vec4(position, 1.0);',
                                                '}'
                                            ].join('\n'),
                                            fragmentShader: [
                                                'uniform sampler2D map;',
                                                'uniform float uRepeat;',
                                                'uniform vec3 uDecalSize;',
                                                'uniform float uFadeTop;',
                                                'uniform float uFadeBottom;',
                                                'uniform float uFadeLeft;',
                                                'uniform float uFadeRight;',
                                                'varying vec2 vUv;',
                                                'varying vec3 vDecalLocalPos;',
                                                'varying vec3 vDecalLocalNormal;',
                                                'void main() {',
                                                '  vec3 unscaledPos = vDecalLocalPos * uDecalSize;',
                                                '  vec3 unscaledNormal = normalize(vDecalLocalNormal * uDecalSize);',
                                                '  vec3 blending = abs(unscaledNormal);',
                                                '  blending = pow(blending, vec3(16.0));',
                                                '  blending = blending / (blending.x + blending.y + blending.z);',
                                                '  vec2 uvX = unscaledPos.zy;',
                                                '  vec2 uvY = unscaledPos.xz;',
                                                '  vec2 uvZ = unscaledPos.xy;',
                                                '  vec2 tiledUvX = uvX * uRepeat;',
                                                '  vec2 tiledUvY = uvY * uRepeat;',
                                                '  vec2 tiledUvZ = uvZ * uRepeat;',
                                                '  vec4 colX = texture2D(map, tiledUvX);',
                                                '  vec4 colY = texture2D(map, tiledUvY);',
                                                '  vec4 colZ = texture2D(map, tiledUvZ);',
                                                '  vec4 texColor;',
                                                '  if (blending.z >= blending.x && blending.z >= blending.y) {',
                                                '    texColor = colZ;',
                                                '  } else if (blending.x >= blending.y && blending.x >= blending.z) {',
                                                '    texColor = colX;',
                                                '  } else {',
                                                '    texColor = colY;',
                                                '  }',
                                                '  float alpha = texColor.a;',
                                                '  if (uFadeTop > 0.001) { alpha *= smoothstep(1.0, 1.0 - uFadeTop, vUv.y); }',
                                                '  if (uFadeBottom > 0.001) { alpha *= smoothstep(0.0, uFadeBottom, vUv.y); }',
                                                '  if (uFadeLeft > 0.001) { alpha *= smoothstep(0.0, uFadeLeft, 1.0 - vUv.x); }',
                                                '  if (uFadeRight > 0.001) { alpha *= smoothstep(1.0, 1.0 - uFadeRight, 1.0 - vUv.x); }',
                                                '  gl_FragColor = vec4(texColor.rgb, alpha);',
                                                '}'
                                            ].join('\n'),
                                            transparent: true,
                                            depthTest: true,
                                            depthWrite: false,
                                            polygonOffset: true,
                                            polygonOffsetFactor: d.type === 'pattern' ? -1.0 : (d.type === 'image' ? -2.0 : -3.0),
                                            polygonOffsetUnits: d.type === 'pattern' ? -1.0 : (d.type === 'image' ? -2.0 : -3.0),
                                        });
                                    } else {
                                        mat = new THREE.MeshBasicMaterial({
                                            map: decalTex,
                                            transparent: true,
                                            depthTest: true,
                                            depthWrite: false,
                                            polygonOffset: true,
                                            polygonOffsetFactor: d.type === 'pattern' ? -1.0 : (d.type === 'image' ? -2.0 : -3.0),
                                            polygonOffsetUnits: d.type === 'pattern' ? -1.0 : (d.type === 'image' ? -2.0 : -3.0),
                                        });
                                    }
                                    
                                    const decalMesh = new THREE.Mesh(geo, mat);
                                    decalMesh.renderOrder = d.type === 'pattern' ? 997 : (d.type === 'image' ? 998 : 999);
                                    targetMesh.add(decalMesh);
                                } catch(subMeshDecalErr) {
                                    console.error("Error loading decal onto submesh:", subMeshDecalErr);
                                }
                            });

                            const isAsyncDecal = (d.type === 'pattern' && d.imageUrl) || (d.type === 'image' && d.imageUrl);
                            if (!isAsyncDecal) {
                                decalsRemaining--;
                                if (decalsRemaining === 0) {
                                    setTimeout(function() {
                                        renderer.render(scene, camera);
                                        checkAllViewersLoaded();
                                    }, 100);
                                }
                            }
                        } catch(decalErr) {
                            console.error("Error loading decal onto 3D print model:", decalErr);
                            decalsRemaining--;
                            if (decalsRemaining === 0) {
                                renderer.render(scene, camera);
                                checkAllViewersLoaded();
                            }
                        }
                    });
                    
                }, undefined, function(error) {
                    console.error("Error loading 3D GLB model:", error);
                    container.innerHTML = '<div class="h-100 d-flex align-items-center justify-content-center text-muted" style="font-size: 11px;">Error loading model</div>';
                    checkAllViewersLoaded();
                });
            }
        })();
    </script>
</body>
</html>
