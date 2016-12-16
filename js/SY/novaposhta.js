novaposhta_controller = document.location.origin+'/novaposhta/ajax/';
novaposhta = {}; // object
novaposhta.vars = []; // global
novaposhta.update = {};
novaposhta.required = {};
novaposhta.required.disable = {};
novaposhta.dropdown = {};
Translator = new Translate('ru_RU');
Translator.add('Select the city','Выберите город');
Translator.add('Select the warehouse','Выберите склад');
Translator.add('Select the street','Выберите улицу');
Translator.add('House number','Номер дома');
Translator.add('Flat','Квартира');
Translator.add('Note','Комментарий');
novaposhta.helper = {
	constructor: {
		createElement : function(container,label,type,attributes,classes,elementStyle,containerStyle){
			var el = new Element(type);
			var keys = Object.keys(attributes);
			var values = Object.values(attributes);
			if(keys && keys.length>0){
				keys.each(function(key, index){
					el.setAttribute(key,values[index]);
				});
			}
			if(classes && classes.length>0){
				classes.each(function(className){
					el.addClassName(className);
				});
			}
			if(elementStyle){
				el.setStyle(elementStyle);
			}
			if(container == true){
				var parentEl = new Element('div');
				parentEl.setStyle({'margin': '5px 0'});
				parentEl.update(el);
				parentEl.prepend(new Element('div').update(label));
				el = parentEl;
				if(containerStyle){
					el.setStyle(containerStyle);
				}
			}
			return el;
		}
	}
}
novaposhta.dropdown.cities = novaposhta.helper.constructor.createElement(
	true,
	Translator.translate('Select the city'),
	"select",{
		id: "novaposhta_cities",
		onchange: "novaposhta.update.city(this.options[this.selectedIndex])"
	},
	['select','cities-dropdown'],
	{"width": '100%'}
).hide();
novaposhta.dropdown.warehouses = novaposhta.helper.constructor.createElement(
	true,
	Translator.translate('Select the warehouse'),
	"select",{
		id: "novaposhta_warehouses",
		onchange: "novaposhta.update.warehouse(this.options[this.selectedIndex])"
	},
	['select','warehouses-dropdown'],
	{"width": '100%'}
).hide();
novaposhta.dropdown.streets = novaposhta.helper.constructor.createElement(
	true,
	Translator.translate('Select the street'),
	"select",{
		id: "novaposhta_streets",
		onchange: "novaposhta.update.street(this.options[this.selectedIndex])"
	},
	['select','streets-dropdown'],
	{"width": '100%',"clear":"both"}
).hide();
novaposhta.dropdown.house = novaposhta.helper.constructor.createElement(
	true,
	Translator.translate('House number'),
	"input",
	{
		id: "novaposhta_house",
		onchange: 'novaposhta.update.house(this, "update")',
		onKeyUp: 'novaposhta.update.house(this, "update")',
		type: "text"
	},
	['input-text','house-input'],
	{"width": "100%"},
	{"width": '49%',"float":"left"}
).hide();
novaposhta.dropdown.flat = novaposhta.helper.constructor.createElement(
	true,
	Translator.translate('Flat'),
	"input",
	{
		id: "novaposhta_flat",
		onchange: 'novaposhta.update.flat(this, "update")',
		onKeyUp: 'novaposhta.update.flat(this, "update")',
		type: "text"
	},
	['input-text','flat-input'],
	{"width": "100%"},
	{"width": '49%',"float":"right"}
).hide();
novaposhta.dropdown.note = novaposhta.helper.constructor.createElement(
	true,
	Translator.translate('Note'),
	"input",
	{
		id: "novaposhta_note",
		onchange: 'novaposhta.update.note(this, "update")',
		onKeyUp: 'novaposhta.update.note(this, "update")',
		type: "text"
	},
	['input-text','note-input'],
	{"width": '100%',"clear":"both"}
).hide();

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
	    onCreate: function(){
	    	$('novaposhta_warehouses').setAttribute('disabled','disabled');
	    	$('novaposhta_streets').setAttribute('disabled','disabled');
	    	$('novaposhta_house').setAttribute('disabled','disabled');
	    	$('novaposhta_flat').setAttribute('disabled','disabled');
	    	$('novaposhta_note').setAttribute('disabled','disabled');
	    },
		onSuccess: function(response) {
			if(response.status == "200"){
				$('s_method_sy_novaposhta_type_WarehouseWarehouse').up().select('label')[0].select('span')[0].update(response.responseJSON[0][0]);
				$('s_method_sy_novaposhta_type_WarehouseDoors').up().select('label')[0].select('span')[0].update(response.responseJSON[0][1]);
				$('novaposhta_warehouses').removeAttribute('disabled');
		    	$('novaposhta_streets').removeAttribute('disabled');
		    	$('novaposhta_house').removeAttribute('disabled');
		    	$('novaposhta_flat').removeAttribute('disabled');
		    	$('novaposhta_note').removeAttribute('disabled');
				novaposhta.onChangeCity();
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
				if($('novaposhta_warehouses').options.length>1 && $('s_method_sy_novaposhta_type_WarehouseWarehouse').checked){
					$('novaposhta_warehouses').up().show();
				}
				else{
					$('novaposhta_warehouses').up().hide();
				}
				novaposhta.update.warehouse($('novaposhta_warehouses').options[$('novaposhta_warehouses').selectedIndex]);
			}
		}
	});
	// Update streets
	new Ajax.Request(novaposhta_controller+'streets', {
		method: 'post',
	    parameters: {
	        ref: option.getAttribute('ref')
	    },
		onSuccess: function(response) {
			if(response.status == "200"){
				$('novaposhta_streets').update(response.responseText);
				if($('novaposhta_streets').options.length>1 && $('s_method_sy_novaposhta_type_WarehouseDoors').checked){
					$('novaposhta_streets').up().show();
				}
				else{
					$('novaposhta_streets').up().hide();
				}
				novaposhta.update.street($('novaposhta_streets').options[$('novaposhta_streets').selectedIndex], true);
			}
		}
	});
}
novaposhta.onChangeCity = function(){
	// Or you can paste your trigger like revew.update() or something else
	// this event will be call on price update and recollect shipping methods
	if($('s_method_sy_novaposhta_type_WarehouseDoors') != undefined && 
		$('s_method_sy_novaposhta_type_WarehouseDoors').checked){
		$('s_method_sy_novaposhta_type_WarehouseDoors').dispatchEvent(new Event('click'));
		$('s_method_sy_novaposhta_type_WarehouseDoors').dispatchEvent(new Event('change'));
	}
	if($('s_method_sy_novaposhta_type_WarehouseWarehouse') != undefined && 
		$('s_method_sy_novaposhta_type_WarehouseWarehouse').checked){
		$('s_method_sy_novaposhta_type_WarehouseWarehouse').dispatchEvent(new Event('click'));
		$('s_method_sy_novaposhta_type_WarehouseWarehouse').dispatchEvent(new Event('change'));
	}
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
novaposhta.update.street = function(option, safe){
	// safe mode = without ajax, only handler events
	// without already street sets clean
	if( typeof safe == 'undefined' || safe != true ){
		new Ajax.Request(novaposhta_controller+'street', {
		    method: 'post',
		    parameters: {
		        ref: option.getAttribute('ref'),
		        name: option.getAttribute('value')
		    }
		});
		if($('billing:street1') != undefined){
			$('billing:street1').setValue($('novaposhta_streets').getValue());
		}
		if($('shipping:street1') != undefined){
			$('shipping:street1').setValue($('novaposhta_streets').getValue());
		}
	}
	if(option.getAttribute('value') != null){
		if($('s_method_sy_novaposhta_type_WarehouseDoors').checked){
			novaposhta.dropdown.house.show();
			novaposhta.dropdown.flat.show();
			novaposhta.dropdown.note.show();
		}
		novaposhta.update.house($('novaposhta_house'));
		novaposhta.update.flat($('novaposhta_flat'));
		novaposhta.update.note($('novaposhta_note'));
	}
	else{
		novaposhta.dropdown.house.hide();
		novaposhta.dropdown.flat.hide();
		novaposhta.dropdown.note.hide();
	}
}
novaposhta.update.house = function(input, mode){
	new Ajax.Request(novaposhta_controller+'house', {
	    method: 'post',
	    parameters: {
	        value: input.getValue(),
	        mode: mode
	    },
	    onComplete: function(response){
	    	if(mode != 'update'){
	    		input.setValue(response.responseJSON.house);
	    	}
			if($('billing:house') != undefined){
				$('billing:house').setValue(response.responseJSON.house);
			}
			if($('shipping:house') != undefined){
				$('shipping:house').setValue(response.responseJSON.house);
			}
	    }
	});
}
novaposhta.update.flat = function(input, mode){
	new Ajax.Request(novaposhta_controller+'flat', {
	    method: 'post',
	    parameters: {
	        value: input.getValue(),
	        mode: mode
	    },
	    onComplete: function(response){
	    	if(mode != 'update'){
	    		input.setValue(response.responseJSON.flat);
	    	}
			if($('billing:flat') != undefined){
				$('billing:flat').setValue(response.responseJSON.flat);
			}
			if($('shipping:flat') != undefined){
				$('shipping:flat').setValue(response.responseJSON.flat);
			}
	    }
	});
}
novaposhta.update.note = function(input, mode){
	new Ajax.Request(novaposhta_controller+'note', {
	    method: 'post',
	    parameters: {
	        value: input.getValue(),
	        mode: mode
	    },
	    onComplete: function(response){
	    	if(mode != 'update'){
	    		input.setValue(response.responseJSON.note);
	    	}
			if($('billing:note') != undefined){
				$('billing:note').setValue(response.responseJSON.note);
			}
			if($('shipping:note') != undefined){
				$('shipping:note').setValue(response.responseJSON.note);
			}
	    }
	});
}
novaposhta.required.disable.warehouses = function(){
	$('novaposhta_warehouses').removeClassName('required-entry').removeClassName('validate-select');
}
novaposhta.required.disable.cities = function(){
	$('novaposhta_cities').removeClassName('required-entry').removeClassName('validate-select');
}
novaposhta.required.disable.streets = function(){
	$('novaposhta_streets').removeClassName('required-entry').removeClassName('validate-select');
}
novaposhta.required.disable.house = function(){
	$('novaposhta_house').removeClassName('required-entry');
}
novaposhta.required.cities = function(){
	novaposhta.required.disable.cities;
	$('novaposhta_cities').addClassName('required-entry').addClassName('validate-select');
}
novaposhta.required.warehouses = function(){
	novaposhta.required.disable.warehouses;
	$('novaposhta_warehouses').addClassName('required-entry').addClassName('validate-select');
}
novaposhta.required.streets = function(){
	novaposhta.required.disable.streets;
	$('novaposhta_streets').addClassName('required-entry').addClassName('validate-select');
}
novaposhta.required.house = function(){
	novaposhta.required.disable.house;
	$('novaposhta_house').addClassName('required-entry');
}
novaposhta.required.handler = function(el){
	switch(el.getAttribute('id')) {
		case 's_method_sy_novaposhta_type_WarehouseWarehouse':
			novaposhta.required.disable.streets();
			novaposhta.required.disable.house();
			novaposhta.required.cities();
			novaposhta.required.warehouses();
			novaposhta.dropdown.streets.hide();
			novaposhta.dropdown.house.hide();
			novaposhta.dropdown.flat.hide();
			novaposhta.dropdown.note.hide();
			novaposhta.dropdown.warehouses.show();
			novaposhta.dropdown.cities.show();
		break;

		case 's_method_sy_novaposhta_type_WarehouseDoors':
			novaposhta.required.disable.warehouses();
			novaposhta.required.cities();
			novaposhta.required.streets();
			novaposhta.required.house();
			novaposhta.dropdown.streets.show();
			if($('novaposhta_streets').getValue() && $('novaposhta_streets').getValue() != ""){
				novaposhta.dropdown.house.show();
				novaposhta.dropdown.flat.show();
				novaposhta.dropdown.note.show();
			}
			novaposhta.dropdown.warehouses.hide();
			novaposhta.dropdown.cities.show();
		break;

		default:
			novaposhta.required.disable.cities();
			novaposhta.required.disable.warehouses();
			novaposhta.required.disable.streets();
			novaposhta.required.disable.house();
			novaposhta.dropdown.streets.hide();
			novaposhta.dropdown.house.hide();
			novaposhta.dropdown.flat.hide();
			novaposhta.dropdown.note.hide();
			novaposhta.dropdown.warehouses.hide();
			novaposhta.dropdown.cities.hide();
		break;
	}
}
novaposhta.init = function(){
	// If cities and warehouses already exists - exit
	if($('s_method_sy_novaposhta_type_WarehouseWarehouse') == undefined || 
		$('s_method_sy_novaposhta_type_WarehouseDoors') == undefined || 
		$('novaposhta_cities') != undefined || 
		$('novaposhta_warehouses') != undefined || 
		$('novaposhta_streets') != undefined){
		return false;
	}
	else{
		$('s_method_sy_novaposhta_type_WarehouseWarehouse').up().up().insert({ before: novaposhta.dropdown.cities });
		$('s_method_sy_novaposhta_type_WarehouseWarehouse').up().insert({ bottom: novaposhta.dropdown.warehouses });
		$('s_method_sy_novaposhta_type_WarehouseDoors').up().insert({ bottom: novaposhta.dropdown.streets });
		$('s_method_sy_novaposhta_type_WarehouseDoors').up().insert({ bottom: novaposhta.dropdown.house });
		$('s_method_sy_novaposhta_type_WarehouseDoors').up().insert({ bottom: novaposhta.dropdown.flat });
		$('s_method_sy_novaposhta_type_WarehouseDoors').up().insert({ bottom: novaposhta.dropdown.note });
		new Ajax.Request(novaposhta_controller+'cities', {
			onSuccess: function(response) {
				if(response.status == "200"){
					novaposhta.dropdown.cities.select('select')[0].update(response.responseText);
					if($('s_method_sy_novaposhta_type_WarehouseWarehouse').checked && $('s_method_sy_novaposhta_type_WarehouseDoors').checked){
						novaposhta.dropdown.cities.show();
					}
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