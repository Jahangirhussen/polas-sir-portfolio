// Generic loader: fetches items for one dashboard section and renders them
// into a container using the page-supplied template function.
// Falls back to a static data/<section>.json file if the PHP API isn't reachable
// (useful for local static preview before the site is on PHP hosting).

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
    if (options.emptyHTML) container.innerHTML = options.emptyHTML;
    return;
  }

  container.innerHTML = items.map(templateFn).join("");

  const observer = new IntersectionObserver(
    (entries) => entries.forEach((e) => { if (e.isIntersecting) { e.target.classList.add("is-visible"); observer.unobserve(e.target); } }),
    { threshold: 0.1 }
  );
  container.querySelectorAll(".reveal").forEach((el) => observer.observe(el));

  if (window.applyIcons) window.applyIcons();
}
