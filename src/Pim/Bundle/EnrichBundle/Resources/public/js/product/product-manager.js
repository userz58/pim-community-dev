'use strict';

define(['jquery', 'routing', 'pim/attribute-manager'], function ($, Routing, AttributeManager) {
    return {
        productValues: null,
        get: function (id) {
            var promise = $.Deferred();

            $.ajax(
                Routing.generate('pim_enrich_product_rest_get', {id: id}),
                {
                    method: 'GET'
                }
            ).done(function(product) {
                promise.resolve(product);
            });

            return promise.promise();
        },
        save: function (id, data) {
            return $.ajax({
                type: 'POST',
                url: Routing.generate('pim_enrich_product_rest_get', {id: id}),
                contentType: 'application/json',
                data: JSON.stringify(data)
            }).promise();
        },
        getValues: function(product) {
            var promise = $.Deferred();

            if (!this.productValues) {
                AttributeManager.getAttributesForProduct(product).done(function(attributes) {
                    this.productValues = {};

                    _.each(attributes, _.bind(function(attributeCode) {
                        if (product.values[attributeCode]) {
                            this.productValues[attributeCode] = product.values[attributeCode];
                        } else {
                            this.productValues[attributeCode] = [];
                        }
                    }, this));

                    promise.resolve(this.productValues);
                });
            }

            return promise.promise();
        }
    };
});