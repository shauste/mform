<?php
/*
site.form.inc.php

@copyright Copyright (c) 2012 by Doerr Softwaredevelopment
@author mail[at]joachim-doerr[dot]com Joachim Doerr

@package redaxo5
@version 3.0
*/

// set headline
$strContent .= '<h2 class="rex-hl2">' . $this->i18n('config') . '</h2>';

// rex request
$config = rex_post('config', array(
  array('mform_template', 'string'),
  array('submit', 'boolean')
));

// if submit set config
if ($config['submit'])
{
  $this->setConfig('mform_template', $config['mform_template']);
  $strContent .= rex_view::info($this->i18n('config_saved'));
}

// read dir
$handle = opendir($this->getBasePath('templates'));
while ($strDir = readdir($handle))
{
  if ($strDir == '.' or $strDir == '..')
  {
  	continue;
  }
  if (is_dir($this->getBasePath('templates/' . $strDir)))
  {
    $arrDirName = explode('_', $strDir);
    $arrThemes[] = array(
      'theme_name'      => $arrDirName[0],
      'theme_path_name' => ucwords(str_replace('_', ' ', $strDir))
    );
  }
}
closedir($handle);

// open form
$strContent .= '
  <form action="' . rex_url::currentBackendPage() . '" method="post">
    <fieldset>
';

// set arrays
$arrFormElements = array();
$arrElements = array();
$arrElements['label'] = '
  <label for="rex-mform-config-template">' . $this->i18n('config_label_template') . '</label>
';

// set select object
$objSelect = new rex_select;
$objSelect->setId('rex-mform-config-template');
$objSelect->setSize(1);
$objSelect->setName('config[mform_template]');
foreach ($arrThemes as $arrTheme)
{
  $objSelect->addOption($arrTheme['theme_path_name'],$arrTheme['theme_name']);
}
$objSelect->setSelected($this->getConfig('mform_template'));
$arrElements['field'] = $objSelect->get();
$arrFormElements[] = $arrElements;

$arrElements = array();
$arrElements['field'] = '
  <input type="submit" name="config[submit]" value="' . $this->i18n('config_save') . '" ' . rex::getAccesskey($this->i18n('config_save'), 'save') . ' />
';
$arrFormElements[] = $arrElements;

// parse form content by fragment
$objFragment = new rex_fragment();
$objFragment->setVar('elements', $arrFormElements, false);
$strContent .= $objFragment->parse('form.tpl');

// close form
$strContent .= '
    </fieldset>
  </form>
';