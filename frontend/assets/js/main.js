(function () {
  const PROMOS_ENDPOINT = "/frontend/api/promos.php";
  const AUTO_INTERVAL_MS = 8000;

  function preloadImage(src) {
    return new Promise((resolve) => {
      const img = new Image();
      img.onload = () => resolve(true);
      img.onerror = () => resolve(false);
      img.src = src;
    });
  }

  function setHeroBackground(heroEl, promo) {
    heroEl.style.backgroundImage = `url('${promo.src}')`;
  }

  function renderDots(dotsEl, promos, activeIndex, onDotClick) {
    dotsEl.innerHTML = "";
    promos.forEach((_, idx) => {
      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "promo-dot" + (idx === activeIndex ? " active" : "");
      btn.setAttribute("aria-label", `Go to promo ${idx + 1}`);
      btn.addEventListener("click", () => onDotClick(idx));
      dotsEl.appendChild(btn);
    });
  }

  async function initPromoHero() {
    const heroEl = document.getElementById("promoHero");
    if (!heroEl) return;

    const prevBtn = heroEl.querySelector(".promo-prev");
    const nextBtn = heroEl.querySelector(".promo-next");
    const dotsEl = heroEl.querySelector(".promo-dots");

    if (!prevBtn || !nextBtn || !dotsEl) return;

    let promos = [];
    try {
      const res = await fetch(PROMOS_ENDPOINT, { cache: "no-store" });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = await res.json();
      if (Array.isArray(data)) promos = data.filter(p => p && typeof p.src === "string" && p.src.length > 0);
    } catch (e) {
      console.error("Failed to load promos:", e);
      promos = [];
    }

    if (promos.length === 0) {
      prevBtn.style.display = "none";
      nextBtn.style.display = "none";
      dotsEl.style.display = "none";
      return;
    }

    let index = 0;
    let timerId = null;

    // Preload first promo (best-effort)
    await preloadImage(promos[0].src);
    setHeroBackground(heroEl, promos[0]);
    renderDots(dotsEl, promos, index, goTo);

    function goTo(newIndex) {
      index = (newIndex + promos.length) % promos.length;
      setHeroBackground(heroEl, promos[index]);
      renderDots(dotsEl, promos, index, goTo);
      restartTimer();
    }

    function next() {
      goTo(index + 1);
    }

    function prev() {
      goTo(index - 1);
    }

    function restartTimer() {
      if (timerId) clearInterval(timerId);
      timerId = setInterval(next, AUTO_INTERVAL_MS);
    }

    prevBtn.addEventListener("click", prev);
    nextBtn.addEventListener("click", next);

    restartTimer();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initPromoHero);
  } else {
    initPromoHero();
  }
})();
