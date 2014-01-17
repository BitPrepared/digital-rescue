'use strict';


rescueApp.controller('RicercaController' , function RicercaController($scope,$modal){

	$scope.dateOptions = {
    	'year-format': "'yyyy'",
    	'month-format': "'MM'",
    	'day-format': "'dd'",
    	'starting-day': 1
  	};

	/*$scope.richiesta = {
		nome: "nome",
		cognome : "cognome",
		datanascita : "2014-01-17T15:45:44.483Z",
		email: "prova@prova.it"
	};*/

	$scope.richiesta = {
		nome: "",
		cognome : "",
		datanascita : "",
		email: ""
	};

	$scope.today = function() {
		$scope.dt = new Date();
	};
	$scope.today();

	$scope.clear = function () {
		$scope.dt = null;
	};

	$scope.open = function($event) {
    	$event.preventDefault();
    	$event.stopPropagation();

    	$scope.opened = true;
	};

	$scope.cerca = function() {

		$scope.richiesta.datanascita = $scope.dt.toJSON();

		// http://stackoverflow.com/questions/19200525/bootstrap-3-modal-in-html5mode-in-angularjs
		var ModalInstanceCtrl = function ($scope, $modalInstance, richiesta) {
			$scope.richiesta = richiesta;

			$scope.confirm = function () {

				console.log(JSON.stringify($scope.richiesta));

				/*$http.post('auth/logout').success(function() {
		        $scope.restrictedContent = [];
		      	});*/

			  	richiesta.nome = "";
				richiesta.cognome = "";
				richiesta.datanascita = "";
				richiesta.email = "";
		    	$modalInstance.close();
			};

			$scope.close = function () {
			  	richiesta.nome = "";
				richiesta.cognome = "";
				richiesta.datanascita = "";
				richiesta.email = "";
		    	$modalInstance.close();
			};
		};

		var modalConfirm = $modal.open({
	      templateUrl: 'confirm-dialog.html',
		  windowClass: 'modal-warning',
		  controller: ModalInstanceCtrl,
      	  resolve: {
        	richiesta: function () {
          		return $scope.richiesta;
        	}
      	  }
	    });

    } // fine cerca

});

