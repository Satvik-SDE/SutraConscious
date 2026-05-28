import './bootstrap';
import Alpine from 'alpinejs';

document.documentElement.classList.add('js');

/* ─────────────────────────────────────────────
   Global Alpine stores
   ───────────────────────────────────────────── */
document.addEventListener('alpine:init', () => {
    Alpine.store('drawer', {
        open: false,
        toggle() { this.open = !this.open; document.body.style.overflow = this.open ? 'hidden' : ''; },
        close() { this.open = false; document.body.style.overflow = ''; },
        show() { this.open = true; document.body.style.overflow = 'hidden'; },
    });

    Alpine.store('nav', {
        scrolled: false,
        mobileOpen: false,
    });
});

window.Alpine = Alpine;
Alpine.start();

/* ─────────────────────────────────────────────
   Scroll reveal observer
   ───────────────────────────────────────────── */
const initReveal = () => {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
        document.querySelectorAll('[data-reveal]').forEach(el => el.classList.add('is-revealed'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-revealed');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.12,
        rootMargin: '0px 0px -80px 0px',
    });

    document.querySelectorAll('[data-reveal]').forEach(el => observer.observe(el));
};

/* ─────────────────────────────────────────────
   Scrolled state for header
   ───────────────────────────────────────────── */
const initScrolledFlag = () => {
    const update = () => {
        const scrolled = window.scrollY > 24;
        if (Alpine.store('nav').scrolled !== scrolled) {
            Alpine.store('nav').scrolled = scrolled;
        }
    };
    window.addEventListener('scroll', update, { passive: true });
    update();
};

/* ─────────────────────────────────────────────
   Image zoom (product page) — lens-style
   ───────────────────────────────────────────── */
const initImageZoom = () => {
    document.querySelectorAll('[data-zoom]').forEach(container => {
        const img = container.querySelector('img');
        if (!img) return;

        const isCoarse = window.matchMedia('(pointer: coarse)').matches;
        if (isCoarse) return;

        container.addEventListener('mouseenter', () => {
            img.style.transition = 'transform 200ms ease-out';
        });

        container.addEventListener('mousemove', (e) => {
            const rect = container.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;
            img.style.transformOrigin = `${x}% ${y}%`;
            img.style.transform = 'scale(1.8)';
        });

        container.addEventListener('mouseleave', () => {
            img.style.transform = '';
            img.style.transformOrigin = '';
        });
    });
};

/* ─────────────────────────────────────────────
   Boot
   ───────────────────────────────────────────── */
const boot = () => {
    initReveal();
    initScrolledFlag();
    initImageZoom();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}

window.addEventListener('error', () => {
    document.querySelectorAll('[data-reveal]').forEach(el => el.classList.add('is-revealed'));
});
