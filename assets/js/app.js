document.addEventListener('click', function (e) {
  const el = e.target;
  if (el && el.matches('[data-confirm]')) {
    const msg = el.getAttribute('data-confirm') || 'Tem certeza?';
    if (!confirm(msg)) {
      e.preventDefault();
    }
  }
});

window.addEventListener('load', function () {
  const alertBox = document.querySelector('[data-autohide="true"]');
  if (alertBox) {
    setTimeout(() => {
      alertBox.style.display = 'none';
    }, 3500);
  }
});