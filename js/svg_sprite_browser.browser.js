
(function($, Drupal) {
  Drupal.behaviors.svgSpriteBrowser = {
    attach: function(context, settings) {
      const selectedSvgClass = 'checked';

      var wrapperElement = once('svgSpriteBrowserBehavior', '.js-svg-sprite-browser-wrapper', context);
      wrapperElement
      .forEach(function() {
        const spriteFieldEditName = $("#svg-sprite-browser-widget-field-id").val();
        const spriteWidgetElement = $("[data-drupal-selector='" + spriteFieldEditName + "']");
        let refreshSelected = function (){
          let selectedSprite = $('#svg-sprite-browser-selected-sprite').val();
          let selectedSheet = $('#svg-sprite-browser-selected-sheet').val();

          $(".js-svg-sprite-browser-item").removeClass(selectedSvgClass);
          $("[data-svg-sprite-machine-name='" + selectedSheet + "." + selectedSprite +"']").addClass(selectedSvgClass);

        }
        refreshSelected();
        //if (spriteWidgetElement.length) {
          $('.js-svg-sprite-browser-item').click(function(ev){
            let id = $(this).data('svg-sprite-id');
            let sheet = $(this).data('svg-sprite-sheet');

            $('#svg-sprite-browser-selected-sheet').val(sheet);
            $('#svg-sprite-browser-selected-sprite').val(id);
            refreshSelected();
          })


          // Search filter box.
          let to = false;
          let searchElement = $("#svg-sprite-browser-search");
          searchElement.on('search', function() {
            $('.js-svg-sprite-browser-item').each(function(){
              let element =  $(this);
              element.fadeIn(100);
            });
          });
          searchElement.keydown(function() {
            const searchInput = $(this);
            if (to) {
              clearTimeout(to);
            }
            to = setTimeout(function() {
              const searchValue = searchInput.val();
              $('.js-svg-sprite-browser-item').each(function(){
                let element =  $(this);
                let id = element.data('svg-sprite-id');

                if(id.search(searchValue) === -1) {
                  if(!element.hasClass(selectedSvgClass))
                    element.fadeOut(100);
                } else {
                  element.fadeIn(100);

                }
              });
            }, 250);
          });
       // }
      });
    }
  };
})(jQuery, Drupal);
