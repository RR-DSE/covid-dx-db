<?php
// edit-patient.php (utf-8)
// 
// Edited by: RR-DSE
// Timestamp: 22-01-12 19:58:00

//---
//App init
//---

require("app-base.php");
require("tools.php");

$app_id = "editpatient";
$app_curr = "edit-patient.php";
$app_outofdate = true;
$session_id = app_get_session();
$session_needed = true;
$app_str = $app_editpatient_str;
$app_layout = $app_editpatient_layout;
actionsetup();

$app_nav_array = array( 
  array($captions['portal'], "portal.php", ""),
  array($captions['setup'], "setup.php", ""), 
  array($captions['patients'], "patients.php", ""),
  array($captions['logout'], "logout.php", "")
);

$app_res = app_start();
if($app_res != $res['ok'])
  app_end();

modesetup();
loaddependencies();
loaddbdependencies();

//---
//Main routine
//---

if($action == "new")
{
  loadnew();
  formedit();
}

else if($action == "add")
{
  loadnew();
  loadpost();
  if(validate())
  {
    dbinsert();
    app_info_print($app_str['added']);
    link_section("patients");
  }
  else
    formedit();
}

else if($action == "edit")
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
    dbupdate();
    app_info_print($app_str['updated']);
    $action = "edit";
    loaddb();
    formoptions();
    formedit();
    link_section("patients");
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
  link_section("patients");
}

else if($action == "enable")
{
  dbenable();
  app_info_print($app_str['enabled']);
  $action = "edit";
  loaddb();
  formoptions();
  formedit();
  link_section("patients");
}

else if($action == "confirmeddelete")
{
  loadpost();
  dbdelete();
  app_info_print($app_str['deleted']);
  link_section("patients");
}

app_end();

//---
//Initialization methods
//---	

function loaddependencies()
{
  global $dependencies;
  global $app_str;
  
  $dependencies['gender_lists'] = array(
    'ids' => array(
      "undefined",
      "male",
      "female"),
    'captions' => array(
      $app_str['undefined'], 
      $app_str['male'],
      $app_str['female'])
  );

  $dependencies['status_lists'] = array(
    'ids' => array(
      "inpatient",
      "discharged",
      "deceased",
      "outpatient",
      "outpatientquarant",
      "outpatientresolved",
      "unknown"),
    'captions' => array(
      $app_str['inpatient'], 
      $app_str['discharged'],
      $app_str['deceased'],
      $app_str['outpatient'],
      $app_str['outpatientquarant'],
      $app_str['outpatientresolved'],
      $app_str['unknown'])
  );
}

function loaddbdependencies()
{
  global $dependencies;
  global $status, $app_str;

  $dependencies['department_lists'] = array(
    'ids' => array(),
    'captions' => array()
    );
  $query = "
    SELECT
      id AS ID,
      title AS Title
    FROM departments
    WHERE status = {$status['active']}
    ORDER BY title";
  $db_query_res = app_db_query($query);
  $dependencies['department_lists']['ids'][] = "none";
  $dependencies['department_lists']['captions'][] = $app_str['departmentnone'];
  while($db_query_row = app_db_fetch($db_query_res))
  {
    $dependencies['department_lists']['ids'][] = $db_query_row['ID'];
    $dependencies['department_lists']['captions'][] = $db_query_row['Title'];
  }
  app_db_free($db_query_res);

  if(count($dependencies['department_lists']['ids']) == 0)
  {
    app_warning_print($app_str['errornodepartments']);
    link_section("departments");
    app_end();
  }
}

