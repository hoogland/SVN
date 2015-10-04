/**
 * Created by Rob on 28-9-2015.
 */

angular
    .module('app')
    .controller('seasonsCtrl', function($scope, SeasonsService){
       $scope.getSeasons = function(){
           $scope.seasons = SeasonsService.querySeasons();
       };

        //$scope.getSeasons();

    });