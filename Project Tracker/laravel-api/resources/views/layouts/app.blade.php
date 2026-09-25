<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        {{ config('app.name') }} &middot; @yield('title', 'Dashboard')
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    {{-- SIDEBAR --}}
    <aside class="sidebar" id="project-sidebar" aria-label="Main navigation">

        <a class="brand"
           href="{{ route('dashboard') }}"
           aria-label="Project Tracker home">

            <img
                src="{{ asset('assets/logo/kedebah-logo.png') }}"
                alt="Project Tracker"
            >

        </a>

        <nav>

            {{-- DASHBOARD --}}
            <a href="{{ route('dashboard') }}"
               class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">

                <i class="nav-icon ri-home-line" aria-hidden="true"></i>

                Dashboard

            </a>


            {{-- ADMIN NAVIGATION --}}
            @if(auth()->check() && auth()->user()->role === 'admin')

                <a href="{{ route('admin.dashboard') }}"
                   class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">

                    <i class="nav-icon ri-admin-line" aria-hidden="true"></i>

                    Admin Dashboard

                </a>

                <a href="{{ route('admin.users.index') }}"
                   class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">

                    <i class="nav-icon ri-group-line" aria-hidden="true"></i>

                    User Management

                </a>

            @endif


            @php
                // "Project Details" opens the first project THIS person can actually open.
                $authUser = auth()->user();

                $firstProjectId = null;

                if ($authUser) {
                    $firstProjectId = \App\Models\Project::query()
                        ->when($authUser->role !== 'admin', function ($query) use ($authUser) {
                            $query->where(function ($q) use ($authUser) {
                                $q->where('owner_id', $authUser->id)
                                  ->orWhereHas('members', fn ($m) => $m->where('users.id', $authUser->id));
                            });
                        })
                        ->orderBy('id')
                        ->value('id');
                }

                $groups = [
                    'projects' => request()->is('projects/*'),
                    'initiation' => request()->is('initiation/*'),
                    'agile' => request()->is('agile/*'),
                    'tasks' => request()->is('tasks*'),
                    'resources' => request()->is('resources/*'),
                    'quality' => request()->is('quality/*'),
                    'reports' => request()->is('reports/*'),
                ];
            @endphp


            {{-- PROJECTS --}}
            <div class="sidebar-group">

                <button
                    class="sidebar-group-toggle {{ $groups['projects'] ? 'active' : '' }}"
                    type="button"
                    data-sidebar-group-toggle
                    aria-controls="sidebar-projects"
                    aria-expanded="{{ $groups['projects'] ? 'true' : 'false' }}"
                >

                    <i class="nav-icon ri-folder-line" aria-hidden="true"></i>

                    Projects

                    <span class="sidebar-group-chevron" aria-hidden="true"></span>

                </button>

                <div
                    class="sidebar-submenu"
                    id="sidebar-projects"
                    @if (! $groups['projects']) hidden @endif
                >

                    <a href="{{ route('projects.index') }}"
                       class="{{ request()->routeIs('projects.index') ? 'active' : '' }}">

                        Projects List

                    </a>

                    @if (\App\ProjectAccess::canCreate(auth()->user()))
                    <a href="{{ route('projects.create') }}"
                       class="{{ request()->routeIs('projects.create') ? 'active' : '' }}">

                        Create Project

                    </a>
                    @endif

                    @if ($firstProjectId)

                        <a href="{{ route('projects.show', $firstProjectId) }}"
                           class="{{ request()->routeIs('projects.show', 'projects.edit') ? 'active' : '' }}">

                            Project Details

                        </a>

                    @endif

                </div>

            </div>


            {{-- INITIATION --}}
            <div class="sidebar-group">

                <button
                    class="sidebar-group-toggle {{ $groups['initiation'] ? 'active' : '' }}"
                    type="button"
                    data-sidebar-group-toggle
                    aria-controls="sidebar-initiation"
                    aria-expanded="{{ $groups['initiation'] ? 'true' : 'false' }}"
                >

                    <i class="nav-icon ri-rocket-line" aria-hidden="true"></i>

                    Initiation

                    <span class="sidebar-group-chevron" aria-hidden="true"></span>

                </button>

                <div
                    class="sidebar-submenu"
                    id="sidebar-initiation"
                    @if (! $groups['initiation']) hidden @endif
                >

                    <a href="{{ route('web.kickoff.index') }}"
                       class="{{ request()->is('initiation/kickoff*') ? 'active' : '' }}">

                        Kick-Off

                    </a>

                    <a href="{{ route('web.stakeholders.index') }}"
                       class="{{ request()->is('initiation/stakeholders*') ? 'active' : '' }}">

                        Stakeholders

                    </a>

                </div>

            </div>


            {{-- AGILE --}}
            <div class="sidebar-group">

                <button
                    class="sidebar-group-toggle {{ $groups['agile'] ? 'active' : '' }}"
                    type="button"
                    data-sidebar-group-toggle
                    aria-controls="sidebar-agile"
                    aria-expanded="{{ $groups['agile'] ? 'true' : 'false' }}"
                >

                    <i class="nav-icon ri-loop-left-line" aria-hidden="true"></i>

                    Agile

                    <span class="sidebar-group-chevron" aria-hidden="true"></span>

                </button>

                <div
                    class="sidebar-submenu"
                    id="sidebar-agile"
                    @if (! $groups['agile']) hidden @endif
                >

                    <a href="{{ route('web.sprints.index') }}"
                       class="{{ request()->is('agile/sprints*') ? 'active' : '' }}">

                        Sprints

                    </a>

                    <a href="{{ route('web.backlog.index') }}"
                       class="{{ request()->is('agile/backlog*') ? 'active' : '' }}">

                        Backlog

                    </a>

                    <a href="{{ route('web.definitions.index') }}"
                       class="{{ request()->is('agile/definitions*') ? 'active' : '' }}">

                        DoR / DoD

                    </a>

                </div>

            </div>


            {{-- TASKS --}}
            <div class="sidebar-group">

                <button
                    class="sidebar-group-toggle {{ $groups['tasks'] ? 'active' : '' }}"
                    type="button"
                    data-sidebar-group-toggle
                    aria-controls="sidebar-tasks"
                    aria-expanded="{{ $groups['tasks'] ? 'true' : 'false' }}"
                >

                    <i class="nav-icon ri-checkbox-circle-line" aria-hidden="true"></i>

                    Tasks

                    <span class="sidebar-group-chevron" aria-hidden="true"></span>

                </button>

                <div
                    class="sidebar-submenu"
                    id="sidebar-tasks"
                    @if (! $groups['tasks']) hidden @endif
                >

                    <a href="{{ route('web.tasks.index') }}"
                       class="{{ request()->is('tasks') || request()->is('tasks/create') || request()->is('tasks/*/edit') ? 'active' : '' }}">

                        Task List

                    </a>

                    <a href="{{ route('kanban') }}"
                       class="{{ request()->is('tasks/kanban') ? 'active' : '' }}">

                        Kanban Board

                    </a>

                    <a href="{{ route('web.workflows.index') }}"
                       class="{{ request()->is('tasks/workflows*') ? 'active' : '' }}">

                        Workflows

                    </a>

                </div>

            </div>


            {{-- RESOURCES --}}
            <div class="sidebar-group">

                <button
                    class="sidebar-group-toggle {{ $groups['resources'] ? 'active' : '' }}"
                    type="button"
                    data-sidebar-group-toggle
                    aria-controls="sidebar-resources"
                    aria-expanded="{{ $groups['resources'] ? 'true' : 'false' }}"
                >

                    <i class="nav-icon ri-team-line" aria-hidden="true"></i>

                    Resources

                    <span class="sidebar-group-chevron" aria-hidden="true"></span>

                </button>

                <div
                    class="sidebar-submenu"
                    id="sidebar-resources"
                    @if (! $groups['resources']) hidden @endif
                >

                    <a href="{{ route('web.team.index') }}"
                       class="{{ request()->is('resources/team*') ? 'active' : '' }}">

                        Team

                    </a>

                    <a href="{{ route('web.time.index') }}"
                       class="{{ request()->is('resources/time-tracking*') ? 'active' : '' }}">

                        Time Tracking

                    </a>

                    <a href="{{ route('web.budget.index') }}"
                       class="{{ request()->is('resources/budget*') ? 'active' : '' }}">

                        Budget

                    </a>

                    <a href="{{ route('web.milestones.index') }}"
                       class="{{ request()->is('resources/milestones*') ? 'active' : '' }}">

                        Milestones

                    </a>

                    <a href="{{ route('gantt') }}"
                       class="{{ request()->is('resources/gantt') ? 'active' : '' }}">

                        Gantt Chart

                    </a>

                </div>

            </div>


            {{-- QUALITY --}}
            <div class="sidebar-group">

                <button
                    class="sidebar-group-toggle {{ $groups['quality'] ? 'active' : '' }}"
                    type="button"
                    data-sidebar-group-toggle
                    aria-controls="sidebar-quality"
                    aria-expanded="{{ $groups['quality'] ? 'true' : 'false' }}"
                >

                    <i class="nav-icon ri-shield-check-line" aria-hidden="true"></i>

                    Quality

                    <span class="sidebar-group-chevron" aria-hidden="true"></span>

                </button>

                <div
                    class="sidebar-submenu"
                    id="sidebar-quality"
                    @if (! $groups['quality']) hidden @endif
                >

                    <a href="{{ route('web.testing.index') }}"
                       class="{{ request()->is('quality/qa-testing*') ? 'active' : '' }}">

                        QA &amp; Testing

                    </a>

                    <a href="{{ route('web.risks.index') }}"
                       class="{{ request()->is('quality/risks*') ? 'active' : '' }}">

                        Risks &amp; Issues

                    </a>

                    <a href="{{ route('web.changes.index') }}"
                       class="{{ request()->is('quality/change-log*') ? 'active' : '' }}">

                        Change Log

                    </a>

                </div>

            </div>


            {{-- REPORTS --}}
            <div class="sidebar-group">

                <button
                    class="sidebar-group-toggle {{ $groups['reports'] ? 'active' : '' }}"
                    type="button"
                    data-sidebar-group-toggle
                    aria-controls="sidebar-reports"
                    aria-expanded="{{ $groups['reports'] ? 'true' : 'false' }}"
                >

                    <i class="nav-icon ri-bar-chart-box-line" aria-hidden="true"></i>

                    Reports

                    <span class="sidebar-group-chevron" aria-hidden="true"></span>

                </button>

                <div
                    class="sidebar-submenu"
                    id="sidebar-reports"
                    @if (! $groups['reports']) hidden @endif
                >

                    <a href="{{ route('analytics') }}"
                       class="{{ request()->is('reports/analytics') ? 'active' : '' }}">

                        Analytics

                    </a>

                    <a href="{{ route('web.documents.index') }}"
                       class="{{ request()->is('reports/documents*') ? 'active' : '' }}">

                        Documents

                    </a>

                    <a href="{{ route('web.lessons.index') }}"
                       class="{{ request()->is('reports/lessons-learned*') ? 'active' : '' }}">

                        Lessons Learned

                    </a>

                </div>

            </div>


            {{-- CHAT --}}
            <a href="{{ route('chat') }}"
               class="{{ request()->is('chat*') ? 'active' : '' }}">

                <i class="nav-icon ri-chat-3-line" aria-hidden="true"></i>

                Project Chat

            </a>

        </nav>

    </aside>


    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>


    {{-- MAIN CONTENT --}}
    <main>

        {{-- HEADER --}}
        <header class="app-header">

            <a
                class="header-brand"
                href="{{ route('dashboard') }}"
                aria-label="Project Tracker home"
            >

                <img
                    src="{{ asset('assets/logo/kedebah-logo.png') }}"
                    alt="Project Tracker"
                >

            </a>


            <button
                id="sidebar-toggle"
                class="header-icon-button"
                type="button"
                aria-label="Toggle navigation"
                aria-controls="project-sidebar"
                aria-expanded="true"
            >

                <span class="hamburger" aria-hidden="true"></span>

            </button>


            {{-- SEARCH --}}
            <form
                class="header-search"
                action="{{ route('projects.index') }}"
                method="GET"
                role="search"
            >

                <label class="sr-only" for="global-search">
                    Search projects or tasks
                </label>

                <span class="search-mark" aria-hidden="true"></span>

                <input
                    id="global-search"
                    name="search"
                    placeholder="Search anything here ..."
                    type="search"
                    autocomplete="off"
                    data-search-url="{{ route('search.global') }}"
                >

                <div
                    id="global-search-results"
                    class="search-results"
                    hidden
                ></div>

            </form>


            <div class="header-actions">

                {{-- MOBILE SEARCH --}}
                <button
                    id="mobile-search-toggle"
                    class="header-icon-button mobile-only"
                    type="button"
                    aria-label="Open search"
                >

                    <svg viewBox="0 0 24 24" aria-hidden="true">

                        <circle cx="11" cy="11" r="6"></circle>

                        <path d="m16 16 4 4"></path>

                    </svg>

                </button>


                {{-- THEME --}}
                <button
                    id="theme-toggle"
                    class="header-icon-button"
                    type="button"
                    aria-label="Toggle color theme"
                    title="Toggle color theme"
                >

                    <svg viewBox="0 0 24 24" aria-hidden="true">

                        <path d="M20.4 15.6A8.6 8.6 0 0 1 8.4 3.6 8.7 8.7 0 1 0 20.4 15.6Z"></path>

                    </svg>

                </button>


                {{-- FULLSCREEN --}}
                <button
                    id="fullscreen-toggle"
                    class="header-icon-button desktop-action"
                    type="button"
                    aria-label="Toggle fullscreen"
                    title="Toggle fullscreen"
                >

                    <svg viewBox="0 0 24 24" aria-hidden="true">

                        <path d="M8 3H3v5M16 3h5v5M21 16v5h-5M3 16v5h5"></path>

                    </svg>

                </button>


                {{-- NOTIFICATIONS --}}
                <div class="header-popover-wrap">

                    <button
                        id="notifications-toggle"
                        class="header-icon-button bell-button"
                        type="button"
                        aria-label="Show notifications"
                        aria-expanded="false"
                    >

                        <svg viewBox="0 0 24 24" aria-hidden="true">

                            <path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"></path>

                        </svg>

                        <span class="notification-dot"></span>

                    </button>


                    <div
                        id="notifications-panel"
                        class="header-popover notifications-panel"
                        hidden
                    >

                        <strong>Notifications</strong>

                        @forelse(($headerNotifications ?? []) as $notification)

                            <a
                                class="notification-item"
                                href="{{ $notification['url'] ?? '#' }}"
                            >

                                <b>
                                    {{ $notification['title'] ?? 'Notification' }}
                                </b>

                                <span>
                                    {{ $notification['detail'] ?? '' }}
                                </span>

                            </a>

                        @empty

                            <p class="notification-empty">
                                No open risks, upcoming milestones, or pending tasks.
                            </p>

                        @endforelse

                    </div>

                </div>


                {{-- PROFILE --}}
                @php
                    $currentUser = auth()->user();

                    $userName = $currentUser?->name ?? 'User';

                    $userEmail = $currentUser?->email ?? '';

                    $userInitials = collect(
                        explode(' ', trim($userName))
                    )
                    ->filter()
                    ->map(
                        fn ($part) => strtoupper(
                            substr($part, 0, 1)
                        )
                    )
                    ->take(2)
                    ->implode('');
                @endphp


                <div class="header-popover-wrap">

                    <button
                        id="profile-toggle"
                        class="profile-button"
                        type="button"
                        aria-label="Open account menu"
                        aria-expanded="false"
                    >

                        <span
                            class="avatar workspace-avatar" style="overflow:hidden;"
                            aria-hidden="true"
                        >

                            @if (auth()->user()?->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="" style="width:100%;height:100%;object-fit:cover;display:block;">
                        @else
                            {{ $userInitials ?: 'U' }}
                        @endif

                        </span>


                        <span class="profile-copy">

                            <b>{{ $userName }}</b>

                            <small>{{ $userEmail }}</small>

                        </span>


                        <span
                            class="chevron"
                            aria-hidden="true"
                        ></span>

                    </button>


                    <div
                        id="profile-panel"
                        class="header-popover profile-panel"
                        hidden
                    >

                        <div class="profile-menu-heading">

                            <span
                                class="avatar workspace-avatar" style="overflow:hidden;"
                                aria-hidden="true"
                            >

                                @if (auth()->user()?->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="" style="width:100%;height:100%;object-fit:cover;display:block;">
                        @else
                            {{ $userInitials ?: 'U' }}
                        @endif

                            </span>


                            <div>

                                <strong>{{ $userName }}</strong>

                                <span>{{ $userEmail }}</span>

                            </div>

                        </div>


                        <a href="{{ route('profile.edit') }}">

                            My profile

                            <span>&rarr;</span>

                        </a>


                        <a href="{{ route('projects.index') }}">

                            My projects

                            <span>&rarr;</span>

                        </a>


                        <a href="{{ route('analytics') }}">

                            Reports

                            <span>&rarr;</span>

                        </a>


                        <form
                            method="POST"
                            action="{{ route('logout') }}"
                            style="margin:0;"
                        >

                            @csrf

                            <button
                                type="submit"
                                style="
                                    width:100%;
                                    display:flex;
                                    align-items:center;
                                    justify-content:space-between;
                                    padding:11px 14px;
                                    border:0;
                                    background:transparent;
                                    color:#dc2626;
                                    font:inherit;
                                    font-size:14px;
                                    cursor:pointer;
                                    text-align:left;
                                "
                            >

                                <span>Logout</span>

                                <span>&rarr;</span>

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </header>


        {{-- PAGE CONTENT --}}
        <section class="content">

            @if (session('success'))

                <div class="notice">

                    {{ session('success') }}

                </div>

            @endif


            @if ($errors->any())

                <div class="notice notice-error">

                    Please correct the highlighted fields and try again.

                </div>

            @endif


            @yield('content')

        </section>

    </main>


    {{-- SIDEBAR JAVASCRIPT --}}
    <script>

        (() => {

            const sidebar =
                document.getElementById('project-sidebar');

            const storageKey =
                'project-tracker-sidebar-scroll';


            const save = () => {

                if (
                    sidebar &&
                    window.innerWidth > 900
                ) {

                    sessionStorage.setItem(
                        storageKey,
                        String(sidebar.scrollTop)
                    );

                }

            };


            const restore = () => {

                const value =
                    sessionStorage.getItem(storageKey);

                if (
                    sidebar &&
                    window.innerWidth > 900 &&
                    value !== null
                ) {

                    sidebar.scrollTop =
                        parseInt(value, 10);

                }

            };


            document.addEventListener(
                'DOMContentLoaded',
                () => {

                    restore();


                    sidebar?.addEventListener(
                        'scroll',
                        save
                    );


                    sidebar
                        ?.querySelectorAll('a')
                        .forEach((link) => {

                            link.addEventListener(
                                'click',
                                save
                            );

                        });


                    sidebar
                        ?.querySelectorAll(
                            '[data-sidebar-group-toggle]'
                        )
                        .forEach((toggle) => {

                            toggle.addEventListener(
                                'click',
                                () => {

                                    const submenu =
                                        document.getElementById(
                                            toggle.getAttribute(
                                                'aria-controls'
                                            )
                                        );


                                    const willOpen =
                                        toggle.getAttribute(
                                            'aria-expanded'
                                        ) !== 'true';


                                    sidebar
                                        .querySelectorAll(
                                            '[data-sidebar-group-toggle]'
                                        )
                                        .forEach(
                                            (groupToggle) => {

                                                const groupMenu =
                                                    document.getElementById(
                                                        groupToggle.getAttribute(
                                                            'aria-controls'
                                                        )
                                                    );


                                                groupToggle.setAttribute(
                                                    'aria-expanded',
                                                    'false'
                                                );


                                                if (groupMenu) {

                                                    groupMenu.hidden = true;

                                                }

                                            }
                                        );


                                    toggle.setAttribute(
                                        'aria-expanded',
                                        String(willOpen)
                                    );


                                    if (submenu) {

                                        submenu.hidden =
                                            !willOpen;

                                    }

                                }
                            );

                        });

                }
            );

        })();

    </script>

</body>

</html>
