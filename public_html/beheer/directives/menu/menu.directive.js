/**
 * Created by Rob on 28-9-2015.
 */


(function () {
    'use strict';

    angular
        .module('app')
        .directive('menu', menu);

    menu.$inject = [];

    function menu(){
        return{
            templateUrl : 'directives/menu/menu.tpl.html',
            controller : 'menuController'
        };
    }

    angular.module('app')
        .controller('menuController', menuController);

    menuController.$inject = ['$scope'];

    function menuController($scope){

    };
})();
