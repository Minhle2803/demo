// ══════════════════════════════════════════════════════════
// TradeX Market Landing — HNX-style Slideshow + Vue
// ══════════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', () => {
    initSlideshow();
    initKeyboardNav();
    initVueApps();
});

// ── Background images per slide ──────────────────────────────

const bgImages = [
    '/assets/images/landing6/main-BG-01.jpg',
    '/assets/images/landing6/main-BG-03.jpg',
    '/assets/images/landing6/main-BG-01.jpg',
    '/assets/images/landing6/main-BG-03.jpg',
];

// ── Slideshow ────────────────────────────────────────────────

let slideIndex = 1;

function showSlides(n) {
    const slides = document.getElementsByClassName('mySlides');
    if (slides.length === 0) return;

    if (n > slides.length) { slideIndex = 1; }
    if (n < 1) { slideIndex = slides.length; }

    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = 'none';
        slides[i].classList.remove('fade-in-right');
    }

    const currentSlide = slides[slideIndex - 1];
    currentSlide.style.display = 'block';
    currentSlide.classList.add('fade-in-right');

    // Rotate background
    const contentMain = document.getElementById('contentMain');
    if (contentMain) {
        contentMain.style.backgroundImage = `url('${bgImages[slideIndex - 1]}')`;
    }
}

function initSlideshow() {
    showSlides(slideIndex);

    let isScrolling = false;

    window.addEventListener('wheel', (e) => {
        if (isScrolling) return;
        isScrolling = true;

        if (e.deltaY > 0) {
            slideIndex++;
            if (slideIndex > document.getElementsByClassName('mySlides').length) {
                slideIndex = 1;
            }
        } else {
            slideIndex--;
            if (slideIndex < 1) {
                slideIndex = document.getElementsByClassName('mySlides').length;
            }
        }
        showSlides(slideIndex);

        setTimeout(() => { isScrolling = false; }, 600);
    }, { passive: true });
}

window.plusSlides = function (n) {
    showSlides(slideIndex += n);
};

// ── Keyboard navigation ──────────────────────────────────────

function initKeyboardNav() {
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
            e.preventDefault();
            window.plusSlides(1);
        } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
            e.preventDefault();
            window.plusSlides(-1);
        }
    });
}

// ── Vue Apps ────────────────────────────────────────────────

function initVueApps() {
    const appData = window.__LANDING6_DATA__;
    if (!appData) return;

    const indicesEl = document.getElementById('marketIndices');
    if (indicesEl) {
        Vue.createApp({
            data() {
                return { indices: appData.indices || [] };
            },
        }).mount('#marketIndices');

        // Duplicate for seamless marquee
        indicesEl.innerHTML += indicesEl.innerHTML;
    }

    const tickerEl = document.getElementById('tickerContent');
    if (tickerEl) {
        Vue.createApp({
            data() {
                return { tickerCoins: appData.tickerCoins || [] };
            },
        }).mount('#tickerContent');

        // Duplicate for seamless marquee
        tickerEl.innerHTML += tickerEl.innerHTML;
    }
}
