/**
 * Created by Rob on 28-9-2015.
 */

angular
    .module('app')
    .controller('CompetitionsCtrl', function ($scope, $state, $stateParams, $filter, $modal, CompetitionService, RoundService, MatchService) {
        $scope.roundSelect = {};

        $scope.getRounds = function (competitionId) {
            RoundService.queryRounds(competitionId).$promise.then(function (value) {
                $scope.rounds = value;
                if ($state.params.roundId)
                    $scope.roundSelect = value[$state.params.roundId - 1];
                else
                    $state.go($state.current.name, {roundId: value[value.length - 1].round});
            });
        };

        $scope.getStanding = function (competitionId, round) {
            CompetitionService.getStanding(competitionId, round).$promise.then(function (value) {
                $scope.standing = value;
            });
        };
        //Get all matches of a round
        $scope.getRoundMatches = function (competitionId, round) {
            $scope.matches = MatchService.queryCompRoundMatches(competitionId, round);
        };
        //Get all matches of a player
        $scope.getPlayerMatches = function (competitionId, playerId) {
            $scope.matches = MatchService.getPlayerMatches(competitionId, playerId);
        };
        //Get all matches of a player
        $scope.getPlayerByes = function (competitionId, playerId) {
            $scope.byes = MatchService.getPlayerByes(competitionId, playerId);
        };

        $scope.queryOptions = function (competitionId) {
            if (competitionId) {
                CompetitionService.queryOptions(competitionId)
                    .$promise.then(function (value) {
                        $scope.competitionOption = value;
                        $scope.getColumns($scope.competitionOption.System.value);
                    });
            }

        };

        $scope.queryParticipants = function (competitionId) {
            $scope.participants = {};
            $scope.participants = CompetitionService.queryParticipants(competitionId).$promise.then(function (value) {
                value.forEach(function (participant) {
                    $scope.participants[participant.speler_id] = participant;
                });
            });
        };

        $scope.setState($state.params);
        $scope.$watch('state.competitionId', function (newValue) {
            if (newValue) {
                // $stateParams.competitionId = newValue.id;
                $scope.getRounds(newValue);
                $scope.queryParticipants(newValue);
                $scope.queryOptions(newValue);

                //Set CompetitionSelect
                $scope.$parent.competitionSelect = $filter('filter')($scope.competitions, {id: parseInt(newValue)})[0];
            }
        });

        $scope.standingFilter = function(row){
            if(row.Games > 0 || row.KeizerTotaal != null)
                return true;
            return false;
        };
    });