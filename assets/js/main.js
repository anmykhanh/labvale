/**
 * LabVale — Client-Side Vanilla ES6+ Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Sticky Header Shadow on Scroll
    const header = document.querySelector('.site-header');
    if (header) {
        const handleScroll = () => {
            if (window.scrollY > 15) {
                header.classList.add('is-scrolled');
            } else {
                header.classList.remove('is-scrolled');
            }
        };
        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll();
    }

    // 2. Mobile Drawer Navigation Toggle
    const mobileToggle = document.querySelector('.mobile-nav-toggle');
    const mobileDrawer = document.querySelector('.mobile-drawer');

    if (mobileToggle && mobileDrawer) {
        const toggleMenu = () => {
            const isOpen = mobileDrawer.classList.toggle('is-open');
            mobileToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            if (isOpen) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        };

        mobileToggle.addEventListener('click', toggleMenu);

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && mobileDrawer.classList.contains('is-open')) {
                toggleMenu();
            }
        });

        // Close when clicking a link inside mobile drawer
        mobileDrawer.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                if (mobileDrawer.classList.contains('is-open')) {
                    toggleMenu();
                }
            });
        });
    }

    // 3. Copy Link Helper
    const copyBtns = document.querySelectorAll('[data-copy-link]');
    copyBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const textToCopy = btn.getAttribute('data-copy-link') || window.location.href;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(textToCopy).then(() => {
                    const originalText = btn.textContent;
                    btn.textContent = 'Đã sao chép liên kết!';
                    btn.classList.add('btn-primary');
                    setTimeout(() => {
                        btn.textContent = originalText;
                        btn.classList.remove('btn-primary');
                    }, 2200);
                }).catch(() => {
                    prompt('Sao chép liên kết:', textToCopy);
                });
            } else {
                prompt('Sao chép liên kết:', textToCopy);
            }
        });
    });
});
