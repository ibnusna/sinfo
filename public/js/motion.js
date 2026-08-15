/**
 * Science Food Festival - Global Motion System & Parallax Scroll Engine
 * High Performance 60 FPS IntersectionObserver Reveal & rAF Parallax Driver
 */

document.addEventListener('DOMContentLoaded', () => {
  initScrollReveal();
  initParallaxEngine();
  initNavbarScrollEffect();
});

/**
 * Centralized IntersectionObserver for all reveal motion utility classes
 */
function initScrollReveal() {
  const revealSelectors = '.fade-up, .fade-down, .fade-left, .fade-right, .zoom-in, .blur-in';
  const revealElements = document.querySelectorAll(revealSelectors);

  if (!revealElements.length) return;

  const observerOptions = {
    root: null,
    rootMargin: '0px 0px -60px 0px',
    threshold: 0.12
  };

  const revealObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const el = entry.target;
        const delay = el.getAttribute('data-delay') || 0;

        if (delay > 0) {
          setTimeout(() => {
            el.classList.add('is-visible');
          }, parseInt(delay, 10));
        } else {
          el.classList.add('is-visible');
        }

        observer.unobserve(el);
      }
    });
  }, observerOptions);

  revealElements.forEach(el => revealObserver.observe(el));
}

/**
 * 60 FPS Passive Parallax Scroll Engine using requestAnimationFrame
 */
function initParallaxEngine() {
  const parallaxBackgrounds = document.querySelectorAll('.parallax-bg');
  const parallaxElements = document.querySelectorAll('.parallax-element');

  if (!parallaxBackgrounds.length && !parallaxElements.length) return;

  let latestScrollY = window.scrollY;
  let ticking = false;

  function updateParallax() {
    const scrollY = latestScrollY;

    // Hero background elements shift
    parallaxBackgrounds.forEach(bg => {
      const speed = parseFloat(bg.getAttribute('data-speed')) || 0.2;
      bg.style.transform = `translate3d(0, ${scrollY * speed}px, 0)`;
    });

    // Parallax interactive elements shift
    parallaxElements.forEach(el => {
      const speed = parseFloat(el.getAttribute('data-speed')) || 0.15;
      const rect = el.getBoundingClientRect();
      const inView = rect.top < window.innerHeight && rect.bottom > 0;
      if (inView) {
        const offset = (rect.top - window.innerHeight / 2) * speed;
        el.style.transform = `translate3d(0, ${offset}px, 0)`;
      }
    });

    ticking = false;
  }

  window.addEventListener('scroll', () => {
    latestScrollY = window.scrollY;
    if (!ticking) {
      requestAnimationFrame(updateParallax);
      ticking = true;
    }
  }, { passive: true });

  // Initial calculation
  updateParallax();
}

/**
 * Smooth Glassmorphism Navbar scroll states
 */
function initNavbarScrollEffect() {
  const navbar = document.querySelector('nav');
  if (!navbar) return;

  const handleScroll = () => {
    if (window.scrollY > 40) {
      navbar.classList.add('shadow-md', 'bg-white/95');
      navbar.classList.remove('bg-white/80');
    } else {
      navbar.classList.remove('shadow-md', 'bg-white/95');
      navbar.classList.add('bg-white/80');
    }
  };

  window.addEventListener('scroll', handleScroll, { passive: true });
  handleScroll();
}
