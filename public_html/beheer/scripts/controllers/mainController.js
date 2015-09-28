/**
 * Created by Rob on 28-9-2015.
 */
(function(){
    'use strict'

    angular.module('svnBeheer')
        .controller('mainController', mainController);

    mainController.$inject = ['$scope', '$stateParams', '$mdSidenav', '$state', '$rootScope'];

    function mainController($scope, $stateParams, $mdSidenav, $state, $rootScope) {
        var vm = this;


        $scope.toggleSidenav = function(menuId) {
            $mdSidenav(menuId).toggle();
        };


    }
})();