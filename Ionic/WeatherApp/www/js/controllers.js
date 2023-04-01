angular.module('starter.controllers', [])

/*
TODO FREE:
- LowLatencyAudio.turnOffAudioDuck(PersonalData.GetAudioSettings.duckOnce);
- 'duck'
- 'noduck'
*/

.controller('BodyCtrl', function($rootScope, $scope,$http,$location,$timeout,$translate,$ionicLoading, $ionicSideMenuDelegate, $stateParams) {
  window.cb = '';
  $scope.callCustom = function(url){
    var schemaParams = deparam(url);
    if (schemaParams["workout"]){
        $ionicLoading.show({
          template: $translate.instant('IMPORTING')
        });
        var getUrl = 'http://sworkitapi.herokuapp.com/workouts?s=' + schemaParams["workout"] + '&d=true';
        $http.get(getUrl).then(function(resp){
            if (resp.data.name){
               installWorkout(resp.data.name, resp.data.exercises, $location, $ionicSideMenuDelegate);
               $timeout(function(){
                var tempLocation = $location.$$url.slice(-7) || '';
                $ionicLoading.hide();
                if (tempLocation !== "workout"){
                  $location.path('/app/home');
                  $ionicSideMenuDelegate.toggleLeft(false);
                }
               }, 1000)
             } else {
              $ionicLoading.hide();
                navigator.notification.alert(
                  $translate.instant('UNABLE'),  // message
                  nullHandler,         // callback
                  $translate.instant('INVALID'),            // title
                  $translate.instant('OK')                  // buttonName
                );
             }
           }, function(err) {
                $ionicLoading.hide();
                navigator.notification.alert(
                  $translate.instant('UNABLE'),  // message
                  nullHandler,         // callback
                  $translate.instant('INVALID'),            // title
                  $translate.instant('OK')                  // buttonName
                );
            })
    }
    else if (schemaParams["custom"]){
      var tempLocation = $location.$$url.slice(-7);
      if (tempLocation !== "workout"){
        $location.path('/app/custom');
        $ionicSideMenuDelegate.toggleLeft(false);
      }
    }
    else if(schemaParams["access_code"]){
        $ionicLoading.show({
          template: $translate.instant('AUTHORIZING') 
        });
        myObj.code = schemaParams["access_code"];
        $timeout(function(){
                var tempLocation = $location.$$url.slice(-7);
                $ionicLoading.hide();
                if (tempLocation !== "workout"){
                  $location.path('/app/settings');
                  setTimeout(function(){$rootScope.childBrowserClosed()}, 500);
                }
               }, 1000)
       
        if (window.cb.close){
          window.cb.close();
        }
    }
    else if(schemaParams["mfperror"]){
        console.log('mfperror: ' + schemaParams);
        window.cb.close();
        $rootScope.childBrowserClosed();
    }
  }
  $scope.changeLanguage = function (langKey) {
    $translate.use(langKey);
  };
})

.controller('AppCtrl', function($rootScope,$scope,$ionicModal,$ionicSlideBoxDelegate,$translate,$timeout,$location,$stateParams,WorkoutService) {

  $scope.clickHome = function(){
    var tempURL = $location.$$url.substring(0,9);
    if (tempURL == '#/app/cust') {
      $location.path('/app/custom');
    } else if (tempURL !== '/app/home'){
      $location.path('/app/home');
    }
  }
  $scope.isItemActive = function(shortUrl) {
    var tempURL = '/app/' + shortUrl;
    return (tempURL == $location.$$path.substring(0,9));
  };
  $scope.showWelcome = function(){
    $ionicModal.fromTemplateUrl('welcome.html', function(modal) {
                                  $scope.welcomeModal = modal;
                                  }, {
                                  scope:$scope,
                                  animation: 'slide-in-up',
                                  focusFirstInput: false,
                                  backdropClickToClose: false
                                  });
    $scope.slideChanged = function(index) {
      $scope.slideIndex = index;
    };
    $scope.next = function() {
      $ionicSlideBoxDelegate.next();
    };
    $scope.previous = function() {
      $ionicSlideBoxDelegate.previous();
    };
    $scope.closeOpenNexercise = function(){
      $scope.closeModal();
    }
    $scope.openModal = function() {
      $scope.welcomeModal.show();
    };
    $scope.closeModal = function() {
      $scope.welcomeModal.hide();
    };
    $scope.$on('$ionicView.leave', function() {
      $scope.welcomeModal.remove();
    });
    $timeout(function(){
             $scope.openModal();
             }, 0);
  }
  $scope.downloadNexerciseMenu = function(){
    if (!isSamsung()){
      trackEvent('More Action', 'Nexercise Apps from Lite', 0);
      setTimeout(function(){
        window.open('http://nexercise.com/download', '_system', 'location=no,AllowInlineMediaPlayback=yes');
      }, 400)
    }
  }
  $scope.launchStore = function(){
    window.open('http://www.ntensify.com/sworkit', '_blank', 'location=yes,AllowInlineMediaPlayback=yes,toolbarposition=top');
  }
})

.controller('HomeCtrl', function($rootScope, $scope, $timeout, $ionicSideMenuDelegate, $location, $translate, $ionicPopup, $stateParams, UserService) {
  LocalHistory.getCustomHistory.lastHomeURL = $location.$$url;
  $scope.title = "<img src='img/sworkit_logo.png'/>"
  $scope.timesUsedVar = parseInt(window.localStorage.getItem('timesUsed'));
  $scope.rewardSettings = UserService.getUserSettings();

  if ($scope.rewardSettings.preferredLanguage == undefined){
    localforage.getItem('userSettings', function (result){
      if (result !== null){
        $translate.use(result.preferredLanguage);
      }
    })
  }
  $scope.quickImage = {img:'quick-' + $translate.instant('LANGUAGE')};
  $rootScope.showPointsBadge = false;
  $rootScope.mPointsTotal = 0;
  $scope.rateAttempts = 0;
  $timeout(function(){
    $scope.rewardSettings = UserService.getUserSettings();
    if ($rootScope.sessionMAvailable && $scope.rewardSettings.mPoints){
      document.getElementById('home-points').classList.remove( "ng-hide" );
    }
  }, 1000);
  $timeout(function(){
    $scope.rewardSettings = UserService.getUserSettings();
    if ($scope.rewardSettings.mPoints && device && $rootScope.sessionMAvailable){
      sessionm.phonegap.getUnclaimedAchievementCount(function callback(data) {
          $rootScope.showPointsBadge = (data.unclaimedAchievementCount > 0) ? true : false;
          $rootScope.mPointsTotal = data.unclaimedAchievementCount;
      });
    } else {
      $rootScope.showPointsBadge = false;
    }
    $scope.launchPopups();
  }, 2500);

  $scope.launchMPoints = function(){
    if (device){
      sessionm.phonegap.presentActivity(2);
    }
  }

  $scope.launchPopups = function(){
    if (globalNew310Option){
        $scope.choosePopup();
        globalNew310Option = false;
        localforage.setItem('new310Home', false);
    } else if (globalRemindOption){
      globalRemindOption = false;
      localforage.setItem('remindHome', {show:false,past:true}, function(){
        var pDate = new Date();
        var pHour = (pDate.getHours() > 12) ? pDate.getHours() - 12 : pDate.getHours();
        var ampm = (pDate.getHours() > 11) ? $translate.instant('PM') : $translate.instant('AM');
        var pMinute = (pDate.getMinutes() < 10) ? "0" + pDate.getMinutes()  : pDate.getMinutes();
        var timeString = pHour + ':' + pMinute + ' ' + ampm;
        if (!LocalData.SetReminder.daily.status){
          $timeout(function(){
            $ionicPopup.confirm({
               title: $translate.instant('DAILY'),
               template: '<p class="padding">'+ $translate.instant('REMINDER_SET') + ' ' + timeString + '. ' + $translate.instant('REMINDER_CONT') +'</p>',
               okType: 'energized',
               okText: $translate.instant('OK'),
               cancelText: $translate.instant('OPTIONS')
             }).then(function(res) {
                  var dDate = new Date();
                  var tDate = new Date();
                  dDate.setSeconds(0);
                  dDate.setDate(dDate.getDate() + 1);
                  LocalData.SetReminder.daily.time = dDate.getHours();
                  LocalData.SetReminder.daily.minutes = dDate.getMinutes();
                  LocalData.SetReminder.daily.status = true;
                  window.plugin.notification.local.add({
                                                         id:         1,
                                                         date:       dDate,    // This expects a date object
                                                         message:    $translate.instant('TIME_TO_SWORKIT'),  // The message that is displayed
                                                         title:      $translate.instant('WORKOUT_REM'),  // The title of the message
                                                         repeat:     'daily',
                                                         autoCancel: true,
                                                         icon: 'ic_launcher',
                                                         smallIcon: 'ic_launcher'
                                                         });
                  window.plugin.notification.local.onclick = function (id, state, json) {
                        window.plugin.notification.local.cancel(1);
                        var nDate = new Date();
                        var tDate = new Date();
                        nDate.setHours(LocalData.SetReminder.daily.time);
                        nDate.setMinutes(LocalData.SetReminder.daily.minutes);
                        nDate.setSeconds(0);
                        if (tDate.getHours() <= nDate.getHours() && tDate.getMinutes() <= nDate.getMinutes()){
                            nDate.setDate(nDate.getDate() + 1);
                        }
                        $timeout( function (){window.plugin.notification.local.add({
                                                                               id:         1,
                                                                               date:       nDate,    // This expects a date object
                                                                               message:    "Time to Swork Out. Bring it on.",  // The message that is displayed
                                                                               title:      'Workout Reminder',  // The title of the message
                                                                               repeat:     'daily',
                                                                               autoCancel: true,
                                                                               icon: 'ic_launcher',
                                                                               smallIcon: 'ic_launcher'
                                                                               });}, 2000);
                    }
                    localforage.setItem('reminder',{daily: {
                      status: true,
                      time: dDate.getHours(),
                      minutes: dDate.getMinutes()},
                      inactivity: {
                        status:LocalData.SetReminder.inactivity.status,
                        time:LocalData.SetReminder.inactivity.time,
                        minutes:LocalData.SetReminder.inactivity.minutes,
                        frequency:LocalData.SetReminder.inactivity.frequency
                      }
                    });
                  if (res){
                    if (ionic.Platform.isIOS()){
                      window.plugin.notification.local.hasPermission(function (granted) {
                          if (!granted){
                            window.plugin.notification.local.promptForPermission();
                          }
                      });
                    }
                  }
                  if (!res) {
                    $location.path('/app/reminders');
                    $ionicSideMenuDelegate.toggleLeft(false);
                  }
                })
          }, 200)
        } 
      })    
    }
  }
  $scope.whatsNewPopup = function(){
      angular.element(document.getElementsByTagName('body')[0]).addClass('home-new');
      $ionicPopup.alert({
               title: $translate.instant('WHAT_NEW'),
               template: '<div class="padding whats-new"><h3>{{"NEW_TRANSLATE1" | translate}}</h3><h3 style="color:#C4C4C4">{{"NEW_TRANSLATE2" | translate}}</h3><h3>{{"NEW_TRANSLATE3" | translate}}</h3></div>',
               okType: 'energized',
               okText: $translate.instant('OK_SWORKIT')
             }).then(function(res) {
               if(res) {
                $timeout(function(){
                angular.element(document.getElementsByTagName('body')[0]).removeClass('home-new');
               }, 2000)
               }
             });
  }

  $scope.whatsNewPopupHealth = function(){
      angular.element(document.getElementsByTagName('body')[0]).addClass('home-new');
      $ionicPopup.alert({
               title: "What's New",
               template: '<div class="padding whats-new"><h3>Sworkit Lite</h3><p>New name, same great features</p><h3>Health App Integration</h3><p>Settings - Connect to Health App</p><h3>Share Custom Workouts</h3><p>Now personal trainers, physicians, coaches, and friends can share specially designed workouts</p><h3>Popular Workouts</h3><p>Find out which custom workouts are popular among other users</p></div>',
               okType: 'energized',
               okText: 'OK, GET TO SWORK'
             }).then(function(res) {
               if(res) {
                $timeout(function(){
                angular.element(document.getElementsByTagName('body')[0]).removeClass('home-new');
               }, 2000)
               }
             });
  }
  
  $scope.choosePopup = function(){
    if (ionic.Platform.isAndroid()){
      $scope.androidPlatform = true;
    } else{
      $scope.androidPlatform = false;
    }
    $scope.whatsNewPopup();
  }

  $scope.activateSelection = function(tag){
    if (ionic.Platform.isAndroid()){
      //angular.element(document.getElementById(tag)).addClass('activated');
    }
  }
  $scope.$on('$ionicView.leave', function() {
    //TODO: remove the activated class from whichever one was chosen and we are still using activateSelection
    //angular.element(document.getElementById(tag)).removeClass('activated');
  });
  $scope.downloadNexercise = function (){
    trackEvent('More Action', 'Install Nexercise', 0);
    setTimeout(function(){
      if (device.platform.toLowerCase() == 'ios') {
        window.open('http://nxr.cz/nex-ios', '_system', 'location=no,AllowInlineMediaPlayback=yes');
      }  else if (isAmazon()){
        window.appAvailability.check('com.amazon.venezia', function() {
             window.open('amzn://apps/android?p=com.nexercise.client.android', '_system')},function(){
             window.open(encodeURI("http://www.amazon.com/gp/mas/dl/android?p=com.nexercise.client.android"), '_system');}
             );
      } else {
      window.open('market://details?id=com.nexercise.client.android', '_system')
      }
    }, 400)
  }
  $scope.$on('$ionicView.beforeEnter', function() {
      $ionicSideMenuDelegate.canDragContent(true);
  }); 
})

.controller('WorkoutCategoryCtrl', function($rootScope, $scope, $translate,$timeout,$location,$ionicPopup,$stateParams, WorkoutService) {
  LocalHistory.getCustomHistory.lastHomeURL = $location.$$url;
  if (ionic.Platform.isAndroid()){
    $scope.androidPlatform = true;
  } else{
    $scope.androidPlatform = false;
  }
  $scope.data = {showInfo:false};
  $scope.timesUsedVar = parseInt(window.localStorage.getItem('timesUsed'));
  $scope.thisCategory = $stateParams.categoryId;
  $scope.categoryTitle = LocalData.GetWorkoutCategories[$stateParams.categoryId].fullName;
  $scope.categories = WorkoutService.getWorkoutsByCategories($stateParams.categoryId);
  $scope.workoutTypes = WorkoutService.getWorkoutsByType();
  $scope.showRateOption = globalRateOption;
  $scope.showShareOption = globalShareOption;
  $scope.rateAttempts = 0;
  $scope.resizeOptions = {grow: false, shrink:true, defaultSize: 18, minSize:18, maxSize:32};
  $scope.rateHeader = $translate.instant('ENJOYING_RATE');
  $scope.noButton = $translate.instant('NOT_REALLY');
  $scope.yesButton = $translate.instant('YES_SM') + '!';
  $scope.noTaps = true;
  $scope.yesTaps = true;
  if ($scope.showShareOption == 4){
    $scope.rateHeader = $translate.instant('THANK_SHARE');
    $scope.noButton = $translate.instant('NO_THANKS');
    $scope.yesButton = $translate.instant('YES_SM') + '!';
  } else if ($scope.showShareOption == 8) {
    $scope.rateHeader = $translate.instant('CAT_TWITTER');
    $scope.noButton = $translate.instant('NO_THANKS');
    $scope.yesButton = $translate.instant('YES_SM') + '!';
  } else if ($scope.showShareOption == 13) {
    $scope.rateHeader = $translate.instant('CAT_FACEBOOK');
    $scope.noButton = $translate.instant('NO_THANKS');
    $scope.yesButton = $translate.instant('YES_SM') + '!';
  }
  $scope.yesOption = function(){
    if ($scope.yesTaps && $scope.noTaps && $scope.showShareOption == 4){
      $scope.showShareOption = 5;
      globalShareOption = 5;
      localforage.setItem('ratingCategory', {show:false,past:true,shareCount:5,sharePast:true}, function(){});
      var challengeText = $translate.instant('DOWNLOAD') + ' #Sworkit ' + $translate.instant('FOR_CUSTOM') + ' at http://sworkit.com';
      window.plugins.socialsharing.share(challengeText, null, null, 'http://sworkit.com');
      trackEvent('Dialog Request', 'Share', 0);
    } else if ($scope.yesTaps && $scope.noTaps && $scope.showShareOption == 8){
      $scope.showShareOption = 9;
      globalShareOption = 9;
      localforage.setItem('ratingCategory', {show:false,past:true,shareCount:9,sharePast:true}, function(){});
      window.open('http://twitter.com/sworkit', '_blank', 'location=yes,AllowInlineMediaPlayback=yes,toolbarposition=top');
      trackEvent('Dialog Request', 'Follow Twitter', 0);
    } else if ($scope.yesTaps && $scope.noTaps && $scope.showShareOption == 13){
      $scope.showShareOption = 14;
      globalShareOption = 14;
      localforage.setItem('ratingCategory', {show:false,past:true,shareCount:14,sharePast:true}, function(){});
      window.open('http://facebook.com/SworkitApps', '_blank', 'location=yes,AllowInlineMediaPlayback=yes,toolbarposition=top');
      trackEvent('Dialog Request', 'Follow Facebook', 0);
    } else if ($scope.yesTaps && $scope.noTaps){
      $scope.yesButton = $translate.instant('OK') + '!';
      $scope.noButton = $translate.instant('NO_THANKS');
      $scope.rateHeader = $translate.instant('PLEASE_REVIEW');
      $scope.yesTaps = false;
    } else if (!$scope.noTaps){
      globalRateOption = false;
      $scope.showRateOption = false;
      localforage.setItem('ratingCategory', {show:false,past:true,shareCount:1,sharePast:false}, function(){});
      if (ionic.Platform.isAndroid()){
        $scope.appVersion = '5.60.05'
      } else {
        $scope.appVersion = '3.5.5'
      }
      var emailBody = "<p>" + $translate.instant('DEVICE') + ": " + device.model + "</p><p>" + $translate.instant('PLATFORM') + ": "  + device.platform + " " + device.version  + "</p>" + "<p>" + $translate.instant('APP_VERSION') + ": " + $scope.appVersion + "</p><p>" + $translate.instant('FEEDBACK') + ": </p>";
      window.plugin.email.open({
                       to:      ['contact@sworkit.com'],
                       subject: $translate.instant('FEEDBACK') + ': Sworkit Lite App',
                       body:    emailBody,
                       isHtml:  true
                       });
    } else {
      $timeout(function(){
        globalRateOption = false;
        $scope.showRateOption = false;
        trackEvent('Dialog Request', 'Feedback', 0);
        var volumeNotification = angular.element(document.getElementsByClassName('volume-notification'));
        var insideTextNew = $translate.instant('THANK_REVIEW');
        volumeNotification.html('<h3 class="ng-binding">'+insideTextNew+'</h3>');
        volumeNotification.addClass('animate').removeClass('flash');
        setTimeout(function(){
              trackEvent('Dialog Request', 'Review', 0);
              volumeNotification.addClass('flash').removeClass('animate');
              var insideText = $translate.instant('VOLUME_REC');
              volumeNotification.html('<h3 class="ng-binding"><span><i class="icon ion-volume-medium"></i></span>  '+insideText+'</h3>');
        }, 4000);
        localforage.setItem('ratingCategory', {show:false,past:true,shareCount:1,sharePast:false}, function(){upgradeNotice(2);});
        }, 500);
    }
  }
  $scope.noOption = function(){
    if ($scope.yesTaps && $scope.noTaps && $scope.showShareOption == 4){
      $scope.showShareOption = 5;
      globalShareOption = 5;
      localforage.setItem('ratingCategory', {show:false,past:true,shareCount:5,sharePast:true}, function(){});
    } else if ($scope.yesTaps && $scope.noTaps && $scope.showShareOption == 8){
      $scope.showShareOption = 9;
      globalShareOption = 9;
      localforage.setItem('ratingCategory', {show:false,past:true,shareCount:9,sharePast:true}, function(){});
    } else if ($scope.yesTaps && $scope.noTaps && $scope.showShareOption == 13){
      $scope.showShareOption = 14;
      globalShareOption = 14;
      localforage.setItem('ratingCategory', {show:false,past:true,shareCount:14,sharePast:true}, function(){});
    } else if ($scope.yesTaps && $scope.noTaps){
      $scope.yesButton = $translate.instant('OK') + '!';
      $scope.noButton = $translate.instant('NO_THANKS');
      $scope.rateHeader = $translate.instant('LEAVE_FEEDBACK');
      $scope.noTaps = false;
    } else if (!$scope.noTaps || !$scope.yesTaps){
      $scope.showRateOption = false;
      globalRateOption = false;
      localforage.setItem('ratingCategory', {show:false,past:true,shareCount:1,sharePast:false}, function(){});
    } else {
      $scope.showRateOption = false;
      globalRateOption = false;
    }
  }

})

