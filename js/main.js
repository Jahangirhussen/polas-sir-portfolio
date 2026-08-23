// Theme toggle
const themeToggle = document.getElementById("themeToggle");
const root = document.documentElement;

function applyTheme(theme) {
  root.setAttribute("data-theme", theme);
  localStorage.setItem("theme", theme);
  if (themeToggle && typeof icon === "function") {
    themeToggle.innerHTML = icon(theme === "dark" ? "sun" : "moon", 18);
  }
}

const savedTheme = localStorage.getItem("theme") || "light";
applyTheme(savedTheme);

if (themeToggle) {
  themeToggle.addEventListener("click", () => {
    const next = root.getAttribute("data-theme") === "dark" ? "light" : "dark";
    applyTheme(next);
  });
}

// Mobile nav toggle
const navToggle = document.getElementById("navToggle");
const navMenu = document.getElementById("navMenu");
if (navToggle && navMenu) {
  navToggle.addEventListener("click", () => navMenu.classList.toggle("open"));
}

// Dropdown tap-toggle for touch devices
document.querySelectorAll(".nav-dropdown > span").forEach((label) => {
  label.addEventListener("click", (e) => {
    if (window.innerWidth <= 860) {
      e.preventDefault();
      label.closest(".nav-dropdown").classList.toggle("open");
    }
  });
});

// Scroll reveal
const revealEls = document.querySelectorAll(".reveal");
const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry, idx) => {
      if (entry.isIntersecting) {
        entry.target.style.setProperty("--i", idx);
        entry.target.classList.add("is-visible");
        observer.unobserve(entry.target);
      }
    });
  },
  { threshold: 0.15 }
);
revealEls.forEach((el) => observer.observe(el));

// Subtle cursor tilt on glass cards
document.querySelectorAll(".tilt").forEach((card) => {
  card.addEventListener("mousemove", (e) => {
    const rect = card.getBoundingClientRect();
    const x = (e.clientX - rect.left) / rect.width - 0.5;
    const y = (e.clientY - rect.top) / rect.height - 0.5;
    card.style.transform = `perspective(600px) rotateX(${-y * 6}deg) rotateY(${x * 6}deg)`;
  });
  card.addEventListener("mouseleave", () => {
    card.style.transform = "";
  });
});
