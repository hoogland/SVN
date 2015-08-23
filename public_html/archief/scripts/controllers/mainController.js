(function(){
    'use strict'

    angular.module('app')
        .controller('mainController', mainController);

    mainController.$inject = ['$scope', '$stateParams', '$state', '$rootScope',  'svnSeasonsService', 'svnTeamsService','svnPlayerService'];

    function mainController($scope, $stateParams, $state, $rootScope, svnSeasonsService, svnTeamsService, svnPlayerService) {
        var vm = this;


        /**
         * Seasons
         */
        $rootScope.seasonSelected = $stateParams.seasonSelected;

        svnSeasonsService.query().success(function(data, status, headers, config) {
            $scope.seasons = data;
        })



        /**
         * Teams
         */
        $rootScope.teamSelected = $stateParams.teamSelected;

        svnTeamsService.query().success(function(data, status, headers, config) {
            $scope.teams = data;
        })

        $scope.changeTeam = function (team) {
            $state.go('extern.season.team', { teamSelected: team });
        }

        /**
         * Players
         */
        svnPlayerService.query().success(function(data, status, headers, config) {
            $scope.players = data;
        });



    }
})();