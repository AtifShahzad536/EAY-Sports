import React, { Suspense, useEffect, useMemo, useRef, useCallback, memo, useS } from 'react';
import { Canvas, useThree, useFrame } from '@react-three/fiber';
import { OrbitControls, useGLTF, Environment, ContactShadows, Center, useProgress, Html } from '@react-three/drei';
import * as THREE from 'three';
import { DecalGeometry } from 'three/examples/jsm/geometries/DecalGeometry.js';

useGLTF.setDecoderPath('https://www.gstatic.com/draco/versioned/decoders/1.5.5/');

const whiteTex = new THREE.CanvasTexture(document.createElement('canvas'));

// ─── TEXT CANVAS HELPER (renders text as a texture) ──────────────────────────
// ─── TEXT CANVAS HELPER (renders text as a texture with full styling) ────────
function createTextCanvas(decal) {
  const { text, color, font, outline1Color, outline1Width, outline2Color, outline2Width, effect, effectIntensity } = decal;

  const canvas = document.createElement('canvas');
  canvas.width = 1024; // High res
  canvas.height = 256;
  const ctx = canvas.getContext('2d');
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  const fontSize = 120;
  ctx.font = `bold ${fontSize}px ${font || 'Arial'}`;
  ctx.textAlign = 'center';
  ctx.textBaseline = 'middle';

  const x = canvas.width / 2;
  const y = canvas.height / 2;

  // Function to draw text or arched text
  const drawPass = (isStroke, strokeWidth, strokeColor) => {
    ctx.fillStyle = strokeColor || color;
    ctx.strokeStyle = strokeColor;
    ctx.lineWidth = strokeWidth * 2;
    ctx.lineJoin = 'round';
    ctx.lineCap = 'round';

    const drawText = (tx, ty) => {
      if (isStroke) ctx.strokeText(text, tx, ty);
      else ctx.fillText(text, tx, ty);
    };

    const drawTail = (tx, ty) => {
      if (decal.tail === 'swoosh') {
        const textWidth = ctx.measureText(text).width;
        ctx.beginPath();
        ctx.moveTo(tx - textWidth / 2, ty + 20);
        ctx.quadraticCurveTo(tx, ty + 60, tx + textWidth / 2 + 20, ty + 10);
        ctx.lineTo(tx + textWidth / 2 + 15, ty + 20);
        ctx.quadraticCurveTo(tx, ty + 70, tx - textWidth / 2 - 10, ty + 25);
        ctx.closePath();
        if (isStroke) ctx.stroke();
        else ctx.fill();
      }
    };

    if (effect === 'arch') {
      const radius = 400 / (effectIntensity || 0.5);
      const characters = text.split('');
      const totalWidth = ctx.measureText(text).width;
      const anglePerPixel = 1 / radius;
      const totalAngle = totalWidth * anglePerPixel;

      let currentAngle = -totalAngle / 2;

      ctx.save();
      ctx.translate(x, y + radius - 20);

      characters.forEach(char => {
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
      drawText(x, y - 10);
      drawTail(x, y - 10);
    }
  };

  // 1. Draw Outline 2 (Bottom-most)
  if (outline2Width > 0) {
    drawPass(true, (outline1Width || 0) + (outline2Width || 0), outline2Color);
  }

  // 2. Draw Outline 1
  if (outline1Width > 0) {
    drawPass(true, outline1Width, outline1Color);
  }

  // 3. Draw Fill (Top-most)
  drawPass(false);

  return canvas;
}

// ─── IMAGE CANVAS HELPER (renders an image as a decal texture) ───────────────
function createImageCanvas(imageUrl) {
  const canvas = document.createElement('canvas');
  canvas.width = 512;
  canvas.height = 512;
  const ctx = canvas.getContext('2d');
  ctx.clearRect(0, 0, 512, 512);
  return canvas;
}

// Image cache to avoid reloading
const imageCache = {};
const patternImageCache = {};
const patternCanvasCache = {};

function getImageCanvasSync(imageUrl) {
  return imageCache[imageUrl] || null;
}

function getPatternCanvasSync(imageUrl, tintColor) {
  const cacheKey = `${imageUrl}_${tintColor}`;
  if (patternCanvasCache[cacheKey]) {
    return patternCanvasCache[cacheKey];
  }

  const img = patternImageCache[imageUrl];
  if (img && img.complete) {
    const canvas = document.createElement('canvas');
    canvas.width = 1024;
    canvas.height = 1024;
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, 1024, 1024);
    ctx.drawImage(img, 0, 0, 1024, 1024);

    try {
      const imgData = ctx.getImageData(0, 0, 1024, 1024);
      const data = imgData.data;

      // 1. Detect if the image already has transparency (PNG)
      let hasAlphaChannel = false;
      for (let i = 3; i < data.length; i += 4) {
        if (data[i] < 220) {
          hasAlphaChannel = true;
          break;
        }
      }

      const isOriginal = tintColor === 'original';
      const tint = new THREE.Color(isOriginal ? '#ffffff' : tintColor);
      const tr = Math.round(tint.r * 255);
      const tg = Math.round(tint.g * 255);
      const tb = Math.round(tint.b * 255);

      if (hasAlphaChannel) {
        // Transparent image (PNG) - just tint the visible pixels and preserve alpha.
        // For checkerboard pattern: Eay Sports checkerboard PNG has transparent squares
        // and opaque background, which causes colors to appear swapped in the custom viewer.
        // We invert it so transparent squares get the pattern color and background gets the base color.
        const isInvertedPattern = imageUrl.includes('checker.png') || imageUrl.includes('checker');
        if (isInvertedPattern) {
          for (let i = 0; i < data.length; i += 4) {
            const alpha = data[i + 3];
            if (alpha > 50) {
              data[i + 3] = 0; // Opaque pixels become transparent background
            } else {
              data[i] = tr;
              data[i + 1] = tg;
              data[i + 2] = tb;
              data[i + 3] = 255; // Transparent squares become opaque pattern
            }
          }
        } else {
          if (!isOriginal) {
            for (let i = 0; i < data.length; i += 4) {
              const alpha = data[i + 3];
              if (alpha > 0) {
                data[i] = tr;
                data[i + 1] = tg;
                data[i + 2] = tb;
              }
            }
          }
        }
      } else {
        // Solid image (JPG) - key out background using corner pixel brightness
        const r0 = data[0];
        const g0 = data[1];
        const b0 = data[2];
        const isBgWhite = (r0 + g0 + b0) / 3 > 127;

        for (let i = 0; i < data.length; i += 4) {
          const r = data[i];
          const g = data[i + 1];
          const b = data[i + 2];
          const alpha = data[i + 3];

          if (alpha === 0) continue;

          const val = (r + g + b) / 3;

          if (isBgWhite) {
            // White is background (transparent), dark is pattern (tinted)
            // Use thresholds to ensure compression noise and vignette borders are 100% transparent.
            let alphaFactor;
            if (val > 235) {
              alphaFactor = 0;
            } else if (val < 180) {
              alphaFactor = 255;
            } else {
              alphaFactor = Math.round(255 * (1.0 - (val - 180) / (235 - 180)));
            }
            data[i + 3] = alphaFactor;
            if (!isOriginal) {
              data[i] = tr;
              data[i + 1] = tg;
              data[i + 2] = tb;
            }
          } else {
            // Black is background (transparent), light is pattern (tinted)
            // Use thresholds to ensure dark noise and borders are 100% transparent.
            let alphaFactor;
            if (val < 20) {
              alphaFactor = 0;
            } else if (val > 75) {
              alphaFactor = 255;
            } else {
              alphaFactor = Math.round(255 * ((val - 20) / (75 - 20)));
            }
            data[i + 3] = alphaFactor;
            if (!isOriginal) {
              data[i] = tr;
              data[i + 1] = tg;
              data[i + 2] = tb;
            }
          }
        }
      }
      ctx.putImageData(imgData, 0, 0);
    } catch (e) {
      console.error("Pattern processing error (possibly CORS or file://):", e);
      // Fallback: draw image directly
      ctx.clearRect(0, 0, 1024, 1024);
      ctx.drawImage(img, 0, 0, 1024, 1024);
    }

    patternCanvasCache[cacheKey] = canvas;
    return canvas;
  }

  return null;
}

function loadImageToCanvas(imageUrl) {
  return new Promise((resolve) => {
    const cached = getImageCanvasSync(imageUrl);
    if (cached) {
      resolve(cached);
      return;
    }
    const img = new Image();
    // blob: and data: URLs are local - setting crossOrigin on them causes load failure
    // Only set crossOrigin for real HTTP/HTTPS URLs (external resources, /storage/ paths via Vite proxy)
    if (!imageUrl.startsWith('blob:') && !imageUrl.startsWith('data:')) {
      img.crossOrigin = 'anonymous';
    }
    img.onload = () => {
      const canvas = document.createElement('canvas');
      canvas.width = 512;
      canvas.height = 512;
      const ctx = canvas.getContext('2d');
      ctx.clearRect(0, 0, 512, 512);
      // Fit image maintaining aspect ratio, centered
      const scale = Math.min(512 / img.width, 512 / img.height) * 0.85;
      const w = img.width * scale;
      const h = img.height * scale;
      ctx.drawImage(img, (512 - w) / 2, (512 - h) / 2, w, h);
      imageCache[imageUrl] = canvas;
      resolve(canvas);
    };
    img.onerror = () => {
      // Retry without crossOrigin in case server doesn't support CORS headers
      if (img.crossOrigin) {
        const retry = new Image();
        retry.onload = () => {
          const canvas = document.createElement('canvas');
          canvas.width = 512;
          canvas.height = 512;
          const ctx = canvas.getContext('2d');
          ctx.clearRect(0, 0, 512, 512);
          const scale = Math.min(512 / retry.width, 512 / retry.height) * 0.85;
          const w = retry.width * scale;
          const h = retry.height * scale;
          ctx.drawImage(retry, (512 - w) / 2, (512 - h) / 2, w, h);
          imageCache[imageUrl] = canvas;
          resolve(canvas);
        };
        retry.onerror = () => {
          const canvas = document.createElement('canvas');
          canvas.width = 512; canvas.height = 512;
          const ctx = canvas.getContext('2d');
          ctx.fillStyle = '#ff000033';
          ctx.fillRect(0, 0, 512, 512);
          ctx.font = 'bold 30px Arial'; ctx.fillStyle = '#f00'; ctx.textAlign = 'center';
          ctx.fillText('ERROR', 256, 260);
          resolve(canvas);
        };
        retry.src = imageUrl;
      } else {
        const canvas = document.createElement('canvas');
        canvas.width = 512; canvas.height = 512;
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = '#ff000033';
        ctx.fillRect(0, 0, 512, 512);
        ctx.font = 'bold 30px Arial'; ctx.fillStyle = '#f00'; ctx.textAlign = 'center';
        ctx.fillText('ERROR', 256, 260);
        resolve(canvas);
      }
    };
    img.src = imageUrl;
  });
}

function loadPatternToCanvas(imageUrl, tintColor) {
  const cacheKey = `${imageUrl}_${tintColor}`;
  return new Promise((resolve) => {
    const cachedCanvas = getPatternCanvasSync(imageUrl, tintColor);
    if (cachedCanvas) {
      resolve(cachedCanvas);
      return;
    }
    const img = new Image();
    // blob: and data: URLs are local - setting crossOrigin on them causes load failure
    if (!imageUrl.startsWith('blob:') && !imageUrl.startsWith('data:')) {
      img.crossOrigin = 'anonymous';
    }
    img.onload = () => {
      patternImageCache[imageUrl] = img;
      const canvas = getPatternCanvasSync(imageUrl, tintColor);
      resolve(canvas);
    };
    img.onerror = () => {
      // Retry without crossOrigin in case server doesn't support CORS headers
      if (img.crossOrigin) {
        const retry = new Image();
        retry.onload = () => {
          patternImageCache[imageUrl] = retry;
          const canvas = getPatternCanvasSync(imageUrl, tintColor);
          resolve(canvas);
        };
        retry.onerror = () => {
          const canvas = document.createElement('canvas');
          canvas.width = 512; canvas.height = 512;
          const ctx = canvas.getContext('2d');
          ctx.fillStyle = '#ff000033';
          ctx.fillRect(0, 0, 512, 512);
          resolve(canvas);
        };
        retry.src = imageUrl;
      } else {
        const canvas = document.createElement('canvas');
        canvas.width = 512; canvas.height = 512;
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = '#ff000033';
        ctx.fillRect(0, 0, 512, 512);
        resolve(canvas);
      }
    };
    img.src = imageUrl;
  });
}


// ─── LOADER ──────────────────────────────────────────────────────────────────
const CoolLoader = memo(function CoolLoader() {
  const { progress } = useProgress();
  return (
    <Html center>
      <div className="flex flex-col items-center justify-center gap-6 select-none pointer-events-none">
        <div className="relative w-16 h-16 flex items-center justify-center">
          <div className="w-6 h-6 bg-[#00b0f0] rounded flex items-center justify-center animate-pulse shadow-[0_0_15px_rgba(0,176,240,0.5)]">
            <span className="text-white text-[8px] font-black">E</span>
          </div>
          <div className="absolute inset-0 rounded-full border-b-2 border-l-2 border-[#00b0f0] animate-spin" style={{ animationDuration: '1.2s' }} />
          <div className="absolute inset-[-6px] rounded-full border-t border-r border-[#00b0f0]/30 animate-spin" style={{ animationDuration: '2.5s', animationDirection: 'reverse' }} />
        </div>
        <div className="flex flex-col items-center gap-1.5">
          <div className="text-[9px] font-black text-[#00b0f0] uppercase tracking-[0.4em] opacity-80">Initializing</div>
          <div className="text-[14px] font-bold text-gray-400 tabular-nums">{Math.round(progress)}%</div>
        </div>
      </div>
    </Html>
  );
})

// ─── CAMERA CONTROLLER ───────────────────────────────────────────────────────
const CameraController = memo(function CameraController({ mouseFollow, isDragging }) {
  const { camera } = useThree();
  const controlsRef = useRef();
  useEffect(() => {
    const onReset = () => {
      camera.position.set(0, 0, 2.5);
      camera.updateProjectionMatrix();
      if (controlsRef.current) controlsRef.current.reset();
    };
    const onZoom = (e) => {
      camera.position.z = Math.max(1.5, Math.min(8, camera.position.z + e.detail));
      camera.updateProjectionMatrix();
    };
    const onExport = () => {
      const canvas = document.querySelector('canvas');
      if (!canvas) return;
      const link = document.createElement('a');
      link.download = `eay-capture-${new Date().getTime()}.png`;
      link.href = canvas.toDataURL('image/png');
      link.click();
    };
    window.addEventListener('eay:resetCamera', onReset);
    window.addEventListener('eay:zoom', onZoom);
    window.addEventListener('eay:export', onExport);
    return () => {
      window.removeEventListener('eay:resetCamera', onReset);
      window.removeEventListener('eay:zoom', onZoom);
      window.removeEventListener('eay:export', onExport);
    };
  }, [camera]);
  return <OrbitControls ref={controlsRef} enabled={!mouseFollow && !isDragging} minPolarAngle={Math.PI / 4} maxPolarAngle={Math.PI / 1.8} />;
})

// ─── DECAL TRANSFORM HANDLES ─────────────────────────────────────────────────
const DecalTransformHandles = ({ decal, updateDecal, setIsDraggingHandle }) => {
  const [activeCorner, setActiveCorner] = React.useState(null);

  if (!decal || !decal.worldPoint || !decal.worldNormal) return null;

  const point = new THREE.Vector3().fromArray(decal.worldPoint);
  const normal = new THREE.Vector3().fromArray(decal.worldNormal);

  const up = Math.abs(normal.y) < 0.95
    ? new THREE.Vector3(0, 1, 0)
    : new THREE.Vector3(1, 0, 0);
  const right = new THREE.Vector3().crossVectors(up, normal).normalize();
  const newUp = new THREE.Vector3().crossVectors(normal, right).normalize();
  const m4 = new THREE.Matrix4().makeBasis(right, newUp, normal);

  const rotation = decal.rotation || 0;
  const mRotation = new THREE.Matrix4().makeRotationZ(rotation);
  m4.multiply(mRotation);

  const basisX = new THREE.Vector3().setFromMatrixColumn(m4, 0).normalize();
  const basisY = new THREE.Vector3().setFromMatrixColumn(m4, 1).normalize();

  const sx = decal.decalScaleX !== undefined ? decal.decalScaleX : (decal.decalScale || 0.15);
  const sy = decal.decalScaleY !== undefined ? decal.decalScaleY : (decal.decalScale || 0.15);

  let actualSy = sy;
  if (decal.type !== 'image' && decal.type !== 'pattern') {
    actualSy = sy * 0.25;
  }

  const hw = sx / 2;
  const hh = actualSy / 2;

  const corners = [
    { id: 'tl', signX: -1, signY: 1 },
    { id: 'tr', signX: 1, signY: 1 },
    { id: 'br', signX: 1, signY: -1 },
    { id: 'bl', signX: -1, signY: -1 },
  ];

  const cornerPositions = corners.map(c => {
    const pos = point.clone()
      .add(basisX.clone().multiplyScalar(c.signX * hw))
      .add(basisY.clone().multiplyScalar(c.signY * hh))
      .add(normal.clone().multiplyScalar(0.02)); // push out slightly
    return { ...c, pos };
  });

  const handlePointerDown = (e, cornerId) => {
    e.stopPropagation();
    e.target.setPointerCapture(e.pointerId);
    setActiveCorner(cornerId);
    setIsDraggingHandle(true);
  };

  const handlePointerMove = (e, cornerId) => {
    if (activeCorner !== cornerId) return;
    e.stopPropagation();

    const plane = new THREE.Plane().setFromNormalAndCoplanarPoint(normal, point);
    const intersect = new THREE.Vector3();
    e.ray.intersectPlane(plane, intersect);
    if (!intersect) return;

    const offset = intersect.clone().sub(point);

    const newHw = Math.abs(offset.dot(basisX));
    let newHh = Math.abs(offset.dot(basisY));

    let newSx = Math.max(0.01, newHw * 2);
    let newSy = Math.max(0.01, newHh * 2);

    if (decal.type !== 'image' && decal.type !== 'pattern') {
      newSy = newSy / 0.25;
    }

    updateDecal(decal.id, { decalScaleX: newSx, decalScaleY: newSy });
  };

  const handlePointerUp = (e) => {
    if (!activeCorner) return;
    e.stopPropagation();
    e.target.releasePointerCapture(e.pointerId);
    setActiveCorner(null);
    setIsDraggingHandle(false);
  };

  const linePoints = [
    cornerPositions[0].pos,
    cornerPositions[1].pos,
    cornerPositions[2].pos,
    cornerPositions[3].pos,
    cornerPositions[0].pos,
  ];

  const linePointsArray = new Float32Array(linePoints.flatMap(p => [p.x, p.y, p.z]));

  return (
    <group>
      <line>
        <bufferGeometry attach="geometry">
          <bufferAttribute
            attach="attributes-position"
            array={linePointsArray}
            count={5}
            itemSize={3}
          />
        </bufferGeometry>
        <lineBasicMaterial attach="material" color="#00b0f0" depthTest={false} linewidth={2} transparent opacity={0.8} />
      </line>

      {cornerPositions.map(c => (
        <mesh
          key={c.id}
          position={c.pos}
          onPointerDown={(e) => handlePointerDown(e, c.id)}
          onPointerMove={(e) => handlePointerMove(e, c.id)}
          onPointerUp={handlePointerUp}
          onPointerOut={(e) => {
            if (activeCorner === c.id) {
              // optionally handle pointer leaving the sphere if capture fails
            }
          }}
        >
          <sphereGeometry args={[0.02, 16, 16]} />
          <meshBasicMaterial color="#00b0f0" depthTest={false} transparent opacity={0.9} />
        </mesh>
      ))}
    </group>
  );
};

// ─── MESH PART (handles color/pattern shaders) ──────────────────────────────
const MeshPart = memo(function MeshPart({ node, state, finish, globalPattern }) {
  const materialRef = useRef();

  const material = useMemo(() => {
    const mat = node.material.clone();
    mat.side = THREE.DoubleSide; // Ensure inner shell is rendered
    mat.userData.uniforms = {
      uColor: { value: new THREE.Color('#ffffff') },
      uIsGradient: { value: 0.0 },
      uColor1: { value: new THREE.Color('#ffffff') },
      uColor2: { value: new THREE.Color('#ffffff') },
      uHasPattern: { value: 0.0 },
      uPatternColor: { value: new THREE.Color('#ffffff') },
      uPatternTexture: { value: whiteTex },
      uPatternType: { value: 0.0 }, // 0: none, 1: carbon, 2: camo, 3: dots
      uMinY: { value: 0 },
      uMaxY: { value: 1 },
      uPatternSize: { value: 0.2 },
      uPatternOffset: { value: new THREE.Vector3(0, 0, 0) },
      uPatternRotation: { value: 0.0 },
      uPatternMinY: { value: 0.0 },
      uPatternMaxY: { value: 1.0 },
      uPatternMappingMode: { value: 0.0 }, // 0: Seamless UV, 1: Triplanar
      uLocalMatrix: { value: node.matrix }
    };

    mat.onBeforeCompile = (shader) => {
      Object.assign(shader.uniforms, mat.userData.uniforms);

      shader.vertexShader = `
        uniform mat4 uLocalMatrix;
        varying vec3 vLocalPos;
        varying vec3 vModelPos;
        varying vec3 vModelNormal;
        varying vec2 vUv;
        ${shader.vertexShader}
      `.replace(
        `#include <begin_vertex>`,
        `#include <begin_vertex>
         vLocalPos = position;
         vModelPos = (uLocalMatrix * vec4(position, 1.0)).xyz;
         vModelNormal = normalize((uLocalMatrix * vec4(normal, 0.0)).xyz);
         vUv = uv;`
      );

      shader.fragmentShader = `
        uniform vec3 uColor;
        uniform float uIsGradient;
        uniform vec3 uColor1;
        uniform vec3 uColor2;
        uniform float uMinY;
        uniform float uMaxY;
        uniform float uHasPattern;
        uniform vec3 uPatternColor;
        uniform sampler2D uPatternTexture;
        uniform float uPatternType;
        uniform float uPatternSize;
        uniform vec3 uPatternOffset;
        uniform float uPatternRotation;
        uniform float uPatternMinY;
        uniform float uPatternMaxY;
        uniform float uPatternMappingMode;
        varying vec3 vLocalPos;
        varying vec3 vModelPos;
        varying vec3 vModelNormal;
        varying vec2 vUv;

        // Procedural Noise for Camo
        float hash(vec2 p) { return fract(sin(dot(p, vec2(127.1, 311.7))) * 43758.5453123); }
        float noise(vec2 p) {
          vec2 i = floor(p); vec2 f = fract(p);
          f = f*f*(3.0-2.0*f);
          return mix(mix(hash(i + vec2(0,0)), hash(i + vec2(1,0)), f.x),
                     mix(hash(i + vec2(0,1)), hash(i + vec2(1,1)), f.x), f.y);
        }

        ${shader.fragmentShader}
      `.replace(
        `vec4 diffuseColor = vec4( diffuse, opacity );`,
        `
        vec3 baseColor = uColor;
        if (uIsGradient > 0.5) {
          float t = (vLocalPos.y - uMinY) / (uMaxY - uMinY);
          t = clamp(t, 0.0, 1.0);
          t = smoothstep(0.1, 0.9, t);
          baseColor = mix(uColor2, uColor1, t);
        }
        
        vec3 finalColor = baseColor;
        
        // 1. Procedural Global Patterns
        float patStrength = 0.0;
        if (uPatternType > 0.5 && uPatternType < 1.5) { // CARBON
          vec2 grid = fract(vUv * 40.0);
          patStrength = step(0.5, grid.x) == step(0.5, grid.y) ? 0.1 : -0.1;
        } else if (uPatternType > 1.5 && uPatternType < 2.5) { // CAMO
          patStrength = (noise(vUv * 10.0) - 0.5) * 0.4;
        } else if (uPatternType > 2.5) { // DOTS
          vec2 grid = fract(vUv * 30.0) - 0.5;
          patStrength = length(grid) < 0.3 ? -0.2 : 0.0;
        }
        finalColor += patStrength;

         // 2. Mesh-Specific Custom Patterns
        if (uHasPattern > 0.5) {
          // Normalize height coordinate
          float heightT = (vLocalPos.y - uMinY) / (uMaxY - uMinY);
          heightT = clamp(heightT, 0.0, 1.0);

          // Calculate coverage mask with 0.05 soft edge
          float lowerFade = smoothstep(uPatternMinY - 0.05, uPatternMinY + 0.05, heightT);
          float upperFade = 1.0 - smoothstep(uPatternMaxY - 0.05, uPatternMaxY + 0.05, heightT);
          float mask = clamp(lowerFade * upperFade, 0.0, 1.0);

          float sRot = sin(uPatternRotation);
          float cRot = cos(uPatternRotation);

          vec4 patternSample;
          if (uPatternMappingMode < 0.5) {
            // 2D UV Mapping (Seamless continuous fabric): center-pivoted scale, offset, and rotation
            vec2 mappedUv = vUv - vec2(0.5);
            mappedUv = mappedUv / uPatternSize;
            mappedUv = mappedUv + uPatternOffset.xy;
            mappedUv = vec2(mappedUv.x * cRot - mappedUv.y * sRot, mappedUv.x * sRot + mappedUv.y * cRot);
            mappedUv = mappedUv + vec2(0.5);
            patternSample = texture2D(uPatternTexture, mappedUv);
          } else if (uPatternMappingMode > 1.5) {
            // 3D Cylindrical Mapping (3D Wrap around jersey center)
            float angle = atan(vModelPos.z, vModelPos.x);
            float u_cyl = angle * 0.3; // Reference radius of 0.3
            float v_cyl = vModelPos.y;

            vec2 mappedUv = vec2(u_cyl, v_cyl) / uPatternSize + uPatternOffset.xy;
            mappedUv = vec2(mappedUv.x * cRot - mappedUv.y * sRot, mappedUv.x * sRot + mappedUv.y * cRot);

            // Mipmapping seam boundary fix:
            vec2 dx = dFdx(mappedUv);
            vec2 dy = dFdy(mappedUv);
            float maxDeriv = 1.5 / uPatternSize;
            if (abs(dx.x) > maxDeriv) dx.x = 0.0;
            if (abs(dy.x) > maxDeriv) dy.x = 0.0;

            #if __VERSION__ >= 300
            patternSample = textureGrad(uPatternTexture, mappedUv, dx, dy);
            #else
            patternSample = texture2D(uPatternTexture, mappedUv);
            #endif
          } else {
            // 3D Triplanar mapping
            // Rotate the coordinates to support rotation angle properly
            vec3 rotatedPos = vModelPos;
            rotatedPos = vec3(rotatedPos.x * cRot - rotatedPos.z * sRot, rotatedPos.y, rotatedPos.x * sRot + rotatedPos.z * cRot);

            vec3 rotatedNormal = vModelNormal;
            rotatedNormal = vec3(rotatedNormal.x * cRot - rotatedNormal.z * sRot, rotatedNormal.y, rotatedNormal.x * sRot + rotatedNormal.z * cRot);

            vec3 blending = abs(normalize(rotatedNormal));

            // Apply horizontal offset (uPatternOffset.x) to both X and Z projections for horizontal continuity
            vec2 uvX = vec2(rotatedPos.z + uPatternOffset.x, rotatedPos.y + uPatternOffset.y) / uPatternSize;
            vec2 uvY = vec2(rotatedPos.x + uPatternOffset.x, rotatedPos.z + uPatternOffset.y) / uPatternSize;
            vec2 uvZ = vec2(rotatedPos.x + uPatternOffset.x, rotatedPos.y + uPatternOffset.y) / uPatternSize;

            // Determine dominant axis to prevent fading
            if (blending.x >= blending.y && blending.x >= blending.z) {
              patternSample = texture2D(uPatternTexture, uvX);
            } else if (blending.y >= blending.x && blending.y >= blending.z) {
              patternSample = texture2D(uPatternTexture, uvY);
            } else {
              patternSample = texture2D(uPatternTexture, uvZ);
            }
          }
          
          float patVal = patternSample.r * mask;
          
          vec4 cornerSample = texture2D(uPatternTexture, vec2(0.01, 0.01));
          float isBgWhite = step(0.5, cornerSample.r);
          
          if (isBgWhite > 0.5) {
            finalColor = mix(uPatternColor, finalColor, patVal);
          } else {
            finalColor = mix(finalColor, uPatternColor, patVal);
          }
        }

        if (!gl_FrontFacing) {
          finalColor = vec3(0.92, 0.92, 0.92); // Clean light-gray inner fabric color
        }

        vec4 diffuseColor = vec4( finalColor, opacity );
        `
      );
      materialRef.current = shader;
    };
    return mat;
  }, [node]);

  useEffect(() => {
    if (!state) return;
    const u = material.userData.uniforms;
    u.uColor.value.set(state.color).convertSRGBToLinear();
    u.uIsGradient.value = state.isGrad ? 1.0 : 0.0;
    u.uColor1.value.set(state.grad1).convertSRGBToLinear();
    u.uColor2.value.set(state.grad2).convertSRGBToLinear();
    u.uPatternColor.value.set(state.pColor).convertSRGBToLinear();

    u.uPatternSize.value = state.pSize || 0.2;
    u.uPatternOffset.value.set(state.pOffsetX || 0.0, state.pOffsetY || 0.0, state.pOffsetZ || 0.0);
    u.uPatternRotation.value = state.pRotation || 0.0;
    u.uPatternMinY.value = state.pMinY === undefined ? 0.0 : state.pMinY;
    u.uPatternMaxY.value = state.pMaxY === undefined ? 1.0 : state.pMaxY;
    u.uPatternMappingMode.value = state.pMappingMode === undefined ? 0.0 : parseFloat(state.pMappingMode);

    node.geometry.computeBoundingBox();
    u.uMinY.value = node.geometry.boundingBox.min.y;
    u.uMaxY.value = node.geometry.boundingBox.max.y;

    // Apply material finish (gloss/metallic/matte)
    const roughness = finish === 'gloss' ? 0.1 : finish === 'metallic' ? 0.2 : 0.8;
    const metalness = finish === 'metallic' ? 0.8 : 0.0;
    material.roughness = roughness;
    material.metalness = metalness;

    // Handle Pattern (Combine global and local)
    const pType = globalPattern === 'carbon' ? 1.0 : globalPattern === 'camo' ? 2.0 : globalPattern === 'dots' ? 3.0 : 0.0;
    u.uPatternType.value = pType;

    if (state.pUrl) {
      new THREE.TextureLoader().load(state.pUrl, (tex) => {
        tex.wrapS = tex.wrapT = THREE.RepeatWrapping;
        tex.colorSpace = THREE.SRGBColorSpace;
        u.uPatternTexture.value = tex;
        u.uHasPattern.value = 1.0;
      });
    } else {
      u.uHasPattern.value = 0.0;
    }
  }, [state, material, node, finish, globalPattern]);

  return <primitive object={node} material={material} />;
})

// ─── MODEL (with DecalGeometry-based text system from script1.js) ────────────
const Model = memo(function Model({ url, layersMetadata = {}, meshStates, onMeshesDetected, decals, selectedDecalId, setSelectedDecalId, updateDecal, removeDecal, finish, globalPattern, mouseFollow }) {
  const { scene: rootScene, viewport, invalidate } = useThree();
  const { scene } = useGLTF(url);
  const clonedScene = useMemo(() => scene.clone(), [scene]);
  const decalMeshesRef = useRef({});
  const meshRef = useRef();
  const mouse = useRef({ x: 0, y: 0 });

  useEffect(() => {
    if (!mouseFollow) return;
    const handleMove = (e) => {
      mouse.current.x = (e.clientX / window.innerWidth) * 2 - 1;
      mouse.current.y = -(e.clientY / window.innerHeight) * 2 + 1;
    };
    window.addEventListener('mousemove', handleMove);
    return () => window.removeEventListener('mousemove', handleMove);
  }, [mouseFollow]);


  useFrame((state) => {
    if (!meshRef.current) return;
    if (mouseFollow) {
      const targetX = mouse.current.x * 0.8;
      const targetY = mouse.current.y * 0.3;
      meshRef.current.rotation.y = THREE.MathUtils.lerp(meshRef.current.rotation.y, targetX, 0.05);
      meshRef.current.rotation.x = THREE.MathUtils.lerp(meshRef.current.rotation.x, -targetY, 0.05);
    } else {
      // Gentle auto-rotation when mouse follow is OFF
      meshRef.current.rotation.y = THREE.MathUtils.lerp(meshRef.current.rotation.y, 0, 0.05);
      meshRef.current.rotation.x = THREE.MathUtils.lerp(meshRef.current.rotation.x, 0, 0.05);
    }
  });

  const meshes = useMemo(() => {
    const list = [];
    clonedScene.traverse(c => { if (c.isMesh) list.push(c); });
    return list;
  }, [clonedScene]);

  // Detect meshes
  useEffect(() => {
    // 1. Get the overall bounding box of all meshes combined
    const fullBox = new THREE.Box3();
    meshes.forEach(m => {
      fullBox.expandByObject(m);
    });
    const fullCenter = new THREE.Vector3();
    fullBox.getCenter(fullCenter);
    const fullSize = new THREE.Vector3();
    fullBox.getSize(fullSize);

    const nameCounts = {};
    const finalMeshInfo = meshes.map(m => {
      // 2. Calculate relative spatial position to determine the part name dynamically
      const box = new THREE.Box3().setFromObject(m);
      const center = new THREE.Vector3();
      box.getCenter(center);

      // Normalized coordinates from -0.5 to 0.5 relative to the whole model
      const nx = fullSize.x > 0 ? (center.x - fullCenter.x) / fullSize.x : 0;
      const ny = fullSize.y > 0 ? (center.y - fullCenter.y) / fullSize.y : 0;
      const nz = fullSize.z > 0 ? (center.z - fullCenter.z) / fullSize.z : 0;

      let partName = "Body Panel";
      if (ny > 0.3) partName = "Collar"; // Top 20%
      else if (Math.abs(nx) > 0.25) partName = nx > 0 ? "Right Sleeve" : "Left Sleeve"; // Outer sides
      else if (nz > 0.1) partName = "Front Body";
      else if (nz < -0.1) partName = "Back Body";
      else partName = "Side Panel";

      nameCounts[partName] = (nameCounts[partName] || 0) + 1;
      const finalName = nameCounts[partName] > 1
        ? `${partName} ${nameCounts[partName]}`
        : partName;

      const originalHex = `#${m.material.color.getHexString()}`;
      return { id: m.name, display: finalName, originalColor: originalHex };
    });
    onMeshesDetected(finalMeshInfo);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [meshes]);

  // ─── Click-to-Place: dispatch world-space hit info (same as script1.js) ────
  const handleMeshClick = useCallback((e) => {
    e.stopPropagation();
    if (!e.face) return;

    // Check if the clicked mesh is locked and show_lock is false
    const clickedMeshName = e.object.name;
    const meta = layersMetadata[clickedMeshName] || {};
    const parentId = meta.merge_parent || clickedMeshName;
    const parentMeta = layersMetadata[parentId] || {};

    if ((meta.is_locked && meta.show_lock === false) || (parentMeta.is_locked && parentMeta.show_lock === false)) {
      // Ignore click on this locked hidden mesh
      return;
    }

    const clickPoint = e.point.clone();
    const worldNormal = e.face.normal.clone()
      .transformDirection(e.object.matrixWorld)
      .normalize();

    // If a decal is already selected → MOVE it to the clicked location
    if (selectedDecalId) {
      // Don't allow moving pattern decals — they are locked in place
      const selectedDecal = decals.find(d => d.id === selectedDecalId);
      if (selectedDecal && selectedDecal.type === 'pattern') {
        return;
      }

      const clickedMeshName = e.object.name;

      // Find all meshes in the same merged group as clickedMeshName
      const targetMeshes = meshes.filter(m => {
        if (m.name === clickedMeshName) return true;
        const meta = layersMetadata[m.name] || {};
        if (meta.merge_parent === clickedMeshName) return true;
        const clickMeta = layersMetadata[clickedMeshName] || {};
        if (clickMeta.merge_parent === m.name) return true;
        if (meta.merge_parent && meta.merge_parent === clickMeta.merge_parent) return true;
        return false;
      });

      // Find the mesh with the largest bounding box volume in the group
      let primaryMesh = e.object;
      let maxVolume = 0;
      targetMeshes.forEach(m => {
        const b = new THREE.Box3().setFromObject(m);
        const s = b.getSize(new THREE.Vector3());
        const vol = s.x * s.y * s.z;
        if (vol > maxVolume) {
          maxVolume = vol;
          primaryMesh = m;
        }
      });

      updateDecal(selectedDecalId, {
        worldPoint: [clickPoint.x, clickPoint.y, clickPoint.z],
        worldNormal: [worldNormal.x, worldNormal.y, worldNormal.z],
        meshId: primaryMesh.name,
        v: Date.now() // force re-render
      });
      return;
    }

    // If no decal is selected → try to select a nearby decal
    let closestDecal = null;
    let closestDist = 0.15; // wider detection radius

    decals.forEach(d => {
      if (!d.worldPoint) return;
      const decalPos = new THREE.Vector3().fromArray(d.worldPoint);
      const dist = clickPoint.distanceTo(decalPos);
      if (dist < closestDist) {
        closestDist = dist;
        closestDecal = d;
      }
    });

    if (closestDecal) {
      setSelectedDecalId(closestDecal.id);
    }
  }, [setSelectedDecalId, selectedDecalId, updateDecal, decals, layersMetadata]);

  // ─── Auto-place new decals on the front of the shirt ───────────────────────
  useEffect(() => {
    const pending = decals.filter(d => !d.worldPoint);
    if (pending.length === 0 || meshes.length === 0) return;

    pending.forEach(d => {
      let initialMesh = meshes.find(m => m.name === d.meshId) || meshes[0];

      // Find all meshes in the same merged group
      const targetMeshes = meshes.filter(m => {
        if (m.name === initialMesh.name) return true;
        const meta = layersMetadata[m.name] || {};
        if (meta.merge_parent === initialMesh.name) return true;
        const initMeta = layersMetadata[initialMesh.name] || {};
        if (initMeta.merge_parent === m.name) return true;
        if (meta.merge_parent && meta.merge_parent === initMeta.merge_parent) return true;
        return false;
      });

      // Find the mesh with the largest bounding box volume in the group
      let targetMesh = initialMesh;
      let maxVolume = 0;
      targetMeshes.forEach(m => {
        const b = new THREE.Box3().setFromObject(m);
        const s = b.getSize(new THREE.Vector3());
        const vol = s.x * s.y * s.z;
        if (vol > maxVolume) {
          maxVolume = vol;
          targetMesh = m;
        }
      });

      // Force update world matrices immediately to prevent uninitialized matrices
      clonedScene.updateMatrixWorld(true);
      targetMesh.updateMatrixWorld(true);

      const box = new THREE.Box3().setFromObject(targetMesh);
      const center = box.getCenter(new THREE.Vector3());
      const size = box.getSize(new THREE.Vector3());

      // Detect mesh position type dynamically to shoot ray from the correct direction
      let queryPoint = new THREE.Vector3(center.x, center.y + size.y * 0.1, box.max.z); // default front
      let shootDir = new THREE.Vector3(0, 0, -1);
      let shootOrigin = new THREE.Vector3(center.x, center.y + size.y * 0.1, box.max.z + 1);

      if (center.z < -0.1) {
        // Back
        queryPoint.set(center.x, center.y + size.y * 0.1, box.min.z);
        shootDir.set(0, 0, 1);
        shootOrigin.set(center.x, center.y + size.y * 0.1, box.min.z - 1);
      } else if (center.x < -0.25) {
        // Left
        queryPoint.set(box.min.x, center.y, center.z);
        shootDir.set(1, 0, 0);
        shootOrigin.set(box.min.x - 1, center.y, center.z);
      } else if (center.x > 0.25) {
        // Right
        queryPoint.set(box.max.x, center.y, center.z);
        shootDir.set(-1, 0, 0);
        shootOrigin.set(box.max.x + 1, center.y, center.z);
      }

      // Calculate dynamic scale for patterns so they cover the entire mesh
      let scaleX = d.decalScaleX;
      let scaleY = d.decalScaleY;
      let scale = d.decalScale;

      // For patterns: store coverage size separately based on the entire merged group's bounding box
      let patternCoverageX = d.patternCoverageX;
      let patternCoverageY = d.patternCoverageY;
      if (d.type === 'pattern') {
        const groupBox = new THREE.Box3();
        targetMeshes.forEach(m => {
          m.updateMatrixWorld(true);
          const b = new THREE.Box3().setFromObject(m);
          groupBox.union(b);
        });
        const groupSize = groupBox.getSize(new THREE.Vector3());

        // Use a generous 3.0x multiplier to guarantee full coverage of all merged panels (e.g. sleeve + back)
        patternCoverageX = Math.max(groupSize.x, groupSize.z) * 3.0;
        patternCoverageY = groupSize.y * 3.0;
        // Don't override decalScale — it controls tile density, not coverage
      }

      const raycaster = new THREE.Raycaster();
      raycaster.set(shootOrigin, shootDir);

      const hits = raycaster.intersectObject(targetMesh, true).filter(h => h.object === targetMesh);
      if (hits.length > 0) {
        const hit = hits[0];
        const wn = hit.face.normal.clone()
          .transformDirection(hit.object.matrixWorld)
          .normalize();

        const baseUpdate = {
          worldPoint: [hit.point.x, hit.point.y, hit.point.z],
          worldNormal: [wn.x, wn.y, wn.z],
          meshId: hit.object.name,
          decalScale: scale,
          decalScaleX: scaleX,
          decalScaleY: scaleY
        };
        // For patterns, store coverage size separately
        if (d.type === 'pattern') {
          baseUpdate.patternCoverageX = patternCoverageX;
          baseUpdate.patternCoverageY = patternCoverageY;
          delete baseUpdate.decalScale; // Don't override — keep default 0.8 for tile density
        }
        updateDecal(d.id, baseUpdate);
      } else {
        // Fallback: Find the closest vertex of the target mesh on the detected side
        const positionAttr = targetMesh.geometry?.attributes?.position;
        const normalAttr = targetMesh.geometry?.attributes?.normal;
        if (positionAttr) {
          let bestPoint = new THREE.Vector3();
          let bestNormal = new THREE.Vector3(0, 0, 1);
          let minD = Infinity;

          const tempV = new THREE.Vector3();
          const step = Math.max(1, Math.floor(positionAttr.count / 100));
          for (let i = 0; i < positionAttr.count; i += step) {
            tempV.fromBufferAttribute(positionAttr, i);
            tempV.applyMatrix4(targetMesh.matrixWorld);

            const dVal = tempV.distanceTo(queryPoint);
            if (dVal < minD) {
              minD = dVal;
              bestPoint.copy(tempV);
              if (normalAttr) {
                bestNormal.fromBufferAttribute(normalAttr, i);
              }
            }
          }

          if (normalAttr) {
            const normalMatrix = new THREE.Matrix3().getNormalMatrix(targetMesh.matrixWorld);
            bestNormal.applyMatrix3(normalMatrix).normalize();
          }

          const fallbackUpdate = {
            worldPoint: [bestPoint.x, bestPoint.y, bestPoint.z],
            worldNormal: [bestNormal.x, bestNormal.y, bestNormal.z],
            meshId: targetMesh.name,
            decalScale: scale,
            decalScaleX: scaleX,
            decalScaleY: scaleY
          };
          if (d.type === 'pattern') {
            fallbackUpdate.patternCoverageX = patternCoverageX;
            fallbackUpdate.patternCoverageY = patternCoverageY;
            delete fallbackUpdate.decalScale;
          }
          updateDecal(d.id, fallbackUpdate);
        } else {
          // If no geometry attributes, fallback to bounding box center
          const centerUpdate = {
            worldPoint: [center.x, center.y, center.z],
            worldNormal: [0, 0, 1],
            meshId: targetMesh.name,
            decalScale: scale,
            decalScaleX: scaleX,
            decalScaleY: scaleY
          };
          if (d.type === 'pattern') {
            centerUpdate.patternCoverageX = patternCoverageX;
            centerUpdate.patternCoverageY = patternCoverageY;
            delete centerUpdate.decalScale;
          }
          updateDecal(d.id, centerUpdate);
        }
      }
    });
  }, [decals, meshes, updateDecal, layersMetadata]);

  // ─── DecalGeometry Management (exact script1.js approach, extended for merged groups) ──────────────────
  useEffect(() => {
    const currentIds = new Set(decals.map(d => d.id));

    // Remove deleted decals
    Object.keys(decalMeshesRef.current).forEach(id => {
      if (!currentIds.has(id)) {
        const list = decalMeshesRef.current[id];
        if (list) {
          list.forEach(m => {
            if (m.parent) {
              m.parent.remove(m);
            } else {
              rootScene.remove(m);
            }
            m.geometry.dispose();
            m.material.dispose();
          });
        }
        delete decalMeshesRef.current[id];
      }
    });

    // Create / update decals
    decals.forEach(d => {
      if (!d.worldPoint || !d.worldNormal) return;

      const targetMesh = meshes.find(m => m.name === d.meshId);
      if (!targetMesh) return;

      // Version check — only recreate if something changed
      const version = `${d.type}_${d.text}_${d.imageUrl || ''}_${d.worldPoint.join(',')}_${d.color}_${d.decalScale || 0.15}_${d.decalScaleX || 0}_${d.decalScaleY || 0}_${d.pFadeTop || 0}_${d.pFadeBottom || 0}_${d.pFadeLeft || 0}_${d.pFadeRight || 0}_${d.pFadeTopLeft || 0}_${d.pFadeTopRight || 0}_${d.pFadeBottomLeft || 0}_${d.pFadeBottomRight || 0}_${d.font}_${d.outline1Color}_${d.outline1Width}_${d.outline2Color}_${d.outline2Width}_${d.effect}_${d.effectIntensity}_${d.tail}_${d.v || 0}_${d.rotation || 0}`;
      const existingList = decalMeshesRef.current[d.id];
      if (existingList && existingList.length > 0 && existingList[0].userData._v === version) return;

      // Remove old version
      if (existingList) {
        existingList.forEach(m => {
          if (m.parent) {
            m.parent.remove(m);
          } else {
            rootScene.remove(m);
          }
          m.geometry.dispose();
          m.material.dispose();
        });
        delete decalMeshesRef.current[d.id];
      }

      try {
        // Create texture canvas based on type
        let canvas;
        let isCached = false;
        if (d.type === 'pattern' && d.imageUrl) {
          const cachedCanvas = getPatternCanvasSync(d.imageUrl, d.color);
          if (cachedCanvas) {
            canvas = cachedCanvas;
            isCached = true;
          } else {
            canvas = createImageCanvas(d.imageUrl);
          }
        } else if (d.type === 'image' && d.imageUrl) {
          const cachedCanvas = getImageCanvasSync(d.imageUrl);
          if (cachedCanvas) {
            canvas = cachedCanvas;
            isCached = true;
          } else {
            canvas = createImageCanvas(d.imageUrl);
          }
        } else {
          canvas = createTextCanvas(d);
        }
        const texture = new THREE.CanvasTexture(canvas);
        texture.colorSpace = THREE.SRGBColorSpace;

        // For pattern decals: set RepeatWrapping so tiled UVs wrap correctly
        if (d.type === 'pattern') {
          texture.wrapS = THREE.RepeatWrapping;
          texture.wrapT = THREE.RepeatWrapping;
        }

        // Build orientation from normal
        const point = new THREE.Vector3().fromArray(d.worldPoint);
        const normal = new THREE.Vector3().fromArray(d.worldNormal);

        const up = Math.abs(normal.y) < 0.95
          ? new THREE.Vector3(0, 1, 0)
          : new THREE.Vector3(1, 0, 0);
        const right = new THREE.Vector3().crossVectors(up, normal).normalize();
        const newUp = new THREE.Vector3().crossVectors(normal, right).normalize();
        const m4 = new THREE.Matrix4().makeBasis(right, newUp, normal);

        // Apply decal rotation around its projection normal vector (Z-axis in basis coordinates)
        const rotation = d.rotation || 0;
        const mRotation = new THREE.Matrix4().makeRotationZ(rotation);
        m4.multiply(mRotation);

        const orientation = new THREE.Euler().setFromRotationMatrix(m4);

        let sx, sy;
        if (d.type === 'pattern') {
          // For patterns: use stored coverage size (always covers full mesh)
          sx = d.patternCoverageX || (d.decalScaleX !== undefined ? d.decalScaleX : 1.0);
          sy = d.patternCoverageY || (d.decalScaleY !== undefined ? d.decalScaleY : 1.0);
        } else {
          sx = d.decalScaleX !== undefined ? d.decalScaleX : (d.decalScale || 0.15);
          sy = d.decalScaleY !== undefined ? d.decalScaleY : (d.decalScale || 0.15);
        }
        // Use a shallow depth (0.3m) for text and image decals so they do not bleed through to the back body.
        // Keep 3.0m for patterns since they are meant to project all the way through to both panels.
        const decalSize = (d.type === 'image' || d.type === 'pattern')
          ? new THREE.Vector3(sx, sy, d.type === 'pattern' ? 3.0 : 0.3)
          : new THREE.Vector3(sx, sy * 0.25, 0.3);

        // Find target meshes — always use the merged group (including for patterns)
        const targetMeshes = meshes.filter(m => {
          if (m.name === d.meshId) return true;

          // Child mesh target: check if child's parent is d.meshId
          const meta = layersMetadata[m.name] || {};
          if (meta.merge_parent === d.meshId) return true;

          // Parent target: check if d.meshId itself is a child and has this mesh as parent
          const dMeta = layersMetadata[d.meshId] || {};
          if (dMeta.merge_parent === m.name) return true;

          // Sibling target: check if they share the same parent
          if (meta.merge_parent && meta.merge_parent === dMeta.merge_parent) return true;

          return false;
        });

        const meshesCreatedForDecal = [];

        // For patterns: compute ONE global projection matrix for consistent tiling across all meshes
        let globalDecalMatrixInv = null;
        let globalDecalSize = null;
        let globalRepeatFactor = null;
        if (d.type === 'pattern') {
          const globalBox = new THREE.Box3();
          targetMeshes.forEach(m => {
            m.updateMatrixWorld(true);
            globalBox.union(new THREE.Box3().setFromObject(m));
          });
          const globalCenter = globalBox.getCenter(new THREE.Vector3());
          const globalSz = globalBox.getSize(new THREE.Vector3());
          const gMinCover = 0.5;
          const gCoverX = Math.max(gMinCover, Math.max(globalSz.x, globalSz.z) * 3.0);
          const gCoverY = Math.max(gMinCover, globalSz.y * 3.0);
          const gCoverZ = Math.max(gMinCover, Math.max(globalSz.x, globalSz.y, globalSz.z) * 3.0);
          globalDecalSize = new THREE.Vector3(gCoverX, gCoverY, gCoverZ);

          const quaternion = new THREE.Quaternion().setFromEuler(orientation);
          const globalDecalMatrix = new THREE.Matrix4().compose(globalCenter, quaternion, globalDecalSize);
          globalDecalMatrixInv = globalDecalMatrix.clone().invert();
          globalRepeatFactor = 8.0 / Math.max(0.05, d.decalScale || 0.8);
        }

        targetMeshes.forEach(tMesh => {
          try {
            let geo;
            if (d.type === 'pattern') {
              // For patterns: clone the mesh geometry directly instead of using DecalGeometry.
              // DecalGeometry projects from one direction and clips faces outside its box,
              // which misses back/side faces on cylindrical meshes (collars, cuffs, rings).
              // Cloning guarantees 100% coverage regardless of mesh shape.
              geo = tMesh.geometry.clone();
            } else {
              geo = new DecalGeometry(tMesh, point, orientation, decalSize);
            }

            let mat;
            if (d.type === 'pattern') {
              mat = new THREE.ShaderMaterial({
                extensions: { derivatives: true },
                uniforms: {
                  map: { value: texture },
                  uRepeat: { value: globalRepeatFactor },
                  uDecalMatrixInv: { value: globalDecalMatrixInv.clone().multiply(tMesh.matrixWorld) },
                  uDecalSize: { value: globalDecalSize },
                  uFadeTop: { value: d.pFadeTop || 0.0 },
                  uFadeBottom: { value: d.pFadeBottom || 0.0 },
                  uFadeLeft: { value: d.pFadeLeft || 0.0 },
                  uFadeRight: { value: d.pFadeRight || 0.0 },
                  uFadeTopLeft: { value: d.pFadeTopLeft || 0.0 },
                  uFadeTopRight: { value: d.pFadeTopRight || 0.0 },
                  uFadeBottomLeft: { value: d.pFadeBottomLeft || 0.0 },
                  uFadeBottomRight: { value: d.pFadeBottomRight || 0.0 },
                },
                vertexShader: `
                  uniform mat4 uDecalMatrixInv;
                  varying vec2 vUv;
                  varying vec3 vDecalLocalPos;
                  varying vec3 vDecalLocalNormal;
                  void main() {
                    vUv = uv;
                    vDecalLocalPos = (uDecalMatrixInv * vec4(position, 1.0)).xyz;
                    vDecalLocalNormal = normalize((uDecalMatrixInv * vec4(normal, 0.0)).xyz);
                    gl_Position = projectionMatrix * viewMatrix * modelMatrix * vec4(position, 1.0);
                  }
                `,
                fragmentShader: `
                  uniform sampler2D map;
                  uniform float uRepeat;
                  uniform vec3 uDecalSize;
                  uniform float uFadeTop;
                  uniform float uFadeBottom;
                  uniform float uFadeLeft;
                  uniform float uFadeRight;
                  uniform float uFadeTopLeft;
                  uniform float uFadeTopRight;
                  uniform float uFadeBottomLeft;
                  uniform float uFadeBottomRight;
                  varying vec2 vUv;
                  varying vec3 vDecalLocalPos;
                  varying vec3 vDecalLocalNormal;
                  void main() {
                    if (!gl_FrontFacing) discard;
                    vec3 unscaledPos = vDecalLocalPos * uDecalSize;
                    vec3 unscaledNormal = normalize(vDecalLocalNormal * uDecalSize);
                    vec3 blending = abs(unscaledNormal);
                    blending = pow(blending, vec3(16.0));
                    blending = blending / (blending.x + blending.y + blending.z);

                    vec2 uvX = unscaledPos.zy;
                    vec2 uvY = unscaledPos.xz;
                    vec2 uvZ = unscaledPos.xy;

                    vec2 tiledUvX = uvX * uRepeat;
                    vec2 tiledUvY = uvY * uRepeat;
                    vec2 tiledUvZ = uvZ * uRepeat;

                    vec4 colX = texture2D(map, tiledUvX);
                    vec4 colY = texture2D(map, tiledUvY);
                    vec4 colZ = texture2D(map, tiledUvZ);

                    vec4 texColor;
                    if (blending.z >= blending.x && blending.z >= blending.y) {
                      texColor = colZ;
                    } else if (blending.x >= blending.y && blending.x >= blending.z) {
                      texColor = colX;
                    } else {
                      texColor = colY;
                    }
                    gl_FragColor = vec4(texColor.rgb, texColor.a);
                  }
                `,
                transparent: true,
                depthTest: true,
                depthWrite: false,
                polygonOffset: true,
                polygonOffsetFactor: -1.0,
                polygonOffsetUnits: -1.0,
                side: THREE.DoubleSide,
              });
            } else {
              mat = new THREE.MeshBasicMaterial({
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
              // For non-patterns: transform geometry to target mesh's local space
              const localMatrix = tMesh.matrixWorld.clone().invert();
              geo.applyMatrix4(localMatrix);
            }

            const decalMesh = new THREE.Mesh(geo, mat);
            decalMesh.renderOrder = d.type === 'pattern' ? 997 : (d.type === 'image' ? 998 : 999);
            decalMesh.userData._v = version;
            decalMesh.userData.decalId = d.id;

            tMesh.add(decalMesh);
            meshesCreatedForDecal.push(decalMesh);
          } catch (err) {
            console.warn('Decal skip for submesh:', tMesh.name, err);
          }
        });

        decalMeshesRef.current[d.id] = meshesCreatedForDecal;

        // For image decals: async load real image, then update texture
        if (d.type === 'image' && d.imageUrl) {
          if (!isCached) {
            loadImageToCanvas(d.imageUrl).then(imgCanvas => {
              if (decalMeshesRef.current[d.id] === meshesCreatedForDecal) {
                updateDecal(d.id, { v: Date.now() });
              }
            });
          }
        } else if (d.type === 'pattern' && d.imageUrl) {
          if (!isCached) {
            loadPatternToCanvas(d.imageUrl, d.color).then(imgCanvas => {
              if (decalMeshesRef.current[d.id] === meshesCreatedForDecal) {
                updateDecal(d.id, { v: Date.now() });
              }
            });
          }
        }
      } catch (err) {
        console.error('DecalGeometry error:', err);
      }
    });
    invalidate();
  }, [decals, meshes, rootScene, invalidate, updateDecal, layersMetadata]);

  // Cleanup on unmount
  useEffect(() => {
    return () => {
      Object.values(decalMeshesRef.current).forEach(list => {
        if (Array.isArray(list)) {
          list.forEach(m => {
            if (m.parent) {
              m.parent.remove(m);
            } else {
              rootScene.remove(m);
            }
            m.geometry.dispose();
            m.material.dispose();
          });
        }
      });
      decalMeshesRef.current = {};
    };
  }, [rootScene]);

  return (
    <group ref={meshRef} scale={1.8} onPointerDown={handleMeshClick}>
      {meshes.map(m => {
        const meta = layersMetadata[m.name] || {};
        const stateKey = meta.merge_parent || m.name;
        const parentMeta = layersMetadata[stateKey] || {};
        const isLocked = meta.is_locked || parentMeta.is_locked;

        if (isLocked) {
          return (
            <primitive key={m.uuid} object={m} />
          );
        }

        return (
          <MeshPart key={m.uuid} node={m} state={meshStates[stateKey]} finish={finish} globalPattern={globalPattern} />
        );
      })}
    </group>
  );
})

// ─── MAIN VIEWER ─────────────────────────────────────────────────────────────
const ModelViewer = memo(({ modelUrl, layersMetadata = {}, meshStates, onMeshesDetected, decals, selectedDecalId, setSelectedDecalId, updateDecal, removeDecal, globalPattern, materialFinish, lightingPreset, mouseFollow }) => {
  const [isDraggingHandle, setIsDraggingHandle] = React.useState(false);
  const selectedDecal = decals.find(d => d.id === selectedDecalId);

  return (
    <div className="flex-1 w-full bg-white relative" style={{ height: '100%' }}>
      <Canvas camera={{ position: [0, 0, 2.5], fov: 42 }} gl={{ preserveDrawingBuffer: true, antialias: true }} onPointerMissed={() => setSelectedDecalId(null)}>
        <ambientLight intensity={lightingPreset === 'night' ? 0.2 : 0.8} />
        <spotLight position={[10, 15, 10]} angle={0.3} penumbra={1} intensity={lightingPreset === 'studio' ? 2.5 : 1.5} />
        <directionalLight position={[-5, 5, -5]} intensity={lightingPreset === 'night' ? 0.1 : 0.5} />
        <Suspense fallback={<CoolLoader />}>
          <Center>
            <Model
              url={modelUrl}
              layersMetadata={layersMetadata}
              meshStates={meshStates}
              onMeshesDetected={onMeshesDetected}
              decals={decals}
              selectedDecalId={selectedDecalId}
              setSelectedDecalId={setSelectedDecalId}
              updateDecal={updateDecal}
              removeDecal={removeDecal}
              finish={materialFinish}
              globalPattern={globalPattern}
              mouseFollow={mouseFollow}
            />
          </Center>

          {/* Floating Controls — theme-matched, just above text */}
          {/* Hide floating controls for pattern decals — patterns are locked in place */}
          {selectedDecal && selectedDecal.worldPoint && selectedDecal.type !== 'pattern' && (
            <Html
              position={[selectedDecal.worldPoint[0], selectedDecal.worldPoint[1] + 0.06, selectedDecal.worldPoint[2] + 0.02]}
              center
              style={{ pointerEvents: 'none' }}
              zIndexRange={[100, 0]}
            >
              <div className="flex items-center gap-0.5 bg-white rounded-full shadow-lg border border-gray-100 px-1 py-0.5" style={{ whiteSpace: 'nowrap', pointerEvents: 'auto' }}>
                <button
                  onClick={(ev) => {
                    ev.stopPropagation();
                    const scale = selectedDecal.decalScale || 0.15;
                    const nextScale = Math.max(0.01, scale - 0.02);
                    const ratio = nextScale / scale;
                    const updates = { decalScale: nextScale };
                    if (selectedDecal.decalScaleX !== undefined) {
                      updates.decalScaleX = selectedDecal.decalScaleX * ratio;
                    }
                    if (selectedDecal.decalScaleY !== undefined) {
                      updates.decalScaleY = selectedDecal.decalScaleY * ratio;
                    }
                    updateDecal(selectedDecalId, updates);
                  }}
                  title="Decrease Size"
                  className="w-5 h-5 flex items-center justify-center rounded-full hover:bg-[#00b0f0]/10 text-[#00b0f0] text-[11px] font-bold transition-colors"
                >−</button>
                <button
                  onClick={(ev) => {
                    ev.stopPropagation();
                    const scale = selectedDecal.decalScale || 0.15;
                    const nextScale = scale + 0.02;
                    const ratio = nextScale / scale;
                    const updates = { decalScale: nextScale };
                    if (selectedDecal.decalScaleX !== undefined) {
                      updates.decalScaleX = selectedDecal.decalScaleX * ratio;
                    }
                    if (selectedDecal.decalScaleY !== undefined) {
                      updates.decalScaleY = selectedDecal.decalScaleY * ratio;
                    }
                    updateDecal(selectedDecalId, updates);
                  }}
                  title="Increase Size"
                  className="w-5 h-5 flex items-center justify-center rounded-full hover:bg-[#00b0f0]/10 text-[#00b0f0] text-[11px] font-bold transition-colors"
                >+</button>
                <div className="w-px h-3 bg-gray-200" />
                <button
                  onClick={(ev) => { ev.stopPropagation(); updateDecal(selectedDecalId, { rotation: (selectedDecal.rotation || 0) - 15 * Math.PI / 180 }); }}
                  title="Rotate Counter-Clockwise"
                  className="w-5 h-5 flex items-center justify-center rounded-full hover:bg-[#00b0f0]/10 text-[#00b0f0] text-[11px] font-bold transition-colors"
                >⟲</button>
                <button
                  onClick={(ev) => { ev.stopPropagation(); updateDecal(selectedDecalId, { rotation: (selectedDecal.rotation || 0) + 15 * Math.PI / 180 }); }}
                  title="Rotate Clockwise"
                  className="w-5 h-5 flex items-center justify-center rounded-full hover:bg-[#00b0f0]/10 text-[#00b0f0] text-[11px] font-bold transition-colors"
                >⟳</button>
                <div className="w-px h-3 bg-gray-200" />
                <button
                  onClick={(ev) => { ev.stopPropagation(); removeDecal(selectedDecalId); }}
                  title="Remove Layer"
                  className="w-5 h-5 flex items-center justify-center rounded-full hover:bg-red-50 text-red-400 text-[10px] transition-colors"
                >✕</button>
              </div>
            </Html>
          )}

          <Environment preset={lightingPreset || "city"} />
          <ContactShadows position={[0, -1.5, 0]} opacity={0.4} scale={15} blur={2.5} far={4} />

          {/* Hide blue boundary box for pattern decals — patterns are locked in place */}
          {selectedDecal && selectedDecal.worldPoint && selectedDecal.type !== 'pattern' && (
            <DecalTransformHandles
              decal={selectedDecal}
              updateDecal={updateDecal}
              setIsDraggingHandle={setIsDraggingHandle}
            />
          )}

        </Suspense>
        <CameraController mouseFollow={mouseFollow} isDragging={isDraggingHandle} />
      </Canvas>
    </div>
  );
});

export default ModelViewer;
