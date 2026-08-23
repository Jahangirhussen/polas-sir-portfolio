function mediaCardHTML(item) {
  const featuredClass = item.featured ? "featured" : "";
  return `
  <div class="glass media-card ${featuredClass} reveal" data-type="${item.type.toLowerCase()}">
    <div class="media-image">
      ${item.featured ? "FEATURED" : item.type}
      <span class="glass media-arrow">↗</span>
    </div>
    <div class="media-body">
      <div class="media-meta"><span>${item.date}</span><span>${item.publication}</span></div>
      <div class="media-headline">${item.headline}</div>
      <div class="media-desc">${item.description}</div>
      <a href="${item.url}" class="media-cta" target="_blank" rel="noopener">Read Article →</a>
    </div>
  </div>`;
}

async function loadMedia(containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;
  const res = await fetch("data/media.json");
  const items = await res.json();
  container.innerHTML = items.map(mediaCardHTML).join("");

  const observer = new IntersectionObserver(
    (entries) => entries.forEach((e) => { if (e.isIntersecting) { e.target.classList.add("is-visible"); observer.unobserve(e.target); } }),
    { threshold: 0.1 }
  );
  container.querySelectorAll(".reveal").forEach((el) => observer.observe(el));

  const filterBar = document.getElementById("mediaFilter");
  if (filterBar) {
    const cards = container.querySelectorAll("[data-type]");
    filterBar.addEventListener("click", (e) => {
      const tag = e.target.closest("[data-filter]");
      if (!tag) return;
      filterBar.querySelectorAll(".tag").forEach((t) => t.classList.remove("active"));
      tag.classList.add("active");
      const type = tag.dataset.filter;
      cards.forEach((card) => {
        card.style.display = type === "all" || card.dataset.type === type ? "" : "none";
      });
    });
  }
}
