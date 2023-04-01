angular.module('weatherapp.controllers', ['mobiscroll-image', 'mobiscroll-datetime', 'ionic.rating', 'jlareau.pnotify', 'ngCordova', 'google.places'])

.controller('AppCtrl', function($scope, $rootScope, FriendService, $ionicModal, $timeout, $ionicLoading, $ionicSideMenuDelegate, $compile,$ionicLoading, $window, notificationService, $state, $cordovaSocialSharing) {
	$scope.capitals = capitals;
	
	//alert(capitals[0]);
	
	$scope.wData = [
    {
      title: 'United Kingdom',
	  degree: -10
    },
	{
      title: 'Spain',
	  degree: -10
    },
	{
      title: 'Germany',
	  degree: -10
    },
	{
      title: 'France',
	  degree: -10
    },
	{
      title: 'Greece',
	  degree: -10
    },
	{
      title: 'Italy',
	  degree: -10
    }];
	
	$scope.fData = [
		{
		  title: 'Average Maximum Temperature',
		  id: 1,
		  bUse: false
		},
		{
		  title: 'Average Minimum Temperature',
		  id: 2,
		  bUse: false
		},
		{
		  title: 'Average Dry Days',
		  id: 3,
		  bUse: false
		},
		{
		  title: 'Average Monthly RainFall Amount',
		  id: 4,
		  bUse: false
		},
		{
		  title: 'Average Daily Rainfall Amount',
		  id: 5,
		  bUse: false
		},
		{
		  title: 'Average Fog Days',
		  id: 6,
		  bUse: false
		},
		{
		  title: 'Average Snow Days',
		  id: 7,
		  bUse: false
		}		
	];
	
	$scope.countryData = [
		{
		  title: 'Average Dry Days',
		  val: "61",
		  imgUrl : "img/sun.png"
		},
		{
		  title: 'Average Monthly Rainfall Amount',
		  val: "4 mm",
		  imgUrl : "img/umbrella.png"
		},
		{
		  title: 'Average Daily Rainfall Amount',
		  val: "1 mm",
		  imgUrl : "img/two_umbrella.png"		  
		},
		{
		  title: 'Average Fog Days',
		  val: "1",
		  imgUrl : "img/fog.png"
		},
		{
		  title: 'Average Snow Days',
		  val: "2",
		  imgUrl : "img/snow.png"
		}
	];
	
  $scope.goWeatherPage = function () {
	$state.go("app.weather");
  }
  
  $scope.arrowImg = "img/des_arrow.png";
  
  $scope.bFilterShow = false;
  
  $scope.countryName =  "The Former Yugoslav Republic of Macedonia";
  $scope.homeTitle = "Weather Watchman";
  $scope.discoverTitle = "Weather discoveries";
  
  //Show Filter List
  $scope.showFilter = function() {
	if($scope.arrowImg == "img/des_arrow.png") {
		$scope.arrowImg = "img/asc_arrow.png";
		$scope.bFilterShow = true;
	}
	else {
		$scope.arrowImg = "img/des_arrow.png";
		$scope.bFilterShow = false;
	}
  }
  
  //Decide Filter Item after click , Apply Filtering
  $scope.onClickFilterItem  = function(sub_item){
	for(var i =0;i<$scope.fData.length;i++){
		$scope.fData[i].bUse = false;		
	}
	for(var i =0;i<$scope.fData.length;i++){
		item = $scope.fData[i];
		if(item.id == sub_item.id) {
			$scope.fData[i].bUse = true;		
			break;
		}
	}
	
	$scope.bFilterShow = false;
	$scope.arrowImg = "img/des_arrow.png";
	

	//Processing Filter Start	
	
	//Processing Filter End
  }
  
  //Show Country Detail
  $scope.onClickCountry = function(item){
	$state.go("app.country");
  }
  
  //go Home Page
  $scope.goHome = function() {
	$state.go("app.home");
  }
  
  //Show Country Map Page
  $scope.goCountryMap = function(item){
	$state.go("app.country_map");
  }  
  
  //Share Country Weather Detail
  $scope.shareDetail = function (){
	$cordovaSocialSharing
    .share(message, subject, file, link) // Share via native share sheet
    .then(function(result) {
      // Success!
    }, function(err) {
      // An error occured. Show a message to the user
    });	
  }
  
  // init gps array
    $scope.basel = { lat: 47.55633987116614, lon: 7.576619513223015 };
	
	$scope.whoiswhere = [
		        { "name": "My Marker", "lat": $scope.basel.lat, "lon": $scope.basel.lon },
				];
})

