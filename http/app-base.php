<?php
// app-base.php (utf-8)
// 
// Edited by: RR-DSE
// Timestamp: 22-01-12 19:44:14

//---
//Dependencies
//---

require('app-consts.php');

//---
//Common messages
//---

function app_error_print($desc)
{
  global $str;

  echo "
    <DIV class=\"app-error\">
    <TABLE class=\"app-error\"><TR><TD class=\"caption\">{$str['error']}</TD><TD class=\"desc\">$desc</TD></TR></TABLE>
    </DIV>";
}

function app_debug_print($method, $desc)
{
  global $str;

  echo "
    <DIV class=\"app-debug\">
    <TABLE class=\"app-debug\"><TR><TD class=\"caption\">{$str['debug']}</TD><TD class = \"method\">$method</TD><TD class=\"desc\">$desc</TD></TR></TABLE>
    </DIV>";
}

function app_warning_print($desc)
{
  global $str;

  echo "
    <DIV class=\"app-warning\">
    <TABLE class=\"app-warning\"><TR><TD class=\"caption\">{$str['warning']}</TD><TD class=\"desc\">$desc</TD></TR></TABLE>
    </DIV>";
}

function app_info_print($desc)
{
  global $str;

  echo "
    <DIV class=\"app-info\">
    <TABLE class=\"app-info\"><TR><TD class=\"caption\">{$str['info']}</TD><TD class=\"desc\">$desc</TD></TR></TABLE>
    </DIV>";
}

//---
//Form controls
//---

function app_form_start($form_caption, $form_columns, $form_action, $form_params, $form_caption_width, $form_id, $file_data)
{
  global $app_form_sepsize;
  global $session_id;

  if($form_params == '' or $form_params == null)
    $form_params_tmp = '';
  else
    $form_params_tmp = "&$form_params";

  if($form_id == '' or $form_id == null)
    $form_id_temp = '';
  else
    $form_id_temp = "name=\"$form_id\"";

  $form_columns_temp = 1;
  if($form_columns > $form_columns_temp)
    $form_columns_temp = $form_columns;

  $file_data_temp = "";
  if($file_data)
    $file_data_temp = "enctype=\"multipart/form-data\"";

  echo "<DIV class=\"app-form\">";

  if($form_caption == '' or $form_caption == null) { } else
  {
    echo "<P class=\"app-form-caption\">$form_caption</P>";
  }

  echo "<FORM class=\"app-form\" action=\"$form_action?update=true&id=$session_id$form_params_tmp\" method=\"post\" $form_id_temp $file_data_temp>";
  echo "<TABLE class=\"app-form\">";

  if($form_caption_width == '' or $form_caption_width == null)
  {
    echo "<COLGROUP>";
    for($i = 1; $i < $form_columns_temp; $i++)
    {
      echo "<COL style=\"width:auto\" />";
      echo "<COL style=\"width:auto\" />";
      echo "<COL style=\"width:$app_form_sepsize\" />";
    }
    echo "<COL style=\"width:auto\" />";
    echo "<COL style=\"width:auto\" />";
    echo "</COLGROUP>";
  }
  else
  {
    echo "<COLGROUP>";
    for($i = 1; $i < $form_columns_temp; $i++)
    {
      echo "<COL style=\"width:$form_caption_width\" />";
      echo "<COL style=\"width:auto\" />";
      echo "<COL style=\"width:$app_form_sepsize\" />";
    }
    echo "<COL style=\"width:$form_caption_width\" />";
    echo "<COL style=\"width:auto\" />";
    echo "</COLGROUP>";
  }

  return array("count" => $form_columns_temp, "curr" => 0);
}

function app_form_noupdate_start($form_caption, $form_columns, $form_action, $form_params, $form_caption_width, $form_id, $file_data)
{
  global $app_form_sepsize;
  global $session_id;

  if($form_params == '' or $form_params == null)
    $form_params_tmp = '';
  else
    $form_params_tmp = "&$form_params";

  if($form_id == '' or $form_id == null)
    $form_id_temp = '';
  else
    $form_id_temp = "name=\"$form_id\"";

  $form_columns_temp = 1;
  if($form_columns > $form_columns_temp)
    $form_columns_temp = $form_columns;
  
  $file_data_temp = "";
  if($file_data)
    $file_data_temp = "enctype=multipart/form-data";

  echo "<DIV class=\"app-form\">";

  if($form_caption == '' or $form_caption == null) { } else 
  {
    echo "<P class=\"app-form-caption\">$form_caption</P>";
  }

  echo "<FORM class=\"app-form\" action=\"$form_action?id=$session_id$form_params_tmp\" method=\"post\" $form_id_temp $file_data_temp>";
  echo "<TABLE class=\"app-form\">";

  if($form_caption_width == '' or $form_caption_width == null)
  {
    echo "<COLGROUP>";
    for($i = 1; $i < $form_columns_temp; $i++)
    {
      echo "<COL style=\"width:auto\" />";
      echo "<COL style=\"width:auto\" />";
      echo "<COL style=\"width:$app_form_sepsize\" />";
    }
    echo "<COL style=\"width:auto\" />";
    echo "<COL style=\"width:auto\" />";
    echo "</COLGROUP>";
  }
  else
  {
    echo "<COLGROUP>";
    for($i = 1; $i < $form_columns_temp; $i++)
    {
      echo "<COL style=\"width:$form_caption_width\" />";
      echo "<COL style=\"width:auto\" />";
      echo "<COL style=\"width:$app_form_sepsize\" />";
    }
    echo "<COL style=\"width:$form_caption_width\" />";
    echo "<COL style=\"width:auto\" />";
    echo "</COLGROUP>";
  }

  return array("count" => $form_columns_temp, "curr" => 0);
}

function app_dataform_start($form_caption, $form_action, $form_params, $form_caption_width)
{
  global $session_id;

  if($form_params == '' or $form_params == null)
    $form_params_tmp = '';
  else
    $form_params_tmp = "&$form_params";

  echo "<DIV class=\"app-form\">";
  echo "<P class=\"app-form-caption\">$form_caption</P>";
  echo "<FORM class=\"app-form\" enctype=\"multipart/form-data\" action=\"$form_action?id=$session_id$form_params_tmp\" method=\"post\">";
  echo "<TABLE class=\"app-form\">";

  if($form_caption_width == '' or $form_caption_width == null)
  {
    echo "<COLGROUP>";
    echo "<COL style=\"width:auto\" />";
    echo "<COL style=\"width:auto\" />";
    echo "</COLGROUP>";
  }
  else
  {
    echo "<COLGROUP>";
    echo "<COL style=\"width:$form_caption_width\" />";
    echo "<COL style=\"width:auto\" />";
    echo "</COLGROUP>";
  }
}

function app_form_end(&$form_position)
{
  if($form_position["curr"] > 0)
    echo '</TR>';
  
  $form_position["curr"] = 0;
 
  echo '</TABLE></FORM></DIV>';
}

function app_form_text(&$form_position, $form_caption, $form_name, $form_value, $form_size, $form_maxlength, $form_class, $form_extra, $form_onchange = null)
{
  global $error_captions, $app_caption_error_style;

  if(in_array($form_name, $error_captions))
    $error_caption_override = $app_caption_error_style;
  else
    $error_caption_override = "";
  
  if($form_onchange == "" or $form_onchange == null)
    $form_onchange_temp = '';
  else
    $form_onchange_temp = "onchange = \"document.$form_onchange.submit()\"";
  
  if($form_class == '' or $form_class == null)
    $form_class_tmp = 'class="app-form-regular"';
  else
    $form_class_tmp = "class=\"$form_class\"";

  if($form_position["curr"] == 0)
    echo "<TR>";

  if($form_caption == 'no_caption')
  {
    echo "<TD $form_class_tmp colspan=\"2\"><INPUT type=\"text\" name=\"$form_name\" value=\"".htmlentities($form_value)."\" size=\"$form_size\" maxlength=\"$form_maxlength\" $form_class_tmp $form_extra $form_onchange_temp></TD>";
  }
  else
  {
    echo "<TD $form_class_tmp $error_caption_override>$form_caption</TD>";
    echo "<TD $form_class_tmp><INPUT type=\"text\" name=\"$form_name\" value=\"".htmlentities($form_value)."\" size=\"$form_size\" maxlength=\"$form_maxlength\" $form_class_tmp $form_extra $form_onchange_temp></TD>";
  }

  $form_position["curr"]++;
  if($form_position["curr"] >= $form_position["count"])
  {
    $form_position["curr"] = 0;
    echo "</TR>";
  }
  else
  {
    echo "<TD></TD>";
  }
}

