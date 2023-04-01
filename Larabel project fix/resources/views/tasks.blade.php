@extends('app')

@section('content')

    <!-- Bootstrap Boilerplate... -->
<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>


<div id="nav">
    <!-- Current Tasks -->
    @if (count($tasks) > 0)
			
		<div class="panel panel-default">
			<div class="panel-heading">
                Current Templates
            </div>
			<form action="/task" method="POST" class="form-horizontal">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">

				<!-- Task Name -->
				<div class="form-group">
					<label for="task" class="col-sm-3 control-label">Template</label>

					<div class="col-sm-6">
						<input type="text" name="name" id="task-name" class="form-control">
						<input type="text" name="searchVal" id="task-name" class="form-control">
					</div>
					<div ng-app="myApp" ng-controller="myCtrl">

					<button ng-click="count = count + 1">Get number from http://www.amazone.com</button>

					

					</div>
				</div>

				<!-- Add Task Button -->
				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-6">
						<button type="submit" class="btn btn-default">
							<i class="fa fa-plus"></i> Add Template
						</button>
						<button type="button" class="btn btn-default">
							<i class="fa fa-plus"></i> 
						</button>
					</div>
				</div>
			</form>
			
            <div class="panel-body">
                <table class="table table-striped task-table">

                    <!-- Table Headings -->
                    <thead>
                        <th>Templates</th>
                        <th>&nbsp;</th>
                    </thead>

                    <!-- Table Body -->
                    <tbody>
                        @foreach ($tasks as $task)
                            <tr>
                                <!-- Task Name -->
                                <td class="table-text">
                                    <div>{{ $task->name }}</div>
                                </td>

                                <!-- Delete Button -->
                                <td>
                                    <form action="/task/{{ $task->id }}" method="POST">
                                        <input type="hidden" name="_method" value="DELETE">
										<input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        
                                        <button>Delete</button>
                                       
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
	<!-- Current Zones -->
    @if (count($tasks) > 0)
			
		<div class="panel panel-default">
			<div class="panel-heading">
                Current Zones
            </div>
			<form action="/task" method="POST" class="form-horizontal">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">

				<!-- Task Name -->
				<div class="form-group">
					<label for="task" class="col-sm-3 control-label">Template</label>

					<div class="col-sm-6">
						<input type="text" name="name" id="task-name" class="form-control">
					</div>
				</div>

				<!-- Add Task Button -->
				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-6">
						<button type="submit" class="btn btn-default">
							<i class="fa fa-plus"></i> Create a Zone
						</button>
					</div>
				</div>
			</form>
			
            <div class="panel-body">
                <table class="table table-striped task-table">

                    <!-- Table Headings -->
                    <thead>
                        <th>Templates</th>
                        <th>&nbsp;</th>
                    </thead>

                    <!-- Table Body -->
                    <tbody>
                        @foreach ($tasks as $task)
                            <tr>
                                <!-- Task Name -->
                                <td class="table-text">
                                    <div>{{ $task->name }}</div>
                                </td>

                                <!-- Delete Button -->
                                <td>
                                    <form action="/task/{{ $task->id }}" method="POST">
                                        <input type="hidden" name="_method" value="DELETE">
										<input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        
                                        <button>Delete</button>
                                       
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
	<div id="panel panel-default">
		<input type="text" name="searchVal" value="{{ $task->searchVal }}">
	</div>
</div>
<div id="section">
	<div id="map-canvas" style="width:400px;height:400px;"></div>
</div>
<div >
	<iframe src="http://www.amazon.com/s/ref=nb_sb_noss_2?url=search-alias%3Daps&field-keywords=cake" WIDTH=250 HEIGHT=100></iframe>
	
</div>
<script>

var app = angular.module('myApp', []);
app.controller('myCtrl', function($scope, $http) {
   //$http.get("http://www.amazon.com/s/ref=nb_sb_noss_2?url=search-alias%3Daps&field-keywords=cake").then(function (response) {
   //   $scope.myData = response.data.records;
   //});
   
	
});


var map = new google.maps.Map(document.getElementById("map-canvas"),{
    center:{
        lat: 27.72,
        lng: 85.36
    },
    zoom:15
});

var marker = new google.maps.Marker({
    postion:{
        lat: 27.72,
        lng: 85.36    
    },
    map:map,
    draggale:true
});

var searchBox = new google.maps.places.SearchBox(document.getElementById('pac-input'));

</script>
@endsection