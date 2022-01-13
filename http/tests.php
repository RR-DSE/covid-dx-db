<?php
// tests.php (utf-8)
// 
// Edited by: RR-DSE
// Timestamp: 22-01-12 20:14:13

//---
//App init
//---

require("app-base.php");
require("tools.php");

$app_id = "tests";
$app_curr = "tests.php";
$session_id = app_get_session();
$session_needed = true;
$app_header_title = $captions['tests'];
$app_str = $app_tests_str;
$app_layout = $app_tests_layout;
$app_nav_array = array( 
  array($captions['portal'], "portal.php", ""), 
  array($captions['patients'], "patients.php", ""), 
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
    'ids' => array(
      "all",
      "inpatient",
      "outpatient",
      "outpatientquarant",
      "outpatientresolved",
      "discharged",
      "deceased",
      "unknown"),
    'captions' => array(
      $app_str['all'],
      $app_str['inpatient'], 
      $app_str['outpatient'], 
      $app_str['outpatientquarant'], 
      $app_str['outpatientresolved'], 
      $app_str['discharged'],
      $app_str['deceased'],
      $app_str['unknown']),
    'dbalias' => array(
      'inpatient' => "(B.patient_status = 'inpatient')",
      'outpatient' => "(B.patient_status = 'outpatient')",
      'outpatientquarant' => "(B.patient_status = 'outpatientquarant')",
      'outpatientresolved' => "(B.patient_status = 'outpatientresolved')",
      'discharged' => "(B.patient_status = 'discharged')",
      'deceased' => "(B.patient_status = 'deceased')",
      'unknown' => "(B.patient_status = 'unknown')"
    )
  );
  
  $dependencies['filter_sarscov2status_lists'] = array(
    'ids' => array(
      "all",
      "notest",
      "waiting",
      "errorinconclusive",
      "negative",
      "positive",
      "nostateportal2"),
    'captions' => array(
      $app_str['all'],
      $app_str['notest'],
      $app_str['waiting'],
      $app_str['errorinconclusive'],
      $app_str['negative'],
      $app_str['positive'],
      $app_str['nostateportal2']),
    'dbalias' => array(
      'notest' => "(A.result_code = 'notest' OR A.result_code IS NULL)",
      'waiting' => "(A.result_code = 'waiting')",
      'negative' => "(A.result_code = 'negative')",
      'positive' => "(A.result_code = 'positive')",
      'errorinconclusive' => "(A.result_code = 'error' OR A.result_code = 'inconclusive')",
      'nostateportal2' => "(A.state_report_1 = 0 OR A.state_report_1 IS NULL)")
  );
}

function loaddbdependencies()
{
  global $dependencies;
  global $status, $app_str;

  $query = "
    SELECT
      B.id AS DepartmentID,
      B.title AS DepartmentTitle 
    FROM patients AS A
    LEFT JOIN departments AS B
      ON B.id = A.department
    WHERE A.status <> {$status['deleted']}
    GROUP BY DepartmentID, DepartmentTitle
    ORDER BY DepartmentTitle";
  $dependencies['filter_department_lists'] = array(
    'ids' => array('all'),
    'captions' => array($app_str['all'])
    );
  $db_query_res = app_db_query($query);
  while($db_query_row = app_db_fetch($db_query_res))
  {
    if(isset($db_query_row['DepartmentID']))
    {
      $dependencies['filter_department_lists']['ids'][] = $db_query_row['DepartmentID'];
      $dependencies['filter_department_lists']['captions'][] = $db_query_row['DepartmentTitle'];
    }
  }
  app_db_free($db_query_res);
}

