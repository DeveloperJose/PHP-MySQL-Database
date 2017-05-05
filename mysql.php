<?php
function db_disconnect(){
	mysql_close($connection);
}

function db_connect(){
    $host='earth.cs.utep.edu';
    $user='cs_jperez50';
    $password='';
    $database='cs_jperez50';
   
    $connection = mysql_connect($host,$user,$password);
    
    if(!$connection)
        die('Could not connect'.mysql_error());

    mysql_select_db($database, $connection);
}

function db_get_tables(){
    $result = mysql_query("SHOW TABLES;");
    
    $tables = array();
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
        $name = $row[0];
        array_push($tables, $name);
    }    
    mysql_free_result($result);

    return $tables;
}

function db_get_cardinality($table){
    $query = sprintf("SELECT * FROM %s;", mysql_real_escape_string($table));
   
    $result=mysql_query($query);
    $cardinality = mysql_num_rows($result);
    
    mysql_free_result($result);
    
    return $cardinality;
}

function db_get_degree($table){
    $query = sprintf("SELECT * FROM %s;", mysql_real_escape_string($table));
    $result=mysql_query($query);
    
    $degree = mysql_num_fields($result);
    
    mysql_free_result($result);
    
    return $degree;
}

function db_get_table_html($table){
	$query = sprintf('SELECT * FROM %s;', mysql_real_escape_string($table));
	return db_get_query_html($query);
}

function db_get_table_html_form($table){
	// Gets the MySQL data types for the columns in the table
	$query = sprintf("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'cs_jperez50' AND TABLE_NAME = '%s';", mysql_real_escape_string($table));
	$result = mysql_query($query);

	if(mysql_errno()){    
		mysql_free_result($result);
		return mysql_error();
	}
	
	// Create an HTML form for modifying this table based on its columns
	$html = '<form method="POST">';
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
		$inputID = 'input_'.$row['COLUMN_NAME'];
		$columnName = $row['COLUMN_NAME'];
		$dataType = $row['DATA_TYPE'];
		
		// Change MySQL types to HTML input types
		if($dataType == "decimal")
			$inputType = "number";
		else
			$inputType = $dataType;
		
		// Prepare input
		$html.='<div class="form-group row">';
		$html.=sprintf('<label for="%s" class="col-2 col-form-label">%s</label>', $inputID, $columnName);
		$html.='<div class="col-10">';
		$html.=sprintf('<input class="form-control" type="%s" placeholder="" name="%s"">', $inputType, $inputID, $inputID);
		$html.='</div></div>';
	
	}
	// Add hidden input that stores the table name for persitence
	$html.=sprintf('<input type="hidden" name="form_table" value="%s">', $table);
	// Add submit button
	$html.='<button type="submit" class="btn btn-primary">Submit</button>';
	$html.="</form>";
	
	mysql_free_result($result);
	
	return $html;
}

function db_parse_post($postVar){
	if(empty(trim($postVar)))
		return "NULL";
	else
		return "'".$postVar."'";
}

function db_get_query_html($query, $sanitize=True){
	if($sanitize)
		$query = mysql_real_escape_string($query);
	
    $result= mysql_query($query);
	
	if(mysql_errno()){    
		mysql_free_result($result);
		return mysql_error();
	}
	
    $degree = mysql_num_fields($result);
    
    $html = "<table class='table table-hover'>\n";
    $html.="<thead class='thead-inverse'>\n";
    
    for($i = 0; $i < $degree; $i++){
        $fieldName = mysql_field_name($result, $i);
        $html.="<th>{$fieldName}</th>\n";
    }
    
    $html.="</thead>\n";
    
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
      $html.="<tr>\n";
      foreach($row as $value)
        $html.='<td>'.$value.'</td>';
      $html.="</tr>\n";
    }
    $html.="</table>";
    
    mysql_free_result($result);
    
    return $html;
}
// http://stackoverflow.com/questions/4366730/how-do-i-check-if-a-string-contains-a-specific-word-in-php?page=2&tab=votes#tab-top
function str_contains($haystack, $needle, $caseSensitive = false) {
    return $caseSensitive ? 
            (strpos($haystack, $needle) === FALSE ? FALSE : TRUE):
            (stripos($haystack, $needle) === FALSE ? FALSE : TRUE);
}

?>