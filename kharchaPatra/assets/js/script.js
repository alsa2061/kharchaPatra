/**
 * kharchaPatra - shared front-end behavior
 */

// Close any open modal when clicking outside its box
document.addEventListener('click', function (e) {
    document.querySelectorAll('.modal-overlay.open').forEach(function (overlay) {
        if (e.target === overlay) {
            overlay.classList.remove('open');
        }
    });
});

// Close modal with Escape key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.open').forEach(function (overlay) {
            overlay.classList.remove('open');
        });
    }
});

// Basic client-side check: amount fields must be greater than 0
document.addEventListener('submit', function (e) {
    const amountField = e.target.querySelector('input[type="number"][name*="amount"]');
    if (amountField && parseFloat(amountField.value) <= 0) {
        alert('Amount must be greater than 0.');
        e.preventDefault();
    }
});