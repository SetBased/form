/*global define */

//----------------------------------------------------------------------------------------------------------------------
define(
  'SetBased/Abc/Form/ColumnTypeHandler/HtmlControl',

  ['jquery',
    'SetBased/Abc/Table/OverviewTable',
    'SetBased/Abc/Table/ColumnTypeHandler/Text'],

  function ($, OverviewTable, Text) {
    "use strict";
    //------------------------------------------------------------------------------------------------------------------
    /**
     * Prototype for column handlers for columns with a span, div, or a element.
     * 
     * @constructor
     */
    function HtmlControl() {
      // Use parent constructor.
      Text.call(this);
    }

    //------------------------------------------------------------------------------------------------------------------
    HtmlControl.prototype = Object.create(Text.prototype);
    HtmlControl.constructor = HtmlControl;

    //------------------------------------------------------------------------------------------------------------------
    /**
     * Returns the text content of a table cell.
     *
     * @param {HTMLTableElement} tableCell The table cell.
     *
     * @returns {string}
     */
    HtmlControl.prototype.extractForFilter = function (tableCell) {
      return OverviewTable.toLowerCaseNoDiacritics($(tableCell).children().text());
    };

    //------------------------------------------------------------------------------------------------------------------
    /**
     * Returns the text content of a table cell.
     *
     * @param {HTMLTableCellElement} tableCell The table cell.
     *
     * @returns {string}
     */
    HtmlControl.prototype.getSortKey = function (tableCell) {
      return OverviewTable.toLowerCaseNoDiacritics($(tableCell).children().text());
    };

    //------------------------------------------------------------------------------------------------------------------
    /**
     * Register column type handlers.
     */
    OverviewTable.registerColumnTypeHandler('control-div', HtmlControl);
    OverviewTable.registerColumnTypeHandler('control-span', HtmlControl);
    OverviewTable.registerColumnTypeHandler('control-link', HtmlControl);

    //------------------------------------------------------------------------------------------------------------------
    return HtmlControl;
  }
);

//----------------------------------------------------------------------------------------------------------------------
