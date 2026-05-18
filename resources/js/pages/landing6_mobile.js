// ══════════════════════════════════════════════════════════
// TradeX Mobile Landing — Accordion + Nav + Footer Menu
// ══════════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', () => {
    initAccordions();
    initMobileNav();
    initFooterMenu();
    initSmoothScroll();
});

// ── Accordion Toggle ──────────────────────────────────────

function initAccordions() {
    document.querySelectorAll('.accordion-toggle').forEach(toggle => {
        toggle.addEventListener('click', () => {
            const targetId = toggle.dataset.target;
            const body = document.getElementById(targetId);
            const arrow = toggle.querySelector('.accordion-arrow');

            if (!body) return;

            const isOpen = body.classList.contains('open');

            // Close all
            document.querySelectorAll('.accordion-body.open').forEach(b => b.classList.remove('open'));
            document.querySelectorAll('.accordion-arrow.open').forEach(a => a.classList.remove('open'));

            // Open clicked (if it wasn't already open)
            if (!isOpen) {
                body.classList.add('open');
                if (arrow) arrow.classList.add('open');
                body.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    });

    // Open first accordion by default
    const firstBody = document.querySelector('.accordion-body');
    const firstArrow = document.querySelector('.accordion-arrow');
    if (firstBody) {
        firstBody.classList.add('open');
        if (firstArrow) firstArrow.classList.add('open');
    }
}

// ── Mobile Nav Active State ───────────────────────────────

function initMobileNav() {
    const navItems = document.querySelectorAll('.mobile-nav .nav-item');

    navItems.forEach(item => {
        item.addEventListener('click', () => {
            navItems.forEach(n => n.classList.remove('active'));
            item.classList.add('active');
        });
    });

    // Update active on scroll
    const sections = document.querySelectorAll('.content-section');
    if (!sections.length) return;

    let scrollTimeout;
    window.addEventListener('scroll', () => {
        if (scrollTimeout) clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            let currentSection = '';
            sections.forEach(section => {
                const rect = section.getBoundingClientRect();
                if (rect.top <= 120) {
                    currentSection = section.id;
                }
            });

            if (currentSection) {
                navItems.forEach(item => {
                    item.classList.remove('active');
                    if (item.getAttribute('href') === '#' + currentSection) {
                        item.classList.add('active');
                    }
                });
            }
        }, 100);
    }, { passive: true });
}

// ── Footer Menu Toggle ────────────────────────────────────

function initFooterMenu() {
    const toggle = document.getElementById('footerMenuToggle');
    const menu = document.getElementById('footerMenu');

    if (toggle && menu) {
        toggle.addEventListener('click', () => {
            menu.classList.toggle('open');
        });
    }
}

// ── Smooth Scroll for Nav Links ───────────────────────────

function initSmoothScroll() {
    document.querySelectorAll('.mobile-nav .nav-item').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('href');
            if (!targetId || !targetId.startsWith('#')) return;

            const target = document.querySelector(targetId);
            if (!target) return;

            const headerHeight = document.getElementById('mobile-header').offsetHeight;
            const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight - 8;

            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });

            // Auto-open accordion
            const accordionBody = target.querySelector('.accordion-body');
            if (accordionBody && !accordionBody.classList.contains('open')) {
                document.querySelectorAll('.accordion-body.open').forEach(b => b.classList.remove('open'));
                document.querySelectorAll('.accordion-arrow.open').forEach(a => a.classList.remove('open'));
                accordionBody.classList.add('open');
                const arrow = target.querySelector('.accordion-arrow');
                if (arrow) arrow.classList.add('open');
            }
        });
    });
}
