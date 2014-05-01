'use strict';


rescueApp.controller('RicercaController' , function RicercaController($scope,$modal,$http){

	var baseUrl = document.location.pathname.substr(0, document.location.pathname.lastIndexOf("\/"));

  	$scope.selectedDate = new Date();

  	$scope.asyncSelected = "";

	$scope.richiesta = {
		nome: "",
		cognome : "",
		datanascita : "",
		luogonascita : ""
	};

	$scope.getType = function(key) {
		return Object.prototype.toString.call($scope[key]);
	};

	$scope.open = function($event) {
		$event.preventDefault();
		$event.stopPropagation();
		$scope.opened = true;
	}; 

	$scope.cerca = function() {

		//$scope.richiesta.datanascita = $scope.dt.toJSON();
		$scope.richiesta.datanascita = $scope.selectedDate;
		$scope.richiesta.luogonascita = $scope.asyncSelected;

		// Modal Info: http://stackoverflow.com/questions/19200525/bootstrap-3-modal-in-html5mode-in-angularjs
		var ModalInstanceCtrl = function ($scope, $http, $modalInstance, richiesta) {
			
			$scope.buttonConfirmChecked = false;

			$scope.richiesta = richiesta;
			//$scope.dateN = richiesta.datanascita;
			//console.log($scope.dateN);
			//$scope.richiesta.datanascita = richiesta.datanascita.getFullYear()+'-'+("0" + (richiesta.datanascita.getMonth() + 1)).slice(-2)+'-'+("0" + richiesta.datanascita.getDate()).slice(-2)
			//console.log($scope.richiesta.datanascita);

			$scope.confirm = function () {

				//devo disabilitare il pulsante 
				$scope.buttonConfirmChecked = true;

				console.log(JSON.stringify($scope.richiesta));
				//+'00:00:00.000Z'

				var toSend = $scope.richiesta;
				toSend.datanascita = moment($scope.richiesta.datanascita).format();

				$http({
                    url: baseUrl+'/rescue/codicecensimento',
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

                	alert(error);
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
    	/*
	    return $http.get(baseUrl+'/location', {
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
	    */
		return ["Badia Polesine","Bagnacavallo","Bari","Bassano Del Grappa","Bazzano","Bellaria Igea Marina","Benevento","Bentivoglio","Bentivoglio (bo)","Bentivoglio Bo","Bergamo","Bertinoro","Bielorussia","Bishop S Stortford (gb)","Bobbio","Bollate","Bologna","Bomporto","Bondeno","Borghi","Borgo Franco Sul Po","Borgo San Lorenzo","Borgonovo V.t.","Borgonovo Val Tidone","Bosco Mesola","Bouake - Costa D Avorio","Brasile","Brescello","Brescia","Brindisi","Broni","Bronte","Brunico","Busto Arsizio"];

	};
});

