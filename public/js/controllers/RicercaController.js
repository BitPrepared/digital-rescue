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
		return ["Abbiategrasso","Acerra","Acireale","Acquaviva Delle Fonti Ba","Acqui Terme","Adjohoun","Adria","Affi (vr)","Agrigento","Agropoli","Alatri","Alba","Albenga","Albignasego","Alessandria","Alezio","Alghero","Ancona","Angera","Aosta","Arezzo","Ariano Polesine","Ascoli Piceno","Asola","Assisi","Asti","Atakpame Ogou Togo","Aversa","Bacau","Badia Polesine","Bagnacavallo","Bari","Bassano Del Grappa","Bazzano","Bellaria Igea Marina","Benevento","Bentivoglio","Bergamo","Bertinoro","Bielorussia","Bishop S Stortford","Blazowa","Bobbio","Bogota","Bollate","Bologna","Bomporto","Bondeno","Borghi","Borgo Franco Sul Po","Borgo San Lorenzo","Borgomanero","Borgonovo Val Tidone","Bosco Mesola","Bouake","Bozzolo","Brasile","Brescello","Brescia","Brindisi","Broni","Bronte","Brunico","Busseto","Busto Arsizio","Cadelbosco Sotto","Cagliari","Calestano","Caltanissetta","Camerino","Campagnola Emilia","Campi Salentina","Campobasso","Campobbasso","Canicatti","Capua","Carbonia","Carbonia-iglesias","Carmiano","Carpi","Carpi Mo","Casalecchio Di Reno","Casalfiumanese","Casalmaggiore","Casalpusterlengo","Casola Valsenio","Casorate Primo","Cassino","Castel S Giovann","Castel S Giovanni","Castel S Pietro","Castel S Pietro Terme","Castel San Giovanni","Castel San Martino","Castel San Pietro","Castel San Pietro Terme","Castel Vetro Piacentino","Castelfranco Emilia","Castellamare Di Stabia","Castellarano","Castelnovo Monti","Castelnuovo In Garfagnana","Castelnuovo Sotto","Castelsanpietro Terme","Castelsanpietroterme","Castelvetro Di Modena","Castiglione Dei Pepoli Bo","Catania","Cattolica","Cavezzo","Cecina","Cento","Cervia","Cesena","Cesenatico","Charleroi (belgio)","Chiaiano Na","Chianciano","Chiavari","Chiavenna","Chieti","Chioggia","Cina","Citta Di Castello Pg","Civitavecchia Roma","Civitella Di Romagna","Civitella Fc","Clusone","Codigoro","Codogno","Codogno Lo","Cogollo Del Cengio","Colombia","Comacchio","Como","Concordia","Concordia Ss","Contarina","Copparo","Coriano","Corigliano Calabro","Correggio","Correggio Re","Cortemaggiore","Cosenza","Cotignola","Cracovia","Crema","Cremona","Crevalcore","Crotone","Desenzano Del Garda","Desenzano Sul Garda","Duisburg Rheinhausen","Elovo","Eritrea","Faenza","Fano","Fara S Martino","Fermo","Ferrara","Ferrara Fe","Fidenza","Fidenza Pr","Fiesole","Finale Emilia","Fiorano Modenese","Fiorenzuola","Fiorenzuola (pc)","Fiorenzuola 26","Fiorenzuola D Arda","Fiorenzuola D Arda Pc","Fiorenzuola Darda","Firenze","Fivizzano","Foggia","Foiano Della Chiana","Foligno","Fontanellato","Fontevivo","Forl","Forli","Forli Cesena","Forlimpopoli","Formia","Formigine","Fornovo Di Taro","Francavilla Fontana","Francia","Fucecchio","Galatina","Galatina Le","Gallarate Va","Garbagnate Milanese","Garwolin Pl","Gazzaniga","Genova","Germania","Giulianova","Gravina","Grecia","Gualtieri","Guastalla","Guezin (benin)","Guidonia","Imola","India","Inghilterra","Jesi","Jolanda Di Savoia","Kerala","Kerala, Kochuveli","La Paz","La Spezia","Lamezia Terme","Lamporecchio (pt)","Lanciano Ch","Las Palmas De Grancanaria","Latina","Lecce","Lecco","Legnago","Legnano","Lentini (sr)","Licata","Ligonchio","Lizzano","Lochow","Lucerna","Lugo","Lugo Di Romagna","Manduria","Manfredonia","Mantova","Maranello","Marradi","Marsala","Martorano","Marzabotto","Massa Lombarda","Massalombarda","Meano","Medicina","Medole","Meldola","Merate","Mercato San Severino Sa","Mesola Fe","Messico","Messina","Mestre","Mezzogoro","Milano","Mirandola","Mirandola Mo","Misano Adriatico","Modena","Modigliana","Modugno","Molfetta","Molinella","Monchio Delle Corti","Monselice","Montagnana Pd","Monte Colombo","Montecchio","Montecchio Emila","Montecchio Emilia","Montecchio Emilia Re","Montecchio Maggiore","Montecchio Re","Montegridolfo","Montella","Monza","Morciano","Morciano Di Romagna","Mordano","Mugnano","Muttathara","Napoli","Nicastro","Novafeltria","Novafeltria (pu)","Novafeltria Ps","Novara","Novellara","Novi Di Modena","Nuoro","Olanda","Oristano","Osimo","Ostellato","Ostiglia","Padova","Palazzuolo","Palermo","Palmanova","Panni","Parma","Pau","Pavia","Pavullo","Pavullo Nel Frignano","Pergola Pu","Perugia","Pesaro","Pescara","Piacenza","Piagge Pu","Pianello Val Tidone","Piazza Armerina","Piedimonte Etneo","Pieve Di Cento","Pisa","Pistoia","Polonia","Ponte Dell Olio","Ponte Dell Olio Pc","Ponte Dellolio","Ponte Dellolio Pc","Pontedellolio","Pontedera","Portalbera","Portici","Portogruaro","Portomaggiore","Portomaggiore Fe","Poviglio","Predappio","Prignano","Puerto Cabello","Putignano","Quistello","R S M","Racca San Casciano","Ragusa","Rapallo","Ravenna","Reggio","Reggio Emilia","Reggio Nell Emilia","Rho","Riccione","Rimini","Riolo Terme","Ripi","Rivoli","Rocca San Casciano","Roma","Roncofreddo","Rovereto","Rovigo","Rsm","Russi","S Angelo Lodigiano","S Canzian D Isonzo","S Cesasario Sp","S Dona Di Piave","S Giovanni In Persiceto","S Ilario","S Jean De Maurienne","S Stefano Quisquina Ag","S. Maria Capuavetere","S.giovanni In Persiceto","S.marino Rsm","S.paolo Del Brasile","Saint-quentin","Salemi","Salerno","Saletta Di Mon","Salo","Salsomaggiore","Salsomaggiore Terme","Salvador Bhaia Brasile","San Benedetto Del Tronto","San Bonifacio","San Cesario Su Panaro","San Dona Di Piave","San Felice S P","San Felice Sul Panaro","San Giorgio Di Piano","San Giovanni In Persiceto","San Giovanni Persiceto","San Jose Venezuela","San Lorenzo","San Marco In Lamis","San Marco In Lamis Fg","San Marino","San Paolo Brasile","San Pietro In Casale","San Pietro Vernotico","San Secondo","San Secondo Parmense","San Severo Fg","San Vito Al Tagliamento","San Vito Chietino","Sannicandro Garganico","Sansepolcro","Santa Giustina In Colle","Santa Sofia","Santangelo In Vado","Santarcangelo","Santarcangelo Di Romagna","Santo Stefano D Aveto (ge","Saronno","Sassuolo","Sassuolo Mo","Savigliano","Savignano","Savignano Sul Panaro","Savignano Sul Rubicone","Scafati","Scandiano","Scutari Albania","Segrate","Senigallia","Seoul Rok","Siracusa","Sogliano Al Rubicone","Solarolo","Soliera","Spezzano","Spilimbergo","Spoleto","Sri Lanka","Stigliano","Sulmona","Susa","Suzzara","Svizzera","Taglio Di Po","Tamaseni","Taormina","Taranto","Taurisano","Teramo","Terlizzi","Termini Imerese","Termoli","Terranova Di Pollino","Torino","Torre Annunziata","Torre Del Greco","Trani","Tredozio","Trento","Treviglio","Treviso","Trier (ger)","Tripoli","Tula Ss","Urbino","Valdagno","Valona","Varese","Velletri","Venezia","Verghereto","Vernasca","Verona","Vetralla (vt)","Viadana","Viadana Mn","Vibo Valentia","Vicenza","Vignola","Villa Verucchio Rn","Villabate","Vimercate","Viterbo","Vittoria","Vittorio Veneto","Voghera","Yaounde (camerun)"];

	};
});

