<?php
declare(strict_types=1);

namespace Plaisio\Form\Control;

use Plaisio\Form\Control\Traits\Mutability;
use Plaisio\Form\Walker\LoadWalker;
use Plaisio\Helper\Html;
use Plaisio\Obfuscator\Obfuscator;
use SetBased\Helper\Cast;

/**
 * Class for form controls of with multiple checkboxes.
 */
class CheckboxesControl extends Control
{
  //--------------------------------------------------------------------------------------------------------------------
  use Mutability;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The key in $options holding the checked flag for the checkboxes.
   *
   * @var string
   */
  protected string $checkedKey;

  /**
   * The map from the keys in the options to attribute names of the input elements.
   *
   * @var array|null
   */
  protected ?array $inputAttributesMap = null;

  /**
   * The key in $options holding the keys for the checkboxes.
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
   * If true and only if true labels are HTML code. Otherwise special characters in the labels will be replaced with
   * HTML entities.
   *
   * @var bool
   */
  protected bool $labelIsHtml = false;

  /**
   * The key in $options holding the labels for the checkboxes.
   *
   * @var string
   */
  protected string $labelKey;

  /**
   * The HTML snippet appended after each label for the checkboxes.
   *
   * @var string
   */
  protected string $labelPostfix = '';

  /**
   * The HTML snippet inserted before each label for the checkboxes.
   *
   * @var string
   */
  protected string $labelPrefix = '';

  /**
   * The options of this select box.
   *
   * @var array[]|null
   */
  protected ?array $options = null;

  /**
   * The obfuscator for the names of the checkboxes.
   *
   * @var Obfuscator|null
   */
  protected ?Obfuscator $optionsObfuscator = null;

