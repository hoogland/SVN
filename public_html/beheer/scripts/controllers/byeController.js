/**
 * Created by Rob on 28-9-2015.
 */

angular
    .module('app')
    .controller('ByeCtrl', function ($scope, ByeService) {
        //Get all byes of a round
        $scope.getRoundByes = function (roundId) {
            $scope.byes = ByeService.queryRoundByes(roundId);
        };

        //Creates a new bye
        $scope.createBye = function (newBye){
            $scope.byes = ByeService.createBye($scope.roundSelect, newBye.player, newBye.bye);

            //Clear the selected players
            $scope.newBye.player = undefined;
            $scope.newBye.bye = undefined
        };

        //Updates an existing bye
        $scope.updateBye = function (bye) {
            console.log(ByeService.updateBye(bye));
        };

        //Deletes an existing bye
        $scope.deleteBye = function (bye) {
            console.log(ByeService.deleteBye(bye));
            $scope.byes.splice($scope.byes.indexOf(bye),1);
        };

        //Add watcher to retrieve byes when changing round
        $scope.$watch('roundSelect', function (newValue) {
            if (newValue)
                $scope.getRoundByes(newValue.id);
        });


    });