@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="text-uppercase small fw-semibold text-muted">Admin / Investment Interests</div>
            <h1 class="h3 mb-1">{{ $interest->name }}</h1>
            <p class="text-muted mb-0">
                Submitted {{ $interest->created_at?->format('M d, Y H:i') }}
            </p>
        </div>

        <a
            href="{{ route('admin.interests.index') }}"
            class="btn btn-outline-secondary"
        >
            Back to Interests
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h2 class="h5 mb-0">Stakeholder Details</h2>
                </div>

                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="small text-muted mb-1">Name</div>
                            <div class="fw-semibold">{{ $interest->name }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted mb-1">Company</div>
                            <div>{{ $interest->company ?: '—' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted mb-1">Email</div>
                            <div>
                                <a href="mailto:{{ $interest->email }}">
                                    {{ $interest->email }}
                                </a>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted mb-1">Phone</div>
                            <div>{{ $interest->phone ?: '—' }}</div>
                        </div>

                        <div class="col-12">
                            <div class="small text-muted mb-1">Project</div>
                            <div class="fw-semibold">
                                {{ $interest->project?->name ?: '—' }}
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="small text-muted mb-1">Message</div>
                            <div class="border rounded p-3 bg-light">
                                {!! nl2br(e($interest->message ?: 'No message provided.')) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h2 class="h5 mb-0">Manage Interest</h2>
                </div>

                <div class="card-body">
                    <form
                        method="POST"
                        action="{{ route('admin.interests.update', $interest) }}"
                    >
                        @csrf
                        @method('PATCH')

                        <label for="status" class="form-label">Status</label>

                        <select
                            id="status"
                            name="status"
                            class="form-select mb-3"
                        >
                            @foreach(['new', 'contacted', 'qualified', 'closed'] as $status)
                                <option
                                    value="{{ $status }}"
                                    @selected($interest->status === $status)
                                >
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>

                        <button type="submit" class="btn btn-primary w-100">
                            Update Status
                        </button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form
                        method="POST"
                        action="{{ route('admin.interests.destroy', $interest) }}"
                        onsubmit="return confirm('Delete this interest submission?');"
                    >
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-outline-danger w-100">
                            Delete Interest
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
