/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

;(function ($) {
    "use strict";

    const pluginName = "suggestAlias",
        defaults = {
            prefix: '',
            slugBaseElement: null,
            aliasElement: null
        };

    function Plugin(element, options) {
        this.element = element;

        this.settings = $.extend({}, defaults, options);
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    $.extend(Plugin.prototype, {
        init: function () {
            $(this.element).on('click', (e) => {
                e.preventDefault();

                this.performAjaxRequest();
            });
        },
        performAjaxRequest: function () {
            if (this.canPerformAjaxRequest()) {
                $.ajax({
                    url: this.element.href,
                    type: 'post',
                    data: {
                        prefix: this.settings.prefix,
                        title: this.settings.slugBaseElement.val()
                    },
                    beforeSend: () => {
                        $(this.element).addClass('disabled');
                    }
                }).done((responseData) => {
                    try {
                        if (typeof responseData.alias !== "undefined" && responseData.alias.length > 0) {
                            $(this.settings.aliasElement).val(responseData.alias);
                        }
                    } catch (err) {
                        console.log(err.message);
                    } finally {
                        $(this.element).removeClass('disabled');
                    }
                });
            }
        },
        /**
         * @returns {boolean}
         */
        canPerformAjaxRequest: function () {
            return this.settings.slugBaseElement !== null
                && this.settings.aliasElement !== null
                && this.settings.slugBaseElement.val() !== '';
        }
    });

    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new Plugin(this, options));
            }
        });
    };
})(jQuery);

jQuery(document).ready(function ($) {
    $('#seo-alias-suggestion').suggestAlias({
        prefix: $('[data-seo-slug-prefix]').data('seo-slug-prefix'),
        slugBaseElement: $('[data-seo-slug-base="true"]'),
        aliasElement: $('#alias')
    });
});
