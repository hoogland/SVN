/**
 * Created by Rob on 28-9-2015.
 */


(function () {
    'use strict';


    angular
        .module('app', [
            'ngResource',
            'ngSanitize',
            'ui.router',    // angular-router (https://github.com/angular-ui/ui-router)
            'ngMaterial',   // Angular material https://material.angularjs.org/latest/#/getting-started
            'ui.bootstrap', // https://angular-ui.github.io/bootstrap/#/getting_started
            'ui.select',    // https://github.com/angular-ui/ui-select
        ])

        .config(function ($urlRouterProvider, $stateProvider, $httpProvider) {
            $stateProvider

                //Internal competition
                .state('internal', {
                    url: '/competitions',
                    templateUrl: 'views/internal.html'
                })
                //Overview competitions
                .state('internal.overview', {
                    url: '/overview',
                    templateUrl: 'partials/internal/competitions.tpl.html'
                })
                //Competition
                .state('internal.grouping', {
                    url: '/:competitionId/grouping',
                    templateUrl: 'partials/internal/grouping.tpl.html',
                    controller: 'CompetitionsCtrl'
                })
                //Round setup
                .state('internal.grouping.round',{
                    url: '^/competitions/:competitionId/rounds/:roundId',
                    templateUrl: 'partials/internal/round.tpl.html',
                    controller: 'MatchCtrl'
                })


                //Seasons
                .state('seasons', {
                    url: '/seasons',
                    templateUrl: 'views/seasons.html',
                    controller: 'seasonsCtrl'
                })
            $urlRouterProvider.otherwise('competitions/overview');
            // $httpProvider.interceptors.push('HttpInterceptorService');
        });
})();
