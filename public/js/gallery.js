/**
 * Science Food Festival - Clean Smart Skeleton Gallery & Filter Engine
 * Handles priority image load, skeleton shimmer, and instant chip filtering.
 */

document.addEventListener('DOMContentLoaded', () => {
  initSmartGallery();
  initGalleryFilters();
});

function initSmartGallery() {
  const lazyImages = document.querySelectorAll('img.img-smart');
  
  const handleImageLoad = (img) => {
    if (img.classList.contains('loaded')) return;

    const markLoaded = () => {
      img.classList.add('loaded');
      const container = img.closest('.skeleton-container');
      if (container) {
        container.classList.add('loaded');
      }
    };

    if ('requestIdleCallback' in window) {
      window.requestIdleCallback(markLoaded, { timeout: 300 });
    } else {
      requestAnimationFrame(markLoaded);
    }
  };

  if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          
          if (img.dataset.src) {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
          }

          if (img.complete) {
            handleImageLoad(img);
          } else {
            img.addEventListener('load', () => handleImageLoad(img), { once: true });
            img.addEventListener('error', () => handleImageLoad(img), { once: true });
          }

          observer.unobserve(img);
        }
      });
    }, {
      rootMargin: '200px 0px',
      threshold: 0.01
    });

    lazyImages.forEach(img => {
      if (img.complete) {
        handleImageLoad(img);
      } else {
        imageObserver.observe(img);
      }
    });
  } else {
    lazyImages.forEach(img => {
      if (img.dataset.src) img.src = img.dataset.src;
      if (img.complete) {
        handleImageLoad(img);
      } else {
        img.addEventListener('load', () => handleImageLoad(img), { once: true });
      }
    });
  }

  const priorityImages = document.querySelectorAll('img.img-priority');
  priorityImages.forEach(img => {
    if (img.complete) {
      img.classList.add('loaded');
    } else {
      img.addEventListener('load', () => img.classList.add('loaded'), { once: true });
    }
  });
}

/**
 * Filter Chips Logic
 */
function initGalleryFilters() {
  const filterBtns = document.querySelectorAll('[data-filter]');
  const galleryItems = document.querySelectorAll('.gallery-card');

  if (!filterBtns.length || !galleryItems.length) return;

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const selectedFilter = btn.getAttribute('data-filter').toLowerCase();

      // Update button styling active state
      filterBtns.forEach(b => {
        if (b === btn) {
          b.className = "px-5 py-2 bg-brand-teal text-white rounded-full text-sm font-semibold whitespace-nowrap shadow-sm cursor-pointer border border-brand-teal";
        } else {
          b.className = "px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full text-sm font-semibold whitespace-nowrap transition-colors cursor-pointer border border-transparent";
        }
      });

      // Filter gallery cards
      galleryItems.forEach(item => {
        const itemCategory = item.getAttribute('data-category');

        if (selectedFilter === 'all' || itemCategory === selectedFilter) {
          item.classList.remove('hidden');
          item.style.display = '';
        } else {
          item.classList.add('hidden');
          item.style.display = 'none';
        }
      });
    });
  });
}

window.initSmartGallery = initSmartGallery;
