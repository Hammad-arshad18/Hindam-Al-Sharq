/* Hindam Al-Sharq — how cloth is made, in one continuous 3D scene scrubbed by scrolling.

   The same 2,160 fibres play every part of the story: a loose cloud of cotton, then a twisted
   yarn, then the warp laid out thread by thread. A loom assembles around that warp, comes apart
   into its working parts, goes back together and weaves.

   Timeline (scroll progress 0 → 1). The chapter stops in index.php follow these numbers.
     0.00  fibre cloud
     0.13  fibres are drawn into a strand and twisted
     0.30  the strand opens out into the warp; the loom arrives around it
     0.47  the loom comes apart, parts are named
     0.63  it goes back together
     0.70  weaving
     0.88  the camera goes down into the cloth and the scene hands over to the page's own fabric
*/
import * as THREE from 'three';
import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';

const clamp = (n, a = 0, b = 1) => Math.min(b, Math.max(a, n));
const span = (p, a, b) => clamp((p - a) / (b - a));
const ease = (t) => t * t * t * (t * (t * 6 - 15) + 10);
const lerp = (a, b, t) => a + (b - a) * t;
const TAU = Math.PI * 2;

const INK = 0x0b2545;
const BONE = 0xede4d3;
const RED = 0xff6a00;

// Loom dimensions, in metres-ish. x runs across the cloth, z from the back (warp beam) to the front (cloth roll).
const ENDS = 120;            // warp threads
const SEGS = 30;             // fibres per warp thread
const HALF = 1.7;            // half the cloth width
const WARP_Y = 1.5;          // height of the warp sheet
const Z_BACK = -1.95;        // back rest
const Z_FELL = 0.9;          // where warp becomes cloth
const Z_BREAST = 1.85;       // breast beam
const Z_HEDDLE = [-0.75, -0.5];
const LIFT = 0.3;            // how far a heddle frame travels
const SLEY_PIVOT = { y: 0.22, z: 0.5 };
const SLEY_REST = -0.3;
const SLEY_BEAT = 0.29;

function mulberry(seed) {
  let a = seed >>> 0;
  return () => {
    a = (a + 0x6d2b79f5) >>> 0;
    let t = a;
    t = Math.imul(t ^ (t >>> 15), t | 1);
    t ^= t + Math.imul(t ^ (t >>> 7), t | 61);
    return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
  };
}

function weaveTexture(renderer) {
  const c = document.createElement('canvas');
  c.width = c.height = 512;
  const g = c.getContext('2d');
  const n = 32;
  const s = c.width / n;
  const rnd = mulberry(7);
  g.fillStyle = '#C94E06';
  g.fillRect(0, 0, c.width, c.height);
  for (let y = 0; y < n; y++) {
    for (let x = 0; x < n; x++) {
      const over = (x + y) % 2 === 0;                       // weft on top here
      const v = Math.round((rnd() - 0.5) * 14);
      g.fillStyle = over ? `rgb(${226 + v}, ${98 + v}, ${22 + v})` : `rgb(${170 + v}, ${62}, ${8})`;
      g.fillRect(x * s, y * s, s, s);
      g.fillStyle = over ? 'rgba(255, 176, 156, .26)' : 'rgba(0, 0, 0, .2)';
      if (over) g.fillRect(x * s, y * s + s * 0.22, s, s * 0.3);
      else g.fillRect(x * s + s * 0.3, y * s, s * 0.3, s);
    }
  }
  // A few slubs, so the eye can see the cloth travel.
  for (let i = 0; i < 9; i++) {
    g.fillStyle = `rgba(255, 190, 170, ${0.10 + rnd() * 0.12})`;
    g.fillRect(rnd() * c.width, Math.floor(rnd() * n) * s + s * 0.2, 30 + rnd() * 110, s * 0.5);
  }
  const tex = new THREE.CanvasTexture(c);
  tex.wrapS = tex.wrapT = THREE.RepeatWrapping;
  tex.colorSpace = THREE.SRGBColorSpace;
  tex.anisotropy = renderer.capabilities.getMaxAnisotropy();
  return tex;
}

function woundTexture() {
  const c = document.createElement('canvas');
  c.width = 4; c.height = 512;
  const g = c.getContext('2d');
  const rnd = mulberry(3);
  for (let y = 0; y < c.height; y += 2) {
    const v = 205 + Math.round(rnd() * 34);
    g.fillStyle = `rgb(${v}, ${v - 8}, ${v - 24})`;
    g.fillRect(0, y, 4, 2);
  }
  const tex = new THREE.CanvasTexture(c);
  tex.colorSpace = THREE.SRGBColorSpace;
  return tex;
}