function app_form_text_readonly(&$form_position, $form_caption, $form_name, $form_value, $form_size, $form_maxlength, $form_class, $form_extra)
{
  global $error_captions, $app_caption_error_style;

  if(in_array($form_name, $error_captions))
    $error_caption_override = $app_caption_error_style;
  else
    $error_caption_override = "";

  if($form_class == '' or $form_class == null)
    $form_class_tmp = 'class="app-form-regular"';
  else
    $form_class_tmp = "class=\"$form_class\"";

  if($form_position["curr"] == 0)
    echo "<TR>";
  
  if($form_caption == 'no_caption')
  {
    echo "<TD $form_class_tmp colspan=\"2\"><INPUT type=\"text\" name=\"$form_name\" value=\"".htmlentities($form_value)."\" size=\"$form_size\" maxlength=\"$form_maxlength\" $form_class_tmp $form_extra></TD>";
  }
  else
  {
    echo "<TD $form_class_tmp $error_caption_override>$form_caption</TD>";
    echo "<TD $form_class_tmp><INPUT type=\"text\" name=\"$form_name\" value=\"".htmlentities($form_value)."\" size=\"$form_size\" maxlength=\"$form_maxlength\" $form_class_tmp $form_extra readonly=\"readonly\"></TD>";
  }
  
  $form_position["curr"]++;
  if($form_position["curr"] >= $form_position["count"])
  {
    $form_position["curr"] = 0;
    echo "</TR>";
  }
  else
  {
    echo "<TD></TD>";
  }
}

function app_form_info(&$form_position, $form_caption, $form_value, $form_class)
{
  if($form_class == '' or $form_class == null)
    $form_class_tmp = 'app-form-regular';
  else
    $form_class_tmp = $form_class;

  if($form_position["curr"] == 0)
    echo "<TR>";
  
  $form_class_infocaption = $form_class_tmp.'-caption';

  if($form_caption == 'no_caption')
  {
    echo "<TD class=\"$form_class_infocaption\" colspan=\"2\">$form_value</TD>";
  }
  else
  {
    echo "<TD class=\"$form_class_tmp\">$form_caption</TD>";
    echo "<TD class=\"$form_class_infocaption\">$form_value</TD>";
  }
  
  $form_position["curr"]++;
  if($form_position["curr"] >= $form_position["count"])
  {
    $form_position["curr"] = 0;
    echo "</TR>";
  }
  else
  {
    echo "<TD></TD>";
  }
}

function app_form_password(&$form_position, $form_caption, $form_name, $form_value, $form_size, $form_maxlength, $form_class, $form_extra)
{
  global $error_captions, $app_caption_error_style;

  if(in_array($form_name, $error_captions))
    $error_caption_override = $app_caption_error_style;
  else
    $error_caption_override = "";

  if($form_class == '' or $form_class == null)
    $form_class_tmp = 'class="app-form-regular"';
  else
    $form_class_tmp = "class=\"$form_class\"";
  
  if($form_position["curr"] == 0)
    echo "<TR>";

  if($form_caption == 'no_caption')
  {
    echo "<TD $form_class_tmp colspan=\"2\"><INPUT type=\"password\" name=\"$form_name\" value=\"$form_value\" size=\"$form_size\" maxlength=\"$form_maxlength\" $form_class_tmp $form_extra></TD>";
  }
  else
  {
    echo "<TD $form_class_tmp $error_caption_override>$form_caption</TD>";
    echo "<TD $form_class_tmp><INPUT type=\"password\" name=\"$form_name\" value=\"$form_value\" size=\"$form_size\" maxlength=\"$form_maxlength\" $form_class_tmp $form_extra></TD>";
  }
  
  $form_position["curr"]++;
  if($form_position["curr"] >= $form_position["count"])
  {
    $form_position["curr"] = 0;
    echo "</TR>";
  }
  else
  {
    echo "<TD></TD>";
  }
}

function app_form_check(&$form_position, $form_caption, $form_name, $form_value, $form_text, $form_check, $form_class, $form_extra, $disabled = False)
{
  global $error_captions, $app_caption_error_style;
  
  $disabledstr = "";
  if($disabled)
    $disabledstr = "disabled";

  if(in_array($form_name, $error_captions))
    $error_caption_override = $app_caption_error_style;
  else
    $error_caption_override = "";

  if($form_class == '' or $form_class == null)
    $form_class_tmp = 'class="app-form-regular"';
  else
    $form_class_tmp = "class=\"$form_class\"";
  
  if($form_position["curr"] == 0)
    echo "<TR>";

  if($form_check == null or $form_check == '' or $form_check == FALSE)
    $checked = '';
  else
    $checked = 'checked';

  if($form_caption == 'no_caption')
  {
    echo "<TD $form_class_tmp colspan=\"2\"><INPUT type=\"checkbox\" name=\"$form_name\" value=\"".htmlentities($form_value)."\" $checked $form_class_tmp $form_extra $disabledstr \\>&nbsp;&nbsp;$form_text</TD>";
  }
  else
  {
    echo "<TD $form_class_tmp $error_caption_override>$form_caption</TD>";
    echo "<TD $form_class_tmp><INPUT type=\"checkbox\" name=\"$form_name\" value=\"".htmlentities($form_value)."\" $checked $form_class_tmp $form_extra $disabledstr \\>&nbsp;&nbsp;$form_text</TD>";
  }
  
  $form_position["curr"]++;
  if($form_position["curr"] >= $form_position["count"])
  {
    $form_position["curr"] = 0;
    echo "</TR>";
  }
  else
  {
    echo "<TD></TD>";
  }
}

function app_form_radio(&$form_position, $form_caption, $form_name, $form_value, $form_check, $form_class, $form_extra)
{
  global $error_captions, $app_caption_error_style;

  if(in_array($form_name, $error_captions))
    $error_caption_override = $app_caption_error_style;
  else
    $error_caption_override = "";

  if($form_class == '' or $form_class == null)
    $form_class_tmp = 'class="app-form-regular"';
  else
    $form_class_tmp = "class=\"$form_class\"";
  
  if($form_position["curr"] == 0)
    echo "<TR>";

  if($form_caption == 'no_caption')
  {
    echo "<TD $form_class_tmp colspan=\"2\"><INPUT type=\"radio\" name=\"$form_name\" value=\"".htmlentities($form_value)."\" $form_check $form_class_tmp $form_extra></TD>";
  }
  else
  {
    echo "<TD $form_class_tmp $error_caption_override>$form_caption</TD>";
    echo "<TD $form_class_tmp><INPUT type=\"radio\" name=\"$form_name\" value=\"".htmlentities($form_value)."\" $form_check $form_class_tmp $form_extra></TD>";
  }
  
  $form_position["curr"]++;
  if($form_position["curr"] >= $form_position["count"])
  {
    $form_position["curr"] = 0;
    echo "</TR>";
  }
  else
  {
    echo "<TD></TD>";
  }
}

function app_form_select(&$form_position, $form_caption, $form_name, $form_size, $form_options, $form_optioncaptions, $form_selected, $form_class, $form_extra, $form_onchange, $disabled = False)
{
  global $error_captions, $app_caption_error_style;

  $disabledstr = "";
  if($disabled)
    $disabledstr = "disabled";

  if(in_array($form_name, $error_captions))
    $error_caption_override = $app_caption_error_style;
  else
    $error_caption_override = "";

  if($form_class == '' or $form_class == null)
    $form_class_tmp = 'class="app-form-regular"';
  else
    $form_class_tmp = "class=\"$form_class\"";

  if($form_position["curr"] == 0)
    echo "<TR>";
  
  if($form_onchange == '' or $form_onchange == null)
    $form_onchange_temp = '';
  else
    $form_onchange_temp = "onchange = \"document.$form_onchange.submit()\"";

  if($form_caption == 'no_caption')
  {
    echo "<TD $form_class_tmp colspan=\"2\"><SELECT size=\"$form_size\" name=\"$form_name\" $form_class_tmp $form_extra $form_onchange_temp $disabledstr>";
  }
  else
  {
    echo "<TD $form_class_tmp $error_caption_override>$form_caption</TD>";
    echo "<TD $form_class_tmp><SELECT size=\"$form_size\" name=\"$form_name\" $form_class_tmp $form_extra $form_onchange_temp $disabledstr>";
  }

  $i = 0;
  foreach($form_options as $form_option_elem)
  {
    $caption = $form_optioncaptions[$i];
    if($form_option_elem == $form_selected)
      echo "<OPTION value=\"$form_option_elem\" selected>$caption</OPTION>";
    else
      echo "<OPTION value=\"$form_option_elem\">$caption</OPTION>";
    $i++;
  }

  echo "</SELECT></TD>";
  
  $form_position["curr"]++;
  if($form_position["curr"] >= $form_position["count"])
  {
    $form_position["curr"] = 0;
    echo "</TR>";
  }
  else
  {
    echo "<TD></TD>";
  }
}

