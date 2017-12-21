<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Abc\Form\Test\Cleaner;

use SetBased\Abc\Form\Cleaner\TidyCleaner;

/**
 * Test cases for class TidyCleaner.
 */
class TidyCleanerTest extends CleanerTest
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function makeCleaner()
  {
    return TidyCleaner::get();
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function test01()
  {
    $cases                        = [];
    $cases['<h2>subheading</h3>'] = '<h2>subheading</h2>';
    $cases['<h1>heading']         = '<h1>heading</h1>';
    $cases['<br>']                = '<br />';

    $cleaner = TidyCleaner::get();

    foreach ($cases as $dirty => $clean)
    {
      $cleaned = $cleaner->clean($dirty);
      self::assertSame($clean, $cleaned, $dirty);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

