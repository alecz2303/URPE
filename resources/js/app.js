import './bootstrap';

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const revealElements = document.querySelectorAll('[data-reveal]');
const header = document.querySelector('[data-motion="header"]');

if (reducedMotion) {
    revealElements.forEach((element) => element.classList.add('is-visible'));
} else if ('IntersectionObserver' in window) {
    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, {
        threshold: 0.16,
        rootMargin: '0px 0px -8% 0px',
    });

    revealElements.forEach((element) => revealObserver.observe(element));
} else {
    revealElements.forEach((element) => element.classList.add('is-visible'));
}

const updateHeader = () => {
    if (!header) return;
    header.classList.toggle('is-scrolled', window.scrollY > 18);
};

updateHeader();
window.addEventListener('scroll', updateHeader, { passive: true });

const pageLoader = document.getElementById('urpe-loader');

const dismissPageLoader = () => {
    if (!pageLoader) return;

    pageLoader.classList.add('is-leaving');
    window.setTimeout(() => pageLoader.remove(), reducedMotion ? 0 : 520);
};

if (document.readyState === 'complete') {
    dismissPageLoader();
} else {
    window.addEventListener('load', dismissPageLoader, { once: true });
}


const mobileMenu = document.querySelector('[data-mobile-menu]');

if (mobileMenu) {
    mobileMenu.querySelectorAll('a[href]').forEach((link) => {
        link.addEventListener('click', () => {
            mobileMenu.removeAttribute('open');
        });
    });

    document.addEventListener('click', (event) => {
        if (mobileMenu.open && !mobileMenu.contains(event.target)) {
            mobileMenu.removeAttribute('open');
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && mobileMenu.open) {
            mobileMenu.removeAttribute('open');
            mobileMenu.querySelector('summary')?.focus();
        }
    });
}


const backToTopButton = document.getElementById('urpe-back-to-top');
const pageFooter = document.querySelector('footer');

const updateBackToTop = () => {
    if (!backToTopButton) return;
    const nearTop = window.scrollY < 420;
    const footerVisible = pageFooter ? pageFooter.getBoundingClientRect().top < window.innerHeight : false;
    const shouldShow = !nearTop && !footerVisible;
    backToTopButton.classList.toggle('opacity-0', !shouldShow);
    backToTopButton.classList.toggle('translate-y-4', !shouldShow);
    backToTopButton.classList.toggle('pointer-events-none', !shouldShow);
    backToTopButton.classList.toggle('opacity-100', shouldShow);
    backToTopButton.classList.toggle('translate-y-0', shouldShow);
};

if (backToTopButton) {
    backToTopButton.addEventListener('click', () => window.scrollTo({ top: 0, behavior: reducedMotion ? 'auto' : 'smooth' }));
    updateBackToTop();
    window.addEventListener('scroll', updateBackToTop, { passive: true });
    window.addEventListener('resize', updateBackToTop, { passive: true });
}


const hineWizard = document.querySelector('[data-hine-wizard]');

if (hineWizard) {
    let currentStep = 0;
    const steps = [...hineWizard.querySelectorAll('[data-hine-step]')];
    const navigation = [...hineWizard.querySelectorAll('[data-hine-go]')];

    const renderHineStep = () => {
        steps.forEach((panel) => {
            panel.hidden = Number(panel.dataset.hineStep) !== currentStep;
        });

        navigation.forEach((button) => {
            const active = Number(button.dataset.hineGo) === currentStep;
            const activeClasses = (button.dataset.hineActiveClass || '').split(' ').filter(Boolean);
            const inactiveClasses = (button.dataset.hineInactiveClass || '').split(' ').filter(Boolean);
            button.classList.remove(...activeClasses, ...inactiveClasses);
            button.classList.add(...(active ? activeClasses : inactiveClasses));
            button.setAttribute('aria-current', active ? 'step' : 'false');
        });
    };

    const goToHineStep = (step) => {
        const requested = Number(step);
        if (!Number.isInteger(requested) || !steps.some((panel) => Number(panel.dataset.hineStep) === requested)) return;
        currentStep = requested;
        renderHineStep();
        window.scrollTo({ top: 0, behavior: reducedMotion ? 'auto' : 'smooth' });
    };

    navigation.forEach((button) => button.addEventListener('click', () => goToHineStep(button.dataset.hineGo)));
    hineWizard.querySelectorAll('[data-hine-prev]').forEach((button) => button.addEventListener('click', () => goToHineStep(currentStep - 1)));
    hineWizard.querySelectorAll('[data-hine-next]').forEach((button) => button.addEventListener('click', () => goToHineStep(currentStep + 1)));

    renderHineStep();
}


const hineScoreForm = document.querySelector('[data-hine-wizard]');

if (hineScoreForm) {
    const liveScore = hineScoreForm.querySelector('[data-hine-live-score]');
    const liveAsymmetries = hineScoreForm.querySelector('[data-hine-live-asymmetries]');
    const scoreLabel = hineScoreForm.querySelector('[data-hine-score-label]');
    const provisional = hineScoreForm.querySelector('[data-hine-provisional]');
    let dirty = false;

    const refreshHineSummary = () => {
        const checkedScores = [...hineScoreForm.querySelectorAll('input[type="radio"][name^="responses["][name$="[score]"]:checked')];
        const checkedAsymmetries = [...hineScoreForm.querySelectorAll('input[type="checkbox"][name^="responses["][name$="[asymmetry]"]:checked')];

        const total = checkedScores.reduce((sum, input) => sum + Number.parseFloat(input.value || '0'), 0);

        if (liveScore) liveScore.textContent = total.toFixed(1);
        if (liveAsymmetries) liveAsymmetries.textContent = String(checkedAsymmetries.length);

        if (dirty) {
            if (scoreLabel) scoreLabel.textContent = 'Puntuación provisional';
            provisional?.classList.remove('hidden');
        }
    };

    hineScoreForm.addEventListener('change', (event) => {
        if (!event.target.matches('input[type="radio"][name^="responses["][name$="[score]"], input[type="checkbox"][name^="responses["][name$="[asymmetry]"]')) return;
        dirty = true;
        refreshHineSummary();
    });

    refreshHineSummary();
}