function app_form_textarea(&$form_position, $form_caption, $form_name, $form_value, $form_rows, $form_cols, $form_maxlength, $form_class, $form_extra)
{
  global $error_captions, $app_caption_error_style;

  if(in_array($form_name, $error_captions))
    $error_caption_override = $app_caption_error_style;
  else
    $error_caption_override = "";
  
  if($form_class == '' or $form_class == null)
    $form_class_tmp = 'class="app-form-regular"';
  else
    $form_class_tmp = "class=\"$form_class\"";
  
  if($form_position["curr"] == 0)
    echo "<TR>";

  if($form_caption == 'no_caption')
  {
    echo "<TD $form_class_tmp colspan=\"2\"><TEXTAREA name=\"$form_name\" maxlength=\"$form_maxlength\" rows=\"$form_rows\" cols=\"$form_cols\" $form_class_tmp $form_extra>$form_value</TEXTAREA></TD>";
  }
  else
  {
    echo "<TD $form_class_tmp $error_caption_override>$form_caption</TD>";
    echo "<TD $form_class_tmp><TEXTAREA name=\"$form_name\" maxlength=\"$form_maxlength\" rows=\"$form_rows\" cols=\"$form_cols\" $form_class_tmp $form_extra>$form_value</TEXTAREA></TD>";
  }	
  
  $form_position["curr"]++;
  if($form_position["curr"] >= $form_position["count"])
  {
    $form_position["curr"] = 0;
    echo "</TR>";
  }
  else
  {
    echo "<TD></TD>";
  }
}

function app_form_textarea_readonly(&$form_position, $form_caption, $form_name, $form_value, $form_rows, $form_cols, $form_maxlength, $form_class, $form_extra)
{
  global $error_captions, $app_caption_error_style;

  if(in_array($form_name, $error_captions))
    $error_caption_override = $app_caption_error_style;
  else
    $error_caption_override = "";
  
  if($form_class == '' or $form_class == null)
    $form_class_tmp = 'class="app-form-regular"';
  else
    $form_class_tmp = "class=\"$form_class\"";
  
  if($form_position["curr"] == 0)
    echo "<TR>";

  if($form_caption == 'no_caption')
  {
    echo "<TD $form_class_tmp colspan=\"2\"><TEXTAREA name=\"$form_name\" maxlength=\"$form_maxlength\" rows=\"$form_rows\" cols=\"$form_cols\" $form_class_tmp $form_extra readonly=\"readonly\">$form_value</TEXTAREA></TD>";
  }
  else
  {
    echo "<TD $form_class_tmp $error_caption_override>$form_caption</TD>";
    echo "<TD $form_class_tmp><TEXTAREA name=\"$form_name\" maxlength=\"$form_maxlength\" rows=\"$form_rows\" cols=\"$form_cols\" $form_class_tmp $form_extra readonly=\"readonly\">$form_value</TEXTAREA></TD>";
  }	
  
  $form_position["curr"]++;
  if($form_position["curr"] >= $form_position["count"])
  {
    $form_position["curr"] = 0;
    echo "</TR>";
  }
  else
  {
    echo "<TD></TD>";
  }
}

function app_form_submit(&$form_position, $form_caption, $form_name, $form_value, $form_class, $form_extra)
{
  if($form_class == '' or $form_class == null)
    $form_class_tmp = 'class="app-form-regular"';
  else
    $form_class_tmp = "class=\"$form_class\"";
  
  if($form_position["curr"] == 0)
    echo "<TR>";

  if($form_caption == 'no_caption')
  {
    echo "<TD $form_class_tmp colspan=\"2\"><INPUT type=\"submit\" name=\"$form_name\" value=\"$form_value\" $form_class_tmp $form_extra></TD>";
  }
  else
  {
    echo "<TD $form_class_tmp>$form_caption</TD>";
    echo "<TD $form_class_tmp><INPUT type=\"submit\" name=\"$form_name\" value=\"$form_value\" $form_class_tmp $form_extra></TD>";
  }
  
  $form_position["curr"]++;
  if($form_position["curr"] >= $form_position["count"])
  {
    $form_position["curr"] = 0;
    echo "</TR>";
  }
  else
  {
    echo "<TD></TD>";
  }
}

function app_form_button(&$form_position, $form_caption, $form_name, $form_value, $form_class, $form_extra)
{
  if($form_class == '' or $form_class == null)
    $form_class_tmp = 'class="app-form-regular"';
  else
    $form_class_tmp = "class=\"$form_class\"";
  
  if($form_position["curr"] == 0)
    echo "<TR>";

  if($form_caption == 'no_caption')
  {
    echo "<TD $form_class_tmp colspan=\"2\"><INPUT type=\"submit\" name=\"$form_name\" value=\"$form_value\" $form_class_tmp $form_extra></TD>";
  }
  else
  {
    echo "<TD $form_class_tmp>$form_caption</TD>";
    echo "<TD $form_class_tmp><INPUT type=\"submit\" name=\"$form_name\" value=\"$form_value\" $form_class_tmp $form_extra></TD>";
  }
  
  $form_position["curr"]++;
  if($form_position["curr"] >= $form_position["count"])
  {
    $form_position["curr"] = 0;
    echo "</TR>";
  }
  else
  {
    echo "<TD></TD>";
  }
}

function app_form_reset(&$form_position, $form_caption, $form_name, $form_value, $form_class, $form_extra) 
{
  if($form_class == '' or $form_class == null)
    $form_class_tmp = 'class="app-form-regular"';
  else
    $form_class_tmp = "class=\"$form_class\"";
  
  if($form_position["curr"] == 0)
    echo "<TR>";

  if($form_caption == 'no_caption')
  {
    echo "<TD $form_class_tmp colspan=\"2\"><INPUT type=\"reset\" name=\"$form_name\" value=\"$form_value\" $form_class_tmp $form_extra></TD>";
  }
  else
  {
    echo "<TD $form_class_tmp>$form_caption</TD>";
    echo "<TD $form_class_tmp><INPUT type=\"reset\" name=\"$form_name\" value=\"$form_value\" $form_class_tmp $form_extra></TD>";
  }
  
  $form_position["curr"]++;
  if($form_position["curr"] >= $form_position["count"])
  {
    $form_position["curr"] = 0;
    echo "</TR>";
  }
  else
  {
    echo "<TD></TD>";
  }
}

function app_form_file(&$form_position, $form_caption, $form_name, $form_value, $form_size, $form_class, $form_extra)
{
  global $error_captions, $app_caption_error_style;

  if(in_array($form_name, $error_captions))
    $error_caption_override = $app_caption_error_style;
  else
    $error_caption_override = "";
  
  if($form_class == '' or $form_class == null)
    $form_class_tmp = 'class="app-form-regular"';
  else
    $form_class_tmp = "class=\"$form_class\"";
  
  if($form_position["curr"] == 0)
    echo "<TR>";

  if($form_caption == 'no_caption')
  {
    echo "<TD $form_class_tmp colspan=\"2\"><INPUT type=\"file\" name=\"$form_name\" value=\"$form_value\" size=\"$form_size\" $form_class_tmp $form_extra></TD>";
  }
  else
  {
    echo "<TD $form_class_tmp $error_caption_override>$form_caption</TD>";
    echo "<TD $form_class_tmp><INPUT type=\"file\" name=\"$form_name\" value=\"$form_value\" size=\"$form_size\" $form_class_tmp $form_extra></TD>";
  }
  
  $form_position["curr"]++;
  if($form_position["curr"] >= $form_position["count"])
  {
    $form_position["curr"] = 0;
    echo "</TR>";
  }
  else
  {
    echo "<TD></TD>";
  }
}

function app_form_cell_start(&$form_position, $form_caption, $form_class, $form_extra, $form_captionclass = "", $form_captionextra = "")
{
  if($form_class == "" or $form_class == null)
    $strextra = "class=\"app-form-regular\"";
  else
    $strextra = "class=\"$form_class\"";
  
  if($form_captionclass == "")
    $strextracaption = "class=\"app-form-regular\"";
  else
    $strextracaption = "class=\"$form_captionclass\"";

  if($form_position["curr"] == 0)
    echo "<TR>";
  
  if($form_caption == 'no_caption')
  {
    echo "<TD $strextra colspan=\"2\" $form_extra>";
  }
  else
  {
    echo "<TD $strextracaption $form_captionextra>$form_caption</TD>";
    echo "<TD $strextra $form_extra>";
  }
}

function app_form_cell_end(&$form_position)
{
  echo "</TD>";
  
  $form_position["curr"]++;
  if($form_position["curr"] >= $form_position["count"])
  {
    $form_position["curr"] = 0;
    echo "</TR>";
  }
  else
  {
    echo "<TD></TD>";
  }
}

function app_form_row(&$form_position)
{
  if($form_position["curr"] == 0)
    echo "<TR></TR>";
  else
  {
    echo "</TR>";
    $form_position["curr"] == 0;
  }
}

function app_form_row_end(&$form_position)
{
  if($form_position["curr"] > 0)
    echo '</TR>';
  $form_position["curr"] = 0;
}

