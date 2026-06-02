const openFilters = () => {
  const triggers = document.querySelectorAll('.apply-filter');
  const filters = document.querySelectorAll('.filter-chiose__items');

  // Toggle active state on button click
  triggers.forEach(trigger => {
    trigger.addEventListener('click', (e) => {
      const targetId = trigger.getAttribute('data-target');
      const targetDiv = document.getElementById(targetId);

      // Toggle active class for the specific target div
      if (targetDiv.classList.contains('active')) {
        targetDiv.classList.remove('active');
      } else {
        // Remove active from other filters
        filters.forEach(filter => filter.classList.remove('active'));
        targetDiv.classList.add('active');
      }

      e.stopPropagation(); // Prevent click propagation
    });
  });

  // Remove active class when clicking outside any active div
  document.addEventListener('click', (e) => {
    filters.forEach(filter => {
      if (filter.classList.contains('active') && !filter.contains(e.target)) {
        filter.classList.remove('active');
      }
    });
  });
};

// Initialize the function
openFilters();
