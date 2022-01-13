<?php
// edit-password.php (utf-8)
// 
// Edited by: RR-DSE
// Timestamp: 22-01-12 19:57:48

//---
//App init
//---

require("app-base.php");
require("tools.php");

$app_id = "editpassword";
$app_curr = "edit-password.php";
$app_outofdate = true;
$session_id = app_get_session();
$session_needed = true;
$app_str = $app_editpassword_str;
actionsetup();

$app_nav_array = array( 
  array($captions['portal'], "portal.php", ""),
  array($captions['setup'], "setup.php", ""), 
  array($captions['logout'], "logout.php", "")
);

$app_res = app_start();
if($app_res != $res['ok'])
  app_end();

//---
//Main routine
//---

if($action == "edit")
{
  loadnew();
  formedit();
}

else if($action == 'update')
{
  loadpost();

  if(validate())
  {
    dbupdate();
    app_info_print($app_str['updated']);
    link_section("setup");
  }
  else
  {
    loadnew();
    formedit();
  }
}

app_end();

//---
//Initialization methods
//---	

function formprepare()
{
  global $action, $data;
  global $app_str;
  
  $form_action = null;
  if($action == "edit")
    $form_action = array('action' => "update", 'cancelredirect' => "setup.php");
  else if($action == "update")
    $form_action = array('action' => "update", 'cancelredirect' => "setup.php");
  
  $form_elements = array(
    array(
      'type' => "password",
      'name' => "oldpass",
      'caption' => $app_str['oldpass'],
      'value' => $data['oldpass'],
      'size' => "medium",
      'maxlength' => "password",
      'style' => "medium"
    ),
    array(
      'type' => "password",
      'name' => "newpass",
      'caption' => $app_str['newpass'],
      'value' => $data['newpass'],
      'size' => "medium",
      'maxlength' => "password",
      'style' => "medium"
    ),
    array(
      'type' => "password",
      'name' => "confirmpass",
      'caption' => $app_str['confirmpass'],
      'value' => $data['confirmpass'],
      'size' => "medium",
      'maxlength' => "password",
      'style' => "medium"
    ),
    array(
      'type' => "cell_start",
      'style' => "left",
      'caption' => ""
    ),
    array(
      'type' => "submit_add",
      'name' => "submit",
      'value' => $app_str['actionupdate'],
      'class' => "bold"
    ),
    array(
      'type' => "space_add",
      'space' => 2
    ),
    array(
      'type' => "button_add",
      'name' => "cancel",
      'value' => $app_str['actioncancel'],
      'class' => "bold"
    ),
    array(
      'type' => "cell_end"
    )
  );
  
  return array(
    'action' => $form_action,
    'elements' => $form_elements
  );
}

function actionsetup()
{
  global $action, $app_header_title;
  global $app_str;
  
  $action = getget("action", FALSE);
  if($action == "" or $action == null)
    $action = "edit";
  if(
    ($action == "update" or $action == "add" or $action == "new")
    and checkget("cancelredirect")
    and checkformpost("cancel"))
      $action = "edit";
  
  $app_header_title = $app_str["update"];
}

//---
//Data manipulation methods
//---	

function loadnew()
{
  global $data;

  $data['oldpass'] = "";
  $data['newpass'] = "";
  $data['confirmpass'] = "";
}

function loadpost()
{
  global $data;
  
  $data['oldpass'] = getformpost("oldpass", FALSE);
  $data['newpass'] = getformpost("newpass", FALSE);
  $data['confirmpass'] = getformpost("confirmpass", FALSE);
}

function dbupdate()
{
  global $data, $session_data;

  $query = "
    UPDATE users
      SET password = md5(" . getstringdb($data['newpass'], "string", FALSE) . ") 
      WHERE id={$session_data['user']}
  ";
  $db_query_res = app_db_query($query);
  app_db_free($db_query_res);
}

//---
//Support functions and methods
//---	

function link_section($link)
{
  global $captions;

  app_section_start(null);
  app_link_app($captions[$link], $link.'.php', null);
  app_section_end();
}

//---
//Input data validation
//---	

function validate()
{
  global $data, $session_data;
  global $app_limits;
  global $app_str;
  global $status;
  
  reseterrorcaptions();

  if(!validatestring($data['oldpass'], $app_limits['password_min'], $app_limits['password'], $app_str['passwordvalidchars']))
  {
    app_warning_print($app_str['errorpassword']);
    errorcaptionadd("oldpass");
    return FALSE;
  }
  if(!validatestring($data['newpass'], $app_limits['password_min'], $app_limits['password'], $app_str['passwordvalidchars']))
  {
    app_warning_print($app_str['errorpassword']);
    errorcaptionadd("newpass");
    return FALSE;
  }
  if(!validatestring($data['confirmpass'], $app_limits['password_min'], $app_limits['password'], $app_str['passwordvalidchars']))
  {
    app_warning_print($app_str['errorpassword']);
    errorcaptionadd("confirmpass");
    return FALSE;
  }
  if($data['newpass'] != $data['confirmpass'])
  {
    app_warning_print($app_str['errorpasswordcheckfailed']);
    errorcaptionadd("confirmpass");
    return FALSE;
  }
  
  $query = "
    SELECT
      password AS Pass,
      md5('{$data['oldpass']}') AS OldHash
    FROM users
    WHERE id={$session_data['user']}
  ";
  $db_query_res = app_db_query($query);
  if(app_db_numrows($db_query_res) == 0)
  {
    app_error_print($app_str['errornoitems']);
    app_db_free($db_query_res);
    return FALSE;
  }
  $db_query_row = app_db_fetch($db_query_res);
  $curr_password = $db_query_row['Pass'];
  $old_password = $db_query_row['OldHash'];
  if($curr_password != $old_password)
  {
    app_warning_print($app_str['errorinvalidpassword']);
    app_db_free($db_query_res);
    errorcaptionadd("oldpass");
    return FALSE;
  }
  app_db_free($db_query_res);

  return TRUE;
}

//---
//Forms and visual output
//---	

function formedit()
{
  $form = formprepare();

  app_form(
    "",
    1,
    "edit-password.php",
    $form['action'],
    "large",
    "",
    $form['elements']
  );
}

?>
