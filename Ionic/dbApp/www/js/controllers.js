angular.module('starter.controllers', [])

.controller('QuizCtrl', function($scope, $ionicSideMenuDelegate) {
  $scope.showRightMenu = function () {
    $ionicSideMenuDelegate.toggleRight();
  };
})

.controller('EncyclopediaCtrl', function($scope, Animals) {
	
	// services Animals
	$scope.animals = Animals.all();
	$scope.remove = function(animal) {
		Animals.remove(animal);
	};
	
	// test to order the list - It's not working
	$scope.order = function(){
		db.transaction(function(tx) {
			tx.executeSql("Select name, ID From REEFGUIDE Order by name ASC", [], function(tx, res) {
				
				var len = res.rows.length;
				
				var list = document.getElementById("alphabetical-list");
				
				for (var i = 0; i < len; i++) { // loop as many times as there are row results
					alert.log(res.rows.item(i).name +', '+ res.rows.item(i).ID);
					
					list.innerHTML +='<ion-item class="item-avatar item-icon-right" type="item-text-wrap" ng-repeat="animal in animals" href="#/tab/encyclopedia/{{animal.id}}><img ng-src="/img/fish1.png" /><h2>'+ results.rows.item(i).name +'</h2><i class="icon ion-chevron-right icon-accessory"></i></ion-item>';
				}
			}, function(e) {
				alert("ERROR: " + e.message);
			});
		});
	};
	
})

.controller('DetailsCtrl', function($scope, $stateParams, Animals) {
	$scope.animal = Animals.get($stateParams.animalId);
})

.controller('TrainingCtrl', function($scope) {
})
.controller('MoreCtrl', function($scope) {
})

// Test to show a simple popup with some data from the database - It's working
.controller("TestDatabaseCtrl", function($scope, $cordovaSQLite) {
 
	$scope.select = function(id) {
        
		db.transaction(function(tx) {

			tx.executeSql("SELECT name, default_name, ID FROM REEFGUIDE WHERE ID ="+id, [], function(tx, res) {
				
				var len = res.rows.length;
				alert("results.rows.length: " + res.rows.length);
				for (var i = 0; i < len; i++) { // loop as many times as there are row results
					alert(res.rows.item(i).name +', '+ res.rows.item(i).default_name +', '+ res.rows.item(i).ID);
				}
				
			}, function(e) {
			  console.log("ERROR: " + e.message);
			});
		});
		
	};
 
});