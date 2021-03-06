/**
 * Created by Rob on 28-9-2015.
 */

'use strict';

angular
    .module('app')
    .factory('RoundService', function($resource, SETTINGS) {

        var API_PATH = SETTINGS.API_BASEURL + 'competitions/:competition/rounds';

        var Round = $resource(API_PATH);

        return {
            queryRounds: function( competition) {
                return Round.query({competition : competition}, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            }
        }
    });