/**
 * Created by Rob on 28-9-2015.
 */


(function () {
    'use strict';


    angular
        .module('svnBeheer', [
            'ngResource',
            'ngSanitize',
            'ui.router',    // angular-router (https://github.com/angular-ui/ui-router)
            'ngMaterial',   // Angular material https://material.angularjs.org/latest/#/getting-started
            'ui.bootstrap', // https://angular-ui.github.io/bootstrap/#/getting_started
        ])

        .config(function ($urlRouterProvider, $stateProvider, $httpProvider) {
            $stateProvider

                //Internal competition
                .state('internal', {
                    url: '/internal',
                    templateUrl: 'views/internal.html'
                })

            $urlRouterProvider.otherwise('internal');
            // $httpProvider.interceptors.push('HttpInterceptorService');
        });
})();