function formprepare()
{
  global $action, $mode, $item, $data, $dependencies;
  global $app_str;

  $form_action = null;
  if($action == "new")
    $form_action = array('action' => "add", 'cancelredirect' => "patients.php");
  else if($action == "add")
    $form_action = array('action' => "add", 'cancelredirect' => "patients.php");
  else if($action == "edit")
    $form_action = array('action' => "update", 'item' => $item, 'cancelredirect' => "patients.php");
  else if($action == "update")
    $form_action = array('action' => "update", 'item' => $item, 'cancelredirect' => "patients.php");
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

    $readonly = False;
    $disabled = False;
    if($mode == "lab" or $mode == "cons")
    {
      $readonly = True;
      $disabled = True;
    }
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
        'type' => "select",
        'name' => "gender",
        'caption' => $app_str['gender'],
        'options' => $dependencies['gender_lists']['ids'],
        'optioncaptions' => $dependencies['gender_lists']['captions'],
        'value' => $data['gender'],
        'size' => 1,
        'style' => "small",
        'disabled' => $disabled
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
        'type' => "select",
        'name' => "status",
        'caption' => $app_str['status'],
        'options' => $dependencies['status_lists']['ids'],
        'optioncaptions' => $dependencies['status_lists']['captions'],
        'value' => $data['status'],
        'size' => 1,
        'style' => "medium",
        'disabled' => $disabled
      );
    $form_elements[] =
      array(
        'type' => "select",
        'name' => "department",
        'caption' => $app_str['department'],
        'options' => $dependencies['department_lists']['ids'],
        'optioncaptions' => $dependencies['department_lists']['captions'],
        'value' => $data['department'],
        'size' => 1,
        'style' => "medium",
        'disabled' => $disabled
      );
    $form_elements[] =
      array(
        'type' => "text",
        'name' => "admittancedate",
        'caption' => $app_str['admittancedate'],
        'value' => $data['admittancedate'],
        'size' => "small",
        'maxlength' => "date",
        'style' => "small",
        'readonly' => $readonly
      );
    $form_elements[] =
      array(
        'type' => "text",
        'name' => "floor",
        'caption' => $app_str['floor'],
        'value' => $data['floor'],
        'size' => "small",
        'maxlength' => "floor",
        'style' => "small",
        'readonly' => $readonly
      );
    $form_elements[] =
      array(
        'type' => "text",
        'name' => "room",
        'caption' => $app_str['room'],
        'value' => $data['room'],
        'size' => "small",
        'maxlength' => "room",
        'style' => "small",
        'readonly' => $readonly
      );
    $form_elements[] =
      array(
        'type' => "text",
        'name' => "bed",
        'caption' => $app_str['bed'],
        'value' => $data['bed'],
        'size' => "small",
        'maxlength' => "bed",
        'style' => "small",
        'readonly' => $readonly
      );
    $form_elements[] =
      array(
        'type' => "check",
        'name' => "hcworker",
        'value' => "hcworker",
        'check' => $data['hcworker'],
        'caption' => $app_str['hcworker'],
        'text' => "",
        'style' => null,
        'disabled' => $disabled
      );
    if($mode == "cons" or $mode == "lab" or $mode == "admin" or $mode == "manag2" or $mode == "")
    {
      $form_elements[] =
        array(
          'type' => "check",
          'name' => "sinave",
          'value' => "sinave",
          'check' => $data['sinave'],
          'caption' => $app_str['sinave'],
          'text' => "",
          'style' => null,
          'disabled' => $disabled
        );
    }
    $form_elements[] =
      array(
        'type' => "textarea",
        'name' => "notes",
        'caption' => $app_str['notes'],
        'value' => $data['notes'],
        'rows' => "small",
        'columns' => "medium",
        'maxlength' => "notes",
        'style' => "medium",
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
        'readonly' => True
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
        'readonly' => True
      );
    $form_elements[] =
      array(
        'type' => "cell_start",
        'style' => "left",
        'caption' => ""
      );
    if($mode != "cons")
    {
      $form_elements[] =
        array(
          'type' => "submit_add",
          'name' => "submit",
          'value' => $app_str[$form_action_strkey],
          'class' => "bold"
        );
      $form_elements[] =
        array(
          'type' => "space_add",
          'space' => 2
        );
    }
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

function modesetup()
{
  global $mode, $session_data;

  $mode = "admin";
  if($session_data['profile'] == 2)
    $mode = "lab";
  if($session_data['profile'] == 3)
    $mode = "manag";
  if($session_data['profile'] == 4)
    $mode = "cons";
  if($session_data['profile'] == 5)
    $mode = "manag2";
}

//---
//Data manipulation methods
//---	

