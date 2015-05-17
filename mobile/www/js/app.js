document.addEventListener('deviceready', function onDeviceReady() {
  angular.bootstrap(document, ['app']);
}, false);

app = angular.module('app', ['ionic','ionic.contrib.drawer','ngCordova'])

app.config(function ($stateProvider,$urlRouterProvider,$ionicConfigProvider) {
  $stateProvider
    .state('tutorial', {
      cache: false,
      url: '/',
      templateUrl: 'views/tutorial.html',
      controller: 'tutorialCtrl'
    })
    .state('login', {
      cache: false,
      url: '/login',
      templateUrl: 'views/login.html',
      controller: 'loginCtrl'
    })
    .state('layout', {
      cache: false,
      url: '/content',
      templateUrl: 'views/layout.html',
      controller: 'layoutCtrl'
    })
    .state('layout.level', {
      cache: false,
      url: '/level',
      views: {
        'content' :{
          templateUrl: 'views/level.html',
          controller: 'levelCtrl'
        }
      }
    })
    .state('layout.lineup', {
      cache: false,
      url: '/lineup',
      views: {
        'content' :{
          templateUrl: 'views/lineup.html',
          controller: 'lineupCtrl',
        }
      }
    })
    .state('layout.infofest', {
      cache: false,
      url: '/infofest',
      views: {
        'content' :{
          templateUrl: 'views/infofest.html',
          controller: 'infoFestCtrl',
        }
      }
    })
    .state('layout.awards', {
      cache: false,
      url: '/awards',
      views: {
        'content' :{
          templateUrl: 'views/awards.html',
          controller: 'awardsCtrl',
        }
      }
    })
    .state('layout.profile', {
      cache: false,
      url: '/profile',
      views: {
        'content' :{
          templateUrl: 'views/profile.html',
          controller: 'profileCtrl',
          resolve: {
            userLogged: ['userAuth', function(userAuth){
              return userAuth.isLogged();
            }]
          }
        }
      }
    })
    .state('layout.timeline', {
      cache: false,
      url: '/timeline',
      views: {
        'content' :{
          templateUrl: 'views/timeline.html',
          controller: 'timelineCtrl',
          resolve: {
            userLogged: ['userAuth', function(userAuth){
              return userAuth.isLogged();
            }]
          }
        }
      }
    })
    .state('layout.ranking', {
      cache: false,
      url: '/ranking',
      views: {
        'content' :{
          templateUrl: 'views/ranking.html',
          controller: 'rankingCtrl',
          resolve: {
            userLogged: ['userAuth', function(userAuth){
              return userAuth.isLogged();
            }]
          }
        }
      }
    })
    .state('noLogged', {
      cache: false,
      url: '/no-logged',
      templateUrl: 'views/noLogged.html'
    });

  $urlRouterProvider.otherwise("/");
  $ionicConfigProvider.views.swipeBackEnabled(false);

});

