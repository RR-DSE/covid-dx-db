<?php
// edit-department.php (utf-8)
// 
// Edited by: RR-DSE
// Timestamp: 22-01-12 19:56:01

//---
//App init
//---

require("app-base.php");
require("tools.php");

$app_id = "editdepartment";
$app_curr = "edit-department.php";
$app_outofdate = true;
$session_id = app_get_session();
$session_needed = true;
$app_str = $app_editdepartment_str;
$app_layout = $app_editdepartment_layout;
actionsetup();

$app_nav_array = array( 
  array($captions['portal'], "portal.php", ""),
  array($captions['setup'], "setup.php", ""), 
  array($captions['departments'], "departments.php", ""),
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
}

else if($action == "add")
{
  loadpost();
  if(validate())
  {
    dbinsert();
    app_info_print($app_str['added']);
    link_section("departments");
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
  loadpost();
  if(validate())
  {
    dbupdate();
    app_info_print($app_str['updated']);
    $action = "edit";
    loaddb();
    formoptions();
    formedit();
    link_section("departments");
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
  link_section("departments");
}

else if($action == "enable")
{
  dbenable();
  app_info_print($app_str['enabled']);
  $action = "edit";
  loaddb();
  formoptions();
  formedit();
  link_section("departments");
}

else if($action == "confirmeddelete")
{
  loadpost();
  dbdelete();
  app_info_print($app_str['deleted']);
  link_section("departments");
}

app_end();

//---
//Initialization methods
//---	

function formprepare()
{
  global $action, $item, $data;
  global $app_str;
  
  $form_action = null;
  if($action == "new")
    $form_action = array('action' => "add", 'cancelredirect' => "departments.php");
  else if($action == "add")
    $form_action = array('action' => "add", 'cancelredirect' => "departments.php");
  else if($action == "edit")
    $form_action = array('action' => "update", 'item' => $item, 'cancelredirect' => "departments.php");
  else if($action == "update")
    $form_action = array('action' => "update", 'item' => $item, 'cancelredirect' => "departments.php");
  else if($action == "delete")
    $form_action = array('action' => "confirmdelete", 'item' => $item);
  
  $form_action_strkey = "actionadd";
  if($action == "edit" or $action == "update")
    $form_action_strkey = "actionupdate";
  if($action == "delete")
    $form_action_strkey = "actiondelete";

  $form_elements = array();
  if($action == "edit" or $action == "add" or $action == "new" or $action == "update")
  {
    $form_elements = array(
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
        'name' => "phone",
        'caption' => $app_str['phone'],
        'value' => $data['phone'],
        'size' => "medium",
        'maxlength' => "phone",
        'style' => "small"
        ),
      array(
        'type' => "textarea",
        'name' => "contacts",
        'caption' => $app_str['contacts'],
        'value' => $data['contacts'],
        'rows' => "small",
        'columns' => "medium",
        'maxlength' => "contacts",
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
        'value' => $app_str[$form_action_strkey],
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

function loadnew()
{
  global $data;
  global $status;
  
  $data['title'] = "";
  $data['desc'] = "";
  $data['phone'] = "";
  $data['contacts'] = "";
  $data['status'] = $status['active'];
  $data['status_id'] = "active";
  $data['status_notes'] = "";
}

function loadpost()
{
  global $action, $data, $status;

  if($action == "add" or $action == "update")
  {
    $data['title'] = getformpost('title', FALSE);
    $data['desc'] = getformpost('desc', FALSE);
    $data['phone'] = getformpost('phone', FALSE);
    $data['contacts'] = getformpost('contacts', FALSE);
    $data['status'] = $status['active'];
    $data['status_id'] = "active";
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
  global $str, $app_regex;

  $query = "
    SELECT
      A.title AS Title,
      A.description AS 'Desc',
      A.phone AS Phone,
      A.contacts AS Contacts,
      A.status AS Status,
      A.status_notes AS StatusNotes,
      A.add_user AS AddUser,
      B.name AS AddName,
      DATE_FORMAT(A.add_datetime, '{$app_regex['sb_datetimeformat']}') AS AddDateTime,
      A.mod_user AS ModUser,
      C.name AS ModName,
      DATE_FORMAT(A.mod_datetime, '{$app_regex['sb_datetimeformat']}') AS ModDateTime
    FROM departments AS A 
    LEFT JOIN users AS B
      ON B.id = A.add_user
    LEFT JOIN users AS C
      ON C.id = A.mod_user
    WHERE A.id=$item
  ";
 
  $db_query_res = app_db_query($query);
  $db_query_rowcount = app_db_numrows($db_query_res);
  if($db_query_rowcount == 0)
  {
    app_error_print($str['error_db_query']);
    app_db_free($db_query_res);
    app_end();
  }

  $db_query_row = app_db_fetch($db_query_res);
  
  $data['title'] = getformdb($db_query_row, "Title", FALSE);
  $data['desc'] = getformdb($db_query_row, "Desc", FALSE);
  $data['phone'] = getformdb($db_query_row, "Phone", FALSE);
  $data['contacts'] = getformdb($db_query_row, "Contacts", FALSE);
  $data['status'] = getformdb($db_query_row, "Status", FALSE);
  $data['status_id'] = getstatusid($data['status']);
  $data['status_notes'] = getformdb($db_query_row, "StatusNotes", FALSE);
  
  $metadata['adduser'] = getformdb($db_query_row, "AddUser", FALSE);
  $metadata['addusername'] = getformdb($db_query_row, "AddName", FALSE);
  $metadata['adddatetime'] = getformdb($db_query_row, "AddDateTime", FALSE);
  $metadata['moduser'] = getformdb($db_query_row, "ModUser", FALSE);
  $metadata['modusername'] = getformdb($db_query_row, "ModName", FALSE);
  $metadata['moddatetime'] = getformdb($db_query_row, "ModDateTime", FALSE);
  
  app_db_free($db_query_res);
}

function dbinsert()
{
  global $data;
  global $session_data;
  global $status;

  $query = "
    INSERT INTO departments VALUES (
      NULL, "
      .getstringdb($data['title'], "string", FALSE) .", "
      .getstringdb($data['desc'], "string", TRUE) .", "
      .getstringdb($data['phone'], "string", TRUE) .", "
      .getstringdb($data['contacts'], "string", TRUE) .", 
      {$status['active']},
      NULL,
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
  
  $query = "
    UPDATE departments
    SET
      title = " . getstringdb($data['title'], "string", FALSE) .", 
      description = " . getstringdb($data['desc'], "string", TRUE) .", 
      phone = " . getstringdb($data['phone'], "string", TRUE) .", 
      contacts = " . getstringdb($data['contacts'], "string", TRUE) .", 
      mod_user = {$session_data['user']},
      mod_datetime = NOW()
    WHERE id=$item
  ";

  $db_query_res = app_db_query($query);
  app_db_free($db_query_res);
}

function dbdelete()
{
  global $item, $data;
  global $session_data;
  global $status;
  
  $query = "
    UPDATE departments
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
  
  $query = "
    UPDATE departments
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

  $query = "
    SELECT id
    FROM departments
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
    UPDATE departments
    SET
      status = {$status['active']},
      mod_user = {$session_data['user']},
      mod_datetime = NOW()
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
  global $data, $captions;

  $params = null;
  if(isset($data['status_id']))
  {
    if($params == null)
      $params = array();
    $params['form-filter-status'] = $data['status_id'];
  }

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
  
  reseterrorcaptions();
  
  if($action == "add" or $action == "update")
  {
    if(!validatestring($data['title'], 1, null, null))
    {
      app_warning_print($app_str['errortitleneeded']);
      errorcaptionadd("title");
      return FALSE;
    }
    $form_title_lc = strtolower($data['title']);
    if($action == "add")
    {
      $query = "
        SELECT id FROM departments
        WHERE
          LOWER(title) = '$form_title_lc'
          AND status != {$status['deleted']}
      ";
    }
    else
    {
      $query = "
        SELECT id FROM departments
        WHERE
          LOWER(title) = '$form_title_lc'
          AND id <> $item
          AND status != {$status['deleted']}
      ";
    }
    $db_query_res = app_db_query($query);
    $db_query_rowcount = app_db_numrows($db_query_res);
    if($db_query_rowcount > 0)
    {
      app_warning_print($app_str['errortitleexists']);
      app_db_free($db_query_res);
      errorcaptionadd("title");
      return FALSE;
    }
    app_db_free($db_query_res);
    if(!validatestring($data['desc'], 0, $app_limits['desc'], null))
    {
      app_warning_print($app_str['errordescriptionlimit']);
      errorcaptionadd("desc");
      return FALSE;
    }
    if(!validatestring($data['phone'], 0, $app_limits['phone'], null))
    {
      app_warning_print($app_str['errorphonelimit']);
      errorcaptionadd("phone");
      return FALSE;
    }
    if(!validatestring($data['contacts'], 0, $app_limits['contacts'], null))
    {
      app_warning_print($app_str['errorcontactslimit']);
      errorcaptionadd("contacts");
      return FALSE;
    }
  }
  if($action == "delete" or $action == "confirmeddelete")
  {
    if(!validatedeletion())
    {
      app_warning_print($app_str['errordeletion']);
      return FALSE;
    }
  }

  return TRUE;
}

function validatedeletion()
{
  global $item;
  
  $query = "
    SELECT COUNT(1) AS Count
    FROM patients
    WHERE department = $item
  ";
  $db_query_res = app_db_query($query);
  $db_query_row = app_db_fetch($db_query_res);
  $count = getintdb($db_query_row, "Count");
  app_db_free($db_query_res);
  if($count != 0)
    return FALSE;
  
  $query = "
    SELECT COUNT(1) AS Count
    FROM audit_patients
    WHERE department = $item
  ";
  $db_query_res = app_db_query($query);
  $db_query_row = app_db_fetch($db_query_res);
  $count = getintdb($db_query_row, "Count");
  app_db_free($db_query_res);
  if($count != 0)
    return FALSE;

  return TRUE;
}

//---
//Forms and visual output
//---	

function formedit()
{
  global $action, $app_str;

  $form = formprepare();
  
  if($action == "edit" or $action == "add" or $action == "new" or $action == "update")
  {
    app_form(
      "",
      1,
      "edit-department.php",
      $form['action'],
      "medium",
      "",
      $form['elements']);
  }
  else if($action == "delete")
    app_form(
      $app_str['delmotive'],
      1,
      "edit-department.php",
      $form['action'],
      "large",
      "",
      $form['elements']);

  if($action == "edit")
    info();
}

function formoptions()
{
  global $item, $data;
  global $status;
  global $app_str, $app_layout;

  $form_elements = array();
  $form_elements[] = array(
    'type' => "cell_start",
    'caption' => $app_str['options'],
    'class' => "options",
    'captionclass' => "options"
  );
  if($data['status'] == $status['active'])
    $form_elements[] = array(
      'type' => "button_add",
      'name' => "disable",
      'value' => $app_str['actiondisable'],
      'class' => "options"
    );
  else
    $form_elements[] = array(
      'type' => "button_add",
      'name' => "enable",
      'value' => $app_str['actionenable'],
      'class' => "options"
    );
  $form_elements[] = array(
    'type' => "space_add",
    'space' => $app_layout['filteroptionssep']
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
    "edit-department.php",
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
