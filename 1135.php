<?php

// HAVIJ REPORT EXPORTER (1135)
// INITIAL VARS
$debug_mode=false;
$aio_query=true; // single insert order
$sql_import=false;	// $to file will contain sql queries
$array_import=false; // $data will contain the final array
$all_queries=''; // DONT TOUCH IT
//

// SET PARAMS
if(isset($_GET['to']))
{	
	$to_name=$_GET['to'];
}
if(!isset($_GET['table'])){
	$table_name='11_TABLE_NAME_35';
}else{
	$table_name=mysql_escape_string($_GET['table']);
}

if(isset($_GET['columns'])){	
	$columns_list=$_GET['columns'];
}

if(!isset($_GET['file'])){	
	$file_name='NOT SET, EXPORT STOPPED';
}else{
	$file_name=$_GET['file'];
}
if(isset($_GET['aio']))
{	
	$aio_query=false;
}

echo'<pre>';
//HEADER MESSAGE
$message="-- HF HAVIJ REPORT EXPORTER (1135)
-- for larger exports, use it in localhost.
-- USAGE:
--	EXPORT TO FILE: 1135.php?file=HAVIJ_REPORT.html&table=YOUR_SQL_TABLE_NAME&to=export.sql
--	EXPORT TO ARRAY: edit 1135.php put array_import to true
--	SHOW QUERIES HERE (def): 1135.php?file=HAVIJ_REPORT.html&table=YOUR_SQL_TABLE_NAME
--	change column names: &columns=column1,column2,...
--	disable all-in-one query to get an INSERT for each line: &aio=0
-- File name: $file_name";
if(isset($to_name)){
	$message.="
-- To file: $to_name";
}else{
	$sql_import=true;
}
	$message.="
-- Table name: $table_name";
	if(isset($columns_list)){
	$message.="
-- Columns list: $columns_list";		
	}
$message.="
-- All-in-one query: $aio_query
-- Debug mode: $debug_mode
-- THIS LINES WILL BE IGNORED IN MYSQL

";
echo $message;
$all_queries=$message;
//--

if($file_name=='NOT SET, EXPORT STOPPED'){die();}
$content = file_get_contents($file_name);
if ($content === false)
   die('Failed when obtaining '.$file_name.', check perms, etc..');

// GET COLUMNS
$results=explode("#DC883D\">",$content);
foreach($results as $id_result=>$column)
{
	if($id_result>3)
	{
		$bah=explode("</font></b></td>",$column);
		$columns[]=$bah[0];
		if($id_result==4){
			$query_columns=$bah[0];
		}else{
			$query_columns.=','.$bah[0];
		}
	}
}
$results='';
$total_columns=sizeof($columns);
if($debug_mode){echo 'Columns: '.$total_columns;}
if(isset($columns_list)){
	$num_columns=sizeof(explode(",",$columns_list));
	if($num_columns==$total_columns){
		$query_columns=mysql_escape_string($columns_list);
	}else{
		die('ERROR: COLUMNS FOUND:'.$total_columns.' YOU LISTED:'.$num_columns);
	}

}
// ---

// GET DATA
$i=0;
$o=0;
$results=explode("#FFF7F2\">",$content);
unset($results[0]);

foreach($results as $column_data)
{
	$bah=explode("</td>",$column_data);
	if($i<$total_columns)
	{	
		if($array_import){
			$data[$o][$columns[$i]]=$bah[0];
		}
		if($i==0){
			$query_data[$o]="'".mysql_escape_string($bah[0])."'";
		}else{
			$query_data[$o].=",'".mysql_escape_string($bah[0])."'";
		}
		$i++;
	}else{
		if($sql_import)
		{
			// SINGLE INSERT?
			if($aio_query)
			{
				if($o==0){
		echo "INSERT INTO $table_name ($query_columns) VALUES<br>($query_data[$o])";
				}else{
		echo ",<br>($query_data[$o])";
				}

			}else{
				echo "INSERT INTO $table_name ($query_columns) VALUES ($query_data[$o]);<br>";
			}
		}
		elseif(!$array_import)
		{
			if($aio_query)
			{
				if($o==0){
		$all_queries.="
INSERT INTO $table_name ($query_columns) VALUES
($query_data[$o])";
				}else{
		$all_queries.=",
($query_data[$o])";
				}
			}else{
				$all_queries.="
INSERT INTO $table_name ($query_columns) VALUES ($query_data[$o]);";
			}

		}
		$o++;
		$i=0;
		if($array_import){
			$data[$o][$columns[$i]]=$bah[0];
		}
		$query_data[$o]="'".mysql_escape_string($bah[0])."'";
		$i++;
	}

}
$i=0;

$results=explode("white\">",$content);
unset($results[0]);

foreach($results as $column_data)
{
	$bah=explode("</td>",$column_data);
	if($i<$total_columns)
	{	
		if($array_import){
			$data[$o][$columns[$i]]=$bah[0];
		}
		if($i==0){
			$query_data[$o]="'".mysql_escape_string($bah[0])."'";
		}else{
			$query_data[$o].=",'".mysql_escape_string($bah[0])."'";
		}
		$i++;
	}
	else
	{
		if($sql_import)
		{
			// SINGLE INSERT?
			if($aio_query)
			{
				if($o==0){
		echo "INSERT INTO $table_name ($query_columns) VALUES<br>";
				}
				echo ",<br>($query_data[$o])";
			}else{
				echo "INSERT INTO $table_name ($query_columns) VALUES ($query_data[$o]);<br>";
			}
		}
		elseif(!$array_import)
		{
			if($aio_query)
			{
				if($o==0){
		$all_queries.="
INSERT INTO $table_name ($query_columns) VALUES
($query_data[$o])";
				}else{
		$all_queries.=",
($query_data[$o])";
				}
			}else{
				$all_queries.="
INSERT INTO $table_name ($query_columns) VALUES ($query_data[$o]);";
			}
		}
		$o++;
		$i=0;
		if($array_import){
			$data[$o][$columns[$i]]=$bah[0];
		}
		$query_data[$o]="'".mysql_escape_string($bah[0])."'";
		$i++;
	}

}
$total_rows=$o+2; // +2 to look like havij report
if($debug_mode){echo 'Columns: '.$total_rows;}
//-

// SAVE DATA
if(!$sql_import && !$array_import)
{
	$triki=fopen($to_name,'w'); 
	fwrite($triki, utf8_decode($all_queries));
	fclose($triki);
	die('Export to '.$to_name.' done. (Dont be idiot use wordpad or better to open it, not notepad.)');
}

?>