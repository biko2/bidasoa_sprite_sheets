(function($) {
    $.fn.svgSpriteBrowserDialogAjaxCallback = function(sheetFieldEditID,spriteFieldEditID, selectedSheet, selectedSprite) {
      $(`#${sheetFieldEditID}`).val(selectedSheet)
      $(`#${spriteFieldEditID}`).val(selectedSprite).change()
    };
  })(jQuery);
