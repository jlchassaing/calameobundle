YUI.add('ez-calameo-editview', function (Y) {
  "use strict";
  /**
   * Provides the field edit view for the Calameo (ezcalameo) fields
   *
   * @module ez-calameo-editview
   */
  Y.namespace('eZ');

  var FIELDTYPE_IDENTIFIER = 'ezcalameo';

  /**
   * Calameo edit view
   *
   * @namespace eZ
   * @class CalameoEditView
   * @constructor
   * @extends eZ.FieldEditView
   */
  Y.eZ.CalameoEditView = Y.Base.create('calameoEditView', Y.eZ.FieldEditView, [], {
    events: {
      '.ez-calameo-input-ui input': {
        'blur': 'validate',
        'valuechange': 'validate'
      }
    },

    /**
     * Validates the current input of calameo
     *
     * @method validate
     */
    validate: function () {
      var validity = this._getInputValidity();

      if ( validity.typeMismatch || validity.patternMismatch ) {
        this.set('errorStatus', Y.eZ.trans('url.not.valid', {}, 'fieldedit'));
      } else if ( validity.valueMissing ) {
        this.set('errorStatus', Y.eZ.trans('this.field.is.required', {}, 'fieldedit'));
      } else {
        this.set('errorStatus', false);
      }
    },

    /**
     * Defines the variables to be imported in the field edit template for calameo.
     *
     * @protected
     * @method _variables
     * @return {Object} containing isRequired
     * entries
     */
    _variables: function () {
      var def = this.get('fieldDefinition');

      return {
        "isRequired": def.isRequired
      };
    },

    /**
     * Returns the input validity state object for the input generated by
     * the calameo template
     *
     * See https://developer.mozilla.org/en-US/docs/Web/API/ValidityState
     *
     * @protected
     * @method _getInputValidity
     * @return {ValidityState}
     */
    _getInputValidity: function () {
      return this.get('container').one('.ez-calameo-input-ui input').get('validity');
    },

    /**
     * Returns the currently filled calameo value
     *
     * @protected
     * @method _getFieldValue
     * @return String
     */
    _getFieldValue: function () {
      return this.get('container').one('.ez-calameo-input-ui input').get('value');
    }
  });

  Y.eZ.FieldEditView.registerFieldEditView(
    FIELDTYPE_IDENTIFIER, Y.eZ.CalameoEditView
  );
});