.controller('WorkoutCustomCtrl', function($rootScope, $scope, $ionicModal, $location, $ionicLoading, $translate, $ionicPopup, $ionicSlideBoxDelegate, $ionicListDelegate, $http, $ionicActionSheet, $ionicScrollDelegate, $location, $timeout, filterFilter, UserService, WorkoutService) {
  LocalHistory.getCustomHistory.lastHomeURL = $location.$$url;
  $scope.customWorkouts = UserService.getCustomWorkoutList();
  if (ionic.Platform.isAndroid()){
    $scope.androidPlatform = true;
  } else{
    $scope.androidPlatform = false;
  }
  if (ionic.Platform.isWebView()){
    $scope.browserPlatform = false;
  } else{
    $scope.browserPlatform = true;
  }
  if (device){
    cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);
  }
  $scope.getFirst = function(phrase){
    if (phrase){
      return phrase.split(/[.!?]+/)[0].substring(0,98) + '' || '';
    } else {
      return '';
    }
  }
  $scope.downloadedWorkouts = downloadableWorkouts.sort(function() { return 0.5 - Math.random() });
  $scope.editMode = false;
  $scope.customName = '';
  $scope.currentCustom = UserService.getCurrentCustom();
  $scope.isPressed = false;
  $scope.exerciseCategories = [
    {shortName:"upper",longName:"UPPER_SM", exercises: WorkoutService.getExercisesByCategory('upper') },
    {shortName:"core",longName:"CORE_SM", exercises: WorkoutService.getExercisesByCategory('core') },
    {shortName:"lower",longName:"LOWER_SM", exercises: WorkoutService.getExercisesByCategory('lower') },
    {shortName:"stretch",longName:"STRETCH_SM", exercises: WorkoutService.getExercisesByCategory('stretch') },
    {shortName:"back",longName:"BACK_SM", exercises: WorkoutService.getExercisesByCategory('back') },
    {shortName:"cardio",longName:"CARDIO_SM", exercises: WorkoutService.getExercisesByCategory('cardio') },
    {shortName:"pilates",longName:"PILATES_SM", exercises: WorkoutService.getExercisesByCategory('pilates') },
    {shortName:"yoga",longName:"YOGA_SM", exercises: WorkoutService.getExercisesByCategory('yoga') }
  ];
  $scope.allExercises = [];
  for(var eachExercise in exerciseObject) {
    $scope.allExercises.push($translate.instant(exerciseObject[eachExercise].name));
  }
  $timeout(function(){
    $scope.allExercises.sort();
  }, 1500)

  $scope.getTranslatedExercise = function(exerciseName){
    return exerciseObject[exerciseName].name;
  }

  $scope.addExercise = function(){
    if ($scope.selectedExerciseAdd.selected !== ''){
      var keyObject = translations[PersonalData.GetUserSettings.preferredLanguage];
      keyObject.getKeyByValue = function( value ) {
        for( var prop in this ) {
          if( this.hasOwnProperty( prop ) ) {
            if( this[ prop ] === value )
            return prop;
          }
        }
      }
      var foundKey = keyObject.getKeyByValue($scope.selectedExerciseAdd.selected);
      var keyInEN = translations['EN'][foundKey];
      $scope.reorderWorkout.push(keyInEN);
    }
  }
  $scope.selectedExerciseAdd = {selected: $translate.instant('ABDOMINALCRUNCH')};
  $scope.workoutLengths = function(){
    PersonalData.GetCustomWorkouts.savedWorkouts.forEach(function(element, index, array){if (element.workout.length == 1){element.total = "1 "} else{element.total = element.workout.length + ' '}});
  }
  $scope.workoutLengths();
  $scope.editAll = function(){
    if ($scope.editMode){
      angular.element(document.getElementsByClassName('my-customs')).removeClass('edit-mode');
      angular.element(document.getElementsByClassName('item-options')).addClass('invisible');
    }
    else{
      angular.element(document.getElementsByClassName('item-options')).removeClass('invisible');
      angular.element(document.getElementsByClassName('my-customs')).addClass('edit-mode');
    }
    $scope.editMode = !$scope.editMode;
  }
  $scope.shareCustom = function(indexEl, customObj) {
    var selectedWorkout = customObj;
    $ionicListDelegate.closeOptionButtons();
    if (selectedWorkout.shareUrl){
      var postURL = 'http://sworkitapi.herokuapp.com/workouts?s=' + selectedWorkout.shareUrl;
    } else{
      var postURL = 'http://sworkitapi.herokuapp.com/workouts';
    }
    $http({
        url: postURL,
        method: "POST",
        data: JSON.stringify({name:selectedWorkout.name, exercises: selectedWorkout.workout}),
        headers: {'Content-Type': 'application/json'}
      }).then(function(resp){
            selectedWorkout.shareUrl = resp.data.shortURI;
            //TODO: Update this URL with swork.it
            var customMessage = $translate.instant('TRY_WORKOUT') + ', ' + resp.data.name + '. ' + $translate.instant('GET_IT') + ' http://m.sworkit.com/share?w=' + resp.data.shortURI;
            if (device){
              window.plugins.socialsharing.share(customMessage, function(){logActionSessionM('ShareCustomWorkout');}, null);
            } else {
              console.log('Share: http://m.sworkit.com/share?w=' + resp.data.name);
            }
          }, function(err) {
            navigator.notification.alert(
                  $translate.instant('PLEASE_RETRY'),  // message
                  nullHandler,         // callback
                  $translate.instant('SHARE_FAIL'),            // title
                  $translate.instant('OK')                  // buttonName
                );
      });
  }
  $scope.deleteCustom  = function(indexEl, customObj){
    var confirmDelete = $translate.instant('DELETE') + ' ' + customObj.name + '?';
    navigator.notification.confirm(
                '',
                 function(buttonIndex){
                  if (buttonIndex == 2){
                    PersonalData.GetCustomWorkouts.savedWorkouts.forEach(function(element, index, array){if (element.name == customObj.name){PersonalData.GetCustomWorkouts.savedWorkouts.splice(index, 1);localforage.setItem('customWorkouts', PersonalData.GetCustomWorkouts);}$scope.$apply()});                                
                    $ionicListDelegate.closeOptionButtons();
                  }
                 },
                confirmDelete,
                [$translate.instant('CANCEL_SM'),$translate.instant('OK')]
              );
  }
  $scope.editCustom = function(indexEl, customObj) {
    if (device && device.platform.toLowerCase() == 'ios'){
      $timeout(function(){
        $ionicActionSheet.show({
         buttons: [
           { text: '<b>'+ $translate.instant("ADD_REMOVE")+'</b>' },
           { text: $translate.instant("RENAME_WORKOUT") },
         ],
         titleText: $translate.instant("EDIT_CUSTOM"),
         cancelText: $translate.instant("CANCEL_SM"),
         buttonClicked: function(indexNum) {
           $scope.actionButtonClicked(indexNum);
           return true;
         },
         cancel: function(indexNum) {
           $scope.actionCancel(indexNum);
           return true;
         }
       });
        $scope.actionPopup = {
          close : function(){
          }
        }
      }, 800);
    } else{
      $scope.actionPopup = $ionicPopup.show({
        title: $translate.instant('EDIT_CUSTOM'),
        subTitle: '',
        scope: $scope,
        template: '<div class="action-button" style="padding-bottom:10px"><button class="button button-full button-stable" ng-click="actionButtonClicked(0)">{{"ADD_REMOVE" | translate}}</button><button class="button button-full button-stable" ng-click="actionButtonClicked(2)">{{"RENAME_WORKOUT" | translate}}</button><button class="button button-full button-stable" ng-click="actionCancel()" style="text-align:center;padding-left:0px;margin-bottom:-10px">{{"CANCEL_SM" | translate}}</button></div>'
      });
      $timeout(function(){
        angular.element(document.getElementsByTagName('body')[0]).addClass('popup-open');
      }, 500)
    }
    $scope.actionButtonClicked = function(indexNum) {
      var selectedItem = customObj;
       if (indexNum == 0){
        $scope.currentCustom = selectedItem.workout;
        $scope.editMode = true;
        $scope.customName = selectedItem.name;
        $scope.createCustom();
        $scope.actionPopup.close();
       } else if (indexNum == 1 || indexNum == 2){
          $ionicPopup.prompt({
             title: $translate.instant('NEW_NAME'),
             cancelText: $translate.instant('CANCEL_SM'),
             inputType: 'text',
             template: '<input ng-model="data.response" type="text" autofocus class="ng-pristine ng-valid">',
             inputPlaceholder: selectedItem.name,
             okText: $translate.instant('SAVE'),
             okType: 'energized'
             }).then(function(res) {
                if (res && res.length > 1){
                  selectedItem.name = res;
                  localforage.setItem('customWorkouts', PersonalData.GetCustomWorkouts);
                }
                $scope.actionPopup.close();
          });
       }
       $ionicListDelegate.closeOptionButtons();
     },
     $scope.actionCancel = function(indexNum) {
       $ionicListDelegate.closeOptionButtons();
       $scope.actionPopup.close();
     };
     $scope.actionDestructiveButtonClicked  = function(indexNum) {
       var selectedItem = customObj;
       PersonalData.GetCustomWorkouts.savedWorkouts.forEach(function(element, index, array){if (element.name == selectedItem.name){PersonalData.GetCustomWorkouts.savedWorkouts.splice(index, 1);localforage.setItem('customWorkouts', PersonalData.GetCustomWorkouts);}});                                
       $ionicListDelegate.closeOptionButtons();
       $scope.actionPopup.close();
     }
  }
  $scope.createCustom = function(){
    $ionicLoading.show({
                  template: $translate.instant('GATHERING'),
                  animation: 'fade-in',
                  showBackdrop: true,
                  maxWidth: 200,
                  duration:5000
              });
    $timeout(function(){
              $scope.createCustomOpen();
             }, 500);
  }
  $scope.createCustomOpen = function(){
    $ionicModal.fromTemplateUrl('custom-workout.html', function(modal) {
                                  $scope.customModal = modal;
                                  }, {
                                  scope:$scope,
                                  animation: 'fade-implode',
                                  focusFirstInput: false,
                                  backdropClickToClose: false,
                                  hardwareBackButtonClose: false
                                  });
    $timeout(function(){
              $scope.openCreateCustom();
             }, 100);
    $scope.openCreateCustom = function() {
      $scope.customModal.show();
    };
    $scope.cancelCreateCustom = function() {
      $scope.customModal.hide();
      $scope.editMode = false;
      $scope.currentCustom = UserService.getCurrentCustom();
      PersonalData.GetWorkoutArray.workoutArray = $scope.selectedExercises();
      $timeout(function(){
                     $scope.customModal.remove();
                     }, 1000);
    };
    $scope.resetCustom = function() {
      if (device){
            navigator.notification.confirm(
              $translate.instant('CLEAR_SELECTIONS'),
               function(buttonIndex){
                if (buttonIndex == 2){
                  for(var exercise in exerciseObject) {
                    exerciseObject[exercise].selected = false;
                  }
                  $scope.totalSelected = 0;
                  PersonalData.GetWorkoutArray.workoutArray = [];
                  $scope.$apply();
                }
               },
              $translate.instant('RESET_CUSTOM'),
              [$translate.instant('CANCEL_SM'),$translate.instant('OK')]
            );
      }else{
        $ionicPopup.confirm({
             title: $translate.instant('RESET_CUSTOM'),
             template: '<p class="padding">'+$translate.instant("CLEAR_SELECTIONS")+'</p>',
             okType: 'energized',
             okText: $translate.instant('OK'),
             cancelText: $translate.instant('CANCEL_SM')
           }).then(function(res) {
             if(res) {
              for(var exercise in exerciseObject) {
                exerciseObject[exercise].selected = false;
              }
              PersonalData.GetWorkoutArray.workoutArray = [];
              $scope.$apply();
             }
           });
      }
    }
    for(var exercise in exerciseObject) {
        exerciseObject[exercise].selected = false;
    }
    $scope.currentCustom.forEach(function(element, index, array){exerciseObject[element].selected = true});
    $scope.selectedExercises = function selectedExercises() {
      var arrUse = [];
      for(var thisExercise in exerciseObject) {
        if (exerciseObject[thisExercise].selected){
          arrUse.push(translations['EN'][exerciseObject[thisExercise].name]);
        }
      }
      return arrUse;
    };
    $scope.totalSelected = $scope.selectedExercises().length;
      $scope.mathSelected = function(addSubtract){
        if (addSubtract){
          $scope.totalSelected++;
        } else {
          $scope.totalSelected--;
        }
    }
    $timeout(function(){
              $ionicLoading.hide();
             }, 1000);
    $scope.$on('$ionicView.leave', function() {
      $scope.customModal.remove();
    });
    $timeout(function(){
            angular.element(document.getElementsByTagName('body')[0]).removeClass('loading-active');
              $ionicLoading.hide();
             }, 6000);
    };
    $scope.searchTyping = function(typedthings){

    }
    $scope.searchSelect = function(suggestion){
       $scope.slideTo($scope.allExercises.indexOf(suggestion), suggestion);
    }
    $scope.slideTo = function(location, suggestion) {
      var newLocation = $location.hash(location);
      var keyObject = translations[PersonalData.GetUserSettings.preferredLanguage];
      keyObject.getKeyByValue = function( value ) {
        for( var prop in this ) {
          if( this.hasOwnProperty( prop ) ) {
            if( this[ prop ] === value )
            return prop;
          }
        }
      }
      var foundKey = keyObject.getKeyByValue(suggestion);
      var keyInEN = translations['EN'][foundKey];
      exerciseObject[keyInEN].selected = true;

      $timeout( function(){
        $ionicScrollDelegate.$getByHandle('createScroll').anchorScroll("#"+newLocation);
        $scope.totalSelected = $scope.selectedExercises().length;
      },50);
    };
    $scope.toggleAll = function(shortCat, indexN){
      var indexID = angular.element(document.getElementById('cat' + indexN));
      indexID.toggleClass('group-active');
      if (indexID.hasClass('group-active')){
        $scope.exerciseCategories[indexN].exercises.forEach(function(element, index, array){element.selected = true});
      } else {
        $scope.exerciseCategories[indexN].exercises.forEach(function(element, index, array){element.selected = false});
      }
      $scope.totalSelected = $scope.selectedExercises().length;
    }
    $scope.saveCustom = function() {
      PersonalData.GetWorkoutArray.workoutArray = $scope.selectedExercises();
      localforage.setItem('currentCustomArray', PersonalData.GetWorkoutArray);
      if ($scope.editMode){
        var fillTitle =  $translate.instant('SAVE_CHANGE') + '  ' + $scope.customName + '?';
        if (device){
            navigator.notification.confirm(
              '',
               function(buttonIndex){
                if (buttonIndex == 2 && $scope.selectedExercises().length > 0){
                  PersonalData.GetCustomWorkouts.savedWorkouts.forEach(function(element, index, array){if (element.name == $scope.customName){element.workout = $scope.selectedExercises();localforage.setItem('customWorkouts', PersonalData.GetCustomWorkouts);}});                                
                  $scope.editMode = false;
                  $scope.currentCustom = UserService.getCurrentCustom();
                  $scope.customModal.hide();
                  $scope.workoutLengths();
                  $timeout(function(){
                     $scope.customModal.remove();
                     }, 1000);
                }
               },
              fillTitle,
              [$translate.instant('CANCEL_SM'),$translate.instant('OK')]
            );
        } else {
            $ionicPopup.confirm({
               title: fillTitle,
               template: '',
               okType: 'energized',
               okText: $translate.instant('OK'),
               cancelText: $translate.instant('CANCEL_SM')
             }).then(function(res) {
               if (res && $scope.selectedExercises().length > 0){
                  PersonalData.GetCustomWorkouts.savedWorkouts.forEach(function(element, index, array){if (element.name == $scope.customName){element.workout = $scope.selectedExercises();localforage.setItem('customWorkouts', PersonalData.GetCustomWorkouts);}});                                
                  $scope.editMode = false;
                  $scope.currentCustom = UserService.getCurrentCustom();
                  $scope.customModal.hide();
                  $scope.workoutLengths();
                  $timeout(function(){
                     $scope.customModal.remove();
                     }, 1000);
                }
             });
           }
      } else {
        $ionicPopup.prompt({
                     title: $translate.instant('NAME_THIS'),
                     text: $translate.instant('REPLACE_CUSTOM'),
                     cancelText: $translate.instant('CANCEL_SM'),
                     inputType: 'text',
                     template: '<input ng-model="data.response" type="text" autofocus class="ng-pristine ng-valid">',
                     inputPlaceholder: 'name',
                     okText: $translate.instant('SAVE'),
                     okType: 'energized'
                     }).then(function(res) {
                        if (res && res.length > 1 && $scope.selectedExercises().length > 0){
                          PersonalData.GetCustomWorkouts.savedWorkouts[0] = {"name": res,"workout": $scope.selectedExercises()};
                          localforage.setItem('customWorkouts', PersonalData.GetCustomWorkouts);
                          if (!$scope.editMode){
                            logActionSessionM('DesignCustomWorkout');
                          }
                          $scope.editMode = false;
                          $scope.currentCustom = UserService.getCurrentCustom();
                          $scope.customModal.hide();
                          $scope.workoutLengths();
                          $timeout(function(){
                           $scope.customModal.remove();
                           }, 1000);
                        }
                        });
    };
    //Was remove from Pro, not sure if it should be here or not
    // $scope.$on('$destroy', function() {
    //   $scope.customModal.remove();
    // });
    // $timeout(function(){
    //          $scope.openCreateCustom();
    //          }, 0);
  }

  $scope.previewCustom = function(){
    angular.element(document.getElementsByTagName('body')[0]).addClass('preview-popup');
    $ionicPopup.alert({
               title: $translate.instant('SELECTED'),
               scope: $scope,
               template: '<div class="selected-exercises"><p class="item" ng-repeat="selExercise in selectedExercises()">{{selExercise | translate}}</p></div>',
               okType: 'energized',
               okText: $translate.instant('OK')
             }).then(function(res) {
              angular.element(document.getElementsByTagName('body')[0]).removeClass('preview-popup');
             });
  }

  $scope.reorderCustom = function(passedWorkout){
    $scope.passedWorkoutSave = passedWorkout;
    $ionicModal.fromTemplateUrl('custom-workout-reorder.html', function(modal) {
                                  $scope.customModal2 = modal;
                                  }, {
                                  scope:$scope,
                                  animation: 'fade-implode',
                                  focusFirstInput: false,
                                  backdropClickToClose: false,
                                  hardwareBackButtonClose: false
                                  });
    
    $scope.reorderWorkout = passedWorkout.workout;
    $scope.data = {showReorder:true,showDelete: false};
    $scope.moveItem = function(item, fromIndex, toIndex) {
      $scope.reorderWorkout.splice(fromIndex, 1);
      $scope.reorderWorkout.splice(toIndex, 0, item);
    };
    $scope.onItemDelete = function(item) {
      $scope.reorderWorkout.splice($scope.reorderWorkout.indexOf(item), 1);
    };
    $scope.openReorderCustom2 = function() {
      $scope.customModal2.show();
    };
    $scope.cancelReorderCustom = function() {
      $scope.customModal2.hide();
      $scope.editMode = false;
      $timeout(function(){
       $scope.customModal2.remove();
      }, 1000);
    };
    $scope.saveReorder = function() {
        var fillTitle = $translate.instant('SAVE_CHANGE') + '  ' + $scope.customName + '?';
        if (device){
          navigator.notification.confirm(
          '',
           function(buttonIndex){
            if (buttonIndex == 2){
                                PersonalData.GetCustomWorkouts.savedWorkouts.forEach(function(element, index, array){if (element.name == $scope.passedWorkoutSave.name){element.workout = $scope.passedWorkoutSave.workout;localforage.setItem('customWorkouts', PersonalData.GetCustomWorkouts);}});                                
                  $scope.editMode = false;
                  $scope.customModal2.hide();
                  $scope.workoutLengths();            }
           },
          fillTitle,
          [$translate.instant('CANCEL_SM'),$translate.instant('OK')]
          );
        } else{
            $ionicPopup.confirm({
               title: fillTitle,
               template: '',
               okType: 'energized',
               okText: $translate.instant('OK'),
               cancelText: $translate.instant('CANCEL_SM')
             }).then(function(res) {
               if (res){
                  PersonalData.GetCustomWorkouts.savedWorkouts.forEach(function(element, index, array){if (element.name == $scope.passedWorkoutSave.name){element.workout = $scope.passedWorkoutSave.workout;localforage.setItem('customWorkouts', PersonalData.GetCustomWorkouts);}});                                
                  $scope.editMode = false;
                  $scope.customModal2.hide();
                  $scope.workoutLengths();
                }
             });
        }
    };
    cordova.plugins.Keyboard.hideKeyboardAccessoryBar(false);
    $scope.$on('$ionicView.leave', function() {
      cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);
      $scope.customModal2.remove();
    });
    $timeout(function(){
             $scope.openReorderCustom2();
             }, 0);
  }

  $scope.selectCustom = function(indexEl, selectedCustom){
    LocalData.GetWorkoutTypes.customWorkout = { id: 100, activityWeight: 6, activityMFP: "134026252709869", activityNames: selectedCustom.name, exercises: selectedCustom.workout},
    $location.path('/app/home/2/customWorkout');
    // $scope.selectPopup = $ionicPopup.show({
    //   title: selectedCustom.name,
    //   subTitle: '',
    //   scope: $scope,
    //   template: '<div class="padding select-popup" style="padding-bottom:20px"><button class="button button-full button-stable uppercase" style="color:#FF8614" ng-click="selectActionClicked(0)">{{"BEGIN" | translate}} {{"WORKOUT_CAP" | translate}}</button><button class="button button-full button-stable uppercase" style="color:#24CC92" ng-click="selectActionClicked(1)">{{"EDIT" | translate}}</button><button class="button button-full button-stable uppercase" style="color:#14CEFF" ng-click="selectActionClicked(2)">{{"SHARE" | translate}}</button><button class="button button-full button-stable uppercase" style="color:#CC2511" ng-click="selectActionClicked(3)">{{"DELETE" | translate}}</button><button class="button button-full button-stable uppercase" ng-click="selectActionClicked(4)" style="text-align:center;margin-bottom:-10px">{{"CANCEL_SM" | translate}}</button></div>'
    // });
    $scope.selectActionClicked = function(indexNum){
      if (indexNum == 0){
        LocalData.GetWorkoutTypes.customWorkout = { id: 100, activityWeight: 6, activityMFP: "134026252709869", activityNames: selectedCustom.name, exercises: selectedCustom.workout},
        $location.path('/app/home/2/customWorkout');
        $scope.selectPopup.close();
      } else if (indexNum == 1){
        $scope.editCustom(indexNum, selectedCustom);
        $scope.selectPopup.close();
      } else if (indexNum == 2){
        $scope.shareCustom(indexNum, selectedCustom);
        $scope.selectPopup.close();
      } else if (indexNum == 3){
        $scope.selectPopup.close();
        $scope.deleteCustom(indexNum, selectedCustom);
      } else {
        $scope.selectPopup.close();
      }
    }
  }

  $scope.addCustomWorkout = function(workid, index){
    var selectWorkout;
    $scope.downloadedWorkouts.forEach(function(element, index, array){if (element.shortURI == workid){selectWorkout = element}});
    if (device){
        navigator.notification.confirm(
                                       $translate.instant('REPLACE_CUSTOM'),
                                       function(button){
                                        if (button == 2){
      var notifyEl = angular.element(document.getElementById('item' + index)).removeClass('ion-plus').addClass('ion-checkmark');
      $timeout(function(){
        angular.element(document.getElementById('item' + index)).removeClass('ion-checkmark').addClass('ion-plus');
      }, 3000)

      PersonalData.GetCustomWorkouts.savedWorkouts[0] = {"name": selectWorkout.name,"workout": selectWorkout.exercises};
      localforage.setItem('customWorkouts', PersonalData.GetCustomWorkouts);
      trackEvent('Download Custom', selectWorkout.workout_name, 0);
      $scope.workoutLengths();
      $scope.$apply();
    }},
                                       $translate.instant('INSTALL'),
                                       [$translate.instant('CANCEL_SM'),$translate.instant('OK')]
                                       );
    }
  }
  $scope.shareWorkout = function(workid){
    var selectWorkout;
    $scope.downloadedWorkouts.forEach(function(element, index, array){if (element.shortURI == workid){selectWorkout = element}});
    //TODO: Update this URL with swork.it
    workoutMessage = $translate.instant('THIS') + ' ' + $translate.instant(selectWorkout.name) + ' ' + $translate.instant('WORKOUT_AWESOME') + ': http://m.sworkit.com/share?w=' + selectWorkout.shortURI;
    if (device){
      window.plugins.socialsharing.share(workoutMessage, null, null);
    } else {
      console.log('Share: http://m.sworkit.com/share?w=' + selectWorkout.shortURI)
    }
  }
  $scope.updateDownloads = function(){
    getDownloadableWorkouts($http, true, $scope.optionSelected.ListType);
    $timeout(function(){
      $scope.$apply();
    }, 3000)
  }
  $scope.showFeatured = function(){
    $location.path('/app/custom/featured');
  }
  $scope.orPressed = function(){
    $scope.isPressed = true;
    $timeout(function(){
      $scope.isPressed = false;
    }, 1000);
  }
  $scope.$on('$ionicView.leave', function() {
    if(device){
      cordova.plugins.Keyboard.hideKeyboardAccessoryBar(false);
    }  
  });
  $scope.$on('$ionicView.beforeEnter', function() {
    $ionicSlideBoxDelegate.update();
  });
})