function filterprepare()
{
  global $filter_data, $dependencies;
  global $app_str;
  
  $filter_data['filter'] = false;
  $filter_data['page'] = 1;
  $filter_data['order'] = 1;
  $filter_data['link'] = array();
  $filter_data['dbstr'] = "(A.status <> 2 AND A.test_code = 'sarscov2')";

  $filter_data['elements'] = array(
    array(
      'id' => "department",
      'dbalias' => "B.department",
      'caption' => $app_str['department'],
      'lists' => $dependencies['filter_department_lists'],
      'selection' => "all",
      'formstyle' => "medium"
      ),
    array(
      'id' => "sarscov2status",
      'caption' => $app_str['sarscov2status'],
      'lists' => $dependencies['filter_sarscov2status_lists'],
      'selection' => "all",
      'formstyle' => "medium"
      ),
    array(
      'id' => "status",
      'caption' => $app_str['status'],
      'lists' => $dependencies['filter_status_lists'],
      'selection' => "all",
      'formstyle' => "medium"
      ),
    array(
      'type' => "text",
      'id' => "record",
      'dbalias' => "B.record",
      'caption' => $app_str['record'],
      'selection' => "",
      'maxlength' => "record",
      'size' => "small",
      'formstyle' => "small"
      ),
    array(
      'type' => "textlike",
      'id' => "name",
      'dbalias' => "B.name",
      'caption' => $app_str['name'],
      'selection' => "",
      'maxlength' => "name",
      'size' => "medium",
      'formstyle' => "medium"
      ),
    array(
      'type' => "text",
      'id' => "sampleid",
      'dbalias' => "A.sample_id",
      'caption' => $app_str['sampleid'],
      'selection' => "",
      'maxlength' => "record",
      'size' => "small",
      'formstyle' => "small"
      ),
    array(
      'type' => "textdatemin",
      'id' => "firstsampledate",
      'dbalias' => "A.sample_date",
      'caption' => $app_str['firstsampledate'],
      'selection' => "",
      'maxlength' => "date",
      'size' => "small",
      'formstyle' => "small"
      ),
    array(
      'type' => "textdatemax",
      'id' => "secondsampledate",
      'dbalias' => "A.sample_date",
      'caption' => $app_str['secondsampledate'],
      'selection' => "",
      'maxlength' => "date",
      'size' => "small",
      'formstyle' => "small"
      )
    );
}

