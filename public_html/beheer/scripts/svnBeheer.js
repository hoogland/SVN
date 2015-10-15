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
            'ui.sortable',
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
                //Participants
                .state('internal.participants', {
                    url: '/:competitionId/participants',
                    templateUrl: 'partials/internal/participants.tpl.html',
                    controller: 'CompetitionsCtrl'
                })
                //Competition settings
                .state('internal.settings', {
                    url: '/:competitionId/settings',
                    templateUrl: 'partials/internal/settings.tpl.html',
                    controller: 'CompetitionsCtrl'
                })
                .state('internal.settings.general', {
                    url: '/general',
                    templateUrl: 'partials/internal/settings.general.tpl.html'
                })
                .state('internal.settings.scores', {
                    url: '/scores',
                    templateUrl: 'partials/internal/settings.scores.tpl.html'
                })
                .state('internal.settings.ranking', {
                    url: '/ranking',
                    templateUrl: 'partials/internal/settings.ranking.tpl.html'
                })
                .state('internal.settings.display', {
                    url: '/display',
                    templateUrl: 'partials/internal/settings.display.tpl.html'
                })
                //Competition
                .state('internal.grouping', {
                    url: '/:competitionId/grouping',
                    templateUrl: 'partials/internal/grouping.tpl.html',
                    controller: 'CompetitionsCtrl'
                })
                //Round setup
                .state('internal.grouping.round', {
                    url: '^/competitions/:competitionId/rounds/:roundId',
                    views: {
                        'matches': {
                            templateUrl: 'partials/internal/round.tpl.html',
                            controller: 'MatchCtrl'
                        },
                        'byes': {
                            templateUrl: 'partials/internal/bye.tpl.html',
                            controller: 'ByeCtrl'
                        }
                    }
                })
                //Rounds
                .state('internal.rounds', {
                    url: '/:competitionId/rounds',
                    templateUrl: 'partials/internal/rounds.tpl.html',
                    controller: 'CompetitionsCtrl'
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