.controller('WorkoutCustom2Ctrl', function($rootScope, $scope, $ionicScrollDelegate, $location, $translate, $ionicPopup, $ionicListDelegate, $http, $ionicScrollDelegate, $timeout, filterFilter, UserService, WorkoutService) {
  $scope.customWorkouts = UserService.getCustomWorkoutList();
  $scope.listOptions = [
      { text: "FEATURED", value: "featured" },
      { text: "POPULAR", value: "popular" },
      { text: "STANDARD", value: "standard"}
  ];
  $scope.optionSelected = {
      listType : 'featured'
  };
  $scope.standardSelected = false;
  $scope.downloadedWorkouts = downloadableWorkouts;

  function newWorkoutObj() {
    this.name="";
    this.shortURI=false;
    this.description="";
    this.exercises="";
    this.credit={name:false,href:false,color:false};
    this.isEquipmentRequired=false;
    this.isFeatured=false;
    this.hiddenWorkout=false;
    this.priority=0;
    this.opens=0;
    this.downloads=0
  }

  $scope.toggleLists = function(){
    if ($scope.optionSelected.listType == 'popular'){
      $scope.downloadedWorkouts = popularWorkouts;
      $scope.standardSelected = false;
    } else if ($scope.optionSelected.listType == 'featured'){
      $scope.downloadedWorkouts = downloadableWorkouts;
      $scope.standardSelected = false;
    } else {
      $scope.downloadedWorkouts = $scope.fullList;
      $scope.standardSelected = true;
    }
  }
  $scope.openDownLink = function(url){
      window.open(url, '_blank', 'location=no,AllowInlineMediaPlayback=yes');
  }
  $scope.showExercises = function(workoutPassed, index){
    var notifyEl = angular.element(document.getElementById('item' + index));
    notifyEl.addClass('green-text');
    $timeout(function(){
      notifyEl.removeClass('green-text');
    }, 1000)
    var tempString = JSON.stringify(workoutPassed.exercises);
    tempString = tempString.replace(/"/g,' ');
    tempString = tempString.replace(/\[/g,'');
    tempString = tempString.replace(/\]/g,'');
    workoutPassed.exercises_view = tempString;
    workoutPassed.show = true;
  }
  $scope.hideExercises = function(workoutPassed){
    workoutPassed.show = false;
  }
  $scope.toggleExercises = function(workoutPassed, index){
    if (workoutPassed.show){
      $scope.hideExercises(workoutPassed);
    } else{
      $scope.showExercises(workoutPassed, index);
    }
  }
  $scope.addCustomWorkout = function(workid, index, standardWorkout){
    var selectWorkout;
    if (standardWorkout){
      selectWorkout = $scope.fullList[index];
    } else {
      $scope.downloadedWorkouts.forEach(function(element, index, array){if (element.shortURI == workid){selectWorkout = element}});      
    }
    if (device){
        navigator.notification.confirm(
                                       $translate.instant('REPLACE_CUSTOM'),
                                       function(button){
                                        if (button == 2){
      var notifyEl = angular.element(document.getElementById('item' + index)).removeClass('ion-plus').addClass('ion-checkmark');
      $timeout(function(){
        angular.element(document.getElementById('item' + index)).removeClass('ion-checkmark').addClass('ion-plus');
      }, 3000)

      PersonalData.GetCustomWorkouts.savedWorkouts[0] = {"name": selectWorkout.name,"workout": selectWorkout.exercises};
      localforage.setItem('customWorkouts', PersonalData.GetCustomWorkouts);
      trackEvent('Download Custom', selectWorkout.workout_name, 0);
      $location.path('/app/custom');
    }},
                                       $translate.instant('INSTALL'),
                                       [$translate.instant('CANCEL_SM'),$translate.instant('OK')]
                                       );
    }
  }
  $scope.shareWorkout = function(workid){
    var selectWorkout;
    $scope.downloadedWorkouts.forEach(function(element, index, array){if (element.shortURI == workid){selectWorkout = element}});
    //TODO: Update this URL with swork.it
    workoutMessage = $translate.instant('THIS') + ' ' + $translate.instant(selectWorkout.name) + ' ' + $translate.instant('WORKOUT_AWESOME') + ': http://m.sworkit.com/share?w=' + selectWorkout.shortURI;
    if (device){
      window.plugins.socialsharing.share(workoutMessage, null, null);
    } else {
      console.log('Share: http://m.sworkit.com/share?w=' + selectWorkout.shortURI)
    }
  }
  $scope.updateDownloads = function(){
    getDownloadableWorkouts($http, true, $scope.optionSelected.ListType);
    $timeout(function(){
      $scope.$apply();
    }, 3000)
  }

  $scope.fullList = [];
  $scope.fakePriority = 3141592;
  for (var workoutType = 0;workoutType < LocalData.GetWorkoutCategories.length; workoutType++){
      for (var thisWorkout = 0;thisWorkout < LocalData.GetWorkoutCategories[workoutType].workoutTypes.length; thisWorkout++){
       var useWorkoutObj = new newWorkoutObj(); 
        var currentWorkoutObj = LocalData.GetWorkoutTypes[LocalData.GetWorkoutCategories[workoutType].workoutTypes[thisWorkout]];
        useWorkoutObj.name = $translate.instant(currentWorkoutObj.activityNames);
        useWorkoutObj.description = $translate.instant(currentWorkoutObj.description);
        useWorkoutObj.priority = $scope.fakePriority;
        $scope.fakePriority--;
        if (!currentWorkoutObj.exercises && currentWorkoutObj.activityNames == "FULL"){
          useWorkoutObj.exercises = LocalData.GetWorkoutTypes.upperBody.exercises.concat(LocalData.GetWorkoutTypes.coreExercise.exercises).concat(LocalData.GetWorkoutTypes.lowerBody.exercises)
        } else {
          useWorkoutObj.exercises = currentWorkoutObj.exercises;
        }
        $scope.fullList.push(useWorkoutObj);
      }
  }

})

.controller('WorkoutTimeCtrl', function($rootScope, $scope, $stateParams,$location,$translate,$timeout,$ionicModal,$ionicPopup,$ionicPopover,WorkoutService, UserService) {
  LocalHistory.getCustomHistory.lastHomeURL = $location.$$url;
  $scope.Math = Math;
  $scope.adjustTimer = function(){
    var contentWidth = angular.element(document.getElementById('time-screen')).prop('offsetWidth');
    var screenHeight = window.innerHeight;
    $scope.size =  Math.min(contentWidth * .75,screenHeight * .4);
    angular.element(document.getElementById('minute-selection'))[0].style.fontSize = ($scope.size * .40) + 'px';
    angular.element(document.getElementById('minute-selection'))[0].style.height = ($scope.size * .40) + 'px';
    angular.element(document.getElementById('timer-minutes'))[0].style.fontSize = ($scope.size * .10) + 'px';
    angular.element(document.getElementById('minus-button'))[0].style.marginRight = ($scope.size / 2.1 - 35) + 'px';
    angular.element(document.getElementById('plus-button'))[0].style.marginLeft = ($scope.size / 2.1 -35) + 'px';
    $scope.areaWidth = contentWidth - 40;
    $scope.areaHeight = $scope.size;
  }
  $scope.adjustTimer();
  $scope.thisType = WorkoutService.getTypeName($stateParams.typeId);
  $scope.categoryTitle = LocalData.GetWorkoutCategories[$stateParams.categoryId].fullName;
  if ($stateParams.typeId == "customWorkout"){
    $scope.categoryTitle  = "STRENGTH";
  }
  $scope.typeName = $stateParams.typeId;
  $scope.advancedTiming = WorkoutService.getTimingIntervals();
  $scope.userSettings = UserService.getUserSettings();
  $scope.timeSelected = {minutes:$scope.userSettings.lastLength};
  $scope.scopeFirstOption = $scope.userSettings.timerTaps < 2 ? true : false;
  $scope.urxAvailable = true;
  if (ionic.Platform.isAndroid() && device){
    $scope.androidPlatform = true;
    if (isAmazon()){
      $scope.urxAvailable = false;
      window.appAvailability.check('com.spotify.music',function() {$scope.urxAvailable = true;},function(){});
    }
  } else{
    $scope.androidPlatform = false;
  }

  $scope.isToolTipTime = false;
  $scope.urxText = "Listen to motivating " + $translate.instant($scope.categoryTitle).toLowerCase() + " music";
  $scope.$on('$ionicView.enter', function () {
      if ($scope.userSettings.showAudioTip && !isAmazon()){
        $scope.userSettings.showAudioTip = false;
        if (PersonalData.GetUserSettings.preferredLanguage == 'EN'){
          $scope.isToolTipTime = true;
          $timeout(function(){
            $scope.isToolTipTime = false;
          }, 4000)
        }
      } else {
        $timeout(function(){
          checkVolume();
        }, 1000);  
      }
  });
  $scope.defaultAdd = 5;
  $scope.sevenTiming = WorkoutService.getSevenIntervals();
  $scope.yogaSelection = false;
  $scope.sevenMinuteSelection = false;
  $scope.times = {lengths: [{id:5, text:5}, {id:10, text:10}, {id:15, text:15}, {id:20, text:20}, {id:30, text:30}, {id:45, text:45}]}
  if ($stateParams.typeId == 'sevenMinute') {
    $scope.minuteArray = [7,14,21,28,35,42,49,56];
    $scope.sevenMinuteSelection = true;
    $scope.defaultAdd = 7;
    $scope.timeSelected.minutes = 7;
  } else{
    $scope.minuteArray = [5,10,15,20,25,30,35,40,45,50,55,60];
  }
  if ($stateParams.typeId == 'sunSalutation' || $stateParams.typeId == 'fullSequence' || $stateParams.typeId == 'runnerYoga'){
    $scope.yogaSelection = true;
  }
  $scope.returnX = function(mins){
    return (($scope.areaWidth/2) + (($scope.size/2)*(Math.cos(((mins-15)*6)*Math.PI/180))) - ($scope.size/8));
  }
  $scope.returnY = function(mins){
    return (($scope.areaHeight/2) + (($scope.size/2)*(Math.sin(((mins-15)*6)*Math.PI/180))) - ($scope.size/8));
  }
  $scope.setMinuteTime = function(num) {
    $scope.timeSelected.minutes = num;
    $scope.scopeFirstOption = false;
    $scope.userSettings.timerTaps++;
  }
  $scope.minusFive = function() {
    if ($scope.timeSelected.minutes > $scope.defaultAdd){
      $scope.timeSelected.minutes = $scope.timeSelected.minutes - $scope.defaultAdd;
    }
  }
  $scope.plusFive = function() {
    if ($scope.timeSelected.minutes < 60){
      $scope.timeSelected.minutes = $scope.timeSelected.minutes + $scope.defaultAdd;
    }
  }
  $scope.customLength = function (){
    $timeout(function(){
      angular.element(document.getElementById('minute-selection'))[0].focus();
    }, 200)
    if ($scope.androidPlatform){
      cordova.plugins.Keyboard.show();
    }
  }
  $scope.clearTime = function(){
    $scope.timeSelected.minutes = '';
  }
  $scope.calcComplete = function (){
    var calcResult = Math.max( 1 - ($scope.timeSelected.minutes/60.00000001), 0);
    return calcResult;
  }
  $scope.beginWorkout = function (){
    $location.path('/app/home/' + $stateParams.categoryId + '/' + $stateParams.typeId + '/' + $scope.timeSelected.minutes + '/workout');
  }
  $scope.validateTime = function(){
    if ($scope.timeSelected.minutes< 1 || $scope.timeSelected.minutes > 1000 || $scope.timeSelected.minutes == ''){
      $scope.timeSelected.minutes = $scope.defaultAdd;
    }
    if ($scope.androidPlatform){
      cordova.plugins.Keyboard.close();
    }
  }
  window.addEventListener('native.keyboardhide', keyboardHideHandler);

  function keyboardHideHandler(e){
    if (isNaN($scope.timeSelected.minutes)){
      $scope.timeSelected.minutes = $scope.defaultAdd;
      $scope.$apply();
    }
  }

  $scope.launchURX = function(){
    if (device){
      cordova.exec(function (){trackEvent('URX Launched', $scope.categoryTitle.toLowerCase(), 0);}, function (){}, "URX", "searchSongs", [ $scope.categoryTitle.toLowerCase() + ' "sworkit workout playlist" OR workout playlist action:ListenAction']);
    }
  }

  var orientationTimeChange = function(){
    $timeout(function(){
      $scope.adjustTimer();
    }, 500)
  }
  
  window.addEventListener("orientationchange", orientationTimeChange , false);

  $scope.$on('$ionicView.leave', function() {
    if ($scope.sevenMinuteSelection) {
      localforage.setItem('timingSevenSettings', $scope.sevenTiming);
    } else {
      PersonalData.GetUserSettings.lastLength = $scope.timeSelected.minutes;
      localforage.setItem('userSettings', PersonalData.GetUserSettings);
      localforage.setItem('timingSettings', TimingData.GetTimingSettings);
    } 
    window.removeEventListener('native.keyboardhide', $scope.keyboardHideHandler);
    window.removeEventListener("orientationchange", orientationTimeChange , false);
  });
})

.controller('WorkoutCtrl', function($rootScope, $scope, $ionicHistory, $stateParams,$ionicModal,$translate,$ionicPopup,$ionicPlatform,$ionicSideMenuDelegate, $http, $ionicSlideBoxDelegate, $ionicNavBarDelegate, $sce,$location,$timeout,$interval, $state, WorkoutService, UserService) {
  $ionicNavBarDelegate.showBackButton(false);
  $scope.transitionStatus = false;
  $scope.title = "<img src='img/sworkit_logo.png'/>"
  $scope.videoAddress = 'video/Blank.mp4';
  $scope.resizeOptions = {grow: false, shrink:true, defaultSize: 30};
  $scope.dimensions = {inHeight: window.innerHeight, inWidth: window.innerWidth}; 
  $scope.isPortrait = true;
  $scope.urxAvailable = true;
  if (device){
    try{
      cordova.plugins.screenorientation.unlockOrientation();
    } catch(e){
      screen.unlockOrientation();
    }
  }
  $scope.adjustTimer = function(){
    var timerHeight = $scope.dimensions.inHeight * .25;
    if ($scope.isPortrait){
      $scope.size =  Math.min(Math.max(timerHeight * .6,
                    60
                  ), timerHeight * .9);     
     } else {
      $scope.size = Math.min(Math.max($scope.dimensions.inHeight * .3,
                    90
                  ), 140);   
     }

    $scope.adjustTimerMinutes();
    if ($scope.dimensions.inWidth > 415 && $scope.dimensions.inHeight > 500){
      //TODO: this isn't really working on iPad. defaultSize not getting to auto-font-size
      $scope.resizeOptions.defaultSize = 42;
    } else {
      $scope.resizeOptions.defaultSize = 30;
    }
  }
  $scope.adjustTimerMinutes = function(){
    var adjustmentAmount = Math.max(($scope.size * .40), 35);
    if ($scope.singleTimer.minutes > 0 || $scope.advancedTiming.breakTime > 59){
      angular.element(document.getElementById('timer-number-h1'))[0].style.fontSize = ($scope.size - 50) + 'px';
    } else {
      angular.element(document.getElementById('timer-number-h1'))[0].style.fontSize = ($scope.size - adjustmentAmount) + 'px';
    } 
  }
  $scope.setVideo = function(){
    var portraitMode = (ionic.viewport.orientation() == 0 || ionic.viewport.orientation() == 180) ? true : false;
    if (portraitMode){
      $scope.isPortrait = true;
      $scope.dimensions.inHeight = Math.max(window.innerHeight, window.innerWidth);
      $scope.dimensions.inWidth = Math.min(window.innerHeight, window.innerWidth);
      $ionicNavBarDelegate.showBar(true);
      if (ionic.Platform.isIOS() && device){
        StatusBar.show()
      }
    } else{
      $scope.isPortrait = false;
      $scope.showControls = true;
      $scope.controlTimeout = $timeout(function(){
        $scope.showControls = false;
      }, 6000);
      $scope.dimensions.inWidth = Math.max(window.innerHeight, window.innerWidth);
      $scope.dimensions.inHeight = Math.min(window.innerHeight, window.innerWidth);
      $ionicNavBarDelegate.showBar(false);
      if (ionic.Platform.isIOS() && device){
        StatusBar.hide()
      }
    }
    if (ionic.Platform.isAndroid()){
      var linkto = angular.element(document.getElementById('linkto'));
      var inlineVid = angular.element(document.getElementById('inlinevideo'));
      var vidBackground = angular.element(document.getElementById('video-background'));
      var imageOnly = angular.element(document.getElementById('image-only'));
      if(ionic.viewport.orientation() == 0 || ionic.viewport.orientation() == 180){
        var percentage = 98;
        var widthToUse = (percentage / 100) * $scope.dimensions.inWidth;
        var heightToUse = widthToUse / (720/404);
        var bHeight = 45;
        inlineVid.css('width', (widthToUse + 'px'));
        vidBackground.css('width', (widthToUse + 'px'));
        inlineVid.css('max-height', (heightToUse + 8 + 'px'));
        vidBackground.css('max-height', (heightToUse+ 8 + 'px'));
        inlineVid.css('min-width', (widthToUse + 'px'));
        vidBackground.css('min-width', (widthToUse + 'px'));
        inlineVid.css('max-width', (widthToUse + 'px'));
        vidBackground.css('max-width', (widthToUse + 'px'));
        inlineVid.css('left', '0px');
        linkto.css('left', '1%');
        linkto.css('min-height', bHeight + '%');
        linkto.css('margin-top', '0px');
        var adWidth = Math.min($scope.dimensions.inWidth, 480);
        angular.element(document.getElementById('workout-ad-container')).css('width', adWidth + 'px');
      } else {
        var widthToUse = $scope.dimensions.inWidth * .80;
        var heightToUse = widthToUse / (720/404);
        var videoMargin = Math.max(($scope.dimensions.inHeight - heightToUse) / 2, 45);
        var bHeight = 25;
        if ($scope.advancedTiming.autoPlay){
          inlineVid.css('width', (heightToUse + 8 + 'px'));
          vidBackground.css('width', (widthToUse + 'px'));
          inlineVid.css('max-height', (heightToUse + 8 + 'px'));
          vidBackground.css('max-height', (heightToUse+ 8 + 'px'));
          inlineVid.css('min-width', (widthToUse + 'px'));
          vidBackground.css('min-width', (widthToUse + 'px'));
          inlineVid.css('max-width', (widthToUse + 'px'));
          vidBackground.css('max-width', (widthToUse + 'px'));
          linkto.css('left', '0%');
          inlineVid.css('left', '0px');
          linkto.css('left', '1%');
          linkto.css('min-height', bHeight + '%');
        } else {
          imageOnly.css('width', (widthToUse + 'px'));
          imageOnly.css('max-height', (heightToUse + 8 + 'px'));
          imageOnly.css('min-width', (widthToUse + 'px'));
          imageOnly.css('max-width', (widthToUse + 'px'));
          linkto.css('left', '10%');
        }
        var adWidth = Math.min($scope.dimensions.inWidth, 170);
        angular.element(document.getElementById('workout-ad-container')).css('width', adWidth + 'px');
        linkto.css('margin-top', videoMargin + 'px');
        if (videoMargin > 45){
          angular.element(document.getElementById('next-exercise-id')).css('margin-top', ((videoMargin - 45) * -1) + 'px');
        }
        linkto.css('min-height', bHeight + '%');
      }

    }
    $scope.adjustTimer();
  }
  $scope.$on('$ionicView.enter', function () {
    $ionicSideMenuDelegate.canDragContent(false);
  });
  angular.element(document.getElementsByTagName('body')[0]).addClass('workout-bar');
  $scope.direction = false;
  $scope.strokeWidth = 5;
  $scope.stroke = '#FF8614';
  $scope.background = '#EEEEEE';
  $scope.totalWidth = 100;
  $scope.counterClockwise = true;
  LocalHistory.getCustomHistory.lastHomeURL = $location.$$url;
  $scope.healthKitData = {healthKitAvailable: false, showHealthKitOption: false, healthKitStatus: ''}
  if (!ionic.Platform.isAndroid()) {
    if (device){
      window.plugins.healthkit.available(
                                               function(result){
                                                if (result == true){
                                                  $scope.healthKitData.healthKitAvailable = true;
                                                }
                                               },
                                               function(){
                                                $scope.healthKitData.healthKitAvailable = false;
                                               }
                                        );
    } else {
      //Available in browser for testing purposes
      $scope.healthKitData.healthKitAvailable = true;
    }
  } else {
    if (isAmazon()){
      $scope.urxAvailable = false;
      window.appAvailability.check('com.spotify.music',function() {$scope.urxAvailable = true;},function(){});
    }
  }
  $scope.advancedTiming = WorkoutService.getTimingIntervals();
  $scope.kindleDevice = false;
  $scope.androidHeader = function(){
    if (ionic.Platform.isAndroid()){
      if (device){
        document.querySelectorAll("drawer")[0].attributes.candrag.value = false || false;
      }
      $scope.androidPlatform = true;
      $scope.iOSPlatform = false;
      angular.element(document.getElementsByClassName('title')).addClass('no-nav');
      //$ionicNavBarDelegate.align('center');
      if (ionic.Platform.version() >= 4.4){
        $scope.isKitKat = true;
      } else{
        $scope.isKitKat = false;
      }
      if (isKindle()){
        $scope.kindleDevice = true;
      }
    } else{
      $scope.androidPlatform = false;
      $scope.iOSPlatform = true;
    }
  }
  $scope.androidHeader();
  
  $timeout(function(){
    $scope.androidHeader();
  }, 800);
  $scope.timesUsedVar = parseInt(window.localStorage.getItem('timesUsed'));
  $scope.userSettings = UserService.getUserSettings();
  $scope.googleFitSettings = UserService.getFitSettings();
  $scope.audioSettings = UserService.getAudioSettings();
  $scope.sevenTiming = WorkoutService.getSevenIntervals();
  $scope.previousExercise = false;
  $scope.endModalOpen = false;
  $scope.unloadQueue = [];
  $scope.isAutoStart = $scope.advancedTiming.autoStart;
  $scope.beginNotification = false;
  $scope.yogaSelection = false;
  $scope.helpText = false;
  $scope.changeText = false;
  if (globalSworkitAds){
    $scope.isAdCampaign = globalSworkitAds.isRunning;
    $scope.isEndAdCampaign = globalSworkitAds.isEndRunning;
    $scope.isAudioCampaign = globalSworkitAds.audioRunning;
    $scope.isAdMobRunning = globalSworkitAds.useAdMob;
    $scope.isMoPubRunning = globalSworkitAds.useMoPub;
  } else {
    $scope.isAdCampaign = false;
    $scope.isEndAdCampaign = false;
    $scope.isAudioCampaign = false;
    $scope.isAdMobRunning = false;
    $scope.isMoPubRunning = false;
  }
  if ($scope.isAdCampaign && globalSworkitAds.imageSuccessWorkout){
    $scope.workoutAdImage = cordova.file.dataDirectory + 'ads/' + globalSworkitAds.adWorkoutImageName;    
  } else {
    $scope.workoutAdImage = "img/sworkit-pro-ad-workout-screen.jpg";    
  }
  var allWorkouts = WorkoutService.getWorkoutsByType();
  $scope.chosenWorkout = allWorkouts[$stateParams.typeId];
  for (i=0;i<$scope.chosenWorkout.exercises.length;i++) {
    if (exerciseObject[$scope.chosenWorkout.exercises[i]] == null) {
      $scope.chosenWorkout.exercises.splice(i,1);
    }
  }
  //Get workout array
  $scope.currentWorkout = [];
  if ($scope.chosenWorkout.exercises){
    $scope.currentWorkout = $scope.currentWorkout.concat($scope.chosenWorkout.exercises);
  } else if ($stateParams.typeId == "fullBody"){
    $scope.currentWorkout = $scope.currentWorkout.concat(allWorkouts['upperBody'].exercises.concat(allWorkouts['lowerBody'].exercises,allWorkouts['coreExercise'].exercises));
  } else if ($stateParams.typeId == "anythingGoes"){
    $scope.currentWorkout = $scope.currentWorkout.concat(allWorkouts['upperBody'].exercises.concat(allWorkouts['lowerBody'].exercises,allWorkouts['coreExercise'].exercises,allWorkouts['stretchExercise'].exercises,allWorkouts['backStrength'].exercises,allWorkouts['cardio'].exercises,allWorkouts['pilatesWorkout'].exercises));
  }
  if ($stateParams.typeId == "quickFive"){
    checkVolume();
  }
  if ($scope.currentWorkout.length == 1){
    $scope.currentWorkout = $scope.currentWorkout.concat($scope.currentWorkout);
  }
  //Randomize Workouts
  if ($stateParams.typeId == 'headToToe' || $stateParams.typeId == 'sevenMinute' || $stateParams.typeId == 'sunSalutation' || $stateParams.typeId == 'runnerYoga' || $stateParams.typeId == 'fullSequence'){
  } else {
     if($scope.advancedTiming.randomizationOption || !$scope.advancedTiming.customSet){
        if ($stateParams.typeId == "upperBody"){
          var pushupBased = ["Push-ups","Diamond Push-ups","Wide Arm Push-ups","Alternating Push-up Plank","One Arm Side Push-up", "Dive Bomber Push-ups","Shoulder Tap Push-ups", "Spiderman Push-up", "Push-up and Rotation"];
          var nonPushup = ["Overhead Press","Overhead Arm Clap","Tricep Dips","Jumping Jacks", "Chest Expander", "T Raise","Lying Triceps Lifts","Reverse Plank","Power Circles","Wall Push-ups"]
          pushupBased = pushupBased.sort(function() { return 0.5 - Math.random() });
          nonPushup = nonPushup.sort(function() { return 0.5 - Math.random() });
          var mergedUpper = mergeAlternating(pushupBased,nonPushup)
          $scope.currentWorkout = mergedUpper;
        } else{
          $scope.currentWorkout = $scope.currentWorkout.sort(function() { return 0.5 - Math.random() });
        }
     }
  }
  
  var startedWorkout = [];
  startedWorkout = startedWorkout.concat($scope.currentWorkout);
  $scope.hiddenURL = '';
  $scope.extraSettings = WorkoutService.getTimingIntervals();
  $scope.showTiming = function(){
    $scope.stopTimer();
    $interval.cancel($scope.transitionCountdown);
    $timeout.cancel($scope.delayStart);
    $scope.transitionStatus = false;
    $scope.timerDelay = null;
    showTimingModal($scope,$ionicModal,$timeout, WorkoutService, true);
  }
  $scope.endWorkout = function(){
  $scope.endModalOpen = true;
  $scope.showWeightAdjust = globalFirstOption && PersonalData.GetUserSettings.weight == 150;
  if ($scope.isEndAdCampaign && globalSworkitAds.imageSuccess) {
    $scope.callToActionImage = cordova.file.dataDirectory + 'ads/' + globalSworkitAds.adActionImageName;
    $scope.callToActionText = globalSworkitAds.adActionText;
    $scope.hiddenURL = window.open('http://sworkit.com/app', '_blank', 'hidden=yes,AllowInlineMediaPlayback=yes,toolbarposition=top');
  } else {
    $scope.isEndCampaign = false;
  }
  if (device && $scope.isAdMobRunning) {
    AdMob.hideBanner()
  }
  if (device && $scope.isMoPubRunning && MoPub) {
    MoPub.hideBanner()
  }
  $ionicModal.fromTemplateUrl('workout-complete.html', function(modal) {
                                $scope.endModal = modal;
                                }, {
                                scope:$scope,
                                animation: 'fade-implode',
                                focusFirstInput: false,
                                backdropClickToClose: false,
                                hardwareBackButtonClose: false
                                });
      $scope.openModal = function() {
        $scope.stopTimer();
        $interval.cancel($scope.transitionCountdown);
        $timeout.cancel($scope.delayStart);
        $scope.transitionStatus = false;
        $timeout( function() {
          $ionicSlideBoxDelegate.update();
        },0);
          var mathComp = ($stateParams.timeId * 60) - ((($scope.totalTimer.minutes) * 60) + $scope.totalTimer.seconds);
          $scope.timeToAdd = Math.round( (mathComp / 60) * 2) / 2.0;
          if ($scope.timeToAdd > 0){
                var kilograms;
                var burnValue = $scope.chosenWorkout.activityWeight;
                kilograms=PersonalData.GetUserSettings.weight / 2.2;
                $scope.minutesCompleted = $scope.timeToAdd / 60.0;
                $scope.burn = Math.round(burnValue*kilograms*$scope.minutesCompleted);
          }
          else{
              $scope.burn = 0;
          }
          $scope.burnRounded = Math.round($scope.burn);
          $scope.timeToAddRounded = Math.round($scope.timeToAdd);
          if ($scope.workoutComplete){
            if ($scope.userSettings.mfpStatus){
              $timeout(function(){$scope.syncMFP();}, 0);
              $timeout(function(){$scope.endWorkoutAnalytics('MyFitnessPal Complete');}, 4000);
            } else{
              $timeout(function(){$scope.endWorkoutAnalytics('Regular Complete');}, 4000);
            }
          }
          if ($scope.burn == null){
            $scope.burn = 0;
          }
          window.db.transaction(function(transaction) {
                             transaction.executeSql('INSERT INTO SworkitFree(created_on, minutes_completed, calories, type, utc_created) VALUES ((datetime("now","localtime")),?,?,?,datetime("now"))',[$scope.timeToAdd, $scope.burn, $stateParams.typeId], nullHandler,errorHandler);
                             });
          var totalWeek = parseInt(window.localStorage.getItem('weeklyTotal'));
          totalWeek += $scope.timeToAdd;
          window.localStorage.setItem('weeklyTotal', totalWeek);
          $scope.totals = {'totalEver':0,'todayMinutes':0,'todayCalories':0,'weeklyMinutes':0,'weeklyCalories':0,'topMinutes':0, 'topCalories':0, 'topDayMins':'', 'topDayCals':''};
          $scope.goalSettings = UserService.getGoalSettings();
          $timeout( function() {
            buildStats($scope);
          },0);
          $timeout( function() {
            $ionicSlideBoxDelegate.update();
          },1000);
          $scope.endModal.show();
          
          $timeout(function(){
            if (!$scope.workoutComplete && $scope.timeToAdd > 1){
                $ionicPopup.confirm({
                       title: $translate.instant('FINISHED'),
                       cancelText: $translate.instant('CANCEL_NO'),
                       template: '<p class="padding">' + $translate.instant('FINISHED_B') + '</p>',
                       okType: 'energized',
                       okText: $translate.instant('YES_SM')
                     }).then(function(res) {
                       if(res) {
                          $scope.confirmDone();
                       }
                     });       
            }
          },1000)
      };
      $scope.confirmDone = function(){
        if (device){
          for (i=0;i<$scope.unloadQueue.length;i++){
            LowLatencyAudio.unload($scope.unloadQueue[i]);
          }
        }
        if (!$scope.workoutComplete){
          $scope.workoutComplete = true;
          $scope.playCongratsSound();
          if ($scope.userSettings.mfpStatus){
            $timeout(function(){$scope.syncMFP();}, 0);
            $timeout(function(){$scope.endWorkoutAnalytics('MyFitnessPal Partial');}, 4000);
          } else{
            $timeout(function(){$scope.endWorkoutAnalytics('Regular Partial');}, 4000);
          }
          if (device && $scope.userSettings.mPoints){
                $timeout(function(){$scope.endworkoutReward();}, 400);
              }
          if (device && $scope.userSettings.kiipRewards){
            $timeout(function(){$scope.endworkoutKiip();}, 2000);
          }
          if (device && $scope.userSettings.healthKit){
            $scope.syncHealthKit();
          }
          if (device && $scope.googleFitSettings.enabled){
            $scope.syncGoogleFit();
          }
          $scope.setVariables();
        }
      }
      $scope.cancelModal = function() {
          $scope.endModal.hide();
          $scope.endModal.remove();
          $scope.endModalOpen = false;
          window.db.transaction(function(transaction) {
                               transaction.executeSql('DELETE FROM SworkitFree WHERE sworkit_id = (SELECT MAX(sworkit_id) FROM SworkitFree)');
                               });
          var totalWeek = parseInt(window.localStorage.getItem('weeklyTotal'));
          totalWeek -= $scope.timeToAdd;
          window.localStorage.setItem('weeklyTotal', totalWeek);
          if ($scope.isAdMobRunning) {
            if (AdMob) AdMob.showBanner(AdMob.AD_POSITION.BOTTOM_CENTER);
          } else if ($scope.isMoPubRunning) {
            if (MoPub) MoPub.showBanner(8);
          }
      };
      $scope.mainMenu = function() {
          $scope.videoAddress = 'video/Blank.mp4';
          $scope.currentWorkout = startedWorkout;
          $scope.endModal.hide();
          $scope.endModal.remove();
          $scope.endModalOpen = false;
          if (device && $scope.isAdMobRunning){AdMob.removeBanner()}
          if (device && $scope.isMoPubRunning) {MoPub.removeBanner()};
          document.removeEventListener('onAdPresent', function(data){});
          if ($scope.androidPlatform && device && !$scope.googleFitSettings.attempted){
            $scope.googleFitSettings.attempted = true;
            localforage.setItem('googleFitStatus', PersonalData.GetGoogleFit);
          }
          document.removeEventListener("pause", workoutOnPause, false);
          document.removeEventListener("resume", onResumeWorkout, false);
          window.removeEventListener("orientationchange", orientationChange);
          if ($scope.timeToAdd < 1){
            window.db.transaction(function(transaction) {
                               transaction.executeSql('DELETE FROM SworkitFree WHERE sworkit_id = (SELECT MAX(sworkit_id) FROM SworkitFree)');
                               });
            var totalWeek = parseInt(window.localStorage.getItem('weeklyTotal'));
            totalWeek -= $scope.timeToAdd;
            window.localStorage.setItem('weeklyTotal', totalWeek);
          } else {
            if (globalSworkitAds.useAdMobInterstitial && device){
              if(AdMob) AdMob.prepareInterstitial( {license: "contact@sworkit.com/748952052cd93201ac292e2578a5c96d",adId:admobid.interstitial, autoShow:true} );
              $timeout(function(){

              }, 10000)
            } else if (globalSworkitAds.useMoPubInterstitial && device){
              if(MoPub) MoPub.prepareInterstitial( {license: "contact@sworkit.com/9397093eceffc87679dd2c2663befdf6",adId:mopubid.interstitial, autoShow:true} );
              $timeout(function(){
              }, 10000)
            }
            globalFirstWorkout = false;
          }
          $ionicNavBarDelegate.showBackButton(true);
          $state.go('app.home');
      };
      $scope.endWorkoutAnalytics =function(mfpRegular){
        if ($stateParams.typeId == "sunSalutation" || $stateParams.typeId == "fullSequence" || $stateParams.typeId == 'runnerYoga'){
          trackEvent('Yoga Finish', mfpRegular, $scope.timeToAdd);
        } else{
          trackEvent('Workout Finish', mfpRegular, $scope.timeToAdd);
        }
      }
      $scope.endworkoutKiip = function(){
        if ($stateParams.typeId == "sunSalutation" || $stateParams.typeId == "fullSequence" || $stateParams.typeId == 'runnerYoga'){
          //callMoment('yogaCompleteSworkit');
        } else{
          //callMoment('workoutCompleteSworkit');
        }
      };
      $scope.setVariables = function(){
        localforage.getItem('ratingCategory', function(result){
          if(!result.past){
            globalRateOption = true;
            localforage.setItem('ratingCategory', {show:true,past:false,shareCount:1,sharePast:false});
            globalShareOption = 1;
          } else{
            if (result.shareCount){
              result.shareCount++
            } else {
              result.shareCount = 2;
            }
            localforage.setItem('ratingCategory', {show:false,past:true,shareCount:result.shareCount,sharePast:false});
            globalShareOption = result.shareCount;
          }
        });
        localforage.getItem('remindHome', function(result){
          if(!result.past){
            globalRemindOption = true;
            localforage.setItem('remindHome', {show:true,past:true});
            if (!$scope.userSettings.healthKit && $scope.iOSPlatform){
              $scope.healthKitData.showHealthKitOption = $scope.healthKitData.healthKitAvailable;
            }
          }
        });
        if (!$scope.userSettings.healthKit && $scope.iOSPlatform){
          $scope.healthKitData.showHealthKitOption = $scope.healthKitData.healthKitAvailable;
        }
      }
      $scope.endworkoutReward = function(){
        if ($stateParams.typeId == "fullBody" || $stateParams.typeId == "upperBody" || $stateParams.typeId == "coreExercise" || $stateParams.typeId == "lowerBody" || $stateParams.typeId == "anythingGoes"){
          sessionm.phonegap.logAction(translations['EN'][LocalData.GetWorkoutTypes[$stateParams.typeId].activityNames]);
        } else if ($stateParams.typeId == "stretchExercise" || $stateParams.typeId == "backStrength" || $stateParams.typeId == "headToToe"|| $stateParams.typeId == "pilatesWorkout"){
          sessionm.phonegap.logAction('Stretch');
        } else if ($stateParams.typeId == "sunSalutation" || $stateParams.typeId == "fullSequence" || $stateParams.typeId == 'runnerYoga'){
          sessionm.phonegap.logAction('Yoga');
        } else if ($stateParams.typeId == "bootCamp" || $stateParams.typeId == "rumpRoaster" || $stateParams.typeId == "bringThePain" || $stateParams.typeId == "sevenMinute"){
          sessionm.phonegap.logAction('Bonus Workout');
        } else if ($stateParams.typeId == "quickFive"){
          sessionm.phonegap.logAction('Quick Five');
        } else if ($stateParams.typeId == "customWorkout"){
          sessionm.phonegap.logAction('Custom Workout');
        } else if ($stateParams.typeId == "cardio" || $stateParams.typeId == "cardioLight", $stateParams.typeId == "plyometrics"){
          sessionm.phonegap.logAction('Cardio');
        }
        var tempTotal = $scope.totals.todayMinutes;
        if (tempTotal >= 5){
          for (var i = 0; i <Math.floor(tempTotal / 5);i++){
            sessionm.phonegap.logAction('Bonus5');
          }
        }
        if (tempTotal >= 10){
          for (var i = 0; i <Math.floor(tempTotal / 10);i++){
            sessionm.phonegap.logAction('Bonus10');
          }
        }
        if ($scope.timeToAdd > 30){
          sessionm.phonegap.logAction('30 Full Minutes');
        }
        if ($scope.totals.todayMinutes > $scope.goalSettings.dailyGoal){
          sessionm.phonegap.logAction('Daily Goal Met');
        }
        if ($scope.totals.todayMinutes > $scope.goalSettings.weeklyGoal){
          sessionm.phonegap.logAction('Weekly Goal Met');
        }
        window.db.transaction(
                           function(transaction) {
                           transaction.executeSql("SELECT * FROM SworkitFree WHERE created_on > (SELECT DATETIME('now', '-1 day'))",
                                                  [],
                                                  function(tx, results){
                                                    var workoutsToday = results.rows.length;
                                                    if (workoutsToday == 2){
                                                      sessionm.phonegap.logAction('Double Take');
                                                    } else if(workoutsToday == 3) {
                                                      sessionm.phonegap.logAction('Triple Hit');
                                                    }
                                                  },
                                                  null)
                           }
                           );
        $timeout(function(){
          $scope.getSessionMCount();
          $scope.$apply(); 
        }, 3000);

      }

      $scope.openCallToAction = function(){
        if (globalSworkitAds.isEndRunning && globalSworkitAds.sworkitProUpgrade) {
          setTimeout(function(){
            if (device.platform.toLowerCase() == 'ios') {
              window.open('http://nxr.cz/sk-pro-ios', '_system', 'location=no,AllowInlineMediaPlayback=yes');
            } else if (isAmazon()){
              window.appAvailability.check('com.amazon.venezia', function() {
                   window.open('amzn://apps/android?p=sworkitproapp.sworkit.com', '_system')},function(){
                   window.open(encodeURI("http://www.amazon.com/gp/mas/dl/android?p=sworkitproapp.sworkit.com"), '_system');}
                   );
            } else {
            window.open('market://details?id=sworkitproapp.sworkit.com', '_system')
            }
          }, 400)
          trackEvent('Ad Click', globalSworkitAds.adName, $stateParams.typeId);
        } else if (globalSworkitAds.isEndRunning) {
          window.open(globalSworkitAds.adActionLink, 'blank', 'location=yes,AllowInlineMediaPlayback=yes,toolbarposition=top' );
        }
      }

      $scope.$on('$ionicView.leave', function() {
                 $scope.endModal.remove();
                 });
      $timeout(function(){
        $scope.openModal();
      }, 0);

    $scope.sessionMCount = {count:false, mPointsAvailable: $rootScope.sessionMAvailable};
    
    $scope.getSessionMCount = function(){
      sessionm.phonegap.getUnclaimedAchievementCount(function callback(data) {
        $scope.sessionMCount.count = (data.unclaimedAchievementCount == 0) ? false : data.unclaimedAchievementCount;  
        $scope.$apply();
      });
      sessionm.phonegap.listenDidDismissActivity(function callback(data2) {
        $scope.getSessionMCount();
      });
    }
    $scope.launchMPoints = function(){
      if (device){
        sessionm.phonegap.presentActivity(2);
      }
    }

    $scope.challengeFriend = function(){
      var challengeText = $translate.instant('I_AWESOME') + ' ' + $scope.timeToAdd + ' ' + $translate.instant('MINUTES_OF') + ' ' + $translate.instant(LocalData.GetWorkoutTypes[$stateParams.typeId].activityNames) + ' ' + $translate.instant('EX_WITH') + ' Sworkit ' + $translate.instant('HASHTAG');
      window.plugins.socialsharing.share(challengeText, null, null, 'http://sworkit.com')
    }

    $scope.enableHealthKit = function(){
      $scope.healthKitData.showHealthKitOption = false;
      window.plugins.healthkit.requestAuthorization(
                                                          {
                                                          'readTypes'  : [ 'HKQuantityTypeIdentifierBodyMass'],
                                                          'writeTypes' : ['HKQuantityTypeIdentifierActiveEnergyBurned', 'workoutType']
                                                          },
                                                          function(){
                                                            PersonalData.GetUserSettings.healthKit = true;
                                                            localforage.setItem('userSettings', PersonalData.GetUserSettings);
                                                            $scope.syncHealthKit();
                                                          },
                                                          function(){}
                                                          );
    }
    $scope.syncHealthKit = function(){
      var workoutHK;
      if ($stateParams.typeId == "upperBody" || $stateParams.typeId == "coreExercise" || $stateParams.typeId == "lowerBody"){
          workoutHK = 'HKWorkoutActivityTypeFunctionalStrengthTraining';
        } else if ($stateParams.typeId == "stretchExercise" || $stateParams.typeId == "backStrength" || $stateParams.typeId == "headToToe"){
          workoutHK = 'HKWorkoutActivityTypePreparationAndRecovery';
        } else if ($stateParams.typeId == "sunSalutation" || $stateParams.typeId == "fullSequence" || $stateParams.typeId == 'runnerYoga'){
          workoutHK = 'HKWorkoutActivityTypeYoga';
        } else if ($stateParams.typeId == "customWorkout" || $stateParams.typeId == "fullBody"  || $stateParams.typeId == "anythingGoes" || $stateParams.typeId == "bootCamp" || $stateParams.typeId == "rumpRoaster" || $stateParams.typeId == "bringThePain" || $stateParams.typeId == "sevenMinute" || $stateParams.typeId == "quickFive"){
          workoutHK = 'HKWorkoutActivityTypeCrossTraining';
        } else if ($stateParams.typeId == "cardio" || $stateParams.typeId == "cardioLight" || $stateParams.typeId == "plyometrics"){
          workoutHK = 'HKWorkoutActivityTypeMixedMetabolicCardioTraining';
        } else if ($stateParams.typeId == "pilatesWorkout"){
          workoutHK = 'HKWorkoutActivityTypeDanceInspiredTraining';
        }
      window.plugins.healthkit.saveWorkout({
                                                 'activityType': workoutHK,
                                                 'quantityType': null,
                                                 'startDate': $scope.startDate,
                                                 'endDate': null,
                                                 'duration': $scope.minutesCompleted * 60 * 60,
                                                 'energy': $scope.burn,
                                                 'energyUnit': 'kcal',
                                                 'distance': null,
                                                 'distanceUnit': 'm'
                                                 },
                                                 function(msg){
                                                  //console.log('HealthKit success: ' + msg);
                                                  $scope.healthKitData.healthKitStatus = $translate.instant('SAVED') + ' HealthKit';
                                                  $timeout(function(){
                                                    $scope.healthKitData.healthKitStatus = '';
                                                  }, 5000)
                                                 },
                                                 function(msg){
                                                  //console.log('HealthKit error: ' + msg);
                                                 }
                                                 );

    }
    $scope.enableGoogleFit = function(){
      var infoTemplate = '<div class="end-workout-health" style="text-align: center;width:230px;margin:0px auto"><img src="img/googleFit.png" style="height:50px;display: block;margin: 10px auto;"/><div style="width:100%"><p>' + $translate.instant('GFIT_1') + '</p><p>' + $translate.instant('GFIT_2') + '</p><p style="color:#777;font-size:12px">' + $translate.instant('GFIT_3') + '</p><button class="button button-assertive" ng-click="confirmGoogleFit()" style="width:230px">{{"CONNECT_FIT" | translate}}</button></div></div>';
      $scope.googleFitPopup = $ionicPopup.show({
        title: '',
        subTitle: '',
        scope: $scope,
        template: infoTemplate,
        hardwareBackButtonClose: true,
        buttons: [
          { text: $translate.instant('CANCEL_SM') }
        ]
      });
    }
    $scope.hideGoogleFitPopup = function(){
      $scope.googleFitPopup.close();
    }
    $scope.confirmGoogleFit = function(){
      $scope.hideGoogleFitPopup();
      $scope.googleFitSettings.enabled = true;
      $scope.googleFitSettings.attempted = true;
      $scope.syncGoogleFit();
      localforage.setItem('googleFitStatus', PersonalData.GetGoogleFit);
    }
    $scope.syncGoogleFit = function(){
      var fitnessActivity;
      if ($stateParams.typeId == "upperBody" || $stateParams.typeId == "fullBody" || $stateParams.typeId == "coreExercise" || $stateParams.typeId == "lowerBody"){
          fitnessActivity = 'STRENGTH_TRAINING';
        } else if ($stateParams.typeId == "stretchExercise" || $stateParams.typeId == "backStrength" || $stateParams.typeId == "headToToe"){
          fitnessActivity = 'CALISTHENICS';
        } else if ($stateParams.typeId == "sunSalutation" || $stateParams.typeId == "fullSequence" || $stateParams.typeId == 'runnerYoga'){
          fitnessActivity = 'YOGA';
        } else if ($stateParams.typeId == "customWorkout" || $stateParams.typeId == "anythingGoes" || $stateParams.typeId == "bootCamp" || $stateParams.typeId == "rumpRoaster" || $stateParams.typeId == "bringThePain" || $stateParams.typeId == "sevenMinute" || $stateParams.typeId == "quickFive"){
          fitnessActivity = 'CIRCUIT_TRAINING';
        } else if ($stateParams.typeId == "cardio" || $stateParams.typeId == "cardioLight" || $stateParams.typeId == "plyometrics"){
          fitnessActivity = 'CIRCUIT_TRAINING';
        } else if ($stateParams.typeId == "pilatesWorkout"){
          fitnessActivity = 'PILATES';
        }
        window.plugins.GoogleFit.insertSession(
          [$scope.startDate.getTime(), $scope.minutesCompleted * 60 * 60000, "Sworkit", fitnessActivity],
          function(){
            $scope.healthKitData.healthKitStatus = $translate.instant('SAVED') + ' Google Fit';
            $timeout(function(){
              $scope.healthKitData.healthKitStatus = '';
            }, 5000)
          },
          function(result){console.log('Google Fit Fail ' + result)}
        )      
    }
  }
  $scope.myFitnessPalRetry = true;
  $scope.syncMFP = function(){
    var dateString = $scope.startTime;
    var actionString = "log_cardio_exercise";
    var accessString = PersonalData.GetUserSettings.mfpAccessToken;
    var appID = "79656b6e6f6d";
    var exerciseID = LocalData.GetWorkoutTypes[$stateParams.typeId].activityMFP
    var durationFloat = $scope.timeToAdd * 60000;
    var energyCalories = $scope.burn;
    var unitCountry = "US";
    var statusMessage = "burned %CALORIES% calories doing %QUANTITY% minutes of " + $translate.instant(LocalData.GetWorkoutTypes[$stateParams.typeId].activityNames) + " with Sworkit";
    //console.log('MFP Sync time: ' + $scope.startTime);
    var dataPost = JSON.stringify({'action' : actionString, 'access_token' : accessString,'app_id': appID, 'exercise_id': exerciseID, 'duration': durationFloat, 'energy_expended': energyCalories, 'start_time' : dateString, 'status_update_message': statusMessage, 'units': unitCountry});
    $http({
      method: 'POST',
      url: 'https://www.myfitnesspal.com/client_api/json/1.0.0?client_id=sworkit',
      data: dataPost,
      headers: {'Content-Type': 'application/json'}
    }).then(function(resp){
      showNotification($translate.instant('MFP_SUCCESS'), 'button-calm', 4000);
     }, function(err) {
      if ($scope){
        if ($scope.myFitnessPalRetry){
          $scope.myFitnessPalRetry = false;
          $timeout(function(){
            $scope.syncMFP();
          }, 1400);
        } else {
          showNotification($translate.instant('MFP_ERROR'), 'button-assertive', 4000);
        } 
      }
    })
  }
            
  $ionicModal.fromTemplateUrl('show-video.html', function(modal) {
                                $scope.videoModal = modal;
                                }, {
                                scope:$scope,
                                animation: 'fade-implode',
                                focusFirstInput: false,
                                backdropClickToClose: false,
                                hardwareBackButtonClose: true
                                });
  $scope.showVideo = false;
  $scope.openVideoModal = function() {
    $scope.networkConnection = navigator.onLine;
    $scope.stopTimer();
    $interval.cancel($scope.transitionCountdown);
    $timeout.cancel($scope.delayStart);
    $scope.transitionStatus = false;
    $scope.timerDelay = null;
    $scope.videoModal.show();
    if ($scope.androidPlatform && device){
        if ($scope.advancedTiming.autoPlay){
        window.plugins.html5Video.initialize({
          "modalvideoplayer" : $scope.currentExercise.video
        })
        $timeout(function(){
          window.plugins.html5Video.play("modalvideoplayer", function(){})
        }, 1400)
        $timeout(function(){
          angular.element(document.getElementById('modalvideoplayer')).css('opacity','1');
        }, 1500)
        $timeout(function(){
            angular.element(document.getElementById('modalvideoplayer')).css('opacity','0.00001');
          }, 0);
      } else{$timeout(function(){
              var videoPlayerFrame = angular.element(document.getElementById('modalvideoplayer'));
              videoPlayerFrame.css('opacity','0.00001');
              videoPlayerFrame[0].src = 'http://m.sworkit.com/assets/exercises/Videos/' + $scope.currentExercise.video;

              videoPlayerFrame[0].addEventListener("timeupdate", function() {
                if (videoPlayerFrame[0].duration > 0 
                  && Math.round(videoPlayerFrame[0].duration) - Math.round(videoPlayerFrame[0].currentTime) == 0) {
                  
                  //if loop atribute is set, restart video
                    if (videoPlayerFrame[0].loop) {
                        videoPlayerFrame[0].currentTime = 0;
                    }
                }
              }, false);
              
              videoPlayerFrame[0].addEventListener("canplay", function(){
                videoPlayerFrame[0].removeEventListener("canplay", this, false);
                videoPlayerFrame[0].play();
                videoPlayerFrame.css('opacity','1');
              }, false);
              
              videoPlayerFrame[0].play();
            }, 100);
        }
    } else {
      $scope.videoAddressModal = 'video/' + $scope.currentExercise.video +'?random=1';
    }
    var calcHeight = (angular.element(document.getElementsByClassName('modal')).prop('offsetHeight'))   * .7;
    calcHeight = calcHeight +'px';
    $scope.showVideo = true;
  } 
  $scope.cancelVideoModal = function() {
    $scope.showVideo = false;
    $scope.videoModal.hide();
    // if($scope.advancedTiming.autoPlay){
    //   var videoElement = angular.element(document.getElementById('inline-video'))[0];
    //   videoElement.muted= true;
    //   videoElement.play();
    // }
  };
  $scope.$on('$ionicView.leave', function() {
    $scope.showVideo = false;
    $scope.videoModal.remove();
  });

  $scope.isPaused = function () {
    return !$scope.totalTimerRunning;
  }
  //Interval variable is 'start'
  var start;

  $scope.setMinutes = function (){
    var singleSeconds = $scope.advancedTiming.exerciseTime;
    var totalMinutes = $stateParams.timeId;
    if (singleSeconds > 60){
      $scope.singleTimer.minutes = Math.floor(singleSeconds / 60);
      $scope.singleTimer.seconds = singleSeconds % 60;
    } else {
      $scope.singleTimer.minutes = 0;
      $scope.singleTimer.seconds = singleSeconds;
    }
    if ($stateParams.typeId == 'sevenMinute' && $stateParams.timeId % 7 == 0){
      var mathMin = ($scope.advancedTiming.exerciseTime * 12) / 60;
      var parseMin = parseInt(mathMin);
      var mathSec = Math.round( (mathMin % parseMin) * 10) / 10;
      mathSec = mathSec * 60;
      if (mathSec.toString().length == 1){
          mathSec = "0" + mathSec;
      }
      $scope.totalTimer.seconds = parseInt(mathSec);
      $scope.totalTimer.minutes =  (parseMin * $stateParams.timeId/7);
    } else {
      $scope.totalTimer.minutes = totalMinutes;
      $scope.totalTimer.seconds = 0;
    }
    $scope.updateTime();
  }
  $scope.updateTime = function() {
    $scope.singleTimer.displayText = $scope.displayTime($scope.singleTimer.minutes, $scope.singleTimer.seconds);
    $scope.totalTimer.displayText = $scope.displayTime($scope.totalTimer.minutes, $scope.totalTimer.seconds);
  }
  $scope.displayTime = function(mins, secs, type){
    var cleanedTime;
    if (mins > 0 && secs < 10){
        secs = '0' + secs;
    } else if (type == 'total' && secs < 10){
      secs = '0' + secs;
    }
    if (mins > 0 || type == 'total'){
      return mins + ":" + secs;
    } else{
      return secs;
    }
  }
  
  //Set defaults each time
  $scope.setDefaults = function(){
    $scope.currentExercise = exerciseObject[$scope.currentWorkout[0]];
    $scope.nextExercise = {status: false, name: false, image: exerciseObject[$scope.currentWorkout[0]].image};
    if ($scope.androidPlatform && device){
      window.plugins.html5Video.initialize({
        "inlinevideo" : $scope.currentExercise.video
      })
    } else {
      $scope.videoAddress = 'video/' + $scope.currentExercise.video;
    }
    $timeout(function(){
      if ($scope.currentExercise.switchOption){
        $scope.helpText = $translate.instant('CHANGE_SIDE');
      } else{
        $scope.helpText = false;
      }
      angular.element(document.getElementById('total-progress-bar')).addClass('started');
    }, 800);
    $scope.hasStarted = false;
    $scope.transitionsStatus = false;
    $scope.timerDelay = null;
    $scope.workoutComplete = false;
    $scope.numExercises = 0;
    if ($stateParams.typeId == 'sevenMinute'){
      $scope.advancedTiming.breakFreq = 0;
      $scope.advancedTiming.exerciseTime = $scope.sevenTiming.exerciseTimeSeven;
      $scope.advancedTiming.breakTime = 0;
      $scope.advancedTiming.transitionTime = $scope.sevenTiming.transitionTimeSeven;
      $scope.advancedTiming.randomizationOption = $scope.sevenTiming.randomizationOptionSeven;
    } else {
      if ($scope.advancedTiming.transition){
        $scope.advancedTiming.transitionTime = 5;
      } else {
        $scope.advancedTiming.transitionTime = 0;
      }
      $scope.advancedTiming.breakFreq = 5;
      $scope.advancedTiming.exerciseTime = 30;
      $scope.advancedTiming.breakTime = 30;
      $scope.advancedTiming.randomizationOption = true;
      if ($stateParams.typeId == 'headToToe' || $stateParams.typeId == 'stretchExercise' || $stateParams.typeId == 'sevenMinute'){
          $scope.advancedTiming.breakFreq = 0;
        }
    }
    if ($stateParams.typeId == 'headToToe'){
      $scope.advancedTiming.randomizationOption = false;
    }
    if ($stateParams.typeId == 'sunSalutation'){
      $scope.yogaSelection = true;
      $scope.advancedTiming.customSet = false;
      $scope.advancedTiming.breakFreq = 0;
      $scope.advancedTiming.exerciseTime = 8;
      $scope.advancedTiming.breakTime = 0;
      $scope.advancedTiming.transitionTime = 0;
      $scope.advancedTiming.transition = false;
      $scope.advancedTiming.randomizationOption = false;
      $scope.advancedTiming.warningAudio = false;
    } else if ($stateParams.typeId == 'fullSequence'){
      $scope.yogaSelection = true;
      $scope.advancedTiming.customSet = false;
      $scope.advancedTiming.breakFreq = 0;
      $scope.advancedTiming.exerciseTime = 21;
      $scope.advancedTiming.breakTime = 0;
      $scope.advancedTiming.transitionTime = 0;
      $scope.advancedTiming.transition = false;
      $scope.advancedTiming.randomizationOption = false;
      $scope.advancedTiming.warningAudio = false;
    } else if ($stateParams.typeId == 'runnerYoga'){
      $scope.yogaSelection = true;
      $scope.advancedTiming.customSet = false;
      $scope.advancedTiming.breakFreq = 0;
      $scope.advancedTiming.exerciseTime = 15;
      $scope.advancedTiming.breakTime = 0;
      $scope.advancedTiming.transitionTime = 0;
      $scope.advancedTiming.transition = false;
      $scope.advancedTiming.randomizationOption = false;
      $scope.advancedTiming.warningAudio = false;
    }

    $scope.transitionCountdown;

    $scope.singleTimerRunning = false;
    $scope.totalTimerRunning = false;
    $scope.singleTimer = {time: $scope.advancedTiming.exerciseTime, minutes: 0, seconds: 0, displayText: '', status: false}
    $scope.totalTimer = {time: $stateParams.timeId, minutes: 0, seconds: 0, displayText: '', status: false}
    $scope.singleSecondsStart = $scope.advancedTiming.exerciseTime;
    $scope.totalSecondsStart = $stateParams.timeId;

    $scope.setMinutes();
    $timeout(function(){
          if(ionic.Platform.isAndroid()){
            playInlineVideo($scope.advancedTiming.autoPlay);
          } else {
            playInlineVideo($scope.advancedTiming.autoPlay, exerciseObject[$scope.currentWorkout[0]]);
          }
          $scope.nextExercise.image = exerciseObject[$scope.currentWorkout[1]].image;
          if($scope.isAutoStart){
            $scope.transitionAction(true);
          }
    },200)
    $timeout(function(){
          $scope.setVideo();
    },500)
    $timeout(function(){
      if ($scope.isAdMobRunning){
        if(AdMob) AdMob.createBanner( {
          license: "contact@sworkit.com/748952052cd93201ac292e2578a5c96d",
          adId: admobid.banner, 
          position: AdMob.AD_POSITION.BOTTOM_CENTER, 
          autoShow: true,
          adExtras: {
              color_bg: '#444444',
              color_bg_top: '444444',
              color_border: 'FF8614',
              color_link: '000080',
              color_text: '808080',
              color_url: 'FF8614'
          }
        }
      );
      document.addEventListener('onAdPresent', function(data){workoutOnPause()});
      } else if ($scope.isMoPubRunning){
        var bannerSize = "SMART_BANNER";
        if (ionic.Platform.isIPad()){
          bannerSize = "LEADERBOARD";
        }
        if(MoPub) MoPub.createBanner( {
          license: "contact@sworkit.com/9397093eceffc87679dd2c2663befdf6",
          adId: mopubid.banner, 
          position: 8, 
          adSize: bannerSize,
          autoShow: true,
          overlap:false
        });
        document.addEventListener('onAdPresent', function(data){workoutOnPause()});
      }
    },1500)
  }

  $scope.setDefaults();

  $scope.startTimer = function (){
    
    start = $interval(function() {
      if ($scope.totalTimer.seconds % 5 == 0){
        $scope.totalWidth = 100 - (((($stateParams.timeId * 60) - ((($scope.totalTimer.minutes) * 60) + $scope.totalTimer.seconds)) / ($stateParams.timeId * 60)) * 100);
      }
      if ($scope.totalTimer.seconds == 0 && $scope.totalTimer.minutes == 0){
        $scope.playCongratsSound();
        $scope.workoutComplete = true;
        $scope.endWorkout();
        $scope.stopTimer;
        $scope.singleTimer.seconds = 1;
        $scope.totalTimer.seconds = 1;
        if (device && $scope.userSettings.mPoints){
              $timeout(function(){$scope.endworkoutReward();}, 1200);
            }
        if (device && $scope.userSettings.kiipRewards){
              $timeout(function(){$scope.endworkoutKiip();}, 600);
            }
        if (device && $scope.userSettings.healthKit){
            $timeout(function(){$scope.syncHealthKit();}, 1500);
           }
        if (device && $scope.googleFitSettings.enabled){
            $timeout(function(){$scope.syncGoogleFit();}, 1500);
          }
        $scope.setVariables();
      }
      else if ($scope.totalTimer.seconds == 0 && $scope.totalTimer.minutes > 0){
        $scope.totalTimer.seconds = 60;
        $scope.totalTimer.minutes --;
      }
      if ($scope.currentExercise.switchOption && $scope.singleTimer.seconds == (Math.round($scope.advancedTiming.exerciseTime / 2))) {
        if ($scope.currentExercise.image !== "Break.jpg"){
          $scope.playSwitchSound();
          $scope.changeText = $translate.instant('CHANGE_SIDE_SM');
          continueInlineVideo($scope.advancedTiming.autoPlay, exerciseObject[$scope.currentWorkout[0]]);
        }
      } else if ($scope.advancedTiming.warningAudio){
        if ($scope.singleTimer.seconds == 11 && $scope.numExercises !== $scope.advancedTiming.breakFreq - 1 && $scope.advancedTiming.breakFreq !== 0  && $scope.advancedTiming.breakTime > 0){
          if ($scope.totalTimer.minutes == 0){
            if ($scope.totalTimer.seconds > 11){
              $scope.playNextWarning(exerciseObject[$scope.currentWorkout[1]]);
              $scope.nextExercise.name = exerciseObject[$scope.currentWorkout[1]].name;
              $scope.nextExercise.status = true;
            }
          } else{
            $scope.playNextWarning(exerciseObject[$scope.currentWorkout[1]]);
            $scope.nextExercise.name = exerciseObject[$scope.currentWorkout[1]].name;
            $scope.nextExercise.status = true;
          }
        } 
      }
      if ($scope.singleTimer.seconds == 0 && $scope.singleTimer.minutes == 0){
        $scope.numExercises++;
        if ($scope.numExercises == $scope.advancedTiming.breakFreq && $scope.advancedTiming.breakFreq !== 0  && $scope.advancedTiming.breakTime > 0){
            $scope.nextExercise.status = false;
            $scope.playBreakSound();
            $scope.numExercises = -1;
            $scope.nextExercise.image = "Break.jpg";
            $scope.helpText = false;
            var breakText = $translate.instant('TAKE') + " " + $scope.advancedTiming.breakTime + " " + $translate.instant('SEC_BREAK');
            $scope.currentExercise = {"name":breakText,"image":"Break.jpg","youtube":"rN6ATi7fujU","switchOption":false,"video":"Break.mp4","category":false};
            $scope.videoAddress = 'video/Break.mp4';
            if ($scope.androidPlatform && device){
            } else {
              $scope.videoAddress = 'video/Break.mp4';
            }
            var videoFrame = angular.element(document.getElementById('inline-video'))[0];
            if ($scope.advancedTiming.autoPlay){

              if (ionic.Platform.isAndroid() && device){
                window.plugins.html5Video.initialize({
                  "inlinevideo" : $scope.currentExercise.video
                })
                setTimeout(function(){
                  playInlineVideo($scope.advancedTiming.autoPlay);
                }, 500);
                setTimeout(function(){
                  angular.element(document.getElementById('inlinevideo')).css('opacity','1');
                  $scope.nextExercise.image = exerciseObject[$scope.currentWorkout[1]].image;
                  $scope.$apply();
                }, 1500)
              }
              else{
                angular.element(document.getElementById('inline-video')).css('opacity','0.0001');
                var playEventListener = function(){
                  playInlineVideo($scope.advancedTiming.autoPlay, exerciseObject[$scope.currentWorkout[0]]);
                  $timeout(function(){
                         $scope.nextExercise.image = exerciseObject[$scope.currentWorkout[1]].image;
                         $scope.$apply()
                         }, 2000);
                  setTimeout(function(){angular.element(document.getElementById('inline-video')).css('opacity','1');}, 800);
                  videoFrame.removeEventListener('canplaythrough', playEventListener);
                }
                videoFrame.addEventListener('canplaythrough', playEventListener);
              }
            } else {
              setTimeout(function(){angular.element(document.getElementById('inline-video')).css('opacity','1');
                $scope.nextExercise.image = exerciseObject[$scope.currentWorkout[0]].image;
              }, 500);
            }
            var singleSeconds = $scope.advancedTiming.breakTime;
            if (singleSeconds > 60){
              $scope.singleTimer.minutes = Math.floor(singleSeconds / 60);
              $scope.singleTimer.seconds = singleSeconds % 60;
            } else {
              $scope.singleTimer.minutes = 0;
              $scope.singleTimer.seconds = singleSeconds;
            }
            $scope.updateTime();
        }
        else{
            $scope.skipExercise();
            $scope.singleTimer.seconds ++;
            $scope.totalTimer.seconds ++;
            if ($scope.totalTimer.seconds > 60){
              $scope.totalTimer.minutes++;
              $scope.totalTimer.seconds = 1;
            }
        }
      }
      else if ($scope.singleTimer.seconds == 4 && $scope.advancedTiming.countdownBeep && $scope.singleTimer.minutes == 0){
        if (!$scope.yogaSelection){
          $scope.playCountdown();
        }
        if($scope.numExercises == $scope.advancedTiming.breakFreq - 1 && $scope.advancedTiming.breakFreq !== 0  && $scope.advancedTiming.breakTime > 0){
          $scope.nextExercise.name = $translate.instant('TAKE') + " " + $scope.advancedTiming.breakTime + " " + $translate.instant('SEC_BREAK');
          $scope.nextExercise.status = true;
        }
      }
      else if ($scope.singleTimer.seconds == 4 && $scope.advancedTiming.countdownBeep && $scope.singleTimer.seconds == 0 && $scope.singleTimer.minutes < 0){
        if (!$scope.yogaSelection){
          $scope.playCountdown();
        }
      }
      else if ($scope.singleTimer.seconds == 0 && $scope.singleTimer.minutes > 0){
          $scope.singleTimer.seconds = 60;
          $scope.singleTimer.minutes--;
      }
      $scope.singleTimer.seconds --;
      $scope.totalTimer.seconds --;
      $scope.updateTime();
    }, 1000);
    $scope.singleTimer.status = true;
    $scope.totalTimer.status = true;

    //Specific actions for very first START
    if (!$scope.hasStarted){
      if (!$scope.advancedTiming.autoStart){
        $scope.playNextSound(exerciseObject[$scope.currentWorkout[0]]);
      }
      $scope.isAutoStart = false;
      if ($stateParams.typeId !== 'sevenMinute' && !$scope.advancedTiming.autoStart){
        $scope.transitionAction();
      }
      $scope.startTime = js_yyyy_mm_dd_hh_mm_ss();
      $scope.startDate = new Date();
    }
    $scope.hasStarted = true;
  };

  $scope.showBegin = function(){
    $scope.beginNotification = true;
    $scope.changeText = false;
      $timeout(function(){
        $scope.beginNotification = false;
    },2000)
  }

  $scope.stopTimer = function (){
      $interval.cancel(start);
      start = undefined;
      $scope.singleTimer.status = false;
      $scope.totalTimer.status  = false;
  };

  $scope.toggleTimer = function (){
    $timeout.cancel($scope.delayStart);
    if ($scope.timerDelay !== null && !$scope.totalTimer.status){
      $scope.transitionStatus = false;
      $interval.cancel($scope.transitionCountdown);
      $scope.isAutoStart = false;
      $scope.timerDelay = null;
      $scope.startTimer();
      $scope.toggleControls();
    } else if($scope.timerDelay == null && $scope.totalTimer.status) {
      $scope.stopTimer();
    } else if ($scope.timerDelay == null && !$scope.totalTimer.status && !$scope.hasStarted){
      $scope.startTimer();
      $scope.toggleControls();
    } else if ($scope.timerDelay == null && !$scope.totalTimer.status){
      //Leaving this else if in case we want to have the pause always after watching video
      //$scope.transitionAction();
      $scope.startTimer();
      $scope.toggleControls();
    }
  };

  $scope.skipExercise = function (fromControl){
    if (fromControl){
      $scope.toggleControls(true);
    }
    $scope.nextExercise.status = false;
    if ($scope.isAutoStart){
      $timeout.cancel($scope.firstExerciseAudio);
    } else {
      $interval.cancel($scope.transitionCountdown);
      $timeout.cancel($scope.delayStart); 
    }
    $scope.previousExercise = $scope.currentExercise;
    $scope.currentWorkout.shift();
    if ($scope.currentWorkout.length == 1){
      $scope.lastExercise = exerciseObject[$scope.currentWorkout[0]];
      $scope.currentWorkout = $scope.currentWorkout.concat(startedWorkout);
      if ($stateParams.typeId == 'headToToe' || $stateParams.typeId == 'sevenMinute' || $stateParams.typeId == 'sunSalutation' || $stateParams.typeId == 'fullSequence' || $stateParams.typeId == 'runnerYoga'){
      } else {
         if($scope.advancedTiming.randomizationOption || !$scope.advancedTiming.customSet){
            if ($stateParams.typeId == "upperBody"){
              var pushupBased = ["Push-ups","Diamond Push-ups","Wide Arm Push-ups","Alternating Push-up Plank","One Arm Side Push-up", "Dive Bomber Push-ups","Shoulder Tap Push-ups", "Spiderman Push-up", "Push-up and Rotation"];
              var nonPushup = ["Overhead Press","Overhead Arm Clap","Tricep Dips","Jumping Jacks", "Chest Expander", "T Raise","Lying Triceps Lifts","Reverse Plank","Power Circles","Wall Push-ups"]
              pushupBased = pushupBased.sort(function() { return 0.5 - Math.random() });
              nonPushup = nonPushup.sort(function() { return 0.5 - Math.random() });
              var mergedUpper = mergeAlternating(pushupBased,nonPushup)
              $scope.currentWorkout = mergedUpper;
            } else{
              $scope.currentWorkout = $scope.currentWorkout.sort(function() { return 0.5 - Math.random() });
            }
         }
      }
      $scope.currentWorkout.shift();
      $scope.currentWorkout.unshift(translations['EN'][$scope.lastExercise.name]);
    }
    $scope.currentExercise = exerciseObject[$scope.currentWorkout[0]];

    if ($scope.androidPlatform && device){
      angular.element(document.getElementById('inlinevideo')).css('opacity','0.00001');
    } else {
      angular.element(document.getElementById('inline-video')).css('opacity','0.00001');
      setTimeout(function(){
        $scope.videoAddress = 'video/' + $scope.currentExercise.video;
      }, 0);
    }
    
    if ($scope.advancedTiming.autoPlay){
      var videoFrame = angular.element(document.getElementById('inline-video'))[0];
      if (ionic.Platform.isAndroid() && device){
          window.plugins.html5Video.initialize({
            "inlinevideo" : $scope.currentExercise.video
          })
          setTimeout(function(){
            playInlineVideo($scope.advancedTiming.autoPlay);
          }, 500);
          setTimeout(function(){
            angular.element(document.getElementById('inlinevideo')).css('opacity','1');
            $scope.nextExercise.image = exerciseObject[$scope.currentWorkout[1]].image;
            $scope.$apply();
          }, 1500)
      } else {
        clearTimeout(inlineVideoTimeout);
        var playEventListener = function(){
          playInlineVideo($scope.advancedTiming.autoPlay, exerciseObject[$scope.currentWorkout[0]]);
          setTimeout(function(){angular.element(document.getElementById('inline-video')).css('opacity','1');
            $scope.nextExercise.image = exerciseObject[$scope.currentWorkout[1]].image;
            $scope.$apply();
          }, 500);
          videoFrame.removeEventListener('canplaythrough', playEventListener);
        }
        videoFrame.addEventListener('canplaythrough', playEventListener);
        setTimeout(function(){angular.element(document.getElementById('inline-video')).css('opacity','1');
          }, 1500);
      }
    } else {
      setTimeout(function(){angular.element(document.getElementById('inline-video')).css('opacity','1');
        $scope.nextExercise.image = exerciseObject[$scope.currentWorkout[1]].image;
      }, 500);   
    }
    var singleSeconds = $scope.advancedTiming.exerciseTime;
    if (singleSeconds > 60){
      $scope.singleTimer.minutes = Math.floor(singleSeconds / 60);
      $scope.singleTimer.seconds = singleSeconds % 60;
    } else {
     $scope.singleTimer.minutes = 0;
      $scope.singleTimer.seconds = singleSeconds;
    }

    if ($scope.totalTimer.status && $scope.timerDelay != null){
      $scope.transitionAction();
    } else if (!$scope.totalTimer.status && $scope.timerDelay != null && !$scope.isAutoStart){
      $scope.transitionTimer = $scope.advancedTiming.transitionTime;
      $scope.transitionAction();
    } else if ($scope.totalTimer.status && $scope.timerDelay == null){
      $scope.transitionTimer = $scope.advancedTiming.transitionTime;
      $scope.transitionAction();
    }

    $scope.playNextSound($scope.currentExercise);
    $scope.updateTime();
    if ($scope.currentExercise.switchOption){
      $scope.helpText = $translate.instant('CHANGE_SIDE');
    } else{
      $scope.helpText = false;
    }
    $scope.changeText = false;
    $scope.totalWidth = 100 - (((($stateParams.timeId * 60) - ((($scope.totalTimer.minutes) * 60) + $scope.totalTimer.seconds)) / ($stateParams.timeId * 60)) * 100);
  };
  $scope.backExercise = function (){
    if ($scope.previousExercise){
      angular.element(document.getElementById('video-background')).css('opacity','0.00001');
      $scope.nextExercise.status = false;
      $interval.cancel($scope.transitionCountdown);
      $timeout.cancel($scope.delayStart);
      $scope.currentWorkout.unshift(translations['EN'][$scope.previousExercise.name]);
      $scope.previousExercise = false;
      $scope.nextExercise.image = exerciseObject[$scope.currentWorkout[0]].image;
      $scope.currentExercise = exerciseObject[$scope.currentWorkout[0]];
      if ($scope.androidPlatform && device){
      } else {
        $scope.videoAddress = 'video/' + $scope.currentExercise.video;
      }
      var videoFrame = angular.element(document.getElementById('inline-video'))[0];
      if ($scope.advancedTiming.autoPlay){
        if (ionic.Platform.isAndroid() && device){
          window.plugins.html5Video.initialize({
            "inlinevideo" : $scope.currentExercise.video
          })
          setTimeout(function(){
            playInlineVideo($scope.advancedTiming.autoPlay);
          }, 500);
          setTimeout(function(){
            angular.element(document.getElementById('video-background')).css('opacity','1');
            angular.element(document.getElementById('inlinevideo')).css('opacity','1');
            $scope.nextExercise.image = exerciseObject[$scope.currentWorkout[1]].image;
            $scope.$apply();
          }, 1500)

        } else{
          clearTimeout(inlineVideoTimeout);
          angular.element(document.getElementById('inline-video')).css('opacity','0.0001');
          var playEventListener = function(){
            setTimeout(function(){angular.element(document.getElementById('inline-video')).css('opacity','1');
              playInlineVideo($scope.advancedTiming.autoPlay, exerciseObject[$scope.currentWorkout[0]]);
              $scope.nextExercise.image = exerciseObject[$scope.currentWorkout[1]].image;
              $scope.$apply();
            }, 500);
            angular.element(document.getElementById('video-background')).css('opacity','1');
            videoFrame.removeEventListener('canplaythrough', playEventListener);
          }
          videoFrame.addEventListener('canplaythrough', playEventListener);
        }
      } else {
        setTimeout(function(){angular.element(document.getElementById('inline-video')).css('opacity','1');
          $scope.nextExercise.image = exerciseObject[$scope.currentWorkout[1]].image;
        }, 500);
        angular.element(document.getElementById('video-background')).css('opacity','1');
      }

      var singleSeconds = $scope.advancedTiming.exerciseTime;
      if (singleSeconds > 60){
        $scope.singleTimer.minutes = Math.floor(singleSeconds / 60);
        $scope.singleTimer.seconds = singleSeconds % 60;
      } else {
       $scope.singleTimer.minutes = 0;
        $scope.singleTimer.seconds = singleSeconds;
      }
      if ($scope.totalTimer.status && $scope.timerDelay != null){
        $scope.transitionAction();
      } else if (!$scope.totalTimer.status && $scope.timerDelay != null){
        $scope.transitionTimer = $scope.advancedTiming.transitionTime;
        $scope.transitionAction();
      } else if ($scope.totalTimer.status && $scope.timerDelay == null){
        $scope.transitionTimer = $scope.advancedTiming.transitionTime;
        $scope.transitionAction();
      }
      $scope.playNextSound($scope.currentExercise);
      $scope.updateTime();
      if ($scope.currentExercise.switchOption){
        $scope.helpText = $translate.instant('CHANGE_SIDE');
      } else{
        $scope.helpText = false;
      }
      $scope.changeText = false;
      $scope.toggleControls(true);
    }

  };
  $scope.swipeLeftSkip = function(){
    $scope.skipExercise();
  }
  $scope.swipeRightBack = function(){
    $scope.backExercise();
  }
  $scope.$on('$ionicView.leave', function() {
    $scope.stopTimer();
    if ($scope.isEndAdCampaign && globalSworkitAds.imageSuccess) {
      $scope.hiddenURL.close();
    }
    angular.element(document.getElementsByClassName('title')).removeClass('no-nav');
    angular.element(document.getElementsByTagName('body')[0]).removeClass('workout-bar');
    if ($scope.androidPlatform & device){
      document.querySelectorAll("drawer")[0].attributes.candrag.value = true;
    } else if (device){
      StatusBar.show();
    }
    if (lockOrientation()){
      try{
        cordova.plugins.screenorientation.lockOrientation('portrait');
      } catch(e){
        if (device){screen.lockOrientation('portrait');};
      }
    }
    $ionicNavBarDelegate.showBar(true);
    $ionicHistory.clearCache();
    localforage.getItem('timingSettings', function(result){TimingData.GetTimingSettings = result})
    if (device){
      LowLatencyAudio.unload('ding');
      LowLatencyAudio.unload('begin');
      LowLatencyAudio.unload('switch');
      LowLatencyAudio.unload('switchding');
      LowLatencyAudio.unload('next');
      LowLatencyAudio.unload('countdown');
      LowLatencyAudio.unload('countdownVoice');
      LowLatencyAudio.unload('break');
      LowLatencyAudio.unload('congrats');
    }
  });

  //Audio section
  var basicAudioPath = 'audio/';
  var normalAudioPath;
  if (ionic.Platform.isAndroid()){
    normalAudioPath = 'audio/';
  } else {
    normalAudioPath = 'audio/';
  }
  $scope.isAudioAvailable = PersonalData.GetLanguageSettings[$scope.userSettings.preferredLanguage];
  $scope.setAudioPaths = function(){
    if ($scope.userSettings.preferredLanguage == 'EN') {
      // Don't change paths here //
    } else if ($scope.isAudioAvailable){
      normalAudioPath = cordova.file.dataDirectory + $scope.userSettings.preferredLanguage + '/';
      basicAudioPath = 'audio/' + $scope.userSettings.preferredLanguage + '/';
    } else if ($scope.userSettings.preferredLanguage !== 'EN') {
      basicAudioPath = 'audio/' + $scope.userSettings.preferredLanguage + '/';
    }
  }
  $scope.setAudioPaths();

  if (device){
    $timeout(function(){
      LowLatencyAudio.preloadAudio('begin', basicAudioPath + 'begin.mp3', 1);
      LowLatencyAudio.preloadAudio('ding', 'audio/ding.mp3', 1);
      LowLatencyAudio.turnOffAudioDuck(PersonalData.GetAudioSettings.duckOnce.toString());
    }, 600)
    $timeout(function(){
      if ($scope.isAudioCampaign && globalSworkitAds.audioSuccess && !globalFirstOption && $scope.advancedTiming.autoStart && globalFirstWorkout){
        if (ionic.Platform.isAndroid()){
          LowLatencyAudio.preloadFX('restBreakAd', cordova.file.dataDirectory + 'ads/' + globalSworkitAds.adRestAudioName);             
        } else {
          LowLatencyAudio.preloadAudio('restBreakAd', cordova.file.dataDirectory + 'ads/' + globalSworkitAds.adRestAudioName,1);             
        }
        $timeout(function(){
          LowLatencyAudio.play('restBreakAd', $scope.audioSettings.duckEverything.toString());
        }, 300);
        $timeout(function(){
          LowLatencyAudio.unload('restBreakAd');
          trackEvent('Ad Audio', 'campaignName', $stateParams.typeId);
        }, 15000);
        if ($scope.advancedTiming.autoStart && PersonalData.GetLanguageSettings[$scope.userSettings.preferredLanguage] && true){
            $scope.firstExerciseAudio = $timeout(function(){
              $scope.playNextSound(exerciseObject[$scope.currentWorkout[0]], true);
            }, 9000);
        }
      } else if (TimingData.GetTimingSettings.welcomeAudio){
        LowLatencyAudio.play('welcome', $scope.audioSettings.duckEverything.toString());
        $timeout(function(){
          if ($scope.advancedTiming.autoStart && PersonalData.GetLanguageSettings[$scope.userSettings.preferredLanguage] && true){
              var timeoutLength = globalFirstOption ? 7000 : 4000;
              $scope.firstExerciseAudio = $timeout(function(){
                $scope.playNextSound(exerciseObject[$scope.currentWorkout[0]], true);
              }, timeoutLength);
          }
        },1200)
      }
      $scope.changeURX = true;
    }, 1000)  
    $timeout(function(){
      LowLatencyAudio.preloadAudio('switch', basicAudioPath + 'changeSides.mp3', 1);
      LowLatencyAudio.preloadAudio('switchding', 'audio/switch.mp3', 1);
      LowLatencyAudio.preloadAudio('next', basicAudioPath + 'Next.mp3', 1);
      LowLatencyAudio.preloadAudio('countdown', 'audio/beepsequence.mp3', 1);
      LowLatencyAudio.preloadAudio('countdownVoice', basicAudioPath + 'countdownVoice.mp3', 1);
      trackEvent('Workout Type', translations['EN'][$scope.chosenWorkout.activityNames], $stateParams.timeId);
      $scope.changeURX = false;
    }, 4000)
  }

  $scope.urlCounter=Math.floor(Math.random()*100000);
  $scope.playNextSound = function(currentEx, firstAudio){
    if (device) {
      $scope.urlCounter = $scope.urlCounter +1;
      var muteUnmute = $scope.extraSettings.audioOption;
      var exerciseNum = "exercise" + $scope.urlCounter.toString();
      var audioURL = normalAudioPath + currentEx.audio;
      if (!ionic.Platform.isAndroid()){
        LowLatencyAudio.preloadAudio(exerciseNum, audioURL, 1);
        $scope.unloadQueue.unshift(exerciseNum);
      } else {
        LowLatencyAudio.preloadFX(exerciseNum, audioURL);
        $scope.unloadQueue.unshift(exerciseNum);
      }
      if (muteUnmute && $scope.isAudioAvailable){
        $timeout(function(){
         LowLatencyAudio.play(exerciseNum, $scope.audioSettings.duckEverything.toString());
         $scope.unloadAudio();
        }, 300);
        
        if (firstAudio){
          $scope.isAudioAvailable = PersonalData.GetLanguageSettings[$scope.userSettings.preferredLanguage];
          if ($scope.isAudioAvailable){
            $scope.setAudioPaths();
          }
        }
      } else{
        if ($scope.yogaSelection && !firstAudio){
          LowLatencyAudio.play('switchding', $scope.audioSettings.duckEverything.toString());
        } else if (!firstAudio){
          LowLatencyAudio.play('ding', $scope.audioSettings.duckEverything.toString());
        }
        $scope.isAudioAvailable = PersonalData.GetLanguageSettings[$scope.userSettings.preferredLanguage];
        if ($scope.isAudioAvailable){
          $scope.setAudioPaths();
        }
        $scope.unloadAudio();
      }
    } else {
      console.log('Sound: Exercise name ');
    }
      
  }
  $scope.numberOfRests = 0;
  $scope.playBreakSound = function(){
    if (device){
      var muteUnmute = $scope.extraSettings.audioOption;
      if ($scope.advancedTiming.breakTime == 30){
          LowLatencyAudio.preloadAudio('break', basicAudioPath + 'Break.mp3',1);
          $scope.numberOfRests = 1;
      } else{
          LowLatencyAudio.preloadAudio('break', basicAudioPath + 'TakeBreak.mp3',1);
      }
      if (muteUnmute){
        $timeout(function(){
          LowLatencyAudio.play('break', $scope.audioSettings.duckEverything.toString());
        }, 300);
      } else {
        LowLatencyAudio.play('ding', $scope.audioSettings.duckEverything.toString());
      }
    } else {
      console.log('Sound: take a break');
    }
    trackEvent('Monetize Event', 'Rest Break', $scope.advancedTiming.breakTime);
  }
  $scope.playSwitchSound = function(){
    $scope.transitionPause();
    if (device){
      var muteUnmute = $scope.extraSettings.audioOption;
      if (muteUnmute){
        $timeout(function(){
          LowLatencyAudio.play('switch', $scope.audioSettings.duckEverything.toString());
        }, 300);
      }
      else{
        $timeout(function(){
          LowLatencyAudio.play('switchding', $scope.audioSettings.duckEverything.toString());
        }, 300);
      }
    } else {
      console.log('Sound: switch sides');
    }
  }
  $scope.playNextWarning = function(currentEx){
    if (device){
      $scope.urlCounter = $scope.urlCounter +1;
      var muteUnmute = $scope.extraSettings.audioOption;
      var exerciseNum = "exercise" + $scope.urlCounter.toString();
      var audioURL = normalAudioPath + currentEx.audio;
      var muteUnmute = $scope.extraSettings.audioOption;
      if (!ionic.Platform.isAndroid()){
        LowLatencyAudio.preloadAudio(exerciseNum, audioURL, 1);
        $scope.unloadQueue.unshift(exerciseNum);
      } else {
        LowLatencyAudio.preloadFX(exerciseNum, audioURL);
        $scope.unloadQueue.unshift(exerciseNum);
      }
      if (muteUnmute && $scope.isAudioAvailable){
        $timeout(function(){
          LowLatencyAudio.play('next', "false");
        },0);
        $timeout(function(){
           LowLatencyAudio.play(exerciseNum, $scope.audioSettings.duckEverything.toString());
        }, 1600);
      }
    } else {
      console.log('Sound: next warning');
    }
  }

  $scope.playCountdown = function(){
    if (device && $scope.extraSettings.countdownStyle){
      $timeout(function(){
        LowLatencyAudio.play('countdownVoice', "false");
      }, 300);
    } else if (device){
      $timeout(function(){
        LowLatencyAudio.play('countdown', "false");
      }, 300);
    } else {
      console.log('Sound: Countdown');
    }
  }
  $scope.playBeginSound = function(){
    $scope.showBegin();
    if (device){
      $timeout(function(){
        LowLatencyAudio.play('begin', $scope.audioSettings.duckEverything.toString());
      }, 300);
    } else {
      console.log('Sound: Begin');
    }
  }
  $scope.playCongratsSound = function(){
    if (device){
      LowLatencyAudio.preloadAudio('congrats', basicAudioPath + 'Congrats.mp3', 1);
      $timeout(function(){
        LowLatencyAudio.play('congrats', $scope.audioSettings.duckEverything.toString());
      }, 300);
    } else {
      console.log('Sound: Congrats!');
    }
  }
  $scope.unloadAudio = function(){
    $timeout(function(){
        for (i=$scope.unloadQueue.length-1;i>=2;i--){
          LowLatencyAudio.unload($scope.unloadQueue[i]);
          $scope.unloadQueue.splice((i), 1);
        }
    }, 2500);
  }
  $scope.toggleAudio = function(){
    $scope.extraSettings.audioOption = !$scope.extraSettings.audioOption;
  }
  $scope.toggleControls = function(override){
    if (!$scope.isPortrait && override){
      $scope.showControls = true;
      $timeout.cancel($scope.controlTimeout);
    }
    else if (!$scope.isPortrait && !$scope.showControls){
      $scope.showControls = true;
      $timeout.cancel($scope.controlTimeout);
      $scope.controlTimeout = $timeout(function(){
        $scope.showControls = true;
      }, 3000)
    } else if (!$scope.isPortrait && $scope.showControls){
      $scope.showControls = false;
      $timeout.cancel($scope.controlTimeout);
    }
  }
  $scope.toggleVideo = function(e){
    var videoElement = angular.element(document.getElementById('inline-video'))[0];
    if (ionic.Platform.isAndroid() && device){
      videoElement.paused ? playInlineVideo($scope.advancedTiming.autoPlay) : videoElement.pause();
    } else{
      videoElement.paused ? playInlineVideo($scope.advancedTiming.autoPlay, exerciseObject[$scope.currentWorkout[0]]) : videoElement.pause();
    }
    videoElement.muted= true;
  }
  $scope.increaseTempo = function(){
    $scope.advancedTiming.exerciseTime ++;
    var singleSeconds = $scope.advancedTiming.exerciseTime;
    if (singleSeconds > 60){
      $scope.singleTimer.minutes = Math.floor(singleSeconds / 60);
      $scope.singleTimer.seconds = singleSeconds % 60;
    } else {
      $scope.singleTimer.minutes = 0;
      $scope.singleTimer.seconds = singleSeconds;
    }
    $scope.updateTime();
  }
  $scope.decreaseTempo = function(){
    if ($scope.advancedTiming.exerciseTime > 1){
      $scope.advancedTiming.exerciseTime --;
      var singleSeconds = $scope.advancedTiming.exerciseTime;
      if (singleSeconds > 60){
        $scope.singleTimer.minutes = Math.floor(singleSeconds / 60);
        $scope.singleTimer.seconds = singleSeconds % 60;
      } else {
        $scope.singleTimer.minutes = 0;
        $scope.singleTimer.seconds = singleSeconds;
      }
      $scope.updateTime();
    }
  }
  $scope.transitionAction = function(autostart, continueTimer){
    if (autostart && !continueTimer){
      var transitionLength = 12;
      $scope.transitionTimer = 12;
    } else if(continueTimer){
      $timeout.cancel($scope.transitionCountdown);
    } else {
      var transitionLength = $scope.advancedTiming.transitionTime;
      $scope.transitionTimer = $scope.advancedTiming.transitionTime;
    }
    $scope.transitionCountdown = $interval(function(){$scope.transitionTimer--;}, 1000)
    if (autostart && !continueTimer){
        $scope.timerDelay = 0;
        $scope.stopTimer();
        $scope.transitionStatus = true;
        $scope.delayStart = $timeout(function(){$scope.playBeginSound();$scope.isAutoStart = false;$scope.startTimer();$scope.timerDelay = null;$scope.transitionStatus = false;$interval.cancel($scope.transitionCountdown);
        }, 12300);
    }
    else if (transitionLength == 10 && $stateParams.typeId == 'sevenMinute'){
        $scope.timerDelay = 0;
        $scope.stopTimer();
        $scope.transitionStatus = true;
        $scope.delayStart = $timeout(function(){$scope.playBeginSound();$scope.startTimer();$scope.timerDelay = null;$scope.transitionStatus = false;$interval.cancel($scope.transitionCountdown);
        }, 10300);
    }
    else if (transitionLength > 0 && transitionLength <= 4 ){
        $scope.timerDelay = 0;
        $scope.stopTimer();
        $scope.transitionStatus = true;
        $scope.delayStart = $timeout(function(){$scope.startTimer();$scope.timerDelay = null;$scope.transitionStatus = false;$interval.cancel($scope.transitionCountdown);
        }, transitionLength*1000);
    }
    else if (transitionLength > 4){
        $scope.timerDelay = 0;
        $scope.stopTimer();
        $scope.transitionStatus = true;
        $scope.delayStart = $timeout(function(){$scope.playBeginSound();$scope.startTimer();$scope.timerDelay = null;$scope.transitionStatus = false;$interval.cancel($scope.transitionCountdown);
        }, transitionLength*1000);
    } 
    else{
      $interval.cancel($scope.transitionCountdown);
    }
  }
  $scope.transitionPause = function(){
      if ($scope.advancedTiming.transitionTime > 0){
          $scope.timerDelay = 0;
          $scope.stopTimer();
          $scope.transitionTimer = 5;
          $scope.transitionCountdown = $interval(function(){$scope.transitionTimer--;}, 1000);
          $scope.transitionStatus = true;
          $scope.delayStart = $timeout(function(){$scope.changeText = false;$scope.helpText = false;$scope.playBeginSound();$scope.startTimer();$scope.timerDelay = null;$scope.transitionStatus = false;$interval.cancel($scope.transitionCountdown);
          }, 5000);
      } else{
        $scope.timerDelay = 0;
          $scope.stopTimer();
          $scope.transitionTimer = 3;
          $scope.transitionCountdown = $interval(function(){$scope.transitionTimer--;}, 1000);
          $scope.transitionStatus = true;
          $scope.delayStart = $timeout(function(){$scope.changeText = false;$scope.helpText = false;$scope.playBeginSound();$scope.startTimer();$scope.timerDelay = null;$scope.transitionStatus = false;$interval.cancel($scope.transitionCountdown);
          }, 3000);
      }
  }

  if (ionic.Platform.isAndroid()){
    var workoutBack = $ionicPlatform.registerBackButtonAction(
      function () {
        if (!$scope.endModalOpen){
          $scope.endWorkout();
        } else if ($scope.endModalOpen && !$scope.workoutComplete ){
          $scope.cancelModal();
        } else if ($scope.endModalOpen && $scope.workoutComplete ){
          $scope.mainMenu();
        }
      }, 250
    );
  }

  $scope.launchURX = function(){
    if (device){
      workoutOnPause();
      cordova.exec(function (){trackEvent('URX Launched', 'Workout Screen', 0);}, function (){}, "URX", "searchSongs", ['"sworkit workout playlist" OR workout playlist action:ListenAction']);
    }
  }

  var workoutOnPause = function(){
    $scope.stopTimer();
    $interval.cancel($scope.transitionCountdown);
    $timeout.cancel($scope.delayStart);
    $scope.transitionStatus = false;
    $scope.timerDelay = null;
  }

  document.addEventListener("pause", workoutOnPause, false);

  var orientationChange = function(){
    if (!$scope.isPortrait){
      $scope.isPortrait = true;
    }
    $timeout(function(){
      $scope.setVideo();
    }, 500)
  }
  
  var onResumeWorkout = function(){
    if (!ionic.Platform.isAndroid()){
      clearTimeout(inlineVideoTimeout);
      playInlineVideo($scope.advancedTiming.autoPlay, exerciseObject[$scope.currentWorkout[0]]);      
    }
  }
  
  window.addEventListener("orientationchange", orientationChange , false);
  document.addEventListener("resume", onResumeWorkout, false);

  $scope.$on('$ionicView.leave', workoutBack);
})

.controller('RewardsCtrl', function($rootScope, $scope, UserService) {
  $scope.sessionmStatus = sessionmAvailable;
  $scope.rewardStatus = UserService.getUserSettings();
  $scope.sessionMCount = {count:false};
  $scope.$on('$ionicView.enter', function () {
    angular.element(document.getElementsByClassName('bar-header')).addClass('green-bar');
  });
  $scope.getSessionMCount = function(){
    sessionm.phonegap.getUnclaimedAchievementCount(function callback(data) {
        $scope.sessionMCount.count = (data.unclaimedAchievementCount == 0) ? false : data.unclaimedAchievementCount;
        $rootScope.mPointsTotal = data.unclaimedAchievementCount;  
        $scope.$apply();
      });
  }
  if (device){
    $scope.getSessionMCount();
    sessionm.phonegap.listenDidDismissActivity(function callback(data2) {
      $scope.getSessionMCount();
    });
  }
  $scope.launchSessionM = function(){
    if (device){
      sessionm.phonegap.presentActivity(2);
    }
  }
  $scope.rewardsFAQ = function(){
    window.open('http://sworkit.com/rewards', 'blank', 'location=yes,AllowInlineMediaPlayback=yes,toolbarposition=top' );
  }
  $scope.disableRewards = function(typeReward){
    if (typeReward == 'sessionm' && $scope.rewardStatus.mPoints == true){
      trackEvent('More Action', 'Disable SessionM', 0);
    } else if (typeReward == 'kiip' && $scope.rewardStatus.kiipRewards == true){
      trackEvent('More Action', 'Disable Kiip', 0);
    }
  }

  $scope.$on('$ionicView.leave', function() {
               localforage.setItem('userSettings', PersonalData.GetUserSettings);
               angular.element(document.getElementsByClassName('bar-header')).removeClass('green-bar');
               });
})

.controller('ProgressCtrl', function($scope, $location, $ionicPlatform, $translate, UserService) {
   $scope.totals = {'totalEver':0,'todayMinutes':0,'todayCalories':0,'weeklyMinutes':0,'weeklyCalories':0, 'totalMonthMin':0, 'topMinutes':0, 'topCalories':0, 'topDayMins':'', 'topDayCals':''};
   $scope.goalSettings = UserService.getGoalSettings();
   buildStats($scope);
   logActionSessionM('View Progress');
   if (device){
    navigator.globalization.getLocaleName(
      function(returnResult){
        var returnCountry;
        if (ionic.Platform.isAndroid()){
          returnCountry = returnResult.value[2];
        } else {
          returnCountry = returnResult.value;
        }
        if (returnCountry.slice(-2).toUpperCase() == 'US'){
          isUSA = true;
        } else {
          isUSA = false;
        }
      },
      function(error){
        isUSA = false;
      }
    )
  }
})

.controller('LogCtrl', function($scope, $ionicLoading, $stateParams, $location, $translate, $ionicPlatform, $ionicPopup, $http, UserService) {
  $ionicLoading.show({
      template: $translate.instant('LOADING_W')
  });
  $scope.noLogs = false;
  $scope.userSettings = UserService.getUserSettings();
  db.transaction(
               function(transaction) {
               transaction.executeSql("SELECT * FROM Sworkit",
                                      [],
                                      $scope.createLog,
                                      null)
               }
               );
  $scope.createLog = function(tx, results){
            $scope.allLogs = [];
            if (results.rows.length ==0){
                $scope.noLogs = true;
                $ionicLoading.hide();
            }
            //TODO: Translate these months and use proper date format
            var month_names_short = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            var totalRows = 0;
            for (var i = results.rows.length - 1; i > -1; i--) {
                var createdDate;
                var wasError = false;
                var useID = results.rows.item(i)['sworkit_id'];
                var activityTitle = LocalData.GetWorkoutTypes[results.rows.item(i)['type']].activityNames;
                var useCalories = results.rows.item(i)['calories'];
                var useMinutes = results.rows.item(i)['minutes_completed'];
                if (!device || (isUSA && $scope.userSettings.preferredLanguage == 'EN' && true)){
                  var useDate = results.rows.item(i)['created_on'].split(/[- :]/);
                  createdDate = new Date(useDate[0], useDate[1]-1, useDate[2], useDate[3], useDate[4], useDate[5]);
                  var ampm = (createdDate.getHours() > 11) ? "pm" : "am";
                  var useHour = (createdDate.getHours() > 12) ? createdDate.getHours() - 12 : createdDate.getHours();
                  var useMinute = (createdDate.getMinutes() < 10) ? "0" + createdDate.getMinutes()  : createdDate.getMinutes();
                  createdDate = month_names_short[createdDate.getMonth()] + ' ' + createdDate.getDate() + ', ' + useHour + ":" + useMinute + " " + ampm;
                  if (!activityTitle){
                    activityTitle = "Workout";
                  }
                  $scope.allLogs.push({'id':useID,
                    'createdDate': createdDate,
                    'useMinutes': useMinutes,
                    'activityTitle': activityTitle,
                    'useCalories': useCalories,
                  })
                  totalRows++;
                  if (totalRows = results.rows.length){
                    $ionicLoading.hide();
                  }
                } else {
                  var useDate = results.rows.item(i)['created_on'].split(/[- :]/);
                  useDate = new Date(useDate[0], useDate[1]-1, useDate[2], useDate[3], useDate[4], useDate[5]);
                  navigator.globalization.dateToString(useDate, function(result){
                    createdDate = result.value;
                    if (!activityTitle){
                      activityTitle = "Workout";
                    }
                    $scope.allLogs.push({'id':useID,
                      'createdDate': createdDate,
                      'useMinutes': useMinutes,
                      'activityTitle': activityTitle,
                      'useCalories': useCalories,
                    })
                    totalRows++;
                    if (totalRows = results.rows.length){
                      $ionicLoading.hide();
                    }
                  }, function(error){}, {formatLength:'short', selector:'date and time'});
                } 

            }
  }
  $scope.syncRow = function (rowId){
    var fakeData = {"sworkit_id":44,"created_on":"2014-06-26 14:08:06","minutes_completed":0,"calories":0,"type":"fullBody","utc_created":"2014-06-26 13:08:06"} 
    db.transaction(function(transaction) {
                           transaction.executeSql('SELECT * FROM Sworkit WHERE sworkit_id = ?',[rowId],
                                                  function(tx, results){
                                                    var syncableWorkout = results.rows.item(0);
                                                    var dateString = syncableWorkout["utc_created"];
                                                    var actionString = "log_cardio_exercise";
                                                    var accessString = PersonalData.GetUserSettings.mfpAccessToken;
                                                    var appID = "79656b6e6f6d";
                                                    var exerciseID = LocalData.GetWorkoutTypes[syncableWorkout["type"]].activityMFP;
                                                    var durationFloat = syncableWorkout["minutes_completed"] * 60000;
                                                    var energyCalories = syncableWorkout["calories"];;
                                                    var unitCountry = "US";
                                                    var statusMessage = "burned %CALORIES% calories doing %QUANTITY% minutes of " + LocalData.GetWorkoutTypes[syncableWorkout["type"]].activityNames + " with Sworkit";
                                                    var dataPost = JSON.stringify({'action' : actionString, 'access_token' : accessString,'app_id': appID, 'exercise_id': exerciseID, 'duration': durationFloat, 'energy_expended': energyCalories, 'start_time' : dateString, 'status_update_message': statusMessage, 'units': unitCountry});
                                                    $http({
                                                      method: 'POST',
                                                      url: 'https://www.myfitnesspal.com/client_api/json/1.0.0?client_id=sworkit',
                                                      data: dataPost,
                                                      headers: {'Content-Type': 'application/json'}
                                                    }).then(function(resp){
                                                      showNotification('Successly logged to MyFitnessPal', 'button-calm', 2000);
                                                     }, function(err) {
                                                      if ($scope){
                                                        showNotification('Unable to log to MyFitnessPal', 'button-assertive', 2000);
                                                      }
                                                    })
                                                  },
                                                  null);
                           });
  }
  $scope.deleteRow = function(rowId){
    if (device){
            navigator.notification.confirm(
          'Are you sure you want to delete this workout?',
           function(buttonIndex){
            if (buttonIndex == 2){
              db.transaction(function(transaction) {
                              transaction.executeSql('DELETE FROM Sworkit WHERE sworkit_id = ?',[rowId]);
                              });
              $scope.allLogs.forEach(function(element, index, array){if (element.id == rowId){$scope.allLogs.splice(index, 1);}});
              $scope.$apply();
            }
           },
          'Delete Workout',
          ['Cancel','Delete']
      );
    } else{
      $ionicPopup.confirm({
             title: 'Delete Workout',
             template: '<p class="padding">Are you sure you want to delete this workout?</p>',
             okType: 'assertive',
             okText: 'Delete'
           }).then(function(res) {
             if(res) {
                db.transaction(function(transaction) {
                              transaction.executeSql('DELETE FROM Sworkit WHERE sworkit_id = ?',[rowId]);
                              });
                $scope.allLogs.forEach(function(element, index, array){if (element.id == rowId){$scope.allLogs.splice(index, 1);}});
                $scope.$apply();
             }
           }); 
      }              
    }
})

.controller('RemindersCtrl', function($scope, $translate, UserService) {
  $scope.$on('$ionicView.enter', function () {
    angular.element(document.getElementsByClassName('bar-header')).addClass('blue-bar');
  });
  if (isNaN(LocalData.SetReminder.daily.minutes)){
    LocalData.SetReminder.daily.minutes = 0;
  } 
  if (isNaN(LocalData.SetReminder.daily.time)){
    LocalData.SetReminder.daily.time = 7;
  }
  if (isNaN(LocalData.SetReminder.inactivity.minutes)){
    LocalData.SetReminder.inactivity.minutes = 0;
  } 
  if (isNaN(LocalData.SetReminder.inactivity.time)){
    LocalData.SetReminder.inactivity.time = 7;
  }
  if (isNaN(LocalData.SetReminder.inactivity.frequency)){
    LocalData.SetReminder.inactivity.frequency = 2;
  }
  $scope.reminderText = {message:''};
  if (device){
    window.plugin.notification.local.hasPermission(function (granted) {
        if (!granted){
          $scope.reminderText.message = $translate.instant('UPDATE_REMINDER');
        }
    });    
  }

  $scope.reminderTimes = {selected: 7, times:[{id:0, real: '12', time:'12 am', short:'am'},{id:1, real: '1', time:'1 am', short:'am'},{id:2, real: '2', time:'2 am', short:'am'},{id:3, real: '3', time:'3 am', short:'am'},{id:4, real: '4', time:'4 am', short:'am'},{id:5, real: '5', time:'5 am', short:'am'},{id:6, real: '6', time:'6 am', short:'am'},{id:7, real: '7', time:'7 am', short:'am'},{id:8, real: '8', time:'8 am', short:'am'},{id:9, real: '9', time:'9 am', short:'am'},{id:10, real: '10', time:'10 am', short:'am'},{id:11, real: '11', time:'11 am', short:'am'},{id:12, real: '12', time:'12 pm', short:'pm'},{id:13, real: '1', time:'1 pm', short:'pm'},{id:14, real: '2', time:'2 pm', short:'pm'},{id:15, real: '3', time:'3 pm', short:'pm'},{id:16, real: '4', time:'4 pm', short:'pm'},{id:17, real: '5', time:'5 pm', short:'pm'},{id:18, real: '6', time:'6 pm', short:'pm'},{id:19, real: '7', time:'7 pm', short:'pm'},{id:20, real: '8', time:'8 pm', short:'pm'},{id:21, real: '9', time:'9 pm', short:'pm'},{id:22, real: '10', time:'10 pm', short:'pm'},{id:23, real: '11', time:'11 pm', short:'pm'}], reminder: false};
  $scope.inactivityTimes = {frequency: 2, selected: 7, times:[{id:0, real: '12', time:'12 am', short:'am'},{id:1, real: '1', time:'1 am', short:'am'},{id:2, real: '2', time:'2 am', short:'am'},{id:3, real: '3', time:'3 am', short:'am'},{id:4, real: '4', time:'4 am', short:'am'},{id:5, real: '5', time:'5 am', short:'am'},{id:6, real: '6', time:'6 am', short:'am'},{id:7, real: '7', time:'7 am', short:'am'},{id:8, real: '8', time:'8 am', short:'am'},{id:9, real: '9', time:'9 am', short:'am'},{id:10, real: '10', time:'10 am', short:'am'},{id:11, real: '11', time:'11 am', short:'am'},{id:12, real: '12', time:'12 pm', short:'pm'},{id:13, real: '1', time:'1 pm', short:'pm'},{id:14, real: '2', time:'2 pm', short:'pm'},{id:15, real: '3', time:'3 pm', short:'pm'},{id:16, real: '4', time:'4 pm', short:'pm'},{id:17, real: '5', time:'5 pm', short:'pm'},{id:18, real: '6', time:'6 pm', short:'pm'},{id:19, real: '7', time:'7 pm', short:'pm'},{id:20, real: '8', time:'8 pm', short:'pm'},{id:21, real: '9', time:'9 pm', short:'pm'},{id:22, real: '10', time:'10 pm', short:'pm'},{id:23, real: '11', time:'11 pm', short:'pm'}], reminder: false};         
  $scope.reminderMins = getMinutesObj();
  $scope.reminderMins.selected = $scope.reminderMins.times[LocalData.SetReminder.daily.minutes];
  $scope.reminderTimes.selected = $scope.reminderTimes.times[LocalData.SetReminder.daily.time];
  $scope.reminderTimes.reminder = LocalData.SetReminder.daily.status;
  $scope.inactivityMins = getMinutesObj();
  $scope.inactivityMins.selected = $scope.inactivityMins.times[LocalData.SetReminder.inactivity.minutes];
  $scope.inactivityTimes.selected = $scope.inactivityTimes.times[LocalData.SetReminder.inactivity.time];
  $scope.inactivityTimes.reminder = LocalData.SetReminder.inactivity.status;
  $scope.inactivityTimes.frequency = LocalData.SetReminder.inactivity.frequency;
  $scope.inactivityOptions = [1,2,3,4,5,6,7,8,9,10,11,12,13,14];
  if (device){window.plugin.notification.local.cancelAll();}
  var newDate = new Date();
  newDate.setHours($scope.reminderTimes.selected.id);
  newDate.setMinutes($scope.reminderMins.selected.id);
  var newDate2 = new Date();
  newDate2.setHours($scope.inactivityTimes.selected.id);
  newDate2.setMinutes($scope.inactivityMins.selected.id);

  $scope.datePickerOpen = function () {
    if (device){
      datePicker.show(
                                   {
                                   "date": newDate,
                                   "mode": "time"
                                   },
                                   function(returnDate){
                                    if (!isNaN(returnDate.getHours())){
                                      $scope.reminderTimes.selected = $scope.reminderTimes.times[returnDate.getHours()];
                                      $scope.reminderMins.selected = $scope.reminderMins.times[returnDate.getMinutes()];
                                      $scope.$apply();
                                    }
                                   }
                                   )
    }

  }
  $scope.datePicker2Open = function () {
    if (device){
      datePicker.show(
                               {
                               "date": newDate2,
                               "mode": "time"
                               },
                               function(returnDate){
                                if (!isNaN(returnDate.getHours())){
                                 $scope.inactivityTimes.selected = $scope.inactivityTimes.times[returnDate.getHours()];
                                 $scope.inactivityMins.selected = $scope.inactivityMins.times[returnDate.getMinutes()];
                                 $scope.$apply();
                                }
                               }
                               )
    }

  }

  $scope.closeScreen = function ($event) {
    if (device){
      LocalData.SetReminder.daily.time = $scope.reminderTimes.selected.id;
      LocalData.SetReminder.daily.minutes = $scope.reminderMins.selected.id;
      LocalData.SetReminder.daily.status = $scope.reminderTimes.reminder;
      LocalData.SetReminder.inactivity.time = $scope.inactivityTimes.selected.id;
      LocalData.SetReminder.inactivity.minutes = $scope.inactivityMins.selected.id;
      LocalData.SetReminder.inactivity.status = $scope.inactivityTimes.reminder;
      LocalData.SetReminder.inactivity.frequency = $scope.inactivityTimes.frequency;
      if (($scope.reminderTimes.reminder || $scope.inactivityTimes.reminder) && ionic.Platform.isIOS()){
        window.plugin.notification.local.hasPermission(function (granted) {
            if (!granted){
              window.plugin.notification.local.promptForPermission();
            }
        });
      }
      if ($scope.reminderTimes.reminder){
        var dDate = new Date();
        var tDate = new Date();
        dDate.setHours($scope.reminderTimes.selected.id);
        dDate.setMinutes($scope.reminderMins.selected.id);
        dDate.setSeconds(0);
        if ($scope.reminderTimes.selected.id <= tDate.getHours() && $scope.reminderMins.selected.id <= tDate.getMinutes()){
        dDate.setDate(dDate.getDate() + 1);
        }
        window.plugin.notification.local.add({
                                             id:         1,
                                             date:       dDate,    // This expects a date object
                                             message:    $translate.instant('TIME_TO_SWORKIT'),  // The message that is displayed
                                             title:      $translate.instant('WORKOUT_REM'),  // The title of the message
                                             repeat:     'daily',
                                             autoCancel: true,
                                             icon: 'ic_launcher',
                                             smallIcon: 'ic_launcher'
                                             });
        window.plugin.notification.local.onclick = function (id, state, json) {
            window.plugin.notification.local.cancel(1);
            var nDate = new Date();
            var tDate = new Date();
            nDate.setHours(LocalData.SetReminder.daily.time);
            nDate.setMinutes(LocalData.SetReminder.daily.minutes);
            nDate.setSeconds(0);
            if (tDate.getHours() <= nDate.getHours() && tDate.getMinutes() <= nDate.getMinutes()){
                nDate.setDate(nDate.getDate() + 1);
            }
            $timeout( function (){window.plugin.notification.local.add({
                                                                   id:         1,
                                                                   date:       nDate,    // This expects a date object
                                                                   message:    $translate.instant('TIME_TO_SWORKIT'),  // The message that is displayed
                                                                   title:      $translate.instant('WORKOUT_REM'),  // The title of the message
                                                                   repeat:     'daily',
                                                                   autoCancel: true,
                                                                   icon: 'ic_launcher',
                                                                   smallIcon: 'ic_launcher'
                                                                   });}, 2000);
        }
        logActionSessionM('SetReminder');
      }
      if ($scope.inactivityTimes.reminder){
        var dDate = new Date();
        dDate.setHours($scope.inactivityTimes.selected.id);
        dDate.setMinutes($scope.inactivityMins.selected.id);
        dDate.setSeconds(0);
        dDate.setDate(dDate.getDate() + $scope.inactivityTimes.frequency);
        window.plugin.notification.local.add({
                                             id:         2,
                                             date:       dDate,    // This expects a date object
                                             message:    $translate.instant('TOO_LONG'),  // The message that is displayed
                                             title:      $translate.instant('WORKOUT_REM'),  // The title of the message
                                             autoCancel: true,
                                             icon: 'ic_launcher',
                                             smallIcon: 'ic_launcher'
                                             });
        window.plugin.notification.local.onclick = function (id, state, json) {
            window.plugin.notification.local.cancel(2);
            var nDate = new Date();
            nDate.setHours(LocalData.SetReminder.inactivity.time);
            nDate.setMinutes(LocalData.SetReminder.inactivity.minutes);
            nDate.setSeconds(0);
            nDate.setDate(nDate.getDate() + $scope.inactivityTimes.frequency);
            $timeout( function (){window.plugin.notification.local.add({
                                                                   id:         2,
                                                                   date:       nDate,    // This expects a date object
                                                                   message:    "It's been too long. Time to Swork Out.",  // The message that is displayed
                                                                   title:      'Workout Reminder',  // The title of the message
                                                                   autoCancel: true,
                                                                   icon: 'ic_launcher',
                                                                   smallIcon: 'ic_launcher'
                                                                   });}, 2000);
        }
        logActionSessionM('SetReminder');
      }

      localforage.setItem('reminder',{daily: {
        status:$scope.reminderTimes.reminder,
        time:$scope.reminderTimes.selected.id,
        minutes:$scope.reminderMins.selected.id},
        inactivity: {
          status:$scope.inactivityTimes.reminder,
          time:$scope.inactivityTimes.selected.id,
          minutes:$scope.inactivityMins.selected.id,
          frequency:$scope.inactivityTimes.frequency
        }
        });
    }
  }

  $scope.$on('$ionicView.leave', function() {
               $scope.closeScreen();
               angular.element(document.getElementsByClassName('bar-header')).removeClass('blue-bar');
               });
})

.controller('SettingsCtrl', function($rootScope, $scope, $http, $ionicModal, $translate, $timeout, $ionicPopup, UserService) {
  $scope.userSettings = UserService.getUserSettings();
  $scope.googleFitSettings = UserService.getFitSettings();
  $scope.goalSettings = UserService.getGoalSettings();
  $scope.timeSettings = UserService.getTimingIntervals();
  $scope.originalLanguage = $scope.userSettings.preferredLanguage;
  $scope.healthKitAvailable = false;
  $scope.data = {showInfo:false};
  $scope.kindleDevice = false;
  if (ionic.Platform.isAndroid()){
    $scope.androidPlatform = true;
    if (isKindle()){
      $scope.kindleDevice = true;
    }
  } else{
    $scope.androidPlatform = false;
    if (device){
      window.plugins.healthkit.available(
                                               function(result){
                                                if (result == true){
                                                  $scope.healthKitAvailable = true;
                                                }
                                               },
                                               function(){
                                                $scope.healthKitAvailable = false;
                                               }
                                               );
    } else {
      //Available in browser for testing purposes
      $scope.healthKitAvailable = true;
    }
  }
  $scope.lowerAndroid = lowerAndroidGlobal;
  $scope.mfpWeightStatus = {data: $scope.userSettings.mfpWeight}
  $scope.displayWeight = {data: 0};
  $scope.weightTypes = [{id: 0, title:'LBS'}, {id:1, title:'KGS'}]
  $scope.selectedType = {data: $scope.weightTypes[$scope.userSettings.weightType]};
  $scope.languages = [
    {id:0, short:'DE', title:'Deutsch'},
    {id:1, short:'EN', title:'English'},
    {id:2, short:'ES', title:'Español (España)'},
    {id:3, short:'ESLA', title:'Español (América Latina)'},
    {id:4, short:'FR', title:'Français'},
    {id:5, short:'IT', title:'Italiano'},
    {id:6, short:'PT', title:'Português'},
    {id:7, short:'RU', title:'Русский'},
    {id:8, short:'TR', title:'Türkçe'},
    {id:9, short:'HI', title:'हिंदी'},
    {id:10, short:'JA', title:'日本語'},
    {id:11, short:'ZH', title:'简体中文'},
    {id:12, short:'KO', title:'한국어 [韓國語]'}
  ]
  // $scope.languages = [
  //   {id:0, short:'DE', title:'Deutsch'},
  //   {id:1, short:'EN', title:'English'},
  //   {id:2, short:'ES', title:'Español (España)'},
  //   {id:3, short:'ESLA', title:'Español (América Latina)'},
  //   {id:4, short:'FR', title:'Français'},
  //   {id:5, short:'IT', title:'Italiano'},
  //   {id:6, short:'PT', title:'Português'},
  //   {id:7, short:'HI', title:'हिंदी'},
  //   {id:8, short:'JA', title:'日本語'},
  //   {id:9, short:'ZH', title:'简体中文'},
  //   {id:10, short:'KO', title:'한국어 [韓國語]'},
  //   {id:11, short:'RU', title:'Русский'}
  // ]
  $scope.getLanguage = function(){
    var matchLang = '';
    $scope.languages.forEach(function(element, index, array){if (element.short == $scope.userSettings.preferredLanguage){matchLang = element}});
    return matchLang;
  }
  $scope.selectedLanguage = {data: $scope.getLanguage()};
  $scope.changeLanguage = function (langKey) {
    $translate.use(langKey.short);
    $scope.userSettings.preferredLanguage = $scope.selectedLanguage.data.short;
  };
  $scope.convertWeight = function(){
    if ($scope.userSettings.weightType == 0){
      $scope.displayWeight.data = $scope.userSettings.weight;
    } else{
      $scope.displayWeight.data = Math.round(($scope.userSettings.weight / 2.20462));
    }
  }
  $scope.convertWeight();
  $scope.$watch('selectedType.data', function(newValue, oldValue) {
        if (newValue.id){
          $scope.userSettings.weightType = newValue.id;
        }
        $scope.convertWeight();
  })
  $scope.$watch('displayWeight.data', function(val) {
    if ($scope.userSettings.weightType == 0){
      $scope.userSettings.weight = $scope.displayWeight.data;
    } else{
      $scope.userSettings.weight = Math.round(($scope.displayWeight.data * 2.20462));
    }
  })
  $scope.syncWeight = function(){
    if ($scope.mfpWeightStatus.data){
      getMFPWeight($http, $scope);
    }
  }
  $scope.connectedGoogleFit = function(){
    $scope.googleFitSettings.attempted = true;
    if ($scope.googleFitSettings.enabled){
      $scope.googleFitSettings.enabled = false;
    } else {
      $scope.enableGoogleFit()
    }
  }
  $scope.enableGoogleFit = function(){
    var infoTemplate = '<div class="end-workout-health" style="text-align: center;width:230px;margin:0px auto"><img src="img/googleFit.png" style="height:50px;display: block;margin: 10px auto;"/><div style="width:100%"><p>' + $translate.instant('GFIT_1') + '</p><p>' + $translate.instant('GFIT_2') + '</p><p style="color:#777;font-size:12px">' + $translate.instant('GFIT_3') + '</p><button class="button button-assertive" ng-click="confirmGoogleFit()" style="width:230px">{{"CONNECT_FIT" | translate}}</button></div></div>';
    $scope.googleFitPopup = $ionicPopup.show({
      title: '',
      subTitle: '',
      scope: $scope,
      template: infoTemplate,
      hardwareBackButtonClose: true,
      buttons: [
        { text: $translate.instant('CANCEL_SM') }
      ]
    });
  }
  $scope.hideGoogleFitPopup = function(){
    $scope.googleFitPopup.close();
  }
  $scope.confirmGoogleFit = function(){
    $scope.hideGoogleFitPopup();
    $scope.googleFitSettings.enabled = true;
    $scope.syncLastWorkoutFit();
  }
  $scope.syncLastWorkoutFit = function(){
    db.transaction(function (tx) {
     tx.executeSql('SELECT * FROM SworkitFree ORDER BY created_on', [], function (tx, results) {
        if (results.rows.length > 0){
          var fitnessActivity;
          var lastWorkout = results.rows.item(0);
          var lastDate = new Date(lastWorkout.utc_created);
          if (lastWorkout.type == "upperBody" || lastWorkout.type == "fullBody" || lastWorkout.type == "coreExercise" || lastWorkout.type == "lowerBody"){
              fitnessActivity = 'STRENGTH_TRAINING';
            } else if (lastWorkout.type == "stretchExercise" || lastWorkout.type == "backStrength" || lastWorkout.type == "headToToe"){
              fitnessActivity = 'CALISTHENICS';
            } else if (lastWorkout.type == "sunSalutation" || lastWorkout.type == "fullSequence" || lastWorkout.type == 'runnerYoga'){
              fitnessActivity = 'YOGA';
            } else if (lastWorkout.type == "customWorkout" || lastWorkout.type == "anythingGoes" || lastWorkout.type == "bootCamp" || lastWorkout.type == "rumpRoaster" || lastWorkout.type == "bringThePain" || lastWorkout.type == "sevenMinute" || lastWorkout.type == "quickFive"){
              fitnessActivity = 'CIRCUIT_TRAINING';
            } else if (lastWorkout.type == "cardio" || lastWorkout.type == "cardioLight" || lastWorkout.type == "plyometrics"){
              fitnessActivity = 'CIRCUIT_TRAINING';
            } else if (lastWorkout.type == "pilatesWorkout"){
              fitnessActivity = 'PILATES';
            }
            window.plugins.GoogleFit.insertSession(
              [lastDate.getTime(), lastWorkout.minutes_completed * 60 * 60000, "Sworkit", fitnessActivity],
              function(){
                console.log('Success Syncing Last Google Fit Workout')
              },
              function(result){console.log('Google Fit Fail ' + result)}
            )
      }}, null );
    });
  }
  $scope.connectHealthKit = function(){
    
    $timeout(function(){
      window.plugins.healthkit.requestAuthorization(
                                                          {
                                                          'readTypes'  : [ 'HKQuantityTypeIdentifierBodyMass'],
                                                          'writeTypes' : ['HKQuantityTypeIdentifierActiveEnergyBurned', 'workoutType']
                                                          },
                                                          function(){
                                                            PersonalData.GetUserSettings.healthKit = true;
                                                            $scope.userSettings.healthKit = true;
                                                            localforage.setItem('userSettings', PersonalData.GetUserSettings);
                                                            $scope.readWeight();
                                                          },
                                                          function(){}
                                                          );
    }, 1000);
  }
  $scope.reconnectHealthKit = function(){
    $scope.healthPopup = $ionicPopup.show({
      title: '',
      subTitle: '',
      scope: $scope,
      template: '<button class="button button-full button-calm" ng-click="hideHealthPopup();healthKitHelp()">'+ $translate.instant('UPDATE_SET') +'</button><button class="button button-full button-assertive" ng-click="hideHealthPopup();disableHealthKit();">'+ $translate.instant('DISABLE_SM') +'</button>',
      buttons: [
        { text: 'Cancel' }
      ]
    });
  }
  $scope.readWeight = function(){
    window.plugins.healthkit.readWeight({
                                                'unit': 'lb'
                                                },
                                                function(msg){
                                                  if (!isNaN(msg)){
                                                    PersonalData.GetUserSettings.weight = msg;
                                                    $scope.convertWeight();
                                                  }
                                                },
                                                function(){}
                                                );
  }
  $scope.hideHealthPopup = function(){
    $scope.healthPopup.close();
  }
  $scope.healthKitHelp = function(){
    $scope.healthModal.show();
  }
  $ionicModal.fromTemplateUrl('healthkit-help.html', function(modal) {
                                $scope.healthModal = modal;
                                }, {
                                scope:$scope,
                                animation: 'slide-in-up',
                                focusFirstInput: false,
                                backdropClickToClose: true,
                                hardwareBackButtonClose: false
                                });
  $scope.closeHealthModal = function(){
    $scope.healthModal.hide();
  }
  $scope.disableHealthKit = function(){
    PersonalData.GetUserSettings.healthKit = false;
    localforage.setItem('userSettings', PersonalData.GetUserSettings);
  }

  $scope.reconnectMFP = function(){
    $scope.mfpPopup = $ionicPopup.show({
      title: 'MyFitnessPal',
      subTitle: '',
      scope: $scope,
      template: '<button class="button button-full button-calm" ng-click="hidePopup();connectMFP();">'+ $translate.instant('RECONNECT') +'</button><button class="button button-full button-assertive" ng-click="hidePopup();disconnectMFP();">'+ $translate.instant('DISCONNECT') +'</button>',
      buttons: [
        { text: $translate.instant('CANCEL_SM') }
      ]
    });
  }
  $scope.hidePopup = function(){
    $scope.mfpPopup.close();
  }
  $scope.disconnectMFP = function(){
            var refresher = PersonalData.GetUserSettings.mfpRefreshToken;
            var newURL = "https://www.myfitnesspal.com/oauth2/revoke?client_id=sworkit&refresh_token=" + refresher;
            $http({
            method: 'POST',
            url: newURL,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
          }).then(function(resp){
            PersonalData.GetUserSettings.mfpAccessToken = false;
            PersonalData.GetUserSettings.mfpRefreshToken = false;
            PersonalData.GetUserSettings.mfpStatus = false;
            PersonalData.GetUserSettings.mfpWeight = false;
            localforage.setItem('userSettings', PersonalData.GetUserSettings);
            showNotification($translate.instant('DISCONNECT_COMP'), 'button-balanced', 2000);
           }, function(err) {
            showNotification($translate.instant('CONN_ERROR'), 'button-assertive', 2000);
      })
  }
  $scope.connectMFP = function(){
    var randomNumber = (new Date().valueOf()).toString() + Math.floor(Math.random()*900);
    var authUrl = 'https://www.myfitnesspal.com/oauth2/authorize?client_id=sworkit&scope=diary&redirect_uri=http://m.sworkit.com/mfp-auth.html&access_type=offline&response_type=code';
    
    cb = window.open(authUrl, '_blank', 'location=no,clearcache=yes,clearsessioncache=yes,AllowInlineMediaPlayback=yes');
    
    cb.addEventListener('loadstart', function(event){$rootScope.interceptFacebook(event.url)});
    
    cb.addEventListener('loadstop', function(event){$rootScope.locationChanged(event.url)});
    
    cb.addEventListener('exit', function(event){$rootScope.childBrowserClosed()});
    
  }

  $rootScope.interceptFacebook = function(url){
      console.log("starting to load: " + url);
      if (url == "http://m.sworkit.com/intercept.html"){
          window.open("https://www.myfitnesspal.com/oauth2/authorize?client_id=sworkit&scope=diary&redirect_uri=http://m.sworkit.com/mfp-auth.html&state=freeapp&access_type=offline&response_type=code", "_system", "location=no,AllowInlineMediaPlayback=yes");
      }
  }

  $rootScope.locationChanged = function(url) {
     cb.executeScript({
                     code: '$("#facebook-login-css").click(function() {window.location = "http://m.sworkit.com/intercept.html"})'
      }, function() {
      });
      myObj = deparam(url);
  }

  $rootScope.childBrowserClosed = function(){
      if (myObj.code){
          console.log('myObj.code is: ' + myObj.code);
          var newURL = "https://www.myfitnesspal.com/oauth2/token?client_id=sworkit&client_secret=192867e0c606f7a7b953&grant_type=authorization_code&code=" + myObj.code;
          $http({
            method: 'POST',
            url: newURL,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
          }).then(function(resp){
            PersonalData.GetUserSettings.mfpAccessToken = resp.data.access_token;
            PersonalData.GetUserSettings.mfpRefreshToken = resp.data.refresh_token;
            PersonalData.GetUserSettings.mfpStatus = true;
            localforage.setItem('userSettings', PersonalData.GetUserSettings);
            showNotification($translate.instant('AUTH_SUCC'), 'button-balanced', 2000);
            trackEvent('More Action', 'MyFitnessPal Connection', 0);
            logActionSessionM('MyFitnessPal');
           }, function(err) {
            $rootScope.childBrowserRetry();
      })
      }
      else {
        var helpMessage = $translate.instant('TAP_HELP') + ' contact@sworkit.com.'
          navigator.notification.confirm(
                                   helpMessage,
                                   $scope.getHelp,
                                   $translate.instant('CONNECT_ERROR'),
                                   [$translate.instant('CANCEL_SM'),$translate.instant('HELP')]
                                   );
      }
      
  }
  $rootScope.childBrowserRetry = function(){
      if (myObj.code){
          myObj.code = myObj.code + '%3d%3d';
          var newURL = "https://www.myfitnesspal.com/oauth2/token?client_id=sworkit&client_secret=192867e0c606f7a7b953&grant_type=authorization_code&code=" + myObj.code;
          $http({
            method: 'POST',
            url: newURL,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
          }).then(function(resp){
            PersonalData.GetUserSettings.mfpAccessToken = resp.data.access_token;
            PersonalData.GetUserSettings.mfpRefreshToken = resp.data.refresh_token;
            PersonalData.GetUserSettings.mfpStatus = true;
            localforage.setItem('userSettings', PersonalData.GetUserSettings);
            showNotification($translate.instant('AUTH_SUCC'), 'button-balanced', 2000);
            trackEvent('More Action', 'MyFitnessPal Connection', 0);
            logActionSessionM('MyFitnessPal');
           }, function(err) {
            showNotification($translate.instant('CON_ERROR'), 'button-assertive', 2000);
      })
      }
      else {
          var helpMessage = $translate.instant('TAP_HELP') + ' contact@sworkit.com.'
          navigator.notification.confirm(
                                   helpMessage,
                                   $scope.getHelp,
                                   $translate.instant('CONNECT_ERROR'),
                                   [$translate.instant('CANCEL_SM'),$translate.instant('HELP')]
                                   );
      }
      
  }
  $scope.getHelp = function(buttonIndex){
    if (buttonIndex == 2){
      window.open('http://sworkit.com/mfp', 'blank', 'location=no,AllowInlineMediaPlayback=yes');
    }
  }
  $scope.rateSworkit = function ($event) {
    $timeout(function(){
       upgradeNotice(2);
      }, 500);
  }
  $scope.downloadVideosChange = function(){
    if ($scope.userSettings.videosDownloaded){
      navigator.notification.confirm(
         'Ready to download the videos for autoplay in workouts? Total size: 40MB',
         $scope.downloadQuestion,
         'Download Videos',
         ['Yes','Cancel']
      );
    } else{
      navigator.notification.confirm(
         'Are you sure you want to delete the videos and disable autoplay?',
         $scope.deleteQuestion,
         'Delete Videos',
         ['Yes','Cancel']
      );
    }
  }

  $scope.downloadVideosChange = function(){
    if ($scope.userSettings.videosDownloaded){
      navigator.notification.confirm(
         $translate.instant('READY_DOWN'),
         $scope.downloadQuestion,
         $translate.instant('DOWNLOAD_VID'),
         [$translate.instant('YES_SM'),$translate.instant('CANCEL_SM')]
      );
    } else{
      navigator.notification.confirm(
         $translate.instant('DELETE_SURE'),
         $scope.deleteQuestion,
         $translate.instant('DELETE_VID'),
         [$translate.instant('YES_SM'),$translate.instant('CANCEL_SM')]
      );
    }
  }

  $scope.downloadQuestion = function(buttonIndex){
    if (buttonIndex == 1){
      downloadProgress($translate.use());
      downloadAllExercise();
      PersonalData.GetUserSettings.downloadDecision = false;
      localforage.setItem('userSettings', PersonalData.GetUserSettings);
    } else if(buttonIndex == 2){
      $scope.userSettings.videosDownloaded = false;
      $scope.$apply();
    }
  }

  $scope.deleteQuestion = function(buttonIndex){
    if (buttonIndex == 1){
      videoDownloader.deleteVideos()
      PersonalData.GetUserSettings.downloadDecision = false;
      PersonalData.GetUserSettings.videosDownloaded = false;
      localforage.setItem('userSettings', PersonalData.GetUserSettings);
    } else if(buttonIndex == 2){
      $scope.userSettings.videosDownloaded = true;
      $scope.$apply();
    }
  }
  if (device){LowLatencyAudio.unload('welcome');}
  $scope.$on('$ionicView.leave', function() {
               if ($scope.mfpWeightStatus.data){
                PersonalData.GetUserSettings.mfpWeight = true;
               }
               localforage.setItem('userSettings', PersonalData.GetUserSettings);
               localforage.setItem('userGoals', PersonalData.GetUserGoals);
               localforage.setItem('timingSettings', TimingData.GetTimingSettings);
               if (PersonalData.GetUserSettings.preferredLanguage !== $scope.originalLanguage && device){
                downloadAllExerciseAudio(PersonalData.GetUserSettings.preferredLanguage);
                getSworkitAds($http, true);
               }
               if (device){
                 setWelcomeAudio();
                 if (ionic.Platform.isAndroid()){
                  localforage.setItem('googleFitStatus', PersonalData.GetGoogleFit);
                 }
               }
               $scope.healthModal.remove();
               });
})

.controller('SettingsAudioCtrl', function($scope, $translate, UserService) {
      $scope.timeSettings = UserService.getTimingIntervals();
      $scope.audioSettings = UserService.getAudioSettings();
      $scope.data = {showInfo:false};
      if (ionic.Platform.isAndroid()){
        $scope.androidPlatform = true;
      } else{
        $scope.androidPlatform = false;
      }
      $scope.changeAudio = function(value){
        switch(value) {
          case 0:
              $scope.audioSettings.ignoreDuck = false;
              $scope.audioSettings.duckEverything = false;
              $scope.audioSettings.duckOnce = true;
              break;
          case 1:
              $scope.audioSettings.duckOnce = false;
              $scope.audioSettings.duckEverything = false;
              $scope.audioSettings.ignoreDuck = true;
              break;
          default:
              $scope.audioSettings.duckOnce = false;
              $scope.audioSettings.ignoreDuck = false;
              $scope.audioSettings.duckEverything = true;
        }
      }
      $scope.$on('$ionicView.leave', function(){
        localforage.setItem('backgroundAudio', PersonalData.GetAudioSettings);
        LowLatencyAudio.turnOffAudioDuck(PersonalData.GetAudioSettings.duckOnce.toString());
      });
})

.controller('ExerciseListCtrl', function($scope, $ionicModal, $http, $sce, $timeout,$translate, $location, $ionicScrollDelegate, WorkoutService) {
  $scope.exerciseCategories = [
    {shortName:"upper",longName:"UPPER_SM", exercises: WorkoutService.getExercisesByCategory('upper') },
    {shortName:"core",longName:"CORE_SM", exercises: WorkoutService.getExercisesByCategory('core') },
    {shortName:"lower",longName:"LOWER_SM", exercises: WorkoutService.getExercisesByCategory('lower') },
    {shortName:"stretch",longName:"STRETCH_SM", exercises: WorkoutService.getExercisesByCategory('stretch') },
    {shortName:"back",longName:"BACK_SM", exercises: WorkoutService.getExercisesByCategory('back') },
    {shortName:"cardio",longName:"CARDIO_SM", exercises: WorkoutService.getExercisesByCategory('cardio') },
    {shortName:"pilates",longName:"PILATES_SM", exercises: WorkoutService.getExercisesByCategory('pilates') },
    {shortName:"yoga",longName:"YOGA_SM", exercises: WorkoutService.getExercisesByCategory('yoga') }
  ];
  if (ionic.Platform.isAndroid()){
    $scope.androidPlatform = true;
  } else{
    $scope.androidPlatform = false;
  }
  $scope.showVideo = false;
  $scope.advancedTiming = WorkoutService.getTimingIntervals();
  if(device){
    cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);
  }
  $scope.currentExercise = exerciseObject['Squats'];
  $ionicModal.fromTemplateUrl('show-video.html', function(modal) {
                              $scope.videoModal = modal;
                              }, {
                              scope:$scope,
                              animation: 'fade-implode',
                              focusFirstInput: false,
                              backdropClickToClose: false
                              });
  $scope.openVideoModal = function(exerciseEl) {
    $scope.currentExercise = exerciseEl;
    $scope.videoModal.show();
      if ($scope.androidPlatform && device){
      if (TimingData.GetTimingSettings.autoPlay){
        window.plugins.html5Video.initialize({
          "videoplayerscreen" : $scope.currentExercise.video
        })
        $timeout(function(){
          window.plugins.html5Video.play("videoplayerscreen", function(){})
        }, 1400);
        $timeout(function(){
          angular.element(document.getElementById('videoplayerscreen')).css('opacity','1');
        }, 1500);
        $timeout(function(){
          angular.element(document.getElementById('videoplayerscreen')).css('opacity','0.00001');
        }, 0);
      } else{$timeout(function(){
          var videoPlayerFrame = angular.element(document.getElementById('videoplayerscreen'));
          videoPlayerFrame.css('opacity','0.00001');
          videoPlayerFrame[0].src = 'http://m.sworkit.com/assets/exercises/Videos/' + $scope.currentExercise.video;

          videoPlayerFrame[0].addEventListener("timeupdate", function() {
            if (videoPlayerFrame[0].duration > 0 
              && Math.round(videoPlayerFrame[0].duration) - Math.round(videoPlayerFrame[0].currentTime) == 0) {
              
              //if loop atribute is set, restart video
                if (videoPlayerFrame[0].loop) {
                    videoPlayerFrame[0].currentTime = 0;
                }
            }
          }, false);
          
          videoPlayerFrame[0].addEventListener("canplay", function(){
            videoPlayerFrame[0].removeEventListener("canplay", this, false);
            videoPlayerFrame[0].play();
            videoPlayerFrame.css('opacity','1');
          }, false);
          
          videoPlayerFrame[0].play();
        }, 100);
      }

    } else {
      $scope.videoAddress = 'video/' + $scope.currentExercise.video;
    }
    var calcHeight = (angular.element(document.getElementsByClassName('modal')).prop('offsetHeight'))   * .7;
    calcHeight = calcHeight +'px';
    // if (ionic.Platform.isAndroid() && !isKindle()){
    //   angular.element(document.querySelector('#videoplayer')).html("<video id='video2' poster='img/exercises/"+$scope.currentExercise.image+"' preload='auto' autoplay loop muted webkit-playsinline='webkit-playsinline' ><source src='"+$scope.videoData.videoURL+"'></source></video>");
    // }
    $scope.showVideo = true;

  };
  $scope.cancelVideoModal = function() {
    // if (ionic.Platform.isAndroid() && !isKindle()){
    //   angular.element(document.querySelector('#videoplayer')).html("");
    // }
    $scope.videoData = {youtubeURL: '',videoURL: ''};
    var videoPlayerFrame = angular.element(document.getElementById('videoplayerscreen'));
    if ($scope.androidPlatform){
      videoPlayerFrame[0].src = '';
    }
    $scope.videoModal.hide();
  };
  
  $scope.searchTyping = function(typedthings){

  }
  $scope.searchSelect = function(suggestion){
    $scope.slideTo($scope.allExercises.indexOf(suggestion), suggestion);
  }
  $scope.slideTo = function(location, suggestion) {
    var newLocation = $location.hash(location);
    document.getElementById('exercise-search').value = '';
    var keyObject = translations[PersonalData.GetUserSettings.preferredLanguage];
    keyObject.getKeyByValue = function( value ) {
      for( var prop in this ) {
        if( this.hasOwnProperty( prop ) ) {
          if( this[ prop ] === value )
          return prop;
        }
      }
    }
    var foundKey = keyObject.getKeyByValue(suggestion);
    var keyInEN = translations['EN'][foundKey];
    $ionicScrollDelegate.$getByHandle('exerciseScroll').anchorScroll("#"+newLocation);
    $timeout( function(){
      $scope.openVideoModal(exerciseObject[keyInEN]);
    },500);
  };

  $scope.allExercises = [];
  for(var eachExercise in exerciseObject) {
    $scope.allExercises.push($translate.instant(exerciseObject[eachExercise].name));
  }
  $timeout(function(){
    $scope.allExercises.sort();
  }, 1500)

  $scope.$on('$ionicView.leave', function() {
    $scope.videoModal.remove();
    if(device){
      cordova.plugins.Keyboard.hideKeyboardAccessoryBar(false);
    }    
  });
})

