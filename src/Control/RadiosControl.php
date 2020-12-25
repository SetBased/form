<?php
declare(strict_types=1);

namespace Plaisio\Form\Control;

use Plaisio\Form\Control\Traits\Mutability;
use Plaisio\Form\Walker\LoadWalker;
use Plaisio\Helper\Html;
use Plaisio\Obfuscator\Obfuscator;
use SetBased\Helper\Cast;

/**
 * Class for an array of form controls of type [input:radio](http://www.w3schools.com/tags/tag_input.asp) with the same
 * name.
 */
class RadiosControl extends Control
{
  //--------------------------------------------------------------------------------------------------------------------
  use Mutability;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The map from the keys in the options to attribute names of the input elements.
   *
   * @var array|null
   */
  protected ?array $inputAttributesMap = null;

  /**
   * The key in $options holding the keys for the radio buttons.
   *
   * @var string
   */
  protected string $keyKey;

  /**
   * The map from the keys in the options to attribute names of the label elements.
   *
   * @var array|null
   */
  protected ?array $labelAttributesMap = null;

  /**
   * If true and only if true labels are HTML code.
   *
   * @var bool
   */
  protected bool $labelIsHtml = false;

  /**
   * The key in $options holding the labels for the radio buttons.
   *
   * @var string
   */
  protected string $labelKey;

  /**
   * The HTML snippet appended after each label for the radio buttons.
   *
   * @var string
   */
  protected string $labelPostfix = '';

  /**
   * The HTML snippet inserted before each label for the radio buttons.
   *
   * @var string
   */
  protected string $labelPrefix = '';

  /**
   * The data for the radio buttons.
   *
   * @var array[]|null
   */
  protected ?array $options = null;

  /**
   * The obfuscator for the names of the radio buttons.
   *
   * @var Obfuscator|null
   */
  protected ?Obfuscator $optionsObfuscator = null;

  /**
   * The value of the checked radio button.
   *
   * @var mixed
   */
  protected $value = null;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  public function __construct(?string $name)
  {
    parent::__construct($name);

    $this->attributes['class'] = 'radios';
  }

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
    $html .= Html::generateTag('span', $this->attributes);

    if (is_array($this->options))
    {
      $valueAsString = Cast::toManString($this->value, '');

      foreach ($this->options as $option)
      {
        $inputAttributes = $this->inputAttributes($option);
        $labelAttributes = $this->labelAttributes($option);

        $labelAttributes['for'] = $inputAttributes['id'];

        $key         = $option[$this->keyKey];
        $keyAsString = Cast::toManString($key, '');
        $value       = ($this->optionsObfuscator) ? $this->optionsObfuscator->encode(Cast::toOptInt($key)) : $key;

        $inputAttributes['value']   = $value;
        $inputAttributes['checked'] = ($valueAsString===$keyAsString);

        $html .= Html::generateVoidElement('input', $inputAttributes);

        $html .= $this->labelPrefix;
        $html .= Html::generateElement('label', $labelAttributes, $option[$this->labelKey], $this->labelIsHtml);
        $html .= $this->labelPostfix;
      }
    }

    $html .= '</span>';
    $html .= $this->postfix;