app.run(function($ionicPlatform,$rootScope,$state,$interval,$ionicPopup,$cordovaNoiseMeter,$cordovaDeviceMotion,$cordovaDevice,$cordovaGeolocation,$cordovaPush,$cordovaNetwork,userAuth,serverConnection) {
  $rootScope.notificacionId = "";
  $rootScope.currentMusic = 0;
  $rootScope.currentDance = 0;
  $rootScope.currentTotal = 0;
  $rootScope.MusicPercent = 0;
  $rootScope.DancePercent = 0;

  $ionicPlatform.ready(function() {
    // Hide the accessory bar by default (remove this to show the accessory bar above the keyboard
    // for form inputs)
    
    //Accelerometer
    var Kacc = 70;
    var InitAcc = true;
    var MinAcceleration = 1;

    //Sound
    var Kmusic = 32756;
     
    var watch = $cordovaDeviceMotion.watchAcceleration({ frequency: 100 });
    
    watch.then(null,function(error) {
      //console.log('ERROR '+error.code+': '+error.message);
    },
    function(acceleration) {
      if(InitAcc)
      {
        lastX = acceleration.x;
        lastY = acceleration.y;
        lastZ = acceleration.z;
        InitAcc = false;
      }
      else
      {
        deltaX =  Math.abs(lastX - acceleration.x);
        deltaY =  Math.abs(lastY - acceleration.y);
        deltaZ =  Math.abs(lastZ - acceleration.z);

        if (deltaX < MinAcceleration) deltaX = 0;
        if (deltaY < MinAcceleration) deltaY = 0;
        if (deltaZ < MinAcceleration) deltaZ = 0;

        lastX = acceleration.x;
        lastY = acceleration.y;
        lastZ = acceleration.z;

        dance = Math.sqrt( Math.pow(deltaX,2) + Math.pow(deltaY,2)+ Math.pow(deltaZ,2) );
        $rootScope.DancePercent = dance/Kacc;
      }
    });
   
    //Get Music
    var noiseWatch = $cordovaNoiseMeter.getNoise();

    noiseWatch.then(null,function(error) {
      //console.log('ERROR NOISE');
    },function(noise) {
      $rootScope.MusicPercent = noise/Kmusic;
    });
    
    $rootScope.isOffline = $cordovaNetwork.isOffline();
  
    if($rootScope.isOffline) {
      if(userAuth.isFirstTime())
      {
        $rootScope.awards = "img/awards.png";
        $rootScope.map = "img/map.png";
        $rootScope.background = "img/background.jpg";
        
        var map = {
          status: "success",
          data: {
            title: "Vi\u00f1a Rock"
          }
        };

        serverConnection.setCache('map',map);
        
        var slider = {
          status: "success",
          data:[
            { text: "La app \u003Cstrong\u003Ecoolway\u003C\/strong\u003E es una app\u003Cbr\u003E \nincre\u00edble que mide tu nivel de\u003Cbr\u003E \nfiesta y el de tus amigos.\u003Cbr\u003E\nPodr\u00e1s compartirlo y ganar\u003Cbr\u003E \nmuchos \u003Cstrong\u003Epremios\u003C\/strong\u003E."},
            { text: "\u003Cstrong\u003EBAILA, SALTA, GRITA\u003C\/strong\u003E\u003Cbr\u003E\u003Cbr\u003E\nLo que sea pero... \u00a1NO PARES! \u003Cbr\u003E\nMedimos tu actividad y la \u003Cbr\u003E\nconvertimos en puntos. \u003Cbr\u003E"},
            { text: "\u003Cstrong\u003ECOMPARTE\u003C\/strong\u003E\u003Cbr\u003E\u003Cbr\u003E\nComparte en redes sociales y\u003Cbr\u003E\ngana puntos extras.\u003Cbr\u003E"},
            { text: "\u003Cstrong\u003E\u00a1VETE DE VIAJE\u003C\/strong\u003E\u003Cbr\u003E\u003Cbr\u003E\nAcumula \u003Cstrong\u003Epuntos\u003C\/strong\u003E y gana un viaje\u003Cbr\u003E\na \u003Cstrong\u003EMarruecos\u003C\/strong\u003E para dos \u003Cbr\u003E\npersonas \u00a1todo incluido! \u003Cbr\u003E\n(Y muchos premios m\u00e1s...) \u003Cbr\u003E"}
          ]};

        serverConnection.setCache('slider',slider);
      }
      else
      {
        $rootScope.awards = cordova.file.dataDirectory + "awards.png";
        $rootScope.map = cordova.file.dataDirectory + "map.png";
        $rootScope.background = cordova.file.dataDirectory + "background.jpg";
      }
    } else {
      userAuth.disabledFirstTime();
      serverConnection.get('awards',function(rsp) {
      },function(rsp){
        //console.log(rsp);
      }, { token: userAuth.getToken() } );

      serverConnection.get('map',function(rsp) {
      },function(rsp){
        //console.log(rsp);
      }, { token: userAuth.getToken() } );
    }

    $rootScope.$on('$cordovaNetwork:online', function(event, networkState){
      $rootScope.isOffline = false;
    });

    $rootScope.$on('$cordovaNetwork:offline', function(event, networkState){
      $rootScope.isOffline = true;      
    });

    if(window.cordova && window.cordova.plugins.Keyboard) {
      cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);
    }
    if(window.StatusBar) {
      StatusBar.styleDefault();
    }

    $ionicPlatform.registerBackButtonAction( function() {
      if(userAuth.checkLogged())
      {
        if( $state.current.name == 'layout.level' )
          navigator.app.exitApp();
        else
          $state.go('layout.level');
      }
      else {
        if( $state.current.name == 'layout.level')
          $state.go('login');
        else if( $state.current.name == 'login')
          $state.go('tutorial');
        else if( $state.current.name == 'tutorial')
          navigator.app.exitApp();
        else
          $state.go('layout.level',{ reload: true });
      }
    },100);
    
    var platform = $cordovaDevice.getPlatform();

    if(platform == 'Android') {

      var androidConfig = {
        "senderID": "1090006415155",
      };

      $rootScope.OS = "Android";
      
      $cordovaPush.register(androidConfig).then(function(result) {
        //console.log(result);
      }, function(error) {
        //console.log(error);
      });

      $rootScope.$on('$cordovaPush:notificationReceived', function(event, notification) {
        switch(notification.event) {
          case 'registered':
            if (notification.regid.length > 0 ) {
              $rootScope.notificationId = notification.regid;
            }
            break;

          case 'message':
            $ionicPopup.show({
              template: '<p style="color:#000;">'+notification.payload.message+'</p>',
              title: notification.payload.title,
              buttons: [
                {
                  text: '<b>Aceptar</b>',
                  type: 'button-positive',
                  onTap: function(e) {
                    this.close();
                  }
                }
              ]
            });
            break;

          case 'error':
            //console.log('GCM ERROR');
            break;

          default:
            //console.log('GCM DEFAULT');
            break;
        }
      });

    } else {

      var iosConfig = {
        "badge": false,
        "sound": true,
        "alert": true,
      };

      $rootScope.OS = "IOS";

      $cordovaPush.register(iosConfig).then(function(deviceToken) {
        $rootScope.notificationId = deviceToken;
      }, function(err) {
        //console.log(err);
        //console.log("error in ios notification register");
      });

      $rootScope.$on('$cordovaPush:notificationReceived', function(event, notification) {
        if (notification.alert) {
          $ionicPopup.show({
              template: '<p style="color:#000;">'+notification.alert+'</p>',
              title: notification.title,
              buttons: [
                {
                  text: '<b>Aceptar</b>',
                  type: 'button-positive',
                  onTap: function(e) {
                    this.close();
                  }
                }
              ]
            });
        }

        if (notification.sound) {
          var snd = new Media(event.sound);
          snd.play();
        } 
      });

    }

  });


  $interval(function(){
      
        var posOptions = {timeout: 10000, enableHighAccuracy: true,maximumAge: 1000*60*60};
        $cordovaGeolocation.getCurrentPosition(posOptions)
        .then(function (position) {
          $rootScope.currentLatitude = position.coords.latitude;
          $rootScope.currentLongitude = position.coords.longitude;

          serverConnection.get('data',function(rsp){
            $rootScope.total = rsp.data.total;
            $rootScope.media = rsp.data.media;
            $rootScope.points =rsp.data.points;
            $rootScope.position = rsp.data.position;
            $rootScope.dance = rsp.data.dance;
            $rootScope.music = rsp.data.music;
            $rootScope.feast = rsp.data.feast;
            $rootScope.currentMusic = 0;
            $rootScope.currentDance = 0;
            $rootScope.currentTotal = 0;
          },function(){
            $rootScope.currentMusic = 0;
            $rootScope.currentDance = 0;
            $rootScope.currentTotal = 0;
          },{
            token: userAuth.getToken(),
            logged: userAuth.checkLogged(),
            music: $rootScope.currentMusic,
            dance: $rootScope.currentDance,
            total: $rootScope.currentTotal,
            latitude: $rootScope.currentLatitude,
            longitude: $rootScope.currentLongitude,
            first: "0"
          });

        }, function(err) {

          $ionicPopup.show({
            template: '<p style="color:#000;">Para poder sumar puntos debes tener activado tu gps</p>',
            title: 'Activar GPS',
            buttons: [
              {
                text: '<b>Aceptar</b>',
                type: 'button-positive',
                onTap: function(e) {
                  this.close();
                }
              }
            ]
          });

          serverConnection.get('data',function(rsp){
            $rootScope.total = rsp.data.total;
            $rootScope.media = rsp.data.media;
            $rootScope.points = rsp.data.points;
            $rootScope.position = rsp.data.position;
            $rootScope.dance = rsp.data.dance;
            $rootScope.music = rsp.data.music;
            $rootScope.feast = rsp.data.feast;
          },function(){
            //console.log("error get data with gps off");
          },{
            token: userAuth.getToken(),
            logged: userAuth.checkLogged(),
            music: "0",
            dance: "0",
            total: "0",
            latitude: "0",
            longitude: "0",
            first: "1"
          });
          
        });
  },1000*60*10);

  $rootScope.total = "...";
  $rootScope.media = "...";

  serverConnection.get('data',function(rsp){
    $rootScope.total = rsp.data.total;
    $rootScope.media = rsp.data.media;
    $rootScope.points = rsp.data.points;
    $rootScope.position = rsp.data.position;
    $rootScope.dance = rsp.data.dance;
    $rootScope.music = rsp.data.music;
    $rootScope.feast = rsp.data.feast;
  },function(){

  },{
    token: userAuth.getToken(),
    logged: userAuth.checkLogged(),
    music: "0",
    dance: "0",
    total: "0",
    latitude: "0",
    longitude: "0",
    first: "1"
  });

  $rootScope.$on("$stateChangeError",function (event, toState, toParams, fromState, fromParams, error) {
    if('noLogged' == error ) {
      $state.go('noLogged');
    }
  });

  $rootScope.$on("$stateChangeStart",function (event,toState,toParams,fromState) {
    if(userAuth.checkLogged()) {
      if(toState.name == "tutorial" && fromState.name == 'layout.awards')
      {
      }
      else if( toState.name == "login" || toState.name == "tutorial" )
      {
        event.preventDefault();
        $state.go('layout.level');
      }
    }
  });

});