.controller('HelpCtrl', function($scope, $translate, UserService) {
  $scope.$on('$ionicView.enter', function () {
    angular.element(document.getElementsByClassName('bar-header')).addClass('blue-bar');
  });
  $scope.sendFeedback = function ($event) {
      if (ionic.Platform.isAndroid()){
        $scope.appVersion = '5.60.05'
      } else {
        $scope.appVersion = '3.5.5'
      }
      var emailBody = "<p>" + $translate.instant('DEVICE') + ": " + device.model + "</p><p>" + $translate.instant('PLATFORM') + ": "  + device.platform + " " + device.version  + "- " + PersonalData.GetUserSettings.preferredLanguage + "</p>" + "<p>" + $translate.instant('APP_VERSION') + ": " + $scope.appVersion + "</p><p>" + $translate.instant('FEEDBACK') + ": </p>";
      window.plugin.email.open({
                       to:      ['contact@sworkit.com'],
                       subject: $translate.instant('FEEDBACK') + ': Sworkit Lite',
                       body:    emailBody,
                       isHtml:  true
                       });
  };
  $scope.openInstructions = function ($event){
    window.open('http://sworkit.com/about#instructions', '_blank', 'location=yes,AllowInlineMediaPlayback=yes,toolbarposition=top');
  }
  $scope.openFAQ = function ($event){
    window.open('http://sworkit.com/about#faq', '_blank', 'location=yes,AllowInlineMediaPlayback=yes,toolbarposition=top');
  }
  $scope.openTOS = function ($event){
    window.open('http://m.sworkit.com/TOS.html', '_blank', 'location=yes,AllowInlineMediaPlayback=yes,toolbarposition=top');
  }
  $scope.openPrivacy = function ($event){
    window.open('http://m.sworkit.com/privacy.html', '_blank', 'location=yes,AllowInlineMediaPlayback=yes,toolbarposition=top');
  }
  $scope.openRules = function ($event){
    window.open('http://m.sworkit.com/rules.html', '_blank', 'location=yes,AllowInlineMediaPlayback=yes,toolbarposition=top');
  }
  $scope.shareTwitter = function ($event) {
    if (device){
      var tweetText = $translate.instant('DOWNLOAD') + ' @Sworkit ' + $translate.instant('FOR_CUSTOM') + '.';
      window.plugins.socialsharing.shareViaTwitter(tweetText, null, 'http://sworkit.com', function(){logActionSessionM('Share')}, function(){window.open('http://twitter.com/sworkit', '_blank', 'location=yes,AllowInlineMediaPlayback=yes,toolbarposition=top');})
    }
  }
  $scope.shareFacebook = function ($event) {
    var facebookText = $translate.instant('DOWNLOAD') + ' #Sworkit ' + $translate.instant('FOR_CUSTOM') + ' at http://sworkit.com';
    if (device){
      window.plugins.socialsharing.shareViaFacebook(facebookText, null, null, function(){logActionSessionM('Share')}, function(){window.open('http://facebook.com/sworkitapps', '_blank', 'location=yes,AllowInlineMediaPlayback=yes,toolbarposition=top');})
    }
  }
  $scope.shareEmail = function ($event) {
      var emailText = $translate.instant('DOWNLOAD') + ' Sworkit ' + $translate.instant('FOR_CUSTOM') + ' at http://sworkit.com';
      window.plugin.email.open({
                       to:      [],
                       subject: $translate.instant('CHECK_OUT'),
                       body:    emailText,
                       isHtml:  true
                       });
  }
  $scope.rateSworkit = function ($event) {
    if (device.platform.toLowerCase() == 'ios') {
      window.open('http://itunes.apple.com/app/id527219710', '_system', 'location=no,AllowInlineMediaPlayback=yes');
    } else if (isAmazon()){
        window.appAvailability.check('com.amazon.venezia', function() {
             window.open('amzn://apps/android?p=sworkitapp.sworkit.com', '_system')},function(){
             window.open(encodeURI("http://www.amazon.com/gp/mas/dl/android?p=sworkitapp.sworkit.com"), '_system');}
             );
    }  else {
      window.open('market://details?id=sworkitapp.sworkit.com', '_system');
    }
  }
  $scope.launchGear = function(){
    window.open('http://www.ntensify.com/sworkit', '_blank', 'location=yes,AllowInlineMediaPlayback=yes,toolbarposition=top');
  }
  var hiddenURL = window.open('http://sworkit.com/app', '_blank', 'hidden=yes,AllowInlineMediaPlayback=yes');
  
  $scope.$on('$ionicView.leave', function() {
               angular.element(document.getElementsByClassName('bar-header')).removeClass('blue-bar');
               });
})