function loadnew()
{
  global $data;
  global $status, $dependencies;
  global $mode;

  $data['name'] = "";
  $data['birthday'] = "";
  $data['gender'] = $dependencies['gender_lists']['ids'][1];
  $data['record'] = "";
  $data['stateid1'] = "";
  $data['status'] = "inpatient";
  $data['statusdatetime'] = app_get_curr_datetime();
  $data['department'] = $dependencies['department_lists']['ids'][0];
  $data['admittancedate'] = "";
  $data['floor'] = "";
  $data['room'] = "";
  $data['bed'] = "";
  $data['hcworker'] = FALSE;
  $data['address_1'] = "";
  $data['address_2'] = "";
  $data['sinave'] = FALSE;
  $data['sinavedatetime'] = app_get_curr_datetime();
  $data['notes'] = "";
  $data['status_notes'] = "";

  if($mode == "admin" or $mode == "lab" or $mode == "")
  {
    $data['status'] = "unknown";
  }

  $filterstr = getformget("filter-status", FALSE);
  if($filterstr != "" and in_array($filterstr, $dependencies['status_lists']['ids']))
    $data['status'] = $filterstr;
  
  $filterstr = getformget("filter-department", FALSE);
  if($filterstr != "" and in_array($filterstr, $dependencies['department_lists']['ids']))
    $data['department'] = $filterstr;
  
  $filterstr = getformget("filter-record", FALSE);
  if($filterstr != "")
    $data['record'] = $filterstr;
  
  $filterstr = getformget("filter-stateid1", FALSE);
  if($filterstr != "")
    $data['stateid1'] = $filterstr;
  
  $filterstr = getformget("filter-name", FALSE);
  if($filterstr != "")
    $data['name'] = $filterstr;
 
  if($mode == "admin" or $mode == "lab" or $mode == "")
  {
    if($data['record'] == "")
    {
      $data['status'] = "outpatient";
      $data['department'] = "9";
    }
  }
}

function loadpost()
{
  global $action, $data, $status, $mode;
  global $dependencies;

  if($action == "add" or $action == "update")
  {
    $data["department_original"] = $data["department"];
    $data["status_original"] = $data["status"];
    $data["sinave_original"] = $data["sinave"];
    updateformfrompost("name", $data['name'], FALSE);
    updateformfrompost("birthday", $data['birthday'], FALSE);
    updateformfrompost("gender", $data['gender'], FALSE);
    $data['record'] = getformpost("record", FALSE);
    $data['stateid1'] = getformpost("stateid1", FALSE);
    updateformfrompost("department", $data['department'], FALSE);
    updateformfrompost("admittancedate", $data['admittancedate'], FALSE);
    $data['floor'] = getformpost("floor", FALSE);
    $data['room'] = getformpost("room", FALSE);
    $data['bed'] = getformpost("bed", FALSE);
    updateformfrompost("hcworker", $data['hcworker'], TRUE);
    if($mode == "admin" or $mode == "" or $mode == "manag2")
      updateformfrompost("sinave", $data['sinave'], TRUE);
    $data['notes'] = getformpost("notes", FALSE);
    updateformfrompost("status", $data['status'], FALSE);
  }
  else
  {
    $data['status_notes'] = getformpost("status_notes", FALSE);
  }
}

