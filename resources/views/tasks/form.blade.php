@include('layouts.header')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Task Management /</span>
        {{ $task ? 'Edit Task' : 'New Task' }}
    </h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">

                    <form action="{{ $task ? route('assigned_task.update', $task->id) : route('assigned_task.store') }}"
                        method="post">
                        @csrf
                        @if ($task)
                            @method('PUT')
                        @endif

                        <div class="form-group mb-3">
                            <label for="project_id" class="form-label">Project</label>
                            <select name="project_id" id="project_id"
                                class="form-select {{ $errors->has('project_id') ? 'is-invalid' : '' }}">
                                <option value="">Select</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}"
                                        {{ old('project_id', $task->project_id ?? '') == $project->id ? 'selected' : '' }}>
                                        {{ $project->project_name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('project_id'))
                                <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('project_id') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group mb-3">
                            <label for="assigned_to" class="form-label">Assign To</label>
                            <select name="assigned_to" id="assigned_to"
                                class="form-select {{ $errors->has('assigned_to') ? 'is-invalid' : '' }}">
                                <option value="">Select Employee</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ old('assigned_to', $task->assigned_to ?? '') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('assigned_to'))
                                <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('assigned_to') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title"
                                class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
                                value="{{ old('title', $task->title ?? '') }}" placeholder="Task title">
                            @if ($errors->has('title'))
                                <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('title') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group mb-3">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" name="due_date" id="due_date"
                                class="form-control {{ $errors->has('due_date') ? 'is-invalid' : '' }}"
                                value="{{ old('due_date', isset($task->due_date) ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : '') }}">
                            @if ($errors->has('due_date'))
                                <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('due_date') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" cols="80" rows="10"
                                class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
                                placeholder="Task details...">{{ old('description', $task->description ?? '') }}</textarea>
                            @if ($errors->has('description'))
                                <span class="invalid-feedback text-danger" role="alert">
                                    <strong>{{ $errors->first('description') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <input class="btn btn-outline-primary" type="submit"
                                value="{{ $task ? 'Update Task' : 'Create Task' }}">
                            <a href="{{ route('assigned_task_list') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')

<script src="https://cdn.ckeditor.com/ckeditor5/34.2.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#description'), {
            toolbar: {
                items: ['heading', '|', 'bold', 'italic', '|', 'bulletedList', 'numberedList', '|', 'link', '|', 'undo', 'redo']
            },
            language: 'en'
        })
        .catch(error => console.error(error));
</script>
