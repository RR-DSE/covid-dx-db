<?php
// edit-user.php (utf-8)
// 
// Edited by: RR-DSE
// Timestamp: 22-01-12 19:59:44

//---
//App init
//---

require("app-base.php");
require("tools.php");

$app_id = "edituser";
$app_curr = "edit-user.php";
$app_outofdate = true;
$session_id = app_get_session();
$session_needed = true;
$app_str = $app_edituser_str;
$app_layout = $app_edituser_layout;
actionsetup();

$app_nav_array = array( 
  array($captions['portal'], "portal.php", ""),
  array($captions['setup'], "setup.php", ""), 
  array($captions['users'], "users.php", ""),
  array($captions['logout'], "logout.php", "")
);

$app_res = app_start();
if($app_res != $res['ok'])
  app_end();

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
  loadpost();
  if(validate())
  {
    dbinsert();
    app_info_print($app_str['added']);
    link_section("users");
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
    link_section("users");
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
  link_section("users");
}

else if($action == "enable")
{
  dbenable();
  app_info_print($app_str['enabled']);
  $action = "edit";
  loaddb();
  formoptions();
  formedit();
  link_section("users");
}

else if($action == "confirmeddelete")
{
  loadpost();
  dbdelete();
  app_info_print($app_str['deleted']);
  link_section("users");
}

app_end();

//---
//Initialization methods
//---	

function loaddbdependencies()
{
  global $dependencies;
  global $app_str, $status;

  $dependencies['profile_lists'] = array(
    'ids' => array(1),
    'captions' => array($app_str['admin'])
  );

  $query = "
    SELECT
      id AS ID,
      title AS Title
    FROM profiles
    WHERE status = {$status['active']}
    ORDER BY title
    ";
  $db_query_res = app_db_query($query);
  while($db_query_row = app_db_fetch($db_query_res))
  {
    if($db_query_row['ID'] != 1)
    {
      $dependencies['profile_lists']['ids'][] = $db_query_row['ID'];
      $dependencies['profile_lists']['captions'][] = $db_query_row['Title'];
    }
  }
  app_db_free($db_query_res);
}

function formprepare()
{
  global $action, $item, $data, $dependencies;
  global $app_str;

  $form_action = null;
  if($action == "new")
    $form_action = array('action' => "add", 'cancelredirect' => "users.php");
  else if($action == "add")
    $form_action = array('action' => "add", 'cancelredirect' => "users.php");
  else if($action == "edit")
    $form_action = array('action' => "update", 'item' => $item, 'cancelredirect' => "users.php");
  else if($action == "update")
    $form_action = array('action' => "update", 'item' => $item, 'cancelredirect' => "users.php");
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
    $form_elements = array(
      array(
        'type' => "text",
        'name' => "username",
        'caption' => $app_str['title'],
        'value' => $data['username'],
        'size' => "medium",
        'maxlength' => "username",
        'style' => "medium"
      ),
      array(
        'type' => "select",
        'name' => "profile",
        'caption' => $app_str['profile'],
        'options' => $dependencies['profile_lists']['ids'],
        'optioncaptions' => $dependencies['profile_lists']['captions'],
        'value' => $data['profile'],
        'size' => 1,
        'style' => "full"
      ),
      array(
        'type' => "text",
        'name' => "name",
        'caption' => $app_str['name'],
        'value' => $data['name'],
        'size' => "large",
        'maxlength' => "title",
        'style' => "medium"
      )
    );
    if($action == "new" or $action == "add")
    {
      $form_elements[] = array(
        'type' => "password",
        'name' => "password",
        'caption' => $app_str['password'],
        'value' => $data['password'],
        'size' => "small",
        'maxlength' => "password",
        'style' => "small"
      );
    }
    $form_elements = array_merge($form_elements, array(
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
    ));
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
  global $status, $dependencies;

  $data['username'] = "";
  $data['name'] = "";
  $data['profile'] = "";
  $data['password'] = "";
  $data['status'] = $status['active'];
  $data['status_id'] = "active";
  $data['status_notes'] = "";
}

