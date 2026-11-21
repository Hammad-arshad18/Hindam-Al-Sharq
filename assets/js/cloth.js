/* Hindam Al-Sharq — the cloth.
   A piece of fabric drawn live on the GPU: folds are computed per pixel, then lit like satin.
   It changes colour, sheen and weave per "scene", leans toward the pointer and is drawn along by scrolling.
   Plain WebGL, no libraries. */
(() => {
  'use strict';

  const VERT = 'attribute vec2 p; void main(){ gl_Position = vec4(p, 0., 1.); }';

  const FRAG = `
#extension GL_OES_standard_derivatives : enable
precision highp float;

uniform vec2  uRes;
uniform float uDpr, uTime, uShift, uForce;
uniform vec2  uMouse;
uniform vec3  uA, uB, uC;          // cloth colour, shadow colour, highlight colour
uniform float uSheen, uWeave, uTwill, uBump;

float hash(vec2 p){ return fract(sin(dot(p, vec2(12.9898, 78.233))) * 43758.5453); }
float noise(vec2 p){
  vec2 i = floor(p), f = fract(p);
  vec2 q = f * f * f * (f * (f * 6. - 15.) + 10.);              // quintic, so lighting stays smooth across cells
  return mix(mix(hash(i), hash(i + vec2(1., 0.)), q.x), mix(hash(i + vec2(0., 1.)), hash(i + vec2(1., 1.)), q.x), q.y);
}

void main(){
  float s  = min(uRes.x, uRes.y);
  vec2  uv = (gl_FragCoord.xy - .5 * uRes) / s;

  // The pointer gathers the cloth toward itself.
  vec2  d = uv - (uMouse - .5 * uRes) / s;
  float g = exp(-dot(d, d) * 5.);
  vec2  p = uv * 1.25 + d * g * .55 * uForce;
  p += vec2(.25, 1.) * uShift;
  p = mat2(.82, -.57, .57, .82) * p;                            // folds fall on a diagonal

  float t = uTime;
  float u = p.x;                                                // along the folds
  float v = p.y;                                                // across them

  // Slow fields that make the folds wander, merge, swell and die away, as hanging cloth does.
  float n1 = noise(vec2(u * .55, v * .45) + t * .020);
  float n2 = noise(vec2(u * .95 + 7.3, v * .80) - t * .026);
  float n3 = noise(vec2(u * .70 + 3.1, v * 1.25 + 9.7) + t * .017);
  float n4 = noise(vec2(u * 1.60 - 5.2, v * 2.10 + 1.4));

  float w = v + (n1 - .5) * 1.5 + (n2 - .5) * .45;
  float h = .56 * sin(w * 3.9 + n2 * 4.6 + t * .10)
          + .30 * sin(w * 8.3 - n1 * 7.0 + u * .55 - t * .07) * smoothstep(.10, .75, n2)
          + .15 * sin(w * 16.5 + n3 * 9.0 + u * 1.2) * smoothstep(.25, .80, n3)
          + .055 * sin(w * 33. + n1 * 21. - u * 2.3) * smoothstep(.35, .90, n4);
  h += (n3 - .5) * .22;                                          // broad swell under the folds

  // Surface normal from the slope of the folds.
  vec2 slope = vec2(dFdx(h), dFdy(h)) * s;
  vec3 n = normalize(vec3(-slope * uBump, 1.));

  // Key light from the upper left, soft fill from the lower right.
  vec3 V  = vec3(0., 0., 1.);
  vec3 L  = normalize(vec3(-.55, .6, .58));
  vec3 H  = normalize(L + V);
  float nl    = dot(n, L);
  float dif   = smoothstep(-.55, .95, nl);
  float nh    = max(dot(n, H), 0.);
  // Satin is brushed along its threads: fine streaks break the highlight up so it never looks like plastic.
  float streak = .62 + .76 * noise(vec2(u * 3., v * 150.));
  float tight = pow(nh, 34.) * streak;
  float broad = pow(nh, 7.) * mix(1., streak, .5);
  float fill  = pow(max(dot(n, normalize(vec3(.8, -.4, .45))), 0.), 4.);
  float rim   = pow(1. - n.z, 1.6);

  vec3 col = mix(uB, uA, dif * dif);
  col *= mix(.38, 1.1, smoothstep(-.9, 1., h));                 // valleys sit in shadow
  col += uC * (tight * .78 + broad * .40) * uSheen;             // satin highlight
  col += mix(uA, uC, .25) * fill * .30;
  col += mix(uA, uC, .5) * rim * .16 * (.3 + uSheen);

  // Thread structure, in screen space so it stays crisp: plain weave or diagonal twill.
  vec2 f = gl_FragCoord.xy / uDpr;
  float plain = sin(f.x * 1.57) * sin(f.y * 1.57);
  float twill = sin((f.x + f.y) * 1.05) * (.75 + .25 * sin((f.x - f.y) * 2.1));
  col *= 1. + mix(plain, twill, uTwill) * uWeave;

  col = 1. - exp(-col * 1.75);
  col = mix(vec3(dot(col, vec3(.299, .587, .114))), col, 1.18);   // keep the dye rich after the roll-off                                   // soft shoulder: highlights roll off instead of clipping
  col *= 1. - .30 * dot(uv, uv);                                 // vignette
  col += (hash(gl_FragCoord.xy + fract(t)) - .5) / 110.;         // film grain, also hides banding
  gl_FragColor = vec4(col, 1.);
}`;

  const hex = (h) => { const n = parseInt(h.slice(1), 16); return [(n >> 16) / 255, ((n >> 8) & 255) / 255, (n & 255) / 255]; };
  const mix3 = (a, b, k) => [a[0] + (b[0] - a[0]) * k, a[1] + (b[1] - a[1]) * k, a[2] + (b[2] - a[2]) * k];

  const scene = (a, b, c, sheen, weave, twill, bump) => ({ a: hex(a), b: hex(b), c: hex(c), sheen, weave, twill, bump });
  const SCENES = {
    silk:   scene('#F23000', '#2A0400', '#FF6218', 1.00, .020, 0, .27),
    cotton: scene('#EADCC0', '#8A7658', '#FFFFFF', .14,  .12,  0, .24),
    navy:   scene('#213A80', '#040816', '#93ADF7', .42,  .14,  1, .28),
    pearl:  scene('#F6EFE3', '#877D6A', '#FFFFFF', .80,  .02,  0, .32),
    drill:  scene('#B8905A', '#2E2010', '#F3DDB4', .16,  .15,  1, .27),
    scrub:  scene('#168C8A', '#031E20', '#9FEFE6', .30,  .10,  0, .26),
    slate:  scene('#5E6875', '#12161C', '#D3DCE8', .26,  .13,  1, .27),
    olive:  scene('#5F6B3A', '#141A0A', '#C9D19A', .14,  .15,  0, .27),
    custom: scene('#E0A21F', '#3A2606', '#FFE9B0', .70,  .06, .5, .30),
  };
  // "Made to order" never settles on one colour.
  const CUSTOM = ['#E0A21F', '#C2562A', '#D1495B', '#7A2A66', '#1F55B5'].map(hex);

  function init(canvas) {
    const gl = canvas.getContext('webgl', { antialias: false, alpha: false, powerPreference: 'high-performance' });
    if (!gl || !gl.getExtension('OES_standard_derivatives')) return null;

    const compile = (type, src) => {
      const sh = gl.createShader(type);
      gl.shaderSource(sh, src);
      gl.compileShader(sh);
      if (!gl.getShaderParameter(sh, gl.COMPILE_STATUS)) throw new Error(gl.getShaderInfoLog(sh));
      return sh;
    };
    let prog;
    try {
      prog = gl.createProgram();
      gl.attachShader(prog, compile(gl.VERTEX_SHADER, VERT));
      gl.attachShader(prog, compile(gl.FRAGMENT_SHADER, FRAG));
      gl.linkProgram(prog);
      if (!gl.getProgramParameter(prog, gl.LINK_STATUS)) throw new Error(gl.getProgramInfoLog(prog));
    } catch (err) {
      console.warn('Cloth disabled:', err.message);
      return null;
    }
    gl.useProgram(prog);

    gl.bindBuffer(gl.ARRAY_BUFFER, gl.createBuffer());
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 3, -1, -1, 3]), gl.STATIC_DRAW);
    const loc = gl.getAttribLocation(prog, 'p');
    gl.enableVertexAttribArray(loc);
    gl.vertexAttribPointer(loc, 2, gl.FLOAT, false, 0, 0);

    const U = {};
    ['uRes', 'uDpr', 'uTime', 'uShift', 'uForce', 'uMouse', 'uA', 'uB', 'uC', 'uSheen', 'uWeave', 'uTwill', 'uBump']
      .forEach((name) => { U[name] = gl.getUniformLocation(prog, name); });

    let dpr = 1;
    let target = 'silk';
    const cur = JSON.parse(JSON.stringify(SCENES.silk));
    const mouse = { x: 0, y: 0, tx: 0, ty: 0, force: 0 };
    let shift = 0;
    let shiftTarget = 0;
    let last = 0;
    let clock = 0;

    function resize() {
      // Fabric is soft: it does not need full retina resolution, and phones thank us for it.
      dpr = Math.min(window.devicePixelRatio || 1, innerWidth < 800 ? 1.25 : 1.5);
      const w = Math.round(innerWidth * dpr);
      const h = Math.round(innerHeight * dpr);
      if (canvas.width !== w || canvas.height !== h) {
        canvas.width = w;
        canvas.height = h;
        gl.viewport(0, 0, w, h);
      }
      if (!mouse.tx) { mouse.tx = mouse.x = w * .68; mouse.ty = mouse.y = h * .6; }
    }

    function frame(now) {
      const dt = Math.min((now - last) / 1000 || 0.016, 0.05);
      last = now;
      clock += dt;
      const k = 1 - Math.exp(-dt * 3.2);

      const goal = SCENES[target];
      let a = goal.a, b = goal.b, c = goal.c;
      if (target === 'custom') {
        const pos = (clock * .32) % CUSTOM.length;
        const i = Math.floor(pos);
        const e = pos - i;
        a = mix3(CUSTOM[i], CUSTOM[(i + 1) % CUSTOM.length], e * e * (3 - 2 * e));
        b = mix3(a, [0, 0, 0], .8);
        c = mix3(a, [1, 1, 1], .68);
      }
      cur.a = mix3(cur.a, a, k);
      cur.b = mix3(cur.b, b, k);
      cur.c = mix3(cur.c, c, k);
      ['sheen', 'weave', 'twill', 'bump'].forEach((key) => { cur[key] += (goal[key] - cur[key]) * k; });

      mouse.x += (mouse.tx - mouse.x) * (1 - Math.exp(-dt * 5));
      mouse.y += (mouse.ty - mouse.y) * (1 - Math.exp(-dt * 5));
      mouse.force += ((mouse.active ? 1 : .35) - mouse.force) * (1 - Math.exp(-dt * 2));
      shift += (shiftTarget - shift) * (1 - Math.exp(-dt * 6));

      gl.uniform2f(U.uRes, canvas.width, canvas.height);
      gl.uniform1f(U.uDpr, dpr);
      gl.uniform1f(U.uTime, clock);
      gl.uniform1f(U.uShift, shift);
      gl.uniform1f(U.uForce, mouse.force);
      gl.uniform2f(U.uMouse, mouse.x, mouse.y);
      gl.uniform3fv(U.uA, cur.a);
      gl.uniform3fv(U.uB, cur.b);
      gl.uniform3fv(U.uC, cur.c);
      gl.uniform1f(U.uSheen, cur.sheen);
      gl.uniform1f(U.uWeave, cur.weave);
      gl.uniform1f(U.uTwill, cur.twill);
      gl.uniform1f(U.uBump, cur.bump);
      gl.drawArrays(gl.TRIANGLES, 0, 3);
    }

    resize();
    addEventListener('resize', resize);
    addEventListener('pointermove', (ev) => {
      mouse.tx = ev.clientX * dpr;
      mouse.ty = (innerHeight - ev.clientY) * dpr;
      mouse.active = true;
    }, { passive: true });
    document.addEventListener('pointerleave', () => { mouse.active = false; });

    return {
      frame,
      resize,
      setScene(name) { if (SCENES[name]) target = name; },
      setScroll(y) { shiftTarget = y * 0.00055; },
      colour() { return cur.a; },
    };
  }

  window.Cloth = { init };
})();
