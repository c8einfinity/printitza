/*var config = {
    map: {
        '*': {
            //'Magento_Catalog/js/price-box': 'Designnbuy_Pricecalculator/js/price-box'
            priceBox : 'Designnbuy_Pricecalculator/js/price-box'
        }
    }
};*/
var config = {
    config: {
        mixins: {
            /*'Magento_Catalog/js/price-box': {
                'Designnbuy_Pricecalculator/js/price-box': true
            },*/
            'Magento_Catalog/js/price-options': {
                'Designnbuy_Pricecalculator/js/price-options': true
            }
        }
    }
};