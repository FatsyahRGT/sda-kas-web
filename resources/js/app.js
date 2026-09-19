// Service Worker Registration
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').then((reg) => {
            console.log('SW registered:', reg.scope);
        }).catch((err) => {
            console.warn('SW registration failed:', err);
        });
    });
}

// Alpine.js data helpers (global)
document.addEventListener('DOMContentLoaded', () => {
    // Flash message auto-dismiss
    const flashes = document.querySelectorAll('[data-flash]');
    flashes.forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        }, 4000);
    });

    // Format nominal input as currency while typing
    document.querySelectorAll('input[data-rupiah]').forEach(input => {
        input.addEventListener('input', () => {
            let val = input.value.replace(/\D/g, '');
            input.value = val;
        });
    });
});
