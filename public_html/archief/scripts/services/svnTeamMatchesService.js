/**
 * Created by Rob on 2-8-2015.
 */
(function () {
    'use strict';

    angular.module('app')
        .factory("svnTeamMatchesService", ['$http', function ($http) {
            var service = {};

            service.query = function(season, team){
                return $http.post('../archief/api/webservice.php',
                    {
                        "method": "GET",
                        "action": "extern",
                        "subaction": "matches",
                        "data": {
                            "season": season,
                            "team": team,
                            "details": true
                        }
                    })
            };

            service.topscorers = function(season, team){
              //  if (season && team) {
                    return $http.post('../archief/api/webservice.php',
                        {
                            "method": "GET",
                            "action": "extern",
                            "subaction": "topScorers",
                            "data": {
                                "season": season,
                                "team": team
                            }
                        })
              //  }
            };

            return service;
        }]);
})();