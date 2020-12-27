<?php
declare(strict_types=1);

namespace Plaisio\Form\Test\Control;

use Plaisio\Form\Control\FieldSet;
use Plaisio\Form\Control\ForceSubmitControl;
use Plaisio\Form\Control\SimpleControl;
use Plaisio\Form\Control\TelControl;
use Plaisio\Form\RawForm;
use Plaisio\Form\Test\Control\Traits\ImmutableTest;

/**
 * Unit tests for class TelControl.
 */
class TelControlTest extends SimpleControlTest
{
  //--------------------------------------------------------------------------------------------------------------------
  use ImmutableTest;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test HTML generated by element.
   */
  public function testHtml(): void
  {
    $input = new TelControl('myInput');

    $fieldSet = new FieldSet('myFieldSet');
    $fieldSet->addFormControl($input);

    $form = new RawForm('myForm');
    $form->addFieldSet($fieldSet);

    $html     = $form->getHtml();
    $expected = '<form method="post" action="/"><fieldset><input type="tel" name="myForm[myFieldSet][myInput]"/></fieldset></form>';
    self::assertSame($expected, $html);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test cleaning is done before testing value of the form control has changed.
   * For text field whitespaceOnly cleaner set default.
   */
  public function testPruneWhitespaceNoChanged(): void
  {
    $_POST['test'] = '  +1   555   123456789  ';

    $form     = new RawForm();
    $fieldset = new FieldSet();
    $form->addFieldSet($fieldset);

    $input = new TelControl('test');
    $input->setValue('+1 555 123456789');
    $fieldset->addFormControl($input);

    $input = new ForceSubmitControl('submit', true);
    $input->setMethod('handleSubmit');
    $fieldset->addFormControl($input);

    $method  = $form->execute();
    $values  = $form->getValues();
    $changed = $form->getChangedControls();

    self::assertSame('handleSubmit', $method);

    // After clean '  +1   555   123456789  ' must be equal '+1 555 123456789'.
    self::assertEquals('+1 555 123456789', $values['test']);

    // Effectively the value is not changed.
    self::assertArrayNotHasKey('test', $changed);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function createControl(string $name): SimpleControl
  {
    return new TelControl($name);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
