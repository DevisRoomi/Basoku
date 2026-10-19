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
});