function app_form_select_add($form_name, $form_size, $form_options, $form_optioncaptions, $form_selected, $form_class, $form_extra, $form_onchange)
{
  if($form_class == '' or $form_class == null)
    $form_class_tmp = 'class="app-form-regular"';
  else
    $form_class_tmp = "class=\"$form_class\"";
  
  if($form_onchange == '' or $form_onchange == null)
    $form_onchange_temp = '';
  else
    $form_onchange_temp = "onchange = \"document.$form_onchange.submit()\"";

  echo "<SELECT size=\"$form_size\" name=\"$form_name\" $form_class_tmp $form_extra $form_onchange_temp>";

  $i = 0;
  foreach($form_options as $form_option_elem)
  {
    $caption = $form_optioncaptions[$i];	
    if($form_option_elem == $form_selected)
      echo "<OPTION value=\"$form_option_elem\" selected>$caption</OPTION>";
    else
      echo "<OPTION value=\"$form_option_elem\">$caption</OPTION>";
    $i++;
  }

  echo "</SELECT>";
}

function app_form_text_add($form_name, $form_value, $form_size, $form_maxlength, $form_class, $form_extra)
{
  if($form_class == '' or $form_class == null)
    $form_class_tmp = 'class="app-form-regular"';
  else
    $form_class_tmp = "class=\"$form_class\"";

  echo "<INPUT type=\"text\" name=\"$form_name\" value=\"".htmlentities($form_value)."\" size=\"$form_size\" maxlength=\"$form_maxlength\" $form_class_tmp $form_extra>";
}

function app_form_submit_add($form_name, $form_value, $form_class, $form_extra)
{
  if($form_class == '' or $form_class == null)
    $form_class_tmp = 'class="app-form-regular"';
  else
    $form_class_tmp = "class=\"$form_class\"";

  echo "<INPUT type=\"submit\" name=\"$form_name\" value=\"$form_value\" $form_class_tmp $form_extra>";
}

function app_form_button_add($form_name, $form_value, $form_class, $form_extra)
{
  if($form_class == '' or $form_class == null)
    $form_class_tmp = 'class="app-form-regular"';
  else
    $form_class_tmp = "class=\"$form_class\"";

  echo "<INPUT type=\"submit\" name=\"$form_name\" value=\"$form_value\" $form_class_tmp $form_extra>";
}

function app_form_space_add($space)
{
  $strspace = "";

  if(!isset($space) or $space < 1)
    $count = 1;
  else
    $count = $space;

  for($index = 0; $index < $count; $index++)
    $strspace = $strspace."&nbsp;";

  echo $strspace;
}

function app_form_caption_add($caption, $form_class)
{
  if($form_class == '' or $form_class == null)
    $form_class_tmp = 'class="app-form-regular"';
  else
    $form_class_tmp = "class=\"$form_class\"";

  echo "<SPAN $form_class_tmp>$caption</SPAN>";
}

function app_form_caption($form_position, $form_caption)
{
  if($form_position["curr"] != 0)
  {
    echo "</TR>";
    $form_position["curr"] == 0;
  }

  $count = $form_position["count"] * 2;

  echo "<TR><TD class=\"app-form-sub-caption\" colspan=\"$count\">$form_caption</TD></TR>";
}

function app_form_line($form_position)
{
  if($form_position["curr"] != 0)
  {
    echo "</TR>";
    $form_position["curr"] == 0;
  }

  $count = $form_position["count"] * 2;
  
  echo "<TR><TD class=\"app-form-line\" colspan=\"$count\"></TD></TR>";
}

//---
//Auto form methods
//---

function app_form(
  $form_caption,
  $form_columns,
  $form_action,
  $form_params,
  $form_caption_width,
  $form_id,
  $form_elements,
  $form_update = True,
  $form_file_data = False)
{
  global $app_limits, $app_form_style, $app_form_captionwidth, $app_form_len, $app_form_rowsize, $app_form_colsize;

  $strparams = "";
  if($form_params != null)
  {
    $first = True;
    foreach($form_params as $key => $value)
    {
      if($first)
      {
        $strparams = $strparams."$key=$value";
        $first = False;
      }
      else
        $strparams = $strparams."&$key=$value";
    }
  }

  if($form_update)
    $form_position = app_form_start($form_caption, $form_columns, $form_action, $strparams, $app_form_captionwidth[$form_caption_width], $form_id, $form_file_data);
  else
    $form_position = app_form_noupdate_start($form_caption, $form_columns, $form_action, $strparams, $app_form_captionwidth[$form_caption_width], $form_id, $form_file_data);

  $formend = true;

  foreach($form_elements as $form_element)
  {
    if(isset($form_element['class']))
      $class = "app-form-".$form_element['class'];
    else
      $class = "";
    if(isset($form_element['style']))
      $extra = $app_form_style[$form_element['style']];
    else
      $extra = "";
    if(isset($form_element['captionclass']))
      $captionclass = "app-form-".$form_element['captionclass'];
    else
      $captionclass = "";
    if(isset($form_element['captionstyle']))
      $captionextra = $app_form_style[$form_element['captionstyle']];
    else
      $captionextra = "";
    if(isset($form_element['onchange']))
      $onchange = $form_element['onchange'];
    else
      $onchange = "";
    if($form_element['type'] == "select")
    {
      $disabled = False;
      if(isset($form_element['disabled']))
        $disabled = $form_element['disabled'];

      app_form_select(
        $form_position,
        $form_element['caption'],
        "form-".$form_element['name'],
        $form_element['size'],
        $form_element['options'],
        $form_element['optioncaptions'],
        $form_element['value'],
        $class,
        $extra,
        $onchange,
        $disabled
      );
    }
    else if($form_element['type'] == "text")
    {
      $readonly = False;
      if(isset($form_element['readonly']))
        $readonly = $form_element['readonly'];

      if(!$readonly)
      {
        app_form_text(
          $form_position,
          $form_element['caption'],
          "form-".$form_element['name'],
          $form_element['value'],
          $app_form_len[$form_element['size']],
          $app_limits[$form_element['maxlength']],
          $class,
          $extra,
          $onchange
        );
      }
      else
      {
        app_form_text_readonly(
          $form_position,
          $form_element['caption'],
          "form-".$form_element['name'],
          $form_element['value'],
          $app_form_len[$form_element['size']],
          $app_limits[$form_element['maxlength']],
          $class,
          $extra
        );
      }
    }
    else if($form_element['type'] == "textarea")
    {
      $readonly = False;
      if(isset($form_element['readonly']))
        $readonly = $form_element['readonly'];
      
      if(!$readonly)
      {
        app_form_textarea(
          $form_position,
          $form_element['caption'],
          "form-".$form_element['name'],
          $form_element['value'],
          $app_form_rowsize[$form_element['rows']],
          $app_form_colsize[$form_element['columns']],
          $app_limits[$form_element['maxlength']],
          $class,
          $extra
        );
      }
      else
      {
        app_form_textarea_readonly(
          $form_position,
          $form_element['caption'],
          "form-".$form_element['name'],
          $form_element['value'],
          $app_form_rowsize[$form_element['rows']],
          $app_form_colsize[$form_element['columns']],
          $app_limits[$form_element['maxlength']],
          $class,
         $extra
        );
      }
    }
    else if($form_element['type'] == "submit")
    {
      app_form_submit(
        $form_position,
        $form_element['caption'],
        "form-".$form_element['name'],
        $form_element['value'],
        $class,
        $extra
      );
    }
    else if($form_element['type'] == "check")
    {
      $disabled = False;
      if(isset($form_element['disabled']))
        $disabled = $form_element['disabled'];

      app_form_check(
        $form_position,
        $form_element['caption'],
        "form-".$form_element['name'],
        $form_element['value'],
        $form_element['text'],
        $form_element['check'],
        $class,
        $extra,
        $disabled
      );
    }
    else if($form_element['type'] == "row")
    {
      app_form_row(
        $form_position
      );
    }
    else if($form_element['type'] == "cell_start")
    {
      app_form_cell_start(
        $form_position,
        $form_element['caption'],
        $class,
        $extra,
        $captionclass,
        $captionextra
      );
    }
    else if($form_element['type'] == "cell_end")
    {
      app_form_cell_end(
        $form_position
      );
    }
    else if($form_element['type'] == "row_end")
    {
      app_form_row_end(
        $form_position
      );
    }
    else if($form_element['type'] == "submit_add")
    {
      app_form_submit_add(
        "form-".$form_element['name'],
        $form_element['value'],
        $class,
        $extra
      );
    }
    else if($form_element['type'] == "button_add")
    {
      app_form_button_add(
        "form-".$form_element['name'],
        $form_element['value'],
        $class,
        $extra
      );
    }
    else if($form_element['type'] == "text_add")
    {
      app_form_text_add(
        "form-".$form_element['name'],
        $form_element['value'],
        $app_form_len[$form_element['size']],
        $app_limits[$form_element['maxlength']],
        $class,
        $extra
      );
    }
    else if($form_element['type'] == "select_add")
    {
      app_form_select_add(
        "form-".$form_element['name'],
        $form_element['size'],
        $form_element['options'],
        $form_element['optioncaptions'],
        $form_element['value'],
        $class,
        $extra,
        $onchange
      );
    }
    else if($form_element['type'] == "space_add")
    {
      app_form_space_add(
        $form_element['space']
      );
    }
    else if($form_element['type'] == "caption_add")
    {
      app_form_caption_add(
        $form_element['caption'],
        $class
      );
    }
    else if($form_element['type'] == "form_start")
    {
      $strparams = "";
      if(isset($form_element['params']))
      {
        $first = True;
        foreach($form_element['params'] as $key => $value)
        {
          if($first)
          {
            $strparams = $strparams."$key=$value";
            $first = False;
          }
          else
            $strparams = $strparams."&$key=$value";
         }
      }
      app_form_end($form_position);
      $formend = false;
      if(isset($form_element['update']) and $form_element['update'])
        app_form_notable_start($form_element['action'], $strparams, $form_element['name']);
      else
        app_form_notable_noupdate_start($form_element['action'], $strparams, $form_element['name']);
    }
    else if($form_element['type'] == "form_end")
    {
      app_form_notable_end($form_position);
    }
    else if($form_element['type'] == "line")
    {
      app_form_line($form_position);
    }
    else if($form_element['type'] == "caption")
    {
      app_form_caption(
        $form_position,
        $form_element['caption']
      );
    }
    else if($form_element['type'] == "password")
    {
      app_form_password(
        $form_position,
        $form_element['caption'],
        "form-".$form_element['name'],
        $form_element['value'],
        $app_form_len[$form_element['size']],
        $app_limits[$form_element['maxlength']],
        $class,
        $extra
      );
    }
    else if($form_element['type'] == "file")
    {
      app_form_file(
        $form_position,
        $form_element['caption'],
        "form-".$form_element['name'],
        $form_element['value'],
        $app_form_len[$form_element['size']],
        $class,
        $extra
      );
    }
  }

  if($formend)
    app_form_end($form_position);
}

