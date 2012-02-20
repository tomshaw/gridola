/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
*/

/**
 * Dynamically injects a new form action based on the mass action select url key value. 
 */
$(document).ready(function () {
    $('#massaction').change(function () {
        var action = $(this).val();
        jQuery.each(jsonActions, function (key, val) {
            if (key == action) {
                $(gridolaFormId).attr('action', val.url);
                return false;
            }
        });
    });
});

/**
 * Removes the clickable row functionality in table elements using inputs. 
 * Namely checkboxes for mass actions.
 */
jQuery(function ($) {
    $('tbody tr[data-href]').addClass('clickable').click(function () {
        window.location = $(this).attr('data-href');
    }).find('input').hover(function () {
        $(this).parents('tr').unbind('click');
    }, function () {
        $(this).parents('tr').click(function () {
            window.location = $(this).attr('data-href');
        });
    });
}); 