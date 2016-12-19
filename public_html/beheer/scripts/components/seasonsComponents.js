/**
 * Created by Rob on 27-8-2016.
 */

angular.module('app').controller('MainCtrl', function MainCtrl() {
    this.hero = {
        name: 'Spawn'
    };
});
function seasonListController() {
    var vm = this;
    console.log(vm);
    vm.seasons = vm.obj;
}

angular.module('app').component('seasonList', {
    bindings: {
        seasons: "="
    },
   /* controller: function (SeasonsService) {
        this.getSeasons = function () {
            this.seasons = SeasonsService.querySeasons();
        };
        this.getSeasons();
        console.log(this.seasons);
    },*/
    controller: seasonListController,
    template: "<div ng-repeat='season in $ctrl.seasons'>{{season.naam}}</div>"

});
