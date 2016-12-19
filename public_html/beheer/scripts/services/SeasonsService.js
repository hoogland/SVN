/**
 * Created by Rob on 28-9-2015.
 */

'use strict';

angular
    .module('app')
    .factory('SeasonsService', function($resource, SETTINGS) {

        var API_PATH = SETTINGS.API_BASEURL + 'seasons';

        var Seasons = $resource(API_PATH);

        return {
            querySeasons: function() {
                return Seasons.query(function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            }
        }
    });