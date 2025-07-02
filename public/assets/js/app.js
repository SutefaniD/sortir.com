document.addEventListener('DOMContentLoaded', () => {
    const openBtn = document.getElementById('openProfile');
    const closeBtn = document.getElementById('closeProfile');
    const drawer = document.getElementById('profileDrawer');

    if (!openBtn || !drawer) return;

    const closeDrawer = () => {
        drawer.classList.remove('open');
    }

    const openDrawer = () => {
        drawer.classList.add('open');
    }

    openBtn.addEventListener('click', () => {
       openDrawer();
    });

    closeBtn.addEventListener('click', () => {
        closeDrawer();
    });

    document.addEventListener('click', (e) => {
        const isClickInsideDrawer = drawer.contains(e.target);
        const isClickOnOpenBtn = openBtn.contains(e.target);
        const isClickOnCloseBtn = closeBtn && closeBtn.contains(e.target);

        if (!isClickInsideDrawer && !isClickOnOpenBtn && !isClickOnCloseBtn) {
            closeDrawer();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeDrawer();
        }
    });
});
