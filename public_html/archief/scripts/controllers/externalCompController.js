/**
 * Created by Rob on 2-8-2015.
 */

(function(){
    'use strict'

    angular.module('app')
        .controller('externalCompController', externalCompController);

    externalCompController.$inject = ['$scope', '$stateParams', '$state', '$rootScope', 'svnTeamMatchesService', 'svnTeamsService', 'svnPlayerService'];

    function externalCompController($scope, $stateParams, $state, $rootScope, svnTeamMatchesService, svnTeamsService) {
        var vm = this;


        /**
         * Seasons
         */
        $rootScope.seasonSelected = $stateParams.seasonSelected;

        $scope.changeSeason = function (season) {
            if($rootScope.teamSelected)
                $state.go('extern.season.team', { seasonSelected: season });
            else
                $state.go('extern.season', { seasonSelected: season });
        }

        /**
         * Teams
         */
        $rootScope.teamSelected = $stateParams.teamSelected;

        /**
         * Matchdata
         */
        svnTeamMatchesService.topscorers($scope.seasonSelected, $scope.teamSelected).success(function(data, status, headers, config) {
            $scope.topscorers = data;
        });
        svnTeamMatchesService.query($scope.seasonSelected, $scope.teamSelected).success(function(data, status, headers, config) {
            $scope.matches = data;
        });


    }
})();