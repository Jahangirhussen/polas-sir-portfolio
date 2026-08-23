function publicationCardHTML(pub) {
  const doiLink = pub.doi ? `https://doi.org/${pub.doi}` : "https://scholar.google.com/citations?user=yGs2dsMAAAAJ&hl=en";
  const year = pub.year || "—";
  const type = pub.type || "journal";
  const typeLabel = type === "book" ? "Book Chapter" : type.charAt(0).toUpperCase() + type.slice(1);

  return `
  <div class="glass pub-card reveal" data-type="${type}">
    <div class="pub-year">
      <div class="card-icon-left" data-icon="file-text"></div>
      <div class="num">${year}</div>
      <span class="type-badge">${typeLabel}</span>
    </div>
    <div class="pub-body">
      <div class="pub-title">${pub.title}</div>
      <div class="pub-authors">${pub.authors || ""}</div>
      <div class="pub-venue">${pub.venue || ""}</div>
    </div>
    <div class="pub-actions">
      <span class="pub-citations">${pub.citations || 0} citations</span>
      <a href="${doiLink}" class="btn btn-ghost" target="_blank" rel="noopener">View Paper</a>
    </div>
  </div>`;
}

async function loadPublications(containerId, options = {}) {
  const container = document.getElementById(containerId);
  if (!container) return;

  const res = await fetch("data/publications.json");
  const data = await res.json();
  let items = data.publications || [];

  if (options.sortByYear) {
    items = [...items].sort((a, b) => (b.year || 0) - (a.year || 0));
  }
  if (options.limit) {
    items = items.slice(0, options.limit);
  }

  container.innerHTML = items.map(publicationCardHTML).join("");

  const revealEls = container.querySelectorAll(".reveal");
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.1 }
  );
  revealEls.forEach((el) => observer.observe(el));

  const filterBar = document.getElementById("pubFilter");
  const cards = container.querySelectorAll("[data-type]");
  let activeType = "all";

  function applyFilters() {
    const query = (document.getElementById("pubSearch")?.value || "").toLowerCase();
    cards.forEach((card) => {
      const matchesType = activeType === "all" || card.dataset.type === activeType;
      const matchesQuery = !query || card.textContent.toLowerCase().includes(query);
      card.style.display = matchesType && matchesQuery ? "" : "none";
    });
  }

  if (filterBar) {
    filterBar.addEventListener("click", (e) => {
      const tag = e.target.closest("[data-filter]");
      if (!tag) return;
      filterBar.querySelectorAll(".tag").forEach((t) => t.classList.remove("active"));
      tag.classList.add("active");
      activeType = tag.dataset.filter;
      applyFilters();
    });
  }

  const searchInput = document.getElementById("pubSearch");
  if (searchInput) {
    searchInput.addEventListener("input", applyFilters);
  }
}
