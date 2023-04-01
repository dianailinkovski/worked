// global variable where will define the database
var db = null;

angular.module('starter', ['ionic', 'ngCordova', 'starter.controllers', 'starter.services'])

.run(function($ionicPlatform, $cordovaSQLite) {
  $ionicPlatform.ready(function() {
    if (window.cordova && window.cordova.plugins.Keyboard) {
      cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);
    }
    if (window.StatusBar) {
      StatusBar.styleDefault();
    }
	
	// database integration - it's working
	if (window.sqlitePlugin) {
		// copy the database in the files of the app
		window.plugins.sqlDB.copy("ReefLifeDB.sqlite",
		function () {
			// copy success, run if the database is existing in the files of the app
			// set "db" as the database
			db = window.sqlitePlugin.openDatabase({name: "ReefLifeDB.sqlite"});
		}, function() {
			// copy error, run if the database is not existing in the files of the app
			// set "db" as the database
			db = window.sqlitePlugin.openDatabase({name: "ReefLifeDB.sqlite"});
		});
	}
	
  });
})

.config(function($stateProvider, $urlRouterProvider, $ionicConfigProvider) {

	$stateProvider	
	
	/* Tabs */
	.state('tab', {
		url: "/tab",
		abstract: true,
		templateUrl: "templates/tabs.html"
	})


	.state('tab.more', {
	  url: '/more',
	  views: {
		'tab-more': {
		  templateUrl: 'templates/tab-more.html',
		  controller: 'MoreCtrl'
		}
	  }
	})
	.state('tab.quiz', {
		url: '/quiz',
		views: {
		  'tab-quiz': {
			templateUrl: 'templates/tab-quiz.html',
			controller: 'QuizCtrl'
		  }
		}
	})
	.state('tab.training', {
	  url: '/training',
	  views: {
		'training': {
		    templateUrl: 'templates/training.html',
			controller: 'TrainingCtrl'
		}
	  }
	})
	.state('tab.encyclopedia', {
	  url: '/encyclopedia',
	  views: {
		'tab-encyclopedia': {
		  templateUrl: 'templates/tab-encyclopedia.html',
		  controller: 'EncyclopediaCtrl'
		}
	  }
	})
	.state('tab.details', {
	  url: '/encyclopedia/:animalId',
	  views: {
		'tab-encyclopedia': {
			templateUrl: 'templates/details.html',
			controller: 'DetailsCtrl'
		}
	  }
	})
	
	$urlRouterProvider.otherwise('/tab/quiz');
	$ionicConfigProvider.tabs.position('bottom');
	$ionicConfigProvider.backButton.text('').icon('ion-chevron-left');
});