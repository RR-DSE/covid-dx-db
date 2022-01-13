<?php
// edit-test.php (utf-8)
// 
// Edited by: RR-DSE
// Timestamp: 22-01-12 19:59:22

//---
//App init
//---

require("app-base.php");
require("tools.php");

$app_id = "edittest";
$app_curr = "edit-test.php";
$app_outofdate = true;
$session_id = app_get_session();
$session_needed = true;
$app_str = $app_edittest_str;
$app_layout = $app_edittest_layout;
actionsetup();

$app_nav_array = array( 
  array($captions['portal'], "portal.php", ""),
  array($captions['setup'], "setup.php", ""), 
  array($captions['tests'], "tests.php", ""),
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
  loaddb();
  formoptions();
  formedit();
}

else if($action == "update")
{
  loaddb();
  loadpost();
  if(validate())
  {
    $action = "edit";
    loaddb();
    formoptions();
    formedit();
    link_section("tests");
  }
  else
  {
    formoptions();
    formedit();
  }
}

else if($action == "delete")
{
  loaddb();
  if(validate())
  {
    formedit();
  }
  else
  {
    $action = "edit";
    formoptions();
    formedit();
  }
}

else if($action == "disable")
{
  if(validate())
  {
    dbdisable();
    loaddb();
    app_info_print($app_str['disabled']);
  }
  link_section("tests");
}

else if($action == "enable")
{
  dbenable();
  app_info_print($app_str['enabled']);
  $action = "edit";
  loaddb();
  formoptions();
  formedit();
  link_section("tests");
}

else if($action == "confirmeddelete")
{
  loadpost();
  dbdelete();
  app_info_print($app_str['deleted']);
  link_section("tests");
}

else
{
  //app_error_print($app_str['errorinvalidaction']);
}

app_end();

//---
//Initialization methods
//---	

