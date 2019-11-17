<?php
declare(strict_types=1);

namespace Plaisio\Form\Control;

use Plaisio\Helper\Html;

/**
 * A class for pseudo form controls for generating arbitrary HTML code inside forms.
 */
class HtmlControl extends Control
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The HTML code generated by this form control.
   *
   * @var string|null
   */
  protected $value;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   *
   * @since 1.0.0
   * @api
   */
  public function getHtml(): string
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
   * @return string|null
   *
   * @since 1.0.0
   * @api
   */
  public function getSubmittedValue()
  {
    return $this->value;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  public function mergeValuesBase(array $values): void
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
   * @param string|null $htmlSnippet The inner HTML. It is the developer's responsibility to unsure it is valid HTML
   *                                 code.
   *
   * @since 1.0.0
   * @api
   */
  public function setHtml(?string $htmlSnippet): void
  {
    $this->value = $htmlSnippet;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the (inner) HTML code generated by this form control.
   *
   * @param string|null $text The text. Special characters will be converted to HTML entities.
   *
   * @since 1.0.0
   * @api
   */
  public function setText(?string $text): void
  {
    $this->value = Html::txt2Html($text);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  public function setValuesBase(?array $values): void
  {
    $this->value = $values[$this->name] ?? null;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function loadSubmittedValuesBase(array $submittedValues,
                                             array &$whiteListValues,
                                             array &$changedInputs): void
  {
    $whiteListValues[$this->name] = $this->value;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Return always true.
   *
   * @param array $invalidFormControls Not used.
   *
   * @return bool
   */
  protected function validateBase(array &$invalidFormControls): bool
  {
    return true;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
