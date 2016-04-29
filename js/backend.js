(function($){
    $(document).ready(function(){
        // disable all dropdowns if ignored
        $('input#ignore-form').on('change',function(){
            if ($(this).attr('checked')) {
                $('select[name*="cf7-robly"]').attr('disabled', true);
            } else {
                $('select[name*="cf7-robly"]').attr('disabled', false);
            }
        });

        // re-enable just prior to form submission
        $('form#wpcf7-admin-form-element').on('submit', function(){
            $('select[name*="cf7-robly"]').attr('disabled', false);
        });

        // add chosen.js
        $('select[name*="cf7-robly"]').chosen();
    });
})(jQuery);