  /**
   * The values of the checkboxes.
   *
   * @var array
   */
  protected array $value = [];

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  public function __construct(?string $name)
  {
    parent::__construct($name);

    $this->attributes['class'] = 'checkboxes';
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
      foreach ($this->options as $option)
      {
        $inputAttributes = $this->inputAttributes($option);
        $labelAttributes = $this->labelAttributes($option);

        $labelAttributes['for'] = $inputAttributes['id'];

        // Get the (database) key of the option.
        $key = $option[$this->keyKey];

        // If an obfuscator is installed compute the obfuscated code of the (database) ID.
        $code = ($this->optionsObfuscator) ? $this->optionsObfuscator->encode(Cast::toOptInt($key)) : $key;

        $inputAttributes['name']    = ($this->submitName!=='') ? $this->submitName.'['.$code.']' : $code;
        $inputAttributes['checked'] = (!empty($option[$this->checkedKey]));

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
   * Adds the value of checked checkboxes the values with the name of this form control as key.
   *
   * @param array $values The values.
   */
  public function getSetValuesBase(array &$values): void
  {
    if ($this->name==='')
    {
      $tmp = &$values;
    }
    else
    {
      $values[$this->name] = [];
      $tmp                 = &$values[$this->name];
    }

    foreach ($this->options as $i => $option)
    {
      // Get the (database) ID of the option.
      $key = $option[$this->keyKey];

      // Get the original value (i.e. the option is checked or not).
      $tmp[$key] = (!empty($option[$this->checkedKey]));
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
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
   * Set the values (i.e. checked or not checked) of the checkboxes of this form control.
   *
   * @param array $values the values.
   */
  public function mergeValuesBase(array $values): void
  {
    if ($this->name==='')
    {
      // Nothing to do.
    }
    elseif (isset($values[$this->name]))
    {
      $values = &$values[$this->name];
    }
    else
    {
      $values = null;
    }

    if ($values!==null)
    {
      foreach ($this->options as $id => $option)
      {
        if (array_key_exists($option[$this->keyKey], $values))
        {
          $this->options[$id][$this->checkedKey] = !empty($values[$option[$this->keyKey]]);
        }
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the map from the keys in the options to attribute names of the input element.
   *
   * Note the following attributes will ignored:
   * <ul>
   * <li> type
   * <li> name
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
   * Note the following attribute will ignored:
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
   * Sets the label prefix, e.g. the HTML code that is inserted before the HTML code of each label of the checkboxes.
   *
   * @param string $htmlSnippet The label prefix.
   *
   * @return $this
   *
   * @since 1.0.0
   * @api
   */
  public function setLabelPostfix(string $htmlSnippet): self
  {
    $this->labelPostfix = $htmlSnippet;

    return $this;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the label postfix., e.g. the HTML code that is appended after the HTML code of each label of the checkboxes.
   *
   * @param string $htmlSnippet The label postfix.
   *
   * @return $this
   *
   * @since 1.0.0
   * @api
   */
  public function setLabelPrefix(string $htmlSnippet): self
  {
    $this->labelPrefix = $htmlSnippet;

    return $this;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the options for the checkboxes box.
   *
   * @param array[]|null $options    An array of arrays with the options.
   * @param string       $keyKey     The key holding the keys of the checkboxes.
   * @param string       $labelKey   The key holding the labels for the checkboxes.
   * @param string       $checkedKey The key holding the checked flag. Any
   *                                 [non-empty](http://php.net/manual/function.empty.php) value results that the
   *                                 checkbox is checked.
   *
   * @return $this
   *
   * @since 1.0.0
   * @api
   */
  public function setOptions(?array $options,
                             string $keyKey,
                             string $labelKey,
                             string $checkedKey = 'abc_map_checked'): self
  {
    $this->options    = $options;
    $this->keyKey     = $keyKey;
    $this->labelKey   = $labelKey;
    $this->checkedKey = $checkedKey;

    return $this;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the obfuscator for the names of the checkboxes. This method should be used when the names of the checkboxes
   * are database IDs.
   *
   * @param Obfuscator $obfuscator The obfuscator for the checkbox names.
   *
   * @return $this
   *
   * @since 1.0.0
   * @api
   */
  public function setOptionsObfuscator(Obfuscator $obfuscator): self
  {
    $this->optionsObfuscator = $obfuscator;

    return $this;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the values of the checkboxes, a [non-empty](http://php.net/manual/function.empty.php) value will check a
   * checkbox.
   *
   * @param array|null $values The values.
   */
  public function setValuesBase(?array $values): void
  {
    if ($this->name==='')
    {
      // Nothing to do.
    }
    elseif (isset($values[$this->name]))
    {
      $values = &$values[$this->name];
    }
    else
    {
      $values = null;
    }

    foreach ($this->options as $id => $option)
    {
      $this->options[$id][$this->checkedKey] = !empty($values[$option[$this->keyKey]]);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function loadSubmittedValuesBase(LoadWalker $walker): void
  {
    $submitKey = $this->submitKey();
    $subWalker = $walker->descend($this->name, $submitKey);

    if ($this->immutable===true)
    {
      foreach ($this->options as $i => $option)
      {
        $subWalker->setWithListValue($option[$this->keyKey], $this->options[$i][$this->checkedKey]);
      }
    }
    else
    {
      foreach ($this->options as $i => $option)
      {
        $key  = $option[$this->keyKey];
        $code = ($this->optionsObfuscator) ? $this->optionsObfuscator->encode(Cast::toOptInt($key)) : $key;

        $value    = $option[$this->checkedKey] ?? false;
        $newValue = $subWalker->getSubmittedValue($code) ?? false;
        if (empty($value)!==empty($newValue))
        {
          $subWalker->setChanged($key);
        }
        $this->value[$key] = !empty($newValue);
        $subWalker->setWithListValue($key, $this->value[$key]);

        $this->options[$i][$this->checkedKey] = $this->value[$key];
      }
    }

    $walker->ascend($this->name);
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
        $invalidFormControls[$this->name] = $this;
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

    $attributes['type'] = 'checkbox';

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
