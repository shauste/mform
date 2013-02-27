<?php
/*
class.a967_parse_mform.inc.php

@copyright Copyright (C) 2013 by Doerr Softwaredevelopment
@author mail[at]joachim-doerr[dot]com Joachim Doerr

@package redaxo5
@version 3.1
*/

// mform parser class 
class parsemform
{
  /**/
  // define defaults
  /**/
  
  public $strOutput;
  public $boolFieldset = false;
  public $strTemplateThemeName;
  
  /**/
  // generate fields
  /**/
  
  /*
  fieldset
  */
  public function generateFieldset($arrElement)
  {
    $arrElement['attributes'] = $this->getAttributes($arrElement['attributes']);
    if ($this->boolFieldset === true)
    {
      $arrElement['close_fieldset'] = '</fieldset>';
    }
    else
    {
      $this->boolFieldset = true;
    }
    
    $strElement = <<<EOT
      
      <mform:element>{$arrElement['close_fieldset']}<fieldset {$arrElement['attributes']}><legend>{$arrElement['default']}</legend></mform:element>
      
EOT;
    return $this->parseElementToTemplate($strElement,$strTemplate);
  }
  
  /*
  html, headline, description
  */
  public function generateLineElement($arrElement)
  {
    switch ($arrElement['type'])
    {
      case 'headline':
      case 'description':
      default:
        $strTemplate = $arrElement['type'];
        break;
    }
    $strElement = <<<EOT
      
      <mform:element>{$arrElement['default']}</mform:element>
      
EOT;
    return $this->parseElementToTemplate($strElement,$strTemplate);
  }
  
  /*
  callback
  */
  public function getCallbackElement($arrElement)
  {
    $strCallElement = call_user_func($arrElement['callabel'], $arrElement['parameter']);

    $strElement = <<<EOT
      
      <mform:element>$strCallElement</mform:element>
      
EOT;
    return $this->parseElementToTemplate($strElement,'html');
  }
  
  /*
  hidden, text, password
  */
  public function generateInputElement($arrElement)
  {
    $arrElement['attributes'] = $this->getAttributes($arrElement['attributes']);
    switch ($arrElement['type'])
    {
      case 'hidden':
        $strTemplate = 'hidden';
        $arrElement['label'] = '';
        break;
        
      case 'text-readonly':
        $arrElement['type'] = 'text';
        $arrElement['attributes'] .= ' readonly="readonly"';
        
      default:
        $strTemplate = 'default';
        break;
    }
    $strElement = <<<EOT
      
      <mform:label><label for="rv{$arrElement['id']}">{$arrElement['label']}</label></mform:label>
      <mform:element><input id="rv{$arrElement['id']}" type="{$arrElement['type']}" name="REX_INPUT_VALUE[{$arrElement['id']}]" value="{$arrElement['default']}" {$arrElement['attributes']} /></mform:element>
      
EOT;
    return $this->parseElementToTemplate($strElement,$strTemplate);
  }
  
  /*
  textarea, markitup
  */
  public function generateAreaElement($arrElement)
  {
    $arrElement['attributes'] = $this->getAttributes($arrElement['attributes']);
    if ($arrElement['type'] == 'area-readonly')
    {
      $arrElement['attributes'] .= ' readonly="readonly"';
    }
    $strElement = <<<EOT
      
      <mform:label><label for="rv{$arrElement['id']}">{$arrElement['label']}</label></mform:label>
      <mform:element><textarea id="rv{$arrElement['id']}" name="REX_INPUT_VALUE[{$arrElement['id']}]" {$arrElement['attributes']} >{$arrElement['default']}</textarea></mform:element>
      
EOT;
    return $this->parseElementToTemplate($strElement,'default');
  }
  
