/**
 * Created by Rob on 2-8-2015.
 */
(function () {
    'use strict';

    angular.module('app')
        .factory("svnTeamsService", ['$http', function ($http) {
            var service = {};

            service.query = function () {
                return  $http.post('../archief/api/webservice.php',
                    {
                        "method" : "GET",
                        "action" : "data",
                        "subaction" : "teams"
                    })
            };
            return service;
        }]);
})();