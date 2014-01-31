'use strict';

var $routeProviderReference;
var $menuRetriverProvider;
var route = function($routeProvider,menuRetriver) {
	$routeProviderReference = $routeProvider;
	$menuRetriverProvider = menuRetriver;
}

var datapicker_config = function($datepickerProvider) {
	angular.extend($datepickerProvider.defaults, {
		dateFormat: 'dd/MM/yyyy',
		weekStart: 1
	});
}

// Declare app level module which depends on filters, and services
var rescueApp = angular
		.module('rescueApp', ['ngRoute','ui.bootstrap','http-auth-interceptor','ngIdle','angularFileUpload','ngAnimate','ngSanitize', 'mgcrea.ngStrap.helpers.dateParser','mgcrea.ngStrap.tooltip','mgcrea.ngStrap.datepicker','mgcrea.ngStrap.helpers.dimensions', 'MenuServiceModule'])
		.config(['$routeProvider', 'menuRetriverProvider', route])
		.config(['$keepaliveProvider', '$idleProvider', function($keepaliveProvider, $idleProvider) {
				  $idleProvider.idleDuration(50000); // DA CONFIGURARE
				  $idleProvider.warningDuration(5);
				  $keepaliveProvider.interval(10);
				}])
		.config(datapicker_config)
		.run(['$idle','$route', function($idle,$route) {
  			$idle.watch();
  			var retriver = $menuRetriverProvider.$get; //ottengo la funzione di restituzione del menu
			retriver($routeProviderReference,$route);
		}]);

