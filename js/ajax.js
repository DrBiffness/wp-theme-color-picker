'use strict';

jQuery(function($) {
    // Add Color Picker to all inputs that have 'color-field' class
    $('.cpa-color-picker').wpColorPicker();

    $('#colorPickers').submit(function() {
        const colors = $('.cpa-color-picker').serializeArray();
        let errors = [];
        for (const { name, value } of colors) {
            if (!value) {
                continue;
            }
            
            if (!validator.isHexColor(value)) {
                errors.push(`Invalid color ${value} for ${name}`);
            }
        }
        if (errors.length > 0) {
            alert(errors.join('\n'));
            return false;
        }

        $.post(ajax_object.ajax_url, {
            action: 'update_css',
            colors
        }).done(function(res) {
            console.dir(res);
            if (res.success) {
                alert('CSS themes successfully updated!');
            } else {
                alert(res.data);
            }
        });

        return false;
    });
});
