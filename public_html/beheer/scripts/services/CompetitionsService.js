/**
 * Created by Rob on 28-9-2015.
 */

'use strict';

angular
    .module('app')
    .factory('CompetitionService', function($resource) {

        var API_PATH = '/API/v1/index.php/seasons/:season/competitions';

        var Competition = $resource(API_PATH, null,            {
            query: {
                url: '/API/v1/index.php/seasons/:season/competitions',
                isArray: true
            },
            participants: {
                url: '/API/v1/index.php/competitions/:competition/participants',
                isArray: true
            }
        });

        return {
            queryCompetitions: function(season) {
                return Competition.query({season : season.season}, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },
            queryParticipants: function(competition) {
                return Competition.participants({competition : competition}, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            }
        }
    });