function loaddb()
{
  global $item;
  global $data, $metadata, $dependencies;
  global $str, $app_regex, $status;

  $query = "
    SELECT
      A.name AS 'Name',
      date_format(A.birthday, '{$app_regex['sb_dateformat']}') AS 'Birthday',
      A.gender AS 'Gender',
      A.record AS 'Record',
      A.state_id_1 AS 'StateID1',
      A.patient_status AS 'PatientStatus',
      DATE_FORMAT(A.patient_status_datetime, '{$app_regex['sb_datetimeformat']}') AS 'PatientStatusDatetime',
      A.department AS 'Department',
      D.status AS 'DepartmentStatus',
      D.title AS 'DepartmentTitle',
      date_format(A.admittance_date, '{$app_regex['sb_dateformat']}') AS 'AdmittanceDate',
      A.floor AS 'Floor',
      A.room AS 'Room',
      A.bed AS 'Bed',
      A.hcworker AS 'HCWorker',
      A.sinave AS 'Sinave',
      date_format(A.sinave_datetime, '{$app_regex['sb_datetimeformat']}') AS 'SinaveDatetime',
      F.address_1 AS 'Address1',
      F.address_2 AS 'Address2',
      A.notes AS 'Notes',
      A.status AS 'Status',
      A.status_notes AS 'StatusNotes',
      A.add_user AS 'AddUser',
      B.name AS 'AddName',
      DATE_FORMAT(A.add_datetime, '{$app_regex['sb_datetimeformat']}') AS 'AddDateTime',
      A.mod_user as 'ModUser',
      C.name AS 'ModName',
      DATE_FORMAT(A.mod_datetime, '{$app_regex['sb_datetimeformat']}') AS 'ModDateTime'
    FROM patients AS A
    LEFT JOIN users AS B
      ON A.add_user = B.id
    LEFT JOIN users AS C
      ON A.mod_user = C.id
    LEFT JOIN departments AS D
      ON D.id = A.department
    LEFT JOIN (
      SELECT
        patient_id AS PatientID,
        MAX(id) AS MaxTestID
      FROM tests
      GROUP BY patient_id
    ) AS E
      ON E.PatientID = A.id
    LEFT JOIN tests AS F
      ON F.id = E.MaxTestID
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
  $data['status'] = getformdb($db_query_row, "PatientStatus", FALSE);
  $data['statusdatetime'] = getformdb($db_query_row, "PatientStatusDatetime", FALSE);
  $data['department'] = getformdb($db_query_row, "Department", FALSE);
  $data['departmentstatus'] = getintdb($db_query_row, "DepartmentStatus", FALSE);
  $data['departmenttitle'] = getformdb($db_query_row, "DepartmentTitle", FALSE);
  $data['admittancedate'] = getformdb($db_query_row, "AdmittanceDate", FALSE);
  $data['floor'] = getformdb($db_query_row, "Floor", FALSE);
  $data['room'] = getformdb($db_query_row, "Room", FALSE);
  $data['bed'] = getformdb($db_query_row, "Bed", FALSE);
  $data['hcworker'] = getformdb($db_query_row, "HCWorker", TRUE);
  $data['sinave'] = getformdb($db_query_row, "Sinave", TRUE);
  $data['sinavedatetime'] = getformdb($db_query_row, "SinaveDatetime", FALSE);
  $data['notes'] = getformdb($db_query_row, "Notes", FALSE);
  $data['address_1'] = getformdb($db_query_row, "Address1", FALSE);
  $data['address_2'] = getformdb($db_query_row, "Address2", FALSE);
  $data['status_notes'] = getformdb($db_query_row, "StatusNotes", FALSE);
  $metadata['adduser'] = getformdb($db_query_row, "AddUser", FALSE);
  $metadata['addusername'] = getformdb($db_query_row, "AddName", FALSE);
  $metadata['adddatetime'] = getformdb($db_query_row, "AddDateTime", FALSE);
  $metadata['moduser'] = getformdb($db_query_row, "ModUser", FALSE);
  $metadata['modusername'] = getformdb($db_query_row, "ModName", FALSE);
  $metadata['moddatetime'] = getformdb($db_query_row, "ModDateTime", FALSE);
  
  if($data['department'] != "" and $data['departmentstatus'] != $status['active'])
  {
    $dependencies['department_lists']['ids'][] = $data['department'];
    $dependencies['department_lists']['captions'][] = $data['departmenttitle'];
  }

  if($data['department'] == "")
    $data['department'] = $dependencies['department_lists']['ids'][0];

  app_db_free($db_query_res);
}

