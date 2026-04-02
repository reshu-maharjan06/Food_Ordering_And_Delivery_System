// ─── SCROLL REVEAL ────────────────────────────────────────────────────────────

const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) entry.target.classList.add("on");
    });
  },
  { threshold: 0.1 },
);

document.querySelectorAll(".rv").forEach((el) => observer.observe(el));
