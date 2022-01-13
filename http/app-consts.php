<?php
// app-consts.php (utf-8)
// 
// Edited by: RR-DSE
// Timestamp: 22-01-12 19:44:41

//---
//Base constants
//---

$res_ok = 0;
$res = array(
  'ok' => $res_ok,
  'err_db' => 11,
  'session_ok' => 100,
  'err_no_session' => 101,
  'err_inactive_user' => 102,
  'err_inactive_profile' => 103,
  'err_no_access' => 104,
  'err_inactive_app' => 105,
  'outofdate' => 120
);

$app_debug = true;
$app_service_title = 'Pesquisas SARS-Cov-2 em Utentes Internados';
$app_header_logo = 'logo.png';
$app_header_logo_alt = 'Logotipo';
$app_header_logo_width = '145px'; 
$user_ip = $_SERVER['REMOTE_ADDR'];

$db_host = '127.0.0.1'; // or 'localhost'
$db_user = 'super';
$db_password = 'super';
$db_database = 'covidint2';
$db_link = null;

$status_inactive = 0;
$status_active = 1;
$status_deleted = 2;

$status = array(
  'inactive' => $status_inactive,
  'active' => $status_active,
  'deleted' => $status_deleted,
);

$gender = array(
  'undefined' => 0,
  'male' => 1,
  'female' => 2
);

$app_loginmaxdelta = 2000;
$app_intcharacters = "+-0123456789";

//---
//App setup and session data 
//---

$app_id = "";
$app_curr = "";
$app_refresh = "";
$app_redirect = "";
$app_timestamp = 0;
$app_updatetimestamp = false;
$app_noreload = false;
$app_outodate = false;
$app_outofdateredirect = "";
$app_header = "";
$app_header_nav = "";
$session_id = "";
$session_needed = false; 
$session_data = array(
  'username'=>"", 
  'host'=>"", 
  'status'=>"", 
  'realusername'=>"", 
  'lastact'=>"", 
  'profile'=>0, 
  'user'=>0, 
  'userstatus'=>0, 
  'profilestatus'=>0,
  'timedelta'=>0,
  'appid'=>0,
  'apptitle'=>"",
  'appdescription'=>"",
  'applocation'=>"",
  'appadmin'=>false
);
$session_apps = array();

//---
//App global variables 
//---

$curr_row = 0;

//---
//Standard layout and style constants
//---

$app_form_captionwidth = array(
  'small' => '80px',
  'medium' => '150px',
  'large' => '230px'
);

$app_form_style = array(
  'small' => 'style="width: 150px"', 
  'medium' => 'style="width: 300px"', 
  'large' => 'style="width: 600px"', 
  'full' => 'style="width: 100%"', 
  'small_2' => 'style="width: 100px"', 
  'small_3' => 'style="width: 50px"',
  'medium_2' => 'style="width: 250px"', 
  'medium_3' => 'style="width: 200px"', 
  'left' => 'style="text-align: left"',
  'center' => 'style="text-align: center"',
  'right' => 'style="text-align: right"',
);

$app_form_rowsize = array(
  'small' => '5', 
  'medium' => '10', 
  'large' => '20'
);

$app_form_colsize = array(
  'small' => '30', 
  'medium' => '60', 
  'large' => '120'
);

$app_form_table = array(
  'cellwidth_rowcount' => '80px', 
  'cellwidth_space' => '10px', 
  'cellwidth_page' => '35px'
);

$app_form_len = array(
  'small' => '30', 
  'medium' => '50', 
  'large' => '50'
);

$app_form_sepsize = '35px';

$app_infotable_style = array(
  'small' => 'style="width: 150px"', 
  'medium' => 'style="width: 300px"', 
  'large' => 'style="width: 600px"', 
  'full' => 'style="width: 100%"', 
  'small_2' => 'style="width: 100px"', 
  'small_3' => 'style="width: 50px"',
  'left' => 'style="text-align: left"',
  'center' => 'style="text-align: center"',
  'right' => 'style="text-align: right"',
);

//---
//App specific constants
//---

$app_regex = array(
  'sb_dateformat' => '%d-%m-%Y',
  'sb_datetimeformat' => '%d-%m-%Y %H:%i:%s',
  'sb_dateformatstr' => 'dd-mm-aaaa'
);

$app_limits = array(
  'id' => 32,
  'serial' => 32,
  'username' => 32,
  'password' => 16,
  'password_min' => 5,
  'name'=> 128,
  'gender'=> 16,
  'host'=> 128,
  'title' => 128,
  'record' => 32,
  'floor' => 128,
  'room' => 32,
  'bed' => 32,
  'desc' => 512,
  'notes' => 512,
  'date' => 10,
  'datetime'=> 16,
  'phone'=> 16,
  'contacts' => 512,
  'days' => 5,
  'days_min' => 1,
  'days_max' => 3650,
  'hours' => 5,
  'hours_min' => 1,
  'hours_max' => 3650,
  'position' => 64,
  'address_1' => 256,
  'address_2' => 64
);

$app_parameters = array();

$error_captions = array();
$app_caption_error_style = "style = \"color: red; font-weight: bold\"";

$app_documents_directory = "/ToolBox/NetRoot/documents";

require('app-strings.php');
require('app-layouts.php');

?>
