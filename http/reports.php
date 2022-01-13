<?php
// reports.php (utf-8)
// 
// Edited by: RR-DSE
// Timestamp: 22-01-12 20:10:38

//---
//App init
//---

require("app-base.php");
require("tools.php");

$app_id = "reports";
$app_curr = "reports.php";
$session_id = app_get_session();
$session_needed = true;
$app_header_title = $captions['reports'];
$app_str = $app_reports_str;
$app_layout = $app_reports_layout;
$app_nav_array = array( 
  array($captions['portal'], "portal.php", ""), 
  array($captions['setup'], "setup.php", ""), 
  array($captions['logout'], "logout.php","")
);

$app_res = app_start();
if($app_res != $res['ok'])
  app_end();

//---
//Main routine
//---

$form_elements = array(
  array(
    'type' => "cell_start",
    'caption' => $app_str['datetime'],
    'class' => null,
    'captionstyle' => "medium"
  ),
  array(
    'type' => "text_add",
    'name' => "datetimebegin",
    'value' => "",
    'maxlength' => "datetime",
    'size' => "small",
    'formstyle' => "small",
    'style' => "small"
  ),
  array(
    'type' => "space_add",
    'space' => 2
  ),
  array(
    'type' => "text_add",
    'name' => "datetimeend",
    'value' => "",
    'maxlength' => "datetime",
    'size' => "small",
    'formstyle' => "small",
    'style' => "small"
  ),
  array(
    'type' => "space_add",
    'space' => 2
  ),
  array(
    'type' => "button_add",
    'name' => "generate",
    'value' => $app_str['generate'],
    'class' => null
  ),
  array(
    'type' => "cell_end"
  )
);

app_form(
  $app_str['captionreport1'],
  1,
  "report-1.php",
  null,
  "medium",
  "",
  $form_elements,
  False,
  False
);


app_end();

?>
