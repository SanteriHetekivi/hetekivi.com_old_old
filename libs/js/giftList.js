var app = angular.module('giftList',  ['ngResource','smart-table']);

app.config(['$httpProvider', function ($httpProvider) {
  $httpProvider.defaults.useXDomain = true;
  delete $httpProvider.defaults.headers.common['X-Requested-With'];
}]);

app.factory('datas', ['$resource', function ($resource) {
	return $resource("REST2/index.php/giftList", {}, {
		get: { method: 'GET', params: {}, isArray: false }
	});
}]);

app.controller('giftListController', ['$scope','datas', function ($scope, datas)
{
  $scope.itemsByPage=5;
  function update()
  {
    datas.get({}, function (data) {
      $scope.collection = data.Gift;
      $scope.GiftTypes = data.GiftTypes;
    });
  }
  update();

}]);

/*(function() {

  app.controller("GiftListController", GiftListController);
  GiftListController.$inject = ["NgTableParams", "$resource"];

  function GiftListController(NgTableParams, $resource) {
    // tip: to debug, open chrome dev tools and uncomment the following line
    //debugger;

    var Api = $resource('REST2/index.php/giftList');
    this.tableParams = new NgTableParams({
      page: 1, // show first page
      count: 5 // count per page
    }, {
      filterDelay: 300,
      getData: function(params) {
        // ajax request to api
        return Api.get(params.url()).$promise.then(function(data) {
          params.total(data.inlineCount);
          return data.Gift;
        });
      }
    });
  }
})();
/*var app = angular.module('GiftList', ["ngTable"]);
app.controller('GiftListController','ngTableParams', function($scope, $http, ngTableParams) {


    $scope.data = [];
    $scope.giftList = new ngTableParams(
      {
        page: 1,            // show first page
        count: 5           // count per page
      }, {
        total: $scope.data.length, // length of data
        getData: function ($defer, params) {
            params.total($scope.data.length);
            $defer.resolve(data.slice((params.page() - 1) * params.count(), params.page() * params.count()));
        }
      }
    );
    $scope.updateTable = function()
    {
      $http.get("REST2/index.php/giftList")
      .success(function (response) {
        $scope.data = response.Gift;
        $scope.giftList.reload();
        $scope.ok();
      });
    };
    $scope.edit = function(id)
    {
      $("#"+object+"Edit").modal("show");
    }
    $scope.remove = function(id)
    {
      var data = {object:"UserLink", id:id};
      $http.post("REST2/index.php/REMOVEObject", data)
      .success(function (response) {
        $scope.get;
      });
    };
    $scope.updateTable();

});*/
function operateFormatter(value, row, index)
{
  return [
      '<button id="edit" type="button" class="btn btn-warning"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></br>',
      '<button id="remove" type="button" class="btn btn-danger"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button>'
  ].join('');
}
window.operateEvents =
{
    'click #edit': function (e, value, row, index) {
      EDIT("gift",row,value);
    },
    'click #remove': function (e, value, row, index) {
      REMOVE(value);
    }
};

var loadTable = function loadTable()
{
  var $table = $('#giftlist_table');
  $table.bootstrapTable('refresh', {url: 'rest/index.php/giftList'});
}
