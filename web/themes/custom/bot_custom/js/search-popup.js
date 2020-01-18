(function ($, Drupal) {
  Drupal.behaviors.BOTCustomSearchPopup = {
    attach: function (context, settings) {
      $('#block-exposedformsearch-contentsearch-content', context).once('BOTCustomSearchPopup').each(function () {
        $('#block-exposedformsearch-contentsearch-content').append('<a class="close-btn" href="javascript:void(0)">Close</a>');
      });

      $('#block-exposedformsearch-contentsearch-content').on('click.BOTCustomSearchPopupClose', '.close-btn', function(e){
        e.preventDefault();
        window.location.hash = '';
      });
    }
  };
})(jQuery, Drupal);