.controller('PartnerAppsCtrl', function($scope, $location, UserService) {
  $scope.learnNexercise = function (){
    if (device.platform.toLowerCase() == 'ios') {
      window.open('http://nxr.cz/nex-ios', '_system', 'location=no,AllowInlineMediaPlayback=yes');
    }  else if (isAmazon()){
        window.appAvailability.check('com.amazon.venezia', function() {
             window.open('amzn://apps/android?p=com.nexercise.client.android', '_system')},function(){
             window.open(encodeURI("http://www.amazon.com/gp/mas/dl/android?p=com.nexercise.client.android"), '_system');}
             );
    }  else {
      window.open('market://details?id=com.nexercise.client.android', '_system')
    }
  }
  $scope.learnMyFitnessPal = function (){
    $location.path('/app/settings');
  }
})

.controller('SworkitProAppCtrl', function($scope, $rootScope, $ionicHistory, $ionicSideMenuDelegate, $ionicNavBarDelegate, $location, UserService) {
  $ionicNavBarDelegate.showBackButton(false);
  $scope.$on('$ionicView.enter', function () {
    angular.element(document.getElementsByClassName('bar-header')).addClass('blue-bar');
  });
  $scope.downloadSworkitPro = function (){
    trackEvent('More Action', 'Install Sworkit Pro', 0);
    setTimeout(function(){
      if (device.platform.toLowerCase() == 'ios') {
        window.open('http://nxr.cz/sk-pro-ios', '_system', 'location=no,AllowInlineMediaPlayback=yes');
      }  else if (isAmazon()){
        window.appAvailability.check('com.amazon.venezia', function() {
             window.open('amzn://apps/android?p=sworkitproapp.sworkit.com', '_system')},function(){
             window.open(encodeURI("http://www.amazon.com/gp/mas/dl/android?p=sworkitproapp.sworkit.com"), '_system');}
             );
      } else {
      window.open('market://details?id=sworkitproapp.sworkit.com', '_system')
      }
    }, 400)
  }
  $scope.goBack = function(){
    if ($ionicHistory.backView() !== null){
      $location.path($ionicHistory.backView().url);
      $ionicSideMenuDelegate.toggleLeft(false);
    } else {
      $location.path('/app/home');
      $ionicSideMenuDelegate.toggleLeft(false);
    }
    
  }
  $scope.$on('$ionicView.leave', function() {
               $ionicNavBarDelegate.showBackButton(true);
               angular.element(document.getElementsByClassName('bar-header')).removeClass('blue-bar');
               });
})

