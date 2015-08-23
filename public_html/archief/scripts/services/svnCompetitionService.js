/**
 * Created by Rob on 4-8-2015.
 */
(function () {
    'use strict';

    angular.module('app')
        .factory("svnCompetitionService", ['$http', function ($http) {
            var service = {};

            service.query = function(season){
                return $http.post('../archief/api/webservice.php',
                    {
                        "method": "GET",
                        "action": "data",
                        "subaction": "competitions",
                        "data": {
                            "season": season
                        }
                    })
            };


            service.generalData = function(competitionId){
                return $http.post('../archief/api/webservice.php',
                    {
                        "method": "GET",
                        "action": "intern",
                        "subaction": "generalData",
                        "data": {
                            "competition": competitionId
                        }
                    })
            };

            service.rounds = function(competitionId){
                return $http.post('../archief/api/webservice.php',
                    {
                        "method": "GET",
                        "action": "intern",
                        "subaction": "rounds",
                        "data": {
                            "competition": competitionId
                        }
                    })
            };

            service.standing = function(competitionId, round){
                if(round !== null)
                    round = [0,round];
                return $http.post('../archief/api/webservice.php',
                    {
                        "method": "GET",
                        "action": "intern",
                        "subaction": "standing",
                        "data": {
                            "competition": competitionId,
                            "round": round
                        }
                    })
            };
            service.matches = function(competitionId, round){
                return $http.post('../archief/api/webservice.php',
                    {
                        "method": "GET",
                        "action": "intern",
                        "subaction": "matches",
                        "data": {
                            "competition": competitionId,
                            "round": round
                        }
                    })
            };

            return service;
        }]);
})();