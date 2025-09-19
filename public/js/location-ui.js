document.addEventListener('DOMContentLoaded', () => {
  // Toggle collapse of location tables
  document.querySelectorAll('.toggle-loc').forEach(btn => {
    btn.addEventListener('click', () => {
      const targetId = btn.getAttribute('data-target');
      const panel = document.getElementById(targetId);
      if (!panel) return;
      const isHidden = panel.style.display === 'none';
      panel.style.display = isHidden ? '' : 'none';
      btn.textContent = isHidden ? 'Ocultar' : 'Mostrar';
    });
  });

  // Auto color status badges based on classes (in case we want JS enhancements later)
  const classMap = {
    'inv-badge-expired': '#dc3545',
    'inv-badge-soon': '#ffc107',
    'inv-badge-fresh': '#198754'
  };
  Object.entries(classMap).forEach(([klass, color]) => {
    document.querySelectorAll('.' + klass).forEach(el => {
      el.style.backgroundColor = color;
      el.style.color = (klass === 'inv-badge-soon') ? '#000' : '#fff';
    });
  });
});