function formprepare()
{
  global $action, $mode, $item, $data, $dependencies;
  global $app_str;

  $form_action = null;
  if($action == "new")
    $form_action = array('action' => "add", 'cancelredirect' => "tests.php");
  else if($action == "add")
    $form_action = array('action' => "add", 'cancelredirect' => "tests.php");
  else if($action == "edit")
    $form_action = array('action' => "update", 'item' => $item, 'cancelredirect' => "tests.php");
  else if($action == "update")
    $form_action = array('action' => "update", 'item' => $item, 'cancelredirect' => "tests.php");
  else if($action == "delete")
    $form_action = array('action' => "confirmdelete", 'item' => $item);
  
  $form_action_strkey = "actionadd";
  if($action == "edit" or $action == "update")
    $form_action_strkey = "actionupdate";
  if($action == "delete")
    $form_action_strkey = "actiondelete";

  $form_elements = null;
  if($action == "edit" or $action == "add" or $action == "new" or $action == "update")
  {
    $form_elements = array();

    $readonly = True;
    $disabled = True;
    $form_elements[] =
      array(
        'type' => "text",
        'name' => "name",
        'caption' => $app_str['name'],
        'value' => $data['name'],
        'size' => "medium",
        'maxlength' => "title",
        'style' => "medium",
        'readonly' => $readonly
      );
    $form_elements[] =
      array(
        'type' => "text",
        'name' => "birthday",
        'caption' => $app_str['birthday'],
        'value' => $data['birthday'],
        'size' => "small",
        'maxlength' => "date",
        'style' => "small",
        'readonly' => $readonly
      );
    $form_elements[] =
      array(
        'type' => "text",
        'name' => "gender",
        'caption' => $app_str['gender'],
        'value' => GetGender($data['gender']),
        'size' => "small",
        'maxlength' => "gender",
        'style' => "small",
        'readonly' => $readonly
      );
    $form_elements[] =
      array(
        'type' => "text",
        'name' => "record",
        'caption' => $app_str['record'],
        'value' => $data['record'],
        'size' => "small",
        'maxlength' => "record",
        'style' => "small",
        'readonly' => $readonly
      );
    $form_elements[] =
      array(
        'type' => "text",
        'name' => "stateid1",
        'caption' => $app_str['stateid1'],
        'value' => $data['stateid1'],
        'size' => "small",
        'maxlength' => "record",
        'style' => "small",
        'readonly' => $readonly
      );
    $form_elements[] =
      array(
        'type' => "text",
        'name' => "department",
        'caption' => $app_str['department'],
        'value' => $data['department'],
        'size' => "medium",
        'maxlength' => "title",
        'style' => "medium",
        'readonly' => $readonly
      );
    $form_elements[] =
      array(
        'type' => "text",
        'name' => "sampleid",
        'caption' => $app_str['sampleid'],
        'value' => $data['sampleid'],
        'size' => "small",
        'maxlength' => "record",
        'style' => "small",
        'readonly' => $readonly
      );
    $form_elements[] =
      array(
        'type' => "text",
        'name' => "sampledate",
        'caption' => $app_str['sampledate'],
        'value' => $data['sampledate'],
        'size' => "small",
        'maxlength' => "date",
        'style' => "small",
        'readonly' => $readonly
      );
    $form_elements[] =
      array(
        'type' => "text",
        'name' => "result",
        'caption' => $app_str['result'],
        'value' => GetResult($data['result']),
        'size' => "small",
        'maxlength' => "title",
        'style' => "small",
        'readonly' => $readonly
      );
    $form_elements[] =
      array(
        'type' => "text",
        'name' => "resultdatetime",
        'caption' => $app_str['resultdatetime'],
        'value' => $data['resultdatetime'],
        'size' => "small",
        'maxlength' => "datetime",
        'style' => "small",
        'readonly' => $readonly
      );
    $form_elements[] =
      array(
        'type' => "textarea",
        'name' => "address_1",
        'caption' => $app_str['address1'],
        'value' => $data['address_1'],
        'rows' => "small",
        'columns' => "medium",
        'maxlength' => "address_1",
        'style' => "medium",
        'readonly' => $readonly
      );
    $form_elements[] =
      array(
        'type' => "text",
        'name' => "address_2",
        'caption' => $app_str['address2'],
        'value' => $data['address_2'],
        'size' => "medium",
        'maxlength' => "address_2",
        'style' => "medium",
        'readonly' => $readonly
      );
    $form_elements[] =
      array(
        'type' => "cell_start",
        'style' => "left",
        'caption' => ""
      );
    $form_elements[] =
      array(
        'type' => "button_add",
        'name' => "cancel",
        'value' => $app_str['actioncancel'],
        'class' => "bold"
      );
    $form_elements[] =
      array(
        'type' => "cell_end"
      );
  }
  else if($action == "delete")
  {
    $form_elements = array(
      array(
        'type' => "textarea",
        'name' => "status_notes",
        'caption' => "no_caption",
        'value' => $data['status_notes'],
        'rows' => "small",
        'columns' => "medium",
        'maxlength' => "notes",
        'style' => "medium"
      ),
      array(
        'type' => "cell_start",
        'style' => "right",
        'caption' => "no_caption"
      ),
      array(
        'type' => "submit_add",
        'name' => "delcancel",
        'value' => $app_str['actioncancel'],
        'class' => "bold"
      ),
      array(
        'type' => "space_add",
        'space' => 2
      ),
      array(
        'type' => "submit_add",
        'name' => "delete",
        'value' => $app_str[$form_action_strkey],
        'class' => "bold"
      ),
      array(
        'type' => "cell_end"
      )
    );
  }
  
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

function loadpost()
{
  global $action, $data, $status, $mode;

  if($action == "update")
  {
  }
  else
  {
    $data['status_notes'] = getformpost("status_notes", FALSE);
  }
}

function loaddb()
{
  global $item;
  global $data, $metadata;
  global $str, $app_regex, $status;

  $query = "
    SELECT
      B.name AS 'Name',
      date_format(B.birthday, '{$app_regex['sb_dateformat']}') AS 'Birthday',
      B.gender AS 'Gender',
      B.record AS 'Record',
      B.state_id_1 AS 'StateID1',
      A.department AS 'Department',
      A.sample_id AS 'SampleID',
      date_format(A.sample_date, '{$app_regex['sb_dateformat']}') AS 'SampleDate',
      A.result_code AS 'Result',
      date_format(A.result_datetime, '{$app_regex['sb_datetimeformat']}') AS 'ResultDateTime',
      A.state_report_1 AS '#STATE_PORTAL_2#',
      A.status AS 'Status',
      A.status_notes AS 'StatusNotes',
      A.address_1 AS 'Address1',
      A.address_2 AS 'Address2',
      A.add_user AS 'AddUser',
      C.name AS 'AddName',
      DATE_FORMAT(A.add_datetime, '{$app_regex['sb_datetimeformat']}') AS 'AddDateTime',
      A.mod_user as 'ModUser',
      D.name AS 'ModName',
      DATE_FORMAT(A.mod_datetime, '{$app_regex['sb_datetimeformat']}') AS 'ModDateTime'
    FROM tests AS A
    LEFT JOIN patients AS B
       ON B.id = A.patient_id
    LEFT JOIN users AS C
      ON C.id = A.add_user
    LEFT JOIN users AS D
      ON D.id = A.mod_user
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

  $data['name'] = getformdb($db_query_row, "Name", FALSE);
  $data['birthday'] = getformdb($db_query_row, "Birthday", FALSE);
  $data['gender'] = getgenderid(getintdb($db_query_row, "Gender", FALSE));
  $data['record'] = getformdb($db_query_row, "Record", FALSE);
  $data['stateid1'] = getformdb($db_query_row, "StateID1", FALSE);
  $data['department'] = getformdb($db_query_row, "Department", FALSE);
  $data['sampleid'] = getformdb($db_query_row, "SampleID", FALSE);
  $data['sampledate'] = getformdb($db_query_row, "SampleDate", FALSE);
  $data['result'] = getformdb($db_query_row, "Result", FALSE);
  $data['resultdatetime'] = getformdb($db_query_row, "ResultDateTime", FALSE);
  $data['stateportal2'] = getformdb($db_query_row, "#STATE_PORTAL_2#", TRUE);
  $data['address_1'] = getformdb($db_query_row, "Address1", FALSE);
  $data['address_2'] = getformdb($db_query_row, "Address2", FALSE);
  $data['status'] = getintdb($db_query_row, "Status", FALSE);
  $data['status_notes'] = getformdb($db_query_row, "StatusNotes", FALSE);
  $metadata['adduser'] = getformdb($db_query_row, "AddUser", FALSE);
  $metadata['addusername'] = getformdb($db_query_row, "AddName", FALSE);
  $metadata['adddatetime'] = getformdb($db_query_row, "AddDateTime", FALSE);
  $metadata['moduser'] = getformdb($db_query_row, "ModUser", FALSE);
  $metadata['modusername'] = getformdb($db_query_row, "ModName", FALSE);
  $metadata['moddatetime'] = getformdb($db_query_row, "ModDateTime", FALSE);
  
  app_db_free($db_query_res);
}

function dbupdate()
{
  global $item;
  global $data;
  global $session_data;
  
  dbauditinsert();

  $query = "
    UPDATE tests
    SET
      state_report_1 = " . getstringdb($data['stateportal2'], "boolean", TRUE) .", 
      state_report_1_datetime = NOW(),
      mod_user = {$session_data['user']},
      mod_datetime = NOW()
    WHERE id = $item
    ";
  $db_query_res = app_db_query($query);
  app_db_free($db_query_res);
}

function dbdelete()
{
  global $item, $data;
  global $session_data;
  global $status;
  
  dbauditinsert();
  
  $query = "
    UPDATE tests
    SET
      status = {$status['deleted']},
      status_notes = " . getstringdb($data['status_notes'], "string", TRUE) .", 
      mod_user = {$session_data['user']},
      mod_datetime = NOW()
    WHERE id = $item
    ";
  $db_query_res = app_db_query($query);
  app_db_free($db_query_res);
}

function dbdisable()
{
  global $item;
  global $session_data;
  global $status;
  
  dbauditinsert();
  
  $query = "
    UPDATE tests
    SET
      status = {$status['inactive']},
      mod_user = {$session_data['user']},
      mod_datetime = NOW()
    WHERE id = $item
    ";
  $db_query_res = app_db_query($query);
  app_db_free($db_query_res);
}

function dbenable()
{
  global $item;
  global $session_data;
  global $status;
  global $str;
  
  dbauditinsert();

  $query = "
    SELECT id
    FROM tests
    WHERE
      id = $item
      AND status = {$status['inactive']}
    ";
  $db_query_res = app_db_query($query);
  if(app_db_numrows($db_query_res) == 0)
  {
    app_error_print($str['error_db_query']);
    app_db_free($db_query_res);
    app_end();
  }
  app_db_free($db_query_res);

  $query = "
    UPDATE tests
    SET
      status = {$status['active']},
      mod_user = {$session_data['user']},
      mod_datetime = NOW()
    WHERE id = $item
    ";
  $db_query_res = app_db_query($query);
  app_db_free($db_query_res);
}

function dbauditinsert()
{
  global $item;

  $query = "
    INSERT audit_tests
    SELECT * FROM tests WHERE id=$item
    ";
  $db_query_res = app_db_query($query);
  app_db_free($db_query_res);
}

//---
//Support functions and methods
//---	

function GetGender($gender)
{
  global $app_str;

  return $app_str[$gender];
}

function GetResult($result)
{
  global $app_str;

  return $app_str[$result];
}

function link_section($link)
{
  global $data, $captions;

  $params = null;
  app_section_start(null);
  app_link_app($captions[$link], $link.'.php', $params);
  app_section_end();
}

//---
//Input data validation
//---	

function validate()
{
  global $action, $item, $data;
  global $status, $app_limits, $app_str;
  global $mode;
  global $dependencies;
  
  reseterrorcaptions();
  
  return TRUE;
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
      "edit-test.php",
      $form['action'],
      "large",
      "",
      $form['elements']);
  }
  else if($action == "delete")
    app_form(
      $app_str['delmotive'],
      1,
      "edit-test.php",
      $form['action'],
      "large",
      "",
      $form['elements']);

  if($action == "edit")
    info();
}

function formoptions()
{
  global $mode;
  global $item, $data;
  global $status;
  global $app_str, $app_layout;

  if($mode == "cons" or $mode == "lab")
    return;

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
    "edit-test.php",
    array('action' => "options", 'item' => $item),
    "medium",
    "",
    $form_elements,
    True
  );
}

function info()
{
  global $metadata;
  global $app_str;

  return;

  $elements = array();
  $elements[] = array(
    'caption' => $app_str['adduser'],
    'content' => $metadata['addusername']
  );
  $elements[] = array(
    'caption' => $app_str['adddatetime'],
    'content' => $metadata['adddatetime']
  );
  $elements[] = array(
    'caption' => $app_str['moduser'],
    'content' => $metadata['modusername']
  );
  $elements[] = array(
    'caption' => $app_str['moddatetime'],
    'content' => $metadata['moddatetime']
  );

  app_infotable($app_str['info'], "large", $elements);
}

?>
