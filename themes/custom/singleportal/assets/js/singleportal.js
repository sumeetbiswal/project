jQuery(document).ready(function(){  
jQuery('body').removeAttr('style');
jQuery("#printit").on('click', function() {
 jQuery('#printable').printThis({});
   });
   
  jQuery("#exportit").click(function (event) {
            jQuery("#printable").wordExport();
        });
   
   jQuery("#logout").on('click', function() {
    jQuery("#warning-popup").css('display','block');
  });
     jQuery(".cancel").on('click', function() {
     jQuery("#warning-popup").css('display','none');
      });
	  
      
	jQuery('.active').parents('ul').parent('li').children('a').addClass('active');
	if (jQuery('[data-toggle=tooltip]').length) {
		jQuery('[data-toggle=tooltip]').tooltip();
	}
});
