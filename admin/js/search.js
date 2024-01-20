function addSearchFunctionality(searchInputId, clearButtonSelector, pageUrl) {
  const searchInput = document.getElementById(searchInputId);
  const clearButton = document.querySelector(clearButtonSelector);

  // Add event listener to clear button to clear search input
  clearButton.addEventListener('click', () => {
    searchInput.value = '';
    searchInput.focus();
    window.location.href = pageUrl;
  });

  // Add event listener to document to clear search input when ESC key is pressed
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      searchInput.value = '';
      searchInput.focus();
      window.location.href = pageUrl;
    }
  });
}
