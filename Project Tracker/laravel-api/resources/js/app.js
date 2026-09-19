import ApexCharts from 'apexcharts';

document.addEventListener('DOMContentLoaded', () => {
    const button = document.querySelector('#theme-toggle');
    const dark = localStorage.getItem('project-tracker-theme') === 'dark';
    document.body.classList.toggle('dark', dark);
    button?.setAttribute('aria-pressed', String(dark));
    button?.addEventListener('click', () => {
        document.body.classList.toggle('dark');
        const isDark = document.body.classList.contains('dark');
        localStorage.setItem('project-tracker-theme', isDark ? 'dark' : 'light');
        button.setAttribute('aria-pressed', String(isDark));
    });

    const fullscreenButton = document.querySelector('#fullscreen-toggle');
    fullscreenButton?.addEventListener('click', async () => {
        try {
            if (document.fullscreenElement) await document.exitFullscreen();
            else await document.documentElement.requestFullscreen();
        } catch (_) {
            fullscreenButton.setAttribute('title', 'Fullscreen is unavailable in this browser view');
        }
    });

    const sidebarToggle = document.querySelector('#sidebar-toggle');
    const sidebarBackdrop = document.querySelector('#sidebar-backdrop');
    const sidebar = document.querySelector('#project-sidebar');
    const setSidebarOpen = (open) => {
        document.body.classList.toggle('sidebar-collapsed', !open);
        document.body.classList.toggle('sidebar-mobile-open', open && window.innerWidth <= 900);
        sidebarToggle?.setAttribute('aria-expanded', String(open));
    };
    sidebarToggle?.addEventListener('click', () => {
        const open = window.innerWidth <= 900
            ? !document.body.classList.contains('sidebar-mobile-open')
            : document.body.classList.contains('sidebar-collapsed');
        setSidebarOpen(open);
    });
    sidebarBackdrop?.addEventListener('click', () => setSidebarOpen(false));
    sidebar?.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => {
        if (window.innerWidth <= 900) setSidebarOpen(false);
    }));
    window.addEventListener('resize', () => {
        if (window.innerWidth > 900) document.body.classList.remove('sidebar-mobile-open');
    });

    const mobileSearchToggle = document.querySelector('#mobile-search-toggle');
    mobileSearchToggle?.addEventListener('click', () => {
        const search = document.querySelector('.header-search');
        search?.classList.toggle('mobile-search-open');
        if (search?.classList.contains('mobile-search-open')) search.querySelector('input')?.focus();
    });

    const closePopovers = (except) => document.querySelectorAll('.header-popover').forEach((panel) => {
        if (panel !== except) panel.hidden = true;
    });
    [['#notifications-toggle', '#notifications-panel'], ['#profile-toggle', '#profile-panel']].forEach(([buttonSelector, panelSelector]) => {
        const trigger = document.querySelector(buttonSelector);
        const panel = document.querySelector(panelSelector);
        trigger?.addEventListener('click', (event) => {
            event.stopPropagation();
            const willOpen = panel.hidden;
            closePopovers(panel);
            panel.hidden = !willOpen;
            trigger.setAttribute('aria-expanded', String(willOpen));
        });
    });
    document.addEventListener('click', () => closePopovers());
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closePopovers();
            clearSearchResults();
            if (window.innerWidth <= 900) setSidebarOpen(false);
        }
    });

    const searchInput = document.querySelector('#global-search');
    const searchResults = document.querySelector('#global-search-results');
    let searchTimer;
    const clearSearchResults = () => { if (searchResults) { searchResults.hidden = true; searchResults.replaceChildren(); } };
    searchInput?.addEventListener('input', () => {
        window.clearTimeout(searchTimer);
        const query = searchInput.value.trim();
        if (query.length < 2) return clearSearchResults();
        searchTimer = window.setTimeout(async () => {
            try {
                const response = await fetch(`${searchInput.dataset.searchUrl}?query=${encodeURIComponent(query)}`, { headers: { Accept: 'application/json' } });
                const { results } = await response.json();
                searchResults.replaceChildren();
                if (!results.length) {
                    const empty = document.createElement('p');
                    empty.className = 'search-results-empty';
                    empty.textContent = 'No matching pages, projects, or delivery records.';
                    searchResults.append(empty);
                } else {
                    const groups = results.reduce((current, result) => {
                        (current[result.category] ??= []).push(result);
                        return current;
                    }, {});
                    Object.entries(groups).forEach(([category, entries]) => {
                        const heading = document.createElement('p');
                        heading.className = 'search-results-heading';
                        heading.textContent = category;
                        searchResults.append(heading);
                        entries.forEach((result) => {
                        const link = document.createElement('a');
                        link.href = result.url;
                        link.innerHTML = `<span>${result.type}</span><b></b><small></small>`;
                        link.querySelector('b').textContent = result.title;
                        link.querySelector('small').textContent = result.meta;
                        searchResults.append(link);
                    });
                    });
                }
                searchResults.hidden = false;
            } catch (_) {
                clearSearchResults();
            }
        }, 220);
    });
    document.addEventListener('click', (event) => {
        if (!event.target.closest('.header-search')) clearSearchResults();
    });

    const tasksReportElement = document.querySelector('#tasks-report');
    const tasksReportData = document.querySelector('#tasks-report-data');
    if (tasksReportElement && tasksReportData && !tasksReportElement.dataset.rendered) {
        const taskActivity = JSON.parse(tasksReportData.textContent);
        const chart = new ApexCharts(tasksReportElement, {
            series: [
                { name: 'This Week', data: taskActivity.thisWeek },
                { name: 'Last Week', data: taskActivity.lastWeek },
            ],
            chart: {
                type: 'line',
                height: 340,
                toolbar: { show: false },
            },
            grid: { borderColor: '#f1f1f1', strokeDashArray: 3 },
            stroke: { width: 2, curve: 'smooth', dashArray: [0, 3] },
            colors: ['rgb(92, 103, 247)', 'rgb(227, 84, 212)'],
            dataLabels: { enabled: false },
            legend: { show: true, position: 'top' },
            tooltip: { enabled: true, theme: 'dark' },
            yaxis: {
                labels: {
                    formatter: (value) => Math.round(value).toString(),
                },
            },
            xaxis: {
                type: 'category',
                categories: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                axisBorder: { show: true, color: 'rgba(119, 119, 142, 0.05)' },
                axisTicks: { show: true, borderType: 'solid', color: 'rgba(119, 119, 142, 0.05)', width: 6 },
                labels: { rotate: -90 },
            },
            responsive: [{
                breakpoint: 580,
                options: {
                    chart: { height: 270 },
                    legend: { position: 'bottom' },
                },
            }],
        });
        tasksReportElement.dataset.rendered = 'true';
        chart.render();
    }
});