function floorTexture() {
  const c = document.createElement('canvas');
  c.width = c.height = 256;
  const g = c.getContext('2d');
  const grad = g.createRadialGradient(128, 128, 0, 128, 128, 128);
  grad.addColorStop(0, 'rgba(255, 244, 230, .16)');
  grad.addColorStop(0.55, 'rgba(255, 244, 230, .05)');
  grad.addColorStop(1, 'rgba(255, 244, 230, 0)');
  g.fillStyle = grad;
  g.fillRect(0, 0, 256, 256);
  return new THREE.CanvasTexture(c);
}

export function createLoom({ canvas, labels = [], rtl = false, reduceMotion = false }) {
  let renderer;
  try {
    renderer = new THREE.WebGLRenderer({ canvas, antialias: true, powerPreference: 'high-performance' });
  } catch (err) {
    return null;
  }
  const small = Math.min(innerWidth, innerHeight) < 700;
  renderer.setClearColor(INK);
  renderer.outputColorSpace = THREE.SRGBColorSpace;
  renderer.toneMapping = THREE.ACESFilmicToneMapping;
  renderer.toneMappingExposure = 1.1;
  renderer.shadowMap.enabled = !small;
  renderer.shadowMap.type = THREE.PCFSoftShadowMap;

  const scene = new THREE.Scene();
  const pmrem = new THREE.PMREMGenerator(renderer);
  scene.environment = pmrem.fromScene(new RoomEnvironment(), 0.04).texture;
  pmrem.dispose();

  const camera = new THREE.PerspectiveCamera(31, 1, 0.1, 80);
  scene.fog = new THREE.Fog(INK, 1, 30);                        // distance falls away into the page's black

  const key = new THREE.DirectionalLight(0xfff0de, 2.6);
  key.position.set(-4.5, 8, 5.5);
  key.castShadow = true;
  key.shadow.mapSize.set(2048, 2048);
  Object.assign(key.shadow.camera, { left: -6, right: 6, top: 6, bottom: -6, near: 1, far: 26 });
  key.shadow.bias = -0.0004;
  key.shadow.normalBias = 0.02;
  key.shadow.radius = 5;
  scene.add(key);
  const rim = new THREE.DirectionalLight(0xff7a1c, 1.5);        // an orange edge light from behind
  rim.position.set(5, 3, -7);
  scene.add(rim);
  scene.add(new THREE.HemisphereLight(0xcfd6ff, 0x1a0a06, 0.35));

  // Everything is built facing one way and mirrored for right-to-left pages.
  const side = rtl ? -1 : 1;
  const root = new THREE.Group();
  root.scale.x = side;
  scene.add(root);

  /* ---------- Materials ---------- */
  const mFrame = new THREE.MeshStandardMaterial({ color: 0x1a2b45, metalness: 0.9, roughness: 0.4, envMapIntensity: 1.1 });
  const mSteel = new THREE.MeshStandardMaterial({ color: 0xcfcabf, metalness: 1, roughness: 0.26, envMapIntensity: 1.1 });
  const mThread = new THREE.MeshStandardMaterial({ color: BONE, roughness: 0.82, metalness: 0, envMapIntensity: 0.55 });
  const mWound = new THREE.MeshStandardMaterial({ color: 0xffffff, map: woundTexture(), roughness: 0.85, envMapIntensity: 0.5 });
  const mRed = new THREE.MeshStandardMaterial({ color: RED, roughness: 0.6, envMapIntensity: 0.6 });
  const mWood = new THREE.MeshStandardMaterial({ color: 0xc19a6b, roughness: 0.5, envMapIntensity: 0.7 });
  const clothMap = weaveTexture(renderer);
  const clothProps = { color: 0xffffff, roughness: 0.78, sheen: 1, sheenColor: new THREE.Color(0xffa860), sheenRoughness: 0.45, envMapIntensity: 0.6, side: THREE.DoubleSide };
  const mapFor = (rx, ry) => { const t = clothMap.clone(); t.repeat.set(rx, ry); t.needsUpdate = true; return t; };
  const mClothTop = new THREE.MeshPhysicalMaterial({ ...clothProps, map: mapFor(14, 3.9) });
  const mClothDown = new THREE.MeshPhysicalMaterial({ ...clothProps, map: mapFor(14, 3.4) });
  const mClothRoll = new THREE.MeshPhysicalMaterial({ ...clothProps, map: mapFor(6, 14) });
  mClothRoll.map.rotation = Math.PI / 2;
  const loomMats = [mFrame, mSteel, mWound, mRed, mWood, mClothTop, mClothDown, mClothRoll];
  const clothMaps = [mClothTop.map, mClothDown.map];

  /* ---------- Small builders ---------- */
  const shadowed = (mesh) => { mesh.castShadow = true; mesh.receiveShadow = true; return mesh; };
  const box = (w, h, d, mat) => shadowed(new THREE.Mesh(new THREE.BoxGeometry(w, h, d), mat));
  // A bar lying across the loom (along x).
  const rod = (r, len, mat, seg = 20) => {
    const geo = new THREE.CylinderGeometry(r, r, len, seg);
    geo.rotateZ(Math.PI / 2);
    return shadowed(new THREE.Mesh(geo, mat));
  };
  const at = (obj, x, y, z) => { obj.position.set(x, y, z); return obj; };

  const parts = [];
  // A part knows where it lives and which way it leaves when the loom is taken apart.
  const part = (x, y, z, ox, oy, oz) => {
    const g = new THREE.Group();
    g.userData.home = new THREE.Vector3(x, y, z);
    g.userData.out = new THREE.Vector3(ox, oy, oz);
    g.position.copy(g.userData.home);
    root.add(g);
    parts.push(g);
    return g;
  };
  const anchor = (group, x, y, z) => { const o = new THREE.Object3D(); o.position.set(x, y, z); group.add(o); return o; };

  /* ---------- Side frames ---------- */
  const roundRect = (x, y, w, h, r) => {
    const p = new THREE.Path();
    p.moveTo(x + r, y);
    p.lineTo(x + w - r, y); p.absarc(x + w - r, y + r, r, -Math.PI / 2, 0, false);
    p.lineTo(x + w, y + h - r); p.absarc(x + w - r, y + h - r, r, 0, Math.PI / 2, false);
    p.lineTo(x + r, y + h); p.absarc(x + r, y + h - r, r, Math.PI / 2, Math.PI, false);
    p.lineTo(x, y + r); p.absarc(x + r, y + r, r, Math.PI, Math.PI * 1.5, false);
    return p;
  };
  const plate = new THREE.Shape();
  [[-2.25, 0], [2.25, 0], [2.25, 1.0], [1.98, 1.4], [-0.2, 1.4], [-0.2, 2.86], [-1.05, 2.86], [-1.05, 1.4], [-2.25, 1.4]]
    .forEach(([z, y], i) => (i ? plate.lineTo(z, y) : plate.moveTo(z, y)));
  plate.closePath();
  plate.holes.push(roundRect(-0.92, 0.3, 1.9, 0.8, 0.14), roundRect(-0.9, 1.7, 0.55, 0.95, 0.1), roundRect(1.25, 0.3, 0.62, 0.22, 0.08), roundRect(-1.95, 0.24, 0.7, 0.2, 0.08));
  const plateGeo = new THREE.ExtrudeGeometry(plate, { depth: 0.08, bevelEnabled: true, bevelThickness: 0.012, bevelSize: 0.012, bevelSegments: 2, curveSegments: 10 });
  plateGeo.rotateY(-Math.PI / 2);          // shape x → loom z, thickness → loom x
  plateGeo.translate(0.04, 0, 0);

  const BEAM = { y: 0.72, z: -1.5, r: 0.34 };
  const ROLL = { y: 0.72, z: 1.45 };
  const sidePlate = (x, out) => {
    const g = part(x, 0, 0, out, 0, 0);
    g.add(shadowed(new THREE.Mesh(plateGeo, mFrame)));
    // Bearing bosses where the beams and shafts pass through.
    [[BEAM.y, BEAM.z, 0.13], [ROLL.y, ROLL.z, 0.11], [1.455, Z_BACK, 0.07], [1.455, Z_BREAST, 0.07], [SLEY_PIVOT.y, SLEY_PIVOT.z, 0.08]].forEach(([y, z, r]) => {
      g.add(at(rod(r, 0.13, mSteel, 28), 0, y, z));
    });
    return g;
  };
  const plateL = sidePlate(-2.0, -1.5);
  const plateR = sidePlate(2.0, 1.5);

  // Flywheel on the outside of one frame: it turns once per pick.
  const wheel = new THREE.Group();
  const wheelRim = new THREE.TorusGeometry(0.44, 0.045, 14, 56);
  wheelRim.rotateY(Math.PI / 2);
  wheel.add(shadowed(new THREE.Mesh(wheelRim, mFrame)));
  for (let i = 0; i < 3; i++) { const sp = box(0.03, 0.86, 0.05, mFrame); sp.rotation.x = (i * Math.PI) / 3; wheel.add(sp); }
  wheel.add(rod(0.07, 0.1, mSteel, 24));
  at(wheel, 0.24, 0.62, 0.3);
  plateR.add(wheel);
  plateR.add(at(rod(0.03, 0.24, mSteel), 0.14, 0.62, 0.3));

  /* ---------- The bed: shafts, tie rods and the two rollers the cloth runs over ---------- */
  const bed = part(0, 0, 0, 0, 0, 0);
  bed.add(at(rod(0.04, 4.0, mFrame), 0, 0.16, 1.95), at(rod(0.04, 4.0, mFrame), 0, 0.16, -1.95));
  bed.add(at(rod(0.03, 4.0, mSteel), 0, 0.62, 0.3));                       // main shaft
  bed.add(at(rod(0.035, 4.0, mSteel), 0, SLEY_PIVOT.y, SLEY_PIVOT.z));     // rocking shaft
  bed.add(at(rod(0.045, 4.0, mSteel, 24), 0, WARP_Y - 0.045, Z_BACK));     // back rest
  bed.add(at(rod(0.05, 3.5, mClothRoll, 24), 0, WARP_Y - 0.05, Z_BREAST)); // breast beam, with cloth over it
  bed.add(at(rod(0.03, 4.0, mSteel), 0, WARP_Y - 0.05, Z_BREAST));

  // Finished cloth: from the fell to the breast beam, then down to the roll.
  const sheet = new THREE.PlaneGeometry(HALF * 2, 1);
  const clothTop = new THREE.Mesh(sheet, mClothTop);
  const clothDown = new THREE.Mesh(sheet, mClothDown);
  [clothTop, clothDown].forEach((m) => { m.castShadow = true; m.receiveShadow = true; root.add(m); });
  const placeSheet = (mesh, ay, az, by, bz) => {
    const dy = by - ay, dz = bz - az;
    mesh.position.set(0, (ay + by) / 2, (az + bz) / 2);
    mesh.rotation.x = Math.atan2(dz, dy);
    mesh.scale.y = Math.hypot(dy, dz);
  };
  placeSheet(clothTop, WARP_Y, Z_FELL, WARP_Y, Z_BREAST);

  /* ---------- Top tubes the heddle frames hang from ---------- */
  const top = part(0, 2.74, -0.625, 0, 1.7, -0.35);
  top.add(at(rod(0.045, 4.0, mFrame), 0, 0, -0.125), at(rod(0.045, 4.0, mFrame), 0, 0, 0.125));

  /* ---------- Warp beam ---------- */
  const beam = part(0, BEAM.y, BEAM.z, 0, 0.35, -2.1);
  const barrel = rod(BEAM.r, 3.5, mWound, 48);
  beam.add(barrel);
  beam.add(rod(0.05, 4.02, mSteel));
  [-1.8, 1.8].forEach((x) => beam.add(at(rod(0.5, 0.04, mFrame, 48), x, 0, 0)));

  /* ---------- Heddle frames ---------- */
  const wire = new THREE.CylinderGeometry(1, 1, 1, 4, 1, true);
  const _m = new THREE.Matrix4();
  const _p = new THREE.Vector3();
  const _q = new THREE.Quaternion();
  const _s = new THREE.Vector3();
  const _d = new THREE.Vector3();
  const UP = new THREE.Vector3(0, 1, 0);
  const endX = (e) => -HALF + (2 * HALF * e) / (ENDS - 1);

  const heddle = (index, oy, oz) => {
    const g = part(0, WARP_Y, Z_HEDDLE[index], 0, oy, oz);
    g.add(at(box(3.78, 0.075, 0.04, mFrame), 0, 0.64, 0), at(box(3.78, 0.075, 0.04, mFrame), 0, -0.64, 0));
    g.add(at(box(0.04, 1.3, 0.04, mFrame), -1.87, 0, 0), at(box(0.04, 1.3, 0.04, mFrame), 1.87, 0, 0));
    const wires = new THREE.InstancedMesh(wire, mSteel, ENDS / 2);
    for (let i = 0; i < ENDS / 2; i++) {
      _m.compose(_p.set(endX(i * 2 + index), 0, 0), _q.identity(), _s.set(0.004, 1.22, 0.004));
      wires.setMatrixAt(i, _m);
    }
    wires.castShadow = true;
    g.add(wires);
    return g;
  };
  const heddles = [heddle(0, 0.95, -0.7), heddle(1, 1.5, 0.0)];

  // Cords from the top tubes down to each frame.
  const cords = new THREE.InstancedMesh(new THREE.CylinderGeometry(1, 1, 1, 6), mFrame, 4);
  cords.frustumCulled = false;
  root.add(cords);

  /* ---------- Sley: the swinging frame that carries the reed and the shuttle ---------- */
  const sley = part(0, SLEY_PIVOT.y, SLEY_PIVOT.z, 0, 0.5, 0.95);
  sley.add(at(box(0.06, 1.62, 0.09, mFrame), -1.9, 0.81, 0), at(box(0.06, 1.62, 0.09, mFrame), 1.9, 0.81, 0));
  sley.add(at(box(3.86, 0.07, 0.09, mFrame), 0, 1.6, 0), at(box(3.86, 0.05, 0.07, mFrame), 0, 1.03, 0));
  sley.add(at(box(3.86, 0.035, 0.3, mFrame), 0, 1.17, 0.17));                // the race the shuttle runs along
  const dents = new THREE.InstancedMesh(wire, mSteel, 110);
  for (let i = 0; i < 110; i++) {
    _m.compose(_p.set(-1.8 + (3.6 * i) / 109, 1.315, 0), _q.identity(), _s.set(0.0035, 0.54, 0.0035));
    dents.setMatrixAt(i, _m);
  }
  dents.castShadow = true;
  sley.add(dents);
  sley.rotation.x = SLEY_REST;

  const shuttle = new THREE.Group();
  const hull = new THREE.CapsuleGeometry(0.05, 0.42, 6, 14);
  hull.rotateZ(Math.PI / 2);
  hull.scale(1, 0.85, 1.25);
  shuttle.add(shadowed(new THREE.Mesh(hull, mWood)));
  const pirn = new THREE.CapsuleGeometry(0.032, 0.2, 4, 10);
  pirn.rotateZ(Math.PI / 2);
  shuttle.add(at(shadowed(new THREE.Mesh(pirn, mRed)), 0, 0.03, 0));
  sley.add(shuttle);
  const SHUTTLE = { y: 1.275, z: 0.2, park: 2.12 };

  // The row of weft being laid.
  const pick = rod(0.011, 1, mRed, 8);
  root.add(pick);

  /* ---------- Cloth roll ---------- */
  const roll = part(0, ROLL.y, ROLL.z, 0, -0.04, 1.6);
  const rollCloth = rod(1, 3.44, mClothRoll, 48);
  roll.add(rollCloth);
  roll.add(rod(0.045, 4.02, mSteel));

  /* ---------- Ground ---------- */
  const ground = new THREE.Mesh(new THREE.PlaneGeometry(40, 40), new THREE.ShadowMaterial({ opacity: 0.5 }));
  ground.rotation.x = -Math.PI / 2;
  ground.receiveShadow = true;
  scene.add(ground);
  const glow = new THREE.Mesh(new THREE.PlaneGeometry(13, 13), new THREE.MeshBasicMaterial({ map: floorTexture(), transparent: true, depthWrite: false, opacity: 0 }));
  glow.rotation.x = -Math.PI / 2;
  glow.position.y = 0.005;
  scene.add(glow);

  /* ---------- Threads ---------- */
  const N = ENDS * SEGS;
  const SEG_LEN = (Z_FELL - Z_BACK) / SEGS;
  const strand = new THREE.CylinderGeometry(1, 1, 1, 5, 1, true);
  const fibres = new THREE.InstancedMesh(strand, mThread, N);
  fibres.instanceMatrix.setUsage(THREE.DynamicDrawUsage);
  fibres.frustumCulled = false;
  fibres.castShadow = true;
  root.add(fibres);

  const warp = new THREE.InstancedMesh(strand, mThread, ENDS * 3);
  warp.instanceMatrix.setUsage(THREE.DynamicDrawUsage);
  warp.frustumCulled = false;
  warp.castShadow = true;
  warp.visible = false;
  root.add(warp);

  // Per-fibre constants.
  const rnd = mulberry(20261120);
  const F = {
    ax: new Float32Array(N), ay: new Float32Array(N), az: new Float32Array(N),
    q: new Float32Array(N * 4), spin: new Float32Array(N * 4), phase: new Float32Array(N),
    len: new Float32Array(N), s: new Float32Array(N), ply: new Float32Array(N), rho: new Float32Array(N), beta: new Float32Array(N),
  };
  for (let i = 0; i < N; i++) {
    // A point inside a soft ellipsoid.
    let x, y, z;
    do { x = rnd() * 2 - 1; y = rnd() * 2 - 1; z = rnd() * 2 - 1; } while (x * x + y * y + z * z > 1);
    F.ax[i] = x * 3.3; F.ay[i] = 1.6 + y * 1.35; F.az[i] = z * 1.7;
    _q.set(rnd() - 0.5, rnd() - 0.5, rnd() - 0.5, rnd() - 0.5).normalize();
    F.q.set([_q.x, _q.y, _q.z, _q.w], i * 4);
    _d.set(rnd() - 0.5, rnd() - 0.5, rnd() - 0.5).normalize();
    F.spin.set([_d.x, _d.y, _d.z, 0.15 + rnd() * 0.45], i * 4);
    F.phase[i] = rnd() * TAU;
    F.len[i] = 0.09 + rnd() * rnd() * 0.3;
    F.s[i] = rnd();
    F.ply[i] = i % 3;
    F.rho[i] = Math.sqrt(rnd()) * 0.062;
    F.beta[i] = rnd() * TAU;
  }

  const YARN = { len: 6.6, y: 1.6, r: 0.07, turns: 7 };
  const qa = new THREE.Quaternion();
  const qb = new THREE.Quaternion();
  const qc = new THREE.Quaternion().setFromUnitVectors(UP, new THREE.Vector3(0, 0, 1));
  const qs = new THREE.Quaternion();
  const axis = new THREE.Vector3();

  function drawFibres(time, drawRaw, twist, warpRaw) {
    const w = YARN.turns * TAU * twist;
    const R = YARN.r * (1.75 - 0.75 * twist);
    const tight = 1.5 - 0.5 * twist;
    const turn = time * 0.9 * (1 - warpRaw);
    for (let i = 0; i < N; i++) {
      const s = F.s[i];
      const e = i % ENDS;
      const k = (i / ENDS) | 0;
      const tAB = ease(clamp((drawRaw - s * 0.42) / 0.58));
      const tBC = ease(clamp((warpRaw - (k / SEGS) * 0.4) / 0.6));
      let px, py, pz, len, rad;

      // A: adrift.
      if (tAB < 1) {
        const ph = F.phase[i];
        px = F.ax[i] + Math.sin(time * 0.21 + ph) * 0.16;
        py = F.ay[i] + Math.sin(time * 0.17 + ph * 1.7) * 0.12;
        pz = F.az[i] + Math.cos(time * 0.19 + ph * 0.6) * 0.14;
        axis.set(F.spin[i * 4], F.spin[i * 4 + 1], F.spin[i * 4 + 2]);
        qs.setFromAxisAngle(axis, time * F.spin[i * 4 + 3] + ph);
        qa.set(F.q[i * 4], F.q[i * 4 + 1], F.q[i * 4 + 2], F.q[i * 4 + 3]).premultiply(qs);
        len = F.len[i]; rad = 0.0058;
        _q.copy(qa);
      }
      // B: one of three plies in a twisted strand.
      if (tAB > 0 && tBC < 1) {
        const ang = (F.ply[i] * TAU) / 3 + s * w + turn;
        const a2 = ang * 1.6 + F.beta[i];
        const rho = F.rho[i] * tight;
        const bx = (s - 0.5) * YARN.len;
        const by = YARN.y + Math.cos(ang) * R + Math.cos(a2) * rho;
        const bz = Math.sin(ang) * R + Math.sin(a2) * rho;
        _d.set(YARN.len, -Math.sin(ang) * R * w, Math.cos(ang) * R * w).normalize();
        qb.setFromUnitVectors(UP, _d);
        if (tAB < 1) {
          px = lerp(px, bx, tAB); py = lerp(py, by, tAB); pz = lerp(pz, bz, tAB); rad = lerp(rad, 0.0072, tAB);
          _q.slerpQuaternions(qa, qb, tAB);
        } else {
          px = bx; py = by; pz = bz; len = F.len[i]; rad = 0.0072;
          _q.copy(qb);
        }
      }
      // C: a length of one warp thread.
      if (tBC > 0) {
        const cx = endX(e);
        const cz = Z_BACK + (k + 0.5) * SEG_LEN;
        if (tBC < 1) {
          px = lerp(px, cx, tBC); py = lerp(py, WARP_Y, tBC); pz = lerp(pz, cz, tBC);
          len = lerp(len, SEG_LEN * 1.04, tBC); rad = lerp(rad, 0.0085, tBC);
          qb.copy(_q);
          _q.slerpQuaternions(qb, qc, tBC);
        } else {
          px = cx; py = WARP_Y; pz = cz; len = SEG_LEN * 1.04; rad = 0.0085;
          _q.copy(qc);
        }
      }
      _m.compose(_p.set(px, py, pz), _q, _s.set(rad, len, rad));
      fibres.setMatrixAt(i, _m);
    }
    fibres.instanceMatrix.needsUpdate = true;
  }

  // A thread or cord between two points.
  function segment(mesh, index, ax, ay, az, bx, by, bz, r) {
    _d.set(bx - ax, by - ay, bz - az);
    const len = _d.length() || 1e-4;
    _q.setFromUnitVectors(UP, _d.multiplyScalar(1 / len));
    _m.compose(_p.set((ax + bx) / 2, (ay + by) / 2, (az + bz) / 2), _q, _s.set(r, len, r));
    mesh.setMatrixAt(index, _m);
  }

  function drawWarp(lift) {
    const by = beam.position.y + 0.1;
    const bz = beam.position.z - BEAM.r * 0.96;
    for (let e = 0; e < ENDS; e++) {
      const f = e % 2;
      const x = endX(e);
      const hy = WARP_Y + (f ? -lift : lift);
      segment(warp, e * 3, x, by, bz, x, WARP_Y, Z_BACK, 0.0085);
      segment(warp, e * 3 + 1, x, WARP_Y, Z_BACK, x, hy, Z_HEDDLE[f], 0.0085);
      segment(warp, e * 3 + 2, x, hy, Z_HEDDLE[f], x, WARP_Y, Z_FELL, 0.0085);
    }
    warp.instanceMatrix.needsUpdate = true;
  }

  /* ---------- Labels ---------- */
  const anchors = {
    beam: anchor(beam, 1.1, BEAM.r + 0.02, 0),
    heddles: anchor(heddles[1], 1.0, 0.68, 0),
    reed: anchor(sley, -1.5, 1.64, 0),
    shuttle: anchor(shuttle, 0, 0.06, 0),
    roll: anchor(roll, 0.2, 0.2, 0.1),
  };
  const tags = labels.map((el) => ({ el, anchor: anchors[el.dataset.part] })).filter((t) => t.anchor);
  const _v = new THREE.Vector3();

  /* ---------- Camera path ---------- */
  const keys = [
    [0.00, [0.0, 1.9, 7.4], [0, 1.6, 0]],
    [0.13, [0.4, 1.95, 6.6], [0, 1.6, 0]],
    [0.22, [1.7, 2.15, 3.5], [0.15, 1.6, 0]],
    [0.30, [2.1, 2.05, 3.1], [0.15, 1.6, 0]],
    [0.42, [5.5, 3.7, 6.7], [0, 1.3, 0]],
    [0.47, [5.6, 3.7, 6.6], [0, 1.3, 0]],
    [0.55, [8.2, 4.9, 8.6], [0, 1.9, -0.2]],
    [0.63, [7.5, 5.2, 9.2], [0, 1.9, -0.2]],
    [0.72, [3.7, 2.95, 5.4], [-0.5, 1.4, 0.3]],
    [0.86, [2.6, 2.75, 4.9], [-0.45, 1.45, 0.6]],
    [0.95, [0.5, 2.15, 2.5], [0, 1.5, 1.35]],
    [1.00, [0.3, 1.95, 2.25], [0, 1.5, 1.4]],
  ];
  const camPos = new THREE.Vector3();
  const camTgt = new THREE.Vector3();
  let reach = 1;                       // camera backs off on narrow screens
  let W = 0, H = 0;

  function aim(p, time) {
    let i = 0;
    while (i < keys.length - 2 && p > keys[i + 1][0]) i++;
    const a = keys[i], b = keys[i + 1];
    const t = ease(span(p, a[0], b[0]));
    camPos.set(lerp(a[1][0], b[1][0], t), lerp(a[1][1], b[1][1], t), lerp(a[1][2], b[1][2], t));
    camTgt.set(lerp(a[2][0], b[2][0], t), lerp(a[2][1], b[2][1], t), lerp(a[2][2], b[2][2], t));
    // A slow drift, so the scene is never a still.
    if (!reduceMotion) {
      camPos.x += Math.sin(time * 0.23) * 0.16;
      camPos.y += Math.sin(time * 0.31) * 0.07;
    }
    camPos.sub(camTgt).multiplyScalar(reach).add(camTgt);
    camPos.x *= side; camTgt.x *= side;
    camera.position.copy(camPos);
    camera.lookAt(camTgt);
  }

  function resize() {
    const w = canvas.clientWidth, h = canvas.clientHeight;
    if (!w || !h || (w === W && h === H)) return;
    W = w; H = h;
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, w < 800 ? 1.5 : 1.75));
    renderer.setSize(w, h, false);
    camera.aspect = w / h;
    const wide = w >= 981;
    // Leave room for the text: beside the scene on wide screens, under it on narrow ones.
    if (wide) camera.setViewOffset(w, h, -side * w * 0.19, 0, w, h);
    else camera.setViewOffset(w, h, 0, h * 0.15, w, h);
    const room = wide ? camera.aspect * 0.66 : camera.aspect;
    reach = clamp(1.36 / room, 1, 2.9);
    camera.updateProjectionMatrix();
  }

  /* ---------- Frame ---------- */
  let last = 0;
  let time = 0;
  let cycle = 0;
  let p = 0;
  let target = 0;
  let started = false;

  function frame(now, progress) {
    const dt = Math.min((now - last) / 1000 || 0.016, 0.05);
    last = now;
    if (!reduceMotion) time += dt;
    target = progress;
    p = started ? p + (target - p) * (1 - Math.exp(-dt * 9)) : target;
    started = true;
    resize();

    const drawRaw = span(p, 0.13, 0.22);
    const twist = ease(span(p, 0.15, 0.30));
    const warpRaw = span(p, 0.30, 0.40);
    const loomIn = ease(span(p, 0.33, 0.42));
    const apart = ease(span(p, 0.47, 0.54)) * (1 - ease(span(p, 0.63, 0.70)));
    const run = ease(span(p, 0.42, 0.44)) * (1 - ease(span(p, 0.455, 0.475))) + ease(span(p, 0.69, 0.73));
    const speed = lerp(1.0, 2.3, span(p, 0.70, 0.80));

    if (reduceMotion) cycle = p * 70; else cycle += dt * speed * run;
    const n = Math.floor(cycle);
    const ph = cycle - n;
    const sgn = n % 2 ? 1 : -1;
    const shed = lerp(-sgn, sgn, ease(span(ph, 0, 0.22)));
    const fly = ease(span(ph, 0.24, 0.62));
    const beatIn = ease(span(ph, 0.64, 0.82));
    const beat = ph < 0.82 ? beatIn : 1 - ease(span(ph, 0.82, 1));
    const lift = shed * LIFT * run;

    // Threads: loose fibres until the warp is laid, then real warp ends that the heddles can lift.
    const laid = warpRaw >= 1;
    fibres.visible = !laid;
    warp.visible = laid;
    if (!laid) drawFibres(time, drawRaw, twist, warpRaw);

    // The loom: fades in around the warp, then leaves and returns along each part's own direction.
    const there = loomIn > 0.001;
    const away = apart + (1 - loomIn) * 1.4;
    parts.forEach((g) => {
      g.visible = there;
      g.position.copy(g.userData.out).multiplyScalar(away).add(g.userData.home);
    });
    [clothTop, clothDown, cords, pick].forEach((m) => { m.visible = there; });
    loomMats.forEach((m) => {
      const solid = loomIn >= 1;
      if (m.transparent === solid) { m.transparent = !solid; m.needsUpdate = true; }
      m.opacity = loomIn;
    });
    glow.material.opacity = loomIn;

    if (there) {
      heddles[0].position.y += lift;
      heddles[1].position.y -= lift;
      sley.rotation.x = lerp(SLEY_REST, SLEY_BEAT, beat * run);

      const sx = lerp(SHUTTLE.park, lerp(-sgn, sgn, fly) * SHUTTLE.park, run);
      shuttle.position.set(lerp(sx, 0.7, apart), SHUTTLE.y + apart * 0.95, SHUTTLE.z + apart * 0.5);
      shuttle.rotation.x = -sley.rotation.x;                    // stays level while the sley swings

      // The weft follows the shuttle across, then the reed carries it to the edge of the cloth.
      const from = -sgn * HALF;
      const to = clamp(lerp(-sgn, sgn, fly) * SHUTTLE.park, -HALF, HALF);
      const wide = Math.abs(to - from) * run * (1 - apart);
      pick.visible = wide > 0.01;
      pick.scale.x = Math.max(wide, 0.001);
      pick.position.set((from + to) / 2, WARP_Y, lerp(0.3, Z_FELL - 0.012, ph < 0.64 ? 0 : ph < 0.82 ? beatIn : 1));

      for (let i = 0; i < 4; i++) {
        const h = heddles[i >> 1];
        const x = i % 2 ? 1.25 : -1.25;
        segment(cords, i, x, top.position.y, top.position.z + (i >> 1 ? 0.125 : -0.125), x, h.position.y + 0.66, h.position.z, 0.008);
      }
      cords.instanceMatrix.needsUpdate = true;

      // Cloth winds on: the roll grows through the weaving chapter.
      const r = lerp(0.2, 0.33, span(p, 0.70, 0.9));
      rollCloth.scale.set(1, r, r);
      placeSheet(clothDown, WARP_Y - 0.05, Z_BREAST + 0.05, roll.position.y + 0.04, roll.position.z + r);
      const turnBy = cycle * TAU;
      wheel.rotation.x = turnBy;
      barrel.rotation.x = -cycle * 0.05;
      rollCloth.rotation.x = cycle * 0.07;
      clothMaps.forEach((map) => { map.offset.y = -cycle * 0.045; });

      if (laid) drawWarp(lift);
    }

    aim(p, time);
    const dist = camera.position.distanceTo(camTgt);
    scene.fog.near = dist - lerp(1.4, 1.0, loomIn);
    scene.fog.far = dist + lerp(2.6, 13, loomIn);
    renderer.render(scene, camera);

    // Part names sit on their parts while the loom is apart.
    if (tags.length) {
      const show = apart > 0.02;
      tags.forEach((t) => {
        if (!show) { t.el.style.opacity = '0'; return; }
        t.anchor.getWorldPosition(_v).project(camera);
        const x = (_v.x * 0.5 + 0.5) * W;
        const y = (-_v.y * 0.5 + 0.5) * H;
        t.el.style.transform = `translate3d(${x.toFixed(1)}px, ${y.toFixed(1)}px, 0)`;
        t.el.style.opacity = ease(span(apart, 0.55, 1)).toFixed(3);
        t.el.classList.toggle('is-flip', rtl ? x < 170 : x > W - 170);
      });
    }
  }

  return { frame, resize };
}
