<?php
// setup.php (utf-8)
// 
// Edited by: RR-DSE
// Timestamp: 22-01-12 20:12:06

//---
//App init
//---

require("app-base.php");

$app_id = "setup";
$app_curr = "setup.php";
$session_id = app_get_session();
$session_needed = true;
$app_header_title = $captions['setup'];
$app_str = $app_setup_str;
$app_nav_array = array( 
  array($captions['portal'], "portal.php", ""), 
  array($captions['logout'], "logout.php","")
);
$app_updatetimestamp = true;

$app_res = app_start();
if($app_res != $res['ok'])
  app_end();

$count = 0;
if(app_access_check("editpassword")) $count++;
//if(app_access_check("profiles")) $count++;
if(app_access_check("users")) $count++;
if($count > 0)
{
  app_section_start($app_str['sectionusersetup']);
  if(app_access_check("editpassword")) app_link_app($captions['editpassword'], "edit-password.php");
  //if(app_access_check("profiles")) app_link_app($captions['profiles'], "profiles.php");
  if(app_access_check("users")) app_link_app($captions['users'], "users.php");
  app_section_end();
}

$count = 0;
if(app_access_check("departments")) $count++;
if(app_access_check("patients")) $count++;
if($count > 0)
{
  app_section_start($app_str['sectiondepartmentsetup']);
  if(app_access_check("departments")) app_link_app($captions['departments'],"departments.php");
  if(app_access_check("patients")) app_link_app($captions['patients'],"patients.php");
  app_section_end();
}

app_end();
?>