//---
//Special form methods
//---

function app_form_ex_start($form_caption, $form_action, $form_params)
{
  global $session_id;

  if($form_params == '' or $form_params == null)
    $form_params_tmp = '';
  else
    $form_params_tmp = "&$form_params";

  echo "<DIV class=\"app-form-ex\">";

  if($form_caption == '' or $form_caption == null) { } else 
  {
    echo "<P class=\"app-form-ex-caption\">$form_caption</P>";
  }

  echo "<FORM class=\"app-form-ex\" action=\"$form_action?update=true&id=$session_id$form_params_tmp\" method=\"post\">";
  echo "<TABLE class=\"app-form-ex\">";
}

function app_form_ex_row_start($form_class)
{
  if($form_class == '' or $form_class == null)
    $form_class_tmp = 'class="app-form-ex-regular"';
  else
    $form_class_tmp = "class=\"$form_class\"";

  echo "<TR $form_class_tmp>";
}

function app_form_ex_cell_start($form_class)
{
  if($form_class == '' or $form_class == null)
    $form_class_tmp = 'class="app-form-ex-regular"';
  else
    $form_class_tmp = "class=\"$form_class\"";

  echo "<TD $form_class_tmp>";
}

function app_form_ex_cell_end()
{
  echo "</TD>";
}

function app_form_ex_row_end()
{
  echo "</TR>";
}

function app_form_ex_end()
{
  echo '</TABLE></FORM></DIV>';
}

function app_form_table_start($form_caption, $form_caption_width)
{
  global $session_id;

  echo "<DIV class=\"app-form\">";

  if($form_caption == '' or $form_caption == null) { } else
  {
    echo "<P class=\"app-form-caption\">$form_caption</P>";
  }

  echo "<TABLE class=\"app-form\">";	

  if($form_caption_width == '' or $form_caption_width == null)
  {
    echo "<COLGROUP>";
    echo "<COL style=\"width:auto\" />";
    echo "<COL style=\"width:auto\" />";
    echo "</COLGROUP>";
  }
  else
  {
    echo "<COLGROUP>";
    echo "<COL style=\"width:$form_caption_width\" />";
    echo "<COL style=\"width:auto\" />";
    echo "</COLGROUP>";
  }
}

function app_form_notable_start($form_action, $form_params, $form_id)
{
  global $session_id;

  if($form_params == '' or $form_params == null)
    $form_params_tmp = '';
  else
    $form_params_tmp = "&$form_params";

  if($form_id == '' or $form_id == null)
    $form_id_temp = '';
  else
    $form_id_temp = "name=\"$form_id\"";

  echo "<FORM class=\"app-form-inline\" action=\"$form_action?update=true&id=$session_id$form_params_tmp\" method=\"post\" $form_id_temp>";
}

function app_form_notable_noupdate_start($form_action, $form_params, $form_id)
{
  global $session_id;

  if($form_params == '' or $form_params == null)
    $form_params_tmp = '';
  else
    $form_params_tmp = "&$form_params";

  if($form_id == '' or $form_id == null)
    $form_id_temp = '';
  else
    $form_id_temp = "name=\"$form_id\"";

  echo "<FORM class=\"app-form-inline\" action=\"$form_action?id=$session_id$form_params_tmp\" method=\"post\" $form_id_temp>";
}

function app_form_notable_end()
{
  echo '</FORM>';
}

function app_form_table_end()
{
  echo '</TABLE></DIV>';
}

//---
//App link methods
//---	

function app_link_app($link_caption, $link_dest, $link_params = null)
{
  global $session_id;

  $strparams = "";
  if(isset($link_params))
  {
    foreach($link_params as $key => $value)
      $strparams = $strparams."&$key=$value";
  }

  echo "<DIV class=\"app-link-app\"><A href=\"$link_dest?id=$session_id$strparams\"><P>$link_caption</P></A></DIV>";
}

//---
//Section controls
//---

function app_section_start($caption)
{
  if($caption == null or $caption == '')
    echo "<DIV class=\"app-section\"><P class=\"caption\">&nbsp;</P>";
    //echo "<DIV class=\"app-section\">";
  else
    echo "<DIV class=\"app-section\"><P class=\"caption\">$caption</P>";
}

function app_section_end()
{
  echo "</DIV>";
}

//---
//Table controls
//---

function app_table_simple_start($columns, $colwidths)
{
  echo "<DIV class=\"app-tablesimple\">";
  echo "<TABLE class=\"app-tablesimple\">";

  echo "<COLGROUP>";

  foreach($colwidths as $colwidth)
  {
    echo "<COL style=\"width:$colwidth\" />";
  }

  echo "</COLGROUP>";

  echo "<TR>";

  foreach($columns as $column)
  {
    echo "<TH>$column</TH>";
  }

  echo "</TR>";	
}

