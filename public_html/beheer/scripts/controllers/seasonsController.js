/**
 * Created by Rob on 28-9-2015.
 */

angular
    .module('app')
    .controller('seasonsCtrl', function seasonsCtrl(SeasonsService) {
        this.getSeasons = function () {
            this.seasons = SeasonsService.querySeasons();
        };
        this.getSeasons();
        console.log(this.seasons);
    });


function seasonsCtrl(SeasonsService) {
    this.getSeasons = function () {
        this.seasons = SeasonsService.querySeasons();
    };
    this.getSeasons();
    console.log(this.seasons);
};