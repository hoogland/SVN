/**
 * Created by Rob on 2-8-2015.
 */
(function () {
    'use strict';

    angular.module('app')
        .factory("svnGeneralDataService", ['$http', function ($http) {
            var service = {};

            service.compColumns = function (compType) {
                return  $http.post('../archief/api/webservice.php',
                    {
                        "method" : "GET",
                        "action" : "data",
                        "subaction" : "competitionColumns",
                        "data": {
                            "compType": compType
                        }
                    })
            };
            return service;
        }]);
})();