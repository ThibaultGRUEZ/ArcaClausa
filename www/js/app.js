angular.module('starter', ['ionic', 'starter.controllers', 'ngConstellation'])

.run(function($ionicPlatform, $rootScope, constellationConsumer) {
  $ionicPlatform.ready(function() {
    //constellationConsumer.initializeClient("http://192.168.137.212:8088", "123456789", "Application Ionic");
    constellationConsumer.initializeClient("http://localhost:8088", "123456789", "Application Ionic");
    constellationConsumer.onConnectionStateChanged(function (change) {
        if (change.newState === $.signalR.connectionState.connected) {
            console.log("Connecté à constellation");
            $rootScope.isConnected = true;
        }
    });
    constellationConsumer.connect();
    $rootScope.constellation = constellationConsumer;
  });

 $rootScope.myFunc = function() {
        var onSuccess = function(position) {
        $("#latitude").text(position.coords.latitude);
        $("#longitude").text(position.coords.longitude);
        constellationConsumer.registerStateObjectLink("*", "MyBrain", "Status", "*", function(so) {
          $rootScope.stat = so.Value;
          $rootScope.$apply();
          });
       
        $rootScope.login= document.getElementById('login').value;
        $rootScope.password= document.getElementById('password').value;
        $rootScope.constellation.sendMessage({ Scope: 'Package', Args: ['MyBrain'] }, 'UpdateValues', [position.coords.latitude, position.coords.longitude,  $rootScope.login,  $rootScope.password ]);   
    } 

    $rootScope.exitApp = function(){
        navigator.app.exitApp();
    }

    function onError(error) {
        alert('code: '    + error.code    + '\n' +
              'message: ' + error.message + '\n');
    }

    navigator.geolocation.getCurrentPosition(onSuccess, onError); 
    };

})

.config(function($stateProvider, $urlRouterProvider) {

  // Ionic uses AngularUI Router which uses the concept of states
  // Learn more here: https://github.com/angular-ui/ui-router
  // Set up the various states which the app can be in.
  // Each state's controller can be found in controllers.js
  $stateProvider

  // setup an abstract state for the tabs directive
    .state('tab', {
    url: '/tab',
    abstract: true,
    templateUrl: 'templates/tabs.html'
  })

  // Each tab has its own nav history stack:

  .state('tab.dash', {
    url: '/dash',
    views: {
      'tab-dash': {
        templateUrl: 'templates/tab-dash.html',
        controller: 'DashCtrl'
      }
    }
  })

  .state('tab.chats', {
      url: '/chats',
      views: {
        'tab-chats': {
          templateUrl: 'templates/tab-chats.html',
          controller: 'ChatsCtrl'
        }
      }
    })
    .state('tab.chat-detail', {
      url: '/chats/:chatId',
      views: {
        'tab-chats': {
          templateUrl: 'templates/chat-detail.html',
          controller: 'ChatDetailCtrl'
        }
      }
    })

  .state('tab.account', {
    url: '/account',
    views: {
      'tab-account': {
        templateUrl: 'templates/tab-account.html',
        controller: 'AccountCtrl'
      }
    }
  });

  // if none of the above states are matched, use this as the fallback
  $urlRouterProvider.otherwise('/tab/dash');

});
