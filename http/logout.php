<?php
// logout.php (utf-8)
// 
// Edited by: RR-DSE
// Timestamp: 22-01-12 20:04:01

//---
//App init
//---

if(!isset($_GET['id']))
  exit();

require('app-base.php');

$app_id = 'logout';
$app_curr = 'logout.php';
$session_id = app_get_session();
$session_needed = true;
$app_header_title = 'Fim de sessão';
$app_nav_array = '';

$app_res = app_start();
if($app_res != $res['ok'])
  app_end();

//---
//Main routine
//---

$query = "
  UPDATE sessions
    SET
      status = 'offline',
      lastact = NOW()
   WHERE user = {$session_data['user']}
  ";

$db_query_res = app_db_query($query);
app_db_free($db_query_res);

app_info_print('Fechou a sessão actual.');

app_section_start(null);
app_link_app('Início de sessão','login.php');
app_section_end();

app_end();
?>