app.controller('tutorialCtrl' ,function ($scope,$state,$ionicLoading,$ionicSlideBoxDelegate,userAuth,serverConnection) {
  
  
  $ionicLoading.show();
  serverConnection.get('slider',function(rsp) {
    $scope.steps = rsp.data;
    $ionicLoading.hide();
  },function(rsp){
    //console.log(rsp);
    $ionicLoading.hide();
  });

  $scope.guide = '<i class="icon ion-chevron-left" ></i> DESLIZA';

  $scope.slideHasChanged = function (index) {
    if(index == 3)
      $scope.guide = 'EMPIEZA <i class="icon ion-chevron-right" ></i>';
    else if ( index == 4 )
    {
      $ionicSlideBoxDelegate.slide(3,5000);
      $state.go('login');
    }
    else
      $scope.guide = '<i class="icon ion-chevron-left" ></i> DESLIZA';
  };

  $scope.activeSlide = function (a) {
    //console.log("a");
  }

});

app.controller('loginCtrl' ,function ($scope,$state,$ionicLoading,$ionicModal,$cordovaOauth,userAuth,serverConnection) {
  
  $scope.loginFake = function () {
    $state.go('layout.level');
  }
  $scope.loginFacebook = function() {
    $cordovaOauth.facebook("900256300038339", ["email"]).then(function(result) {
      userAuth.login('facebook',result.access_token);
    }, function(error) {
      //console.log(error);
    });
  };

  $scope.loginTwitter = function() {
    $cordovaOauth.twitter("jHWUDD8iD2ghAUNNBMMMLM1Yf","48LWqgN5NdhEGBx8ya2j5r5bpJx2qdzikq2jL9wKJdkOkSC92D").then(function(result) {
      r = { 
        clientId: 'jHWUDD8iD2ghAUNNBMMMLM1Yf',
        clientSecret: '48LWqgN5NdhEGBx8ya2j5r5bpJx2qdzikq2jL9wKJdkOkSC92D',
        token: result.oauth_token,
        oauth_token_secret: result.oauth_token_secret
      };
      userAuth.login('twitter',r);
    }, function(error) {
      //console.log(error);
    });
  };

  $scope.loginGoogle = function() {
    $cordovaOauth.google("1090006415155-3rdvfqlmd2sno0ehrvhghgl602conopv.apps.googleusercontent.com", ["email"]).then(function(result) {
      userAuth.login('google',result.access_token);
    }, function(error) {
      //console.log(error);
    });
  };

  $scope.loginInstagram = function() {
    $cordovaOauth.instagram("0e3680b448de464db376a09dc9e82137", ["basic"]).then(function(result) {
      userAuth.login('instagram',result.access_token);
    }, function(error) {
      //console.log(error);
    });
  };

  serverConnection.get('terms',function(rsp) {
    $scope.title = rsp.data.title;
    $scope.text = rsp.data.text;
  },function(rsp){
    //console.log(rsp);
  }, { token: userAuth.getToken() } );
  
  $ionicModal.fromTemplateUrl('views/modal.html', {
    scope: $scope,
    animation: 'slide-in-up'
  }).then(function(modal) {
    $scope.modal = modal;
  });

  $scope.openModal = function() {
    $scope.modal.show();
  };
  
  $scope.closeModal = function() {
    $scope.modal.hide();
  };

});

