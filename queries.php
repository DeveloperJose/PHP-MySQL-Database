<?php
require_once("mysql.php");
db_connect();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="">

		<title>Queries - PVFC</title>

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
			  <li class="nav-item">
				<a class="nav-link" href="tables.php">Tables</a>
			  </li>
			  <li class="nav-item active">
				<a class="nav-link" href="queries.php">Queries</a>
			  </li>
			</ul>
		  </div>
		</nav>
      
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
		<li class="nav-item dropdown">
		<a class="nav-link dropdown-toggle" data-toggle="dropdown"  role="button" aria-haspopup="true" aria-expanded="false">Add to Table</a>
		<div class="dropdown-menu">
		  <a class="nav-link" href="tables.php?table=Skill_T">Add Skill</a>
		  <a class="nav-link" href="tables.php?table=Vendor_T">Add Vendor</a>
		  <a class="nav-link" href="tables.php?table=Customer_T">Add Customer</a>
		  <a class="nav-link" href="tables.php?table=Employee_T">Add Employee</a>
		</div>
		</li>
		  <li class="nav-item">
			<a class="nav-link <?php print($_POST['fragment_q1']); ?> " data-toggle="tab" href="#query1" role="tab">Associate Skill to Employee</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#query2" role="tab">List Employee Names and Skills</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#query3" role="tab">Customer Who Ordered Most</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#query4" role="tab">Vendor/Customer/Employee Info</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link <?php print($_POST['fragment_q5']); ?>" data-toggle="tab" href="#query5" role="tab">City Search</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#query6" role="tab">Top Five Customers</a>
		  </li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
		  <!-- Associate Skill to Employee -->
			<div class="tab-pane <?php print($_POST['fragment_q1']); ?>" id="query1" role="tabpanel"><div class="container"><br /><br />
				<h1>Objective</h1><hr>
				<p>Be able to associate a skill to an employee on a date</p><br />
					<form method="POST">
					<input type="hidden" name="fragment_q1" value="active" />
					<div class="form-group row"><label for="formSkillDes">Skill:</label>
						<select class="form-control" name="formSkillDes" id="formSkillDes">
							<?php
								$query = mysql_real_escape_string("SELECT SkillDescription FROM Skill_T;");
								$result = mysql_query($query);
								while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
									$skillDes = $row['SkillDescription'];
									print(sprintf('<option value="%s">%s</option>', $skillDes, $skillDes));
								}
								mysql_free_result($result);
							?>
						</select>
					</div>
					
					<div class="form-group row">
						<label for="formEmployeeName">Employee:</label>
						<select class="form-control" name="formEmployeeName" id="formEmployeeName">
						<?php
							$query = mysql_real_escape_string("SELECT EmployeeName FROM Employee_T;");
							$result = mysql_query($query);
							while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
								$employeeName = $row['EmployeeName'];
								print(sprintf('<option value="%s">%s</option>', $employeeName, $employeeName));
							}
							mysql_free_result($result);
						?>
						</select>
					</div>
					<!-- Date input -->
					<div class="form-group row"> <label for="formDate">Date</label> <input class="form-control" type="date" name="formDate" id="formDate"> </div>
					<!-- Submit button -->
					<button type="submit" class="btn btn-primary">Submit</button>
				</form>
				
				<?php
					$postSkillDes = db_parse_post($_POST['formSkillDes']);
					$postEmployeeName = db_parse_post($_POST['formEmployeeName']);
					$postDate = db_parse_post($_POST['formDate']);
					
					if(isset($_POST['formSkillDes'])){
						$result = mysql_query(sprintf("SELECT SkillID FROM Skill_T WHERE SkillDescription = %s", $postSkillDes));
						$skillID = db_parse_post(mysql_fetch_array($result, MYSQL_NUM)[0]);
						mysql_free_result($result);
						
						$result = mysql_query(sprintf("SELECT EmployeeID FROM Employee_T WHERE EmployeeName = %s", $postEmployeeName));
						$employeeID = db_parse_post(mysql_fetch_array($result, MYSQL_NUM)[0]);
						mysql_free_result($result);
						
						$query = sprintf("INSERT INTO EmployeeSkills_T (EmployeeID, SkillID, QualifyDate) VALUES (%s, %s, %s) ON DUPLICATE KEY UPDATE QualifyDate=%s;", $employeeID, $skillID, $postDate, $postDate);
						print("<br /><h1>Query</h1><hr>");
						print("<p>".$query."</p><br />");
						
						print("<h1>Results</h1>");
						print(sprintf("<p>Employee Name: %s, Employee ID: %s", $postEmployeeName, $employeeID));
						print(sprintf("<p>Skill Description: %s, Skill ID: %s", $postSkillDes, $skillID));
						print(sprintf("<p>Qualify Date: %s", $postDate));
						mysql_query($query);
						print(db_get_query_html("SELECT * FROM EmployeeSkills_T"));
					}
				?>
			</div></div>
		  
			<!-- List Employee Names and Skills -->
			<div class="tab-pane" id="query2" role="tabpanel"><div class="container"><br /><br />
				<h1>Question</h1><hr>
				<p>What are the names of all employees and their skills?</p><br />
				<?php
					$query = "SELECT EmployeeName, GROUP_CONCAT(SkillDescription) as Skills FROM Employee_T NATURAL JOIN EmployeeSkills_T NATURAL JOIN Skill_T GROUP BY EmployeeName;";
					print("<h1>Query</h1><hr>");
					print("<p>".$query."</p>");
					
					print("<h1>Results</h1><hr>");
					print(db_get_query_html($query));
				?>
			</div></div>
		  
			<!-- Customer Who Ordered Most -->
			<div class="tab-pane" id="query3" role="tabpanel"><div class="container"><br /><br />
				<h1>Question</h1><hr>
				<p>Who is the customer that has ordered the most in total?</p><br />			
				<?php
					$query = "SELECT CustomerName, COUNT(OrderID) AS TotalOrders FROM Customer_T NATURAL JOIN Order_T GROUP BY CustomerID ORDER BY COUNT(OrderID) DESC LIMIT 1;";
					print("<h1>Query</h1><hr>");
					print("<p>".$query."</p>");
					
					print("<h1>Results</h1><hr>");
					print(db_get_query_html($query));
				?>
			</div></div>
		  
			<!-- Vendor/Customer/Employee Info -->
			<div class="tab-pane" id="query4" role="tabpanel"><div class="container"><br /><br />
				<h1>Question</h1><hr>
				<p>What are the names, addresses, city and states for all vendors, customers and employees?</p><br />
				<?php
					$query = "SELECT CustomerName as Name, CustomerAddress as Address, CustomerCity as City, CustomerState as State FROM Customer_T UNION ALL ";
					$query.= "SELECT VendorName as Name, VendorAddress as Address, VendorCity as City, VendorState as State FROM Vendor_T UNION ALL ";
					$query.= "SELECT EmployeeName as Name, EmployeeAddress as Address, EmployeeCity as City, EmployeeState as State FROM Employee_T;";
				
					print("<h1>Query</h1>");
					print("<p>".$query."</p>");
					
					print("<h1>Results</h1><hr>");
					print(db_get_query_html($query));
				?>
			</div></div>
		  
			<!-- City Search -->
			<div class="tab-pane <?php print($_POST['fragment_q5']); ?>" id="query5" role="tabpanel"><div class="container"><br /><br />
			<h1>Question</h1><hr>
			<p>What are the names and addresses of all vendors, sales persons and customers in the same city?</p><br />
			<form method="POST">
				<input type="hidden" name="fragment_q5" value="active" />
				<div class="form-group row"> <label for="formCity">City:</label>
					<select class="form-control" name="formCity" id="formCity">
						<?php
							$query = "SELECT DISTINCT CustomerCity as City FROM Customer_T UNION ALL ";
							$query.= "SELECT DISTINCT VendorCity as City FROM Vendor_T UNION ALL ";
							$query.= "SELECT DISTINCT SalespersonCity as City FROM Salesperson_T;";
							$result = mysql_query($query);
							while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
								$city = $row['City'];
								if(!empty(trim($city)))
									print(sprintf('<option value="\'%s\'">%s</option>', $city, $city));
							}
							mysql_free_result($result);
						?>
						<option value="NULL">(No specified city)</option>
					</select>
				</div>
				<!-- Submit button -->
				<button type="submit" class="btn btn-primary">Submit</button>
			</form>
			<br />
			
			<?php
				$postCity = $_POST['formCity'];
				if(isset($postCity)){
					$query = "SELECT Name, Address FROM ";
					$query .= "(SELECT CustomerName as Name, CustomerAddress as Address, CustomerCity as City FROM Customer_T UNION ALL ";
					$query .= "SELECT VendorName as Name, VendorAddress as Address, VendorCity as City FROM Vendor_T UNION ALL ";
					$query .= "SELECT SalespersonName as Name, SalespersonAddress as Address, SalespersonCity as City FROM Salesperson_T) AS Table1 ";
					
					if($postCity == "NULL")
						$query .= "WHERE Table1.City IS NULL";
					else
						$query .= sprintf("WHERE Table1.City = %s;", $postCity);
					
					print("<h1>Query</h1><hr>");
					print("<p>".$query."</p>");
					
					print(sprintf("<h1>Results for city %s</h1><hr>", $postCity));
					print(db_get_query_html($query, False));
				}
			?>
			
			</div>
		  </div>
		  <!-- Top Five Customers -->
		  <div class="tab-pane" id="query6" role="tabpanel"><br /><br />
			<div class="container">
				<h1>Question</h1><hr>
				<p>What are the names, address and payment amounts of the top five customers that have had the highest individual payments, ordered by payment amount?</p><br />
				<?php
					$query = "SELECT CustomerName, CustomerAddress, PaymentAmount FROM Customer_T NATURAL JOIN Payment_T NATURAL JOIN Order_T ORDER BY PaymentAmount DESC LIMIT 5;";
					
					print("<h1>Query</h1>");
					print("<p>".$query."</p>");
					
					print("<h1>Results</h1><hr>");
					print(db_get_query_html($query));
				?>
			</div>
		  </div>
		  
		</div>
		
<?php
if(isset($_POST['fragment'])){
	print("hey");
	header('Location:queries.php#query3');
}
?>		
		
		<!-- Bootstrap core JavaScript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
</html>
<?php
db_disconnect();
?>