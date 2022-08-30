var config = {
    map: {
        "*": {
            "estshipping" : "Lumaplaza_Calculatorshipping/js/customCatalogAddToCart",
			"swatchRenderer01" : "Lumaplaza_Calculatorshipping/js/swatch-renderer01",
			"select2" : "Lumaplaza_Calculatorshipping/js/standalone/select2.full"
		}
    },
	paths: {
		"estshipping" : "Lumaplaza_Calculatorshipping/js/customCatalogAddToCart",
		"swatchRenderer01" : "Lumaplaza_Calculatorshipping/js/swatch-renderer01",
		"select2" : "Lumaplaza_Calculatorshipping/js/standalone/select2.full"
    },
    shim: {
		"estshipping": {
			deps: ["jquery"]
		},
		"swatchRenderer01": {
			deps: ["jquery"]
		},
		"select2": {
			deps: ["jquery"]
		}
	}
};

