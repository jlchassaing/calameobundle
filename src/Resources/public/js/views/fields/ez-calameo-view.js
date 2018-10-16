YUI.add('ez-calameo-view', function (Y) {
  "use strict";
  /**
   * Provides the Calameo field view
   *
   * @module ez-calameo-view
   */
  Y.namespace('eZ');

  /**
   * The Calameo field view
   *
   * @namespace eZ
   * @class CalameoView
   * @constructor
   * @extends eZ.FieldView
   */
  Y.eZ.CalameoView = Y.Base.create('calameoView', Y.eZ.FieldView, [], {
    /**
     * Returns the value to be used in the template. If the value is not
     * filled, it returns undefined otherwise an object with a `url` entry.
     *
     * @method _getFieldValue
     * @protected
     * @return Object
     */
    _getFieldValue: function () {
      var value = this.get('field').fieldValue, res;

      if ( !value || !value.url ) {
        return res;
      }

      res = {url: value.url, img: value.data.PosterUrl};

      return res;
    }
  });

  Y.eZ.FieldView.registerFieldView('ezcalameo', Y.eZ.CalameoView);
});
