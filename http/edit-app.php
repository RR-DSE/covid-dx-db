<?php
// edit-app.php (utf-8)
// 
// Edited by: RR-DSE
// Timestamp: 22-01-12 19:55:27

//---
//App init
//---

require("app-base.php");
require("tools.php");

$app_id = "editapp";
$app_curr = "edit-app.php";
$app_outofdate = true;
$session_id = app_get_session();
$session_needed = true;
$app_str = $app_editapp_str;
actionsetup();

$app_nav_array = array( 
  array($captions['portal'], "portal.php", ""),
  array($captions['setup'], "setup.php", ""), 
  array($captions['apps'], "apps.php", ""),
  array($captions['logout'], "logout.php", "")
);

$app_res = app_start();
if($app_res != $res['ok'])
  app_end();

//---
//Main routine
//---

if($action == "new")
{
  loadnew();
  formedit();
  link_section("apps");
}

else if($action == "add")
{
  loadpost();
  if(validate())
  {
    dbinsert();
    app_info_print($app_str['added']);
  }
  else
    formedit();
  link_section("apps");
}

else if($action == "edit")
{
  loaddb();
  formoptions();
  formedit();
  link_section("apps");
}

else if($action == "update") 
{
  loadpost();
  if(validate())
  {
    dbupdate();
    app_info_print($app_str['updated']);
    $action = "edit";
    loaddb();
    formoptions();
    formedit();
    link_section("apps");
  }
  else
  {
    formoptions();
    formedit();
    link_section("apps");
  }
}

else if($action == "delete")
{
  loadpost();
  if(validate())
  {
    dbdelete();
    app_info_print($app_str['deleted']);
    link_section("apps");
  }
  else
  {
    $action = "edit";
    formoptions();
    formedit();
    link_section("apps");
  }
}

app_end();

//---
//Initialization methods
//---	