app.controller('layoutCtrl' ,function ($scope,userAuth) {
  $scope.logout = function () {
    userAuth.logout();
  };

  $scope.isLogged = userAuth.checkLogged();

});

app.controller('levelCtrl' ,function ($rootScope,$scope,$state,$interval,$cordovaDeviceMotion,$cordovaNoiseMeter,userAuth,serverConnection) {
  
  $scope.MusicPercent = 0;
  $scope.DancePercent = 0;
  $scope.TotalPercent = 0;

  var Kf = 0.5;
  var Kr = 0.5;
  var Km = 1.5;
    
  //Update interface
  $interval(function () {
    $scope.MusicPercent = $rootScope.MusicPercent;
    $scope.DancePercent = $rootScope.DancePercent;

    tPercent = (Kf * (Km*$scope.DancePercent+Kr*$scope.MusicPercent));

    if($scope.DancePercent > $rootScope.currentDance)
      $rootScope.currentDance = $scope.DancePercent;
    
    if($scope.MusicPercent > $rootScope.currentMusic)
      $rootScope.currentMusic = $scope.MusicPercent;
    
    $rootScope.currentTotal = (Kf * (Km*$rootScope.currentDance+Kr*$rootScope.currentMusic));

    $scope.updateMusicPercent($scope.MusicPercent);
    $scope.updateDancePercent($scope.DancePercent);
    $scope.updateTotalPercent(tPercent);
  },100);

  $scope.share = function() {
    serverConnection.share('level',{ token: userAuth.getToken() });
  }
    

  $scope.updateMusicPercent = function(percent)
  {
    percent = Math.round(percent*100);
    $scope.MusicPolygon = $scope.calculateBar(percent);
  };

  $scope.updateDancePercent = function(percent)
  {
    percent = Math.round(percent*100);
    $scope.DancePolygon = $scope.calculateBar(percent);
  };

  $scope.updateTotalPercent = function(percent){
    percent = Math.round(percent*100);
    $scope.TotalPercent = percent;
    if(percent <= 12.5)
    { 
      xp = 50 - (percent*50)/12;
      $scope.TotalPolygon = "polygon(50% 50%, 50% 100%,"+xp+"% 100%)";
    }
    else if(percent <= 37.5)
    {
      yp = 100 - ((percent-12.5)*100)/25;
      $scope.TotalPolygon = "polygon(50% 50%, 50% 100%,0% 100%,0% "+yp+"%)"; 
    }
    else if (percent <= 62.5)
    {
      xp = ((percent-37.5)*100)/25;
      $scope.TotalPolygon = "polygon(50% 50%, 50% 100%,0% 100%,0% 0%,"+xp+"% 0%)"; 
    }
    else if (percent <= 87.5)
    {
      yp = ((percent-62.5)*100)/25;
      $scope.TotalPolygon = "polygon(50% 50%, 50% 100%,0% 100%,0% 0%,100% 0%, 100% "+yp+"%)";  
    }
    else if (percent <= 100 )
    {
      xp = 100 - ((percent-87.5)*100)/25;
      $scope.TotalPolygon = "polygon(50% 50%, 50% 100%,0% 100%,0% 0%,100% 0%, 100% 100%, "+xp+"% 100%)";
    }
  };

  $scope.calculateBar = function (percent)
  {
    percent = 100 - Math.round(percent/14)*14;
    return "polygon(0% 100%, 100% 100%,100% "+percent+"%,0% "+percent+"%)";
  }
});

