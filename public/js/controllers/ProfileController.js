'use strict';

rescueApp.controller('ProfileController' , function ProfileController($scope){

	$scope.logout = function() {
      $http.post('auth/logout').success(function() {
        $scope.restrictedContent = [];
      });
    }
		

	}
);