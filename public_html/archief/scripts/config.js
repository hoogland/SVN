/**
 * Created by Rob on 7-8-2015.
 */

(function() {
    'use strict'

    angular.module('app')
            .constant('CONFIG', {
            'vereniging': "SV Nieuwerkerk",
            'verenigingsNummer': "1428",

            'baseUrl': 'http://svn.local/',
            'archive': 'http://svn.local/archief',

            'standardCompetitionSeason': 41,
            'standardCompetition': 32,
            'standardCompetitionExternal': 1,
            'standardCompetitionExternalSeason': 41,
            'teamName': "SVN",

            'google_analytics_id': "UA-60217582-1"
        });
});