function app_table_start($columns, $colwidths, $ordercolumn, $rowcount, $pagecount, $page, $linkextra, $height)
{
  global $session_id;
  global $app_curr;
  global $app_form_table;

  if($height == null or $height == '')
    $tmp_height = "style=\"height:auto\"";
  else
    $tmp_height = "style=\"height:$height\"";
  
  $strparams = "";
  if($linkextra != null)
  {
    foreach($linkextra as $key => $value)
      $strparams = $strparams."&$key=$value";
  }

  echo "<DIV class=\"app-table-header\">";
  echo "<TABLE class=\"app-table-header\">";
  echo "<COLGROUP>";
  echo "<COL style=\"width:$app_form_table[cellwidth_rowcount]\" />";
  echo "<COL style=\"width:$app_form_table[cellwidth_space]\" />";	
  echo "<COL style=\"width:$app_form_table[cellwidth_page]\" span=\"9\" />";
  echo "</COLGROUP>";
  echo "<TR>";
  echo "<TD class=\"app-table-rowcount\">$rowcount</TD>";
  echo "<TD class=\"app-table-empty\"></TD>";

  $link = $app_curr;

  if($ordercolumn == "" or $ordercolumn == null)
    $strorder = "";
  else
    $strorder = "&order=$ordercolumn";

  if($pagecount < 10)
  {
    for($index = 1; $index <= $pagecount; $index++)
    {
      if($index == $page)
        echo "<TD class=\"app-table-selpage\">$index</TD>";
      else
      {
        echo "<TD class=\"app-table-page\" onclick=\"DoNav('$link?id=$session_id&page=$index$strorder$strparams');\">$index</TD>";
      }
    }
  }
  else
  {
    if($page <= 5)
      $mode = 1;
    elseif($page >= $pagecount - 4)
      $mode = 3;
    else
      $mode = 2;

    if($mode == 1)
    {
      for($index = 1; $index < 8; $index++)
      {
        if($index == $page)
          echo "<TD class=\"app-table-selpage\">$index</TD>";
        else
        {
          echo "<TD class=\"app-table-page\" onclick=\"DoNav('$link?id=$session_id&page=$index$strorder$strparams');\">$index</TD>";
        }
      }
      echo "<TD class=\"app-table-page\">...</TD>";
      echo "<TD class=\"app-table-page\" onclick=\"DoNav('$link?id=$session_id&page=$pagecount$strorder$strparams');\">$pagecount</TD>";
    }
    else if($mode == 2)
    {
      echo "<TD class=\"app-table-page\" onclick=\"DoNav('$link?id=$session_id&page=1$strorder$strparams');\">1</TD>";
      echo "<TD class=\"app-table-page\">...</TD>";
      $number = $page-2;
      echo "<TD class=\"app-table-page\" onclick=\"DoNav('$link?id=$session_id&page=$number$strorder$strparams');\">$number</TD>";
      $number = $page-1;
      echo "<TD class=\"app-table-page\" onclick=\"DoNav('$link?id=$session_id&page=$number$strorder$strparams');\">$number</TD>";
      $number = $page;
      echo "<TD class=\"app-table-selpage\">$number</TD>";
      $number = $page+1;
      echo "<TD class=\"app-table-page\" onclick=\"DoNav('$link?id=$session_id&page=$number$strorder$strparams');\">$number</TD>";
      $number = $page+2;
      echo "<TD class=\"app-table-page\" onclick=\"DoNav('$link?id=$session_id&page=$number$strorder$strparams');\">$number</TD>";
      echo "<TD class=\"app-table-page\">...</TD>";
      echo "<TD class=\"app-table-page\" onclick=\"DoNav('$link?id=$session_id&page=$pagecount$strorder$strparams');\">$pagecount</TD>";
    }
    else
    {
      echo "<TD class=\"app-table-page\" onclick=\"DoNav('$link?id=$session_id&page=1$strorder$strparams');\">1</TD>";
      echo "<TD class=\"app-table-page\">...</TD>";
      for($index = $pagecount - 6; $index <= $pagecount; $index++)
      {
        if($index == $page)
          echo "<TD class=\"app-table-selpage\">$index</TD>";
        else
        {
          echo "<TD class=\"app-table-page\" onclick=\"DoNav('$link?id=$session_id&page=$index$strorder$strparams');\">$index</TD>";
        }
      }
    }
  }

  echo "</TR></TABLE></DIV><DIV class=\"app-table\" $tmp_height>";
  echo "<TABLE class=\"app-table\">";

  echo "<COLGROUP>";
  
  foreach($colwidths as $colwidth) 
  {
    echo "<COL style=\"width:$colwidth\" />";
  }

  echo "</COLGROUP>";

  echo "<TR class=\"header\">";

  $currcol = 1;		
  foreach($columns as $column)
  {
    if($currcol == $ordercolumn)
      echo "<TH class=\"app-table-ordercol\" onclick=\"DoNav('$link?id=$session_id&page=$page&order=$currcol$strparams');\">$column</TH>";
    else
      echo "<TH onclick=\"DoNav('$link?id=$session_id&page=$page&order=$currcol$strparams')\">$column</TH>";
    $currcol++;
  }

  echo "</TR>";	
}

function app_table_row($cells, $link, $item, $linkextra, $row_class_1, $row_class_2, $cell_class, $cell_style)
{
  global $session_id;
  global $curr_row;
  global $app_form_style;

  $strparams = "";
  if($linkextra != null)
  {
    foreach($linkextra as $key => $value)
      $strparams = $strparams."&$key=$value";
  }

  if($row_class_1 == null or $row_class_1 == '')
    $class_1 = 'app-row-link-1';
  else
    $class_1 = "app-row-".$row_class_1;

  if($row_class_2 == null or $row_class_2 == '')
    $class_2 = 'app-row-link-2';
  else
    $class_2 = "app-row-".$row_class_2;

  if($link == null or $link == '')
  {
    if($curr_row == 0)
    {
      echo "<TR class=\"$class_1\">";
      $curr_row = 1;
    }
    else
    {
      echo "<TR class=\"$class_2\">";
      $curr_row = 0;
    }
  }
  else
  {
    if($curr_row == 0)
    {
      echo "<TR class=\"$class_1\" onclick=\"DoNav('$link?id=$session_id&item=$item$strparams');\">";
      $curr_row = 1;
    }
    else
    {
      echo "<TR class=\"$class_2\" onclick=\"DoNav('$link?id=$session_id&item=$item$strparams');\">";
      $curr_row = 0;
    }
  }
  
  $index = 0;
  foreach($cells as $cell)
  {
    $extra = "";
    if(isset($cell_class[$index]) and $cell_class[$index] != "")
    {
      $strclass = "app-cell-".$cell_class[$index];
      $extra = $extra." class=\"$strclass\"";
    }
    if(isset($cell_style[$index]) and $cell_style[$index] != "")
    {
      $strstyle = $app_form_style[$cell_style[$index]];
      $extra = $extra." ".$strstyle;
    }
    echo "<TD$extra>$cell</TD>";
    $index++;
  }

  echo "</TR>";
}

function app_table_row_ex($cells, $link, $cell_links, $item, $linkextra, $row_class_1, $row_class_2, $cell_class, $cell_style)
{
  global $session_id;
  global $curr_row;
  global $app_form_style;

  $strparams = "";
  if($linkextra != null)
  {
    foreach($linkextra as $key => $value)
      $strparams = $strparams."&$key=$value";
  }

  if($row_class_1 == null or $row_class_1 == '')
    $class_1 = 'app-row-link-1';
  else
    $class_1 = "app-row-".$row_class_1;

  if($row_class_2 == null or $row_class_2 == '')
    $class_2 = 'app-row-link-2';
  else
    $class_2 = "app-row-".$row_class_2;

  if($link == null or $link == '')
  {
    if($curr_row == 0)
    {
      echo "<TR class=\"$class_1\">";
      $curr_row = 1;
    }
    else
    {
      echo "<TR class=\"$class_2\">";
      $curr_row = 0;
    }
  }
  else
  {
    if($curr_row == 0)
    {
      echo "<TR class=\"$class_1\" onclick=\"DoNav('$link?id=$session_id&item=$item$strparams');\">";
      $curr_row = 1;
    }
    else
    {
      echo "<TR class=\"$class_2\" onclick=\"DoNav('$link?id=$session_id&item=$item$strparams');\">";
      $curr_row = 0;
    }
  }
  
  $index = 0;
  foreach($cells as $cell)
  {
    $extra = "";
    if(isset($cell_class[$index]) and $cell_class[$index] != "")
    {
      $strclass = "app-cell-".$cell_class[$index];
      $extra = $extra." class=\"$strclass\"";
    }
    if(isset($cell_style[$index]) and $cell_style[$index] != "")
    {
      $strstyle = $app_form_style[$cell_style[$index]];
      $extra = $extra." ".$strstyle;
    }
    if(isset($cell_links[$index]) and $cell_links[$index] != "" and $cell_links[$index] != null)
    {
      echo "<TD$extra onclick=\"DoNav('{$cell_links[$index]}');\">$cell</TD>";
    }
    else
    {
      echo "<TD$extra>$cell</TD>";
    }
    $index++;
  }

  echo "</TR>";
}

function app_table_end()
{
  echo "</TABLE></DIV>";
}

//---
//Info table methods
//---

function app_infotable_start($info_caption, $info_caption_width)
{
  echo "<DIV class=\"app-infotable\">";

  if($info_caption == '' or $info_caption == null) { } else
  {
    echo "<P class=\"app-infotable-caption\">$info_caption</P>";
  }

  echo "<TABLE class=\"app-infotable\">";	

  if($info_caption_width == '' or $info_caption_width == null)
  {
    echo "<COLGROUP>";
    echo "<COL width=\"*\" />";
    echo "<COL width=\"*\" />";
    echo "</COLGROUP>";
  }
  else
  {
    echo "<COLGROUP>";
    echo "<COL width=\"$info_caption_width\" />";
    echo "<COL width=\"*\" />";
    echo "</COLGROUP>";
  }
}

function app_infotable_row($info_caption, $info_value, $info_class, $info_extra = "", $info_captionextra = "")
{
  if($info_class == "" or $info_class == null)
  {
    $info_class_tmp = "class=\"app-infotable-regular\"";
    $info_class_tmp_caption = "class=\"app-infotable-regular-caption\"";
  }
  else
  {
    $info_class_tmp = "class=\"$info_class\"";
    $info_class_tmp_caption = "class=\"".$info_class."-caption\"";
  }

  if($info_caption == 'no_caption')
  {
    echo "<TR><TD $info_class_tmp colspan=\"2\" $info_extra>$info_value</TD></TR>";
  }
  else
  {
    echo "<TR><TD $info_class_tmp_caption $info_captionextra>$info_caption</TD>";
    echo "<TD $info_class_tmp $info_extra>$info_value</TD></TR>";
  }
}

function app_infotable_end()
{
  echo '</TABLE></DIV>';
}

function app_infotable($title, $captionwidth, $elements)
{
  global $app_form_captionwidth, $app_infotable_style;
  
  app_infotable_start($title, $app_form_captionwidth[$captionwidth]);
  foreach($elements as $element)
  {
    if(isset($element['class']))
      $class = "app-infotable-".$element['class'];
    else
      $class = "";
    if(isset($element['style']))
      $extra = $app_infotable_style[$element['style']];
    else
      $extra = "";
    if(isset($element['captionstyle']))
      $captionextra = $app_infotable_style[$element['captionstyle']];
    else
      $captionextra = "";
    app_infotable_row($element['caption'], $element['content'], $class, $extra, $captionextra);
  }
  app_infotable_end();
}

