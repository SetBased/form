<?php

namespace SetBased\Abc\Form\Test\Control;

use SetBased\Abc\Form\Control\DivControl;
use SetBased\Abc\Form\Control\FieldSet;
use SetBased\Abc\Form\RawForm;
use SetBased\Abc\Form\Test\AbcTestCase;

class DivControlTest extends AbcTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  public function testPrefixAndPostfix()
  {
    $form     = new RawForm();
    $fieldset = new FieldSet('');
    $form->addFieldSet($fieldset);

    $input = new DivControl('name');
    $input->setPrefix('Hello');
    $input->setPostfix('World');
    $fieldset->addFormControl($input);

    $form->prepare();
    $html = $form->generate();

    $pos = strpos($html, 'Hello<div>');
    self::assertNotEquals(false, $pos);

    $pos = strpos($html, '</div>World');
    self::assertNotEquals(false, $pos);
  }
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test control is hidden.
   */
  public function testIsHidden()
  {
    $control = new DivControl('hidden');

    self::assertSame(false, $control->isHidden());
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

