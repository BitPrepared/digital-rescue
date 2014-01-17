'use strict';

rescueApp.controller('LoginController', function ($scope, $http, authService) {
    
    $scope.submit = function() {
      $http.post('auth/login').success(function() {
        authService.loginConfirmed();
      });
    }

  });