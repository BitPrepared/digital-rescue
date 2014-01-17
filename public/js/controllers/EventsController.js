'use strict';

rescueApp.controller('EventsController', function($scope, $idle, $modal) {
            
            $scope.started = false; //QUI TRACCIO L'AUTENTICAZIONE E QUELLA DA FAR SCADERE

            function closeModals() {
                if ($scope.warning) {
                    $scope.warning.close();
                    $scope.warning = null;
                }
                if ($scope.timedout) {
                    $scope.timedout.close();
                    $scope.timedout = null;
                }
            }

            $scope.events = [];

            $scope.$on('$idleStart', function() {
                // the user appears to have gone idle
                closeModals();
                $scope.warning = $modal.open({
                    templateUrl: 'warning-dialog.html',
                    windowClass: 'modal-warning'
                });              
            });

            $scope.$on('$idleWarn', function(e, countdown) {
                // follows after the $idleStart event, but includes a countdown until the user is considered timed out
                // the countdown arg is the number of seconds remaining until then.
                // you can change the title or display a warning dialog from here.
                // you can let them resume their session by calling $idle.watch()
            });

            $scope.$on('$idleTimeout', function() {
                // the user has timed out (meaning idleDuration + warningDuration has passed without any activity)
                // this is where you'd log them
                closeModals();
                $scope.timedout = $modal.open({
                    templateUrl: 'timedout-dialog.html',
                    windowClass: 'modal-danger'
                });
            })

            $scope.$on('$idleEnd', function() {
                // the user has come back from AFK and is doing stuff. if you are warning them, you can use this to hide the dialog 
                closeModals();
            });

            $scope.$on('$keepalive', function() {
                // do something to keep the user's session alive
            })
            
            
            $scope.start = function() {
                closeModals();
                $idle.watch();
                $scope.started = true;
            };
            
            $scope.stop = function() {
                closeModals();
                $idle.unwatch();
                $scope.started = false;
            };
            

        })