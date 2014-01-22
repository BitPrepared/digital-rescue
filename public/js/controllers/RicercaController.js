'use strict';


rescueApp.controller('RicercaController' , function RicercaController($scope,$modal,$http){

	$scope.dateOptions = {
    	'year-format': "'yyyy'",
    	'month-format': "'MM'",
    	'day-format': "'dd'",
    	'starting-day': 1
  	};

  	$scope.asyncSelected = "";

	$scope.richiesta = {
		nome: "",
		cognome : "",
		datanascita : "",
		luogonascita : ""
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
		$scope.richiesta.luogonascita = $scope.asyncSelected;

		// http://stackoverflow.com/questions/19200525/bootstrap-3-modal-in-html5mode-in-angularjs
		var ModalInstanceCtrl = function ($scope, $http, $modalInstance, richiesta) {
			$scope.richiesta = richiesta;

			$scope.confirm = function () {

				console.log(JSON.stringify($scope.richiesta));

				var toSend = $scope.richiesta;

				$http({
                    url: '/rescue/codicecensimento',
                    data: toSend,
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "access_token": "sometoken"
                    }
                }).success(function(data, status, headers, config) {
							// this callback will be called asynchronously
							// when the response is available
							/*
							console.log(data);
							console.log(status);
							console.log(headers);
							console.log(config);
							*/
                			richiesta.nome = "";
							richiesta.cognome = "";
							richiesta.datanascita = "";
							richiesta.luogonascita = "";
					    	$modalInstance.close();

                        }).error(function(error){

                        	richiesta.nome = "";
							richiesta.cognome = "";
							richiesta.datanascita = "";
							richiesta.luogonascita = "";
					    	$modalInstance.close();

                        	//console.log(error);
                        });


				/*$http.post('/rescue/codicecensimento').success(function($data) {
		      	});*/

			};

			$scope.close = function () {
			  	richiesta.nome = "";
				richiesta.cognome = "";
				richiesta.datanascita = "";
				richiesta.luogonascita = "";
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

    $scope.getLocation = function(val) {
	    return $http.get('/location', {
	      params: {
	        location: val,
	      }
	    }).then(function(res){
	      var addresses = [];
	      angular.forEach(res.data.results, function(item){
	        addresses.push(item);
	      });
	      return addresses;
	    });
	};
/*
    $scope.getLocation = function(val) {
	    return $http.get('http://maps.googleapis.com/maps/api/geocode/json', {
	      params: {
	        address: val,
	        sensor: false
	      }
	    }).then(function(res){
	      var addresses = [];
	      angular.forEach(res.data.results, function(item){
	        addresses.push(item.formatted_address);
	      });
	      return addresses;
	    });
	};
*/
});

