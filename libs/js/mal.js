var app = angular.module('MAL',  ['ngResource','smart-table']);

app.config(['$httpProvider', function ($httpProvider) {
  $httpProvider.defaults.useXDomain = true;
  delete $httpProvider.defaults.headers.common['X-Requested-With'];
}]);

app.factory('datas', ['$resource', function ($resource) {
	return $resource("REST2/index.php/MAL", {}, {
		get: { method: 'GET', params: {}, isArray: false }
	});
}]);

app.controller('MALController', ['$scope','datas', function ($scope, datas)
{
  $scope.itemsByPage=5;
  function update()
  {
    datas.get({}, function (data) {
      $scope.collection = data.Manga;
      $scope.MALstatuses = data.MALstatuses;
      $scope.Statuses = data.Statuses;
    });
  }
  update();
}]);



/*$(document).ready(function(){
  $("#manga_or_anime").bootstrapSwitch();
  $('#manga_or_anime').on('switchChange.bootstrapSwitch', function(event, state)
    {
      if(state) loadTable("manga");
      else loadTable("anime");
  });
});

var loadTable = function loadTable(manga_or_anime)
{
  var url = 'rest/index.php/mal?manga_or_anime='+manga_or_anime;
  $('#mal_table').bootstrapTable('refresh', {url: url});
}*/
