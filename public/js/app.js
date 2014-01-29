'use strict';

var route = function($routeProvider) {
	$routeProvider.when('/profilo', {templateUrl: 'partials/profilo.html', controller: 'ProfileController', authRequired: true});
	$routeProvider.when('/home', {templateUrl: 'partials/home.html', controller: 'HomeController'});
	$routeProvider.when('/login', {templateUrl: 'partials/login.html', controller: 'LoginController'});
	
	$routeProvider.when('/ricercacensimento', {templateUrl: 'partials/ricercacensimento.html', controller: 'RicercaController'});
	$routeProvider.when('/lostpassword', {templateUrl: 'partials/lostpassword.html', controller: 'ProfileController'});
	
	$routeProvider.when('/upload-asa', {templateUrl: 'partials/uploadasa.html', controller: 'AdminController'});

	$routeProvider.otherwise({redirectTo: '/ricercacensimento'}); // quando ci saranno altre funzionalita sara' /home
}

var datapicker_config = function($datepickerProvider) {
	angular.extend($datepickerProvider.defaults, {
		dateFormat: 'dd/MM/yyyy',
		weekStart: 1
	});
}

// Declare app level module which depends on filters, and services
var rescueApp = angular
		.module('rescueApp', ['ngRoute','ui.bootstrap','http-auth-interceptor','ngIdle','angularFileUpload','ngAnimate','ngSanitize', 'mgcrea.ngStrap.helpers.dateParser','mgcrea.ngStrap.tooltip','mgcrea.ngStrap.datepicker','mgcrea.ngStrap.helpers.dimensions'])
		.config(['$routeProvider', route])
		.config(['$keepaliveProvider', '$idleProvider', function($keepaliveProvider, $idleProvider) {
				  $idleProvider.idleDuration(50000); // DA CONFIGURARE
				  $idleProvider.warningDuration(5);
				  $keepaliveProvider.interval(10);
				}])
		.config(datapicker_config);

rescueApp.run(['$idle', function($idle) {
  $idle.watch();
}]);