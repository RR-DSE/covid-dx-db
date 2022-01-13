<?php
// tools.php (utf-8)
// 
// Edited by: RR-DSE
// Timestamp: 22-01-12 20:14:40

function getpost($name, $boolean = FALSE)
{
  $result = "";
  if($boolean)
    $result = FALSE;

  if(isset($_POST[$name]) and $_POST[$name] !== null and $_POST[$name] !== "")
  {
    if($boolean)
      $result = TRUE;
    else
      $result = $_POST[$name];
  }

  return $result;
}

function checkpost($name, $value = null)
{
  if(is_null($value) and isset($_POST[$name]) and $_POST[$name] !== null and $_POST[$name] !== "")
    return TRUE;
  else if(isset($value) and isset($_POST[$name]) and $_POST[$name] == $value)
    return TRUE;
  else
    return FALSE;
}

function getformpost($name, $boolean = FALSE)
{
  $formname = "form-".$name;

  $result = "";
  if($boolean)
    $result = FALSE;

  if(isset($_POST[$formname]) and $_POST[$formname] !== null and $_POST[$formname] !== "")
  {
    if($boolean)
      $result = TRUE;
    else
      $result = $_POST[$formname];
  }

  return $result;
}

function checkformpost($name, $value = null)
{
  $formname = "form-".$name;
  
  if(is_null($value) and isset($_POST[$formname]) and $_POST[$formname] !== null and $_POST[$formname] !== "")
    return TRUE;
  else if(isset($value) and isset($_POST[$formname]) and $_POST[$formname] == $value)
    return TRUE;
  else
    return FALSE;
}

function getget($name, $boolean = FALSE)
{
  $result = "";
  if($boolean)
    $result = FALSE;

  if(isset($_GET[$name]) and $_GET[$name] !== null and $_GET[$name] !== "")
  {
    if($boolean)
      $result = TRUE;
    else
      $result = $_GET[$name];
  }

  return $result;
}

function checkget($name, $value = null)
{
  if(is_null($value) and isset($_GET[$name]) and $_GET[$name] !== null and $_GET[$name] !== "")
    return TRUE;
  else if(isset($value) and isset($_GET[$name]) and $_GET[$name] == $value)
    return TRUE;
  else
    return FALSE;
}

function getformget($name, $boolean = FALSE)
{
  $formname = "form-".$name;

  $result = "";
  if($boolean)
    $result = FALSE;

  if(isset($_GET[$formname]) and $_GET[$formname] !== null and $_GET[$formname] !== "")
  {
    if($boolean)
      $result = TRUE;
    else
      $result = $_GET[$formname];
  }

  return $result;
}

function checkformget($name, $value = null)
{
  $formname = "form-".$name;
  
  if(is_null($value) and isset($_GET[$formname]) and $_GET[$formname] !== null and $_GET[$formname] !== "")
    return TRUE;
  else if(isset($value) and isset($_GET[$formname]) and $_GET[$formname] == $value)
    return TRUE;
  else
    return FALSE;
}

function getformdb($array, $name, $boolean = FALSE)
{
  $result = "";
  if($boolean)
    $result = FALSE;
  
  if(isset($array[$name]) and $array[$name] !== null and $array[$name] !== "")
  {
    if($boolean and $array[$name] != 0)
      $result = TRUE;
    else
      $result = $array[$name];
  }

  return $result;
}

function getintdb($array, $name, $boolean = FALSE)
{
  if(isset($array[$name]) and $array[$name] !== null and $array[$name] !== "")
  {
    $result = intval($array[$name]);
    if($boolean)
    {
      if($result != 0)
        $result = True;
      else
        $result = False;
    }
  }
  else
  {  
    $result = null;
    if($boolean)
      $result = False;
  }

  return $result;
}

function getstringdb($str, $type, $null)
{
  global $app_regex;

  $result = "'".$str."'";

  if($type == "numeric")
    $result = $str;
  
  if($type == "boolean")
  {
    if($str)
      $result = "1";
    else
      $result = "0";
  }
  
  if($type == "date")
    $result = "str_to_date('$str', '{$app_regex['sb_dateformat']}')";
  
  if($type == "datetime")
    $result = "str_to_date('$str', '{$app_regex['sb_datetimeformat']}')";

  if($null and ($str === "" or $str === null))
    $result = "NULL";

  return $result;
}