//---
//Database methods and functions
//---

function app_db_connect()
{
  global $db_host, $db_user, $db_password, $db_database;
  global $db_link;

  $db_link = @mysqli_connect($db_host, $db_user, $db_password, $db_database);
  if($db_link)
    mysqli_set_charset($db_link, "utf8");

  return $db_link;
}

function app_db_query($query_str)
{
  global $db_link, $str;
  global $app_debug;

  $res = mysqli_query($db_link, $query_str);
  if($res == null)
  {
    app_error_print($str['error_db_query']);
    //echo($query_str); //uncomment for debug
    if($app_debug)
      app_debug_print("AppDBQuery", $query_str);
    app_end();
  }

  return $res;
}

function app_db_query_no_error_print($query_str)
{
  global $db_link;

  $res = mysqli_query($db_link, $query_str);
  //if(!$res) echo($query_str); //uncomment for debug
  return $res;
}

function app_db_fetch($db_query_res)
{
  return mysqli_fetch_assoc($db_query_res);
}

function app_db_numrows($db_query_res)
{
  return mysqli_num_rows($db_query_res);
}

function app_db_free($db_query_res)
{
  if(gettype($db_query_res) != 'boolean')
    return mysqli_free_result($db_query_res);
}

function app_db_close()
{
  global $db_link;
 
  if($db_link)
    mysqli_close($db_link);
}

function app_db_clearquotes($string)
{
  if($string == null)
    return null;
  else
    return addslashes($string);
    //return str_replace("'" , "''", $string);
}

//---
//Tools
//---

function app_check_number($string, $max_units, $max_dec, $non_neg)
{
  $len = strlen($string);
  $units = 0;
  $decs = 0;
  $mode = 0;
  $valid = true;

  for($i = 0; $i < $len; $i++)
  {
    $asc = ord($string[$i]);
    if($mode == 0)
    {
      if($asc > 47 and $asc < 58)
      {
        $mode = 1;
        $units++;
      }
      else if($asc == 43 or $asc == 45)
      {
        $mode = 1;
        if($non_neg and $asc == 45)
        {
          $valid = false;
          break;
        }
      }
      else if($asc == 46)
      {
        $mode = 2;
        if($max_dec == 0)
        {
          $valid = false;
          break;
        }
      }
      else
      {
        $valid = false;
        break;
      }
    }
    else if($mode == 1)
    {
      if($asc > 47 and $asc < 58)
        $units++;
      else if($asc == 46)
      {
        $mode = 2;
        if($max_dec == 0)
        {
          $valid = false;
          break;
        }
      }
      else
      {
        $valid = false;
        break;
      }
    }
    else if($mode == 2)
    {
      if($asc > 47 and $asc < 58)
        $decs++;
      else
      {
        $valid = false;
        break;
      }
    }
    else
    {
      $valid = false;
      break;
    }
  }

  if($units + $decs == 0)
    $valid = false;

  if($units > $max_units or $decs > $max_dec)
    $valid = false;

  return $valid;
}

function app_check_date($date_1, $date_2, $min_diff, $max_diff)
{
  global $app_regex;
  global $date_1_check, $date_2_check;

  $date_1_check = $date_1;
  $date_2_check = $date_2;
  if($date_1 === null or $date_1 == "")
    return FALSE;
  if($date_2 === null or $date_2 == "")
    $date_2_check = $date_1;

  $query = "
    SELECT
      timestampdiff(
        DAY,
        str_to_date(
          '$date_1_check',
          '{$app_regex['sb_dateformat']}'),
          str_to_date('$date_2_check',
          '{$app_regex['sb_dateformat']}')) AS Diff
    ";
  $db_query_res = app_db_query($query);
  if(app_db_numrows($db_query_res) > 0)
  {
    $db_query_row = app_db_fetch($db_query_res);
    if($db_query_row['Diff'] === null or $db_query_row['Diff'] == "")
    {
      app_db_free($db_query_res);
      return FALSE;
    }
  }
  else
  {
    app_db_free($db_query_res);
    return FALSE;
  }

  if($min_diff !== null and $min_diff !== "")
  {
    if($db_query_row['Diff'] < $min_diff)
    {
      app_db_free($db_query_res);
      return FALSE;
    }
  }
  
  if($max_diff !== null and $max_diff !== "")
  {
    if($db_query_row['Diff'] > $max_diff)
    {
      app_db_free($db_query_res);
      return FALSE;
    }
  }

  app_db_free($db_query_res);
  
  return TRUE;
}

function app_check_datetime($datetime)
{
  global $app_regex;

  $query = "SELECT str_to_date('$datetime', '{$app_regex['sb_datetimeformat']}') AS DateTime";
  $db_query_res = app_db_query($query);
  if(app_db_numrows($db_query_res) == 0)
  {
    app_db_free($db_query_res);
    return FALSE;
  }
  app_db_free($db_query_res);
  return TRUE;
}

function app_get_curr_date()
{
  global $app_regex;

  $query = "SELECT date_format(NOW(), '{$app_regex['sb_dateformat']}') AS CurrDate";
  $db_query_res = app_db_query($query);
  $db_query_row = app_db_fetch($db_query_res);
  $result = $db_query_row['CurrDate'];
  app_db_free($db_query_res);
  return $result;
}

function app_get_curr_datetime()
{
  global $app_regex;

  $query = "SELECT date_format(NOW(), '{$app_regex['sb_datetimeformat']}') AS CurrDateTime";
  $db_query_res = app_db_query($query);
  $db_query_row = app_db_fetch($db_query_res);
  $result = $db_query_row['CurrDateTime'];
  app_db_free($db_query_res);
  return $result;
}

function app_get_session()
{
  global $session_id;

  if(isset($_GET['id']))
    $session_id = $_GET['id'];
  else
    $session_id = null;

  return $session_id;
}

//---
//Initialization and session managment
//---

function app_session_init()
{
  global $res, $status;
  global $app_id, $session_id, $session_data, $user_ip, $session_apps;
  global $app_loginmaxdelta;

  $query = "
    SELECT
      A.user AS User,
      A.host AS Host,
      A.status AS Status,
      A.lastact AS LastAct,
      B.profile AS Profile,
      B.name AS Name,
      B.username AS Username,
      B.status AS UserStatus,
      C.status AS ProfileStatus,
      (TO_DAYS(NOW())*86400+TIME_TO_SEC(NOW()))-(TO_DAYS(A.lastact)*86400+TIME_TO_SEC(A.lastact)) AS TimeDelta
    FROM sessions AS A
    LEFT JOIN users AS B
      ON A.user = B.id
    LEFT JOIN profiles AS C
      ON B.profile = C.id
    WHERE A.session_id='$session_id' AND B.status={$status["active"]}";

  $db_query_res = app_db_query_no_error_print($query);
  if(!$db_query_res)
    return $res['err_db'];

  $db_query_res_rowcount = app_db_numrows($db_query_res);

  if($db_query_res_rowcount == 0)
  {
    app_db_free($db_query_res);
    return $res['err_no_session'];
  }
  
  $res_row = app_db_fetch($db_query_res);

  $session_data['username'] = $res_row['Username'];
  $session_data['host'] = $res_row['Host'];
  $session_data['status'] = $res_row['Status'];
  $session_data['realusername'] = $res_row['Name'];
  $session_data['lastact'] = $res_row['LastAct'];
  $session_data['profile'] = $res_row['Profile'];
  $session_data['user'] = $res_row['User'];
  $session_data['userstatus'] = $res_row['UserStatus'];
  $session_data['profilestatus'] = $res_row['ProfileStatus'];
  $session_data['timedelta'] = $res_row['TimeDelta'];

  app_db_free($db_query_res);

  if($session_data['userstatus'] != $status['active'])
    return $res['err_inactive_user'];
    
  if($session_data['profilestatus'] != $status["active"])
    return $res['err_inactive_profile'];

  $query = "
    SELECT
      B.code AS App,
      B.admin AS Admin
    FROM
    (
      SELECT app
      FROM profilesetup
      WHERE profile = {$session_data['profile']}
    ) AS A
    LEFT JOIN apps AS B
      ON A.app = B.id
  ";
    
  $db_query_res = app_db_query_no_error_print($query);
  if(!$db_query_res)
    return $res['err_db'];
  
  while($db_query_row = app_db_fetch($db_query_res))
    array_push($session_apps, $db_query_row['App']);
  
  app_db_free($db_query_res);

  $query = "
    SELECT
      A.id AS ID,
      A.code AS Code,
      A.title AS Title,
      A.location AS Location,
      A.admin AS Admin,
      A.description AS 'Desc'
    FROM apps AS A
    WHERE Code = '$app_id'
  ";

  $db_query_res = app_db_query_no_error_print($query);
  if(!$db_query_res)
    return $res['err_db'];
  
  $db_query_res_rowcount = app_db_numrows($db_query_res);
  if($db_query_res_rowcount == 0)
  {
    app_db_free($db_query_res);
    return $res['err_db'];
  }
  $db_query_row = app_db_fetch($db_query_res);
  $session_data['appid'] = $db_query_row['ID'];
  $session_data['apptitle'] = $db_query_row['Title'];
  $session_data['applocation'] = $db_query_row['Location'];
  if($db_query_row['Admin'] != 0 and $db_query_row['Admin'] != null)
    $session_data['appadmin'] = true;
  $session_data['appdescription'] = $db_query_row['Desc'];
  
  app_db_free($db_query_res);

  if(app_access_check($app_id))
    $result = $res['session_ok'];
  else
    $result = $res['err_no_access'];

  if($session_data['host'] != $user_ip or $session_data['status'] != 'online' or $session_data['timedelta'] > $app_loginmaxdelta)
    $result = $res['err_no_session'];

  if($result == $res['session_ok'])
  {
    $db_query_res = app_db_query_no_error_print("REPLACE INTO sessions VALUES({$session_data['user']},'$session_id', '$user_ip', 'online', NOW())");
    if(!$db_query_res)
      return $res['err_db'];
  }

  return $result;
}

