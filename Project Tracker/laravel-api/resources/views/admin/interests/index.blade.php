@extends('layouts.app')

@section('title', 'Investment Interests')

@section('content')

<style>
    .interests-page {
        padding: 28px 34px 40px;
        color: #172033;
    }

    .interest-hero {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 24px;
        margin-bottom: 26px;
    }

    .interest-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #356dff;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        margin-bottom: 9px;
    }

    .interest-eyebrow::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #356dff;
        box-shadow: 0 0 0 4px rgba(53, 109, 255, .10);
    }

    .interest-hero h1 {
        margin: 0;
        font-size: 32px;
        line-height: 1.15;
        font-weight: 800;
        letter-spacing: -.03em;
    }

    .interest-hero p {
        margin: 8px 0 0;
        color: #64748b;
        font-size: 14px;
    }

    .interest-total {
        min-width: 150px;
        padding: 16px 18px;
        border: 1px solid rgba(226, 232, 240, .9);
        border-radius: 16px;
        background: rgba(255, 255, 255, .72);
        box-shadow: 0 10px 30px rgba(15, 23, 42, .06);
        text-align: right;
    }

    .interest-total span {
        display: block;
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 3px;
    }

    .interest-total strong {
        display: block;
        color: #172033;
        font-size: 26px;
        line-height: 1;
    }

    .interest-card {
        overflow: hidden;
        border: 1px solid rgba(226, 232, 240, .95);
        border-radius: 20px;
        background: rgba(255, 255, 255, .82);
        box-shadow: 0 16px 40px rgba(15, 23, 42, .07);
    }

    .interest-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 20px 22px;
        border-bottom: 1px solid #edf0f5;
    }

    .interest-card-title {
        margin: 0;
        font-size: 17px;
        font-weight: 800;
    }

    .interest-card-subtitle {
        margin: 4px 0 0;
        color: #64748b;
        font-size: 13px;
    }

    .interest-table-wrap {
        overflow-x: auto;
    }

    .interest-table {
        width: 100%;
        min-width: 900px;
        border-collapse: collapse;
    }

    .interest-table th {
        padding: 13px 22px;
        background: #f8fafc;
        border-bottom: 1px solid #edf0f5;
        color: #64748b;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .08em;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .interest-table td {
        padding: 17px 22px;
        border-bottom: 1px solid #f0f2f6;
        vertical-align: middle;
        font-size: 13px;
    }

    .interest-table tbody tr {
        transition: background .18s ease;
    }

    .interest-table tbody tr:hover {
        background: #f8fbff;
    }

    .stakeholder {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .stakeholder-avatar {
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        background: #edf3ff;
        color: #356dff;
        font-weight: 800;
        font-size: 13px;
    }

    .stakeholder-name {
        color: #172033;
        font-weight: 750;
    }

    .stakeholder-date {
        margin-top: 3px;
        color: #94a3b8;
        font-size: 11px;
    }

    .company-name {
        color: #334155;
        font-weight: 600;
    }

    .project-name {
        color: #356dff;
        font-weight: 650;
    }

    .contact-email {
        color: #334155;
    }

    .contact-phone {
        margin-top: 3px;
        color: #94a3b8;
        font-size: 11px;
    }

    .interest-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
    }

    .interest-status::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .status-new {
        background: #edf3ff;
        color: #356dff;
    }

    .status-new::before {
        background: #356dff;
    }

    .status-contacted {
        background: #fff7df;
        color: #a56b00;
    }

    .status-contacted::before {
        background: #e5a100;
    }

    .status-qualified {
        background: #eafaf1;
        color: #16824a;
    }

    .status-qualified::before {
        background: #22a861;
    }

    .status-closed {
        background: #f1f5f9;
        color: #64748b;
    }

    .status-closed::before {
        background: #94a3b8;
    }

    .interest-view {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 72px;
        padding: 7px 12px;
        border: 1px solid #dbe4f2;
        border-radius: 9px;
        background: #fff;
        color: #356dff;
        font-size: 12px;
        font-weight: 750;
        text-decoration: none;
        transition: all .18s ease;
    }

    .interest-view:hover {
        border-color: #356dff;
        background: #356dff;
        color: #fff;
        transform: translateY(-1px);
    }

    .interest-empty {
        padding: 65px 24px;
        text-align: center;
    }

    .interest-empty-icon {
        width: 54px;
        height: 54px;
        display: grid;
        place-items: center;
        margin: 0 auto 15px;
        border-radius: 16px;
        background: #edf3ff;
        color: #356dff;
        font-size: 22px;
    }

    .interest-empty h3 {
        margin: 0 0 7px;
        font-size: 16px;
        font-weight: 800;
    }

    .interest-empty p {
        max-width: 430px;
        margin: 0 auto;
        color: #64748b;
        font-size: 13px;
    }

    .interest-pagination {
        padding: 18px 22px;
        border-top: 1px solid #edf0f5;
    }

    @media (max-width: 768px) {
        .interests-page {
            padding: 22px 16px 30px;
        }

        .interest-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .interest-total {
            width: 100%;
            text-align: left;
        }
    }
