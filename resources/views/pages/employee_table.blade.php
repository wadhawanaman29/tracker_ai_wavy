<!-- resources/views/partials/employee_table.blade.php -->

<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Project</th>
            <th>Task&nbsp;Date</th>                           
            <th>Time</th>
            <th>Report</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="table-border-bottom-0">
        @if (!empty($data) && count($data) > 0)
            @foreach ($data as $index => $record)
                <tr>
                    <td>{{ $data->firstItem() + $index }}</td>
                    <td>{{ check_user_name($record->assigned_to) }}</td>
                    <td>{{ $record->project_name }}</td>
                    <td>{{ date('d/m/Y', strtotime($record->project_date)) }}</td>  
                    @php
                        $hours = intdiv($record->project_time, 60);
                        $minutes = $record->project_time % 60; 
                    @endphp
                    <td>{{ sprintf('%d:%02d', $hours, $minutes) }}</td>
                    <td> {!! $record->comment !!}</td>
                    <td>
                        <span class="view-reason" 

                        data-reason="<?php 
                                        $text = $record->comment;
                                        echo htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); 
                                    ?>"
                            data-reason="{{ str_replace('&nbsp;', ' ', strip_tags($record->comment)) }}" 
                            data-toggle="modal" 
                            data-target="#exampleModalCenter" 
                            style="font-size: 14px; cursor: pointer;">View</span>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="7" class="text-center">No records found</td>
            </tr>
        @endif
    </tbody>
</table>

<!-- Pagination Links -->
{{-- {{ $data->links() }} --}}
<div class="row mt-4">
    <div class="col-md-12 col-sm-12 mx-auto">
        <div class="custom-pagination-wrapper">
            <div class="pagination justify-content-center">
                {{ $data->links() }}
            </div>
        </div>
    </div>
</div>


