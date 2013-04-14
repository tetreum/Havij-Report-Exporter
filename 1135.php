<?php
// HAVIJ REPORT EXPORTER (1135)
// INITIAL VARS
$sql=array();
$config=array(
	'to'=>'report.sql',
	'file'=>'report.html',
	'table'=>"exportedTable",
	'createTable'=>true,
);
//---

// Checking data
if(isset($_GET['file'])){
	$config['file']=$_GET['file'];
}
if(isset($_GET['table'])){
	$config['table']=$_GET['table'];
}
if(isset($_GET['to'])){
	$config['to']=$_GET['to'];
}



if(!isset($config['file'])){
	die('No file to import');
}

$content = file_get_contents($config['file']);

if($content==''){
	die('clean file given');
}

//Detect Havij version
$p="/<font face=\"Verdana\" size=\"4\" color=\"#DC883D\">(.*)<br>/";
preg_match($p, $content, $r);

if(strpos($r[1],'1.15')!==false){
	$config['version']='1.15';
}else{
	$config['version']='1.14';
}

//Get columns
$p="/<td nowrap bgcolor=\"#FFFFCE\"><b><font color=\"#DC883D\">(.*)<\/font><\/b><\/td>/";
preg_match_all($p, $content, $r);

$config['columns']=$r[1];
if(sizeof($config['columns'])<1){
	die('No columns found');
}

//get data
$p="/\<td bgcolor=\"\#FFF7F2\"\>(.*)\<\/td\>/";
preg_match_all($p, $content, $r);

$i=0;
$o=0;

$columns=sizeof($config['columns']);
foreach($r[1] as $data)
{
	$sql[$o][]=$data;
	$i++;
	if($i==$columns){
		$i=0;
		$o++;
	}
}

$p="/\<td bgcolor=\"\#FFFFFF\"\>(.*)\<\/td\>/";
preg_match_all($p, $content, $r);

$i=0;

foreach($r[1] as $data)
{
	$sql[$o][]=$data;
	$i++;
	if($i==$columns){
		$i=0;
		$o++;
	}
}

// parse data to SQL

$parser['content']="-- HF HAVIJ REPORT EXPORTER (1135)[on github.com]\n";

if($config['createTable']){
	$parser['content'].="
--CHECK CREATE TABLE SCHEME BEFORE IMPORTING IT!!\n\n

CREATE TABLE IF NOT EXISTS `".$config['table']."` (\n";
}


//columns line
$parser['columns']='';
foreach($config['columns'] as $column)
{
	$column=mysql_escape_string($column);
	if($parser['columns']==''){
		$parser['columns']=$column;
		$primary=$column;
	}else{
		$parser['columns'].=','.$column;
	}

	if($config['createTable']){		
		$parser['content'].="
	`".$column."` varchar(120) DEFAULT NULL,\n";
	}
}

if($config['createTable']){	
	$parser['content'].="
PRIMARY KEY (`".$primary."`) \n) ENGINE=MyISAM DEFAULT CHARSET=latin1; \n\n";
}
// data

foreach($sql as $rows)
{
	$line='';
	foreach($rows as $row)
	{
		$row=mysql_escape_string($row);
		if(!is_numeric($row)){
			$row="'".$row."'";
		}
		if($line==''){
			$line=$row;
		}else{
			$line.=','.$row;
		}		
	}
	$parser['content'].="
	INSERT INTO ".$config['table']." (".$parser['columns'].") VALUES ($line);\n";
}

if(isset($config['to'])){
	$triki=fopen($config['to'],'w'); 
	fwrite($triki, utf8_decode($parser['content']));
	fclose($triki);
}

?>
