/**
 * Created by Rob on 28-9-2015.
 */

'use strict';

angular
    .module('app')
    .factory('PlayerService', function($resource) {

        var API_PATH = '/API/v1/index.php/players/:player';

        var Round = $resource(API_PATH, null,            {
            save:{
                method: 'PUT'
            },
        });

        return {
            queryRounds: function( competition) {
                return Round.query({competition : competition}, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },
        }
    });