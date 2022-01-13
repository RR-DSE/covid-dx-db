<?php
// users.php (utf-8)
// 
// Edited by: RR-DSE
// Timestamp: 22-01-12 20:15:04

//---
//App init
//---

require("app-base.php");
require("tools.php");

$app_id = "users";
$app_curr = "users.php";
$session_id = app_get_session();
$session_needed = true;
$app_header_title = $captions['users'];
$app_str = $app_users_str;
$app_layout = $app_users_layout;
$app_nav_array = array( 
  array($captions['portal'], "portal.php", ""), 
  array($captions['setup'], "setup.php", ""), 
  array($captions['logout'], "logout.php","")
);

$app_res = app_start();
if($app_res != $res['ok'])
  app_end();

loaddbdependencies();
loaddependencies();
filterprepare();
filtersetup();
formfilter();

//---
//Main routine
//---

table_main();

app_end();

//---
//Initialization methods
//---	

function loaddependencies()
{
  global $dependencies;
  global $app_str;

  $dependencies['filter_status_lists'] = array(
    'ids' => array("active", "inactive", "all"),
    'captions' => array($app_str['active'], $app_str['inactive'], $app_str['all']),
    'dbids' => array(
      'active' => "1",
      'inactive' => "0"
    )
  );
}

function loaddbdependencies()
{
  global $dependencies;
  global $status, $app_str;

  $query = "
    SELECT
      B.id AS ProfileID,
      B.title AS ProfileTitle 
    FROM users AS A
    LEFT JOIN profiles AS B
      ON B.id = A.profile
    WHERE A.status <> {$status['deleted']}
    GROUP BY ProfileID, ProfileTitle
    ORDER BY ProfileTitle
  ";
  $dependencies['profile_lists'] = array(
    'ids' => array('all'),
    'captions' => array($app_str['all'])
  );
  $db_query_res = app_db_query($query);
  while($db_query_row = app_db_fetch($db_query_res))
  {
    $dependencies['profile_lists']['ids'][] = $db_query_row['ProfileID'];
    $dependencies['profile_lists']['captions'][] = $db_query_row['ProfileTitle'];
  }
  app_db_free($db_query_res);
}

function filterprepare()
{
  global $filter_data, $dependencies;
  global $app_str;
  
  $filter_data['filter'] = false;
  $filter_data['link'] = array();
  $filter_data['dbstr'] = "";

  $filter_data['elements'] = array(
    array(
      'id' => "profile",
      'dbalias' => "B.id",
      'caption' => $app_str['profile'],
      'lists' => $dependencies['profile_lists'],
      'selection' => "all",
      'formstyle' => "medium"
      ),
    array(
      'id' => "status",
      'dbalias' => "A.status",
      'caption' => $app_str['status'],
      'lists' => $dependencies['filter_status_lists'],
      'selection' => "active",
      'formstyle' => "medium"
    )
  );
}

function filtersetup()
{
  global $filter_data;

  if(checkget("filter", "active") and !checkformpost("filter-reset"))
  {
    $index = 0;
    foreach($filter_data['elements'] as $element)
    {
      updatefrompost("form-filter-".$element['id'], $filter_data['elements'][$index]['selection']);
      $index = $index + 1;
    }
  }
  else
  {
    $index = 0;
    foreach($filter_data['elements'] as $element)
    {
      updatefromget("form-filter-".$element['id'], $filter_data['elements'][$index]['selection']);
      $index = $index + 1;
    }
  }

  foreach($filter_data['elements'] as $element)
  {
    if($element['selection'] != "all")
    {
      $filter_data['filter'] = true;
      if($filter_data['dbstr'] != "")
        $filter_data['dbstr'] .= ' AND ';
      if(isset($element['lists']['dbids']))
        $filter_data['dbstr'] .= $element['dbalias']." = ".$element['lists']['dbids'][$element['selection']];
      else
        $filter_data['dbstr'] .= $element['dbalias']." = ".$element['selection'];
    }
    $filter_data['link']["filter-".$element['id']] = $element['selection'];
  }
  
  if($filter_data['dbstr'] != "")
    $filter_data['dbstr'] = "WHERE ".$filter_data['dbstr'];
}

