<?php
// login.php (utf-8)
// 
// Edited by: RR-DSE
// Timestamp: 22-01-12 20:03:27

//---
//App init
//---

require('app-base.php');
require('tools.php');

$app_id = 'app_login';
$app_curr = 'login.php';
$session_needed = false;
$app_header_title = $captions['login'];
$app_str = $app_login_str;

$app_res = app_init();
if($app_res != $res['ok'])
  app_end();

//---
//Main routine
//---

$data = array();
$data['username'] = "";
$data['password'] = "";

if(checkformpost("submit", $app_str['actionsubmit']))
{
  $data['username'] = getformpost("username");
  $data['password'] = getformpost("password");

  $username = $data['username'];
  $password = $data['password'];

  $query = "
    SELECT id AS User
    FROM users
    WHERE username='$username'
      AND password=md5('$password')
      AND status={$status['active']}";
  $db_query_res = app_db_query($query);
  $db_query_rowcount = app_db_numrows($db_query_res);
  if($db_query_rowcount == 0)
  {
    app_db_free($db_query_res);
    app_header_echo();
    app_warning_print($app_str['errorloginfailed']);
    $data['password'] = "";
    formedit();
    app_end();
  }
  else
  {
    $res_row = app_db_fetch($db_query_res);
    $session_user = getformdb($res_row, "User");
    $session_id = md5($username);
    app_db_free($db_query_res);
    
    $query = "
      REPLACE INTO sessions
      VALUES($session_user, '$session_id', '$user_ip', 'online', NOW())";
    $db_query_res = app_db_query($query);
    app_db_free($db_query_res);

    $app_redirect = "portal.php";
    app_header_echo();

    app_info_print($app_str['infologinok']);
    
    app_section_start(null);
    app_link_app($captions['portal'], 'portal.php');
    app_section_end();
    
    app_end();
  }
}
else
{
  app_header_echo();
  formedit();
  app_end();			
}

//---
//Initialization methods
//---	

function formprepare()
{
  global $data;
  global $app_str;

  $form_action = array('action' => "login");
  
  $form_elements = array(
    array(
      'type' => "text",
      'name' => "username",
      'caption' => $app_str['username'],
      'value' => $data['username'],
      'size' => "small",
      'maxlength' => "username",
      'style' => "small"
    ),
    array(
      'type' => "password",
      'name' => "password",
      'caption' => $app_str['password'],
      'value' => $data['password'],
      'size' => "small",
      'maxlength' => "password",
      'style' => "small"
    ),
    array(
      'type' => "submit",
      'name' => "submit",
      'caption' => "",
      'value' => $app_str['actionsubmit'],
      'class' => "bold"
    )
  );

  return array(
    'action' => $form_action,
    'elements' => $form_elements
  );
}

//---
//Forms and visual output
//---	

function formedit()
{
  global $action;
  global $app_str;

  $form = formprepare();

  app_form(
    $app_str['formcaption'],
    1,
    "login.php",
    $form['action'],
    "medium",
    "",
    $form['elements'],
    True,
    False
  );
}

?>