// Controller that shows more detailed info about a friend
.controller('FriendCtrl', function($scope, $stateParams, FriendService, $window, $ionicLoading, $timeout) {
  
  

  $scope.onSwipeLeft = function () {
	//alert("swipeleft");
  }
    $scope.onSwipeRight = function () {
	url ="#/app/friendlist";
	$window.location.href = url;	
	//alert("swiperight");
  }
  
})

// Controller that shows profile
.controller('ProfileCtrl', function($scope, $stateParams, FriendService, $ionicLoading, $ionicModal,$sce, $timeout, $http, notificationService, $cordovaCamera) {
  
  $scope.profileInfo = [];
  $scope.profileInfo.name = "John Steve";
  $scope.profileInfo.email = "johnsteve@gmail.com";
  $scope.profileInfo.phone = "1232342342";
  $scope.profileInfo.emergencyinfo = "this is emergency information";
  
  $scope.phone = "";
  
  $scope.friend = FriendService.get($stateParams.commentId);
  $scope.title = "Profile";  	

  $scope.selectedCar;
 	
  $scope.birthday = new Date();

	/*zheng start*/
	$scope.showModal = function(templateUrl) {
		$ionicModal.fromTemplateUrl(templateUrl, {
			scope: $scope,
			animation: 'slide-in-up'
		}).then(function(modal) {
			$scope.modal = modal;
			$scope.modal.show();
		});
	}
	
		// Close the modal
		$scope.closeModal = function() {
			$scope.modal.hide();
			$scope.modal.remove()
		};

		
		$scope.clipSrc = 'https://www.safecab.com/home/AdvertMedia?AdvertId=2';
 
		$scope.playVideo = function() {
			$scope.showModal('templates/video-popover.html');
		}
		
		 $scope.trustSrc = function(src) {
			return $sce.trustAsResourceUrl(src);
		}
		
		/*zheng end*/
		
  $scope.callPhoneService = function(){
  	//alert($scope.profileInfo.phone);
  	if($scope.profileInfo.phone.toString().length < 5)
  		return ;
   // alert(typeof($scope.profileInfo.phone));
    var url  = "https://external.safecab.com/ValidationService.svc/ValidatePhone/" + $scope.profileInfo.phone + "/GB";
   // alert(url);
  	$http.get(url).
	  success(function(data, status, headers, config) {
	    // this callback will be called asynchronously
	    // when the response is available
	    alert(data);
	  }).
	  error(function(data, status, headers, config) {
	    // called asynchronously if an error occurs
	    // or server returns response with an error status.
	  });
  }
  
  $scope.imgData = null;
  
  $scope.openCamera  = function (){
	var options = {
      quality: 50,
      destinationType: Camera.DestinationType.DATA_URL,
      sourceType: Camera.PictureSourceType.CAMERA,
      allowEdit: true,
      encodingType: Camera.EncodingType.JPEG,
      targetWidth: 100,
      targetHeight: 100,
      popoverOptions: CameraPopoverOptions,
      saveToPhotoAlbum: false
    };

    $cordovaCamera.getPicture(options).then(function(imageData) {
      var image = document.getElementById('myImage');
      image.src = "data:image/jpeg;base64," + imageData;
	  $scope.imgData = imageData;
    }, function(err) {
      // error
    }); 
  }
  
  
})
