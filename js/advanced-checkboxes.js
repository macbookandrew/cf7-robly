(function($){
    $(document).ready(function() {
        $('input[type="checkbox"][name*="locations-of-interest"]').on('change', function() {
            var $roblyListsInput = $('input[name="robly-lists"]'),
                thisVal = $(this).val(),
                currentVal = $roblyListsInput.val().split(','),
                thisValIndex;

                // modify as needed
                if (thisVal.indexOf('Option 1') > -1) {
                    var thisListId = '11111';
                } else if (thisVal.indexOf('Option 2') > -1) {
                    var thisListId = '11111';
                }

            if ($(this).attr('checked')) {
                currentVal.push(thisListId);
            } else {
                thisValIndex = currentVal.indexOf(thisListId);
                if (thisValIndex > -1) {
                    currentVal.splice(thisValIndex, 1);
                }
            }

            $roblyListsInput.val(currentVal.join());
        });
    });
})(jQuery);
