// Generic loader: fetches items for one dashboard section and renders them
// into a container using the page-supplied template function.
// Falls back to a static data/<section>.json file if the PHP API isn't reachable
// (useful for local static preview before the site is on PHP hosting).
//
// When the visitor is logged into the admin dashboard, this also appends a
// WordPress-style "+ Add New" box in the same grid/list, matching the
// section's own card design, plus a small delete control on every item —
// so new content can be entered right on the live page instead of a
// separate admin form screen.

let _isAdminPromise = null;
function isAdmin() {
  if (!_isAdminPromise) {
    _isAdminPromise = fetch("api/whoami.php")
      .then((r) => (r.ok ? r.json() : { admin: false }))
      .then((d) => d.admin)
      .catch(() => false);
  }
  return _isAdminPromise;
}

async function loadSection(section, containerId, templateFn, options = {}) {
  const container = document.getElementById(containerId);
  if (!container) return;

  let items = [];
  try {
    const res = await fetch(`api/data.php?section=${section}`);
    if (!res.ok) throw new Error("api unavailable");
    items = await res.json();
  } catch (e) {
    try {
      const fallback = await fetch(`data/${section}.json`);
      items = await fallback.json();
    } catch (e2) {
      items = [];
    }
  }

  if (!items.length) {
    container.innerHTML = options.emptyHTML || "";
  } else {
    container.innerHTML = items.map(templateFn).join("");
  }

  const observer = new IntersectionObserver(
    (entries) => entries.forEach((e) => { if (e.isIntersecting) { e.target.classList.add("is-visible"); observer.unobserve(e.target); } }),
    { threshold: 0.1 }
  );
  container.querySelectorAll(".reveal").forEach((el) => observer.observe(el));

  if (window.applyIcons) window.applyIcons();

  await enableInlineAdmin(section, containerId, () => loadSection(section, containerId, templateFn, options));
}

// Shared by loadSection and any page with its own custom loader (e.g.
// publications/media use richer filtering, so they render themselves and
// just call this afterwards to get the same delete controls + Add New box).
async function enableInlineAdmin(section, containerId, reload) {
  const container = document.getElementById(containerId);
  if (!container) return;

  const admin = await isAdmin();
  if (!admin) return;

  container.querySelectorAll("[data-item-id]").forEach((el) => {
    if (el.querySelector(".admin-delete-btn")) return;
    el.style.position = el.style.position || "relative";
    const del = document.createElement("button");
    del.type = "button";
    del.className = "admin-delete-btn";
    del.title = "Delete this item";
    del.textContent = "✕";
    del.addEventListener("click", async (e) => {
      e.preventDefault();
      e.stopPropagation();
      if (!confirm("Delete this item?")) return;
      await fetch("api/delete_item.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ section, id: el.dataset.itemId }),
      });
      reload();
    });
    el.appendChild(del);
  });

  appendAddNewTile(section, containerId, reload);
}

async function appendAddNewTile(section, containerId, reload) {
  const container = document.getElementById(containerId);
  if (!container || container.querySelector(".admin-add-tile")) return;

  const fieldsRes = await fetch(`api/schema.php?section=${section}`);
  if (!fieldsRes.ok) return;
  const fields = await fieldsRes.json();

  const tile = document.createElement("div");
  tile.className = "glass admin-add-tile";
  tile.innerHTML = `<span class="admin-add-plus">+</span><span>Add New</span>`;

  tile.addEventListener("click", () => {
    tile.classList.add("open");
    tile.innerHTML = buildInlineForm(fields);
    wireImageFields(tile);
    tile.querySelector("form").addEventListener("click", (e) => e.stopPropagation());
    tile.querySelector("form").addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const data = {};
      Object.keys(fields).forEach((key) => {
        data[key] = fields[key].type === "checkbox" ? formData.has(key) : (formData.get(key) || "");
      });
      const res = await fetch("api/save_item.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ section, data }),
      });
      const result = await res.json();
      if (!res.ok) {
        alert(result.error || "Could not save.");
        return;
      }
      reload();
    });
    tile.querySelector(".admin-cancel-btn").addEventListener("click", reload);
  });

  container.appendChild(tile);
}

function buildInlineForm(fields) {
  const rows = Object.entries(fields).map(([key, field]) => {
    if (field.type === "checkbox") {
      return `<label class="checkbox-row"><input type="checkbox" name="${key}"> ${field.label}</label>`;
    }
    if (field.type === "select") {
      const opts = field.options.map((o) => `<option value="${o}">${o}</option>`).join("");
      return `<label class="form-label">${field.label}</label><select name="${key}" class="form-field">${opts}</select>`;
    }
    if (field.type === "textarea") {
      return `<label class="form-label">${field.label}</label><textarea name="${key}" class="form-field" rows="3"></textarea>`;
    }
    if (field.type === "image") {
      return `<label class="form-label">${field.label}</label>
        <div class="image-upload-zone" data-target="${key}">
          <div class="image-upload-preview" hidden></div>
          <div class="image-upload-prompt">
            <span>Drag & drop a photo, or click to browse</span>
            <input type="file" accept="image/png,image/jpeg,image/webp,image/gif" class="image-upload-input" hidden>
          </div>
        </div>
        <input type="text" name="${key}" class="form-field image-url-field" placeholder="or paste an image URL">`;
    }
    return `<label class="form-label">${field.label}${field.required ? " *" : ""}</label><input type="${field.type === "number" ? "number" : "text"}" name="${key}" class="form-field">`;
  }).join("");

  return `<form class="admin-inline-form">${rows}
    <div style="display:flex; gap:8px; margin-top:8px;">
      <button type="submit" class="btn btn-primary" style="padding:9px 18px; font-size:13px;">Save</button>
      <button type="button" class="btn btn-ghost admin-cancel-btn" style="padding:9px 18px; font-size:13px;">Cancel</button>
    </div>
  </form>`;
}

function wireImageFields(root) {
  root.querySelectorAll(".image-upload-zone").forEach((zone) => {
    const targetName = zone.dataset.target;
    const urlField = root.querySelector(`.image-url-field[name="${targetName}"]`);
    const fileInput = zone.querySelector(".image-upload-input");
    const preview = zone.querySelector(".image-upload-preview");
    const prompt = zone.querySelector(".image-upload-prompt");

    function showPreview(url) {
      preview.style.backgroundImage = `url('${url}')`;
      preview.hidden = false;
      prompt.hidden = true;
      urlField.value = url;
    }

    async function upload(file) {
      const formData = new FormData();
      formData.append("image", file);
      zone.classList.add("uploading");
      try {
        const res = await fetch("api/upload_image.php", { method: "POST", body: formData });
        const result = await res.json();
        if (!res.ok) { alert(result.error || "Upload failed"); return; }
        showPreview(result.url);
      } finally {
        zone.classList.remove("uploading");
      }
    }

    zone.addEventListener("click", (e) => {
      if (e.target === urlField || e.target === fileInput) return;
      fileInput.click();
    });
    fileInput.addEventListener("change", () => { if (fileInput.files[0]) upload(fileInput.files[0]); });

    ["dragenter", "dragover"].forEach((evt) => zone.addEventListener(evt, (e) => { e.preventDefault(); zone.classList.add("drag-over"); }));
    ["dragleave", "drop"].forEach((evt) => zone.addEventListener(evt, (e) => { e.preventDefault(); zone.classList.remove("drag-over"); }));
    zone.addEventListener("drop", (e) => {
      const file = e.dataTransfer.files[0];
      if (file) upload(file);
    });

    urlField.addEventListener("input", () => {
      if (urlField.value) showPreview(urlField.value);
    });
  });
}
