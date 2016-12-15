<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Abc\Form\Control;

use SetBased\Abc\Form\Cleaner\PruneWhitespaceCleaner;
use SetBased\Abc\Helper\Html;

//--------------------------------------------------------------------------------------------------------------------
/**
 * Class for form controls of type [input:password](http://www.w3schools.com/tags/tag_input.asp).
 */
class PasswordControl extends SimpleControl
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function __construct($name)
  {
    parent::__construct($name);

    // By default whitespace is trimmed from password form controls.
    $this->cleaner = PruneWhitespaceCleaner::get();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the HTML code for this form control.
   *
   * @return string
   */
  public function generate()
  {
    $this->attributes['type']  = 'password';
    $this->attributes['name']  = $this->submitName;
    $this->attributes['value'] = $this->value;

    $ret = $this->prefix;
    $ret .= $this->generatePrefixLabel();
    $ret .= Html::generateVoidElement('input', $this->attributes);
    $ret .= $this->generatePostfixLabel();
    $ret .= $this->postfix;

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function loadSubmittedValuesBase(&$submittedValue, &$whiteListValue, &$changedInputs)
  {
    $submit_name = ($this->obfuscator) ? $this->obfuscator->encode($this->name) : $this->name;

    // Get the submitted value.
    $new_value = (isset($submittedValue[$submit_name])) ? $submittedValue[$submit_name] : null;

    // Clean the submitted value, if we have a cleaner.
    if ($this->cleaner) $new_value = $this->cleaner->clean($new_value);

    if ((string)$this->value!==(string)$new_value)
    {
      $changedInputs[$this->name] = $this;
      $this->value                = $new_value;
    }

    // The user can enter any text in a input:password box. So, any value is white listed.
    $whiteListValue[$this->name] = $new_value;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
