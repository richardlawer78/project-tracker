document.addEventListener('DOMContentLoaded', () => {
    const button = document.querySelector('#theme-toggle');
    const dark = localStorage.getItem('project-tracker-theme') === 'dark';
    document.body.classList.toggle('dark', dark);
    button?.addEventListener('click', () => {
        document.body.classList.toggle('dark');
        localStorage.setItem('project-tracker-theme', document.body.classList.contains('dark') ? 'dark' : 'light');
    });
});
