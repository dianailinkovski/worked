@extends('layouts.app')

@section('content')
    <!-- Create Templates Form... -->
        <!-- Bootstrap Boilerplate... -->

        <div class="panel-body">
            <!-- Display Validation Errors -->
            @include('common.errors')

            <!-- New Task Form -->
            <form action="/template" method="POST" class="form-horizontal">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <!-- Task Name -->
                <div class="form-group">
                    <label for="template" class="col-sm-3 control-label">Template</label>

                    <div class="col-sm-6">
                        <input type="text" name="name" id="task-name" class="form-control">
                    </div>
                </div>

                <!-- Add Task Button -->
                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-6">
                        <button type="submit" class="btn btn-default">
                            <i class="fa fa-plus"></i> Add Template
                        </button>
                    </div>
                </div>
            </form>
        </div>

    <!-- Current Templates -->
    @if (count($templates) > 0)
        <div class="panel panel-default">
            <div class="panel-heading">
                Current Templates
            </div>

            <div class="panel-body">
                <table class="table table-striped task-table">

                    <!-- Table Headings -->
                    <thead>
                        <th>Templates</th>
                        <th>&nbsp;</th>
                    </thead>

                    <!-- Table Body -->
                    <tbody>
                        @foreach ($template as $template)
                            <tr>
                                <!-- Task Name -->
                                <td class="table-text">
                                    <div>{{ $templates->name }}</div>
                                </td>

                                <td>
                                    <!-- TODO: Delete Button -->
                                    <form action="/template/{{ $template->id }}" method="POST">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        {{ method_field('DELETE') }}

                                        <button>Delete Templates</button>
                                        <input type="hidden" name="_method" value="DELETE">
                                    </form>
                                </td>
                            </tr> 
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    
@endsection