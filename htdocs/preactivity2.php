<?php require_once('Connections/connection.php'); ?>
<?php require_once('Connections/function.php'); ?>

<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><head>
<title>CDGFSS Activity System </title>
<link rel="stylesheet" type="text/css" href="style.css">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
<script type="text/javascript" src="/as/common/angular-drag-and-drop-lists.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.16/angular-route.min.js"></script>
<link rel="stylesheet" type="text/css" href="simple.css" /> <!-- Need to remove dependency on this -->
<!-- link rel="stylesheet" href="\dd\demo\framework\vendor\bootstrap.min.css" -->
<style type="text/css">
  .dndCol { 
    float: left;
    margin-left: 1em;
    margin-right: 1em;
    width: 32em;
  }
  
  .dndPanelHeading {
    color: #31708f;
    background-color: #d9edf7;
    border-color: #bce8f1;
    padding: 10px 15px;
    border-bottom: 1px solid transparent;
    border-top-right-radius: 3px;
    border-top-left-radius: 3px;
    box-sizing: border-box;
  }
  
  .dndPanel {
    border: 1px solid #bce8f1;
    margin-bottom: 20px;
    background-color: #fff;
    border-radius: 4px;
    -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05);
    box-shadow: 0 1px 1px rgba(0,0,0,.05);    
  }
  
  .dndPanelTitle {
    margin-top: 0;
    margin-bottom: 0;
    font-size: 16px;
    color: inherit;    
  }
  
  .dndPlaceHolder {
    display: block;
    background-color: #ddd;
    min-height: 37px !important;
    padding: 0 !important;
    margin: 0 !important;
  }
  
  .dndPanelBody {
    padding: 15px;
    height: 20em;
    overflow: scroll;
  }
  
</style>

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

  <div class="col-md-8">
    <div class="row">
      <div ng-repeat="(listName, Students) in models.lists" class="dndCol">
        <div class="dndPanel">
          <div class="dndPanelHeading">
            <h3 class="dndPanelTitle">{{listName}}</h3>
          </div>
          <!--div class="dndPanelBody" ng-include="'simple.html'"-->
          <div class="dndPanelBody">
            <!-- The dnd-list directive allows to drop elements into it.
                 The dropped data will be added to the referenced list -->
            <ul dnd-list="Students">
              <!-- The dnd-draggable directive makes an element draggable and will
                   transfer the object that was assigned to it. If an element was
                   dragged away, you have to remove it from the original list
                   yourself using the dnd-moved attribute -->
              <li ng-repeat="student in Students"
                  dnd-draggable="student"
                  dnd-moved="Students.splice($index, 1)"
                  dnd-effect-allowed="move"
                  dnd-selected="models.selected = student"
                  ng-class="{'selected': models.selected === student}"
                  >{{student.class}} {{student.class_num}} {{student.name}}
              </li>
            </ul>
          </div>
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
        lists: {"Students": [], "Participants": [], 
        "Test": [{
          name: "one",
          show: false,
        }]
        }
    };
    
    p({name: "Test1"});

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
