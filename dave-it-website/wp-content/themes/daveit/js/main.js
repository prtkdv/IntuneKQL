/* Dave IT — Main JavaScript */

(function () {
    'use strict';

    /* ------------------------------------------
       Sticky header
       ------------------------------------------ */
    const header = document.getElementById('site-header');
    if (header) {
        const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 40);
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    /* ------------------------------------------
       Mobile nav toggle
       ------------------------------------------ */
    const toggle = document.getElementById('nav-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    if (toggle && mobileMenu) {
        toggle.addEventListener('click', () => {
            const isOpen = mobileMenu.style.display === 'flex';
            mobileMenu.style.display = isOpen ? 'none' : 'flex';
            toggle.setAttribute('aria-expanded', String(!isOpen));
        });

        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.style.display = 'none';
                toggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    /* ------------------------------------------
       Active nav link on scroll
       ------------------------------------------ */
    const sections = document.querySelectorAll('section[id], div[id="trust-bar"]');
    const navLinks = document.querySelectorAll('.nav-links a[href^="#"]');

    const activateLink = () => {
        let current = '';
        sections.forEach(sec => {
            if (window.scrollY >= sec.offsetTop - 120) current = sec.id;
        });
        navLinks.forEach(link => {
            link.classList.toggle('active', link.getAttribute('href') === '#' + current);
        });
    };

    window.addEventListener('scroll', activateLink, { passive: true });

    /* ------------------------------------------
       Scroll reveal
       ------------------------------------------ */
    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        document.querySelectorAll('[data-reveal]').forEach(el => io.observe(el));
    } else {
        document.querySelectorAll('[data-reveal]').forEach(el => el.classList.add('revealed'));
    }

    /* ------------------------------------------
       Back to top
       ------------------------------------------ */
    const btt = document.getElementById('back-to-top');
    if (btt) {
        window.addEventListener('scroll', () => {
            btt.classList.toggle('visible', window.scrollY > 400);
        }, { passive: true });
        btt.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    }

    /* ------------------------------------------
       Contact form (AJAX)
       ------------------------------------------ */
    const form = document.getElementById('contact-form');
    const responseEl = document.getElementById('cf-response');
    const submitBtn = document.getElementById('cf-submit');

    if (form && typeof ajaxurl !== 'undefined') {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending…';
            responseEl.textContent = '';
            responseEl.style.color = '';

            const data = new FormData(form);

            try {
                const res = await fetch(ajaxurl, { method: 'POST', body: data });
                const json = await res.json();

                if (json.success) {
                    responseEl.textContent = json.data.message;
                    responseEl.style.color = '#34D399';
                    form.reset();
                } else {
                    responseEl.textContent = json.data?.message || 'Something went wrong. Please try again.';
                    responseEl.style.color = '#F87171';
                }
            } catch {
                responseEl.textContent = 'Network error. Please try again.';
                responseEl.style.color = '#F87171';
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Send Message →';
            }
        });
    }

    /* ------------------------------------------
       Smooth scroll for anchor links
       ------------------------------------------ */
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', (e) => {
            const id = anchor.getAttribute('href').slice(1);
            const target = document.getElementById(id);
            if (!target) return;
            e.preventDefault();
            const offset = 80;
            window.scrollTo({ top: target.offsetTop - offset, behavior: 'smooth' });
        });
    });

})();
