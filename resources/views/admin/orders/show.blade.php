@extends('admin.master')

@section('title', 'Retail Order Details')

@section('header', 'Retail Order Breakdown')

@section('content')
    <!-- Three.js Libraries for 3D Preview -->
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/RGBELoader.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/geometries/DecalGeometry.js"></script>

    <div class="mb-4 d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-chevron-left me-1"></i> Back to Orders Log
        </a>
        <a href="{{ route('admin.orders.pdf', $order->id) }}" target="_blank" class="btn btn-sm btn-danger d-flex align-items-center gap-1.5 fw-bold shadow-sm">
            <i class="bi bi-file-earmark-pdf-fill"></i> Generate PDF Invoice
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

    <div class="row g-4">
        <!-- Order Items Breakdown -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
                <h5 class="fw-bold border-bottom pb-3 mb-4 text-dark uppercase tracking-wide">Products Invoice</h5>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-3 py-3">Product</th>
                                <th class="py-3">Specifications</th>
                                <th class="py-3">Quantity</th>
                                <th class="py-3">Unit Price</th>
                                <th class="text-end pe-3 py-3">Row Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="ps-3 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-1 me-3 d-flex align-items-center justify-content-center border" style="width: 50px; height: 50px; overflow:hidden;">
                                                <img src="{{ $item->image ?: 'https://images.unsplash.com/photo-1551280857-2b9bbe52acf4?w=600&h=400&fit=crop&q=80' }}" alt="{{ $item->product_name }}" class="img-fluid rounded" style="object-fit: cover; width: 100%; height: 100%;">
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark text-truncate" style="max-width: 180px;">{{ $item->product_name }}</div>
                                                <small class="text-muted">Product ID: #{{ $item->product_id ?: 'N/A' }}</small>
                                            </div>
                                        </div>
                                        @if($item->savedDesign)
                                            <div class="mt-2">
                                                <span class="badge bg-primary rounded px-2 py-1 text-white" style="font-size: 9px; font-weight: 600; text-shadow: none;">
                                                    <i class="bi bi-cube-fill me-1"></i> Custom 3D Design Attached
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1.5 align-items-start">
                                            @if($item->size)
                                                <span class="border fw-bold text-center" style="color: #4f46e5 !important; background-color: #f5f3ff !important; border-color: #ddd6fe !important; font-size: 11px; padding: 6px 12px; display: inline-block; border-radius: 50px; min-width: 90px; text-shadow: none;">Size: {{ $item->size }}</span>
                                            @endif
                                            @if($item->color)
                                                <span class="border fw-bold text-center" style="color: #4b5563 !important; background-color: #f3f4f6 !important; border-color: #e5e7eb !important; font-size: 11px; padding: 6px 12px; display: inline-block; border-radius: 50px; min-width: 90px; text-shadow: none;">Color: {{ $item->color }}</span>
                                            @endif
                                            @if($item->custom_name)
                                                <span class="border fw-bold text-center" style="color: #be123c !important; background-color: #fff1f2 !important; border-color: #fecdd3 !important; font-size: 11px; padding: 6px 12px; display: inline-block; border-radius: 50px; min-width: 90px; text-shadow: none;">Name: {{ strtoupper($item->custom_name) }}</span>
                                            @endif
                                            @if($item->custom_number)
                                                <span class="border fw-bold text-center" style="color: #047857 !important; background-color: #ecfdf5 !important; border-color: #a7f3d0 !important; font-size: 11px; padding: 6px 12px; display: inline-block; border-radius: 50px; min-width: 90px; text-shadow: none;">No: {{ $item->custom_number }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="fw-semibold text-dark">{{ $item->quantity }} units</td>
                                    <td>${{ number_format($item->price, 2) }}</td>
                                    <td class="text-end pe-3 fw-bold text-dark">${{ number_format($item->price * $item->quantity, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Customized 3D Design Details Section -->
            @php
                $hasCustomDesigns = false;
                foreach($order->items as $item) {
                    if($item->savedDesign) {
                        $hasCustomDesigns = true;
                        break;
                    }
                }
            @endphp

            @if($hasCustomDesigns)
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
                    <h5 class="fw-bold border-bottom pb-3 mb-4 text-dark uppercase tracking-wide">
                        <i class="bi bi-palette-fill text-primary me-2"></i> Custom 3D Design Specifications
                    </h5>
                    
                    @foreach($order->items as $item)
                        @if($item->savedDesign)
                            <div class="mb-5 border-bottom pb-4 last-border-0">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; font-size: 12px; font-weight: bold;">
                                        {{ $loop->iteration }}
                                    </div>
                                    <h6 class="fw-bold text-dark mb-0">{{ $item->product_name }} - Custom Design Specifications</h6>
                                </div>

                                <div class="row g-4 align-items-stretch">
                                    <!-- 3D Interactive Model Viewer -->
                                    @if($item->model_url)
                                        <div class="col-md-6">
                                            <div class="card border bg-light h-100 p-3 rounded-3 shadow-none">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-secondary fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                                                        <i class="bi bi-3d-rotate me-1"></i> 3D Interactive Viewer
                                                    </span>
                                                    <small class="text-muted" style="font-size: 9px;">Drag to rotate | Scroll to zoom</small>
                                                </div>
                                                <div id="three-container-{{ $item->id }}" style="width: 100%; height: 350px; background: #eef2f3; position: relative;" class="rounded border bg-gradient">
                                                    <div class="position-absolute top-50 start-50 translate-middle text-muted small spinner-label" style="font-size: 11px;">
                                                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                                        Loading 3D Workspace...
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <script>
                                                (function() {
                                                     const container = document.getElementById('three-container-{{ $item->id }}');
                                                     const modelUrl = '{{ $item->model_url }}';
                                                     const meshStates = @json($item->savedDesign->design_data['meshStates'] ?? []);
                                                     const layersMetadata = @json($item->layers_metadata ?? []);
                                                     const decals = @json($item->savedDesign->design_data['decals'] ?? []);
                                                     const materialFinish = '{{ $item->savedDesign->design_data['materialFinish'] ?? 'matte' }}';
                                                     const lightingPreset = '{{ $item->savedDesign->design_data['lightingPreset'] ?? 'city' }}';

                                                     const whiteTex = new THREE.CanvasTexture(document.createElement('canvas'));
                                                     const scene = new THREE.Scene();
                                                     scene.background = new THREE.Color('#eef2f3');

                                                    const camera = new THREE.PerspectiveCamera(40, container.clientWidth / container.clientHeight, 0.1, 100);
                                                    camera.position.set(0, 0.4, 2.8);

                                                    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, preserveDrawingBuffer: true });
                                                    renderer.setSize(container.clientWidth, container.clientHeight);
                                                    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
                                                    renderer.outputEncoding = THREE.sRGBEncoding;
                                                    renderer.toneMapping = THREE.ACESFilmicToneMapping;
                                                    renderer.toneMappingExposure = 1.35; // Increased exposure for bright white colors
                                                    renderer.physicallyCorrectLights = true;
                                                    container.appendChild(renderer.domElement);

                                                    // Load Neutral Studio HDRi Environment Map to avoid warm yellow/sunset shading
                                                    const pmremGenerator = new THREE.PMREMGenerator(renderer);
                                                    pmremGenerator.compileEquirectangularShader();
                                                    const hdriUrl = 'https://raw.githubusercontent.com/mrdoob/three.js/dev/examples/textures/equirectangular/royal_esplanade_1k.hdr';
                                                    new THREE.RGBELoader().load(hdriUrl, function(texture) {
                                                         const envMap = pmremGenerator.fromEquirectangular(texture).texture;
                                                         scene.environment = envMap;
                                                         texture.dispose();
                                                         pmremGenerator.dispose();
                                                    });

                                                    // Lighting exactly matching builder: ambientLight(1.2) + spotLight(2.2) + directionalLight(0.8)
                                                    const isNight = lightingPreset === 'night';
                                                    const isStudio = lightingPreset === 'studio';
                                                    scene.add(new THREE.AmbientLight('#ffffff', isNight ? 0.3 : 1.2));
                                                    var spotLight = new THREE.SpotLight('#ffffff', isStudio ? 3.0 : 2.2);
                                                    spotLight.position.set(10, 15, 10);
                                                    spotLight.angle = 0.3;
                                                    spotLight.penumbra = 1;
                                                    scene.add(spotLight);
                                                    var dirLight = new THREE.DirectionalLight('#ffffff', isNight ? 0.15 : 0.8);
                                                    dirLight.position.set(-5, 5, -5);
                                                    scene.add(dirLight);

                                                    const controls = new THREE.OrbitControls(camera, renderer.domElement);
                                                    controls.enableDamping = true;
                                                    controls.dampingFactor = 0.05;
                                                    controls.maxPolarAngle = Math.PI / 1.8;
                                                    controls.minPolarAngle = Math.PI / 4;
                                                    controls.minDistance = 1.5;
                                                    controls.maxDistance = 6.0;

                                                    const loader = new THREE.GLTFLoader();
                                                    loader.load(modelUrl, function(gltf) {
                                                        const spinner = container.querySelector('.spinner-label');
                                                        if (spinner) spinner.remove();

                                                        const model = gltf.scene;
                                                        
                                                        // Scale the model first (using fixed 1.8 scale to match the builder exactly)
                                                        const scale = 1.8;
                                                        model.scale.set(scale, scale, scale);
                                                        
                                                        // Then center it
                                                        model.updateMatrixWorld(true);
                                                        const scaledBox = new THREE.Box3().setFromObject(model);
                                                        const scaledCenter = scaledBox.getCenter(new THREE.Vector3());
                                                        model.position.sub(scaledCenter);
                                                        
                                                        model.traverse(function(node) {
                                                            if (node.isMesh) {
                                                                
                                                                if (node.material) {
                                                                    node.material.side = THREE.DoubleSide;
                                                                }

                                                                 const meta = layersMetadata[node.name] || layersMetadata[node.name + '.obj'] || {};
                                                                 const stateKey = meta.merge_parent || node.name;
                                                                 const parentMeta = layersMetadata[stateKey] || layersMetadata[stateKey + '.obj'] || {};
                                                                 const isLocked = meta.is_locked || parentMeta.is_locked;
                                                                 if (isLocked) {
                                                                     return;
                                                                 }
                                                                  const state = meshStates[stateKey] || meshStates[stateKey + '.obj'];
                                                                  if (state) {
                                                                      const mat = node.material.clone();
                                                                      
                                                                       mat.userData.uniforms = {
                                                                           uColor: { value: new THREE.Color(state.color || '#ffffff').convertSRGBToLinear() },
                                                                           uIsGradient: { value: state.isGrad ? 1.0 : 0.0 },
                                                                           uColor1: { value: new THREE.Color(state.grad1 || '#ffffff').convertSRGBToLinear() },
                                                                           uColor2: { value: new THREE.Color(state.grad2 || '#ffffff').convertSRGBToLinear() },
                                                                           uHasPattern: { value: 0.0 },
                                                                           uPatternColor: { value: new THREE.Color(state.pColor || '#ffffff').convertSRGBToLinear() },
                                                                           uPatternTexture: { value: whiteTex },
                                                                           uPatternType: { value: 0.0 },
                                                                           uMinY: { value: 0 },
                                                                           uMaxY: { value: 1 },
                                                                           uPatternSize: { value: state.pSize || 0.2 },
                                                                           uPatternOffset: { value: new THREE.Vector3(state.pOffsetX || 0.0, state.pOffsetY || 0.0, state.pOffsetZ || 0.0) },
                                                                           uPatternRotation: { value: state.pRotation || 0.0 },
                                                                           uPatternMinY: { value: state.pMinY === undefined ? 0.0 : state.pMinY },
                                                                           uPatternMaxY: { value: state.pMaxY === undefined ? 1.0 : state.pMaxY },
                                                                           uPatternMappingMode: { value: state.pMappingMode === undefined ? 0.0 : parseFloat(state.pMappingMode) },
                                                                           uLocalMatrix: { value: node.matrix }
                                                                       };

                                                                      mat.onBeforeCompile = function(shader) {
                                                                          Object.assign(shader.uniforms, mat.userData.uniforms);

                                                                          shader.vertexShader = [
                                                                              'uniform mat4 uLocalMatrix;',
                                                                              'varying vec3 vLocalPos;',
                                                                              'varying vec3 vModelPos;',
                                                                              'varying vec3 vModelNormal;',
                                                                              'varying vec2 vUv;',
                                                                              shader.vertexShader
                                                                          ].join('\n').replace(
                                                                              '#include <begin_vertex>',
                                                                              '#include <begin_vertex>\n' +
                                                                              'vLocalPos = position;\n' +
                                                                              'vModelPos = (uLocalMatrix * vec4(position, 1.0)).xyz;\n' +
                                                                              'vModelNormal = normalize((uLocalMatrix * vec4(normal, 0.0)).xyz);\n' +
                                                                              'vUv = uv;'
                                                                          );

                                                                          shader.fragmentShader = [
                                                                              'uniform vec3 uColor;',
                                                                              'uniform float uIsGradient;',
                                                                              'uniform vec3 uColor1;',
                                                                              'uniform vec3 uColor2;',
                                                                              'uniform float uMinY;',
                                                                              'uniform float uMaxY;',
                                                                              'uniform float uHasPattern;',
                                                                              'uniform vec3 uPatternColor;',
                                                                              'uniform sampler2D uPatternTexture;',
                                                                              'uniform float uPatternType;',
                                                                              'uniform float uPatternSize;',
                                                                              'uniform vec3 uPatternOffset;',
                                                                              'uniform float uPatternRotation;',
                                                                              'uniform float uPatternMinY;',
                                                                              'uniform float uPatternMaxY;',
                                                                              'uniform float uPatternMappingMode;',
                                                                              'varying vec3 vLocalPos;',
                                                                              'varying vec3 vModelPos;',
                                                                              'varying vec3 vModelNormal;',
                                                                              'varying vec2 vUv;',
                                                                              'float hash(vec2 p) { return fract(sin(dot(p, vec2(127.1, 311.7))) * 43758.5453123); }',
                                                                              'float noise(vec2 p) {',
                                                                              '  vec2 i = floor(p); vec2 f = fract(p);',
                                                                              '  f = f*f*(3.0-2.0*f);',
                                                                              '  return mix(mix(hash(i + vec2(0,0)), hash(i + vec2(1,0)), f.x),',
                                                                              '             mix(hash(i + vec2(0,1)), hash(i + vec2(1,1)), f.x), f.y);',
                                                                              '}',
                                                                              shader.fragmentShader
                                                                          ].join('\n').replace(
                                                                              'vec4 diffuseColor = vec4( diffuse, opacity );',
                                                                              'vec3 baseColor = uColor;\n' +
                                                                              'if (uIsGradient > 0.5) {\n' +
                                                                              '  float t = (vLocalPos.y - uMinY) / (uMaxY - uMinY);\n' +
                                                                              '  t = clamp(t, 0.0, 1.0);\n' +
                                                                              '  t = smoothstep(0.1, 0.9, t);\n' +
                                                                              '  baseColor = mix(uColor2, uColor1, t);\n' +
                                                                              '}\n' +
                                                                              'vec3 finalColor = baseColor;\n' +
                                                                              'float patStrength = 0.0;\n' +
                                                                              'if (uPatternType > 0.5 && uPatternType < 1.5) {\n' +
                                                                              '  vec2 grid = fract(vUv * 40.0);\n' +
                                                                              '  patStrength = step(0.5, grid.x) == step(0.5, grid.y) ? 0.1 : -0.1;\n' +
                                                                              '} else if (uPatternType > 1.5 && uPatternType < 2.5) {\n' +
                                                                              '  patStrength = (noise(vUv * 10.0) - 0.5) * 0.4;\n' +
                                                                              '} else if (uPatternType > 2.5) {\n' +
                                                                              '  vec2 grid = fract(vUv * 30.0) - 0.5;\n' +
                                                                              '  patStrength = length(grid) < 0.3 ? -0.2 : 0.0;\n' +
                                                                              '}\n' +
                                                                              'finalColor += patStrength;\n' +
                                                                              'if (uHasPattern > 0.5) {\n' +
                                                                              '  float heightT = (vLocalPos.y - uMinY) / (uMaxY - uMinY);\n' +
                                                                              '  heightT = clamp(heightT, 0.0, 1.0);\n' +
                                                                              '  float lowerFade = smoothstep(uPatternMinY - 0.05, uPatternMinY + 0.05, heightT);\n' +
                                                                              '  float upperFade = 1.0 - smoothstep(uPatternMaxY - 0.05, uPatternMaxY + 0.05, heightT);\n' +
                                                                              '  float mask = clamp(lowerFade * upperFade, 0.0, 1.0);\n' +
                                                                              '  float sRot = sin(uPatternRotation);\n' +
                                                                              '  float cRot = cos(uPatternRotation);\n' +
                                                                              '  vec4 patternSample;\n' +
                                                                              '  if (uPatternMappingMode < 0.5) {\n' +
                                                                              '    vec2 mappedUv = vUv - vec2(0.5);\n' +
                                                                              '    mappedUv = mappedUv / uPatternSize;\n' +
                                                                              '    mappedUv = mappedUv + uPatternOffset.xy;\n' +
                                                                              '    mappedUv = vec2(mappedUv.x * cRot - mappedUv.y * sRot, mappedUv.x * sRot + mappedUv.y * cRot);\n' +
                                                                              '    mappedUv = mappedUv + vec2(0.5);\n' +
                                                                              '    patternSample = texture2D(uPatternTexture, mappedUv);\n' +
                                                                              '  } else if (uPatternMappingMode > 1.5) {\n' +
                                                                              '    float angle = atan(vModelPos.z, vModelPos.x);\n' +
                                                                              '    float u_cyl = angle * 0.3;\n' +
                                                                              '    float v_cyl = vModelPos.y;\n' +
                                                                              '    vec2 mappedUv = vec2(u_cyl, v_cyl) / uPatternSize + uPatternOffset.xy;\n' +
                                                                              '    mappedUv = vec2(mappedUv.x * cRot - mappedUv.y * sRot, mappedUv.x * sRot + mappedUv.y * cRot);\n' +
                                                                              '    patternSample = texture2D(uPatternTexture, mappedUv);\n' +
                                                                              '  } else {\n' +
                                                                              '    vec3 rotatedPos = vModelPos;\n' +
                                                                              '    rotatedPos = vec3(rotatedPos.x * cRot - rotatedPos.z * sRot, rotatedPos.y, rotatedPos.x * sRot + rotatedPos.z * cRot);\n' +
                                                                              '    vec3 rotatedNormal = vModelNormal;\n' +
                                                                              '    rotatedNormal = vec3(rotatedNormal.x * cRot - rotatedNormal.z * sRot, rotatedNormal.y, rotatedNormal.x * sRot + rotatedNormal.z * cRot);\n' +
                                                                              '    vec3 blending = abs(normalize(rotatedNormal));\n' +
                                                                              '    vec2 uvX = vec2(rotatedPos.z + uPatternOffset.x, rotatedPos.y + uPatternOffset.y) / uPatternSize;\n' +
                                                                              '    vec2 uvY = vec2(rotatedPos.x + uPatternOffset.x, rotatedPos.z + uPatternOffset.y) / uPatternSize;\n' +
                                                                              '    vec2 uvZ = vec2(rotatedPos.x + uPatternOffset.x, rotatedPos.y + uPatternOffset.y) / uPatternSize;\n' +
                                                                              '    if (blending.x >= blending.y && blending.x >= blending.z) {\n' +
                                                                              '      patternSample = texture2D(uPatternTexture, uvX);\n' +
                                                                              '    } else if (blending.y >= blending.x && blending.y >= blending.z) {\n' +
                                                                              '      patternSample = texture2D(uPatternTexture, uvY);\n' +
                                                                              '    } else {\n' +
                                                                              '      patternSample = texture2D(uPatternTexture, uvZ);\n' +
                                                                              '    }\n' +
                                                                              '  }\n' +
                                                                              '  float patVal = patternSample.r * mask;\n' +
                                                                              '  vec4 cornerSample = texture2D(uPatternTexture, vec2(0.01, 0.01));\n' +
                                                                              '  float isBgWhite = step(0.1, cornerSample.r);\n' +
                                                                              '  if (isBgWhite > 0.5) {\n' +
                                                                              '    finalColor = mix(uPatternColor, finalColor, patVal);\n' +
                                                                              '  } else {\n' +
                                                                              '    finalColor = mix(finalColor, uPatternColor, patVal);\n' +
                                                                              '  }\n' +
                                                                              '}\n' +
                                                                              'if (!gl_FrontFacing) {\n' +
                                                                              '  finalColor = vec3(0.92, 0.92, 0.92);\n' +
                                                                              '}\n' +
                                                                              'vec4 diffuseColor = vec4( finalColor, opacity );'
                                                                          );
                                                                      };

                                                                      mat.roughness = materialFinish === 'gloss' ? 0.1 : materialFinish === 'metallic' ? 0.2 : 0.8;
                                                                      mat.metalness = materialFinish === 'metallic' ? 0.8 : 0.0;
                                                                      mat.side = THREE.DoubleSide;

                                                                      node.geometry.computeBoundingBox();
                                                                      mat.userData.uniforms.uMinY.value = node.geometry.boundingBox.min.y;
                                                                      mat.userData.uniforms.uMaxY.value = node.geometry.boundingBox.max.y;

                                                                      if (state.pUrl) {
                                                                          const textureLoader = new THREE.TextureLoader();
                                                                          textureLoader.load(state.pUrl, function(tex) {
                                                                              tex.wrapS = tex.wrapT = THREE.RepeatWrapping;
                                                                              tex.colorSpace = THREE.SRGBColorSpace;
                                                                              mat.userData.uniforms.uPatternTexture.value = tex;
                                                                              mat.userData.uniforms.uHasPattern.value = 1.0;
                                                                              mat.needsUpdate = true;
                                                                          });
                                                                      }

                                                                      node.material = mat;
                                                                  }
                                                            }
                                                        });
                                                        
                                                        const viewerGroup = new THREE.Group();
                                                        scene.add(viewerGroup);
                                                        viewerGroup.add(model);
                                                        
                                                        // Update world matrices so DecalGeometry projects correctly
                                                        model.updateMatrixWorld(true);
                                                        
                                                        
                                                         decals.forEach(function(d) {
                                                             
                                                              const norm = function(id) {
                                                                  if (!id) return '';
                                                                  return id.toLowerCase().replace(/\.obj$/i, '').replace(/[^a-z0-9]/g, '');
                                                              };
                                                              
                                                              const targetMeshes = [];
                                                              model.traverse(function(m) {
                                                                  if (m.isMesh) {
                                                                      // Check exact match first like ModelViewer.jsx (line 1256-1271)
                                                                      let isTarget = false;
                                                                      if (m.name === d.meshId) {
                                                                          isTarget = true;
                                                                      } else {
                                                                          // Fallback to normalized matching
                                                                          const normM = norm(m.name);
                                                                          const normD = norm(d.meshId);
                                                                          
                                                                          let mMeta = {};
                                                                          let dMeta = {};
                                                                          if (layersMetadata) {
                                                                              for (let key in layersMetadata) {
                                                                                  const normKey = norm(key);
                                                                                  if (normKey === normM) mMeta = layersMetadata[key] || {};
                                                                                  if (normKey === normD) dMeta = layersMetadata[key] || {};
                                                                              }
                                                                          }
                                                                          
                                                                          if (normM === normD) {
                                                                              isTarget = true;
                                                                          } else if (mMeta.merge_parent && norm(mMeta.merge_parent) === normD) {
                                                                              isTarget = true;
                                                                          } else if (dMeta.merge_parent && norm(dMeta.merge_parent) === normM) {
                                                                              isTarget = true;
                                                                          } else if (mMeta.merge_parent && dMeta.merge_parent && norm(mMeta.merge_parent) === norm(dMeta.merge_parent)) {
                                                                              isTarget = true;
                                                                          }
                                                                      }
                                                                      
                                                                      if (isTarget) {
                                                                          targetMeshes.push(m);
                                                                      }
                                                                  }
                                                              });

                                                             if (targetMeshes.length === 0) return;
                                                             
                                                             try {
                                                                  let canvas;
                                                                  if (d.type === 'pattern' && d.imageUrl) {
                                                                      // Pattern: 1024x1024, edge-to-edge, with background keying (matches builder exactly)
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
                                                                          
                                                                          // Background keying & tinting (same as builder getPatternCanvasSync)
                                                                          try {
                                                                              var imgData = ctx.getImageData(0, 0, 1024, 1024);
                                                                              var data = imgData.data;
                                                                              
                                                                              // Detect if image has alpha channel (PNG)
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
                                                                                    // PNG with transparency — tint visible pixels, preserve alpha
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
                                                                                  // Solid image (JPG) — key out bg using corner pixel brightness
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
                                                                          texture.needsUpdate = true;
                                                                      };
                                                                      img.src = d.imageUrl;
                                                                  } else if (d.type === 'image' && d.imageUrl) {
                                                                      // Image decal: 512x512, centered at 85% scale (correct for images)
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
                                                                          const scale = Math.min(512 / img.width, 512 / img.height) * 0.85;
                                                                          const w = img.width * scale;
                                                                          const h = img.height * scale;
                                                                          ctx.drawImage(img, (512 - w) / 2, (512 - h) / 2, w, h);
                                                                          texture.needsUpdate = true;
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
                                                                 
                                                                  const texture = new THREE.CanvasTexture(canvas);
                                                                  texture.colorSpace = THREE.SRGBColorSpace;
                                                                  
                                                                  // For pattern decals: set RepeatWrapping so tiled UVs wrap correctly
                                                                  if (d.type === 'pattern') {
                                                                      texture.wrapS = THREE.RepeatWrapping;
                                                                      texture.wrapT = THREE.RepeatWrapping;
                                                                  }
                                                                  
                                                                   // Use stored worldPoint directly (it is already in correct world space)
                                                                   const point = new THREE.Vector3().fromArray(d.worldPoint);
                                                                   const normal = new THREE.Vector3().fromArray(d.worldNormal);
                                                                   
                                                                   const up = Math.abs(normal.y) < 0.95
                                                                       ? new THREE.Vector3(0, 1, 0)
                                                                       : new THREE.Vector3(1, 0, 0);
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
                                                                   
                                                                   // For patterns: compute ONE global projection matrix for consistent tiling across all meshes
                                                                   // This matches the builder's approach exactly (ModelViewer.jsx lines 1276-1296)
                                                                   var globalDecalMatrixInv = null;
                                                                   var globalDecalSize = null;
                                                                   var globalRepeatFactor = null;
                                                                   if (d.type === 'pattern') {
                                                                       var globalBox = new THREE.Box3();
                                                                       targetMeshes.forEach(function(m) {
                                                                           m.updateMatrixWorld(true);
                                                                           globalBox.union(new THREE.Box3().setFromObject(m));
                                                                       });
                                                                       var globalCenter = globalBox.getCenter(new THREE.Vector3());
                                                                       var globalSz = globalBox.getSize(new THREE.Vector3());
                                                                       var gMinCover = 0.5;
                                                                       var gCoverX = Math.max(gMinCover, Math.max(globalSz.x, globalSz.z) * 3.0);
                                                                       var gCoverY = Math.max(gMinCover, globalSz.y * 3.0);
                                                                       var gCoverZ = Math.max(gMinCover, Math.max(globalSz.x, globalSz.y, globalSz.z) * 3.0);
                                                                       globalDecalSize = new THREE.Vector3(gCoverX, gCoverY, gCoverZ);
                                                                       
                                                                       var quaternion = new THREE.Quaternion().setFromEuler(orientation);
                                                                       var globalDecalMatrix = new THREE.Matrix4().compose(globalCenter, quaternion, globalDecalSize);
                                                                       globalDecalMatrixInv = globalDecalMatrix.clone().invert();
                                                                       globalRepeatFactor = 8.0 / Math.max(0.05, d.decalScale || 0.8);
                                                                   }
                                                                   
                                                                   targetMeshes.forEach(function(targetMesh) {
                                                                       try {
                                                                           var geo;
                                                                           if (d.type === 'pattern') {
                                                                               geo = targetMesh.geometry.clone();
                                                                           } else {
                                                                               geo = new DecalGeo(targetMesh, point, orientation, decalSize);
                                                                           }
                                                                           
                                                                           var mat;
                                                                           if (d.type === 'pattern') {
                                                                               mat = new THREE.ShaderMaterial({
                                                                                   uniforms: {
                                                                                       map: { value: texture },
                                                                                       uRepeat: { value: globalRepeatFactor },
                                                                                       uDecalMatrixInv: { value: globalDecalMatrixInv.clone().multiply(targetMesh.matrixWorld) },
                                                                                       uDecalSize: { value: globalDecalSize },
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
                                                                                       '  if (!gl_FrontFacing) discard;',
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
                                                                                       '  gl_FragColor = vec4(texColor.rgb, texColor.a);',
                                                                                       '}'
                                                                                   ].join('\n'),
                                                                                   transparent: true,
                                                                                   depthTest: true,
                                                                                   depthWrite: false,
                                                                                   polygonOffset: true,
                                                                                   polygonOffsetFactor: -1.0,
                                                                                   polygonOffsetUnits: -1.0,
                                                                                   side: THREE.DoubleSide
                                                                               });
                                                                            } else {
                                                                                mat = new THREE.MeshStandardMaterial({
                                                                                   map: texture,
                                                                                   transparent: true,
                                                                                   depthTest: true,
                                                                                   depthWrite: false,
                                                                                   polygonOffset: true,
                                                                                   polygonOffsetFactor: d.type === 'image' ? -2.0 : -3.0,
                                                                                   polygonOffsetUnits: d.type === 'image' ? -2.0 : -3.0,
                                                                               });
                                                                           }
                                                                           
                                                                            
                                                                            if (d.type !== 'pattern') {
                                                                                var localMatrix = targetMesh.matrixWorld.clone().invert();
                                                                                geo.applyMatrix4(localMatrix);
                                                                            }
                                                                            
                                                                            const decalMesh = new THREE.Mesh(geo, mat);
                                                                            decalMesh.renderOrder = d.type === 'pattern' ? 997 : (d.type === 'image' ? 998 : 999);
                                                                            targetMesh.add(decalMesh);
                                                                        } catch(subMeshDecalErr) {
                                                                            console.error('Submesh decal error:', subMeshDecalErr);
                                                                        }
                                                                    });
                                                             } catch(decalErr) {
                                                                 console.error('Decal render error:', decalErr);
                                                             }
                                                         });

                                                        function animate() {
                                                            requestAnimationFrame(animate);
                                                            viewerGroup.rotation.y += 0.008;
                                                            controls.update();
                                                            renderer.render(scene, camera);
                                                        }
                                                        animate();
                                                    }, undefined, function(error) {
                                                        console.error(error);
                                                        container.innerHTML = '<div class="h-100 d-flex align-items-center justify-content-center text-muted" style="font-size: 11px;">Error loading model</div>';
                                                    });

                                                    window.addEventListener('resize', () => {
                                                        if (!container.clientWidth || !container.clientHeight) return;
                                                        camera.aspect = container.clientWidth / container.clientHeight;
                                                        camera.updateProjectionMatrix();
                                                        renderer.setSize(container.clientWidth, container.clientHeight);
                                                    });
                                                })();
                                            </script>
                                        </div>
                                    @endif

                                    <!-- Specifications Details -->
                                    <div class="col-md-6">
                                        <div class="card border h-100 p-4 rounded-3 shadow-none bg-light">
                                            <div class="fw-bold text-primary mb-3 text-uppercase tracking-wider" style="font-size: 11px;">
                                                <i class="bi bi-gear-wide-connected me-1"></i> Custom Specifications Breakdown
                                            </div>

                                            @if(isset($item->savedDesign->design_data['materialFinish']) || isset($item->savedDesign->design_data['globalPattern']))
                                                <div class="mb-3">
                                                    <span class="text-secondary fw-bold d-block mb-1" style="font-size: 10px; text-transform: uppercase;">Material & Texture Finish</span>
                                                    <span class="badge bg-secondary text-white rounded-pill uppercase px-3 py-1.5" style="font-size: 10px; font-weight: 600;">
                                                        Finish: {{ $item->savedDesign->design_data['materialFinish'] ?? 'default' }}
                                                    </span>
                                                    @if(isset($item->savedDesign->design_data['globalPattern']))
                                                        <span class="badge bg-dark text-white rounded-pill uppercase px-3 py-1.5" style="font-size: 10px; font-weight: 600;">
                                                            Pattern: {{ $item->savedDesign->design_data['globalPattern'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif

                                             @if(isset($item->layers_metadata) && count($item->layers_metadata) > 0)
                                                 <div class="mb-3">
                                                     <span class="text-secondary fw-bold d-block mb-1.5" style="font-size: 10px; text-transform: uppercase;">Component Colors</span>
                                                     <div class="row g-2">
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
                                                             <div class="col-sm-6">
                                                                 <div class="bg-white border rounded p-2 d-flex align-items-center gap-2">
                                                                     <span class="d-inline-block rounded-circle border" style="width: 14px; height: 14px; background-color: {{ $color }}; flex-shrink: 0;"></span>
                                                                     <div class="text-truncate" style="font-size: 10.5px;">
                                                                         <span class="fw-bold text-dark">{{ $displayName }}</span>
                                                                         @if(isset($state['pUrl']) && $state['pUrl'])
                                                                             <div class="text-indigo-600" style="font-size: 9px; font-weight: 600;">
                                                                                 Tiled Overlay Txtr
                                                                             </div>
                                                                         @endif
                                                                     </div>
                                                                 </div>
                                                             </div>
                                                         @endforeach
                                                     </div>
                                                 </div>
                                             @elseif(isset($item->savedDesign->design_data['meshStates']))
                                                 <div class="mb-3">
                                                     <span class="text-secondary fw-bold d-block mb-1.5" style="font-size: 10px; text-transform: uppercase;">Component Colors</span>
                                                     <div class="row g-2">
                                                         @foreach($item->savedDesign->design_data['meshStates'] as $meshId => $state)
                                                             @if(isset($state['color']))
                                                                 @php
                                                                     $displayName = ucwords(str_replace('_', ' ', str_replace('.obj', '', $meshId)));
                                                                 @endphp
                                                                 <div class="col-sm-6">
                                                                     <div class="bg-white border rounded p-2 d-flex align-items-center gap-2">
                                                                         <span class="d-inline-block rounded-circle border" style="width: 14px; height: 14px; background-color: {{ $state['color'] }}; flex-shrink: 0;"></span>
                                                                         <div class="text-truncate" style="font-size: 10.5px;">
                                                                             <span class="fw-bold text-dark">{{ $displayName }}</span>
                                                                             @if(isset($state['pUrl']) && $state['pUrl'])
                                                                                 <div class="text-indigo-600" style="font-size: 9px; font-weight: 600;">
                                                                                     Tiled Overlay Txtr
                                                                                 </div>
                                                                             @endif
                                                                         </div>
                                                                     </div>
                                                                 </div>
                                                             @endif
                                                         @endforeach
                                                     </div>
                                                 </div>
                                             @endif

                                            @php
                                                $textDecals = array_filter($item->savedDesign->design_data['decals'] ?? [], function($d) { return ($d['type'] ?? 'text') === 'text'; });
                                                $imageDecals = array_filter($item->savedDesign->design_data['decals'] ?? [], function($d) { return ($d['type'] ?? 'text') === 'image'; });
                                            @endphp

                                            @if(count($textDecals) > 0)
                                                <div class="mb-3">
                                                    <span class="text-secondary fw-bold d-block mb-1.5" style="font-size: 10px; text-transform: uppercase;">Custom Typography Layers</span>
                                                    @foreach($textDecals as $decal)
                                                        <div class="bg-white border rounded p-3 mb-2" style="font-size: 11px;">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <span class="fw-bold text-dark fs-6">"{{ $decal['text'] ?? '' }}"</span>
                                                                <span class="badge bg-primary-subtle text-primary border rounded-pill px-2 py-0.5" style="font-size: 9px;">Text Decal</span>
                                                            </div>
                                                            <div class="row g-2 text-muted" style="font-size: 10px;">
                                                                <div class="col-6">Font: <strong class="text-dark">{{ $decal['font'] ?? 'Outfit' }}</strong></div>
                                                                <div class="col-6">Color: <span class="d-inline-block rounded-circle border" style="width: 10px; height: 10px; background-color: {{ $decal['color'] ?? '#fff' }}; vertical-align: middle;"></span> <strong class="text-dark">{{ $decal['color'] ?? '#ffffff' }}</strong></div>
                                                                
                                                                <div class="col-6">
                                                                    Outline 1: 
                                                                    @if(isset($decal['outline1Width']) && $decal['outline1Width'] > 0)
                                                                        <strong class="text-dark">{{ $decal['outline1Width'] }}px</strong> <span class="d-inline-block rounded-circle border" style="width: 10px; height: 10px; background-color: {{ $decal['outline1Color'] ?? '#000' }}; vertical-align: middle;"></span>
                                                                    @else
                                                                        <strong class="text-dark">None</strong>
                                                                    @endif
                                                                </div>
                                                                <div class="col-6">
                                                                    Outline 2: 
                                                                    @if(isset($decal['outline2Width']) && $decal['outline2Width'] > 0)
                                                                        <strong class="text-dark">{{ $decal['outline2Width'] }}px</strong> <span class="d-inline-block rounded-circle border" style="width: 10px; height: 10px; background-color: {{ $decal['outline2Color'] ?? '#000' }}; vertical-align: middle;"></span>
                                                                    @else
                                                                        <strong class="text-dark">None</strong>
                                                                    @endif
                                                                </div>
                                                                
                                                                <div class="col-12">
                                                                    Curve Effect: 
                                                                    @if(isset($decal['effect']) && $decal['effect'] !== 'none')
                                                                        <strong class="text-dark">{{ strtoupper($decal['effect']) }} ({{ round(($decal['effectIntensity'] ?? 0.5) * 100) }}% intensity)</strong>
                                                                    @else
                                                                        <strong class="text-dark">None</strong>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            @if(count($imageDecals) > 0)
                                                <div>
                                                    <span class="text-secondary fw-bold d-block mb-1.5" style="font-size: 10px; text-transform: uppercase;">Applied Graphics/Logos</span>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @foreach($imageDecals as $decal)
                                                            <div class="bg-white border rounded p-2 d-inline-flex align-items-center gap-2">
                                                                @if(isset($decal['imageUrl']))
                                                                    <img src="{{ $decal['imageUrl'] }}" alt="Logo" style="height: 24px; max-width: 40px; object-fit: contain;" />
                                                                @endif
                                                                <div>
                                                                    <span class="text-dark fw-bold d-block" style="font-size: 10px;">{{ $decal['text'] ?? 'Logo Layer' }}</span>
                                                                    <small class="text-muted" style="font-size: 8px;">Scale: {{ $decal['decalScale'] ?? '0.15' }}</small>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Order Summary & Status Update Panel -->
        <div class="col-lg-4">
            <!-- Status Update -->
            <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
                <h5 class="fw-bold border-bottom pb-3 mb-4 text-dark uppercase tracking-wide">Order Status</h5>
                
                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-xs font-bold text-muted uppercase">Set Current Status</label>
                        <select name="status" class="form-select rounded-xl py-2.5 px-3 border-slate-200 cursor-pointer fw-semibold text-dark">
                            @foreach($statuses as $status)
                                <option value="{{ $status->name }}" {{ strtolower($order->status) === strtolower($status->name) ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                            @if($order->status && !$statuses->contains('name', $order->status))
                                <option value="{{ $order->status }}" selected>{{ $order->status }}</option>
                            @endif
                        </select>
                        @error('status')
                            <p class="text-xs text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="admin_note" class="form-label text-xs font-bold text-muted uppercase">Admin Internal Note</label>
                        <textarea id="admin_note" name="admin_note" rows="4" class="form-control rounded-xl border-slate-200 p-3 text-sm" placeholder="Write internal processing notes here (visible to admin only)...">{{ old('admin_note', $order->admin_note) }}</textarea>
                        @error('admin_note')
                            <p class="text-xs text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold d-flex align-items-center justify-content-center gap-1.5 shadow-sm" style="border-radius: 0.75rem;">
                        <i class="bi bi-save fs-5"></i> &nbsp;Save Changes
                    </button>
                </form>
            </div>

            <!-- Customer & Payment details -->
            <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4" style="word-break: break-word;">
                <h5 class="fw-bold border-bottom pb-3 mb-4 text-dark uppercase tracking-wide">Customer Details</h5>
                <div class="space-y-2 small fw-semibold text-slate-500 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2 gap-3">
                        <span class="flex-shrink-0">Name:</span>
                        <strong class="text-dark text-end" style="word-break: break-word;">{{ $order->billing_name }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2 gap-3">
                        <span class="flex-shrink-0">Email:</span>
                        <strong class="text-dark text-end" style="word-break: break-all;">{{ $order->billing_email }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2 gap-3">
                        <span class="flex-shrink-0">Phone:</span>
                        <strong class="text-dark text-end">{{ $order->phone }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2 gap-3">
                        <span class="flex-shrink-0">Payment Method:</span>
                        <strong class="text-dark text-end">{{ $order->payment_method }}</strong>
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
                <h5 class="fw-bold border-bottom pb-3 mb-4 text-dark uppercase tracking-wide">Order Summary</h5>

                <div class="space-y-3 small fw-semibold text-slate-500">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong class="text-dark">${{ number_format($order->subtotal, 2) }}</strong>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="d-flex justify-content-between mb-2 text-emerald-600">
                            <span>Discount ({{ $order->coupon_code }}):</span>
                            <strong>-${{ number_format($order->discount_amount, 2) }}</strong>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <strong class="text-dark">${{ number_format($order->shipping, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax:</span>
                        <strong class="text-dark">${{ number_format($order->tax, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-top border-bottom py-2">
                        <span>Shipping Address:</span>
                        <strong class="text-dark text-end" style="max-width: 180px; word-wrap: break-word;">{{ $order->shipping_address }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <span class="text-xs font-extrabold text-slate-800 uppercase tracking-widest">GRAND TOTAL:</span>
                        <h4 class="mb-0 fw-extrabold text-primary">${{ number_format($order->total, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

