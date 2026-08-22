/* =========================================================
   kharchaPatra - script.js
   Small helper functions for opening/closing modal popups.
   No frameworks used - plain JavaScript so it's easy to follow.
   ========================================================= */

// Opens a modal by its id, e.g. openModal('addIncomeModal')
function openModal(id) {
  document.getElementById(id).classList.add('open');
}

// Closes a modal by its id
function closeModal(id) {
  document.getElementById(id).classList.remove('open');
}

// Close a modal if the user clicks the dark overlay outside the box
document.addEventListener('click', function (e) {
  if (e.target.classList.contains('modal-overlay')) {
    e.target.classList.remove('open');
  }
});

// Ask for confirmation before deleting a row
function confirmDelete(message) {
  return confirm(message || 'Are you sure you want to delete this item?');
}