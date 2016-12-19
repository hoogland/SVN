/**
 * Created by Rob on 28-9-2015.
 */

angular
    .module('app')
    .controller('ByeCtrl', function ($scope, ByeService, CompetitionService, MatchService) {
        //Get all byes of a round
        $scope.getRoundByes = function (roundId) {
            $scope.byes = ByeService.queryRoundByes(roundId);
        };

        //Creates a new bye
        $scope.createBye = function (newBye){
            ByeService.createBye($scope.roundSelect, newBye.player, newBye.bye).$promise.then(function(value){
                $scope.byes = value;
                //Save the standing
                CompetitionService.saveStanding($scope.competitionSelect.id, $scope.roundSelect.round).$promise.then(function(){
                });
                //Clear the selected players
                $scope.newBye.player = undefined;
                $scope.newBye.bye = undefined
            });
        };

        //Updates an existing bye
        $scope.updateBye = function (bye) {
            console.log(ByeService.updateBye(bye));
        };

        //Deletes an existing bye
        $scope.deleteBye = function (bye) {
            ByeService.deleteBye(bye).$promise.then(function(value){
                CompetitionService.saveStanding($scope.competitionSelect.id, $scope.roundSelect.round).$promise.then(function(){
                });

                //Remove the bye from dom
                $scope.byes.splice($scope.byes.indexOf(bye),1);
            });
        };

        //Add watcher to retrieve byes when changing round
        $scope.$watch('roundSelect', function (newValue) {
            if (newValue)
                $scope.getRoundByes(newValue.id);
        });

        //Mass add Byes
        $scope.addNonPlayingByes = function(byes, byeTypes, participants){
            var newBye = {};
            newBye.bye = byeTypes[0];
            for(var i = 0; i < participants.length; i++){
                var player = participants[i];
                var addBye = true;
                MatchService.matches.forEach(function(game){
                    if(game.speler_wit == player.id || game.speler_zwart == player.id)
                        addBye = false;
                });
                byes.forEach(function(bye){
                    if(bye.user_id == player.id)
                        addBye = false;
                });
                if(addBye){
                    newBye.player = player;
                    $scope.createBye(newBye);
                };
            };
        }
    });