function filtersetup()
{
  global $filter_data;
  global $session_data;

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
  
  if($session_data['profile'] == 3)
    $filter_data['elements'][1]['selection'] = "all";

  if($filter_data['elements'][0]['selection'] == "none")
    $filter_data['elements'][0]['selection'] = "all";

  $count = 0;
  foreach($filter_data['elements'] as $element)
  {
    if(!isset($element['type']) or $element['type'] == "select")
    {
      if($element['selection'] != "all")
      {
        $filter_data['filter'] = true;
        if($filter_data['dbstr'] != "")
          $filter_data['dbstr'] .= ' AND ';
        if(isset($element['lists']['dbids']))
          $filter_data['dbstr'] .= $element['dbalias']." = ".$element['lists']['dbids'][$element['selection']];
        else if(isset($element['lists']['dbalias']))
          $filter_data['dbstr'] .= $element['lists']['dbalias'][$element['selection']];
        else
          $filter_data['dbstr'] .= $element['dbalias']." = ".$element['selection'];
      }
    }
    if(isset($element['type']) and $element['type'] == "text")
    {
      if($element['selection'] != "")
      {
        $filter_data['filter'] = true;
        if($filter_data['dbstr'] != "")
          $filter_data['dbstr'] .= ' AND ';
        if(isset($element['lists']['dbids']))
          $filter_data['dbstr'] .= $element['dbalias']." = '".$element['lists']['dbids'][$element['selection']]."'";
        else if(isset($element['lists']['dbalias']))
          $filter_data['dbstr'] .= $element['lists']['dbalias'][$element['selection']];
        else
          $filter_data['dbstr'] .= $element['dbalias']." = '".$element['selection']."'";
      }
    }
    if(isset($element['type']) and $element['type'] == "textdatemin")
    {
      if($element['selection'] != "")
      {
        if(!validatedatestring($element['selection'], null, 0, 0, FALSE, FALSE))
          $filter_data['elements'][$count]['selection'] = "";
        else
        {
          $filter_data['filter'] = true;
          if($filter_data['dbstr'] != "")
            $filter_data['dbstr'] .= ' AND ';
          if(isset($element['lists']['dbids']))
            $filter_data['dbstr'] .= $element['dbalias']." >= ".getstringdb($element['lists']['dbids'][$element['selection']], "date", FALSE);
          else if(isset($element['lists']['dbalias']))
            $filter_data['dbstr'] .= $element['lists']['dbalias'][$element['selection']];
          else
            $filter_data['dbstr'] .= $element['dbalias']." >= ".getstringdb($element['selection'], "date", FALSE);
        }
      }
    }
    if(isset($element['type']) and $element['type'] == "textdatemax")
    {
      if($element['selection'] != "")
      {
        if(!validatedatestring($element['selection'], null, 0, 0, FALSE, FALSE))
          $filter_data['elements'][$count]['selection'] = "";
        else
        {
          $filter_data['filter'] = true;
          if($filter_data['dbstr'] != "")
            $filter_data['dbstr'] .= ' AND ';
          if(isset($element['lists']['dbids']))
            $filter_data['dbstr'] .= $element['dbalias']." <= ".getstringdb($element['lists']['dbids'][$element['selection']], "date", FALSE);
          else if(isset($element['lists']['dbalias']))
            $filter_data['dbstr'] .= $element['lists']['dbalias'][$element['selection']];
          else
            $filter_data['dbstr'] .= $element['dbalias']." <= ".getstringdb($element['selection'], "date", FALSE);
        }
      }
    }
    if(isset($element['type']) and $element['type'] == "textlike")
    {
      if($element['selection'] != "")
      {
        $filter_data['filter'] = true;
        if($filter_data['dbstr'] != "")
          $filter_data['dbstr'] .= ' AND ';
        if(isset($element['lists']['dbids']))
        {
          $selection = preg_replace("/\s+/", "%", $element['lists']['dbids'][$element['selection']]);
          $filter_data['dbstr'] .= $element['dbalias']." LIKE '%".$selection."%'";
        }
        else if(isset($element['lists']['dbalias']))
        {
          $filter_data['dbstr'] .= $element['lists']['dbalias'][$element['selection']];
        }
        else
        {
          $selection = preg_replace("/\s+/", "%", $element['selection']);
          $filter_data['dbstr'] .= $element['dbalias']." LIKE '%".$selection."%'";
        }
      }
    }
    $filter_data['link']["form-filter-".$element['id']] = $element['selection'];
    $count = $count + 1;
  }

  if($filter_data['dbstr'] != "")
    $filter_data['dbstr'] = "WHERE ".$filter_data['dbstr'];

  updatefromget("order", $filter_data['order']);
  $filter_data['dbstrorder'] = "ORDER BY SampleID DESC";
  if($filter_data['order'] == "1")
    $filter_data['dbstrorder'] = "ORDER BY Name ASC, SampleID DESC";
  else if($filter_data['order'] == "2")
    $filter_data['dbstrorder'] = "ORDER BY Age ASC, SampleID DESC";
  else if($filter_data['order'] == "3")
    $filter_data['dbstrorder'] = "ORDER BY Record ASC, SampleID Desc";
  else if($filter_data['order'] == "4")
    $filter_data['dbstrorder'] = "ORDER BY PatientStatus ASC, SampleID DESC";
  else if($filter_data['order'] == "5")
    $filter_data['dbstrorder'] = "ORDER BY DepartmentTitle ASC, SampleID DESC";
  else if($filter_data['order'] == "6")
    $filter_data['dbstrorder'] = "ORDER BY SampleID DESC";
  else if($filter_data['order'] == "7")
    $filter_data['dbstrorder'] = "ORDER BY A.sample_date DESC, SampleID DESC";
  else if($filter_data['order'] == "8")
    $filter_data['dbstrorder'] = "ORDER BY Result ASC, SampleID DESC";
  else if($filter_data['order'] == "9")
    $filter_data['dbstrorder'] = "ORDER BY A.result_datetime DESC, SampleID DESC";
  else if($filter_data['order'] == "10")
    $filter_data['dbstrorder'] = "ORDER BY StateReport1Status ASC, SampleID DESC";
  else
    $filter_data['order'] = 1;
  
  updatefromget("page", $filter_data['page']);
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
  global $session_data;

  $form_elements = array();
  $counter = 0;
  foreach($filter_data['elements'] as $element)
  {
    if(!isset($element['type']) or $element['type'] == "select")
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
      $counter++;
    }
    if(isset($element['type']) and ($element['type'] == "text" or $element['type'] == "textlike" or $element['type'] == "textdatemin" or $element['type'] == "textdatemax"))
    {
      $form_elements[] = array(
        'type' => "text",
        'name' => "filter-".$element['id'],
        'caption' => $app_str[$element['id']],
        'value' => $element['selection'],
        'size' => $element['size'],
        'style' => $element['formstyle'],
        'onchange' => "filter",
        'maxlength' => $element['maxlength']
      );
      $counter++;
    }
  }
  if($session_data['profile'] == 3)
    $form_elements[1]['disabled'] = True;
  if($counter % 2 !== 0)
  {
    $form_elements[] = array(
      'type' => "row_end"
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
    "tests.php",
    array('filter' => "active"),
    "medium",
    "filter",
    $form_elements,
    False
  );
}

