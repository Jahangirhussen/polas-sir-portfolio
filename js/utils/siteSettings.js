// Fetches sitewide settings once and applies them to every element on the
// page tagged with data-bind (text content) or data-bind-href (link target).
// Falls back silently to whatever static text is already in the HTML if the
// API isn't reachable or a field is empty — so the site never looks broken.

(async function () {
  let settings = {};
  try {
    const res = await fetch("api/settings.php");
    if (res.ok) settings = await res.json();
  } catch (e) {
    return; // keep static fallback text already in the HTML
  }

  document.querySelectorAll("[data-bind]").forEach((el) => {
    const key = el.getAttribute("data-bind");
    if (settings[key]) el.textContent = settings[key];
  });

  document.querySelectorAll("[data-bind-href]").forEach((el) => {
    const key = el.getAttribute("data-bind-href");
    if (settings[key]) el.setAttribute("href", settings[key]);
  });

  document.querySelectorAll("[data-bind-mailto]").forEach((el) => {
    const key = el.getAttribute("data-bind-mailto");
    if (settings[key]) el.setAttribute("href", `mailto:${settings[key]}`);
  });
})();
