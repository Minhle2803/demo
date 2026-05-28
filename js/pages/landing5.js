// ══════════════════════════════════════════════════════════
// Crypto Trading Landing — Interactive JS
// ══════════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', () => {
    initHamburger();
    initFAQ();
    initCountUp();
    initTickerAnimation();
    initScrollReveal();
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

            document.querySelectorAll('.faq-item.open').forEach(el => {
                el.classList.remove('open');
            });

            if (!isOpen) {
                item.classList.add('open');
            }
        });
    });
}

// ── Count-up animation for stat numbers ──────────────────────────

function initCountUp() {
    const nums = document.querySelectorAll('.stat-num');
    if (!nums.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const target = parseInt(el.dataset.count, 10);
                const suffix = el.dataset.suffix || '';
                let current = 0;
                const step = Math.ceil(target / 60);
                const timer = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    el.textContent = current + suffix;
                }, 30);
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.5 });

    nums.forEach(num => observer.observe(num));
}

// ── Live ticker price animation ──────────────────────────────────

function initTickerAnimation() {
    const changes = document.querySelectorAll('.coin-change');
    changes.forEach(el => {
        setInterval(() => {
            const isUp = el.classList.contains('up');
            const current = parseFloat(el.textContent);
            if (isNaN(current)) return;
            const delta = (Math.random() - 0.48) * 0.3;
            const next = current + delta;
            const sign = next >= 0 ? '+' : '';
            el.textContent = sign + next.toFixed(2) + '%';
            if (next >= 0) {
                el.classList.remove('down');
                el.classList.add('up');
            } else {
                el.classList.remove('up');
                el.classList.add('down');
            }
        }, 3000 + Math.random() * 2000);
    });
}

// ── Scroll reveal animation ──────────────────────────────────────

function initScrollReveal() {
    const cards = document.querySelectorAll('.feature-card, .tool-card, .step-card, .coin-card, .stat-card');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    cards.forEach((card, i) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(24px)';
        card.style.transition = `all 0.5s ease ${i * 0.05}s`;
        observer.observe(card);
    });
}
