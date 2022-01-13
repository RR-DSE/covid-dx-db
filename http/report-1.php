<?php
// report-1.php (utf-8)
// 
// Edited by: RR-DSE
// Timestamp: 22-01-12 20:10:31

//---
//App init
//---

require('app-base.php');
require('tools.php');

$app_id = 'report1';
$app_curr = 'report-1.php';
$session_id = app_get_session();
$session_needed = true;
$app_header_title = $captions['report1'];
$app_str = $app_report1_str;
$app_layout = $app_report1_layout;

$app_res = app_init();
if($app_res != $res['ok'])
  app_end();

$style = $app_layout['style'];

$resdatetimebegin = "";
$resdatetimeend = "";
updatefromget("datetimebegin", $resdatetimebegin);
updatefrompost("form-datetimebegin", $resdatetimebegin);
updatefromget("datetimeend", $resdatetimeend);
updatefrompost("form-datetimeend", $resdatetimeend);

if($resdatetimebegin == "")
  $resdatetimebegin = app_get_curr_date()." 00:00:00";
if($resdatetimeend == "")
  $resdatetimeend = app_get_curr_date()." 23:59:00";

if(!app_check_datetime($resdatetimebegin))
{
  //echo("<P {$style['ERROR']}>{$app_str['errordate']}</P>");
  app_header_echo();
  app_error_print($app_str['errordate']);
  app_end();
}

if(!app_check_datetime($resdatetimeend))
{
  //echo("<P {$style['ERROR']}>{$app_str['errordate']}</P>");
  app_header_echo();
  app_error_print($app_str['errordate']);
  app_end();
}

$rescurrdate = app_get_curr_date();

//---
//Main routine
//---

$dbstrdatetimebegin = getstringdb($resdatetimebegin, "datetime", FALSE);
$dbstrdatetimeend = getstringdb($resdatetimeend, "datetime", FALSE);

echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">");
echo("<HTML>");
echo("<BODY {$style['BODY']}>");

echo("<P {$style['ORG']}>{$app_str['org']}");
echo("<P {$style['TITLE']}>{$app_str['title']}");
echo("<P {$style['INFO2']}>{$app_str['infodate']}".$rescurrdate."</P>");

$query = "
  SELECT
    B.name AS Name,
    DATE_FORMAT(B.birthday, '{$app_regex['sb_dateformat']}') AS Birthday,
    TIMESTAMPDIFF(YEAR, B.birthday, CURDATE()) AS Age,
    B.record AS Record,
    B.state_id_1 AS StateID1,
    B.patient_status AS Status,
    C.title AS DepartmentTitle,
    DATE_FORMAT(A.sample_date, '{$app_regex['sb_dateformat']}') AS SampleDate,
    DATE_FORMAT(A.result_datetime, '{$app_regex['sb_datetimeformat']}') AS ResultDateTime,
    A.result_code AS Result
  FROM tests AS A
  LEFT JOIN patients AS B
    ON B.id = A.patient_id
  LEFT JOIN departments AS C
    ON C.id = B.department
  WHERE
    A.status <> 2
    AND A.result_code != 'waiting' AND A.result_code != 'notest'
    AND A.result_datetime >= $dbstrdatetimebegin AND A.result_datetime <= $dbstrdatetimeend
  ORDER BY A.name
  ";
$db_query_res = app_db_query($query);
$db_query_rowcount = app_db_numrows($db_query_res);

echo("<H1 {$style['H1']}>{$app_str['header1']}</H1>");
echo("<P {$style['INFO']}>{$app_str['infodate']}".$resdatetimebegin." a ".$resdatetimeend."</P>");
echo("<P {$style['INFO']}>{$app_str['infoquantity']}".$db_query_rowcount."</P>");

echo("<DIV {$style['DIV_TABLE']}>");
echo("<TABLE {$style['TABLE']}>");
echo("<COLGROUP>");
echo("  <COL {$style['COL_1']}>");
echo("  <COL {$style['COL_2']}>");
echo("  <COL {$style['COL_3']}>");
echo("  <COL {$style['COL_4']}>");
echo("  <COL {$style['COL_5']}>");
echo("  <COL {$style['COL_6']}>");
echo("  <COL {$style['COL_7']}>");
echo("  <COL {$style['COL_8']}>");
echo("  <COL {$style['COL_9']}>");
echo("</COLGROUP>");
echo("  <THEAD>");
echo("    <TR>");
echo("      <TH {$style['TH']}>{$app_str['name']}</TH>");
echo("      <TH {$style['TH']}>{$app_str['birthday']}</TH>");
echo("      <TH {$style['TH']}>{$app_str['age']}</TH>");
echo("      <TH {$style['TH']}>{$app_str['record']}</TH>");
echo("      <TH {$style['TH']}>{$app_str['stateid1']}</TH>");
echo("      <TH {$style['TH']}>{$app_str['department']}</TH>");
echo("      <TH {$style['TH']}>{$app_str['sampledate']}</TH>");
echo("      <TH {$style['TH']}>{$app_str['result']}</TH>");
echo("      <TH {$style['TH']}>{$app_str['resultdatetime']}</TH>");
echo("    </TR>");
echo("  </THEAD>");
echo("  <TBODY>");
while($db_query_row = app_db_fetch($db_query_res))
{
  echo("  <TR>");
  echo("    <TD {$style['TD']}>".$db_query_row['Name']."</TD>");
  echo("    <TD {$style['TD']}>".$db_query_row['Birthday']."</TD>");
  echo("    <TD {$style['TD']}>".$db_query_row['Age']."</TD>");
  echo("    <TD {$style['TD']}>".$db_query_row['Record']."</TD>");
  echo("    <TD {$style['TD']}>".$db_query_row['StateID1']."</TD>");
  echo("    <TD {$style['TD']}>".$db_query_row['DepartmentTitle']."</TD>");
  echo("    <TD {$style['TD']}>".$db_query_row['SampleDate']."</TD>");
  echo("    <TD {$style['TD']}>".getteststatusstr($db_query_row['Result'])."</TD>");
  echo("    <TD {$style['TD']}>".$db_query_row['ResultDateTime']."</TD>");
  echo("  </TR>");
}
echo("  </TBODY>");
echo("</TABLE>");
echo("</DIV>");