  /*
  select, multiselect
  */
  public function generateOptionsElement($arrElement)
  {
    $arrElement['attributes'] = $this->getAttributes($arrElement['attributes']);
    $strSelectAttributes = ''; $strMultiselectJavascript = ''; $strMultiselectHidden = ''; $arrHiddenValue = array(); $strOptions = '';
    $strSelectAttributes = (is_numeric($arrElement['size']) === true) ? 'size="' . $arrElement['size'] . '"' : '' ;
    if ($arrElement['size'] == 'full')
    {
      $strSelectAttributes = 'size="' . sizeof($arrElement['options']) . '"';
    }
    if ($arrElement['multi'] === true)
    {
      $strSelectAttributes .= ' multiple="multiple"';
      $strMultiselectJavascript = <<<EOT
        <script type="text/javascript">
          /* <![CDATA[ */
            jQuery(document).ready(function($){
              $("#rv{$arrElement['id']}").change(function() {
                $("#hidden_rv{$arrElement['id']}").val($(this).val());
              });
            });
          /* ]]> */
        </script>
EOT;
      $strMultiselectHidden = <<<EOT
        <input id="hidden_rv{$arrElement['id']}" type="hidden" name="REX_INPUT_VALUE[{$arrElement['id']}]" value="{$arrElement['default']}" />
EOT;
      if ($arrElement['default'] != '')
      {
        $arrHiddenValue = explode(',',$arrElement['default']);
      }
    }
    else
    {
      $arrHiddenValue = array($arrElement['default']);
    }
    if (array_key_exists('options',$arrElement) === true)
    {
      foreach ($arrElement['options'] as $intKey => $strValue) {
        $strOptions .= '<option value="' . $intKey . '" ';
          foreach ($arrHiddenValue as $strHiddenValue) {
            if ($intKey == $strHiddenValue)
            {
              $strOptions .= 'selected="selected" ';
            }
          }
        $strOptions .= '>' . $strValue . '</option>';
      }
    }
    $strElement = <<<EOT
      
      <mform:label><label for="rv{$arrElement['id']}">{$arrElement['label']}</label>$strMultiselectJavascript</mform:label>
      <mform:element><select id="rv{$arrElement['id']}" name="REX_INPUT_VALUE[{$arrElement['id']}]" {$arrElement['attributes']} $strSelectAttributes>$strOptions</select>$strMultiselectHidden</mform:element>
      
EOT;
    return $this->parseElementToTemplate($strElement,'default');
  }
  
  /*
  radio
  */
  public function generateRadioElement($arrElement)
  {
    $intCount = 0; $strOptions = '';
    if (array_key_exists('options',$arrElement) === true)
    {
      foreach ($arrElement['options'] as $intKey => $strValue)
      {
        $intCount++;
        $strRadioAttributes = $this->getAttributes($arrElement['attributes']['radio-attr'][$intKey]);
        $strOptions .= '<div class="radio_element"><input id="rv' . $arrElement['id'] . $intCount . '" type="radio" name="REX_INPUT_VALUE[' . $arrElement['id'] . ']" value="' . $intKey . '" ' . $strRadioAttributes;
        
        if ($intKey == $arrElement['default'])
        {
          $strOptions .= ' checked="checked" ';
        }
        $strOptions .= ' /><span class="radio_description"><label class="description" for="rv' . $arrElement['id'] . $intCount . '">' . $strValue . '</label></span></div>';
      }
    }
    $strElement = <<<EOT
      
      <mform:label><label for="rv{$arrElement['id']}">{$arrElement['label']}</label></mform:label>
      <mform:element>$strOptions</mform:element>
      
EOT;
    return $this->parseElementToTemplate($strElement,'default');
  }
  
  /*
  checkbox
  */
  public function generateCheckboxElement($arrElement)
  {
    if (array_key_exists('options',$arrElement) === true)
    {
      $arrElement['attributes'] = $this->getAttributes($arrElement['attributes']);
      $arrElement['options'] = array(end(array_keys($arrElement['options'])) => end($arrElement['options'])); $strOptions = '';
      foreach ($arrElement['options'] as $intKey => $strValue)
      {
        $strOptions .= '<div class="radio_element"><input id="rv' . $arrElement['id'] . '" type="checkbox" name="REX_INPUT_VALUE[' . $arrElement['id'] . ']" value="' . $intKey . '" ' . $arrElement['attributes'];
        if ($intKey == $arrElement['default'])
        {
          $strOptions .= ' checked="checked" ';
        }
        $strOptions .= ' /><span class="radio_description"><label class="description" for="rv' . $arrElement['id'] . '">' . $strValue . '</label></span></div>';
      }
    }
    $strElement = <<<EOT
      
      <mform:label><label for="rv{$arrElement['id']}">{$arrElement['label']}</label></mform:label>
      <mform:element>$strOptions</mform:element>
      
EOT;
    return $this->parseElementToTemplate($strElement,'default');
  }
  