app.controller('lineupCtrl' ,function ($rootScope,$scope,$ionicLoading,$ionicScrollDelegate,userAuth,serverConnection) {
  
  $scope.searchArtist = '';

  $ionicLoading.show();
  serverConnection.get('lineup',function(rsp) {
    $scope.lineup = rsp.data;
    $ionicLoading.hide();
  },function(rsp){
    //console.log("Error get data in lineup");
    $ionicLoading.hide();
  },{ token: userAuth.getToken() });

  $scope.isFavorite = '';

  $scope.scrollTop = function () {
    $ionicScrollDelegate.scrollTop(true);
  };

  $scope.filterFavorite = function(){
    $scope.scrollTop();
    if($scope.isFavorite)
      $scope.isFavorite = '';
    else
      $scope.isFavorite = 1;
    return $scope.isFavorite;
  };

  $scope.addFavorite = function(artist){
    rollback = artist.favorite;
    toFavorite = artist.favorite == 0 ? 1 : 0;
    artist.favorite = 2;
    serverConnection.get('lineup/favorite',function(rsp) {
      artist.favorite = toFavorite;
    },function(rsp){
      artist.favorite = rollback;
      if($rootScope.isOffline)
        serverConnection.warning();
    },{ token: userAuth.getToken(), id : artist.id, is_favorite: rollback });
  };

});

app.controller('infoFestCtrl' ,function ($scope,$ionicLoading,userAuth,serverConnection) {
  
  $ionicLoading.show();
  serverConnection.get('map',function(rsp) {
    $scope.title = "Plano "+rsp.data.title;
    $ionicLoading.hide();
  },function(rsp){
    //console.log(rsp);
    $ionicLoading.hide();
  }, { token: userAuth.getToken() } );

  $scope.height = function () {
    return (screen.height - 90);
  };

});

