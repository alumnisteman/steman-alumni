// bootstrap-loader.js – ensures Bootstrap is loaded before any inline code runs
window.bootstrapReady = new Promise((resolve, reject) => {
  const script = document.createElement('script');
  script.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js';
  script.onload = () => {
    // Expose the bootstrap namespace for legacy inline code
    window.bootstrap = bootstrap;
    resolve();
  };
  script.onerror = reject;
  document.head.appendChild(script);
});
