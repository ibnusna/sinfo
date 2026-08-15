/**
 * Science Food Festival - Academic Year Auto Detection Engine
 * Logika Indonesia: Bulan Juli adalah awal tahun ajaran baru.
 */

/**
 * Calculates current active academic year
 * @param {Date} [date=new Date()] Optional Date object
 * @returns {{startYear: number, endYear: number, formatted: string, formattedShort: string}}
 */
function getAcademicYear(date = new Date()) {
  const targetDate = date instanceof Date && !isNaN(date) ? date : new Date();
  const year = targetDate.getFullYear();
  const month = targetDate.getMonth(); // 0 = Jan, 6 = Jul, 11 = Dec

  // July (month >= 6) marks the start of new academic year
  const startYear = month >= 6 ? year : year - 1;
  const endYear = startYear + 1;

  return {
    startYear,
    endYear,
    formatted: `${startYear} / ${endYear}`,
    formattedShort: `${startYear}/${endYear}`
  };
}

// Auto update DOM elements when document is loaded
document.addEventListener('DOMContentLoaded', () => {
  const currentAY = getAcademicYear();

  // Update elements with data-academic-year attribute
  document.querySelectorAll('[data-academic-year]').forEach(el => {
    const format = el.getAttribute('data-academic-year-format');
    if (format === 'short') {
      el.textContent = currentAY.formattedShort;
    } else if (format === 'start') {
      el.textContent = currentAY.startYear;
    } else if (format === 'end') {
      el.textContent = currentAY.endYear;
    } else {
      el.textContent = currentAY.formatted;
    }
  });

  // Update dynamic timeline highlight in Annual Program section
  const timelineContainer = document.getElementById('academic-timeline');
  if (timelineContainer) {
    const cards = timelineContainer.querySelectorAll('.timeline-card');
    cards.forEach(card => {
      const yearVal = parseInt(card.getAttribute('data-year'), 10);
      if (yearVal === currentAY.startYear) {
        card.className = "timeline-card bg-brand-teal text-white w-full md:w-52 p-6 rounded-2xl shadow-lg border border-brand-teal scale-105 transition-all duration-300";
        const badge = card.querySelector('.timeline-status');
        if (badge) badge.textContent = "Tahun Ajaran Aktif";
      } else if (yearVal < currentAY.startYear) {
        card.className = "timeline-card bg-white text-brand-dark w-full md:w-48 p-6 rounded-2xl shadow-card border border-gray-100 opacity-90 transition-all duration-300";
        const badge = card.querySelector('.timeline-status');
        if (badge) badge.textContent = "Previous Edition";
      } else {
        card.className = "timeline-card bg-brand-light text-gray-400 w-full md:w-48 p-6 rounded-2xl border border-dashed border-gray-300 transition-all duration-300";
        const badge = card.querySelector('.timeline-status');
        if (badge) badge.textContent = "Upcoming Chapter";
      }
    });
  }
});

// Expose globally
window.getAcademicYear = getAcademicYear;
