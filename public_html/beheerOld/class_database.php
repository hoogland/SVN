<?PHP

class database{
//**********************************************//
//				Conversion data					//
//**********************************************//

//checking wich sort of data has to be converted
function conversion($waarde, $soort, $invoer){
	//DEBUG INFO
	if($_POST['DEBUG'] == 1){
		echo $waarde." = ".$soort."<BR>";
	}
	//conversions for extraction of data
	if ($invoer == "0")
		{
			if ($soort == "text" OR $soort == "number")
			{
			}
			if ($soort == "date")
			{
				$waarde	= $this->datum($waarde);
			}
			if ($soort == "date_time")
			{
				$waarde	= $this->datum_tijd($waarde);
			}
			if ($soort == "textarea")
			{
				$waarde = $this->textarea($waarde);
			}
		}
	//conversions for input data	
	if ($invoer == "1")
		{
			if ($soort == "date")
			{
				$waarde	= $this->mysql_datum($waarde);
			}
			if ($soort == "text")
			{
				$waarde	= $this->mysql_text($waarde);
			}
		}				
		return $waarde;	
	}

//function for conversion

	//Conversion from mysql date + time --> readable date
	function datum_tijd($datum_tijd){
			if($datum_tijd != "0000-00-00 00:00:00"){
					$tijd = strtotime($datum_tijd);
			$datum_tijd = date("d-m-Y H:i:s", $tijd);};
			return $datum_tijd;
	}
	//Conversion from mysql date --> readable date	
	function datum($datum){
			if($datum != "0000-00-00" && $datum != ""){
					$tijd = strtotime($datum);
					$datum = date("d-m-Y", $tijd);};
			return $datum;
	}

	//Conversion from text out of the database to total formatted HTML-text
	function textarea($textarea){
		$textarea	= nl2br($textarea);
		return $textarea;
	}
	
	//Conversion from readable date -> mysql date
	function mysql_datum($mysql_datum){
		list($dag, $maand, $jaar) = split('[/-]', $mysql_datum);
		$mysql_datum = $jaar."-".$maand."-".$dag;
		return $mysql_datum;
	}
	
