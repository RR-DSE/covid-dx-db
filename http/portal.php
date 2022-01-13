<?php
// portal.php (utf-8)
// 
// Edited by: RR-DSE
// Timestamp: 22-01-12 20:08:16

require('app-base.php');

$app_id = 'portal';
$app_curr = 'portal.php';
$session_id = app_get_session();
$session_needed = true;
$app_header_title = $captions['portal'];
$app_str = $app_portal_str;
$app_nav_array = array( 
  array($captions['setup'], "setup.php", ""), 
  array($captions['logout'], "logout.php","")
);

$app_updatetimestamp = true;

$app_res = app_start();

if($app_res != $res['ok'])
  app_end();

app_section_start($app_str['sectionapplinks']);
if(app_access_check("patients"))
{
  app_link_app($app_str['linkpatients'], "patients.php");
  app_link_app($app_str['linkpatientsadm'], "patients.php", array('form-filter-status' => "inpatient"));
  if($session_data['profile'] != 3)
  {
    app_link_app($app_str['linkpatientstestwaiting'], "patients.php", array('form-filter-sarscov2status' => "waiting"));
    app_link_app($app_str['linkpatientstesterror'], "patients.php", array('form-filter-sarscov2status' => "errorinconclusive"));
    app_link_app($app_str['linkpatientstestpositive'], "patients.php", array('form-filter-status' => "positiveresults"));
    app_link_app($app_str['linktests'], "tests.php");
  }
}
app_section_end();

$count = 0;
if(app_access_check("reports")) $count++;
if($count > 0)
{
  app_section_start($app_str['sectionreports']);
  if(app_access_check("reports")) app_link_app($app_str['linkreports'], "reports.php");
  app_section_end();
}

$count = 0;
if(app_access_check("departments")) $count++;
if(app_access_check("users")) $count++;
if($count > 0)
{
  app_section_start($app_str['sectionsetuplinks']);
  if(app_access_check("departments")) app_link_app($app_str['linkdepartments'], "departments.php");
  if(app_access_check("users")) app_link_app($app_str['linkusers'], "users.php");
  app_section_end();
}

app_end();

?>