    return $html;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Adds the value of a check radio button the values with the name of this form control as key.
   *
   * @param array $values The values.
   */
  public function getSetValuesBase(array &$values): void
  {
    $values[$this->name] = $this->value;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the value of the check radio button.
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
   * Sets the value of the checked radio button.
   *
   * @param array $values
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
   * Sets the map from the keys in the options to attribute names of the input element.
   *
   * When a key does not exists in an option the attribute will not be generated. The following attributes will ignored:
   * <ul>
   * <li> type
   * <li> name
   * <li> value
   * <li> checked
   * </ul>
   *
   * @param array|null $map The map.
   *
   * @return $this
   */
  public function setInputAttributesMap(?array $map): self
  {
    $this->inputAttributesMap = $map;

    return $this;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the map from the keys in the options to attribute names of the label element.
   *
   * When a key does not exists in an option the attribute will not be generated. The following attribute will ignored:
   * <ul>
   * <li> for
   * </ul>
   *
   * @param array|null $map The map.
   *
   * @return $this
   */
  public function setLabelAttributesMap(?array $map): self
  {
    $this->labelAttributesMap = $map;

    return $this;
  }

//--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets whether labels are HTML code.
   *
   * @param bool $labelIsHtml If true and only if true labels are HTML code.
   *
   * @return $this
   *
   * @since 1.0.0
   * @api
   */
  public function setLabelIsHtml(bool $labelIsHtml = true): self
  {
    $this->labelIsHtml = $labelIsHtml;

    return $this;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the label postfix., e.g. the HTML code that is appended after the HTML code of each label of the checkboxes.
   *
   * @param string|null $htmlSnippet The label postfix.
   *
   * @return $this
   *
   * @since 1.0.0
   * @api
   */
  public function setLabelPostfix(?string $htmlSnippet): self
  {
    $this->labelPostfix = $htmlSnippet ?? '';

    return $this;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the label prefix, e.g. the HTML code that is inserted before the HTML code of each label of the checkboxes.
   *
   * @param string|null $htmlSnippet The label prefix.
   *
   * @return $this
   */
  public function setLabelPrefix(?string $htmlSnippet): self
  {
    $this->labelPrefix = $htmlSnippet ?? '';

    return $this;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the options for this select box.
   *
   * @param array[]|null $options  An array of arrays with the options.
   * @param string       $keyKey   The key holding the keys of the radio buttons.
   * @param string       $labelKey The key holding the labels for the radio buttons.
   *
   * @return $this
   *
   * @since 1.0.0
   * @api
   */
  public function setOptions(?array $options, string $keyKey, string $labelKey): self
  {
    $this->options  = $options;
    $this->keyKey   = $keyKey;
    $this->labelKey = $labelKey;

    return $this;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the obfuscator for the values of the radio buttons. This method should be used when the values of the radio
   * buttons are database IDs.
   *
   * @param Obfuscator|null $obfuscator The obfuscator for the radio buttons values.
   *
   * @return $this
   *
   * @since 1.0.0
   * @api
   */
  public function setOptionsObfuscator(?Obfuscator $obfuscator): self
  {
    $this->optionsObfuscator = $obfuscator;

    return $this;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the value of this form control.
   *
   * @param mixed $value The new value for the form control.
   *
   * @return $this
   *
   * @since 1.0.0
   * @api
   */
  public function setValue($value): self
  {
    $this->value = $value;

    return $this;
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
  protected function loadSubmittedValuesBase(LoadWalker $walker): void
  {
    if ($this->immutable===true)
    {
      $walker->setWithListValue($this->name, $this->value);
    }
    else
    {
      $submitKey = $this->submitKey();

      // Normalize current value as a string.
      $valueAsString = Cast::toManString($this->value, '');

      if ($walker->getSubmittedValue($submitKey))
      {
        // Normalize the submitted value as a string.
        $newValueAsString = Cast::toManString($walker->getSubmittedValue($submitKey), '');

        foreach ($this->options as $option)
        {
          // Get the (database) ID of the option.
          $key         = $option[$this->keyKey];
          $keyAsString = Cast::toManString($key, '');

          // If an obfuscator is installed compute the obfuscated code of the radio button name.
          $code = ($this->optionsObfuscator) ? $this->optionsObfuscator->encode(Cast::toOptInt($key)) : $keyAsString;

          if ($newValueAsString===$code)
          {
            // If the original value differs from the submitted value then the form control has been changed.
            if ($valueAsString!==$keyAsString)
            {
              $walker->setChanged($this->name);
            }

            // Set the white listed value.
            $walker->setWithListValue($this->name, $key);
            $this->value = $key;

            // Leave the loop after first match.
            break;
          }
        }
      }

      if ($walker->getWithListValue($this->name)===null)
      {
        // No value has been submitted or a none white listed value has been submitted
        $this->value = null;
        $walker->setWithListValue($this->name, null);
        if ($valueAsString!=='')
        {
          $walker->setChanged($this->name);
        }
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function validateBase(array &$invalidFormControls): bool
  {
    $valid = true;

    foreach ($this->validators as $validator)
    {
      $valid = $validator->validate($this);
      if (!$valid)
      {
        $invalidFormControls[] = $this;
        break;
      }
    }

    return $valid;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the attributes for the input element.
   *
   * @param array $option The option.
   *
   * @return array
   */
  private function inputAttributes(array $option): array
  {
    $attributes = [];

    if (is_array($this->inputAttributesMap))
    {
      foreach ($this->inputAttributesMap as $key => $name)
      {
        if (isset($option[$key])) $attributes[$name] = $option[$key];
      }
    }

    $attributes['type'] = 'radio';
    $attributes['name'] = $this->submitName;

    if (!isset($attributes['id'])) $attributes['id'] = Html::getAutoId();

    return $attributes;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the attributes for the label element.
   *
   * @param array $option The option.
   *
   * @return array
   */
  private function labelAttributes(array $option): array
  {
    $attributes = [];

    if (is_array($this->labelAttributesMap))
    {
      foreach ($this->labelAttributesMap as $key => $name)
      {
        if (isset($option[$key])) $attributes[$name] = $option[$key];
      }
    }

    return $attributes;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
