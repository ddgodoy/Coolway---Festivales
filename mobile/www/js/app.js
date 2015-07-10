document.addEventListener('deviceready', function onDeviceReady() {
  angular.bootstrap(document, ['app']);
}, false);

app = angular.module('app', ['ionic','ionic.contrib.drawer','ngCordova','ImgCache'])

app.config(function ($stateProvider,$urlRouterProvider,$ionicConfigProvider,ImgCacheProvider) {
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
  ImgCacheProvider.setOptions({
        debug: true,
        usePersistentCache: true
    });
  ImgCacheProvider.manualInit = true;

});

app.run(function($ionicPlatform,$rootScope,$state,$interval,$ionicPopup,$cordovaNoiseMeter,$cordovaDeviceMotion,$cordovaDevice,$cordovaGeolocation,$cordovaPush,$cordovaNetwork,userAuth,serverConnection,ImgCache) {
  
  $rootScope.notificacionId = "";
  $rootScope.OS = "IOS";
  $rootScope.currentMusic = 0;
  $rootScope.currentDance = 0;
  $rootScope.currentTotal = 0;
  $rootScope.MusicPercent = 0;
  $rootScope.DancePercent = 0;
  $rootScope.total = "...";
  $rootScope.media = "...";

  $ionicPlatform.ready(function() {
    ImgCache.$init();
    if(window.cordova) {
      /*if(!cordova.plugins.backgroundMode.isEnabled())
      {
        cordova.plugins.backgroundMode.configure({
          silent: true
        })
        cordova.plugins.backgroundMode.enable();
      }*/
    
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

      userAuth.disabledFirstTime();
      
      $rootScope.isOffline = $cordovaNetwork.isOffline();

      $rootScope.$on('$cordovaNetwork:online', function(event, networkState){
        $rootScope.isOffline = false;
      });

      $rootScope.$on('$cordovaNetwork:offline', function(event, networkState){
        $rootScope.isOffline = true;      
      });

      if(window.cordova.plugins.Keyboard) {
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
          "senderID": "1090006415155"
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

        $rootScope.OS = "IOS";

        var iosConfig = {
          "badge": false,
          "sound": true,
          "alert": true
        };

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
    }

  });

  if(window.cordova) {
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
  }

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

});

app.controller('loginCtrl' ,function ($scope,$state,$ionicLoading,$ionicModal,$cordovaOauth,userAuth,serverConnection) {
  
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

  $scope.title = 'Politicas de Privacidad';
  $scope.text = 'Vanyor S.A. se encuentra adaptada a las disposiciones de la Ley Orgánica 15/1999, de 13 de diciembre, y a la Ley 34/2002, de 11 de julio, de servicios de la sociedad de la información y del comercio electrónico, y disposiciones que las desarrollan.Se le informa que el registro de sus datos en un fichero automatizado titularidad de Vanyor S.A. debidamente registrado en la Agencia Española de Protección, que tiene por finalidad la prestación y administración del servicio contratado o solicitado, así como la realización de estadísticas, la remisión de publicidad y otras promociones comerciales que Vanyor S.A. pueda efectuar en un futuro.El titular de los datos tendrá en todo momento el derecho de acceder a los ficheros automatizados, pudiendo ejercitar también los derechos de rectificación, cancelación y oposición en los términos recogidos en la legislación de protección de datos. Para el ejercicio de dichos derechos deberá dirigirse por carta, o cualquier otro medio fehaciente, al domicilio social de esta entidad, situado en Paterna, Calle Charles Robert Darwin, 34-36 (46980 - VALENCIA), con la inserción del término A.R.C.O., debidamente firmada por el titular de los datos, con indicación de su domicilio y adjuntando copia de su Documento Nacional de Identidad.';
  
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

  serverConnection.get('lineup',function(rsp) {
    $scope.lineup = rsp.data;
  },function(rsp){
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

app.controller('infoFestCtrl' ,function ($scope,$ionicLoading,userAuth,serverConnection,ImgCache) {
  $scope.height = function () {
    return (screen.height - 90);
  };
  $scope.map = userAuth.getMap();
});

app.controller('awardsCtrl' ,function ($scope,$ionicLoading,$ionicModal,serverConnection,userAuth) {

  $scope.height = function () {
    return (screen.height - (90+45));
  };

  $scope.title = 'PREMIO CONCURSO ARENAL SOUND.';
  $scope.text = '1. Ámbito de la promoción: este es un sorteo promocional organizado en España y de conformidad con la legislación española, en el que pueden participar únicamente usuarios residentes en España. Se anulará automáticamente cualquier participación procedente de un territorio donde esté prohibido por normativas o por legislación.2. Objeto: La organización de esta promoción es llevada a cabo por la empresa VANYOR S.A con número de CIF A96304134 y sita en la Calle Charles Robert Darwin 34-36, CP: 46980 Paterna, España. Durante el periodo promocional los participantes entrarán en el sorteo de un viaje a Coachella.3. Período promocional: Del día 28/07/2015 al 02/08/2015, durante el festival del ARENAL SOUND 2015.4. Participación: La participación en la promoción tiene carácter gratuito y puede participar cualquier persona física mayor de 18 años que resida en España y que se descargue y registre correctamente en la aplicación de Coolway. La inclusión en la promoción de aquellos consumidores que cumplan los requisitos del punto anterior será automática. El consumidor agraciado podrá renunciar al premio, pero en todo caso la aceptación del mismo llevará implícita la aceptación sin reservas de las bases de esta promoción. El premio no podrá ser canjeables por gratificación económica u otro regalo. El ganador del primer premio será la persona que más puntos consiga y que quede primero en la clasificación general del festival.5. Concurso: El día 03/08/2015 anunciaremos al ganador tras verificar que su participación ha sido correcta. El mismo día 03/08/2015 se comunicará la noticia a los agraciados por correo electrónico, y una vez que confirmen su asistencia, se publicará el nombre de ellos en la página oficial de facebook de Coolway.6. La empresa organizadora de la promoción se reserva expresamente el derecho a efectuar cualquier cambio en las bases de la promoción así como suspender o ampliar la misma por causa justificada.7. Estas bases generales y cualquier base específica publicada por Coolway se rigen por la legislación española y cualquier litigio que no pueda resolverse de forma amistosa será competencia exclusiva de los tribunales de Valencia (España).8. Tratamiento de datos personales de los participantes: De conformidad con La Ley Orgánica 15/1999, de Protección de Datos de Carácter Personal, le informamos que los datos suministrados a través del formulario pasarán automáticamente a ser registros de usuarios en la plataforma Vanyor y Yorga y serán incorporados a los ficheros titularidad de VANYOR S.A cuya finalidad es el envío de publicidad e información comercial sobre los productos, así como de productos, servicios y promociones que realizan las mismas, mediante los siguientes medios: servicios de mensajería, SMS, correo electrónico y/o correo postal, o cualquier otro medio. Los datos no serán cedidos o comunicados a terceros, salvo en los supuestos necesarios para la organización, desarrollo y control de las finalidades expresadas, así como en los supuestos previstos, según Ley. Usted puede acceder, rectificar, cancelar u oponerse al tratamiento de los datos, así como revocar el consentimiento inicialmente prestado, enviando un correo electrónico desde la dirección e-mail que utilizó en su registro, a info@coolway.com, indicando en el asunto "BAJA Publicidad".';
  
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
  
  serverConnection.get('timeline',function(rsp) {
    $scope.timeline = rsp.data;
  },function(rsp){
  },{ token: userAuth.getToken() });

  $scope.share = function() {
    serverConnection.share('timeline',{ token: userAuth.getToken() });
  }
  
});

app.controller('rankingCtrl' ,function ($rootScope,$scope,$ionicScrollDelegate,$ionicLoading,$cordovaSocialSharing,userAuth,serverConnection) {
  
  serverConnection.get('ranking',function(rsp) {
    $scope.ranking = rsp.data;
  },function(rsp){
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

app.factory('userAuth',function ($rootScope,$state,$q,$http,$ionicPopup,$ionicLoading,$cordovaDevice,$cordovaOauthUtility,serverConnection) {
  return {
    getMap : function() {
      return  serverConnection.getHost()+'/uploads/images/plano_55a0034121857.png';
    },
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
      if(window.cordova)
        return $cordovaDevice.getUUID();
      else
        return '1e93ee47231575bd'; 
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
  var host = 'http://festivales.icox.mobi';
  //var host = 'http://local.coolway.192.168.1.102.xip.io';
  //var host = 'http://local.coolway/app_dev.php';
  //var host = 'http://62.75.210.58';
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
          link = 'http://www.coolway.com/coolway-on-tour';
          break;

        case 'timeline':
          message = "¡Mira cuánto me divierto! Descarga tú también la app y mide tu nivel de diversión";
          subject = "Coolway Let´s Dance";
          link = 'http://www.coolway.com/coolway-on-tour';
          break;

        case 'ranking':
          message = "¡Mira cuánto me divierto! Descarga tú también la app y mide tu nivel de diversión";
          subject = "Coolway Let´s Dance";
          link = 'http://www.coolway.com/coolway-on-tour';
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
      $http({
        method: "POST",
        url: api+url,
        data: params,
      }).success(function (rsp) {
        if(rsp.status == "success")
          success(rsp);
        else
          error(rsp);
      })
      .error(function(rsp){
        rsp = {};
        rsp.message = "Not Found";
        error(rsp);
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