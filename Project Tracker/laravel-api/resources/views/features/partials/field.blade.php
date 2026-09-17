@php
    $value = old($field, $item->{$field} ?? '');
    if ($field === 'stages' && is_array($value)) {
        $value = implode(', ', $value);
    }
    if ($value instanceof \DateTimeInterface) {
        $value = $value->format('Y-m-d');
    }
@endphp

@if ($field === 'project_id')
    <label>Project
        <select name="project_id">
            <option value="">Select project</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}" @selected((string) old('project_id', $item->project_id) === (string) $project->id)>{{ $project->name }}</option>
            @endforeach
        </select>
        @error('project_id')<small class="error">{{ $message }}</small>@enderror
    </label>
@elseif (in_array($field, ['user_id', 'assigned_to', 'owner_id', 'requestor_id']))
    <label>{{ Str::headline($field) }}
        <select name="{{ $field }}">
            <option value="">Select person</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected((string) old($field, $item->{$field}) === (string) $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
        @error($field)<small class="error">{{ $message }}</small>@enderror
    </label>
@elseif ($field === 'task_id')
    <label>Task
        <select name="task_id">
            <option value="">Optional task</option>
            @foreach ($tasks as $task)
                <option value="{{ $task->id }}" @selected((string) old('task_id', $item->task_id) === (string) $task->id)>{{ $task->title }}</option>
            @endforeach
        </select>
        @error('task_id')<small class="error">{{ $message }}</small>@enderror
    </label>
@elseif ($field === 'description' || $field === 'goal' || $field === 'mitigation' || $field === 'text')
    <label class="wide">{{ Str::headline($field) }}
        <textarea name="{{ $field }}" rows="4">{{ $value }}</textarea>
        @error($field)<small class="error">{{ $message }}</small>@enderror
    </label>
@elseif ($field === 'checked')
    <label>Completed
        <select name="checked">
            <option value="0" @selected(! old('checked', $item->checked))>No</option>
            <option value="1" @selected(old('checked', $item->checked))>Yes</option>
        </select>
    </label>
@elseif ($field === 'status')
    <label>Status
        <select name="status">
            @foreach (\App\Support\TrackerFields::options($feature, 'status') as $option)
                <option value="{{ $option }}" @selected((string) old('status', $item->status ?: $option) === $option)>{{ Str::headline($option) }}</option>
            @endforeach
        </select>
        @error('status')<small class="error">{{ $message }}</small>@enderror
    </label>
@elseif (in_array($field, ['priority', 'influence', 'interest', 'type', 'list_type', 'category', 'impact']))
    <label>{{ Str::headline($field) }}
        <select name="{{ $field }}">
            @foreach (\App\Support\TrackerFields::options($feature, $field) as $option)
                <option value="{{ $option }}" @selected((string) old($field, $item->{$field}) === $option)>{{ Str::headline($option) }}</option>
            @endforeach
        </select>
        @error($field)<small class="error">{{ $message }}</small>@enderror
    </label>
@elseif (str_contains($field, 'date') || in_array($field, ['last_run', 'date', 'due_date', 'start_date', 'end_date']))
    <label>{{ Str::headline($field) }}
        <input type="date" name="{{ $field }}" value="{{ $value }}">
        @error($field)<small class="error">{{ $message }}</small>@enderror
    </label>
@elseif (in_array($field, ['attendees', 'points', 'hours', 'allocated', 'spent', 'allocation_percent']))
    <label>{{ Str::headline($field) }}
        <input type="number" step="0.01" min="0" name="{{ $field }}" value="{{ $value }}">
        @error($field)<small class="error">{{ $message }}</small>@enderror
    </label>
@else
    <label class="{{ in_array($field, ['title', 'name', 'stages']) ? 'wide' : '' }}">{{ Str::headline($field) }}
        <input name="{{ $field }}" value="{{ $value }}" @if($field === 'stages') placeholder="To do, In progress, Done" @endif>
        @error($field)<small class="error">{{ $message }}</small>@enderror
    </label>
@endif
