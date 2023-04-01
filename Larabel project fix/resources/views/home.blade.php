@extends('app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Home</div>

				<div class="panel-body">
                    <input id="pac-input" type="text" placeholder="Search Box">
                      
                </div>
               
			</div> 
		</div>
	</div> <div class="panel-body">
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

    
    
    <div id="map-canvas" style="width:1000px;height:600px;"></div>
    
</div>
<script>
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

    // we fetch the hidden fields of the form using jQuery
/*    var latField = $('input#lat');
    lngField = $('input#lng');
    
    public function rules()
    {
        return [
            'lat' => 'required',
            'lng' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'lat.required' => 'Please select a location on the map',
            'lng.required' => 'Please select a location on the map',
        ];
    }
    $maperizer.maperizer('attachEventsToMap', [
        {
            name: 'click',
                callback: function(event){         
                    $maperizer.maperizer('removeAllMarkers');
                    $maperizer.maperizer('addMarker', {
                        lat: event.latLng.lat(),
                        lng: event.latLng.lng() 
                    });
               } 
         }
    ]);
         
    var marker = new google.maps.Marker({
         podtion:{
            lat:27.72,
            lng:85.38
         },
         map:mp
         draggable :true   
   
    });    
    
    
       //         search.parts
       
       
    var searchBox = new google.maps.place.SearchBox(document.getElementById("searchmap")),
    google.map.event.addListnener(searchBox,'places_changed',function(){
    });
        
    google.maps.event.addListener(marker,'postion_changed',funtion(){})    
    $.ajax({
    type: "POST",
    url: window.location.href
    }).done(function(entry){
        //addFocusedMarker is a function to add a Marker and change the map view to center it
        $maperizer.maperizer('addFocusedMarker', {
            lat: entry.lat,
            lng: entry.lng
        });
    });   */
</script>
@endsection
