<?php
declare(strict_types=1);

namespace Plaisio\Form\Test\Control;

use Plaisio\Form\Control\FieldSet;
use Plaisio\Form\Control\ForceSubmitControl;
use Plaisio\Form\Control\SilentControl;
use Plaisio\Form\Control\SubmitControl;
use Plaisio\Form\RawForm;
use Plaisio\Form\Test\PlaisioTestCase;

/**
 * Unit tests for class SilentControl.
 */
class SilentControlTest extends PlaisioTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test a submitted value '0'.
   */
  public function test1Empty1(): void
  {
    $name            = 0;
    $_POST['name']   = $name;
    $_POST['submit'] = 'submit';

    $form    = $this->setupForm1(null);
    $values  = $form->getValues();
    $changed = $form->getChangedControls();

    self::assertEquals($name, $values['name']);
    self::assertEmpty($changed);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test a submitted value '0.0'.
   */
  public function test1Empty2(): void
  {
    $name            = '0.0';
    $_POST['name']   = $name;
    $_POST['submit'] = 'submit';

    $form    = $this->setupForm1('');
    $values  = $form->getValues();
    $changed = $form->getChangedControls();

    self::assertEquals($name, $values['name']);
    self::assertEmpty($changed);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test HTML generated by element.
   */
  public function testHtml(): void
  {
    $input = new SilentControl('myInput');

    $fieldSet = new FieldSet('myFieldSet');
    $fieldSet->addFormControl($input);

    $form = new RawForm('myForm');
    $form->addFieldSet($fieldSet);

    $html     = $form->getHtml();
    $expected = '<form method="post" action="/"><fieldset><input type="hidden" name="myForm[myFieldSet][myInput]"/></fieldset></form>';
    self::assertSame($expected, $html);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test control is hidden.
   */
  public function testIsHidden(): void
  {
    $control = new SilentControl('hidden');

    self::assertTrue($control->isHidden());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test is submit trigger.
   */
  public function testIsSubmitTrigger(): void
  {
    $input = new SilentControl('trigger');

    self::assertFalse($input->isSubmitTrigger());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for methods setPrefix() and setPostfix().
   */
  public function testPrefixAndPostfix(): void
  {
    $form     = new RawForm();
    $fieldset = new FieldSet();
    $form->addFieldSet($fieldset);

    $input = new SilentControl('name');
    $input->setValue('1')
          ->setPrefix('Hello')
          ->setPostfix('World');
    $fieldset->addFormControl($input);

    $html = $form->getHtml();

    $pos = strpos($html, 'Hello<input');
    self::assertNotEquals(false, $pos);

    $pos = strpos($html, '/>World');
    self::assertNotEquals(false, $pos);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test submitted values are echoed after to form has been executed.
   */
  public function testSubmittedValuesAreEchoed(): void
  {
    $_POST['name']   = $this->getValidSubmittedValue();
    $_POST['submit'] = 'submit';

    $form = $this->setupForm1($this->getValidInitialValue());
    $html = $form->getHtml();

    self::assertStringContainsString($this->getValidSubmittedValue(), $html);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test a submitted value.
   */
  public function testValid101(): void
  {
    $name = 'Set Based IT Consultancy';

    $_POST['name']   = $name;
    $_POST['submit'] = 'submit';

    $form    = $this->setupForm1(null);
    $values  = $form->getValues();
    $changed = $form->getChangedControls();

    self::assertEquals($name, $values['name']);
    self::assertEmpty($changed);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test a submitted empty value.
   */
  public function testValid102(): void
  {
    $name            = 'Set Based IT Consultancy';
    $_POST['name']   = '';
    $_POST['submit'] = 'submit';

    $form    = $this->setupForm1($name);
    $values  = $form->getValues();
    $changed = $form->getChangedControls();

    self::assertEmpty($values['name']);
    self::assertEmpty($changed);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test with missing submitted value.
   */
  public function testValid103(): void
  {
    $name                = 'Set Based IT Consultancy';
    $_POST['other_name'] = '';
    $_POST['submit']     = 'submit';

    $form    = $this->setupForm1($name);
    $values  = $form->getValues();
    $changed = $form->getChangedControls();

    self::assertEmpty($values['name']);
    self::assertEmpty($changed);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test change value.
   */
  public function testValue(): void
  {
    $_POST['test'] = 'New value';

    $form     = new RawForm();
    $fieldset = new FieldSet();
    $form->addFieldSet($fieldset);

    $input = new SilentControl('test');
    $input->setValue('Old value');
    $fieldset->addFormControl($input);

    $input = new ForceSubmitControl('submit', true);
    $input->setMethod('handleSubmit');
    $fieldset->addFormControl($input);

    $method  = $form->execute();
    $values  = $form->getValues();
    $changed = $form->getChangedControls();

    self::assertTrue($form->isValid());
    self::assertSame('handleSubmit', $method);
    self::assertEmpty($changed);
    self::assertSame('New value', $values['test']);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a valid initial value.
   *
   * @return mixed
   */
  protected function getValidInitialValue()
  {
    return 'Hello, World!';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a valid submitted value (different form initial value).
   *
   * @return string
   */
  protected function getValidSubmittedValue(): string
  {
    return 'Bye, bye!';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Setups a form with a simple form control and executes the form.
   *
   * @param mixed $value The value of the form control
   *
   * @return RawForm
   */
  private function setupForm1($value): RawForm
  {
    $form     = new RawForm();
    $fieldset = new FieldSet();
    $form->addFieldSet($fieldset);

    $input = new SilentControl('name');
    $input->setValue($value);
    $fieldset->addFormControl($input);

    $input = new SubmitControl('submit');
    $input->setValue('submit')
          ->setMethod('handleSubmit');
    $fieldset->addFormControl($input);

    $form->execute();

    return $form;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
