/**
 * Created by Rob on 2-8-2015.
 */

(function(){
    'use strict'

    angular.module('app')
        .controller('internalCompController', internalCompController);

    internalCompController.$inject = ['$scope', '$stateParams', '$state', '$rootScope', 'svnCompetitionService', 'svnGeneralDataService'];

    function internalCompController($scope, $stateParams, $state, $rootScope, svnCompetitionService, svnGeneralDataService) {
        var vm = this;

        if($scope.compColumns === undefined)
            getCompColumns();
        

        /**
         * Season
         */
        $rootScope.seasonSelected = $stateParams.seasonSelected;
        $scope.changeSeason = function (season) {
            //Change available competitions
            svnCompetitionService.query(season).success(function(data, status, headers, config) {
                $scope.competitions = data;
                $rootScope.seasonSelected = season;

            });
        }

        /**
         * Competition
         */
        $rootScope.competitionSelected = $stateParams.competitionSelected;
        $scope.changeComp = function (competition) {
                $rootScope.competitionSelected = competition;
                $rootScope.roundSelected = null
                $state.go('intern.competition.round.standings', { seasonSelected: $rootScope.seasonSelected, competitionSelected : $rootScope.competitionSelected, roundSelected: "" });
        }

        /**
         * Rounds
         */
        $rootScope.roundSelected = $stateParams.roundSelected;


        // Get competition data when competition changes
        $rootScope.$watch('competitionSelected', function()
        {
            getRounds($rootScope.competitionSelected);
            getGeneralData($rootScope.competitionSelected);
            /*getStandings($rootScope.competitionSelected, null);*/
        });

        // Get competition data when rounds changes

        function getRounds(competitionId){
            svnCompetitionService.rounds(competitionId).success(function(data, status, headers, config) {
                $scope.rounds = data;
              /*  if($rootScope.roundSelected !== null) {
                    $scope.roundInfo = $filter('roundInfo')($scope.rounds.data, {ronde: $rootScope.roundSelected});
                }*/
            });
        }
        function getGeneralData(competitionId, round){
            svnCompetitionService.generalData(competitionId).success(function(data, status, headers, config) {
                $scope.competition = data;
            });
        }
        function getCompColumns(){
            console.log("compColumns");
            svnGeneralDataService.compColumns('Keizer').success(function(data, status, headers, config) {
                $scope.compColumns = data;
            });
        }
    }

})();