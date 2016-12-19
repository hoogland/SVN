/**
 * Created by Rob on 28-9-2015.
 * Simple controller to save new state values
 */

angular
    .module('app')
    .controller('stateCtrl', function ($scope, $state) {

        $scope.setState($state.params);
    });