<?php
// apps.php (utf-8)
// 
// Edited by: RR-DSE
// Timestamp: 22-01-12 19:51:43

//---
//App init
//---

require("app-base.php");
require("tools.php");

$app_id = "apps";
$app_curr = "apps.php";
$session_id = app_get_session();
$session_needed = true;
$app_header_title = $captions['apps'];
$app_str = $app_apps_str;
$app_layout = $app_apps_layout;
$app_nav_array = array( 
  array($captions['portal'], "portal.php", ""), 
  array($captions['setup'], "setup.php", ""), 
  array($captions['logout'], "logout.php","")
);

$app_res = app_start();
if($app_res != $res['ok'])
  app_end();

//---
//Main routine
//---

table_main();

app_end();

//---
//Support functions and methods
//---

//---
//Forms and visual output
//---

function table_main()
{
  global $app_regex, $app_str, $app_layout;
  global $status;

  $query = "
    SELECT
      A.id AS App,
      A.code AS Code,
      A.title AS Title,
      A.location AS Location,
      A.admin + 0 AS Admin
    FROM apps AS A
    ORDER BY Title
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
        $app_str['id'],
        $app_str['title'],
        $app_str['location'],
        $app_str['admin'] 
      ),
      $app_layout['colwidths']);
    while($db_query_row = app_db_fetch($db_query_res))
    {
      $admin = getformdb($db_query_row, "Admin", true);
      if($admin)
        $adminstr = $app_str['yes'];
      else
        $adminstr = $app_str['no'];
      app_table_row(
        array(
          $db_query_row['Code'],
          $db_query_row['Title'],
          $db_query_row['Location'],
          $adminstr
        ),
        'edit-app.php',
        $db_query_row['App'], null,
        null, null, null,
        $app_layout['colstyles']
      );
    }
    app_table_end();
  }

  app_db_free($db_query_res);
}

function formoptions()
{
  global $app_str;
  
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
    "edit-app.php",
    array('action' => "new"),
    "medium",
    "",
    $form_elements,
    True
  );
}

?>