  /*
  link, linklist
  */
  public function generateLinkElement($arrElement)
  {
    $arrID = explode('-', $arrElement['id']);
    if (sizeof($arrElement['parameter']) >= 0)
    {
      $arrElement['parameter'] = array();
    }
    if ($arrElement['type'] == 'link')
    {
      $strOptions = rex_var_link::getWidget($arrID[1], 'REX_INPUT_LINK[' . $arrID[1] . ']', $arrElement['default'], $arrElement['parameter']);
    }
    if ($arrElement['type'] == 'linklist')
    {
      $strOptions = rex_var_linklist::getWidget($arrID[1], 'REX_INPUT_LINKLIST[' . $arrID[1] . ']', $arrElement['default'], $arrElement['parameter']);
    }
    $strElement = <<<EOT
      
      <mform:label><label>{$arrElement['label']}</label></mform:label>
      <mform:element>$strOptions</mform:element>
      
EOT;
    return $this->parseElementToTemplate($strElement,'default');
  }
  
  /*
  media, medialist
  */
  public function generateMediaElement($arrElement) 
  {
    $arrID = explode('-', $arrElement['id']);
    if (sizeof($arrElement['parameter']) >= 0)
    {
      $arrElement['parameter'] = array();
    }
    if ($arrElement['type'] == 'media')
    {
      $strOptions = rex_var_media::getWidget($arrID[1], 'REX_INPUT_MEDIA[' . $arrID[1] . ']', $arrElement['default'], $arrElement['parameter']);
    }
    if ($arrElement['type'] == 'medialist')
    {
      $strOptions = rex_var_medialist::getWidget($arrID[1], 'REX_INPUT_MEDIALIST[' . $arrID[1] . ']', $arrElement['default'], $arrElement['parameter']);        
    }
    $strElement = <<<EOT
      
      <mform:label><label>{$arrElement['label']}</label></mform:label>
      <mform:element>$strOptions</mform:element>
      
EOT;
    return $this->parseElementToTemplate($strElement,'default');
  }
  
  /**/
  // get attributes
  /**/
  
  public function getAttributes($arrAttributes)
  {
    $strAttributes = NULL;
    if (sizeof($arrAttributes) > 0)
    {
      foreach ($arrAttributes as $strKey => $strValue)
      {
        if (!in_array($strKey, array('id', 'name', 'type', 'value', 'checked', 'selected')))
        {
          $strAttributes .= ' '.$strKey.'="'.$strValue.'"';
        }
      }
    }
    return $strAttributes;
  }
  
  /**/
  // parse form fields by types
  /**/
  
  public function parseFormFields($arrElements)
  {
    if (sizeof($arrElements) > 0)
    {
      foreach ($arrElements as $intKey => $arrElement)
      {
        switch ($arrElement['type'])
        {
          case 'fieldset':
            $this->generateFieldset($arrElement);
            break;
            
          case 'html':
          case 'headline':
          case 'description':
            $this->generateLineElement($arrElement);
            break;
          
          case 'callback':
            $this->getCallbackElement($arrElement);
            break;
                    
          case 'text':
          case 'hidden':
          case 'text-readonly':
            $this->generateInputElement($arrElement);
            break;
                    
          case 'textarea':
          case 'markitup':
          case 'area-readonly':
            $this->generateAreaElement($arrElement);
            break;
          
          case 'select':
          case 'multiselect':
            $this->generateOptionsElement($arrElement);
            break;
          
          case 'radio':
          case 'radiobutton':
            $this->generateRadioElement($arrElement);
            break;
          
          case 'checkbox':
            $this->generateCheckboxElement($arrElement);
            break;
          
          case 'link':
          case 'linklist':
            $this->generateLinkElement($arrElement);
            break;
          
          case 'media':
          case 'medialist':
            $this->generateMediaElement($arrElement);
            break;
        }
      }
    }
  }
  
