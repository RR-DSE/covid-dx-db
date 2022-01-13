<?php
// patients.php (utf-8)
// 
// Edited by: RR-DSE
// Timestamp: 22-01-12 20:07:47

//---
//App init
//---

require("app-base.php");
require("tools.php");

$app_id = "patients";
$app_curr = "patients.php";
$session_id = app_get_session();
$session_needed = true;
$app_header_title = $captions['patients'];
$app_str = $app_patients_str;
$app_layout = $app_patients_layout;
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
    'ids' => array(
      "all",
      "inpatient",
      "outpatient",
      "outpatientquarant",
      "outpatientresolved",
      "discharged",
      "deceased",
      "nostateportal1",
      "positiveresults",
      "unknown"),
    'captions' => array(
      $app_str['all'],
      $app_str['inpatient'], 
      $app_str['outpatient'], 
      $app_str['outpatientquarant'], 
      $app_str['outpatientresolved'], 
      $app_str['discharged'],
      $app_str['deceased'],
      $app_str['nostateportal1'],
      $app_str['positiveresults'],
      $app_str['unknown']),
    'dbalias' => array(
      'inpatient' => "(A.patient_status = 'inpatient')",
      'outpatient' => "(A.patient_status = 'outpatient')",
      'outpatientquarant' => "(A.patient_status = 'outpatientquarant')",
      'outpatientresolved' => "(A.patient_status = 'outpatientresolved')",
      'discharged' => "(A.patient_status = 'discharged')",
      'deceased' => "(A.patient_status = 'deceased')",
      'unknown' => "(A.patient_status = 'unknown')",
      'nostateportal1' => "(A.stateportal1 = 0 OR A.stateportal1 IS NULL)",
      'positiveresults' => "(IFNULL(C2.TestCount, 0) > 0)"
    )
  );
  
  $dependencies['filter_sarscov2status_lists'] = array(
    'ids' => array(
      "all",
      "notest",
      "waiting",
      "errorinconclusive",
      "negative",
      "positive"),
    'captions' => array(
      $app_str['all'],
      $app_str['notest'],
      $app_str['waiting'],
      $app_str['errorinconclusive'],
      $app_str['negative'],
      $app_str['positive']),
    'dbalias' => array(
      'notest' => "(B2.result_code = 'notest' OR B2.result_code IS NULL)",
      'waiting' => "(B2.result_code = 'waiting')",
      'negative' => "(B2.result_code = 'negative')",
      'positive' => "(B2.result_code = 'positive')",
      'errorinconclusive' => "(B2.result_code = 'error' OR B2.result_code = 'inconclusive')")
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
  $filter_data['dbstr'] = "(A.status <> 2)";

  $filter_data['elements'] = array(
    array(
      'id' => "department",
      'dbalias' => "A.department",
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
      'type' => "textlike",
      'id' => "name",
      'dbalias' => "A.name",
      'caption' => $app_str['name'],
      'selection' => "",
      'maxlength' => "name",
      'size' => "medium",
      'formstyle' => "medium"
      ),
    array(
      'type' => "text",
      'id' => "record",
      'dbalias' => "A.record",
      'caption' => $app_str['record'],
      'selection' => "",
      'maxlength' => "record",
      'size' => "small",
      'formstyle' => "small"
      ),
    array(
      'type' => "text",
      'id' => "stateid1",
      'dbalias' => "A.state_id_1",
      'caption' => $app_str['stateid1'],
      'selection' => "",
      'maxlength' => "record",
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
  }

  if($filter_data['dbstr'] != "")
    $filter_data['dbstr'] = "WHERE ".$filter_data['dbstr'];

  updatefromget("order", $filter_data['order']);
  $filter_data['dbstrorder'] = "ORDER BY Name ASC";
  if($filter_data['order'] == "1")
    $filter_data['dbstrorder'] = "ORDER BY Name ASC";
  else if($filter_data['order'] == "2")
    $filter_data['dbstrorder'] = "ORDER BY Age ASC, DepartmentTitle ASC, Name ASC";
  else if($filter_data['order'] == "3")
    $filter_data['dbstrorder'] = "ORDER BY Record ASC, DepartmentTitle ASC, Name ASC";
  else if($filter_data['order'] == "4")
    $filter_data['dbstrorder'] = "ORDER BY StateID1 ASC, DepartmentTitle ASC, Name ASC";
  else if($filter_data['order'] == "5")
    $filter_data['dbstrorder'] = "ORDER BY PatientStatus ASC, DepartmentTitle ASC, Name ASC";
  else if($filter_data['order'] == "6")
    $filter_data['dbstrorder'] = "ORDER BY DepartmentTitle ASC, Name ASC";
  else if($filter_data['order'] == "7")
    $filter_data['dbstrorder'] = "ORDER BY A.admittance_date ASC, DepartmentTitle ASC, Name ASC";
  else if($filter_data['order'] == "8")
    $filter_data['dbstrorder'] = "ORDER BY SARSCoV2Result ASC, DepartmentTitle ASC, Name ASC";
  else if($filter_data['order'] == "9")
    $filter_data['dbstrorder'] = "ORDER BY B2.sample_date DESC, DepartmentTitle ASC, Name ASC";
  else if($filter_data['order'] == "10")
    $filter_data['dbstrorder'] = "ORDER BY TestCount DESC, DepartmentTitle ASC, Name ASC";
  else if($filter_data['order'] == "11")
    $filter_data['dbstrorder'] = "ORDER BY PositiveCount DESC, DepartmentTitle ASC, Name ASC";
  else if($filter_data['order'] == "12")
    $filter_data['dbstrorder'] = "ORDER BY StatePortal1Status ASC, DepartmentTitle ASC, Name ASC";
  else if($filter_data['order'] == "13")
    $filter_data['dbstrorder'] = "ORDER BY HCWorkerStatus ASC, DepartmentTitle ASC, Name ASC";
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
    if(isset($element['type']) and ($element['type'] == "text" or $element['type'] == "textlike"))
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
    "patients.php",
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
    "edit-patient.php",
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
  global $session_data, $session_id;
  
  $query = "
    SELECT
      A.id AS Patient,
      A.name AS Name,
      DATE_FORMAT(A.birthday, '{$app_regex['sb_dateformat']}') AS Birthday,
      TIMESTAMPDIFF(YEAR, A.birthday, CURDATE()) AS Age,
      A.record AS Record,
      A.state_id_1 AS StateID1,
      A.patient_status AS PatientStatus,
      F.title AS DepartmentTitle,
      DATE_FORMAT(A.admittance_date, '{$app_regex['sb_dateformat']}') AS AdmittanceDate,
      A.floor AS Floor,
      A.room AS Room,
      A.bed AS Bed,
      A.hcworker AS HCWorkerStatus,
      B2.result_code AS SARSCoV2Result,
      DATE_FORMAT(B2.sample_date, '{$app_regex['sb_dateformat']}') AS SARSCoV2Date,
      IFNULL(C1.TestCount, 0) AS TestCount,
      IFNULL(C2.TestCount, 0) AS PositiveCount,
      A.stateportal1 AS StatePortal1Status,
      A.status AS Status,
      D.name AS AddName,
      E.name AS ModName,
      DATE_FORMAT(A.add_datetime, '{$app_regex['sb_dateformat']}') AS AddDateTime,
      DATE_FORMAT(A.mod_datetime, '{$app_regex['sb_dateformat']}') AS ModDateTime
    FROM patients AS A
    LEFT JOIN
    (
      SELECT
        patient_id AS PatientID,
        MAX(sample_id) AS TestSample
      FROM tests
      WHERE
        test_code = 'sarscov2'
        AND result_code IS NOT NULL
        AND result_code != 'notest'
        AND result_code != ''
        AND status <> 2
      GROUP BY patient_id
    ) AS B1
       ON B1.PatientID = A.id
    LEFT JOIN tests AS B2
      ON (B2.sample_id = B1.TestSample AND B2.status<>2)
    LEFT JOIN
    (
      SELECT 
        patient_id AS PatientID,
        COUNT(1) AS TestCount
      FROM tests
      WHERE
        test_code = 'sarscov2'
        AND result_code IS NOT NULL
        AND result_code != 'notest'
        AND result_code != ''
        AND status <> 2
      GROUP BY patient_id
    ) AS C1
      ON C1.PatientID = A.id
    LEFT JOIN
    (
      SELECT 
        patient_id AS PatientID,
        COUNT(1) AS TestCount
      FROM tests
      WHERE
        test_code = 'sarscov2'
        AND result_code = 'positive'
        AND status <> 2
      GROUP BY patient_id
    ) AS C2
      ON C2.PatientID = A.id
    LEFT JOIN users AS D
      ON D.id = A.add_user
    LEFT JOIN users AS E
      ON E.id = A.mod_user
    LEFT JOIN departments AS F
      ON F.id = A.department
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
        $app_str['stateid1'],
        $app_str['status'],
        $app_str['department'],
        $app_str['admittancedate'],
        $app_str['sarscov2'],
        $app_str['sarscov2date'],
        $app_str['sarscov2count'],
        $app_str['positivecount'],
        $app_str['stateportal1'],
        $app_str['hcworker']
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
      $testresult = getformdb($db_query_row, 'SARSCoV2Result');
      if($testresult == "")
        $testresult = "notest";
      $teststatusstr = $app_str[$testresult];
      $teststatusclass = "link";
      $stateportal1str = getformdb($db_query_row, "StatePortal1Status", TRUE)? $app_str['yes']: $app_str['no'];
      $hcworkerstr = getformdb($db_query_row, "HCWorkerStatus", TRUE)? $app_str['yes']: $app_str['no'];
      $stateportal1strclass = "link";

      if($db_query_row['SARSCoV2Result'] == "negative")
        $teststatusclass = "link-green";
      if($db_query_row['SARSCoV2Result'] == "positive")
        $teststatusclass = "link-red";
      if($db_query_row['SARSCoV2Result'] == "waiting")
        $teststatusclass = "link-orange";
      if($db_query_row['SARSCoV2Result'] == "error")
        $teststatusclass = "link-orange";
      if($db_query_row['SARSCoV2Result'] == "inconclusive")
        $teststatusclass = "link-red";
      
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

      $positivecountstr = getformdb($db_query_row, 'PositiveCount');
      if($positivecountstr == "0")
        $positivecountclass = "link-green";
      else
        $positivecountclass = "link-red";
      
      if(getformdb($db_query_row, "StatePortal1Status", TRUE))
        $stateportal1strclass = "link-green";
      else
        $stateportal1strclass = "link-orange";
      
      $teststatusdatestr = $db_query_row['SARSCoV2Date'];

      $editpatientlink = "edit-patient.php?id=$session_id&item={$db_query_row['Patient']}";
      $testslink = "tests.php?id=$session_id&form-filter-record={$db_query_row['Record']}&form-filter-name={$db_query_row['Name']}&form-filter-birthday={$db_query_row['Birthday']}&form-filter-stateid1={$db_query_row['StateID1']}";
      
      $testcountstr = $db_query_row['TestCount'];
      $positivecountstr = $db_query_row['PositiveCount'];
      
      if($session_data['profile'] == 3)
      {
        $teststatusstr = "";
        $teststatusdatestr = "";
        $stateportal1str = "";
        $testcountstr = "";
        $positivecountstr = "";
        $testslink = $editpatientlink;
      }
      
      app_table_row_ex(
        array(
          $db_query_row['Name'],
          $db_query_row['Age'],
          $db_query_row['Record'],
          $db_query_row['StateID1'],
          $statusstr,
          $db_query_row['DepartmentTitle'],
          $db_query_row['AdmittanceDate'],
          $teststatusstr,
          $teststatusdatestr,
          $testcountstr,
          $positivecountstr,
          $stateportal1str,
          $hcworkerstr
        ),
        '',
        array(
          $editpatientlink,
          $editpatientlink,
          $editpatientlink,
          $editpatientlink,
          $editpatientlink,
          $editpatientlink,
          $editpatientlink,
          $testslink,
          $testslink,
          $testslink,
          $testslink,
          $editpatientlink,
          $editpatientlink
        ),
        $db_query_row['Patient'],
        null,
        null,
        null,
        array(
          "link",
          "link",
          "link",
          "link",
          $statusclass,
          "link",
          "link",
          $teststatusclass,
          "link",
          "link",
          $positivecountclass,
          $stateportal1strclass,
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