	//Conversion from text -> insertable text in db
	function mysql_text($text){
		$text	= mysql_real_escape_string($text);
		return $text;
	}

//**********************************************//
//	GENERATING DIFFERENT TYPE OF INPUT-FIELDS	//
//**********************************************//

function form_type ($type, $id){
	if ($type == "text"){
		echo "<input name=\"$id\">";
	}	
	if ($type == "number"){
		echo "<input name=\"$id\">";
	}
	if ($type == "date"){
		echo "<input name=\"".$id."\"  size='10' maxlength='10' onFocus=\"javascript:vDateType='3'\" onKeyUp=\"DateFormat(this,this.value,event,false,'3')\" onBlur=\"DateFormat(this,this.value,event,true,'3')\">";
	}
	if ($type == "textarea"){
		echo "<TEXTAREA name=".$id."></TEXTAREA>";
	}
	if ($type['0'] == "select"){
		echo "<SELECT name=\"$id\">";
				$aantal = count($type);
				for ($a = 1; $a < $aantal; $a++){
					echo "\n\t\t<OPTION value=\"$a\">".$type[$a];	
				}
		echo "\n\t</SELECT>";
	}
}

//**********************************************//
//				Extraction data					//
//**********************************************//

function extraction($table, $rows, $where,$type){
	include('database.inc');

	//Making the where clausule
	if ($where != ""){
		$where		= "WHERE ".$where;	
	}
	
	//Making the ORDER BY clausule
	if ($_GET['sort'] 	!= ""){
		$sort_column	= $_GET['sort'];
		$order_by		= "ORDER BY ".$sort_column." ".strtoupper($_GET['order_sort']);
	}
	
	//Creating and executing the query
	$query			= "SELECT ".$rows." FROM ".$table." ".$where." ".$order_by;
	if($_POST['DEBUG'] == 1){
		echo $query;
	}
	$result			= mysql_query($query);
	$num_rows		= mysql_num_rows($result);
	$rows			= explode(",", $rows);
	$aantal			= count($rows);

	//for each row extract the data
	for($a = 0; $a < $num_rows; $a++){
		$row	= mysql_fetch_array($result);
		$gegevens = "";
		//for each column extract data to $gegevens
		for($b = 0; $b < $aantal ; $b++){
			$waarde	= $row[$b];
			//conversion to the right values
			 $waarde = $this-> conversion($waarde, $type[$b], 0);
			if ($gegevens == ""){
				$gegevens	= 	",,,".$waarde;
			}
			else{
				$gegevens	.= ",,,".$waarde;
			}
		}
		$gegevens	= substr($gegevens, 3);
		$gegevens	= explode(",,,",$gegevens);
		$array[$a]	= $gegevens;	
		
	}
	return $array;
}

//**********************************************//
//				Printing Tables					//
//**********************************************//

function table($array, $headers, $print_header, $sort, $ext_css, $visible, $link){

	//convert headers to an array
	$header		= trim($header);
	$header 	= str_replace(', ',',',$header);
	
	$rows			= count($array);
	$columns		= count($array[0]);
	$order_sort 	= "asc";
	if ($_GET['order_sort'] == "asc"){
		$order_sort	= "desc";}

	//check if headers have to be printed
	if ($print_header == 1){
		//Printing headers
		$header		= "\t\t<TR class =\"eerste_rij\">\t";
		for ($a = 0; $a < count($headers); $a++){
			if ($visible[$a] == 1){
				if ($sort != ""){
					$header	.= "<TD><a href='".$_SERVER['PHP_SELF']."?sort=".$sort[$a]."&order_sort=".$order_sort."'>".$headers[$a]."</a></TD>";
				}
				else{
					$header		.= "<TD>".$headers[$a]."</TD>";
				}
			}
		}
		$header		.= "</TR>\n";
	}

	//printing table
	echo "<table>\n";
	echo $header;
	
	for($a = 0; $a < $rows; $a++){
		
		//Creating extra css
		if($ext_css == 1){
			if ($a % 2){
			$css = "class =\"even\"";}
			else {$css = "class =\"oneven\"";}
		}

		echo "\t\t<TR ". $css.">\t\t";
		for($b = 0; $b < count($array[$a]); $b++){
			if($visible[$b] == 1){
				$links	= $this-> links($link,$array[$a],$b);
				
				echo "<TD>".$links['1'],$array[$a][$b],$links['2']."</TD>";
			}
		}
		echo "</TR>\n";
	}
	echo "\t</TABLE>\n";
}

//**********************************************//
//			Creating the link if nessecary		//
//**********************************************//
function links($link,$array,$b){
	$links			= array('','');
	$b 				= $b + 1;
	if(in_array($b, $link)){							//Checken of bij huidige kolom er een link is
		$key		= array_keys($link,$b);				//Keynummer vinden bij huidige kolom (-1 voor link)
		$key		= $key['0'] - 1;
		$temp		= $link[$key];						//Linkgegevens doorgeven
		while(strstr($temp,"!")){
			$a			= strpos($temp, "!");			//Linkerkant variabele
			$b			= strpos($temp, "#");			//Rechterkant variabele
			$c			= $b - $a - 1;
			$column		= substr($temp, $a +1, $c);	//Kolom nummer voor variabele binnenhalen
			$temp		= substr($temp, 0 , $a)."".str_replace("'","\'",$array[$column - 1])."".substr($temp,$b +1)."";
		}
		$links['1'] = "<a href=\"".$temp."\">";
		$links['2']	= "</a>";
	}
	return $links;	
}


//**********************************************//
//			Printing filled info				//
//**********************************************//

function filled_data($array, $headers, $ext_css, $link){
	$aantal		= count($array['0']);
	if($ext_css == 1){
		$css	= " class = 'eigenschap'";
	}
	echo "<TABLE>\n";
	for($a = 0; $a < $aantal; $a++){
		echo "\t<TR><TD$css>".$headers[$a]."<TD>".$array['0'][$a]."\n";
	}
	echo "</TABLE>";
}

//**********************************************//
//				Printing HTML-forms				//
//**********************************************//

function form ($doel, $headers, $type, $id, $name){
	$aantal	= count($headers);
	
	//add date-file if needed
	if(in_array("date", $type)){
		include('datum_check.php');
	}
	
	echo "\n<FORM method=\"post\" action=\"".$doel."\" name=\"".$name."\">\n";
	echo "\t<TABLE>\n";
	for ($a = 0; $a < $aantal; $a++){
		echo "\t\t<TR><TD>".$headers[$a]."<TD>";
		$this->form_type($type[$a], $id[$a]);
		echo "\n";
	}
	echo "\n\t</TABLE>";
	echo "\n\t<input type=\"submit\" value=\"verzenden\"> ";
	echo "\n\t<input type=\"reset\" value=\"wissen\">";
	echo "\n</form>\n";
}

//**********************************************//
//				Changing data-forms				//
//**********************************************//


//**********************************************//
//				INSERTING DATA IN DATABASE		//
//**********************************************//


	function invoeren_database($gegevens, $rijen, $soort, $nieuw, $tabel, $voorwaarden)
	{
		include('database.inc');
		//Inserting the data
		if($nieuw == "1")
		{
			$query			= "INSERT INTO `".$tabel."` (";
			$aantal_rijen	= count($rijen);
			//Stating in which rows the data has to be entered
			$b 				= $aantal_rijen - 1;
			for ($a = 0; $a < $b; $a++)
			{
				$query		.= "`".$rijen[$a]."`, ";	
			}
			$query			.= "`".$rijen[$b]."`)";
			
			//Putting down the values
			$query			.= " VALUES (";
			for ($a = 0; $a < $b; $a++)
			{
				$data		= $this->conversion($gegevens[$a], $soort[$a], '1');
				$query		.= "'".$data."', ";
			}
			$data			= $this->conversion($gegevens[$b], $soort[$b], '1');
			$query			.= "'".$data."')";
		}
		
		//Changing the data
		if($nieuw == "0")
		{
				
				
		}

		//Checking the query
		if (get_magic_quotes_gpc()) {
		    $query = addslashes($query);
		  }
		if(version_compare(phpversion(),"4.3.0") == "-1") {
		    mysql_escape_string($query);
		}
		else {
		    mysql_real_escape_string($query);
		}

		//Executing the query
		$result	= mysql_query($query);
		if(!$result){//echo "The query went wrong pleas try again, error: ".mysql_error()."<BR>The query was: ".$query;
        }
		else{ //echo "The data has succesfully been inserted";
		//DATA FOR ANALYSING RESULTS OF QUERY
		$return['0']	= mysql_affected_rows();		//Number of rows effected
		$return['1']	= mysql_insert_id();			//Number of autoincrement (last id)
 		return;
		}
	}

//**********************************************//
//				END OF DATABASE CLASSE    		//
//**********************************************//
}

?>
