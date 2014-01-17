'use strict';

var route = function($routeProvider) {
	$routeProvider.when('/profilo', {templateUrl: 'partials/profilo.html', controller: 'ProfileController', authRequired: true});
	$routeProvider.when('/home', {templateUrl: 'partials/home.html', controller: 'HomeController'});
	$routeProvider.when('/login', {templateUrl: 'partials/login.html', controller: 'LoginController'});
	
	$routeProvider.when('/ricercacensimento', {templateUrl: 'partials/ricercacensimento.html', controller: 'RicercaController'});
	$routeProvider.when('/lostpassword', {templateUrl: 'partials/lostpassword.html', controller: 'ProfileController'});
	
	$routeProvider.otherwise({redirectTo: '/home'});
}

// Declare app level module which depends on filters, and services
var rescueApp = angular
		.module('rescueApp', ['ngRoute','ui.bootstrap','http-auth-interceptor','ngIdle'])
		.config(['$routeProvider', route])
		.config(['$keepaliveProvider', '$idleProvider', function($keepaliveProvider, $idleProvider) {
				  $idleProvider.idleDuration(50000); // DA CONFIGURARE
				  $idleProvider.warningDuration(5);
				  $keepaliveProvider.interval(10);
				}]);

rescueApp.run(['$idle', function($idle) {
  $idle.watch();
}]);