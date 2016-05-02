(function($){
    $(document).ready(function(){
        // disable all dropdowns if ignored
        $('input#ignore-form').on('change',function(){
            if ($(this).attr('checked')) {
                $('select[name*="cf7-robly"], button.cf7-robly-add-custom-field').attr('disabled', true).trigger('chosen:updated');
            } else {
                $('select[name*="cf7-robly"], button.cf7-robly-add-custom-field').attr('disabled', false).trigger('chosen:updated');
            }
        });

        // re-enable just prior to form submission
        $('form#wpcf7-admin-form-element').on('submit', function(){
            $('select[name*="cf7-robly"]').attr('disabled', false);
        });

        // add chosen.js
        $('select[name*="cf7-robly"]:not([name*="custom-field-template-name"])').chosen({width: '100%'});

        // add message to Robly tab if main content is changed
        $('#wpcf7-form').on('change', function(){
            $('.cf7-robly-message').html('It looks like you&rsquo;ve changed the form content; please save the form before changing any Robly settings.');
            $('select[name*="cf7-robly"]').attr('disabled', true);
            $('.cf7-robly-table').hide();
        });

        // add ability to clone new custom fields
        $('.cf7-robly-add-custom-field').on('click', function(event) {
            event.preventDefault();
            $('.cf7-robly-field-custom-field-template').clone(true).attr('class', 'cf7-robly-field-custom').addClass('new').appendTo('.cf7-robly-table tbody').find('select').chosen({width: '100%'});
        });

        // set name of new custom fields
        $('.cf7-robly-table').on('blur', '.cf7-robly-field-custom.new input[name="custom-field-name"]', function() {
            var $parent = $(this).parents('.cf7-robly-field-custom.new'),
                customFieldName = $(this).val().length > -1 ? $(this).val() : 'custom-field-name';

            $parent.find('select').attr('name', 'cf7-robly[fields][' + customFieldName + '][]').trigger('chosen:updated');
            $parent.find('code span.name').html(customFieldName);
        });
    });
})(jQuery);
