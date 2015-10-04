/**
 * Created by Rob on 28-9-2015.
 */

'use strict';

angular
    .module('app')
    .factory('SeasonsService', function($resource) {

        var API_PATH = '/API/v1/index.php/seasons';

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