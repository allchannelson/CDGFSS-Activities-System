angular.module('myApp', ["ngRoute", "dndLists"]);
angular.module("myApp").controller("SimpleDemoController", function($scope) {

    $scope.models = {
        selected: null,
        lists: {"A": [], "B": []}
    };

    // Generate initial model
    for (var i = 1; i <= 3; ++i) {
        //$scope.models.lists.A.push({label: "Item A" + i, test: "rah" + i});
        pushA("Item A" + i, "rah" + i);
        // $scope.models.lists.B.push({label: "Item B" + i, test: "boo" + i});
    }
    
    pushA("test");
    
    // Model to JSON for demo purpose
    /*
    $scope.$watch('models', function(model) {
        $scope.modelAsJson = angular.toJson(model, true);
    }, true);
    */
    
    function pushA(input1, input2) {
      $scope.models.lists.A.push({label: input1, test: input2});
    }

});