//---
//Support functions and methods
//---

//---
//Forms and visual output
//---

function formfilter()
{
  global $filter_data;
  global $app_str, $app_layout;

  $form_elements = array();
  foreach($filter_data['elements'] as $element)
  {
    $form_elements[] = array(
      'type' => "select",
      'name' => "filter-".$element['id'],
      'caption' => $app_str[$element['id']],
      'options' => $element['lists']['ids'],
      'optioncaptions' => $element['lists']['captions'],
      'value' => $element['selection'],
      'size' => 1,
      'style' => $element['formstyle'],
      'onchange' => "filter"
    );
  }
  $form_elements[] = array(
    'type' => "cell_start",
    'caption' => "no_caption"
  );
  $form_elements[] = array(
    'type' => "button_add",
    'name' => "filter-apply",
    'value' => $app_str['filterapply']
  );
  $form_elements[] = array(
    'type' => "space_add",
    'space' => $app_layout['filteroptionssep']
  );
  $form_elements[] = array(
    'type' => "button_add",
    'name' => "filter-reset",
    'value' => $app_str['filterreset']
  );
  $form_elements[] = array(
    'type' => "cell_end"
  );

  app_form(
    "",
    2,
    "users.php",
    array('filter' => "active"),
    "small",
    "filter",
    $form_elements,
    False
  );
}

function formoptions()
{
  global $app_str;
  global $filter_data;
  
  $form_elements = array(
    array(
      'type' => "cell_start",
      'caption' => $app_str['options'],
      'class' => "options",
      'captionclass' => "options"
    ),
    array(
      'type' => "button_add",
      'name' => "new",
      'value' => $app_str['actionadd'],
      'class' => "options"
    ),
    array(
      'type' => "cell_end"
    )
  );
  
  app_form(
    "",
    1,
    "edit-user.php",
    array_merge(array('action' => "new"), $filter_data['link']),
    "medium",
    "",
    $form_elements,
    True
  );
}

function table_main()
{
  global $app_regex, $filter_data, $app_str, $app_layout;
  global $status;

  $query = "
    SELECT
      A.id AS User,
      A.username AS Username,
      A.name AS Name,
      A.status AS Status,
      B.id AS ProfileID,
      B.title AS ProfileTitle,
      C.name AS AddName,
      D.name AS ModName,
      DATE_FORMAT(A.add_datetime, '{$app_regex['sb_dateformat']}') AS AddDateTime,
      DATE_FORMAT(A.mod_datetime, '{$app_regex['sb_dateformat']}') AS ModDateTime
    FROM users AS A
    LEFT JOIN profiles AS B
      ON B.id = A.profile
    LEFT JOIN users AS C
      ON C.id = A.add_user
    LEFT JOIN users AS D
      ON D.id = A.mod_user
    {$filter_data['dbstr']}
    ORDER BY Name
  ";

  $db_query_res = app_db_query($query);
  $db_query_rowcount = app_db_numrows($db_query_res);

  if($db_query_rowcount == 0)
  {
    app_info_print($app_str['infonoitems']);
    formoptions();
  }
  else 
  {
    formoptions();
    app_table_simple_start(
      array(
        $app_str['user'],
        $app_str['name'],
        $app_str['profile'],
        $app_str['status'], 
        $app_str['moduser'],
        $app_str['moddatetime']
      ),
      $app_layout['colwidths']);
    while($db_query_row = app_db_fetch($db_query_res))
    {
      app_table_row(
        array(
          $db_query_row['Username'],
          $db_query_row['Name'],
          $db_query_row['ProfileTitle'],
          getstatuscaption($db_query_row['Status']),
          $db_query_row['ModName'],
          $db_query_row['ModDateTime']
        ),
        'edit-user.php',
        $db_query_row['User'], null,
        null, null, null,
        $app_layout['colstyles']
      );
    }
    app_table_end();
  }

  app_db_free($db_query_res);
}

?>
