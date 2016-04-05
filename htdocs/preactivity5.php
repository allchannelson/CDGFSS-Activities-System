<?php require_once('Connections/connection.php'); ?>
<?php require_once('Connections/function.php'); ?>

<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><head>
<title>CDGFSS Activity System </title>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
<script type="text/javascript" src="/as/common/angular-drag-and-drop-lists.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.16/angular-route.min.js"></script>
<link rel="stylesheet" href="\dd\demo\framework\vendor\bootstrap.min.css"> <!-- Keeping dependency -->
<link rel="stylesheet" type="text/css" href="simple.css" /> <!-- May need to remove dependency on this -->
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<header><h1>Carmel Divine Grace Foundation Secondary School<p>
	Application Form for All Types of Activities/Competitions</h1></header>
<nav>
	<li><a href ='./login.php'>Login</a></li>
</nav>
<section>
	<form method="POST" action="catcode_edit.php" onkeydown="if(event.keycode==13) return false;">
	<table>
		<tr>
			<td>Teacher(s) in Charge:</td> 
			<td><input name="teacher-in-charge" id="teacher-in-charge" type="text" size = "40"></td>
			<td>Particpating Unit:</td>
			<td><input name="participating_unit" id="participating_unit" type="text" size= "40"></td>
		</tr>
		<tr>
			<td>Date:</td><td>From
				<input name="start_date" id="start_date" type="date" size="30" value = "<?php echo date('Y-m-d'); ?>"> to
				<input name="end_date" id="end_date" type="date" size="30" value = "<?php echo date('Y-m-d'); ?>">
			</td>
			<td>Time: </td><td>From
				<input name="start_time" id="start_time" type="time" step = '0' size="30" value = "<?php echo time(); ?>"> to
				<input name="end_time" id="end_time" type="time" step = '0' size="30" value = "<?php echo time(); ?>">
			</td>
		</tr>
		<tr>
			<td rowspan = '2' width = '30'>Name of Activity/ Competition:</td>
			<td colspan = '3'>(ENG)<input name="activity_eng_name" id="activity_eng_name"  type="text" size = "120"></td>
			<tr><td colspan = '3'>(CHI)<input name="activity_chi_name" id="activity_chi_name" type="text" size = "120"></td>
		</tr>
		<tr>
			<td rowspan = '2' width = '30'>Partner Organization (if any):</td>
			<td colspan = '3'> (ENG) <input name="partner_org_eng" id="partner_org_eng" type="text" size = "120"></td>
			<tr><td colspan = '3'>(CHI)<input name="partner_org_chi" id="partner_org_chi" type="text" size = "120"></td>
		</tr>	
		<tr>
			<td>Destination/Route (if any):</td> 
			<td colspan = '3'><input name="destination" id="destination" type="text" size = "120"></td>
		</tr>
	</table>
	<br>
</section>

<div class="simpleDemo row" ng-app="myApp" ng-controller="SimpleDemoController">

  <script type="text/ng-template" id="list.html">
    <ul dnd-list="list">
      <li ng-repeat="item in list"
          dnd-draggable="item"
          dnd-effect-allowed="move"
          dnd-moved="list.splice($index, 1)"
          dnd-selected="models.selected = item"
          ng-class="{selected: models.selected === item}"
          ng-include="item.type + '.html'">
      </li>
    </ul>
  </script>

  <!-- This template is responsible for rendering a container element. It uses
       the above list template to render each container column -->
  <script type="text/ng-template" id="class.html">
    <div class="container-element box box-blue">
      <h3>Class {{item.name}}</h3>
      <!--div class="column" ng-repeat="list in item.students" ng-include="'list.html'"></div-->
      <div ng-repeat="students in item.students">
        <ul dnd-list="students">
          <li ng-repeat="student in students"
              dnd-draggable="student"
              dnd-effect-allowed="move"
              dnd-moved="students.splice($index, 1)"
              dnd-selected="models.selected = student"
              ng-class="{selected: models.selected === student}"
              ng-include="student.html">
          </li>
        </ul>
      </div>
      <div class="clearfix"></div>
    </div>
  </script>

  <!-- Template for a normal list item -->
  <script type="text/ng-template" id="student.html">
    <div class="item">{{student.name}}</div>
  </script>

  <div class="col-md-8">
    <div class="row">
      <div ng-repeat="(listName, list) in models.lists" class="col-md-6 ng-scope">
        <div class="dropzone box box-blue">
            <h3>{{listName}}</h3>
            <div ng-include="'list.html'"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4" style="display: initial;">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="dndPanelTitle">Generated Model</h3>
      </div>
      <div class="dndPanelBody">
        <pre class="code">{{modelAsJson}}</pre>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
angular.module('myApp', ["dndLists"]);
angular.module("myApp").controller("SimpleDemoController", function($scope) {

  $scope.models = {
    selected: null,
    lists: {
      "Students": [], 
      "Tests": [{
      type: "class",
      name: "S1A",
      //students: [{type: "student", name: "Mr. Chan", class: "S1A"},{type: "student", name: "Mr. Bunny", class: "S1A"}]
      students: [{type: "student", name: "Mr. Chan"}]
    }]
    }
  };
  
  // p({name: "Test1"});

  // studentdnd.php include //
  <?php require_once('studentdnd.php'); ?>
  // studentdnd.php include END //
 
  function p(input1) {
    // this function was named p to reduce footprint
    // this function is called for every single record
    // $scope.models.lists.Students.push({label: input1});
    $scope.models.lists.Students.push(input1);
  }

  $scope.expand = function(input) {
    input.show = !input.show;
  }
  
  $scope.$watch('models', function(model) {
    $scope.modelAsJson = angular.toJson(model, true);
  }, true);
});
</script>
</body>
</html>
