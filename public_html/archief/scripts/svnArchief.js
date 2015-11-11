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
                    url: '/intern',
                    templateUrl: 'views/internal.html',
                    controller: 'mainController'
                })
                //Overview seasons
                .state('internal.seasons', {
                    url: '/seizoen',
                    templateUrl: 'partials/internal/seasons.tpl.html',
                    controller: 'stateCtrl'
                })
                //Overview competitions in season
                .state('internal.season', {
                    url: '/seizoen/{seasonId:int}',
                    templateUrl: 'partials/internal/competitions.tpl.html',
                    controller: 'stateCtrl'
                })
                //Competition
                .state('internal.competition', {
                    url: '/seizoen/{seasonId:int}/competitie/{competitionId:int}',
                    templateUrl: 'partials/internal/competition.tpl.html',
                    controller: 'CompetitionsCtrl',
                })
                //Round
                .state('internal.competition.round', {
                    abstract: true,
                    url: '/ronde/:roundId',
                    controller: 'CompetitionsCtrl',
                    template: '<ui-view/>',
                })

                //Standing
                .state('internal.competition.round.standing', {
                    url: '/stand',
                    templateUrl: 'partials/internal/competition.standing.tpl.html',
                  //  controller: 'CompetitionsCtrl',
                })
                //Games
                .state('internal.competition.round.games', {
                    url: '/partijen',
                    templateUrl: 'partials/internal/competition.games.tpl.html',
                   // controller: 'CompetitionsCtrl',
                })
                //Crosstable
                .state('internal.competition.round.crosstable', {
                    url: '/kruistabel',
                    templateUrl: 'partials/internal/competition.crosstable.tpl.html',
                    //controller: 'CompetitionsCtrl',
                })
                //Player competition details
                .state('internal.competition.player', {
                    url: '/speler/:playerId',
                    templateUrl: 'partials/internal/competition.player.tpl.html',
                    controller: 'stateCtrl',
                })
            $urlRouterProvider.otherwise('intern/seizoen');
            // $httpProvider.interceptors.push('HttpInterceptorService');
        });
})();
