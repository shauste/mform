# Modul-Input Demo Redaxo 5.x

Für Redaxo 5 steht MForm ab der Version 3.0 zur Verfügung.

```php
<?php
/*
MODUL INPUT DEMO

@copyright Copyright (c) 2013 by Doerr Softwaredevelopment
@author mail[at]joachim-doerr[dot]com Joachim Doerr

@package redaxo4
@version 2.2.0
*/

// instanziieren
$objForm = new MForm();

// html
$objForm->addHtml('<b>HTML Code</b>');

// headline
$objForm->addHeadline('Text-Input und Hidden Elemente');

// text field
$objForm->addTextField(1,array('label'=>'Input','style'=>'width:200px'));

// hidden field
$objForm->addHiddenField(2,'hidden feld string',array('label'=>'Hidden','style'=>'width:200px'));

// readonly field
$objForm->addTextReadOnlyField(3,'readonly feld string',array('label'=>'Readonly','style'=>'width:200px'));

// textarea field
$objForm->addTextAreaField(4,array('label'=>'Textarea','style'=>'width:300px;height:180px'));

// markitup
$objForm->addTextAreaField(5,array('label'=>'Textarea','class'=>"markitup1"));

// textarea readonly field
$objForm->addTextReadOnlyField(6,'string readonly',array('label'=>'Readonly','style'=>'width:300px;height:180px'));


// headline
$objForm->addHeadline('Select und Multiselect Elemente');

// select
$objForm->addSelectField(7,array(1=>'test-1',2=>'test-2'),array('label'=>'Select'));

// select mit ausgelagerten Options, Size und Label
$objForm->addSelectField(8);
$objForm->addOptions(array(1=>'test-1',2=>'test-2'));
$objForm->setSize(5);
$objForm->setLabel('Select');

// multiselect
$objForm->addMultiSelectField(9,array(1=>'test-1',2=>'test-2'),array('label'=>'Multiselect','size'=>'8'));

// multiselect
$objForm->addMultiSelectField(10,array(1=>'test-1',2=>'test-2',3=>'test-3',4=>'test-4'),array('label'=>'Multiselect'), 'full');


// headline
$objForm->addHeadline('Radio und Checkbox Elemente');

// checkbox
$objForm->addCheckboxField(11,array(1=>'test-1'),array('label'=>'Checkbox'));

// radiobox
$objForm->addRadioField(12,array(1=>'test-1',2=>'test-2'),array('label'=>'Radio Buttons'));


// headline
$objForm->addHeadline('System-Button Elemente');

// media button
$objForm->addMediaField(1,array('types'=>'gif,jpg','preview'=>1,'category'=>4,'label'=>'Bild'));

// medialist button
$objForm->addMedialistField(1,array('types'=>'gif,jpg','preview'=>1,'category'=>4,'label'=>'Bildliste'));

// link button
$objForm->addLinkField(1,array('label'=>'Link','category'=>3));

// linklist button
$objForm->addLinklistField(1,array('label'=>'Linkliste','category'=>3));


// headline
$objForm->addHeadline('Text Elemente');

// description
$objForm->addDescription('Beschreibungstext auch Mehrzeilig');

// HTML
$objForm->addHtml('<b>HTML <i>Text</i></b>');


// get formular
echo $objForm->show();

?>
```