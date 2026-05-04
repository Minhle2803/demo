/**
 * Binance Landing Page — Interactive Features
 * FAQ accordion, download tabs, mobile nav, scroll effects
 */
document.addEventListener('DOMContentLoaded', () => {

    /* ═══════════════ FAQ ACCORDION ═══════════════ */
    const faqQuestions = document.querySelectorAll('.bn-faq__question');

    faqQuestions.forEach(btn => {
        btn.addEventListener('click', () => {
            const answer = btn.nextElementSibling;
            const isOpen = btn.getAttribute('aria-expanded') === 'true';

            // Close all
            faqQuestions.forEach(q => {
                q.setAttribute('aria-expanded', 'false');
                q.nextElementSibling?.classList.remove('bn-faq__answer--open');
            });

            // Open clicked (unless it was already open)
            if (!isOpen) {
                btn.setAttribute('aria-expanded', 'true');
                answer?.classList.add('bn-faq__answer--open');
            }
        });
    });

    /* ═══════════════ DOWNLOAD TAB SWITCHER ═══════════════ */
    const downloadTabs = document.querySelectorAll('.bn-download__tab');
    const previewImg = document.getElementById('download-preview-img');

    const previewImages = {
        desktop: 'https://bin.bnbstatic.com/image/julia/new-homepage/download-desktop-dark-en.png',
        lite: 'https://bin.bnbstatic.com/image/julia/new-homepage/download-lite-dark-en.png',
        pro: 'https://bin.bnbstatic.com/image/julia/new-homepage/download-pro-dark-en.png',
    };

    downloadTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            downloadTabs.forEach(t => t.classList.remove('bn-download__tab--active'));
            tab.classList.add('bn-download__tab--active');

            const tabName = tab.dataset.tab;
            if (previewImg && previewImages[tabName]) {
                previewImg.src = previewImages[tabName];
            }
        });
    });

    /* ═══════════════ MOBILE NAV ═══════════════ */
    const hamburger = document.getElementById('bn-hamburger');
    const header = document.querySelector('.bn-header');

    if (hamburger && header) {
        // Create overlay and panel
        const overlay = document.createElement('div');
        overlay.className = 'bn-mobile-nav-overlay';
        overlay.id = 'bn-mobile-overlay';

        const panel = document.createElement('div');
        panel.className = 'bn-mobile-nav-panel';
        panel.id = 'bn-mobile-panel';

        // Clone nav links into mobile panel
        const navLinks = header.querySelectorAll('.bn-nav__link');
        const navClone = document.createElement('nav');
        navClone.style.display = 'flex';
        navClone.style.flexDirection = 'column';
        navClone.style.gap = '0';
        navLinks.forEach(link => {
            const clone = link.cloneNode(true);
            clone.classList.add('bn-mobile-nav-link');
            navClone.appendChild(clone);
        });
        panel.appendChild(navClone);

        // Add auth buttons to mobile panel
        const authBtns = header.querySelectorAll('.bn-header__right .bn-btn');
        const authClone = document.createElement('div');
        authClone.style.display = 'flex';
        authClone.style.flexDirection = 'column';
        authClone.style.gap = '8px';
        authClone.style.marginTop = '20px';
        authBtns.forEach(btn => {
            const clone = btn.cloneNode(true);
            authClone.appendChild(clone);
        });
        panel.appendChild(authClone);

        document.body.appendChild(overlay);
        document.body.appendChild(panel);

        const openMenu = () => {
            overlay.classList.add('bn-mobile-nav-overlay--open');
            panel.classList.add('bn-mobile-nav-panel--open');
            document.body.style.overflow = 'hidden';
        };

        const closeMenu = () => {
            overlay.classList.remove('bn-mobile-nav-overlay--open');
            panel.classList.remove('bn-mobile-nav-panel--open');
            document.body.style.overflow = '';
        };

        hamburger.addEventListener('click', openMenu);
        overlay.addEventListener('click', closeMenu);

        // Close on link click
        panel.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', closeMenu);
        });
    }

    /* ═══════════════ HEADER SCROLL SHADOW ═══════════════ */
    let lastScrollY = window.scrollY;

    const onScroll = () => {
        const scrollY = window.scrollY;
        if (scrollY > 10) {
            header.classList.add('bn-header--scrolled');
        } else {
            header.classList.remove('bn-header--scrolled');
        }
        lastScrollY = scrollY;
    };

    window.addEventListener('scroll', onScroll, { passive: true });

    /* ═══════════════ COUNT-UP ANIMATION ON SCROLL ═══════════════ */
    const statValues = document.querySelectorAll('.bn-hero-stat__value, .bn-safu-stat__value');

    const formatStat = (el) => {
        // Already has content from server — skip count-up for simplicity
        // This is a placeholder for potential count-up animation
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.2 });

    document.querySelectorAll('.bn-hero-stat, .bn-award, .bn-news-card, .bn-safu-stat, .bn-coin-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(el);
    });

    // Trigger initial visible elements
    setTimeout(() => {
        document.querySelectorAll('.bn-hero-stat, .bn-award, .bn-news-card, .bn-safu-stat, .bn-coin-card').forEach(el => {
            const rect = el.getBoundingClientRect();
            if (rect.top < window.innerHeight) {
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }
        });
    }, 100);

});
