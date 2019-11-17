<?php
declare(strict_types=1);

namespace Plaisio\Form\Test\Cleaner;

use Plaisio\Form\Cleaner\Cleaner;
use Plaisio\Form\Test\PlaisioTestCase;

/**
 * Abstract parent test cases for cleaners.
 */
abstract class CleanerTest extends PlaisioTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  protected $emptyValues = ['', false, null, ' ', '  ', "\n", "\n \n", "\n \t"];

  protected $zeroValues = ['0', ' 0 ', "\t 0 \n"];

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Creates a cleaner.
   *
   * @return Cleaner
   */
  abstract public function makeCleaner(): Cleaner;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * All cleaners must return null when cleaning empty (i.e. only whitespace) values.
   */
  public function testEmptyValues(): void
  {
    $cleaner = $this->makeCleaner();

    foreach ($this->emptyValues as $value)
    {
      $cleaned = $cleaner->clean($value);

      self::assertNull($cleaned, sprintf("Cleaning '%s' must return null.", $value));
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Most cleaners must return '0' when cleaning '0' values.
   */
  public function testZeroValues(): void
  {
    $cleaner = $this->makeCleaner();

    foreach ($this->zeroValues as $value)
    {
      $cleaned = $cleaner->clean($value);

      self::assertEquals('0', $cleaned, sprintf("Cleaning '%s' must return '0'.", $value));
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
