// Theme handling for MediVault
// Provides a dark mode toggle persisted in localStorage and Alpine.js

document.addEventListener('alpine:init', () => {
  Alpine.store('theme', {
    dark: localStorage.getItem('theme') === 'dark',
    toggle() {
      this.dark = !this.dark;
      if (this.dark) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
      } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
      }
    },
    init() {
      // Ensure correct initial state on page load
      if (this.dark) {
        document.documentElement.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
      }
    }
  });
});

// Optional: expose a global toggle function
window.toggleTheme = () => {
  Alpine.store('theme').toggle();
};
