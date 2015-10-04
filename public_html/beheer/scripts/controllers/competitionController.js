/**
 * Created by Rob on 28-9-2015.
 */

angular
    .module('app')
    .controller('CompetitionsCtrl', function($scope, CompetitionService, RoundService){
        $scope.getRounds = function(competitionId){
            $scope.rounds = RoundService.queryRounds(competitionId);
        };

        $scope.queryParticipants = function(competitionId){
            $scope.participants = CompetitionService.queryParticipants(competitionId);
        }

        $scope.$watch('competitionSelect', function(newValue){
            console.log('nieuwe competitie');
            console.log(newValue);
            if(newValue) {
                $scope.getRounds(newValue.id);
                $scope.queryParticipants(newValue.id);
            }
        });

    });