app.controller('awardsCtrl' ,function ($scope,$ionicLoading,$ionicModal,serverConnection,userAuth) {

  $scope.height = function () {
    return (screen.height - (90+45));
  };

  $ionicLoading.show();
  serverConnection.get('awards',function(rsp) {
    $scope.title = rsp.data.title;
    $scope.text = rsp.data.text;
    $ionicLoading.hide();
  },function(rsp){
    //console.log(rsp);
    $ionicLoading.hide();
  }, { token: userAuth.getToken() } );
  
  $ionicModal.fromTemplateUrl('views/modal.html', {
    scope: $scope,
    animation: 'slide-in-up'
  }).then(function(modal) {
    $scope.modal = modal;
  });

  $scope.openModal = function() {
    $scope.modal.show();
  };
  
  $scope.closeModal = function() {
    $scope.modal.hide();
  };
});

app.controller('profileCtrl' ,function ($scope,$state,$ionicLoading,userAuth,serverConnection) {

  $scope.saved = false;
  
  $ionicLoading.show();
  serverConnection.get('profile',function(rsp) {
    $scope.user = rsp.data;
    $ionicLoading.hide();
  },function(rsp){
    //console.log("ERROR GET DATA PROFILE");
    $ionicLoading.hide();
  },{ token: userAuth.getToken() });
  

  $scope.processForm = function () {
    $ionicLoading.show();
    serverConnection.get('profile/update',function(rsp) {
      alert('Sus datos fueron guardados');
      $ionicLoading.hide();
      $state.go('layout.level');
    },function(rsp){
      alert('Por favor intente nuevamente');
      $ionicLoading.hide();
    },{ token: userAuth.getToken(), name: $scope.user.name, email: $scope.user.email });
  };

});

app.controller('timelineCtrl' ,function ($scope,$ionicLoading,$cordovaSocialSharing,userAuth,serverConnection) {
  
  $ionicLoading.show();
  serverConnection.get('timeline',function(rsp) {
    $scope.timeline = rsp.data;
    $ionicLoading.hide();
  },function(rsp){
    //console.log("ERROR GET DATA TIMELINE");
    $ionicLoading.hide();
  },{ token: userAuth.getToken() });

  $scope.share = function() {
    serverConnection.share('timeline',{ token: userAuth.getToken() });
  }
  
});

app.controller('rankingCtrl' ,function ($rootScope,$scope,$ionicScrollDelegate,$ionicLoading,$cordovaSocialSharing,userAuth,serverConnection) {
  
  $ionicLoading.show();
  serverConnection.get('ranking',function(rsp) {
    $scope.ranking = rsp.data;
    $ionicLoading.hide();
  },function(rsp){
    //console.log(rsp);
    $ionicLoading.hide();
  },{ token: userAuth.getToken() });

  
  $scope.share = function() {
    serverConnection.share('timeline',{ token: userAuth.getToken() });
  }

  $scope.isFavorite = 0;
  
  $scope.scrollTop = function () {
    $ionicScrollDelegate.scrollTop(true);
  };

  $scope.filterFavorite = function(){
    $scope.scrollTop();
    if($scope.isFavorite)
      $scope.isFavorite = '';
    else
      $scope.isFavorite = 1;
    return $scope.isFavorite;
  };

  $scope.addFavorite = function(friend){
    rollback = friend.favorite;
    toFavorite = friend.favorite == 0 ? 1 : 0;
    friend.favorite = 2;
    serverConnection.get('ranking/favorite',function(rsp) {
      friend.favorite = toFavorite;
    },function(rsp){
      friend.favorite = rollback;
      if($rootScope.isOffline)
        serverConnection.warning();
    },{ token: userAuth.getToken(), id : friend.id, is_favorite: rollback });
  };
  
});

