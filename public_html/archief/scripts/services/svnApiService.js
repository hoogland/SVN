/**
 * Created by Rob on 2-8-2015.
 */


(function(){
    'use strict';

    angular.module('app')
        .factory('svnApiService', svnApiService);

    svnApiService.$inject = ['svnSeasonsService', 'svnTeamsService'];

    /**
     *
     * @param svnSeasonsService
     * @returns {{Seasons: *}}
     */
    function svnApiService(svnSeasonsService, svnTeamsService){
        var service;

        service = {
            Seasons: svnSeasonsService,
            Teams: svnTeamsService
        };

        return service;
    }

})();