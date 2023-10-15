(function ($) {
    $(document).ready(
        function () {
            jQuery('#edit-addresscopy').next('label').removeClass('control-label col-md-3');
            isChecked = $('#edit-addresscopy').prop('checked')?true:false;
            if(isChecked) {
                $('#permanentAddress').hide();
            }
            else
            {
                $('#permanentAddress').show();
            }
        
        
            $("#edit-addresscopy").click(
                function () {
                    isChecked = $('#edit-addresscopy').prop('checked')?true:false;
                    if(isChecked) {
                        $('#permanentAddress').hide();
                    }
                    else
                    {
                        $('#permanentAddress').show();
                    }
        
                }
            );   
        }
    );
})(jQuery);