app_db_free($db_query_res);

$query = "
  SELECT
    B.name AS Name,
    DATE_FORMAT(B.birthday, '{$app_regex['sb_dateformat']}') AS Birthday,
    TIMESTAMPDIFF(YEAR, B.birthday, CURDATE()) AS Age,
    B.record AS Record,
    B.state_id_1 AS StateID1,
    B.patient_status AS Status,
    C.title AS DepartmentTitle,
    DATE_FORMAT(A.sample_date, '{$app_regex['sb_dateformat']}') AS SampleDate,
    DATE_FORMAT(A.result_datetime, '{$app_regex['sb_datetimeformat']}') AS ResultDateTime,
    A.result_code AS Result
  FROM tests AS A
  LEFT JOIN patients AS B
    ON B.id = A.patient_id
  LEFT JOIN departments AS C
    ON C.id = B.department
  WHERE
    A.status <> 2
    AND A.result_code = 'waiting'
  ORDER BY A.name
  ";
$db_query_res = app_db_query($query);
$db_query_rowcount = app_db_numrows($db_query_res);

echo("<H1 {$style['H1']}>{$app_str['header2']}</H1>");
echo("<P {$style['INFO']}>{$app_str['infoquantity']}".$db_query_rowcount."</P>");

echo("<DIV {$style['DIV_TABLE']}>");
echo("<TABLE {$style['TABLE']}>");
echo("<COLGROUP>");
echo("  <COL {$style['COL_1']}>");
echo("  <COL {$style['COL_2']}>");
echo("  <COL {$style['COL_3']}>");
echo("  <COL {$style['COL_4']}>");
echo("  <COL {$style['COL_5']}>");
echo("  <COL {$style['COL_6']}>");
echo("  <COL {$style['COL_7']}>");
echo("</COLGROUP>");
echo("  <THEAD>");
echo("    <TR>");
echo("      <TH {$style['TH']}>{$app_str['name']}</TH>");
echo("      <TH {$style['TH']}>{$app_str['birthday']}</TH>");
echo("      <TH {$style['TH']}>{$app_str['age']}</TH>");
echo("      <TH {$style['TH']}>{$app_str['record']}</TH>");
echo("      <TH {$style['TH']}>{$app_str['stateid1']}</TH>");
echo("      <TH {$style['TH']}>{$app_str['department']}</TH>");
echo("      <TH {$style['TH']}>{$app_str['sampledate']}</TH>");
echo("    </TR>");
echo("  </THEAD>");
echo("  <TBODY>");
while($db_query_row = app_db_fetch($db_query_res))
{
  echo("  <TR>");
  echo("    <TD {$style['TD']}>".$db_query_row['Name']."</TD>");
  echo("    <TD {$style['TD']}>".$db_query_row['Birthday']."</TD>");
  echo("    <TD {$style['TD']}>".$db_query_row['Age']."</TD>");
  echo("    <TD {$style['TD']}>".$db_query_row['Record']."</TD>");
  echo("    <TD {$style['TD']}>".$db_query_row['StateID1']."</TD>");
  echo("    <TD {$style['TD']}>".$db_query_row['DepartmentTitle']."</TD>");
  echo("    <TD {$style['TD']}>".$db_query_row['SampleDate']."</TD>");
  echo("  </TR>");
}
echo("  </TBODY>");
echo("</TABLE>");
echo("</DIV>");

app_db_free($db_query_res);

app_end();

function getstatusstr($status)
{
  global $app_str;
  
  $res = "";
  if($status == "inpatient")
    $res = $app_str['inpatient'];
  if($status == "discharged")
    $res = $app_str['discharged'];
  if($status == "deceased")
    $res = $app_str['deceased'];
  if($status == "outpatient")
    $res = $app_str['outpatient'];
  if($status == "outpatientquarant")
    $res = $app_str['outpatient'];
  if($status == "outpatientresolved")
    $res = $app_str['outpatient'];

  return $res;
}

function getteststatusstr($status)
{
  global $app_str;
  
  $res = "";
  if($status == "notest")
    $res = $app_str['notest'];
  if($status == "negative")
    $res = $app_str['negative'];
  if($status == "positive")
    $res = $app_str['positive'];
  if($status == "waiting")
    $res = $app_str['waiting'];
  if($status == "error")
    $res = $app_str['error'];
  if($status == "inconclusive")
    $res = $app_str['inconclusive'];

  return $res;
}

?>
