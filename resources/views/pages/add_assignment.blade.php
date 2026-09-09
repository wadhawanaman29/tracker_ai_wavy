@include('layouts.header')
@php
    use Carbon\Carbon;
@endphp



<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">{{ Route::currentRouteName() === 'editAssignment' ? 'Edit Project' : 'Add Project' }}</h4>

    <div class="row">
      <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-body">
               
                <form action="{{ Route::currentRouteName() === 'editAssignment' ? route('updateAssignment', ['id' => $id]) : route('storeAssignment') }}" method="post">
                    @csrf
                
                
                    <div class="form-group mb-3">
                        <label for="project_name" class="form-label">Project</label>
                     
                        <select name="project" id="project" class="form-control {{ $errors->has('project') ? 'is-invalid' : '' }}">
                            <option value="">Select</option>
                            @foreach ($projects as $item)
                            <option value="{{ $item->id }}" {{ isset($project_data) && $item->id == $project_data ? 'selected' : '' }}>
                                {{ $item->project_name }}
                            </option>
                            
                               
                            @endforeach
                        </select>
                        
                        @if ($errors->has('project'))
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong>{{ $errors->first('project') }}</strong>
                            </span>
                        @endif
                        

                    </div>

                    {{-- <div class="form-group mb-3">
                        <label for="project_date" class="form-label">Project Date</label>
                        <input type="date" class="form-control {{ $errors->has('project_date') ? 'is-invalid' : '' }}" 
                               id="project_date" 
                               name="project_date" 
                               value="{{ old('project_date', $id->project_date ?? '') }}" 
                               required >
                        <div id="nameHelp" class="form-text"></div>
                        @if ($errors->has('project_date'))
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong>{{ $errors->first('project_date') }}</strong>
                            </span>
                        @endif
                    </div>
                    

                    <div class="form-group mb-3">
                        <label for="project_time" class="form-label">Project Time</label>&nbsp;<span style="font-size: 12px;">(Time in hour)</span>
                        <input type="number" class="form-control {{ $errors->has('project_time') ? 'is-invalid' : '' }}" id="project_time" name="project_time" value="{{ old('project_time', $id->project_time ?? '') }}" autofocus placeholder="Time" aria-describedby="nameHelp" pattern="[0-9]{1}">
                        <div id="nameHelp" class="form-text"></div>
                        @if ($errors->has('project_time'))
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong>{{ $errors->first('project_time') }}</strong>
                            </span>
                        @endif
                    </div> --}}


                    <div class="form-group mb-3">
                        <label for="project_date" class="form-label">Project Date</label>
                        <input type="date" class="form-control {{ $errors->has('project_date') ? 'is-invalid' : '' }}" 
                        id="project_date" 
                        name="project_date" 
                        value="{{ old('project_date', isset($id->project_date) ? Carbon::parse($id->project_date)->format('Y-m-d') : date('Y-m-d')) }}" 
                        max="<?php echo date('Y-m-d'); ?>" required>
                 
                        <div id="nameHelp" class="form-text"></div>
                        @if ($errors->has('project_date'))
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong>{{ $errors->first('project_date') }}</strong>
                            </span>
                        @endif
                    </div>

                    {{-- value="{{ old('project_date', ('Y-m-d',strtotime($id->project_date)) ?? date('Y-m-d')) }}"  --}}
                    

                    {{-- <div class="form-group mb-3">
                        <label for="project_time" class="form-label">Project Time</label>&nbsp;<span style="font-size: 12px;">(Time in hour)</span>
                        <input type="text" class="form-control {{ $errors->has('project_time') ? 'is-invalid' : '' }}" id="project_time" name="project_time" value="{{ old('project_time', $id->project_time ?? '') }}" autofocus placeholder="Time" aria-describedby="nameHelp" pattern="[0-9]{1}">
                        <div id="nameHelp" class="form-text"></div>
                        @if ($errors->has('project_time'))
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong>{{ $errors->first('project_time') }}</strong>
                            </span>
                        @endif
                    </div> --}}

                  <div class="form-group mb-3 d-flex">
                        <label for="project_time" class="form-label">Project Time</label>&nbsp;
                        <div style="width: 50%;">
                            <select id="hours" class="form-select {{ $errors->has('hours') ? 'is-invalid' : '' }}" name="hours" aria-describedby="hoursHelp" required>
                                <option value="">Select Hours</option>
                                <option value="0" {{ old('hours') == '0' ? 'selected' : '' }}>0</option>
                                <option value="1" {{ old('hours') == '1' ? 'selected' : '' }}>1</option>
                                <option value="2" {{ old('hours') == '2' ? 'selected' : '' }}>2</option>
                                <option value="3" {{ old('hours') == '3' ? 'selected' : '' }}>3</option>
                                <option value="4" {{ old('hours') == '4' ? 'selected' : '' }}>4</option>
                                <option value="5" {{ old('hours') == '5' ? 'selected' : '' }}>5</option>
                                <option value="6" {{ old('hours') == '6' ? 'selected' : '' }}>6</option>
                                <option value="7" {{ old('hours') == '7' ? 'selected' : '' }}>7</option>
                                <option value="8" {{ old('hours') == '8' ? 'selected' : '' }}>8</option>
                                <option value="9" {{ old('hours') == '9' ? 'selected' : '' }}>9</option>
                            </select>
                            
                        </div>&nbsp;
                        <div style="width: 50%;">
                            <select id="minutes" class="form-select {{ $errors->has('minutes') ? 'is-invalid' : '' }}" name="minutes" aria-describedby="minutesHelp">
                                <option value="">Select Minutes</option>
                                {{-- <option value="0" selected style="display: none;">0</option> --}}
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                                <option value="25">25</option>
                                <option value="30">30</option>
                                <option value="35">35</option>
                                <option value="40">40</option>
                                <option value="45">45</option>
                                <option value="50">50</option>
                                <option value="55">55</option>
                            </select>
                        </div>
                    </div>
                    <div id="nameHelp" class="form-text"></div>
                    @if ($errors->has('project_time'))
                        <span class="invalid-feedback text-danger" role="alert">
                            <strong>{{ $errors->first('project_time') }}</strong>
                        </span>
                    @endif 
                    
                    
                    
                
                    <div class="form-group mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea name="comment" id="comment" cols="80" rows="20" class="form-control {{ $errors->has('comment') ? 'is-invalid' : '' }}" placeholder="Enter comment here..."autofocus>{{ old('comment', $id->comment ?? '') }}</textarea>
                        <div id="emailHelp" class="form-text"></div>
                        @if ($errors->has('comment'))
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong>{{ $errors->first('comment') }}</strong>
                            </span>
                        @endif
                    </div>
                
                   
                
                    <div class="form-group">
                        <input class="btn btn-outline-primary" type="submit" value="Submit">
                    </div>
                </form>
                
                
                
            </div>
        </div>
      </div>
     
    </div>
  </div>