app.factory('userAuth',function ($rootScope,$state,$q,$http,$ionicLoading,$cordovaDevice,$cordovaOauthUtility,serverConnection) {
  return {

    login : function (social,token) {
      $ionicLoading.show();
      var that = this;
      if('google' == social)
      {
        that.googleProfile(token,function(obj) {
          that.serverLogin(obj);
        },function(){
          $ionicLoading.hide();
          alert("Se produjo un error por favor vuelva a intentarlo");
        });
      }
      else if('facebook' == social)
      {
        that.facebookProfile(token,function(obj) {
          that.serverLogin(obj);
        },function(){
          $ionicLoading.hide();
          alert("Se produjo un error por favor vuelva a intentarlo");
        });
      }
      else if('instagram' == social)
      {
        that.instagramProfile(token,function(obj) {
          that.serverLogin(obj);
        },function(){
          $ionicLoading.hide();
          alert("Se produjo un error por favor vuelva a intentarlo");
        });
      }
      else
      {
        that.twitterProfile(token,function(obj) {
          that.serverLogin(obj);
        },function(){
          $ionicLoading.hide();
          alert("Se produjo un error por favor vuelva a intentarlo");
        });
      }
    },

    serverLogin : function (obj) {
      serverConnection.get('login',function(){
        window.localStorage.setItem('isLogged',1);
        $ionicLoading.hide();
        if(obj.email)
          $state.go('layout.level');
        else
          $state.go('layout.profile');
      },function(rsp) {
        $ionicLoading.hide();
        alert("Se produjo un error por favor vuelva a intentarlo");
      }, { 
        token: this.getToken(),
        email: obj.email,
        name: obj.name,
        notificationId: $rootScope.notificationId,
        os: $rootScope.OS
      });
    },

    logout : function () {
      window.localStorage.setItem('isLogged',0);
      $state.go('tutorial');
    },

    checkLogged : function () {
      if (window.localStorage.getItem('isLogged') == 1)
        return true;
      return false;
    },

    isLogged : function () {
      var defer = $q.defer();
      if (window.localStorage.getItem('isLogged') == 1)
        defer.resolve("isLogged");
      else
        defer.reject('noLogged');
      return defer.promise;
    },

    isFirstTime : function () {
      if (window.localStorage.getItem('isFirstTime') != 0 )
        return true;
      return false;
    },

    disabledFirstTime : function () {
      window.localStorage.setItem('isFirstTime',0);
    },

    getToken : function () {
      //return '1e93ee47231575bd'; 
      return $cordovaDevice.getUUID();
    },

    instagramProfile: function (token,success,error) {
      $http({
        method: "GET",
        url: 'https://api.instagram.com/v1/users/self/?access_token='+token
      }).success(function (rsp) {
        success({ name: rsp.data.full_name, email: '' });
      })
      .error(function(rsp){
        rsp = {};
        rsp.message = "Instagram Not Found";
        error(rsp);
      });
    },

    facebookProfile: function (token,success,error) {
      $http({
        method: "GET",
        url: 'https://graph.facebook.com/me?access_token='+token
      }).success(function (rsp) {
        success({ name: rsp.name, email: rsp.email });
      })
      .error(function(rsp){
        rsp = {};
        rsp.message = "Facebook Not Found";
        error(rsp);
      });
    },

    googleProfile: function (token,success,error) {
      $http({
        method: "GET",
        url: 'https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token='+token
      }).success(function (rsp) {
        success({ name: rsp.name, email: rsp.email });
      })
      .error(function(rsp){
        rsp = {};
        rsp.message = "Google Not Found";
        error(rsp);
      });
    },

    twitterProfile: function (result,success,error) {

      var oauthObject = {
        oauth_consumer_key: result.clientId,
        oauth_nonce: $cordovaOauthUtility.createNonce(10),
        oauth_signature_method: "HMAC-SHA1",
        oauth_timestamp: Math.round((new Date()).getTime() / 1000.0),
        oauth_token: result.token,
        oauth_version: "1.0"
      };

      var signatureObj = $cordovaOauthUtility.createSignature("GET", "https://api.twitter.com/1.1/account/verify_credentials.json", oauthObject,{ include_email: "true" }, result.clientSecret,result.oauth_token_secret);
      
      $http({
        method: "GET",
        url: 'https://api.twitter.com/1.1/account/verify_credentials.json',
        headers: {
          "Authorization": signatureObj.authorization_header
        },
        params: {
          include_email: "true"
        }
      }).success(function (rsp) {
        success({ name: rsp.name, email: '' });
      })
      .error(function(rsp){
        rsp = {};
        rsp.message = "Twitter Not Found";
        error(rsp);
      });
    }
  }
});