function validatestring(&$str, $minlen, $maxlen, $validchars)
{
  $str = app_db_clearquotes($str);
  $str = trim($str);
  $len = strlen($str);

  if($str === null)
    $str = "";
  if($minlen !== null and $minlen >= 0)
  {
    if($len < $minlen)
      return FALSE;
  }
  if($maxlen !== null and $maxlen >= 0)
  {
    if($len > $maxlen)
      return FALSE;
  }
  if($validchars !== null)
  {
    $res = TRUE;
    $strarray = utf8stringtoarray($str);
    $validcharsarray = utf8stringtoarray($validchars);
    foreach($strarray as $char)
    {
      if(!in_array($char, $validcharsarray))
      {
        $res = FALSE;
        break;
      }
    }
    if(!$res)
     return $res;
  }

  return TRUE;
}

function validateintstring(&$str, $minlen, $maxlen, $minvalue, $maxvalue)
{
  global $app_intcharacters;

  if(!validatestring($str, $minlen, $maxlen, $app_intcharacters))
    return FALSE;

  $res = intval($str);
  if($minvalue !== null and $minvalue > $res)
    return FALSE;
  if($maxvalue !== null and $maxvalue < $res)
    return FALSE;

  $str = app_db_clearquotes(strval($res));
  return TRUE;
}

function validatedatestring(&$str, $compdate, $min_diff, $max_diff, $force, $null)
{
  global $app_regex;

  $str = trim($str);

  if(($str === null or $str === '') and $null)
  {
    $str = '';
    return TRUE;
  }

  if(($compdate === null or $compdate === '') and $force)
    return FALSE;
  
  $ret = app_check_date($str, $compdate, $min_diff, $max_diff);
  if(!$ret)
    return FALSE;

  $query = "
    SELECT date_format(
      str_to_date(
        '$str',
        '{$app_regex['sb_dateformat']}'),
      '{$app_regex['sb_dateformat']}') AS 'Date'";
  $db_query_res = app_db_query($query);
  $db_query_row = app_db_fetch($db_query_res);
  $str = $db_query_row['Date'];
  app_db_free($db_query_res);

  return TRUE;
}

function utf8stringtoarray($string)
{ 
  $strlen = mb_strlen($string); 
  while($strlen)
  { 
    $array[] = mb_substr($string, 0, 1,"UTF-8");
    $string = mb_substr($string, 1, $strlen, "UTF-8");
    $strlen = mb_strlen($string);
  } 
  return $array; 
} 

function getstatusid($value)
{
  global $status;

  $result = null;
  $keys = array_keys($status);
  for($index = 0; $index < count($keys); $index++)
  {
    if($status[$keys[$index]] == $value)
    {
      $result = $keys[$index];
      break;
    }
  }

  return $result;
}

function getgenderid($value)
{
  global $gender;

  $result = null;
  $keys = array_keys($gender);
  for($index = 0; $index < count($keys); $index++)
  {
    if($gender[$keys[$index]] == $value)
    {
      $result = $keys[$index];
      break;
    }
  }

  return $result;
}

function updatefrompost($index, &$var)
{
  if(isset($_POST[$index]) and $_POST[$index] != null and $_POST[$index] != '')
  {
    $var = $_POST[$index];
    return TRUE;
  }
  else
    return FALSE;
}

function updateformfrompost($name, &$var, $boolean = FALSE)
{
  $formname = "form-".$name;

  $result = "";
  if($boolean)
    $result = FALSE;

  if(isset($_POST[$formname]) and $_POST[$formname] !== null and $_POST[$formname] !== "")
  {
    if($boolean)
      $result = TRUE;
    else
      $result = $_POST[$formname];
    $var = $result;
    return TRUE;
  }
  else
  {
    if($boolean)
    {
      $var = FALSE;
      return TRUE;
    }
    else
      return FALSE;
  }
}

function updatefromget($index, &$var)
{
  if(isset($_GET[$index]) and $_GET[$index] != null and $_GET[$index] != '')
  {
    $var = $_GET[$index];
    return TRUE;
  }
  else
    return FALSE;
}

function getstatuscaption($status)
{
  global $str;

  if($status == 0)
    return $str['status_inactive'];
  else if($status == 1)
    return $str['status_active'];
  else if($status == 2)
    return $str['status_deleted'];

  return '';
}

function reseterrorcaptions()
{
  global $error_captions;

  $error_captions = array();
}

function errorcaptionadd($name)
{
  global $error_captions;
  
  $error_captions[] = "form-".$name;
}

function checkfile($name)
{
  global $app_limits;

  if(!isset($_FILES['form-'.$name]))
    return FALSE;
  if(!isset($_FILES['form-'.$name]['error']))
    return FALSE;
  if(is_array($_FILES['form-'.$name]['error']))
    return FALSE;
  if($_FILES['form-'.$name]['error'] != UPLOAD_ERR_OK)
    return FALSE;
  if($_FILES['form-'.$name]['size'] > $app_limits['filesize'])
    return FALSE;

  return TRUE;
}

?>