  /**/
  // set theme
  /**/
  
  public function setTheme($strNewTemplateThemeName)
  {
    $strMformAddonPath = rex_path::addon('mform');
    $strDefaultTemplateThemeName = rex_addon::get('mform')->getConfig('mform_template');
    
    if (is_dir($strMformAddonPath . "/templates/" . $strNewTemplateThemeName . "_theme/") === true && $strNewTemplateThemeName != $strDefaultTemplateThemeName)
    {
      $this->strTemplateThemeName = $strNewTemplateThemeName;
      return 
        PHP_EOL.'   <!-- mform -->'.
        PHP_EOL.'     <link rel="stylesheet" type="text/css" href="?&mform_theme=' . $this->strTemplateThemeName . '" media="all" />'.
        PHP_EOL.'   <!-- mform -->';
    }
  }
  
  /**/
  // parse form to template
  /**/
  
  public function parseElementToTemplate($strElement, $strTemplateKey, $boolParseFinal = false)
  {
    $strMformAddonPath = rex_path::addon('mform');
    $strDefaultTemplateThemeName = rex_addon::get('mform')->getConfig('mform_template');
    
    $strTemplateThemeName = $strDefaultTemplateThemeName;
    if ($this->strTemplateThemeName != '')
    {
      $strTemplateThemeName = $this->strTemplateThemeName;
    }
    
    if ($strTemplateKey != '' && $strTemplateKey != 'html')
    {
      $strTemplate = implode(file($strMformAddonPath . "templates/" . $strTemplateThemeName . "_theme/mform_" . $strTemplateKey . ".ini", FILE_USE_INCLUDE_PATH));
    }
    
    # echo $strTemplate;
    
    preg_match('|<mform:label>(.*?)</mform:label>|ism', $strElement, $arrLabel);
    preg_match('|<mform:element>(.*?)</mform:element>|ism', $strElement, $arrElement);
    
    switch ($strTemplateKey)
    {
      case 'default':
      case 'hidden':
        if ($strTemplate != '')
        {
          $strElement = str_replace(array(' />','<mform:label/>','<mform:element/>'), array('/>',$arrLabel[1],$arrElement[1]), $strTemplate);
        }
        break;
        
      case 'html':
      case 'fieldset':
        $strTemplate = '<mform:output/>';
        
      case 'wrapper':
      default:
        if (isset($arrLabel[1]) === true or isset($arrElement[1]) === true)
        {
          if (sizeof($arrLabel) > 0 && sizeof($arrElement) > 0)
          {
            $strElement = $arrLabel[1].$arrElement[1];
          }
        }
        if ($strTemplate != '')
        {
          $strElement = str_replace(array(' />','<mform:output/>'), array('/>',$strElement), $strTemplate);
        }
        break;
    }
    if ($strElement != '')
    {
      $strElement = str_replace(array('<mform:element>','<mform:element/>','<mform:element />', '</mform:element>', '</ mform:element>'), '', $strElement);
    }
    if ($boolParseFinal === true)
    {
      if ($this->boolParseFinal === true)
      {
        $strElement = $strElement.'</fieldset>';
      }
      $this->strOutput = $strElement;
    }
    else
    {
      $this->strOutput .= $strElement;
    }
  }
  
  /*
  final parseing
  */
  public function parse_mform($arrElements, $strNewTemplateThemeName = false)
  {
    if ($strNewTemplateThemeName != false)
    {
      $this->strOutput .= $this->setTheme($strNewTemplateThemeName);
    }
    $this->parseFormFields($arrElements);
    $this->parseElementToTemplate($this->strOutput,'wrapper',true);
    return $this->strOutput;
  }
}
