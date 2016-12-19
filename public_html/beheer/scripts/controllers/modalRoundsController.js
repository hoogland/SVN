/**
 * Created by Rob on 6-10-2015.
 */
angular.module('app').controller('modalRoundsCtrl', function ($scope, $modalInstance, RoundService) {
    $scope.roundDate = new Date();

    $scope.ok = function () {
        $modalInstance.close();
    };

    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
    $scope.open = function($event) {
        $scope.status.opened = true;
    };


});