.controller('NexerciseAppCtrl', function($scope, $location, UserService) {
  $scope.downloadNexercise = function (){
    trackEvent('More Action', 'Install Nexercise', 0);
    setTimeout(function(){
      if (device.platform.toLowerCase() == 'ios') {
        window.open('http://nxr.cz/nex-ios', '_system', 'location=no,AllowInlineMediaPlayback=yes');
      }  else if (isAmazon()){
        window.appAvailability.check('com.amazon.venezia', function() {
             window.open('amzn://apps/android?p=com.nexercise.client.android', '_system')},function(){
             window.open(encodeURI("http://www.amazon.com/gp/mas/dl/android?p=com.nexercise.client.android"), '_system');}
             );
      } else {
      window.open('market://details?id=com.nexercise.client.android', '_system')
      }
    }, 400)
  }
})

.directive('integer', function(){
    return {
        require: 'ngModel',
        link: function(scope, ele, attr, ctrl){
            ctrl.$parsers.unshift(function(viewValue){
                return parseInt(viewValue);
            });
        }
    };
});

function showTimingModal($scope, $ionicModal, $timeout, WorkoutService, parent){
  $scope.toggleOptions = {data:true}
  if (ionic.Platform.isAndroid()){
    $scope.androidPlatform = true;
  } else{
    $scope.androidPlatform = false;
  }
  if (parent){
    $scope.toggleOptions = {data:false};
  }
  var tempExerciseTime = $scope.advancedTiming.exerciseTime;
    $ionicModal.fromTemplateUrl('advanced-timing.html', function(modal) {
                                $scope.timeModal = modal;
                                }, {
                                scope:$scope,
                                animation: 'slide-in-up',
                                focusFirstInput: false,
                                backdropClickToClose: false
                                });
    $scope.openModal = function() {
      $scope.timeModal.show();
    };
    $scope.closeModal = function() {
      TimingData.GetTimingSettings.breakFreq = parseInt(TimingData.GetTimingSettings.breakFreq);
      TimingData.GetTimingSettings.exerciseTime = parseInt(TimingData.GetTimingSettings.exerciseTime);
      TimingData.GetTimingSettings.breakTime = parseInt(TimingData.GetTimingSettings.breakTime);
      TimingData.GetTimingSettings.transitionTime = parseInt(TimingData.GetTimingSettings.transitionTime);
      if ($scope.extraSettings.transition){
        $scope.advancedTiming.transitionTime = 5;
        TimingData.GetTimingSettings.transitionTime = 5;
      } else {
        $scope.advancedTiming.transitionTime = 0;
        TimingData.GetTimingSettings.transitionTime = 0;
      }
      
      if (parent && tempExerciseTime !== $scope.advancedTiming.exerciseTime){
        var singleSeconds = $scope.advancedTiming.exerciseTime;
        if (singleSeconds > 60){
          $scope.singleTimer.minutes = Math.floor(singleSeconds / 60);
          $scope.singleTimer.seconds = singleSeconds % 60;
        } else {
          $scope.singleTimer.minutes = 0;
          $scope.singleTimer.seconds = singleSeconds;
        }
        $scope.updateTime();
        $scope.adjustTimerMinutes();
      } else if (parent){
        if (ionic.Platform.isAndroid() && device){
          playInlineVideo($scope.advancedTiming.autoPlay);
        } else{
          playInlineVideo($scope.advancedTiming.autoPlay, exerciseObject[$scope.currentWorkout[0]]);
        }
        localforage.setItem('timingSettings', TimingData.GetTimingSettings);
      } else {
        localforage.setItem('timingSettings', TimingData.GetTimingSettings);
      }
      $scope.timeModal.hide();
      $scope.timeModal.remove();    };
    $scope.resetDefaults =  function(){
      var getAudio = TimingData.GetTimingSettings.audioOption;
      var getWarning = TimingData.GetTimingSettings.warningAudio;
      var getCountdown = TimingData.GetTimingSettings.countdownBeep;
      var getStyle = TimingData.GetTimingSettings.countdownStyle;
      var getAutoPlay = TimingData.GetTimingSettings.autoPlay;
      var getWelcome = TimingData.GetTimingSettings.welcomeAudio;
      var getAuto = TimingData.GetTimingSettings.autoStart;
      $scope.advancedTiming = {"customSet":false,"breakFreq":5,"exerciseTime":30,"breakTime":30,"transitionTime":0,"transition":false,"randomizationOption":true,"workoutLength":60, "audioOption": getAudio, "warningAudio": getWarning, "countdownBeep": getCountdown, "autoPlay": getAutoPlay, "welcomeAudio": getWelcome, "autoStart": getAuto} 
      TimingData.GetTimingSettings = $scope.advancedTiming;
    }
    $scope.$on('$ionicView.leave', function() {
               $scope.timeModal.remove();
               });
    $timeout(function(){
             $scope.openModal();
             }, 0);
}

