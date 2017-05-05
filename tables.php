<?php
require_once("mysql.php");
db_connect();

// Read POST information
$postTable = $_GET['table'];

// Variables
$arrayModifiableTables = array("Skill_T", "Vendor_T", "Customer_T", "Employee_T");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="">

		<title>Tables - PVFC</title>

		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">

        <!-- cdn for modernizr, if you haven't included it already -->
        <script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
        <!-- polyfiller file to detect and load polyfills -->
        <script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
        <script>
          webshims.setOptions('waitReady', false);
          webshims.setOptions('forms-ext', {types: 'date'});
          webshims.polyfill('forms forms-ext');
        </script>

        </head>

	<body>
		<!-- Top of Page Navigation -->
		<nav class="navbar navbar-toggleable-md navbar-light bg-faded">
		  <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		  </button>
		  <a class="navbar-brand" href="http://josegperez.myportfolio.com">Jose Perez</a>

		  <div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav mr-auto">
			  <li class="nav-item">
				<a class="nav-link" href="PVFC.php">Homepage</a>
			  </li>
			  <li class="nav-item active">
				<a class="nav-link" href="tables.php">Tables</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="queries.php">Queries</a>
			  </li>
			</ul>
		  </div>
		</nav>
	
		<!-- Table Selection Container -->
		<div class="container">
			<h1>Table Selection</h1>
			<p>Select a table from the database to see the contents</p>
			<p>Certain tables allow you to add new rows to them</p>
			
			<form name="tableForm" id="tableForm" method="GET" >
			  <div class="input-append"> <input class="span2" id="table" name="table" type="hidden">
				<div class="btn-group">
				  <button class="btn dropdown-toggle btn-primary" data-toggle="dropdown">Select Table<span class="caret"></span></button>
				  
				  <!-- Dynamically add table names to dropdown -->
				  <ul class="dropdown-menu" >
					<?php
					foreach(db_get_tables() as $table)
						echo("<li class=\"dropdown-item\" onclick=\"$('#table').val('{$table}'); $('#tableForm').submit()\">{$table}</li>\n");
					?>
				  </ul>
				</div>
			  </div>
			</form>
			
			<!-- Table info if available -->
			<?php
			$formTable = $_POST['form_table'];
			if (isset($postTable) && !isset($formTable)){
				print("<hr>");
				$tableCardinality = db_get_cardinality($postTable);
				$tableDegree = db_get_degree($postTable);
				
				print(sprintf("<h1>Selected Table: %s </h1>", $postTable));
				print(sprintf("<p>Cardinality: %s </p>", $tableCardinality));
				print(sprintf("<p>Degree: %s </p>", $tableDegree));
				
				// Check if we should show the modification controls
				if(in_array($postTable, $arrayModifiableTables)){
					print("<hr>");
					print("<h1>Modification Form </h1>");
					print("<p> This table is part of the modifiable tables. You can use the form below to add rows to the table </p>");
					print(db_get_table_html_form($postTable));
				}
				
				// Display table contents
				print("<hr>");
				print("<h1>Table Contents:</h1>");
				$tableHTML = db_get_table_html($postTable);
				print($tableHTML);
			}
			
			// They tried to update a table. Check the result
			
			if(isset($formTable)){
				print("<hr>");
				$tableCardinality = db_get_cardinality($formTable);
				$tableDegree = db_get_degree($formTable);
				
				// Sanitize table name for query
				$formTable = mysql_escape_string($formTable);
				// http://stackoverflow.com/questions/5479999/foreach-value-from-post-from-form
				$arrayKeys = array();
				$arrayValues = array();
				// Get the keys and values for the query
				foreach($_POST as $key => $value) {
					// Ignore post variables that aren't part of the input query
					if(!str_contains($key, 'input_'))
						continue;
					
					// Remove the input identifier
					$key = substr($key, 6);
					
					// Eliminate whitespace and sanitize
					$value = mysql_escape_string(trim($value));
					
					// Empty values should be NULL in MySQL for proper constraint checking
					// NULL shouldn't have quotation marks!
					if(empty($value))
						$value = "NULL";
					else
						$value = "'".$value."'";
					
					// Update our arrays
					array_push($arrayKeys, $key);
					array_push($arrayValues, $value);
				}
				// Prepare the query
				$query = sprintf("INSERT INTO %s (%s) VALUES (%s)", $formTable, implode(", " , $arrayKeys), implode(", ", $arrayValues));
				$result = mysql_query($query);
				
				// It failed :(
				if(mysql_errno()){
					print(sprintf('<div class="alert alert-danger alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
							<p><strong>Oh snap!</strong> Something went wrong while adding to the table </p>
							<p>Table: %s</p>
							<p>MySQL Query: %s</p>
							<p>MySQL Error: %s</p>
							</div>', $formTable, $query, mysql_error()));
				}
				else{
					print(sprintf('<div class="alert alert-success alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
							<p> <strong>Success!</strong> Your update to the database was succesful </p>
							<p> Table: %s </p>
							<p> MySQL Query: %s </p>
							</div>', $formTable, $query));
				}
				
				mysql_free_result($result);
				
				print(sprintf("<h1>Selected Table: %s </h1>", $formTable));
				print(sprintf("<p>Cardinality: %s </p>", $tableCardinality));
				print(sprintf("<p>Degree: %s </p>", $tableDegree));
				
				// Check if we should show the modification controls
				if(in_array($formTable, $arrayModifiableTables)){
					print("<hr>");
					print("<h1>Modification Form </h1>");
					print("<p> This table is part of the modifiable tables. You can use the form below to add rows to the table </p>");
					print(db_get_table_html_form($formTable));
				}
				
				
				
				// Display results
				print("<hr>");
				print("<h1>Modification Results:</h1>");
				$tableHTML = db_get_table_html($formTable);
				print($tableHTML);
			}
			?>
		</div>
		
		<!-- Bootstrap core JavaScript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
	</body>
</html>

<?php
db_disconnect();
?>