// ══════════════════════════════════════════════════════════
// Binance Landing Page — Binance-inspired JS
// ══════════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', () => {
    initHamburger();
    initFAQ();
    initDownloadTabs();
    initCountUp();
    initScrollShadow();
    initSmoothScroll();
});

// ── Mobile hamburger menu ────────────────────────────────────────

function initHamburger() {
    const btn = document.getElementById('hamburger');
    const nav = document.getElementById('nav-links');
    if (!btn || !nav) return;

    btn.addEventListener('click', () => {
        nav.classList.toggle('mobile-open');
        const expanded = btn.getAttribute('aria-expanded') === 'true';
        btn.setAttribute('aria-expanded', String(!expanded));
    });
}

// ── FAQ accordion ────────────────────────────────────────────────

function initFAQ() {
    document.querySelectorAll('.faq-question').forEach(btn => {
        btn.addEventListener('click', () => {
            const item = btn.closest('.faq-item');
            const isOpen = item.classList.contains('open');

            // Close all
            document.querySelectorAll('.faq-item.open').forEach(el => {
                el.classList.remove('open');
            });

            // Toggle clicked
            if (!isOpen) {
                item.classList.add('open');
            }
        });
    });
}

// ── Download tabs ────────────────────────────────────────────────

function initDownloadTabs() {
    document.querySelectorAll('.download-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.download-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            const platform = tab.dataset.platform;
            const infoEl = document.getElementById('download-platform-info');
            if (infoEl) {
                infoEl.textContent = tab.textContent + ' download options';
            }
        });
    });
}

// ── Count-up animation on scroll ─────────────────────────────────

function initCountUp() {
    const nums = document.querySelectorAll('.stat-num[data-count]');
    if (!nums.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;

            const el = entry.target;
            const target = parseFloat(el.dataset.count);
            const suffix = (el.nextElementSibling?.textContent || '').replace(/[0-9.]/g, '');
            const isDecimal = el.dataset.count.includes('.');
            const decimals = isDecimal ? el.dataset.count.split('.')[1].length : 0;
            const duration = 1500;
            const start = performance.now();

            function step(now) {
                const elapsed = now - start;
                const progress = Math.min(elapsed / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                const current = target * eased;
                el.textContent = isDecimal ? current.toFixed(decimals) : Math.floor(current).toLocaleString();
                if (progress < 1) {
                    requestAnimationFrame(step);
                } else {
                    el.textContent = isDecimal ? target.toFixed(decimals) : Math.floor(target).toLocaleString();
                }
            }

            requestAnimationFrame(step);
            observer.unobserve(el);
        });
    }, { threshold: 0.3 });

    nums.forEach(el => observer.observe(el));
}

// ── Navbar shadow on scroll ──────────────────────────────────────

function initScrollShadow() {
    const navbar = document.getElementById('navbar');
    if (!navbar) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 10) {
            navbar.style.boxShadow = '0 2px 8px rgba(0,0,0,0.4)';
        } else {
            navbar.style.boxShadow = 'none';
        }
    });
}

// ── Smooth scroll for anchor links ───────────────────────────────

function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');
            if (href === '#') return;
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
}
