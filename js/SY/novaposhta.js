novaposhta_controller = document.location.origin+'/novaposhta/ajax/';
novaposhta = {}; // object
novaposhta.vars = []; // global
novaposhta.update = {};
novaposhta.required = {};
novaposhta.required.disable = {};
novaposhta.dropdown = {};
novaposhta.dropdown.cities = new Element('div');
novaposhta.dropdown.warehouses = new Element('div');
Translator = new Translate('ru_RU');
Translator.add('Select the city','Выберите город');
Translator.add('Select the warehouse','Выберите склад');

// dropdown elements construct
var select = new Element('select');
select.setAttribute('id', 'novaposhta_cities');
select.addClassName('select');
select.setStyle({"width": '100%'});
select.setAttribute('onchange', 'novaposhta.update.city(this.options[this.selectedIndex])');
novaposhta.dropdown.cities.setStyle({'margin': '5px 0'});
novaposhta.dropdown.cities.update(select);
novaposhta.dropdown.cities.prepend(new Element('div').update(Translator.translate('Select the city')));
novaposhta.dropdown.cities.hide();
var select = new Element('select');
select.setAttribute('id', 'novaposhta_warehouses');
select.addClassName('select');
select.setStyle({"width": '100%'});
select.setAttribute('onchange', 'novaposhta.update.warehouse(this.options[this.selectedIndex])');
novaposhta.dropdown.warehouses.setStyle({'margin': '5px 0'});
novaposhta.dropdown.warehouses.update(select);
novaposhta.dropdown.warehouses.prepend(new Element('div').update(Translator.translate('Select the warehouse')));
novaposhta.dropdown.warehouses.hide();
// dropdown elements construct

novaposhta.update.city = function(option){
	if($('billing:city') != undefined){
		$('billing:city').setValue(option.value);
	}
	if($('shipping:city') != undefined){
		$('shipping:city').setValue(option.value);
	}
	// Update city and price
	new Ajax.Request(novaposhta_controller+'city', {
		method: 'post',
	    parameters: {
	        value: option.value
	    },
		onSuccess: function(response) {
			if(response.status == "200"){
				$('s_method_sy_novaposhta_type_WarehouseWarehouse').up().select('label')[0].select('span')[0].update(response.responseJSON[0][0]);
				$('s_method_sy_novaposhta_type_WarehouseDoors').up().select('label')[0].select('span')[0].update(response.responseJSON[0][1]);
			}
		}
	});
	
	// Update warehouses
	new Ajax.Request(novaposhta_controller+'warehouses', {
		method: 'post',
	    parameters: {
	        ref: option.getAttribute('ref')
	    },
		onSuccess: function(response) {
			if(response.status == "200"){
				$('novaposhta_warehouses').update(response.responseText);
				if($('novaposhta_warehouses').options.length>1){
					$('novaposhta_warehouses').up().show();
				}
				else{
					$('novaposhta_warehouses').up().hide();
				}
				novaposhta.update.warehouse($('novaposhta_warehouses').options[$('novaposhta_warehouses').selectedIndex]);
			}
		}
	});
}
novaposhta.update.warehouse = function(option){
	new Ajax.Request(novaposhta_controller+'warehouse', {
	    method: 'post',
	    parameters: {
	        ref: option.getAttribute('ref'),
	        description: $('novaposhta_warehouses').value
	    }
	});
}
novaposhta.required.disable.warehouses = function(){
	$('novaposhta_warehouses').removeClassName('required-entry').removeClassName('validate-select');
}
novaposhta.required.disable.cities = function(){
	$('novaposhta_cities').removeClassName('required-entry').removeClassName('validate-select');
}
novaposhta.required.cities = function(){
	novaposhta.required.disable.cities;
	$('novaposhta_cities').addClassName('required-entry').addClassName('validate-select');
}
novaposhta.required.warehouses = function(){
	novaposhta.required.disable.warehouses;
	$('novaposhta_warehouses').addClassName('required-entry').addClassName('validate-select');
}
novaposhta.required.handler = function(el){
	switch(el.getAttribute('id')) {
		case 's_method_sy_novaposhta_type_WarehouseWarehouse':
			novaposhta.required.cities();
			novaposhta.required.warehouses();
		break;

		case 's_method_sy_novaposhta_type_WarehouseDoors':
			novaposhta.required.cities();
			novaposhta.required.disable.warehouses();
		break;

		default:
			novaposhta.required.disable.cities();
			novaposhta.required.disable.warehouses();
		break;
	}
}
novaposhta.init = function(){
	// If cities and warehouses already exists - exit
	if($('s_method_sy_novaposhta_type_WarehouseWarehouse') == undefined || 
		$('s_method_sy_novaposhta_type_WarehouseDoors') == undefined || 
		$('novaposhta_cities') != undefined || 
		$('novaposhta_warehouses') != undefined){
		return false;
	}
	else{
		$('s_method_sy_novaposhta_type_WarehouseWarehouse').up().up().insert({ before: novaposhta.dropdown.cities });
		$('s_method_sy_novaposhta_type_WarehouseWarehouse').up().insert({ bottom: novaposhta.dropdown.warehouses });
		new Ajax.Request(novaposhta_controller+'cities', {
			onSuccess: function(response) {
				if(response.status == "200"){
					novaposhta.dropdown.cities.select('select')[0].update(response.responseText);
					novaposhta.dropdown.cities.show();
					novaposhta.update.city($('novaposhta_cities').options[$('novaposhta_cities').selectedIndex]);
				}
			}
		});
		// Switch "Required"
		var nameOfRadioGroup = $('s_method_sy_novaposhta_type_WarehouseWarehouse').getAttribute('name');
		var radioGroup = $$('[name="'+nameOfRadioGroup+'"]');
		// Switch event
		radioGroup.invoke('observe', 'change', function(el) {
			novaposhta.required.handler(el.target);
		});
		// And required by default
		radioGroup.each(function(el){
			if(el.checked){
				novaposhta.required.handler(el);
			}
		});
	}
}