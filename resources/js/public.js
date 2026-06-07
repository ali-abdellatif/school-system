import './bootstrap';

/**
 * تفاعلات الموقع العام — JavaScript أصلي بدون Alpine
 * لتفادي تعارض نسختين من Alpine مع سكربتات Livewire.
 */

function initRevealOnScroll() {
    const els = document.querySelectorAll('[data-reveal]');
    if (!els.length) return;

    if (!('IntersectionObserver' in window)) {
        els.forEach((el) => el.classList.add('is-revealed'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const delay = entry.target.getAttribute('data-reveal-delay') || 0;
                    setTimeout(() => entry.target.classList.add('is-revealed'), delay);
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.15, rootMargin: '0px 0px -40px 0px' }
    );

    els.forEach((el) => observer.observe(el));
}

function initCountUp() {
    const counters = document.querySelectorAll('[data-count]');
    if (!counters.length) return;

    const animate = (el) => {
        const target = parseFloat(el.getAttribute('data-count'));
        const prefix = el.getAttribute('data-count-prefix') || '';
        const suffix = el.getAttribute('data-count-suffix') || '';
        const duration = 1600;
        const start = performance.now();

        const tick = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3); // easeOutCubic
            const value = Math.floor(eased * target);
            el.textContent = prefix + value.toLocaleString('en-US') + suffix;
            if (progress < 1) requestAnimationFrame(tick);
            else el.textContent = prefix + target.toLocaleString('en-US') + suffix;
        };
        requestAnimationFrame(tick);
    };

    if (!('IntersectionObserver' in window)) {
        counters.forEach(animate);
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    animate(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.5 }
    );

    counters.forEach((el) => observer.observe(el));
}

function initNavbarScroll() {
    const nav = document.getElementById('main-nav');
    if (!nav) return;

    const onScroll = () => {
        if (window.scrollY > 20) {
            nav.classList.add('nav-scrolled');
        } else {
            nav.classList.remove('nav-scrolled');
        }
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
}

function initMobileMenu() {
    const toggle = document.getElementById('mobile-menu-toggle');
    const menu = document.getElementById('mobile-menu');
    if (!toggle || !menu) return;

    const iconOpen = toggle.querySelector('[data-icon="open"]');
    const iconClose = toggle.querySelector('[data-icon="close"]');

    const setMenuState = (open) => {
        menu.classList.toggle('hidden', !open);
        iconOpen?.classList.toggle('hidden', open);
        iconClose?.classList.toggle('hidden', !open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        toggle.setAttribute('aria-label', open ? 'إغلاق القائمة' : 'فتح القائمة');
    };

    toggle.addEventListener('click', () => {
        setMenuState(menu.classList.contains('hidden'));
    });

    menu.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => setMenuState(false));
    });
}

function initFaqAccordion() {
    const items = document.querySelectorAll('[data-faq-item]');
    if (!items.length) return;

    items.forEach((item) => {
        const trigger = item.querySelector('.faq-trigger');
        const panel = item.querySelector('.faq-panel');
        if (!trigger || !panel) return;

        trigger.addEventListener('click', () => {
            const isOpen = item.classList.contains('is-open');

            items.forEach((other) => {
                other.classList.remove('is-open');
                other.querySelector('.faq-trigger')?.setAttribute('aria-expanded', 'false');
                other.querySelector('.faq-panel')?.classList.add('hidden');
            });

            if (!isOpen) {
                item.classList.add('is-open');
                trigger.setAttribute('aria-expanded', 'true');
                panel.classList.remove('hidden');
            }
        });
    });
}

function initScrollProgress() {
    const bar = document.getElementById('scroll-progress');
    if (!bar) return;

    const update = () => {
        const el = document.documentElement;
        const max = el.scrollHeight - el.clientHeight;
        bar.style.width = max > 0 ? `${(el.scrollTop / max) * 100}%` : '0%';
    };
    update();
    window.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update, { passive: true });
}

function initBackToTop() {
    const btn = document.getElementById('back-to-top');
    if (!btn) return;

    const toggle = () => btn.classList.toggle('is-visible', window.scrollY > 500);
    toggle();
    window.addEventListener('scroll', toggle, { passive: true });
    btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
}

function initLightbox() {
    const lb = document.getElementById('lightbox');
    if (!lb) return;

    const img = document.getElementById('lightbox-img');
    const caption = document.getElementById('lightbox-caption');
    const triggers = document.querySelectorAll('[data-lightbox]');
    if (!triggers.length) return;

    const open = (src, text) => {
        img.src = src;
        img.alt = text || '';
        caption.textContent = text || '';
        lb.classList.add('is-open');
        lb.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    };

    const close = () => {
        lb.classList.remove('is-open');
        lb.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        img.src = '';
    };

    triggers.forEach((el) => {
        const fire = () => open(el.getAttribute('data-lightbox-src'), el.getAttribute('data-lightbox-caption'));
        el.addEventListener('click', fire);
        el.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                fire();
            }
        });
    });

    lb.addEventListener('click', (e) => {
        if (e.target === lb || e.target.closest('[data-lightbox-close]')) close();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && lb.classList.contains('is-open')) close();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initRevealOnScroll();
    initCountUp();
    initNavbarScroll();
    initMobileMenu();
    initFaqAccordion();
    initScrollProgress();
    initBackToTop();
    initLightbox();
});