function formprepare()
{
  global $action, $item, $data, $dependencies;
  global $app_str;

  $form_action = null;
  if($action == "new")
    $form_action = array('action' => "add", 'cancelredirect' => "apps.php");
  else if($action == "add")
    $form_action = array('action' => "add", 'cancelredirect' => "apps.php");
  else if($action == "edit")
    $form_action = array('action' => "update", 'item' => $item, 'cancelredirect' => "apps.php");
  else if($action == "update")
    $form_action = array('action' => "update", 'item' => $item, 'cancelredirect' => "apps.php");
  
  $form_action_strkey = "actionadd";
  if($action == "edit" or $action == "update")
    $form_action_strkey = "actionupdate";

  $form_elements = array(
    array(
      'type' => "text",
      'name' => "code",
      'caption' => $app_str['id'],
      'value' => $data['code'],
      'size' => "medium",
      'maxlength' => "id",
      'style' => "medium"
    ),
    array(
      'type' => "text",
      'name' => "title",
      'caption' => $app_str['title'],
      'value' => $data['title'],
      'size' => "large",
      'maxlength' => "title",
      'style' => "medium"
    ),
    array(
      'type' => "textarea",
      'name' => "desc",
      'caption' => $app_str['description'],
      'value' => $data['desc'],
      'rows' => "small",
      'columns' => "medium",
      'maxlength' => "desc",
      'style' => "medium"
    ),
    array(
      'type' => "text",
      'name' => "location",
      'caption' => $app_str['location'],
      'value' => $data['location'],
      'size' => "large",
      'maxlength' => "position",
      'style' => "medium"
    ),
    array(
      'type' => "check",
      'name' => "admin",
      'caption' => $app_str['adminonly'],
      'value' => "admin",
      'text' => "",
      'check' => $data['admin']
    ),
    array(
      'type' => "cell_start",
      'style' => "left",
      'caption' => ""
    ),
    array(
      'type' => "submit_add",
      'name' => "submit",
      'value' => $app_str[$form_action_strkey],
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
  global $action, $item, $app_header_title;
  global $app_str;

  $action = getget("action", FALSE);
  $item = getget("item", FALSE);
  
  if($item == "" or $item == null)
  {
    if($action != "add")
      $action = "new";
    $item = "";
  }
  if($action == "" or $action == null)
  {
    if($item != "")
      $action = "edit";
    else
    {
      $action = "new";
      $item = "";
    }
  }
  if($action == "options")
  {
    if(checkformpost("delete"))
      $action = "delete";
    else if(checkformpost("enable"))
      $action = "enable";
    else
      $action = "disable";
  }
  if($action == "confirmdelete")
  {
    if(checkformpost("delete"))
      $action = "confirmeddelete";
    else
      $action = "edit";
  }
  if(
    ($action == "update" or $action == "add" or $action == "new")
    and checkget("cancelredirect")
    and checkformpost("cancel"))
      $action = "new";
  
  $app_header_title = $app_str[$action];
}

//---
//Data manipulation methods
//---	

function loadnew()
{
  global $data;
  global $status, $dependencies;

  $data['code'] = "";
  $data['title'] = "";
  $data['desc'] = "";
  $data['location'] = "";
  $data['admin'] = "0";
}

function loadpost()
{
  global $action, $data, $status;
  global $dependencies;
  
  $data['code'] = getformpost("code", FALSE);
  $data['title'] = getformpost("title", FALSE);
  $data['desc'] = getformpost("desc", FALSE);
  $data['location'] = getformpost("location", FALSE);
  $data['admin'] = getformpost("admin", TRUE);
}

function loaddb()
{
  global $item;
  global $data;
  global $str;
  
  $query = "
    SELECT
      A.code AS Code,
      A.title AS Title,
      A.description AS 'Desc',
      A.location AS Location,
      A.admin + 0 AS Admin
    FROM apps AS A
    WHERE A.id = $item
  ";
  $db_query_res = app_db_query($query);
  if(app_db_numrows($db_query_res) == 0)
  {
    app_error_print($str['error_db_query']);
    app_db_free($db_query_res);
    app_end();
  }
  $db_query_row = app_db_fetch($db_query_res);
  $data['code'] = getformdb($db_query_row, "Code", FALSE);
  $data['title'] = getformdb($db_query_row, "Title", FALSE);
  $data['desc'] = getformdb($db_query_row, "Desc", FALSE);
  $data['location'] = getformdb($db_query_row, "Location", FALSE);
  $data['admin'] = getformdb($db_query_row, "Admin", TRUE);
  app_db_free($db_query_res);
}

function dbinsert()
{
  global $data;

  $query = "
    INSERT INTO apps VALUES (
      NULL, "
      .getstringdb($data['code'], "string", FALSE) .", "
      .getstringdb($data['title'], "string", FALSE) .", "
      .getstringdb($data['desc'], "string", TRUE) .", "
      .getstringdb($data['location'], "string", FALSE) .", "
      .getstringdb($data['admin'], "boolean", FALSE) ." 
    )
  ";
  $db_query_res = app_db_query($query);
  app_db_free($db_query_res);
}

function dbupdate()
{
  global $item, $data;
  
  $query = "
    UPDATE apps
    SET
      code = " . getstringdb($data['code'], "string", FALSE) .", 
      title = " . getstringdb($data['title'], "string", FALSE) .", 
      description = " . getstringdb($data['desc'], "string", TRUE) .", 
      location = " . getstringdb($data['location'], "string", FALSE) .", 
      admin = " . getstringdb($data['admin'], "boolean", FALSE) ." 
    WHERE id = $item
  ";
  $db_query_res = app_db_query($query);
  app_db_free($db_query_res);
}

function dbdelete()
{
  global $item;
  
  $query = "
    DELETE FROM apps
    WHERE id = $item
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
  app_link_app($captions[$link], $link.'.php');
  app_section_end();
}

function check_id()
{
  global $data;
  global $app_limits;
  global $app_str;

  $data['code'] = strtolower($data['code']);
  $data['code'] = trim($data['code']);

  if($data['code'] == "" or $data['code'] == null)
  {
    app_warning_print($app_str['erroridneeded']);
    return false;
  }

  $len = strlen($data['code']);
  if($len > $app_limits['id'])
  {
    app_warning_print($app_str['erroridlimit']);
    return false;
  }

  $validate = true;
  for($i = 0; $i < $len; $i++)
  {
    $asc = ord($data['code'][$i]);
    if(!(($asc > 47 and $asc < 58) or ($asc > 96 and $asc < 123) or $asc == 95))
    {
      app_warning_print($app_str['erroridcharacters']);
      $validate = false;
      break;
    }
  }

  if(!$validate)
    return false;

  if($data['code'] == 'all')
  {
    app_warning_print($app_str['erroridreserved']);
    return false;
  }

  return true;
}

function check_unique_id()
{
  global $item, $data;
  global $app_str;
  
  $query = "
    SELECT
      id,
      code
    FROM apps
    WHERE
      code = '{$data['code']}'
      AND id != $item
    ";
  $db_query_res = app_db_query($query);
  if(app_db_numrows($db_query_res) > 0)
  {
    app_warning_print($app_str['erroridalreadyexists']);
    app_db_free($db_query_res);
    return false;
  }
  app_db_free($db_query_res);
  return true;
}

//---
//Input data validation
//---	

function validate()
{
  global $action, $item, $data;
  global $app_str;
  
  reseterrorcaptions();

  $data['code'] = app_db_clearquotes($data['code']);
  $data['title'] = app_db_clearquotes($data['title']);
  $data['desc'] = app_db_clearquotes($data['desc']);
  $data['location'] = app_db_clearquotes($data['location']);

  if($action == "delete")
    return true;

  if(!check_id())
  {
    errorcaptionadd("code");
    return false;
  }

  if($action == "add")
  {
    $query = "
      SELECT id
      FROM apps
      WHERE
        code = '{$data['code']}'
    ";
    $db_query_res = app_db_query($query);
    if(app_db_numrows($db_query_res) > 0)
    {
      app_warning_print($app_str['erroridalreadyexists']);
      app_db_free($db_query_res);
      errorcaptionadd("code");
      return false;
    }
    app_db_free($db_query_res);
  }

  if($item != "" and !check_unique_id())
  {
    errorcaptionadd("code");
    return false;
  }

  if($data['title'] == "" or $data['title'] == null)
  {
    app_warning_print($app_str['errortitleneeded']);
    errorcaptionadd("title");
    return false;
  }

  if($data['location'] == "" or $data['location'] == null)
  {
    app_warning_print($app_str['errorlocationneeded']);
    errorcaptionadd("location");
    return false;
  }
  
  return true;
}

//---
//Forms and visual output
//---	

function formedit()
{
  global $action;
  global $app_str;

  $form = formprepare();

  if($action == "edit" or $action == "add" or $action == "new" or $action == "update")
  {
    app_form(
      "",
      1,
      "edit-app.php",
      $form['action'],
      "medium",
      "",
      $form['elements']);
  }
}

function formoptions()
{
  global $item, $data;
  global $status;
  global $app_str;

  $form_elements = array();
  $form_elements[] = array(
    'type' => "cell_start",
    'caption' => $app_str['options'],
    'class' => "options",
    'captionclass' => "options"
  );
  $form_elements[] = array(
    'type' => "button_add",
    'name' => "delete",
    'value' => $app_str['actiondelete'],
    'class' => "options"
  );
  $form_elements[] = array(
    'type' => "cell_end"
  );
  
  app_form(
    "",
    1,
    "edit-app.php",
    array('action' => "options", 'item' => $item),
    "medium",
    "",
    $form_elements,
    True
  );
}

?>