function buildStats($scope, $translate){
  $scope.getTotal = function(){
            window.db.transaction(
                           function(transaction) {
                           transaction.executeSql("SELECT SUM(minutes_completed) AS minutes FROM SworkitFree",
                                                  [],
                                                  function(tx, results){
                                                    $scope.totals.totalEver = results.rows.item(0)["minutes"] || 0;
                                                    $scope.$apply();
                                                  },
                                                  null)
                           }
                           );
            window.db.transaction(
                           function(transaction) {
                           transaction.executeSql("SELECT strftime('%Y-%m-%d', created_on) AS day, SUM(minutes_completed) AS minutes, SUM(calories) AS calories FROM SworkitFree WHERE created_on > (SELECT DATETIME('now', '-1 day')) GROUP BY strftime('%Y-%m-%d', created_on)",
                                                  [],
                                                  function(tx, results){
                                                    try{
                                                      if (results.rows.item(0)){
                                                       $scope.totals.todayMinutes = results.rows.item(results.rows.length -1)["minutes"];
                                                       $scope.totals.todayCalories = results.rows.item(results.rows.length -1)["calories"];
                                                      }
                                                    } catch(e){
                                                      $scope.totals.todayMinutes = 0;
                                                      $scope.totals.todayCalories = 0;
                                                    }
                                                  },
                                                  null)
                           }
                           );
            window.db.transaction(
                         function(transaction) {
                         transaction.executeSql("SELECT strftime('%Y-%m-%d', created_on) AS day, SUM(minutes_completed) AS minutes, SUM(calories) AS calories FROM SworkitFree WHERE created_on > (SELECT DATETIME('now', '-7 day')) GROUP BY strftime('%Y-%m-%d', created_on)",
                                                [],
                                                function(tx, results){
                                                    dateHashMin = {}
                                                    dateHashCal = {}
                                                    for (var i = 0; i < results.rows.length; i++) { dateHashMin[results.rows.item(i)["day"]] = results.rows.item(i)["minutes"];
                                                    dateHashCal[results.rows.item(i)["day"]] = results.rows.item(i)["calories"]; }
                                                    
                                                    $scope.graphData7Min = [];
                                                    $scope.graphData7Cal = [];
                                                    for (var i = 0; i < 7; i++) {
                                                        date = new Date();
                                                        date.setTime(date.getTime() - (i * 24 * 60 * 60 * 1000));
                                                        
                                                        day = (date.getDate() < 10) ? "0" + date.getDate() : date.getDate().toString();
                                                        month = (date.getMonth() < 9) ? "0" + (date.getMonth() + 1) : (date.getMonth() + 1).toString();
                                                        createdOnFormat = date.getFullYear() + "-" + month + "-" + day;
                                                        
                                                        minutes = dateHashMin[createdOnFormat] || 0;
                                                        calories = dateHashCal[createdOnFormat] || 0;
                                                        
                                                        
                                                        displayDate = (i == 0) ? "today" : (date.getMonth() + 1) + "." + date.getDate();
                                                        
                                                        $scope.graphData7Min.unshift([displayDate, minutes]);
                                                        $scope.graphData7Cal.unshift([displayDate, calories]);
                                                    }
                                                  },
                                                null)
                         }
                         );
            window.db.transaction(
                                     function(transaction) {
                                     transaction.executeSql("SELECT strftime('%Y-%m-%d', created_on) AS day, SUM(minutes_completed) AS minutes, SUM(calories) AS calories FROM SworkitFree WHERE created_on > (SELECT DATETIME('now', '-30 day')) GROUP BY strftime('%Y-%m-%d', created_on)",
                                                            [],
                                                            function(tx, results){
                                                                var totalMonthMinutes = 0;
                                                                dateHashMin30 = {}
                                                                dateHashCal30 = {}
                                                                for (var i = 0; i < results.rows.length; i++) { dateHashMin30[results.rows.item(i)["day"]] = results.rows.item(i)["minutes"];
                                                                dateHashCal30[results.rows.item(i)["day"]] = results.rows.item(i)["calories"]; }
                                                                
                                                                $scope.graphData30Min = [];
                                                                $scope.graphData30Cal = [];
                                                                for (var i = 0; i < 30; i++) {
                                                                    date = new Date();
                                                                    date.setTime(date.getTime() - (i * 24 * 60 * 60 * 1000));
                                                                    
                                                                    day = (date.getDate() < 10) ? "0" + date.getDate() : date.getDate().toString();
                                                                    month = (date.getMonth() < 9) ? "0" + (date.getMonth() + 1) : (date.getMonth() + 1).toString();
                                                                    createdOnFormat = date.getFullYear() + "-" + month + "-" + day;
                                                                    
                                                                    minutes = dateHashMin30[createdOnFormat] || 0;
                                                                    calories = dateHashCal30[createdOnFormat] || 0;
                                                                    
                                                                    
                                                                    displayDate = (i == 0) ? "today" : (date.getMonth() + 1) + "." + date.getDate();
                                                                    if (minutes > 0){
                                                                      totalMonthMinutes++
                                                                    }
                                                                     
                                                                    $scope.graphData30Min.unshift([displayDate, minutes]);
                                                                    $scope.graphData30Cal.unshift([displayDate, calories]);
                                                                }
                                                                $scope.totals.totalMonthMin = totalMonthMinutes;
                                                              },
                                                            null)
                                     }
                                     );

            window.db.transaction(
                           function(transaction) {
                           transaction.executeSql("SELECT strftime('%Y-%m-%d', created_on) AS day, SUM(minutes_completed) AS minutes, SUM(calories) AS calories FROM SworkitFree GROUP BY strftime('%Y-%m-%d', created_on) ORDER BY minutes DESC LIMIT 1",
                                                  [],
                                                  function(tx, results){
                                                    try{
                                                      if (results.rows.item(0)){
                                                       $scope.totals.topMinutes = results.rows.item(results.rows.length -1)["minutes"];
                                                       $scope.totals.topDayMins = results.rows.item(results.rows.length -1)["day"];
                                                      }
                                                    } catch(e){
                                                      $scope.totals.topMinutes = 0;
                                                      $scope.totals.topDayMins = '';
                                                    }
                                                  },
                                                  null)
                           }
                           );
            window.db.transaction(
                           function(transaction) {
                           transaction.executeSql("SELECT strftime('%Y-%m-%d', created_on) AS day, SUM(minutes_completed) AS minutes, SUM(calories) AS calories FROM SworkitFree GROUP BY strftime('%Y-%m-%d', created_on) ORDER BY calories DESC LIMIT 1",
                                                  [],
                                                  function(tx, results){
                                                    try{
                                                      if (results.rows.item(0)){
                                                       $scope.totals.topCalories = results.rows.item(results.rows.length -1)["calories"];
                                                       $scope.totals.topDayCals = results.rows.item(results.rows.length -1)["day"];
                                                      }
                                                    } catch(e){
                                                      $scope.totals.topCalories = 0;
                                                      $scope.totals.topDayCals = '';
                                                    }
                                                    $scope.$apply();
                                                  },
                                                  null)
                           }
                           );
        }
  $scope.getTotal();
  $scope.weeklyMinutes = parseInt(window.localStorage.getItem('weeklyTotal'));
  $scope.drawGraph = function(){
    $scope.dailyData = [
      {
          "key": "Series1",
          "color": "#FF8614",
          "values": [
              ["You" , $scope.totals.todayMinutes ],
              ["Goal" , $scope.goalSettings.dailyGoal ]
          ]
      }
    ];
    $scope.weeklyData = [
        {
            "key": "Series2",
            "color": "#FF8614",
            "values": [
                ["You" , $scope.weeklyMinutes ],
                ["Goal" , $scope.goalSettings.weeklyGoal ]
            ]
        }
    ];
    $scope.weeklyCals = [
                    {
                        "key": "Series 1",
                        "color": "#FF8614",
                        "values": $scope.graphData7Cal
                    }
              ];
    $scope.weeklyMins = [
                  {
                      "key": "Series 1",
                      "color": "#FF8614",
                      "values": $scope.graphData7Min
                  }
            ];
    
    $scope.monthlyCals = [
                  {
                      "key": "Series 1",
                      "color": "#FF8614",
                      "values": $scope.graphData30Cal
                  }
            ];
    
    $scope.monthlyMins = [
                  {
                      "key": "Series 1",
                      "color": "#FF8614",
                      "values": $scope.graphData30Min
                  }
            ];
    }

    $scope.xFunction = function(){
        return function(d) {
            return d.key;
        };
    }
    $scope.yFunction = function(){
        return function(d) {
            return d.y;
        };
    }

    $scope.descriptionFunction = function(){
        return function(d){
            return d.key;
        }
    }
}

