'use strict';

angular.module('MenuServiceModule', ['ngRoute'])
		.factory('menuRetriver', function($routeProvider,$route) {

			var baseUrl = document.location.pathname.substr(0, document.location.pathname.lastIndexOf("\/"));

			var injector = angular.injector(['ng']);
        	var $http = injector.get('$http');
			var $q = injector.get('$q');

        	var deferred = $q.defer();

			$http({
		        url: baseUrl+'/menu',
		        data: null,
		        method: "GET",
		        headers: {
		            "Content-Type": "application/json",
		            "access_token": "sometoken"
		        }
		    }).success(function(result) {
		    	angular.forEach(result.menu,function(el){
					$routeProvider.when(el.Path, {  templateUrl: ""+el.TemplateUrl , controller: ""+el.Controller });
				});
				$routeProvider.otherwise({redirectTo: '/ricercacensimento'}); // quando ci saranno altre funzionalita sara' /home
				$route.reload();
				deferred.resolve($routeProvider);
			}).error(function(error){
		    	alert(error);
		    	//console.log(error);
		    });

		    return deferred.promise;	

		});