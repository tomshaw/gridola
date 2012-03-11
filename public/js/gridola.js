/* Gridola - Super Simple Grid for Zend Framework 1.11.11
 * ============================================================
 * http://github.com/tomshaw/gridola
 * ============================================================
 * (The MIT License)
 * 
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * 'Software'), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ============================================================ */

/*!
 * Gridola - Super Simple Grid for Zend Framework 1.11.11
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */

!function ($) {

    "use strict";

    var Action = function (element) {
        this.$element = $(element);
        this.$jsonActions = jsonActions;
        this.process();
    };

    Action.prototype = {
        constructor: Action,
        process: function () {
            var $el = this.$element
              , $jsonActions = this.$jsonActions;
            jQuery.each($jsonActions, function (key, val) {
                if (key == $el.val()) {
                    $(gridolaFormId).attr('action', val.url);
                    return false;
                }
            });
        }
    };

    $.fn.action = function (option) {
        return this.each(function () {
            var $this = $(this)
              , data = $this.data('action');
            if (!this.$default) this.$default = $(gridolaFormId).attr("action");
            $(gridolaFormId).attr('action', this.$default);
            if (!data) $this.data('action', (data = new Action(this)));
            if (typeof option == 'string') data[option].call($this);
        });
    };

    $.fn.action.Constructor = Action;

    $(function () {
        $('body').on('change', '#massaction', function () {
            var $switch = $(this);
            $switch.action($switch.data());
        });
    });
    
}(window.jQuery);

/*!
 * Gridola - Super Simple Grid for Zend Framework 1.11.11
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */

!
function ($) {

    "use strict";

    var selector = 'tbody tr[data-href]',
        namespace = 'click.table-row.data-api',
        checkboxes = '#checkall',
        checknamespace = 'click.checkall.data-api',
        Row = function (element) {
            this.checkboxes($(element).on(namespace, this.location));
        };

    Row.prototype = {
        constructor: Row,
        location: function () {
            window.location = $(this).attr('data-href');
            return false;
        },
        checkboxes: function (element) {
            var $element = $(element);
            $element.find('input').hover(function () {
                $(this).parents('tr').unbind(namespace);
            }).parents('tr').on(namespace, function () {
                $(this).attr('data-href');
            });
        },
        checkall: function () {
            $(this).parents('table:eq(0)').find(':checkbox').attr('checked', this.checked);
        }
    };

    $.fn.tablerow = function (option) {
        return this.each(function () {
            var $this = $(this),
                data = $this.data('tablerow');
            if (!data) $this.data('tablerow', (data = new Row(this)));
            if (typeof option == 'string') data[option].call($this);
        });
    };

    $.fn.tablerow.Constructor = Row;

    $(function () {
        $('body').on(checknamespace, checkboxes, Row.prototype.checkall);
        $(selector).each(function () {
            var $this = $(this);
            if ($this.data('tablerow')) return;
            $this.tablerow($this.data());
        });
    });

}(window.jQuery);