@include('layouts.footer')

<script>
    // document.getElementById('project_time').addEventListener('input', function (e) {
    //     var value = e.target.value;
    //     if (!/^\d$/.test(value)) {
    //         e.target.value = value.slice(0, -1);
    //     }
    // });
    </script>

<script src="https://cdn.ckeditor.com/ckeditor5/34.2.0/classic/ckeditor.js"></script>

<script>
    ClassicEditor
        .create(document.querySelector('#comment'), {
            toolbar: {
                items: [
                    'heading',
                    '|',
                    'bold',
                    'italic',
                    '|',
                    'bulletedList',
                    'numberedList',
                    '|',
                    'link',
                    '|',
                    'undo',
                    'redo'
                ]
            },
            language: 'en',
            image: {
                toolbar: [
                    'imageTextAlternative',
                    '|',
                    'imageStyle:inline',
                    'imageStyle:block',
                    'imageStyle:side'
                ]
            },
            table: {
                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
            }
        })
        .catch(error => {
            console.error(error);
        });
</script>

{{-- <script>
    // Get today's date
    var today = new Date();

    // Set the maximum date to today
    document.getElementById("project_date").setAttribute("max", formatDate(today));

    // Calculate the date of one week ago
    var oneWeekAgo = new Date(today);
    oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);

    // Set the minimum date to one week ago
    document.getElementById("project_date").setAttribute("min", formatDate(oneWeekAgo));

    function formatDate(date) {
        var month = '' + (date.getMonth() + 1);
        var day = '' + date.getDate();
        var year = date.getFullYear();
        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;
        return [year, month, day].join('-');
    }
</script> --}}
    
