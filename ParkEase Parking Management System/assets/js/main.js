/* ============================================================
   PARKEASE — ENTERPRISE JAVASCRIPT SYSTEM
   High Performance, No Delay, Smooth Interactions
   ============================================================ */

'use strict';

// ============================================================
// 1. MOBILE NAVIGATION & SIDEBAR TOGGLE
// ============================================================
function toggleSidebar() {
  const sidebar = document.getElementById('appSidebar');
  if (sidebar) {
    sidebar.classList.toggle('open');
  }
}

function toggleMenu() {
  const navLinks = document.getElementById('navLinks');
  if (navLinks) {
    navLinks.classList.toggle('open');
  }
}

// Close mobile navigation or sidebar when clicking outside
document.addEventListener('click', function (e) {
  const nav = document.getElementById('navLinks');
  const navBtn = document.querySelector('.menu-toggle');
  if (nav && navBtn && !nav.contains(e.target) && !navBtn.contains(e.target)) {
    nav.classList.remove('open');
  }

  const sidebar = document.getElementById('appSidebar');
  const sidebarBtn = document.querySelector('.sidebar-mobile-toggle');
  if (sidebar && sidebarBtn && !sidebar.contains(e.target) && !sidebarBtn.contains(e.target)) {
    sidebar.classList.remove('open');
  }
});

// ============================================================
// 2. NAVBAR SCROLL ELEVATION
// ============================================================
(function initNavScroll() {
  const navbar = document.querySelector('.navbar');
  if (!navbar) return;
  const onScroll = () => {
    navbar.classList.toggle('scrolled', window.scrollY > 12);
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
})();

// ============================================================
// 3. SCROLL-REVEAL SYSTEM (INTERSECTION OBSERVER)
// ============================================================
(function initScrollReveal() {
  const elements = document.querySelectorAll('[data-animation]');
  if (!elements.length) return;

  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReduced) {
    elements.forEach(el => el.classList.add('is-visible'));
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.08, rootMargin: '0px 0px -20px 0px' }
  );

  elements.forEach(el => observer.observe(el));
})();

// ============================================================
// 4. STAT COUNTER ROLL-UP ANIMATION
// ============================================================
(function initCounters() {
  const counters = document.querySelectorAll('.stat-number');
  if (!counters.length) return;

  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReduced) return;

  counters.forEach(counter => {
    const rawVal = counter.textContent.trim();
    const target = parseInt(rawVal, 10);
    if (isNaN(target)) return;

    counter.textContent = '0';
    const duration = 650;
    const start = performance.now();

    const step = (now) => {
      const elapsed = now - start;
      const progress = Math.min(elapsed / duration, 1);
      const ease = 1 - Math.pow(1 - progress, 3);
      counter.textContent = Math.floor(ease * target);
      if (progress < 1) {
        requestAnimationFrame(step);
      } else {
        counter.textContent = target;
      }
    };

    setTimeout(() => requestAnimationFrame(step), 120);
  });
})();

// ============================================================
// 5. TACTILE BUTTON RIPPLE EFFECT
// ============================================================
(function initButtonRipple() {
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn');
    if (!btn) return;

    const rect = btn.getBoundingClientRect();
    const circle = document.createElement('span');
    const diameter = Math.max(rect.width, rect.height);
    const radius = diameter / 2;

    circle.style.width = circle.style.height = `${diameter}px`;
    circle.style.left = `${e.clientX - rect.left - radius}px`;
    circle.style.top = `${e.clientY - rect.top - radius}px`;
    circle.style.position = 'absolute';
    circle.style.borderRadius = '50%';
    circle.style.background = 'rgba(0, 0, 0, 0.06)';
    circle.style.transform = 'scale(0)';
    circle.style.animation = 'ripple 0.45s ease-out';
    circle.style.pointerEvents = 'none';

    btn.appendChild(circle);
    setTimeout(() => circle.remove(), 480);
  });
})();

// ============================================================
// 6. AUTO-DISMISS ALERTS
// ============================================================
(function initAlertDismiss() {
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(alert => {
    setTimeout(() => {
      alert.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
      alert.style.opacity = '0';
      alert.style.transform = 'translateY(-4px)';
      setTimeout(() => alert.remove(), 320);
    }, 5000);
  });
})();

// ============================================================
// 7. TABLE ROWS STAGGER REVEAL
// ============================================================
(function initTableStagger() {
  const rows = document.querySelectorAll('.data-table tbody tr');
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReduced) return;

  rows.forEach((row, i) => {
    row.style.opacity = '0';
    row.style.transform = 'translateY(6px)';
    row.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
    setTimeout(() => {
      row.style.opacity = '1';
      row.style.transform = 'translateY(0)';
    }, 40 + i * 30);
  });
})();
