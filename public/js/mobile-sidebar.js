// Mobile Sidebar Enhancement Script
document.addEventListener('DOMContentLoaded', function () {
    console.log('[Mobile] Initializing sidebar enhancements...');

    // Check if we're on mobile
    const isMobile = window.innerWidth <= 768;

    if (isMobile) {
        console.log('[Mobile] Mobile device detected, applying sidebar fixes...');

        // Add CSS to force sidebar button visibility
        const style = document.createElement('style');
        style.id = 'mobile-sidebar-fix';
        style.textContent = `
            /* Force sidebar toggle button to show on mobile */
            @media (max-width: 768px) {
                button[x-on\\:click*="sidebar"] {
                    display: flex !important;
                    opacity: 1 !important;
                    visibility: visible !important;
                }
                
                /* Make sure topbar shows the menu button */
                .fi-topbar .fi-topbar-start {
                    display: flex !important;
                }
            }
        `;
        document.head.appendChild(style);

        // Try to find and enhance sidebar toggle button
        setTimeout(() => {
            const sidebarButtons = document.querySelectorAll('button[x-on\\:click*="sidebar"], button[aria-label*="menu"], button[aria-label*="navigation"]');

            sidebarButtons.forEach(btn => {
                console.log('[Mobile] Found sidebar button:', btn);
                btn.style.display = 'flex';
                btn.style.opacity = '1';
                btn.style.visibility = 'visible';
            });

            if (sidebarButtons.length === 0) {
                console.warn('[Mobile] No sidebar toggle button found!');
            }
        }, 500);
    }
});

// Also check on window resize
window.addEventListener('resize', function () {
    const isMobile = window.innerWidth <= 768;
    const fixStyle = document.getElementById('mobile-sidebar-fix');

    if (isMobile && !fixStyle) {
        location.reload(); // Reload to apply mobile fixes
    }
});

console.log('[Mobile] Sidebar enhancement script loaded');