function formoptions()
{
  global $app_str;
  global $filter_data;

  return
  
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
    "edit-test.php",
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
  global $status, $teststatus;
  global $session_id, $session_data;
  
  $query = "
    SELECT
      A.id AS Test,
      B.name AS Name,
      TIMESTAMPDIFF(YEAR, B.birthday, CURDATE()) AS Age,
      B.record AS Record,
      B.state_id_1 AS StateID1,
      B.patient_status AS PatientStatus,
      DATE_FORMAT(B.birthday, '{$app_regex['sb_dateformat']}') AS Birthday,
      C.title AS DepartmentTitle,
      A.sample_id AS SampleID,
      DATE_FORMAT(A.sample_date, '{$app_regex['sb_dateformat']}') AS SampleDate,
      A.result_code AS Result,
      DATE_FORMAT(A.result_datetime, '{$app_regex['sb_datetimeformat']}') AS ResultDatetime,
      A.state_report_1 AS StateReport1Status
     FROM tests AS A
     LEFT JOIN patients AS B
       ON B.id = A.patient_id
     LEFT JOIN departments AS C
       ON C.id = B.department
    {$filter_data['dbstr']}
    {$filter_data['dbstrorder']}
    ";
  
  $db_query_res = app_db_query("SELECT COUNT(1) AS Count FROM ($query) AS A");
  $db_query_row = app_db_fetch($db_query_res);
  $rowcount = $db_query_row['Count'];
  app_db_free($db_query_res);

  if($rowcount == 0)
  {
    app_info_print($app_str['infonoitems']);
    formoptions();
  }
  else 
  {
    formoptions();

    $totalcount = $rowcount;
    $pagecount = ceil($rowcount / $app_layout['maxrowcount']);
    if($pagecount < 1)
      $pagecount = 1;
    $rowoffset = ($filter_data['page'] - 1) * $app_layout['maxrowcount'];
    
    $query = $query . " LIMIT $rowoffset, {$app_layout['maxrowcount']}";

    $db_query_res = app_db_query($query);
    $db_query_rowcount = app_db_numrows($db_query_res);
    $rowcount = $db_query_rowcount;
    
    app_table_start(
      array(
        $app_str['name'],
        $app_str['age'],
        $app_str['record'],
        $app_str['status'],
        $app_str['department'],
        $app_str['sampleid'],
        $app_str['sampledate'],
        $app_str['result'],
        $app_str['resultdatetime']
      ),
      $app_layout['colwidths'],
      $filter_data['order'],
      $totalcount,
      $pagecount,
      $filter_data['page'],
      $filter_data['link'],
      $app_layout['tableheight']
    );

    while($db_query_row = app_db_fetch($db_query_res))
    {
      $testresult = getformdb($db_query_row, 'Result');
      if($testresult == "")
        $testresult = "notest";
      $teststatusstr = $app_str[$testresult];
      $teststatusclass = "link";
      if($db_query_row['Result'] == "negative")
        $teststatusclass = "link-green";
      if($db_query_row['Result'] == "positive")
        $teststatusclass = "link-red";
      if($db_query_row['Result'] == "waiting")
        $teststatusclass = "link-orange";
      if($db_query_row['Result'] == "error")
        $teststatusclass = "link-orange";
      if($db_query_row['Result'] == "inconclusive")
        $teststatusclass = "link-red";
      if(getformdb($db_query_row, "PatientStatus") == "")
      {
        app_error_print($app_str['errordb']);
        app_end();
      }
      $statusstr = $app_str[$db_query_row['PatientStatus']];
      $statusclass = "link";
      if($db_query_row['PatientStatus'] == "inpatient")
        $statusclass = "link-blue";
      if($db_query_row['PatientStatus'] == "discharged")
        $statusclass = "link-green";
      if($db_query_row['PatientStatus'] == "deceased")
        $statusclass = "link-red";
      if($db_query_row['PatientStatus'] == "outpatient")
        $statusclass = "link-orange";
      if($db_query_row['PatientStatus'] == "outpatientquarant")
        $statusclass = "link-red";
      if($db_query_row['PatientStatus'] == "outpatientresolved")
        $statusclass = "link-blue";
      if($db_query_row['PatientStatus'] == "unknown")
        $statusclass = "link-orange";
      
      $stateportal2str = getformdb($db_query_row, "StateReport1Status", TRUE)? $app_str['yes']: $app_str['no'];
      $stateportal2strclass = "link";
      if(getformdb($db_query_row, "StateReport1Status", TRUE))
        $stateportal2strclass = "link-green";
      else
        $stateportal2strclass = "link-orange";
      
      $patientslink = "patients.php?id=$session_id&form-filter-record={$db_query_row['Record']}&form-filter-name={$db_query_row['Name']}&form-filter-birthday={$db_query_row['Birthday']}&form-filter-stateid1={$db_query_row['StateID1']}";
      $testlink = "edit-test.php?id=$session_id&item={$db_query_row['Test']}";

      $teststatusdatestr = $db_query_row['ResultDatetime'];
      if($session_data['profile'] == 3)
      {
        $teststatusstr = "";
        $teststatusdatestr = "";
        $stateportal2str = "";
        $testlink = $patientslink;
      }
      if($session_data['profile'] == 4 or $session_data['profile'] == 5)
      {
        $testlink = $patientslink;
      }
      
      app_table_row_ex(
        array(
          $db_query_row['Name'],
          $db_query_row['Age'],
          $db_query_row['Record'],
          $statusstr,
          $db_query_row['DepartmentTitle'],
          $db_query_row['SampleID'],
          $db_query_row['SampleDate'],
          $teststatusstr,
          $teststatusdatestr
        ),
        '',
        array(
          $patientslink,
          $patientslink,
          $patientslink,
          $patientslink,
          $patientslink,
          $testlink,
          $testlink,
          $testlink,
          $testlink
        ),
        $db_query_row['Test'],
        null,
        null,
        null,
        array(
          "link",
          "link",
          "link",
          $statusclass,
          "link",
          "link",
          "link",
          $teststatusclass,
          "link"
        ),
        $app_layout['colstyles']
      );
    }
    app_table_end();
    app_db_free($db_query_res);
  }
}

?>