function app_header_prepare()
{
  global $app_service_title, $app_header_logo, $app_header_logo_alt, $app_header_title, $app_nav_array;
  global $app_refresh, $app_redirect, $app_noreload, $app_outofdateredirect;
  global $app_header, $app_header_nav;
  global $session_id, $session_data;
  global $app_timestamp, $app_updatetimestamp;
  global $app_header_logo_width;

  $app_timestamp = time();

  $app_header  = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">";
  $app_header .= "<HTML lang=\"pt\">";

  $app_header .= "<HEAD>";

  $app_header .= "<META HTTP-EQUIV=\"content-type\" content=\"text/html; charset=UTF-8\">";
  $app_header .= "<META HTTP-EQUIV=\"cache-control\" CONTENT=\"no-cache\">";
  $app_header .= "<META HTTP-EQUIV=\"expires\" content=\"0\">";
  $app_header .= "<META HTTP-EQUIV=\"pragma\" content=\"no-cache\">";
  $app_header .= "<META HTTP-EQUIV=\"Content-Script-Type\" CONTENT=\"text/javascript\">";

  if(isset($app_refresh) and $app_refresh != "")
  {
    $app_header .= "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"$app_refresh\">";
  }

  $app_header .= "<TITLE>$app_service_title - $app_header_title</TITLE>";
  $app_header .= "<LINK href=\"style.css\" rel=\"stylesheet\" type=\"text/css\" />";

  $app_header .= "<SCRIPT type=\"text/javascript\">";
  $app_header .= "function DoNav(theUrl) {document.location.href = theUrl;}";

  if(isset($_GET['update']) and $_GET['update'] == 'true' or $app_updatetimestamp)
    $update = true;
  else
    $update = false;

  if(isset($app_noreload) and $app_noreload)
    $reload = false;
  else
    $reload = true;

  $app_header .= "
    function Load () {";

  if(
    isset($_GET['cancelredirect'])
    and $_GET['cancelredirect'] != ""
    and isset($_POST['form-cancel'])
    and $_POST['form-cancel'] != "")
    $app_redirect = $_GET['cancelredirect'];

  if(isset($app_redirect) and $app_redirect != '')
  {
    $app_header .= "
      location.assign(\"$app_redirect?id=$session_id\");
      return;
      ";
  }

  if($update)
  {
    $app_header .= "
      var timestamp = $app_timestamp;
      window.name = timestamp.toString(10);
      ";
  }

  if($reload)
  {
    if($app_outofdateredirect == "")
    {
      $app_header .= "
        if(parseInt(window.name, 10) > $app_timestamp)
        {
          location.reload(true);
          return;
        }
        ";
    }
    else
    {
      $app_header .= "
        if(parseInt(window.name, 10) > $app_timestamp)
        {
          location.assign(\"$app_outofdateredirect?id=$session_id&item=invalid\");
          return;
        }
        ";
    }
  }

  $app_header .= "}";

  $app_header .= "</SCRIPT>";

  $app_header .= "</HEAD>";
  $app_header .= "<BODY lang=\"pt\" onLoad = \"Load()\">";
  $app_header .= "<DIV class=\"app-header\">";
  $app_header .= "<TABLE class=\"app-header\">";

  $app_header .= "<COLGROUP>";
  $app_header .= "<COL width=\"$app_header_logo_width\" />";
  $app_header .= "<COL width=50% />";
  $app_header .= "<COL width=50% />";
  $app_header .= "</COLGROUP>";

  $app_header .= "<TR>";
  $app_header .= "<TD class=\"header-logo\"><IMG src=\"$app_header_logo\" alt=\"$app_header_logo_alt\"/></TD>";
  $app_header .= "<TD class=\"header-title\">$app_header_title</TD>";
  $app_header	.= "<TD class=\"header-user\">{$session_data['realusername']}</TD>";
  $app_header .= "</TR>";
  $app_header .= "</TABLE>";
  $app_header .= "</DIV>";

  $link_count = 0;

  $app_header_navrow = "";

  if($app_nav_array == '' or $app_nav_array == null)
    $app_header_nav = '';
  else
  {
    foreach($app_nav_array as $nav_array_elem)
    {
      if($link_count == 0)
      {
        $app_header_navrow .= "<TD onclick=\"DoNav('";
        $app_header_navrow .= $nav_array_elem[1];
        $app_header_navrow .= "?id=$session_id";
        $app_header_navrow .= $nav_array_elem[2];
        $app_header_navrow .= "');\">";
        $app_header_navrow .= $nav_array_elem[0];
        $app_header_navrow .= '</TD>';
      }
      else
      {
        $app_header_navrow .= "<TD class=\"app-header-nav-border\" onclick=\"DoNav('";
        $app_header_navrow .= $nav_array_elem[1];
        $app_header_navrow .= "?id=$session_id";
        $app_header_navrow .= $nav_array_elem[2];
        $app_header_navrow .= "');\">";
        $app_header_navrow .= $nav_array_elem[0];
        $app_header_navrow .= '</TD>';
      }
      $link_count++;
    }

    $app_header_nav	 = "<DIV class=\"app-header-nav\">";
    $app_header_nav .= "<TABLE class=\"app-header-nav\">";
    $app_header_nav .= "<TR>$app_header_navrow</TR>";
    $app_header_nav .= "</TABLE>";
    $app_header_nav .= "</DIV>";
  }
}

function app_header_echo()
{
  global $app_header, $app_header_nav;

  app_header_prepare();
  echo $app_header;
  echo $app_header_nav;
}

function app_init()
{
  global $app_id, $session_needed, $session_id, $session_data;
  global $app_header, $app_header_nav;
  global $res, $str;

  $db_link_res = app_db_connect();
  if(!$db_link_res)
  {
    app_header_prepare();
    echo $app_header;
    app_error_print($str['error_db_link']);
    return $res['err_db'];
  }

  if($session_needed)
  {
    $app_session_init_res = app_session_init();
    
    if($app_session_init_res == $res['err_no_session'])
    {
      app_header_echo();
      app_warning_print($str['warning_login_needed']);
      app_section_start(null);
      app_link_app($str['login_caption'], 'login.php');
      app_section_end();
      app_end();
    }
    else if($app_session_init_res == $res['err_db'])
    {
      app_header_echo();
      app_error_print($str['error_db_query']);
      return $res['err_db'];
    }
    else if($app_session_init_res == $res['err_no_access'])
    {
      app_header_echo();
      app_warning_print($str['warning_no_access']);
      app_end();
    }
  }

  return $res['ok'];
}

function app_start()
{
  global $app_outofdate, $app_outofdateredirect, $app_curr;
  global $res, $str;

  $app_init_res = app_init();

  if($app_init_res != $res['ok'])
    return $app_init_res;

  if(isset($app_outofdate) and $app_outofdate)
    $app_outofdateredirect = $app_curr;
  else
    $app_outofdateredirect = "";

  app_header_echo();
  
  if(isset($_GET['item']) and $_GET['item'] == "invalid")
  {
    app_info_print($str['outofdate']);
    $app_init_res = $res['outofdate'];
  }
  
  return $app_init_res;
}

function app_access_check($id)
{
  global $session_apps, $session_data, $app_id;

  if($session_data['profile'] == 1)
    return TRUE;
  else if($app_id == 'logout')
    return TRUE;
  else if ($session_data['appadmin'] and $session_data['profile'] != 1)
    return FALSE;
  else
  {
    $res = in_array($id, $session_apps);
    return $res;
  }
}

function app_end()
{
  app_db_close();
  echo '</BODY></HTML>';
  die();
}

?>
