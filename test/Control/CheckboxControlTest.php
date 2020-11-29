<?php
declare(strict_types=1);

namespace Plaisio\Form\Test\Control;

use Plaisio\Form\Control\CheckboxControl;
use Plaisio\Form\Control\FieldSet;
use Plaisio\Form\Test\PlaisioTestCase;
use Plaisio\Form\Test\TestForm;

/**
 * Unit tests for class CheckboxControl.
 */
class CheckboxControlTest extends PlaisioTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test values of immutable form control do not change.
   */
  public function testImmutable(): void
  {
    $_POST['immutable2'] = 'on';

    $form     = new TestForm();
    $fieldset = new FieldSet();
    $form->addFieldSet($fieldset);

    $input = new CheckboxControl('immutable1');
    $input->setImmutable(true)
          ->setValue(true);
    $fieldset->addFormControl($input);

    $input = new CheckboxControl('immutable2');
    $input->setImmutable(true)
          ->setValue(false);
    $fieldset->addFormControl($input);

    $form->loadSubmittedValues();
    $values  = $form->getValues();
    $changed = $form->getChangedControls();

    self::assertTrue($values['immutable1']);
    self::assertArrayNotHasKey('immutable1', $changed);

    self::assertFalse($values['immutable2']);
    self::assertArrayNotHasKey('immutable2', $changed);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test control is hidden.
   */
  public function testIsHidden(): void
  {
    $control = new CheckboxControl('hidden');

    self::assertSame(false, $control->isHidden());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test values of mutable form control do change.
   */
  public function testMutable(): void
  {
    $_POST['immutable2'] = 'on';

    $form     = new TestForm();
    $fieldset = new FieldSet();
    $form->addFieldSet($fieldset);

    $input = new CheckboxControl('immutable1');
    $input->setMutable(true)
          ->setValue(true);
    $fieldset->addFormControl($input);

    $input = new CheckboxControl('immutable2');
    $input->setMutable(true)
          ->setValue(false);
    $fieldset->addFormControl($input);

    $form->loadSubmittedValues();
    $values  = $form->getValues();
    $changed = $form->getChangedControls();

    self::assertFalse($values['immutable1']);
    self::assertArrayHasKey('immutable1', $changed);

    self::assertTrue($values['immutable2']);
    self::assertArrayHasKey('immutable2', $changed);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for methods setPrefix() and setPostfix().
   */
  public function testPrefixAndPostfix(): void
  {
    $form     = new TestForm();
    $fieldset = new FieldSet();
    $form->addFieldSet($fieldset);

    $input = new CheckboxControl('name');
    $input->setPrefix('Hello')
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
   * Test submit value.
   * In form unchecked.
   * In POST unchecked.
   */
  public function testSubmittedValue1(): void
  {
    $form     = new TestForm();
    $fieldset = new FieldSet();
    $form->addFieldSet($fieldset);

    $input = new CheckboxControl('test1');
    $fieldset->addFormControl($input);

    $form->loadSubmittedValues();
    $values  = $form->getValues();
    $changed = $form->getChangedControls();

    // Value has not set.
    self::assertFalse($values['test1']);
    // Value has not change.
    self::assertArrayNotHasKey('test1', $changed);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test submit value.
   * In form unchecked.
   * In POST checked
   */
  public function testSubmittedValue2(): void
  {
    $_POST['test2'] = 'on';

    $form     = new TestForm();
    $fieldset = new FieldSet();
    $form->addFieldSet($fieldset);

    $input = new CheckboxControl('test2');
    $fieldset->addFormControl($input);

    $form->loadSubmittedValues();
    $values  = $form->getValues();
    $changed = $form->getChangedControls();

    // Value set from POST.
    self::assertTrue($values['test2']);

    // Assert value has changed.
    self::assertNotEmpty($changed['test2']);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test submit value.
   * In form checked.
   * In POST unchecked.
   */
  public function testSubmittedValue3(): void
  {
    $form     = new TestForm();
    $fieldset = new FieldSet();
    $form->addFieldSet($fieldset);

    $input = new CheckboxControl('test3');
    $input->setValue(true);
    $fieldset->addFormControl($input);

    $form->loadSubmittedValues();
    $values  = $form->getValues();
    $changed = $form->getChangedControls();

    // Value set from POST checkbox unchecked.
    self::assertFalse($values['test3']);

    // Value is change.
    self::assertNotEmpty($changed['test3']);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test submit value.
   * In form unchecked.
   * In POST checked
   */
  public function testSubmittedValue4(): void
  {
    $_POST['test4'] = 'on';

    $form     = new TestForm();
    $fieldset = new FieldSet();
    $form->addFieldSet($fieldset);

    $input = new CheckboxControl('test4');
    $input->setValue(true);
    $fieldset->addFormControl($input);

    $form->loadSubmittedValues();
    $values  = $form->getValues();
    $changed = $form->getChangedControls();

    // Value set from POST.
    self::assertTrue($values['test4']);

    // Value has not changed.
    self::assertArrayNotHasKey('test4', $changed);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test submit value with alternative values.
   *
   * In form unchecked.
   * In POST unchecked.
   */
  public function testSubmittedValue5(): void
  {
    $form     = new TestForm();
    $fieldset = new FieldSet();
    $form->addFieldSet($fieldset);

    $input = new CheckboxControl('test5');
    $fieldset->addFormControl($input);

    $form->loadSubmittedValues();
    $values  = $form->getValues();
    $changed = $form->getChangedControls();

    // Value has not set.
    self::assertSame(false, $values['test5']);
    // Value has not change.
    self::assertArrayNotHasKey('test5', $changed);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test submit value with alternative values.
   *
   * In form unchecked.
   * In POST checked.
   */
  public function testSubmittedValue6(): void
  {
    $_POST['test6'] = 'on';

    $form     = new TestForm();
    $fieldset = new FieldSet();
    $form->addFieldSet($fieldset);

    $input = new CheckboxControl('test6');
    $fieldset->addFormControl($input);

    $form->loadSubmittedValues();
    $values  = $form->getValues();
    $changed = $form->getChangedControls();

    // Value has not set.
    self::assertSame(true, $values['test6']);

    // Value has changed.
    self::assertArrayHasKey('test6', $changed);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
