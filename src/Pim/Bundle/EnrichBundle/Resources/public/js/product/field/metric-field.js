"use strict";

define(['pim/field', 'underscore', 'text!pim/template/product/field/metric', 'jquery.select2'], function (Field, _, fieldTemplate) {
    return Field.extend({
        template: _.template(fieldTemplate),
        events: {
            'change .data, .unit': 'updateModel'
        },
        render: function() {
            Field.prototype.render.apply(this, arguments);

            this.$('.unit').select2('destroy').select2({});
        },
        getEmptyData: function() {
            return {
                'data': null,
                'unit': this.attribute.default_metric_unit
            };
        },
        updateModel: function () {
            var data = this.$('.data').val();
            this.setCurrentValue({
                unit: this.$('.unit option:selected').val(),
                data: '' !== data ? data : null
            });
        }
    });
});