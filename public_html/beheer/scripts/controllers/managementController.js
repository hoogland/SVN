/**
 * Created by Rob on 28-9-2015.
 */

angular
    .module('app')
    .controller('managementCtrl', function ($scope, CompetitionService, RoundService) {

        $scope.recalcSeasons = function () {
            recalculateGetCompetitions(1);
        };


        function recalculateGetCompetitions(season) {
            console.log("Seizoen: " + $scope.seasons[season].id);

            CompetitionService.queryCompetitions({season: $scope.seasons[season].id}).$promise.then(function (value) {
                var competitions = value;

                var result = compRounds(competitions, 0, $scope.seasons[season].id);

                season++;
                if (season < $scope.seasons.length) {
                    recalculateGetCompetitions(season);
                }
                else {
                    console.log("ALLES OPGESLAGEN");
                }

            })

        }

        function compRounds(competitions, competition, season) {
            if (competitions.length == 0)
                return true;
            console.log("S" + season + " - C" + competitions[competition].id);
            RoundService.queryRounds(competitions[competition].id).$promise.then(function (rounds) {
                var result = recalculateStandings(competitions[competition].id, rounds, 1);

                competition++;
                if (competition < competitions.length) {
                    compRounds(competitions, competition, season);
                }
                else {
                    console.log("seizoen opgeslagen");
                    return true;
                }
            })
        }

        //Actual function to recursively calculate all the scores
        function recalculateStandings(competitionId, rounds, round) {
            console.log(round);
            CompetitionService.saveStanding(competitionId, rounds[round - 1].round).$promise.then(function () {
                if (round < rounds.length) {
                    round++;
                    recalculateStandings(competitionId, rounds, round);
                }
                else {
                    console.log("competitie opgeslagen");
                    return true;
                }
            });

        }
    });