function loadpost()
{
  global $action, $data, $status;
  global $dependencies;
  
  if($action == "add" or $action == "update")
  {
    $data['username'] = getformpost("username", FALSE);
    $data['name'] = getformpost("name", FALSE);
    $data['profile'] = getformpost("profile", FALSE);
    $data['password'] = getformpost("password", FALSE);
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
  global $data, $metadata, $dependencies;
  global $str, $app_regex, $status;
  
  $query = "
    SELECT
      A.username AS Username,
      A.password AS Password,
      A.profile AS Profile,
      D.status AS ProfileStatus,
      D.title AS ProfileTitle,
      A.name AS Name,
      A.status AS Status,
      A.status_notes AS StatusNotes,
      A.add_user AS AddUser,
      B.name AS AddName,
      DATE_FORMAT(A.add_datetime, '{$app_regex['sb_datetimeformat']}') AS AddDateTime,
      A.mod_user as ModUser,
      C.name AS ModName,
      DATE_FORMAT(A.mod_datetime, '{$app_regex['sb_datetimeformat']}') AS ModDateTime
    FROM users AS A
    LEFT JOIN users AS B
      ON B.id = A.add_user
    LEFT JOIN users AS C
      ON C.id = A.mod_user
    LEFT JOIN profiles AS D
      ON D.id = A.profile
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

  $data['username'] = getformdb($db_query_row, "Username", FALSE);
  $data['name'] = getformdb($db_query_row, "Name", FALSE);
  $data['password'] = getformdb($db_query_row, "Password", FALSE);
  $data['profile'] = getformdb($db_query_row, "Profile", FALSE);
  $data['profilestatus'] = getintdb($db_query_row, "ProfileStatus", FALSE);
  $data['profiletitle'] = getformdb($db_query_row, "ProfileTitle", FALSE);
  $data['status'] = getintdb($db_query_row, "Status", FALSE);
  $data['status_id'] = getstatusid($data['status']);
  $data['status_notes'] = getformdb($db_query_row, "StatusNotes", FALSE);

  $metadata['adduser'] = getformdb($db_query_row, "AddUser", FALSE);
  $metadata['addusername'] = getformdb($db_query_row, "AddName", FALSE);
  $metadata['adddatetime'] = getformdb($db_query_row, "AddDateTime", FALSE);
  $metadata['moduser'] = getformdb($db_query_row, "ModUser", FALSE);
  $metadata['modusername'] = getformdb($db_query_row, "ModName", FALSE);
  $metadata['moddatetime'] = getformdb($db_query_row, "ModDateTime", FALSE);
  
  if($data['profilestatus'] != $status['active'])
  {
    $dependencies['profile_lists']['ids'][] = $data['profile'];
    $dependencies['profile_lists']['captions'][] = $data['profiletitle'];
  }

  app_db_free($db_query_res);
}

function dbinsert()
{
  global $data, $dependencies;
  global $session_data;
  global $status;

  $query = "
    INSERT INTO users VALUES (
      NULL, "
      .getstringdb($data['username'], "string", FALSE) .", 
      md5(" .getstringdb($data['password'], "string", FALSE) ."), "
      .getstringdb($data['profile'], "numeric", FALSE) .", "
      .getstringdb($data['name'], "string", FALSE) .", 
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
  global $item, $data, $dependencies;
  global $session_data;
  global $status;
  
  $query = "
    UPDATE users
    SET
      username = " . getstringdb($data['username'], "string", FALSE) .", 
      name = " . getstringdb($data['name'], "string", FALSE) .", 
      profile = " . getstringdb($data['profile'], "numeric", FALSE) .", 
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
  
  $query = "
    UPDATE users
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
    UPDATE users
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
    FROM users
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
    UPDATE users
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
  if(isset($data['profile']))
  {
    if($params == null)
      $params = array();
    $params['form-filter-profile'] = $data['profile'];
  }
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

function checkuniqueadmin()
{
  global $admin_list, $item;
  global $status;

  $admin_list = [];
  $query = "
    SELECT id
    FROM users
    WHERE
      profile = 1
        AND status = {$status['active']}
    ";
  $db_query_res = app_db_query($query);
  while($db_query_row = app_db_fetch($db_query_res))
    $admin_list[] = $db_query_row['id'];
  app_db_free($db_query_res);
  if(in_array($item, $admin_list) and count($admin_list) == 1)
    return true;
  else
    return false;
}

//---
//Input data validation
//---	

function validate()
{
  global $action, $item, $data;
  global $status, $app_limits, $app_str;
  global $session_data;
  
  reseterrorcaptions();
  
  if($action == "add" or $action == "update")
  {
    $data['username'] = strtolower($data['username']);
    if(!validatestring($data['username'], 1, $app_limits['username'], $app_str['usernamevalidchars']))
    {
      app_warning_print($app_str['errorusername']);
      errorcaptionadd("username");
      return FALSE;
    }
    $form_title_lc = strtolower($data['username']);
    if($action == "add")
    {
      $query = "
        SELECT id FROM users
        WHERE
          LOWER(username) = '$form_title_lc'
          AND status != {$status['deleted']}
      ";
    }
    else
    {
      $query = "
        SELECT id FROM users
        WHERE
          LOWER(username) = '$form_title_lc'
          AND id <> $item
          AND status != {$status['deleted']}
      ";
    }
    $db_query_res = app_db_query($query);
    $db_query_rowcount = app_db_numrows($db_query_res);
    if($db_query_rowcount > 0)
    {
      app_warning_print($app_str['erroruserexists']);
      app_db_free($db_query_res);
      errorcaptionadd("username");
      return FALSE;
    }
    app_db_free($db_query_res);
    if(!validatestring($data['name'], 1, $app_limits['title'], null))
    {
      app_warning_print($app_str['errortitleneeded']);
      errorcaptionadd("name");
      return FALSE;
    }
    if($action == "add" and !validatestring($data['password'], $app_limits['password_min'], $app_limits['password'], $app_str['passwordvalidchars']))
    {
      app_warning_print($app_str['errorpassword']);
      errorcaptionadd("password");
      return FALSE;
    }
    if(!validatestring($data['profile'], 1, null, null))
    {
      app_warning_print($app_str['errorprofileneeded']);
      errorcaptionadd("profile");
      return FALSE;
    }
    if($action == "update" and checkuniqueadmin() and $data['profile'] != 1)
    {
      app_warning_print($app_str['erroradminneeded']);
      errorcaptionadd("profile");
      return FALSE;
    }
    if($data['username'] == "all")
    {
      app_warning_print($app_str['erroridreserved']);
      errorcaptionadd("username");
      return FALSE;
    }
  }
  if(($action == 'disable' or $action == 'delete') and $session_data['user'] == $item)
  {
    app_warning_print($app_str['errorcurrentsession']);
    return FALSE;
  }
  if(($action == 'disable' or $action == 'delete') and checkuniqueadmin())
  {
    app_warning_print($app_str['erroruniqueadmin']);
    return FALSE;
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
    WHERE
      add_user = $item
      OR mod_user = $item
  ";
  $db_query_res = app_db_query($query);
  $db_query_row = app_db_fetch($db_query_res);
  $count = getintdb($db_query_row, "Count");
  app_db_free($db_query_res);
  if($count != 0)
    return FALSE;
  
  $query = "
    SELECT COUNT(1) AS Count
    FROM departments
    WHERE
      add_user = $item
      OR mod_user = $item
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
    WHERE
      add_user = $item
      OR mod_user = $item
  ";
  $db_query_res = app_db_query($query);
  $db_query_row = app_db_fetch($db_query_res);
  $count = getintdb($db_query_row, "Count");
  app_db_free($db_query_res);
  if($count != 0)
    return FALSE;
  
  $query = "
    SELECT COUNT(1) AS Count
    FROM profiles
    WHERE
      add_user = $item
      OR mod_user = $item
  ";
  $db_query_res = app_db_query($query);
  $db_query_row = app_db_fetch($db_query_res);
  $count = getintdb($db_query_row, "Count");
  app_db_free($db_query_res);
  if($count != 0)
    return FALSE;
  
  $query = "
    SELECT COUNT(1) AS Count
    FROM users
    WHERE
      id != $item
      AND(
        add_user = $item
        OR mod_user = $item
      )
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
  global $action;
  global $app_str;

  $form = formprepare();

  if($action == "edit" or $action == "add" or $action == "new" or $action == "update")
  {
    app_form(
      "",
      1,
      "edit-user.php",
      $form['action'],
      "medium",
      "",
      $form['elements']);
  }
  else if($action == "delete")
    app_form(
      $app_str['delmotive'],
      1,
      "edit-user.php",
      $form['action'],
      "large",
      "",
      $form['elements']);

  if($action == "edit")
    info();
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
    "edit-user.php",
    array('action' => "options", 'item' => $item),
    "medium",
    "",
    $form_elements,
    True
  );
}

?>
