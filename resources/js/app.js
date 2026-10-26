document.addEventListener('DOMContentLoaded', () => {
    // Mobile navigation toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = document.getElementById('menu-icon');
    const closeIcon = document.getElementById('close-icon');

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            const isExpanded = mobileMenuBtn.getAttribute('aria-expanded') === 'true';
            mobileMenuBtn.setAttribute('aria-expanded', String(!isExpanded));
            mobileMenu.classList.toggle('hidden');

            if (menuIcon && closeIcon) {
                menuIcon.classList.toggle('hidden');
                closeIcon.classList.toggle('hidden');
            }
        });

        // Close mobile menu on clicking any link inside
        mobileMenu.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                mobileMenuBtn.setAttribute('aria-expanded', 'false');
                if (menuIcon && closeIcon) {
                    menuIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                }
            });
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                mobileMenuBtn.setAttribute('aria-expanded', 'false');
                if (menuIcon && closeIcon) {
                    menuIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                }
            }
        });
    }

    // Video playback error handling / fallback
    const heroVideo = document.getElementById('hero-video');
    const heroFallback = document.getElementById('hero-fallback');
    if (heroVideo && heroFallback) {
        heroVideo.addEventListener('error', () => {
            heroVideo.style.display = 'none';
            heroFallback.style.display = 'block';
        });
    }

    // ═══════════════════════════════════════════════════════════════════
    // REFINED HERO MOTION SYSTEM (Masked Reveal, Scroll-Linked Parallax, Magnetic CTA)
    // ═══════════════════════════════════════════════════════════════════
    function initHeroMotionSystem() {
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const heroSection = document.getElementById('hero');
        const heroCluster = document.getElementById('hero-content-cluster');
        const kicker = document.getElementById('hero-kicker');
        const headlineLines = document.querySelectorAll('.hero-masked-line');
        const desc = document.getElementById('hero-desc');
        const ctaGroup = document.getElementById('hero-cta-group');
        const ctaBtn = document.getElementById('hero-cta-btn');

        // 1. Masked Headline Reveal (Restrained translation & opacity)
        if (prefersReducedMotion) {
            if (kicker) kicker.classList.add('is-revealed');
            headlineLines.forEach((line) => line.classList.add('is-revealed'));
            if (desc) desc.classList.add('is-revealed');
            if (ctaGroup) ctaGroup.classList.add('is-revealed');
        } else {
            // Staggered reveal via RAF timeouts
            setTimeout(() => { if (kicker) kicker.classList.add('is-revealed'); }, 90);
            
            headlineLines.forEach((line, index) => {
                setTimeout(() => {
                    line.classList.add('is-revealed');
                }, 220 + (index * 140));
            });

            setTimeout(() => { if (desc) desc.classList.add('is-revealed'); }, 540);
            setTimeout(() => { if (ctaGroup) ctaGroup.classList.add('is-revealed'); }, 700);

            // Safety fallback: ensure complete visibility even if delayed
            setTimeout(() => {
                if (kicker) kicker.classList.add('is-revealed');
                headlineLines.forEach((line) => line.classList.add('is-revealed'));
                if (desc) desc.classList.add('is-revealed');
                if (ctaGroup) ctaGroup.classList.add('is-revealed');
            }, 1200);
        }

        // 2. Scroll-Linked Typography (scale 1 -> 0.94, translateY 0 -> 55px, opacity 1 -> 0.35)
        if (heroSection && heroCluster && !prefersReducedMotion) {
            let ticking = false;

            const updateHeroParallax = () => {
                const scrollY = window.scrollY;
                const heroHeight = heroSection.offsetHeight || 600;

                if (scrollY <= heroHeight * 1.05) {
                    const progress = Math.min(1, Math.max(0, scrollY / (heroHeight * 0.85)));
                    const scale = 1 - (progress * 0.06); // 1.0 down to 0.94
                    const translateY = progress * 55;   // 0px down to 55px
                    const opacity = 1 - (progress * 0.65); // 1.0 down to 0.35

                    heroCluster.style.transform = `translate3d(0, ${translateY.toFixed(2)}px, 0) scale(${scale.toFixed(3)})`;
                    heroCluster.style.opacity = opacity.toFixed(2);
                }
                ticking = false;
            };

            window.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(updateHeroParallax);
                    ticking = true;
                }
            }, { passive: true });

            // Initial call to sync with current scroll
            updateHeroParallax();
        }

        // 3. CTA Microinteraction (Desktop fine-pointer magnetic motion, strict 4-6px limit)
        const isFinePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
        if (ctaBtn && isFinePointer && !prefersReducedMotion) {
            const maxOffset = 5; // Strict 5px clamp limit

            ctaBtn.addEventListener('mousemove', (e) => {
                const rect = ctaBtn.getBoundingClientRect();
                const centerX = rect.left + rect.width / 2;
                const centerY = rect.top + rect.height / 2;
                const deltaX = (e.clientX - centerX) * 0.16;
                const deltaY = (e.clientY - centerY) * 0.16;
                const moveX = Math.max(-maxOffset, Math.min(maxOffset, deltaX));
                const moveY = Math.max(-maxOffset, Math.min(maxOffset, deltaY));

                ctaBtn.style.transform = `translate3d(${moveX.toFixed(2)}px, ${moveY.toFixed(2)}px, 0)`;
            });

            ctaBtn.addEventListener('mouseleave', () => {
                ctaBtn.style.transform = 'translate3d(0, 0, 0)';
            });

            ctaBtn.addEventListener('blur', () => {
                ctaBtn.style.transform = 'translate3d(0, 0, 0)';
            });
        }
    }

    // ═══════════════════════════════════════════════════════════════════
    // MOTION GRAPHICS SCROLL OBSERVER (Smooth 60fps kinetic reveals)
    // ═══════════════════════════════════════════════════════════════════
    function initMotionGraphics() {
        const motionElements = document.querySelectorAll(
            '.motion-reveal:not(.is-visible), .motion-reveal-text:not(.is-visible), .motion-scale:not(.is-visible), .motion-draw-line:not(.is-visible)'
        );

        if (!motionElements.length) return;

        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -40px 0px'
            });

            motionElements.forEach((el) => observer.observe(el));
        } else {
            motionElements.forEach((el) => el.classList.add('is-visible'));
        }
    }

    initHeroMotionSystem();
    initMotionGraphics();

    // Re-check on Livewire DOM morphs
    document.addEventListener('livewire:navigated', () => {
        initHeroMotionSystem();
        initMotionGraphics();
    });
    document.addEventListener('livewire:initialized', () => {
        initHeroMotionSystem();
        initMotionGraphics();
    });
});
