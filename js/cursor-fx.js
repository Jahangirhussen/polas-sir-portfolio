(function () {
  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  const isTouch = window.matchMedia("(pointer: coarse)").matches;
  if (reduceMotion || isTouch) return;

  const canvas = document.createElement("canvas");
  canvas.id = "cursorFx";
  canvas.style.cssText = "position:fixed;inset:0;width:100vw;height:100vh;pointer-events:none;z-index:9999;";
  document.body.appendChild(canvas);
  const ctx = canvas.getContext("2d");

  function resize() {
    canvas.width = window.innerWidth * devicePixelRatio;
    canvas.height = window.innerHeight * devicePixelRatio;
    ctx.setTransform(devicePixelRatio, 0, 0, devicePixelRatio, 0, 0);
  }
  resize();
  window.addEventListener("resize", resize);

  let mouseX = -100, mouseY = -100, lastX = -100, lastY = -100;
  let hasMoved = false;
  const sparks = [];

  window.addEventListener("mousemove", (e) => {
    lastX = mouseX; lastY = mouseY;
    mouseX = e.clientX; mouseY = e.clientY;
    if (!hasMoved) { lastX = mouseX; lastY = mouseY; hasMoved = true; }

    const dist = Math.hypot(mouseX - lastX, mouseY - lastY);
    const count = Math.min(3, Math.max(1, Math.floor(dist / 6)));

    for (let i = 0; i < count; i++) {
      const t = i / count;
      const x = lastX + (mouseX - lastX) * t;
      const y = lastY + (mouseY - lastY) * t;
      sparks.push({
        x: x + (Math.random() - 0.5) * 6,
        y: y + (Math.random() - 0.5) * 6,
        vx: (Math.random() - 0.5) * 1.2,
        vy: (Math.random() - 0.5) * 1.2,
        life: 1,
        size: Math.random() * 1.6 + 0.6,
      });
    }
    if (sparks.length > 140) sparks.splice(0, sparks.length - 140);
  }, { passive: true });

  const accent = getComputedStyle(document.documentElement).getPropertyValue("--accent-primary").trim() || "#7c6cf0";

  function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    for (let i = sparks.length - 1; i >= 0; i--) {
      const s = sparks[i];
      s.x += s.vx;
      s.y += s.vy;
      s.life -= 0.035;
      if (s.life <= 0) { sparks.splice(i, 1); continue; }

      ctx.save();
      ctx.globalAlpha = s.life * 0.7;
      ctx.shadowColor = accent;
      ctx.shadowBlur = 8;
      ctx.fillStyle = accent;
      ctx.beginPath();
      ctx.arc(s.x, s.y, s.size * s.life, 0, Math.PI * 2);
      ctx.fill();
      ctx.restore();
    }

    // connecting jagged bolt between nearby sparks for electric feel
    ctx.save();
    ctx.strokeStyle = accent;
    ctx.lineWidth = 0.6;
    for (let i = 1; i < sparks.length; i++) {
      const a = sparks[i - 1], b = sparks[i];
      const d = Math.hypot(a.x - b.x, a.y - b.y);
      if (d < 26) {
        ctx.globalAlpha = Math.min(a.life, b.life) * 0.35;
        ctx.beginPath();
        ctx.moveTo(a.x, a.y);
        ctx.lineTo(b.x, b.y);
        ctx.stroke();
      }
    }
    ctx.restore();

    requestAnimationFrame(draw);
  }
  requestAnimationFrame(draw);
})();