function installWorkout(workoutName, workoutList, loc, sidemenu){
  if (device){
    navigator.notification.confirm(
                                   'This will replace your current custom workout. With Sworkit Pro you can save multiple custom workouts.',
                                   function(button){
                                    if (button == 2){
                                      PersonalData.GetCustomWorkouts.savedWorkouts[0] ={"name": workoutName,"workout": workoutList};
                                      localforage.setItem('customWorkouts', PersonalData.GetCustomWorkouts);
                                      trackEvent('URL Scheme', workoutName, 0);
                                      showNotification('Custom Workout Added', 'button-balanced', 2000);
                                      var tempLocation = loc.$$url.slice(-7) || '';
                                      if (tempLocation !== "workout"){
                                        loc.path('/app/custom');
                                        sidemenu.toggleLeft(false);
                                      }
                                      logActionSessionM('ImportCustomWorkout');
                                    }},
                                   'Import Custom Workout?',
                                   ['Cancel','OK']
                                   );
  }
}
function showNotification(message, style, duration){
  var notifyEl = angular.element(document.getElementById('status-notification'));
  notifyEl.html('<button class="button button-full button-outline notify-style '+style+' fade-in-custom">'+message+'</button>');
  setTimeout(function(){
    notifyEl.html('<button class="button button-full button-outline '+style+' fade-out-custom">'+message+'</button>');
    notifyEl.html('');
  }, duration)
}
function getMinutesObj(){
    var minuteObj = {selected: 0, times:[]};
    for (i=0;i<60;i++){
        var stringNum = (i < 10) ? '0' + i : i;
        minuteObj.times.push({'id':i,'time':stringNum});
    }
    return minuteObj;
}
function forceUpdate(){
  if (device){
    navigator.notification.confirm(
                                   'Please update Sworkit',
                                   upgradeNotice,
                                   'Not Available',
                                   ['Cancel','Upgrade']
                                   );
  } else{
    alert('Force Update');
  }
}
function upgradeNotice(button){
  if (button == 2){
    if (device.platform.toLowerCase() == 'ios') {
      window.open('http://itunes.apple.com/app/id527219710', '_system', 'location=no,AllowInlineMediaPlayback=yes');
    } else if (isAmazon()){
        window.appAvailability.check('com.amazon.venezia', function() {
             window.open('amzn://apps/android?p=sworkitapp.sworkit.com', '_system')},function(){
             window.open(encodeURI("http://www.amazon.com/gp/mas/dl/android?p=sworkitapp.sworkit.com"), '_system');}
             );
    } else {
      window.open('market://details?id=sworkitapp.sworkit.com', '_system');
    }
  }
}
function checkVolume(){
  var volumeNotification = angular.element(document.getElementsByClassName('volume-notification'));
  if (device){
    window.plugin.volume.getVolume(function(volume) {
      if (volume < 0.05){
        volumeNotification.addClass('animate').removeClass('flash');
        if (!ionic.Platform.isAndroid()){
          window.plugin.volume.setVolumeChangeCallback(function() {
            volumeNotification.addClass('flash').removeClass('animate');
          })
        }
        setTimeout(function(){
          volumeNotification.addClass('flash').removeClass('animate');
        }, 4000);
      }
    });
  } else {
    volumeNotification.addClass('animate').removeClass('flash');
    setTimeout(function(){
          volumeNotification.addClass('flash').removeClass('animate');
    }, 4000);
  }
}

var inlineVideoTimeout;
function playInlineVideo(autoState, exerciseObj){
  if (autoState && ionic.Platform.isAndroid() && device){
    window.plugins.html5Video.play("inlinevideo", function(){
    })
  }
  else if(autoState){
    var videoElement = angular.element(document.getElementById('inline-video'))[0];
    videoElement.play();
    videoElement.muted = true;
    if (autoState && exerciseObj.videoTiming[0]){
      inlineVideoTimeout = setTimeout(function(){
        videoElement.pause();
      }, exerciseObj.videoTiming[0] + 1000);
    }
  }
}

function continueInlineVideo(autoState, exerciseObj){
  clearTimeout(inlineVideoTimeout);
  if (autoState && ionic.Platform.isAndroid() && device){
    
  }
  else if(autoState){
    var videoElement = angular.element(document.getElementById('inline-video'))[0];
    videoElement.play();
    videoElement.muted = true;
    if (autoState && exerciseObj.videoTiming[1]){
      inlineVideoTimeout = setTimeout(function(){
        videoElement.pause();
      }, exerciseObj.videoTiming[1] + 2000 - exerciseObj.videoTiming[0]);
    }
  }
}

function setBackButton($scope,$location,$ionicPlatform, customLocation){
  $scope.customBack = $ionicPlatform.registerBackButtonAction(
          function () {
              if (customLocation){
                var customString = '/app/' + customLocation;
                $location.path(customString);
              } else{
                $location.path('/app/home');
              }
              
          }, 100
  );
  $scope.$on('$ionicView.leave', $scope.customBack);
}
