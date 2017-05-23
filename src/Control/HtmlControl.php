<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Abc\Form\Control;

use SetBased\Abc\Helper\Html;

//----------------------------------------------------------------------------------------------------------------------
/**
 * A class for pseudo form controls for generating arbitrary HTML code inside forms.
 */
class HtmlControl extends Control
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The HTML code generated by this form control.
   *
   * @var string
   */
  protected $value;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function generate()
  {
    $html = $this->prefix;
    $html .= $this->value;
    $html .= $this->postfix;

    return $html;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the value of this form control.
   *
   * @return mixed
   */
  public function getSubmittedValue()
  {
    return $this->value;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function mergeValuesBase($values)
  {
    if (array_key_exists($this->name, $values))
    {
      $this->setValuesBase($values);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the (inner) HTML code generated by this form control.
   *
   * @param string $htmlSnippet The inner HTML. It is the developer's responsibility to unsure it is valid HTML code.
   */
  public function setHtml($htmlSnippet)
  {
    $this->value = $htmlSnippet;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the (inner) HTML code generated by this form control.
   *
   * @param string $text The text. Special characters will be converted to HTML entities.
   */
  public function setText($text)
  {
    $this->value = Html::txt2Html($text);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function setValuesBase($values)
  {
    $this->value = $values[$this->name] ?? null;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function loadSubmittedValuesBase(&$submittedValue, &$whiteListValue, &$changedInputs)
  {
    $whiteListValue[$this->name] = $this->value;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Return always true.
   *
   * @param array $invalidFormControls Not used.
   *
   * @return bool
   */
  protected function validateBase(&$invalidFormControls)
  {
    return true;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
