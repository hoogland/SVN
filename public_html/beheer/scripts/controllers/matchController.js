/**
 * Created by Rob on 28-9-2015.
 */

angular
    .module('app')
    .controller('MatchCtrl', function ($scope, MatchService, CompetitionService) {
        //Get all matches of a round
        $scope.getRoundMatches = function (roundId) {
            $scope.matches = MatchService.queryRoundMatches(roundId);
        };

        //Creates a new match
        $scope.createMatch = function (newMatch){
            $scope.matches = MatchService.createMatch($scope.competitionSelect, $scope.roundSelect, newMatch.speler_wit, newMatch.speler_zwart);

            //Clear the selected players
            $scope.newMatch.speler_wit = undefined;
            $scope.newMatch.speler_zwart = undefined
        };

        //Updates an existing match
        $scope.updateMatch = function (match, update) {
            MatchService.updateMatch(match).$promise.then(function(){
                if(update){
                    CompetitionService.saveStanding(match.comp_id, match.ronde).$promise.then(function(){
                        console.log("competitie opgeslagen");
                    })};
            })
        };

        //Deletes an existing match
        $scope.deleteMatch = function (match) {
            console.log(MatchService.deleteMatch(match));
            $scope.matches.splice($scope.matches.indexOf(match),1);
            CompetitionService.saveStanding(match.comp_id, match.ronde).$promise.then(function(){
                console.log("competitie opgeslagen");
            })
        };


        console.log('MatchController');
        $scope.$watch('roundSelect', function (newValue) {
            if (newValue)
                $scope.getRoundMatches(newValue.id);
        });


    });