app.factory('serverConnection',function ($rootScope,$http,$q,$timeout,$cordovaGeolocation,$cordovaSocialSharing,$ionicPopup,$cordovaFileTransfer) {
  //var host = 'http://festivales.icox.mobi';
  //var host = 'http://local.coolway.192.168.1.100.xip.io';
  var host = 'http://62.75.210.58';
  var api = host+'/api/';
  return {

    getHost : function () {
      return host;
    },
    share : function (section,params) {
      that = this;
      switch (section) {
        case 'level': 
          message = "Ranking Posición "+$rootScope.position+" con "+$rootScope.points+" Puntos. Baile: "+$rootScope.dance+" , Músic: "+$rootScope.music+" , Feast: "+$rootScope.feast;
          subject = "Coolway Let´s Dance";
          file = host+'/images/icon.png';
          link = api+'download';
          break;

        case 'timeline': 
          message = "¡Mira cuánto me divierto! Descarga tú también la app y mide tu nivel de diversión";
          subject = "Coolway Let´s Dance";
          link = api+'download';
          break;

        case 'ranking':
          message = "¡Mira cuánto me divierto! Descarga tú también la app y mide tu nivel de diversión";
          subject = "Coolway Let´s Dance";
          link = api+'download';
          break;
      }

      if(section != 'level') {
        
        navigator.screenshot.save(function(error,res){
          if(error){
            console.error(error);
          }else{
            file = "file://"+res.filePath;
            $cordovaSocialSharing
              .share(message,subject,file,link)
              .then(function(result) {

                var posOptions = {timeout: 10000, enableHighAccuracy: true,maximumAge: 1000*60*60};
                $cordovaGeolocation.getCurrentPosition(posOptions).then(function (position) {
                  params.latitude = position.coords.latitude;
                  params.longitude = position.coords.longitude;
                  that.get('share',function(result){
                    //console.log(result);
                  }, function(){
                    //console.log("error share");
                  }, params);
                }, function(err){
                  //console.log(err)
                });

              }, function(err) {
                //console.log(err);
            });
          }
        });
      
      } else {

        $cordovaSocialSharing.share(message,subject,file,link)
          .then(function(result) {

            var posOptions = {timeout: 10000, enableHighAccuracy: true,maximumAge: 1000*60*60};
            $cordovaGeolocation.getCurrentPosition(posOptions).then(function (position) {
              params.latitude = position.coords.latitude;
              params.longitude = position.coords.longitude;
              that.get('share',function(result){
                //console.log(result);
              }, function(){
                //console.log("error share");
              }, params);
            }, function(err){
              //console.log(err)
            });

          }, function(err) {
            //console.log(err);
        });
      }

    },

    get : function (url,success,error,params) {
      var that = this;
      
      if( $rootScope.isOffline ) {
        cache = that.getCache(url);
        if(cache.status == 'success')
          success(cache);
        else
          error(cache);
      }
      else {
        $http({
          method: "POST",
          url: api+url,
          data: params,
        }).success(function (rsp) {
          if(rsp.status == "success")
          {
            that.setCache(url,rsp);
            success(rsp);
          }
          else
            error(rsp);
        })
        .error(function(rsp){
          rsp = {};
          rsp.message = "Not Found";
          error(rsp);
        });
      }
    },

    setCache: function (url,rsp) {
      var str = JSON.stringify(rsp);
      window.localStorage.setItem(url,str);
      if( rsp.status == "success" &&  typeof rsp.data != "undefined" && typeof rsp.data.image != "undefined" )
      {
        this.downloadFile(rsp.data.image,url,"png");
        if(url == 'awards')
          this.downloadFile(rsp.data.background,"background","jpg");
      }
    },

    getCache: function (url) {
      if(data = window.localStorage.getItem(url))
      {

        if( url == 'map' && window.localStorage.getItem('isFirstTime') == 0 )
          $rootScope.map = cordova.file.dataDirectory + "map.png";
        
        else if( url == 'awards' && window.localStorage.getItem('isFirstTime') == 0 )
        {
          $rootScope.awards = cordova.file.dataDirectory + "awards.png";
          $rootScope.background = cordova.file.dataDirectory + "background.jpg";
        }

        return JSON.parse(data);
      }
      return {status:'error',message:'not cached'};
    },

    downloadFile: function(url,name,format) {
      var targetPath = cordova.file.dataDirectory + name + "." +format;
      var trustHosts = true
      var options = {};

      $cordovaFileTransfer.download(url, targetPath, options, trustHosts)
        .then(function(result) {
          //console.log("succes download");
          if(name == 'awards')
            $rootScope.awards = targetPath;
          else if(name == 'map')
            $rootScope.map = targetPath;
          else if(name == 'background')
            $rootScope.background = targetPath;
        }, function(err) {
          //console.log("error download");
        }, function (progress) {
          /*$timeout(function () {
            //console.log((progress.loaded / progress.total) * 100);
          })*/
      });
    },

    warning: function () {
      $ionicPopup.show({
        template: '<p style="color:#000;">Debe tener conexion a internet para poder acceder a esta funcionalidad</p>',
        title: 'Conexion a Internet',
        buttons: [
          {
            text: '<b>Salir</b>',
            type: 'button-positive',
            onTap: function(e) {
              this.close();
              navigator.app.exitApp();
            }
          }
        ]
      });
    }

  }
});