function dbinsert()
{
  global $data;
  global $session_data;
  global $gender;
  global $dependencies;

  $patientgender = $gender[$data['gender']];

  $department = $data['department'];
  if($data['department'] == $dependencies['department_lists']['ids'][0])
    $department = "";

  $query = "
    INSERT INTO patients VALUES(
      NULL, "
      .getstringdb($data['name'], "string", FALSE) .", "
      .getstringdb($data['birthday'], "date", FALSE) .", "
      .getstringdb($patientgender, "numeric", FALSE) .", "
      .getstringdb($data['record'], "string", TRUE) .", "
      .getstringdb($data['stateid1'], "string", TRUE) .", "
      .getstringdb($data['status'], "string", FALSE) .", "
      .getstringdb($data['statusdatetime'], "datetime", FALSE) .", "
      .getstringdb($department, "numeric", TRUE) .", "
      .getstringdb($data['admittancedate'], "date", TRUE) .", "
      .getstringdb($data['floor'], "string", TRUE) .", "
      .getstringdb($data['room'], "string", TRUE) .", "
      .getstringdb($data['bed'], "string", TRUE) .", "
      .getstringdb($data['hcworker'], "boolean", TRUE) .", "
      .getstringdb($data['sinave'], "boolean", TRUE) .", "
      .getstringdb($data['sinavedatetime'], "datetime", TRUE) .", "
      .getstringdb($data['notes'], "string", TRUE) . ", 
      1, NULL,
      {$session_data['user']},
      NOW(),
      {$session_data['user']},
      NOW()
    )
    ";
  $db_query_res = app_db_query($query);
  app_db_free($db_query_res);
}

function dbupdate()
{
  global $item;
  global $data;
  global $session_data;
  global $gender;
  global $dependencies;
  
  $patientgender = $gender[$data['gender']];
  
  $department = $data['department'];
  if($data['department'] == $dependencies['department_lists']['ids'][0])
    $department = "";

  dbauditinsert();

  $query = "
    UPDATE patients
    SET
      name = " . getstringdb($data['name'], "string", FALSE) .", 
      birthday = " . getstringdb($data['birthday'], "date", FALSE) .", 
      gender = " . getstringdb($patientgender, "numeric", FALSE) .", 
      record = " . getstringdb($data['record'], "string", TRUE) .", 
      state_id_1 = " . getstringdb($data['stateid1'], "string", TRUE) .", 
      patient_status = " . getstringdb($data['status'], "string", FALSE) .", 
      patient_status_datetime = " . getstringdb($data['statusdatetime'], "datetime", FALSE) .", 
      department = " . getstringdb($department, "numeric", TRUE) .", 
      admittance_date = " . getstringdb($data['admittancedate'], "date", TRUE) .", 
      floor = " . getstringdb($data['floor'], "string", TRUE) .", 
      room = " . getstringdb($data['room'], "string", TRUE) .", 
      bed = " . getstringdb($data['bed'], "string", TRUE) .", 
      hcworker = " . getstringdb($data['hcworker'], "boolean", TRUE) .", 
      sinave = " . getstringdb($data['sinave'], "boolean", TRUE) .", 
      sinave_datetime = " . getstringdb($data['sinavedatetime'], "datetime", TRUE) .", 
      notes = " . getstringdb($data['notes'], "string", TRUE) .", 
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
    UPDATE patients
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
    UPDATE patients
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
    FROM patients
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
    UPDATE patients
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
    INSERT audit_patients
    SELECT * FROM patients WHERE id=$item
    ";
  $db_query_res = app_db_query($query);
  app_db_free($db_query_res);
}

//---
//Support functions and methods
//---