</style>

<div class="interests-page">

    <div class="interest-hero">
        <div>
            <div class="interest-eyebrow">Stakeholder Management</div>
            <h1>Investment Interests</h1>
            <p>Review and manage enquiries submitted through your public project pages.</p>
        </div>

        <div class="interest-total">
            <span>Total submissions</span>
            <strong>{{ $interests->total() }}</strong>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="interest-card">

        <div class="interest-card-header">
            <div>
                <h2 class="interest-card-title">Submitted Interests</h2>
                <p class="interest-card-subtitle">
                    Stakeholders who have expressed interest in your projects.
                </p>
            </div>
        </div>

        <div class="interest-table-wrap">
            <table class="interest-table">
                <thead>
                    <tr>
                        <th>Stakeholder</th>
                        <th>Company</th>
                        <th>Project</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($interests as $interest)
                        @php
                            $initials = collect(preg_split('/\s+/', trim($interest->name)))
                                ->filter()
                                ->take(2)
                                ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                                ->implode('');

                            $statusClass = match($interest->status) {
                                'new' => 'status-new',
                                'contacted' => 'status-contacted',
                                'qualified' => 'status-qualified',
                                'closed' => 'status-closed',
                                default => 'status-closed',
                            };
                        @endphp

                        <tr>
                            <td>
                                <div class="stakeholder">
                                    <div class="stakeholder-avatar">
                                        {{ $initials ?: '?' }}
                                    </div>
                                    <div>
                                        <div class="stakeholder-name">{{ $interest->name }}</div>
                                        <div class="stakeholder-date">
                                            {{ $interest->created_at?->format('M d, Y · H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="company-name">
                                    {{ $interest->company ?: 'Independent' }}
                                </span>
                            </td>

                            <td>
                                <span class="project-name">
                                    {{ $interest->project?->name ?: '—' }}
                                </span>
                            </td>

                            <td>
                                <div class="contact-email">{{ $interest->email }}</div>

                                @if($interest->phone)
                                    <div class="contact-phone">{{ $interest->phone }}</div>
                                @endif
                            </td>

                            <td>
                                <span class="interest-status {{ $statusClass }}">
                                    {{ ucfirst($interest->status) }}
                                </span>
                            </td>

                            <td>
                                
                                    <a href="{{ route('admin.interests.show', $interest) }}"
                                    class="interest-view"
                                >
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="interest-empty">
                                    <div class="interest-empty-icon"><svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg></div>
                                    <h3>No investment interests yet</h3>
                                    <p>
                                        When someone submits an interest form from a public
                                        project page, their enquiry will appear here.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($interests->hasPages())
            <div class="interest-pagination">
                {{ $interests->links() }}
            </div>
        @endif

    </div>
</div>

@endsection

