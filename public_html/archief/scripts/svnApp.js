/**
 * Created by Rob on 2-8-2015.
 */

(function () {
    'use strict';


    angular
        .module('app', [
            'ngResource',
            'ngSanitize',
            'ui.router',    // angular-router (https://github.com/angular-ui/ui-router)
        ])

        .config(function ($urlRouterProvider, $stateProvider, $httpProvider) {
            $stateProvider

                //Player router
                .state('spelers', {
                    url: '/spelers',
                    templateUrl: 'views/spelers.html'
                })
                .state('spelers.overzicht', {
                    url: '/spelers',
                    templateUrl: 'partials/spelers/overzicht.html'
                })
                .state('spelers.intern', {
                    url: '/intern',
                    templateUrl: 'partials/spelers/intern.html'
                })
                .state('spelers.extern', {
                    url: '/extern',
                    templateUrl: 'partials/spelers/extern.html'
                })

                //Internal competition
                .state('intern', {
                    url: '/intern',
                    templateUrl: 'views/intern.html',
                    controller: 'internalCompController'
                })
                .state('intern.competition', {
                    url: '/seizoen/:seasonSelected/competitie/:competitionSelected',
                    templateUrl: 'partials/internal/competition_view.html',
                    controller: 'internalCompController',
                    /*params:{
                        roundSelected: {value: null, squash:true}
                    }*/
                })
                .state('intern.competition.round', {
                    url: '/ronde/:roundSelected',
                    templateUrl: 'partials/internal/competition.html',
                    params:{
                        roundSelected: {value: null, squash:false}
                    }
                })
                .state('intern.competition.round.crosstable', {
                    url: '/kruistabel',
                    templateUrl: 'partials/internal/crosstable.html',
                    controller: 'roundController'
                })
                .state('intern.competition.round.standings', {
                    url: '/stand',
                    templateUrl: 'partials/internal/standings.html',
                    controller: 'roundController'
                })
                .state('intern.competition.round.matches', {
                    url: '/partijen',
                    templateUrl: 'partials/internal/matches.html',
                    controller: 'roundController'
                })

                //External competition
                .state('extern', {
                    url: '/extern',
                    templateUrl: 'views/extern.html',
                    controller: 'externalCompController'
                })
                .state('extern.season', {
                    url: '/seizoen/:seasonSelected',
                    templateUrl: 'partials/extern/season.html'
                })
                .state('extern.season.team', {
                    url: '/team/:teamSelected',
                    templateUrl: 'partials/extern/seasonTeam.html',
                    controller: 'externalCompController'
                })



                //History
                .state('historie', {
                    url: '/historie',
                    templateUrl: 'views/historie.html'
                })
                .state('historie.kampioenen', {
                    url: '/kampioenen',
                    templateUrl: 'partials/historie/kampioenen.html'
                })
                .state('historie.bestuur', {
                    url: '/bestuur',
                    templateUrl: 'partials/historie/bestuur.html'
                });

            $urlRouterProvider.otherwise('historie/kampioenen');
           // $httpProvider.interceptors.push('HttpInterceptorService');
        });
})();