function link_section($link)
{
  global $data, $captions;

  $params = null;
  if(isset($data['department']))
  {
    if($params == null)
      $params = array();
    $params['form-filter-department'] = $data['department'];
  }
  if(isset($data['status']))
  {
    if($params == null)
      $params = array();
    $params['form-filter-status'] = $data['status'];
  }
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

  if($action == "add" or $action == "update")
  {
    if(!validatestring($data['name'], 5, null, null))
    {
      app_warning_print($app_str['errornameneeded']);
      errorcaptionadd("name");
      return FALSE;
    }
  }

  if($data['record'] != "" and ($action == "update" or $action == "add"))
  {
    $form_record_lc = strtolower($data['record']);
    $query = "";
    if($action == "add")
    {
      $query = "
      SELECT record FROM patients
      WHERE
        LOWER(record) = '$form_record_lc'
        AND status != {$status['deleted']}
      ";
    }
    else if($action == "update")
    {
      $query = "
      SELECT record FROM patients
      WHERE
        LOWER(record) = '$form_record_lc'
        AND id <> $item
        AND status != {$status['deleted']}
      ";
    }
    
    $db_query_res = app_db_query($query);
    if(app_db_numrows($db_query_res) > 0)
    {
      app_warning_print($app_str['errorrecordexists']);
      app_db_free($db_query_res);
      errorcaptionadd("record");
      return FALSE;
     }
    app_db_free($db_query_res);
  }
  
  if($data['stateid1'] != "" and ($action == "update" or $action == "add"))
  {
    $form_stateid1 = strtolower($data['stateid1']);
    $query = "";
    if($action == "add")
    {
      $query = "
      SELECT state_id_1 FROM patients
      WHERE
        LOWER(state_id_1) = '$form_stateid1'
        AND status != {$status['deleted']}
      ";
    }
    else if($action == "update")
    {
      $query = "
      SELECT state_id_1 FROM patients
      WHERE
        LOWER(state_id_1) = '$form_stateid1'
        AND id <> $item
        AND status != {$status['deleted']}
      ";
    }
    
    $db_query_res = app_db_query($query);
    if(app_db_numrows($db_query_res) > 0)
    {
      app_warning_print($app_str['errorstateid1exists']);
      app_db_free($db_query_res);
      errorcaptionadd("stateid1");
      return FALSE;
     }
    app_db_free($db_query_res);
  }

  if($action == "update")
  {
    if($data['status'] != $data['status_original'])
      $data['statusdatetime'] = app_get_curr_datetime();
    if($data['sinave'] != $data['sinave_original'])
      $data['sinavedatetime'] = app_get_curr_datetime();
    if($data['department'] != $dependencies['department_lists']['ids'][0]
       and $data['department'] != $data['department_original'])
    {
      $data['admittancedate'] = app_get_curr_date();
    }
  }
  
  if($action == "add" or $action == "update")
  {
    $birthdaystr = preg_replace("/\s+/", "-", $data['birthday']);
    $birthdaystr = preg_replace("/\\+/", "-", $birthdaystr);
    $birthdaystr = preg_replace("/\/+/", "-", $birthdaystr);
    $data['birthday'] = $birthdaystr;
    
    if(!validatedatestring($data['birthday'], app_get_curr_date(), 0, null, FALSE, FALSE))
    {
      app_warning_print($app_str['errorbirthday']);
      errorcaptionadd("birthday");
      return FALSE;
    }
    
    if($data['statusdatetime'] == "")
      $data['statusdatetime'] = app_get_curr_datetime();
    
    if($data['status'] == "inpatient" or $data['status'] == "discharged")
    {
      if($data['department'] == $dependencies['department_lists']['ids'][0])
      {
        app_warning_print($app_str['errordepartmentneeded']);
        errorcaptionadd("department");
        return FALSE;
      }
      if($data['admittancedate'] == "")
        $data['admittancedate'] = app_get_curr_date();
      if(
        !validatedatestring($data['admittancedate'], app_get_curr_date(), 0, null, FALSE, FALSE)
        or !validatedatestring($data['admittancedate'], $data['birthday'], null, 0, FALSE, FALSE)
      )
      {
        app_warning_print($app_str['erroradmittancedate']);
        errorcaptionadd("admittancedate");
        return FALSE;
      }
    }
    else
    {
      /*if($data['status'] != "deceased")
      {
        $data['department'] = $dependencies['department_lists']['ids'][0];
        $data['admittancedate'] = "";
      }*/
    }
    
    if($data['department'] != $dependencies['department_lists']['ids'][0])
    {
      if($data['admittancedate'] == "")
        $data['admittancedate'] = app_get_curr_date();
    }
  }
  
  if($action == "delete" or $action == "confirm_delete")
  {
    $query = "
      SELECT id FROM tests
      WHERE
        patient_id = $item
        AND status != {$status['deleted']}
      ";
    $db_query_res = app_db_query($query);
    if(app_db_numrows($db_query_res) > 0)
    {
      app_warning_print($app_str['errordeletion']);
      app_db_free($db_query_res);
      return FALSE;
    }
    app_db_free($db_query_res);
  }
  
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
      "edit-patient.php",
      $form['action'],
      "large",
      "",
      $form['elements']);
  }
  else if($action == "delete")
    app_form(
      $app_str['delmotive'],
      1,
      "edit-patient.php",
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
    "edit-patient.php",
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
