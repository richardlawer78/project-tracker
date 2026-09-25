<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $project->name }} &middot; Project Report</title>
    <style>
        :root {
            --ink: #111827;
            --muted: #6b7280;
            --faint: #9ca3af;
            --line: #e5e7eb;
            --brand: #4338ca;
            --brand-soft: #eef2ff;
            --bg-soft: #f9fafb;
            --danger: #b91c1c;
            --danger-soft: #fef2f2;
            --success: #15803d;
            --success-soft: #f0fdf4;
            --warning: #b45309;
            --warning-soft: #fffbeb;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', -apple-system, Helvetica, Arial, sans-serif;
            color: var(--ink);
            background: #f3f4f6;
            font-size: 13.5px;
            line-height: 1.55;
        }

        .page {
            max-width: 880px;
            margin: 0 auto;
            background: #fff;
            padding: 48px 56px 40px;
        }

        .toolbar {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            max-width: 880px;
            margin: 16px auto 0;
            padding: 0 4px;
        }

        .toolbar button {
            border: 0;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .toolbar .btn-primary {
            background: var(--brand);
            color: #fff;
        }

        .toolbar .btn-secondary {
            background: #fff;
            color: var(--ink);
            border: 1px solid var(--line);
        }

        /* ---------- letterhead ---------- */

        .letterhead {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding-bottom: 22px;
            border-bottom: 3px solid var(--brand);
        }

        .letterhead .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .letterhead .brand img {
            height: 34px;
            width: auto;
        }

        .letterhead .brand-name {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .letterhead .doc-meta {
            text-align: right;
            color: var(--muted);
            font-size: 11.5px;
            line-height: 1.6;
        }

        .doc-title {
            margin-top: 26px;
        }

        .doc-title h1 {
            margin: 0 0 6px;
            font-size: 25px;
            font-weight: 800;
            letter-spacing: -0.01em;
        }

        .doc-title .subtitle {
            color: var(--muted);
            font-size: 13.5px;
        }

        .doc-title .subtitle span + span::before {
            content: '\00b7';
            margin: 0 8px;
            color: var(--faint);
        }

        /* ---------- badges ---------- */

        .badge {
            display: inline-block;
            padding: 3px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: capitalize;
            background: var(--bg-soft);
            color: var(--muted);
            border: 1px solid var(--line);
        }

        .badge-completed, .badge-closed, .badge-on-track, .badge-low, .badge-under {
            background: var(--success-soft); color: var(--success); border-color: #bbf7d0;
        }
        .badge-in-progress, .badge-mitigating, .badge-medium, .badge-at-risk {
            background: var(--warning-soft); color: var(--warning); border-color: #fde68a;
        }
        .badge-pending, .badge-upcoming, .badge-open {
            background: var(--brand-soft); color: var(--brand); border-color: #c7d2fe;
        }
        .badge-high, .badge-overdue, .badge-over {
            background: var(--danger-soft); color: var(--danger); border-color: #fecaca;
        }

        /* ---------- sections ---------- */

        section { margin-top: 34px; }

        .section-title {
            display: flex;
            align-items: baseline;
            gap: 10px;
            margin-bottom: 16px;
        }

        .section-title .num {
            font-size: 11px;
            font-weight: 700;
            color: #fff;
            background: var(--brand);
            width: 20px;
            height: 20px;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .section-title h2 {
            margin: 0;
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .section-title .rule {
            flex: 1;
            height: 1px;
            background: var(--line);
        }

        /* ---------- overview grid ---------- */

        .overview-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px 20px;
            margin: 0 0 4px;
        }

        .overview-grid dt {
            font-size: 10.5px;
            color: var(--faint);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }

        .overview-grid dd {
            margin: 0;
            font-weight: 700;
            font-size: 14px;
        }

        .description-block {
            margin-top: 18px;
            padding: 14px 16px;
            background: var(--bg-soft);
            border-left: 3px solid var(--line);
            border-radius: 0 6px 6px 0;
            color: #374151;
        }

        /* ---------- stat cards ---------- */

        .stat-cards {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
        }

        .stat-card {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 14px 10px;
            text-align: center;
        }

        .stat-card .value {
            font-size: 24px;
            font-weight: 800;
            line-height: 1;
        }

        .stat-card .label {
            margin-top: 6px;
            font-size: 10px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .stat-card.overdue .value { color: var(--danger); }
        .stat-card.completed .value { color: var(--success); }

        /* ---------- budget summary ---------- */

        .budget-summary {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 24px;
            align-items: center;
            padding: 18px 20px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: var(--bg-soft);
        }

        .budget-summary .figure .label {
            font-size: 10.5px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .budget-summary .figure .amount {
            margin-top: 4px;
            font-size: 19px;
            font-weight: 800;
        }

        .budget-summary .figure.spent.is-over .amount { color: var(--danger); }

        .progress-track {
            background: #e5e7eb;
            border-radius: 999px;
            height: 8px;
            overflow: hidden;
            margin-top: 8px;
        }

        .progress-fill {
            height: 100%;
            background: var(--brand);
        }

        .progress-fill.is-over { background: var(--danger); }
        .progress-fill.is-at-risk { background: var(--warning); }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            text-align: left;
            padding: 9px 10px;
            border-bottom: 1px solid var(--line);
            font-size: 12.5px;
        }

        thead th {
            color: var(--muted);
            font-weight: 700;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border-bottom: 2px solid var(--line);
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tfoot td {
            border-top: 2px solid var(--line);
            border-bottom: none;
            font-weight: 700;
        }

        .empty-note {
            color: var(--faint);
            font-style: italic;
            font-size: 12.5px;
            padding: 6px 0 2px;
        }

        .report-footer {
            margin-top: 46px;
            padding-top: 14px;
            border-top: 1px solid var(--line);
            color: var(--faint);
            font-size: 10.5px;
            text-align: center;
        }

        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .page { max-width: none; padding: 0; }
            section { break-inside: avoid; }
        }

        @media (max-width: 720px) {
            .page { padding: 28px 20px; }
            .overview-grid { grid-template-columns: repeat(2, 1fr); }
            .stat-cards { grid-template-columns: repeat(2, 1fr); }
            .budget-summary { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <button class="btn-secondary" type="button" onclick="window.close()">Close</button>
        <button class="btn-primary" type="button" onclick="window.print()">Print / Save as PDF</button>
    </div>

    <div class="page">

        <div class="letterhead">
            <div class="brand">
                @if (file_exists(public_path('assets/logo/kedebah-logo.png')))
                    <img src="{{ asset('assets/logo/kedebah-logo.png') }}" alt="{{ config('app.name') }}">
                @endif
                <span class="brand-name">{{ config('app.name') }}</span>
            </div>
            <div class="doc-meta">
                Project Report<br>
                Ref: PRJ-{{ str_pad($project->id, 4, '0', STR_PAD_LEFT) }}<br>
                Generated {{ $generatedAt->format('M j, Y \a\t g:i A') }}
            </div>
        </div>

        <div class="doc-title">
            <h1>{{ $project->name }}</h1>
            <div class="subtitle">
                <span>{{ $project->client ?: 'No client specified' }}</span>
                <span>{{ $project->team ?: 'Unassigned team' }}</span>
                <span>Owner: {{ $project->owner?->name ?? '—' }}</span>
            </div>
        </div>

        <section>
            <div class="section-title">
                <span class="num">1</span>
                <h2>Project Overview</h2>
                <span class="rule"></span>
            </div>

            <dl class="overview-grid">
                <div>
                    <dt>Status</dt>
                    <dd><span class="badge badge-{{ $project->status }}">{{ str_replace('-', ' ', $project->status) }}</span></dd>
                </div>
                <div>
                    <dt>Priority</dt>
                    <dd><span class="badge badge-{{ $project->priority }}">{{ $project->priority }}</span></dd>
                </div>
                <div>
                    <dt>Start Date</dt>
                    <dd>{{ $project->start_date?->format('M j, Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt>End Date</dt>
                    <dd>{{ $project->end_date?->format('M j, Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt>Overall Progress</dt>
                    <dd>
                        {{ $overallProgress }}%
                        <div class="progress-track">
                            <div class="progress-fill" style="width: {{ $overallProgress }}%"></div>
                        </div>
                    </dd>
                </div>
            </dl>
            <p class="empty-note" style="margin-top: 2px;">Calculated from completed vs. total tasks below.</p>

            <div class="description-block">
                {{ $project->description ?: 'No description provided.' }}
            </div>
        </section>

        <section>
            <div class="section-title">
                <span class="num">2</span>
                <h2>Task Summary</h2>
                <span class="rule"></span>
            </div>
            <div class="stat-cards">
                <div class="stat-card">
                    <div class="value">{{ $stats['total'] }}</div>
                    <div class="label">Total</div>
                </div>
                <div class="stat-card completed">
                    <div class="value">{{ $stats['completed'] }}</div>
                    <div class="label">Completed</div>
                </div>
                <div class="stat-card">
                    <div class="value">{{ $stats['in_progress'] }}</div>
                    <div class="label">In Progress</div>
                </div>
                <div class="stat-card">
                    <div class="value">{{ $stats['pending'] }}</div>
                    <div class="label">Pending</div>
                </div>
                <div class="stat-card overdue">
                    <div class="value">{{ $stats['overdue'] }}</div>
                    <div class="label">Overdue</div>
                </div>
            </div>
        </section>

        <section>
            <div class="section-title">
                <span class="num">3</span>
                <h2>Progress by Team Member</h2>
                <span class="rule"></span>
            </div>
            @if ($teamProgress->isEmpty())
                <p class="empty-note">No tasks have been assigned yet.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Tasks Assigned</th>
                            <th>Completed</th>
                            <th>Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($teamProgress as $row)
                            <tr>
                                <td>{{ $row['name'] }}</td>
                                <td>{{ str_replace('-', ' ', $row['role']) }}</td>
                                <td>{{ $row['total'] }}</td>
                                <td>{{ $row['completed'] }}</td>
                                <td>
                                    {{ $row['percent'] }}%
                                    <div class="progress-track" style="width: 80px; display: inline-block; vertical-align: middle; margin-left: 6px;">
                                        <div class="progress-fill" style="width: {{ $row['percent'] }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>

        <section>
            <div class="section-title">
                <span class="num">4</span>
                <h2>Budget</h2>
                <span class="rule"></span>
            </div>

            {{--
                Allocated / spent are computed automatically: when the project
                has line-item budget entries, their totals are used; otherwise
                the project's own budget/spent figures are shown. Either way
                the status badge and bar below are calculated, not typed in.
            --}}
            <div class="budget-summary">
                <div class="figure">
                    <div class="label">Allocated</div>
                    <div class="amount">${{ number_format($totalAllocated, 2) }}</div>
                </div>
                <div class="figure spent @if($budgetStatus === 'over') is-over @endif">
                    <div class="label">Spent</div>
                    <div class="amount">${{ number_format($totalSpent, 2) }}</div>
                </div>
                <div class="figure">
                    <div class="label">Utilization</div>
                    <div class="amount">{{ $percentUsed }}%</div>
                    <div class="progress-track">
                        <div class="progress-fill @if($budgetStatus === 'over') is-over @elseif($budgetStatus === 'at-risk') is-at-risk @endif"
                             style="width: {{ min(100, $percentUsed) }}%"></div>
                    </div>
                </div>
                <div class="figure">
                    <div class="label">Status</div>
                    <div class="amount">
                        @if ($budgetStatus === 'over')
                            <span class="badge badge-over">Over Budget</span>
                        @elseif ($budgetStatus === 'at-risk')
                            <span class="badge badge-at-risk">At Risk</span>
                        @elseif ($budgetStatus === 'no-budget')
                            <span class="badge">No Budget Set</span>
                        @else
                            <span class="badge badge-on-track">On Track</span>
                        @endif
                    </div>
                </div>
            </div>

            @if ($budgetItems->isEmpty())
                <p class="empty-note" style="margin-top: 10px;">
                    No budget line items have been entered for this project yet. Add them from the project's Budget page to see real figures here.
                </p>
            @endif

            @if ($budgetItems->isNotEmpty())
                <table style="margin-top: 18px;">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Allocated</th>
                            <th>Spent</th>
                            <th>Remaining</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($budgetItems as $item)
                            <tr>
                                <td>{{ $item->category }}</td>
                                <td>${{ number_format($item->allocated, 2) }}</td>
                                <td>${{ number_format($item->spent, 2) }}</td>
                                <td>${{ number_format($item->allocated - $item->spent, 2) }}</td>
                                <td><span class="badge badge-{{ $item->status }}">{{ str_replace('-', ' ', $item->status) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td>${{ number_format($totalAllocated, 2) }}</td>
                            <td>${{ number_format($totalSpent, 2) }}</td>
                            <td>${{ number_format($totalAllocated - $totalSpent, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            @endif
        </section>

        <section>
            <div class="section-title">
                <span class="num">5</span>
                <h2>Tasks ({{ $tasks->count() }})</h2>
                <span class="rule"></span>
            </div>
            @if ($tasks->isEmpty())
                <p class="empty-note">No tasks recorded for this project.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Assignee</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tasks as $task)
                            <tr>
                                <td>{{ $task->title }}</td>
                                <td>{{ $task->assignee?->name ?? 'Unassigned' }}</td>
                                <td><span class="badge badge-{{ $task->status }}">{{ str_replace('-', ' ', $task->status) }}</span></td>
                                <td><span class="badge badge-{{ $task->priority }}">{{ $task->priority }}</span></td>
                                <td>
                                    {{ $task->due_date?->format('M j, Y') ?? '—' }}
                                    @if ($task->status !== 'completed' && $task->due_date && $task->due_date->isPast())
                                        <span class="badge badge-overdue">overdue</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>

        <section>
            <div class="section-title">
                <span class="num">6</span>
                <h2>Team Members ({{ $members->count() }})</h2>
                <span class="rule"></span>
            </div>
            @if ($members->isEmpty())
                <p class="empty-note">No members assigned to this project.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($members as $member)
                            <tr>
                                <td>{{ $member->name }}</td>
                                <td>{{ $member->email }}</td>
                                <td>{{ str_replace('-', ' ', $member->pivot->role ?? '—') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>

        <section>
            <div class="section-title">
                <span class="num">7</span>
                <h2>Milestones ({{ $milestones->count() }})</h2>
                <span class="rule"></span>
            </div>
            @if ($milestones->isEmpty())
                <p class="empty-note">No milestones defined for this project.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Due Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($milestones as $milestone)
                            <tr>
                                <td>{{ $milestone->name }}</td>
                                <td>{{ $milestone->due_date?->format('M j, Y') ?? $milestone->date?->format('M j, Y') ?? '—' }}</td>
                                <td><span class="badge badge-{{ $milestone->status }}">{{ str_replace('-', ' ', $milestone->status) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>

        <section>
            <div class="section-title">
                <span class="num">8</span>
                <h2>Risks ({{ $risks->count() }})</h2>
                <span class="rule"></span>
            </div>
            @if ($risks->isEmpty())
                <p class="empty-note">No risks logged for this project.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Probability</th>
                            <th>Impact</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($risks as $risk)
                            <tr>
                                <td>{{ $risk->title }}</td>
                                <td>{{ ucfirst($risk->category) }}</td>
                                <td><span class="badge badge-{{ $risk->probability }}">{{ $risk->probability }}</span></td>
                                <td><span class="badge badge-{{ $risk->impact }}">{{ $risk->impact }}</span></td>
                                <td><span class="badge badge-{{ $risk->status }}">{{ $risk->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>

        <div class="report-footer">
            This report was generated automatically by {{ config('app.name') }} &middot; {{ $generatedAt->format('M j, Y \a\t g:i A') }}
        </div>

    </div>

</body>
</html>