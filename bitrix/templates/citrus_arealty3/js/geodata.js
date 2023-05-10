;(function () {
    "use strict";

	/**
	 *
	 * @param geodataDivId
	 * @param {object} fields - ����:
	 * @param {int} fields.Latitude - ������
	 * @param {int} fields.Longitude - �������
	 * @param {string} fields.Address - �����
	 * @param {object} settings - ���������:
	 * @param {boolean} settings.allow_polygon - �������� �������� ��������
	 */
	window.MapGeoProperty = function (geodataDivId, fields, settings) {
        var self = this;
		self.fields = fields || {};
		self.settings = settings || {};

		var mapProperty = document.getElementById(geodataDivId);

		if (mapProperty.hasAttribute("citrus-map-init")) {
			return;
		}

		mapProperty.setAttribute("citrus-map-init", 1);

		var coordLat = self.fields.Latitude || 0;
		var coordLong = self.fields.Longitude || 0;

	    var mapAddress = mapProperty.querySelector(".js-citrus-map-select-object-title"),
		    mapNode = mapProperty.querySelector(".js-citrus-map-select-object-map");

	    self.mapObjectInput = mapProperty.querySelector(".js-citrus-map-selected-object-input");

	    var inputBounds0 = mapProperty.querySelector(".js-citrus-map-mapbounds0");
	    var inputBounds1 = mapProperty.querySelector(".js-citrus-map-mapbounds1");
	    var inputBounds2 = mapProperty.querySelector(".js-citrus-map-mapbounds2");
	    var inputBounds3 = mapProperty.querySelector(".js-citrus-map-mapbounds3");
	    var inputZoom = mapProperty.querySelector(".js-citrus-map-mapzoom");
	    self.polygonInput = mapProperty.querySelector(".js-citrus-map-polygon");

	    var addressInputFields = {
		    'CountryName': 1,
		    'AdministrativeAreaName': 1,
		    'SubAdministrativeAreaName': 1,
		    'LocalityName': 1,
		    'DependentLocalityName': 1,
		    'ThoroughfareName': 1,
		    'PremiseNumber': 1
	    };

		// ������ ��������� ��������
	    this.addPolygonButton = function () {
		    self.polygonButton = new ymaps.control.Button({
			    data: {
				    title: BX.message('GEODATA_BTN_TITLE'),
				    content: BX.message('GEODATA_BTN_CONTENT'),
			    },
			    options: {
				    selectOnClick: true,
				    maxWidth: [30, 100, 300],
			    }
		    });

		    self.polygonButton.events.add('select', function (e) {
			    self.polygonButton.data.set('content', BX.message('GEODATA_BTN_CONTENT_END'));
			    //if(self.placemark) self.placemark.options.set('visible', false);
			    self.polygon.editor.startDrawing();
		    });
		    self.polygonButton.events.add('deselect', function (e) {
			    self.polygonButton.data.set('content', BX.message('GEODATA_BTN_CONTENT'));
			    //if(self.placemark) self.placemark.options.set('visible', true);
			    self.polygon.editor.stopDrawing();
		    });
		    self.map.controls.add(self.polygonButton, {float: 'right'});
	    };
	    this.addPolygon = function () {
		    var polygonData = self.fields.polygon || [];
		    if (self.fields.polygon.length) self.polygonInput.value = JSON.stringify(self.fields.polygon);
		    self.polygon = new ymaps.Polygon([polygonData], {}, {
			    // ������ � ������ ���������� ����� ������.
			    editorDrawingCursor: "crosshair",
			    // ����������� ���������� ���������� ������.
			    editorMaxPoints: 10,
			    // ���� �������.
			    fillColor: '#ff0000',
			    fillOpacity: 0.4,
			    // ���� �������.
			    strokeColor: '#FF7800',
			    // ������ �������.
			    strokeWidth: 3,
			    //draggable: true,
		    });
		    // ��������� ������������� �� �����.
		    self.map.geoObjects.add(self.polygon);
		    // � ������ ���������� ����� ������ ������ ���� ������� ��������������.
		    var stateMonitor = new ymaps.Monitor(self.polygon.editor.state);
		    var onUpdatepolygon = function () {
			    //self.mapData.polygon = stateMonitor.get('drawing') ? [] : self.polygon.geometry.getCoordinates()[0];
			    if (!stateMonitor.get('drawing')) {
				    self.polygonButton.state.set('selected', false);
				    self.polygonInput.value = JSON.stringify(self.polygon.geometry.getCoordinates()[0]);
			    } else {
				    self.polygonInput.value = '';
			    }
		    };
		    stateMonitor.add("drawing", function (newValue) {
			    onUpdatepolygon();
			    self.polygon.options.set("strokeColor", newValue ? '#ff0000' : '#0000FF');
		    });
		    self.polygon.events.add('geometrychange',onUpdatepolygon);
	    };

	    this.initMap = function() {

		    var hasCoords = coordLat && coordLong;

		    self.map = new ymaps.Map(mapNode, {
			    center: [coordLat || 53.902284, coordLong || 27.561831],
			    zoom: hasCoords ? 16 : 9,
			    controls: []
		    });

			//zoom
			self.map.controls.add(new ymaps.control.ZoomControl());

		    //polygon
		    if (self.settings.allow_polygon) {
		    	self.addPolygonButton();
		    	self.addPolygon();
		    }

		    // init mark if has coords
			if (hasCoords) {
				var coords = [coordLat, coordLong];
				if (self.placemark) {
					self.placemark.geometry.setCoordinates(coords);
				} else {
					self.placemark = self.createPlacemark(coords);
					self.map.geoObjects.add(self.placemark);
					self.placemark.events.add('dragend', function () {
						self.getAddress(placemark.geometry.getCoordinates(), true);
					});
				}
				self.placemark.properties.set('iconCaption', mapAddress.innerHTML);
			}

		    //bounds
			if (inputBounds0.value && inputBounds1.value
				&& inputBounds2.value && inputBounds2.value) {
				self.map.setBounds(
					[
						[inputBounds0.value, inputBounds1.value],
						[inputBounds2.value, inputBounds3.value]
					],
					{
						checkZoomRange: false
					}
				);
			}
			if (inputZoom.value && parseInt(inputZoom.value) > 0) {
				self.map.setZoom(inputZoom.value);
			}

			self.map.events.add("boundschange", function (e) {
				var bounds = self.map.getBounds();
				inputBounds0.value = bounds[0][0];
				inputBounds1.value = bounds[0][1];
				inputBounds2.value = bounds[1][0];
				inputBounds3.value = bounds[1][1];
				inputZoom.value = self.map.getZoom();
			});

			//adress form input
			self.initAdressFormInput();
	    };

	    this.createPlacemark = function(coords) {
		    return new ymaps.Placemark(coords, {
			    iconCaption: '...'
		    }, {
			    preset: 'islands#violetDotIconWithCaption',
			    draggable: false,
			    visible: !self.settings.allow_polygon
		    });
	    };

	    this.setPlacemark = function(res) {
		    var firstGeoObject = res.geoObjects.get(0);
		    var address = [
			    firstGeoObject.getLocalities().length ? firstGeoObject.getLocalities() : firstGeoObject.getAdministrativeAreas(),
			    firstGeoObject.getThoroughfare() || firstGeoObject.getPremise()
		    ].filter(Boolean).join(', ');

		    self.placemark.properties.set({
				    iconCaption: address,
				    balloonContent: firstGeoObject.getAddressLine()
			    });

		    mapAddress.innerHTML = firstGeoObject.getAddressLine();
	    };

	    this.arrayWalk = function(obj, callback) {
		    for (var k in obj) {
			    if (typeof obj[k] === "object" && obj[k] !== null) {
				    this.arrayWalk(obj[k], callback);
			    }
			    else {
				    callback(obj, k, obj[k]);
			    }
		    }
	    };

	    this.getAddress = function(coords, useParamCoords) {
		    self.placemark.properties.set('iconCaption', '...');
		    ymaps.geocode(coords).then(function (res) {
			    self.setPlacemark(res);
				self.map.setCenter(coords);
				self.map.setZoom(16);
		    });
		    ymaps.geocode(coords, {
			    json: true,
			    results: 1
		    }).then(function (res) {
			    if ("GeoObjectCollection" in res) {
				    if ("featureMember" in res.GeoObjectCollection) {
					    if (res.GeoObjectCollection.featureMember[0]) {
						    var geoData = res.GeoObjectCollection.featureMember[0].GeoObject;
						    // reset address values in input fields
						    for (var k in addressInputFields) {
							    var input = document.querySelector(".js-citrus-map-address-" + k);
							    if (input) {
								    input.value = "";
							    }
						    }
						    // init address values in input fields
						    self.arrayWalk(geoData, function (obj, k, v) {
							    if (k in addressInputFields) {
								    var input = document.querySelector(
									    ".js-citrus-map-address-" + k);
								    if (input) { // init input value
									    input.value = v;
								    }
							    }
						    });
						    if (useParamCoords === true) {
							    geoData.Point.pos = coords[1] + " " + coords[0];
						    }
						    self.mapObjectInput.value = JSON.stringify(geoData);
					    }
				    }
			    }
		    });
	    };

	    this.updateJsonFromAddress = function() {
		    var geoData;
		    if (self.mapObjectInput.value === ""
			    || self.mapObjectInput.value.substr(0, 1) !== "{"
		    ) {
			    geoData = self.fields;
		    } else {
			    geoData = JSON.parse(self.mapObjectInput.value);
		    }

		    // init geodata values from input fields
		    self.arrayWalk(self.fields, function (obj, k, v) {
			    if (k in addressInputFields) {
				    var input = document.querySelector(
					    ".js-citrus-map-address-" + k);
				    if (input) { // init from input value
					    obj[k] = input.value;
				    }
			    }
		    });
		    self.mapObjectInput.value = JSON.stringify(geoData);
	    };

        this.init = function () {
	        if (coordLat && coordLong) {
		        self.initMap();
	        }else if (mapProperty.getAttribute("data-address")) {
		        self = this;
		        ymaps.geocode(mapProperty.getAttribute("data-address"), {
			        results: 1
		        }).then(function (res) {
			        var object = res.geoObjects.get(0);
			        
					if (object) {
				        [coordLat, coordLong] = object.geometry.getCoordinates();
				        self.initMap();
			        }
		        });
	        } else {
		        self.initMap();
	        }

	        // init update json on change address inputs
	        for (var k in addressInputFields) {
		        var input = document.querySelector(".js-citrus-map-address-" + k);
		        
				if (input) {
			        BX.bind(input, "change", self.updateJsonFromAddress);
		        }
	        }
        };

		this.initAdressFormInput = function(){
			var form = mapProperty.closest('form');
			var adressInputs = form.querySelectorAll('.js-adress-property');

			if(adressInputs.length > 0){
				for (var i = 0 ; i < adressInputs.length; i++) {
					adressInputs[i].addEventListener('change', this.getFullAdress);
				}
			}
		};

		this.getFullAdress = function () {
			var form = mapProperty.closest('form');
			var adressInputs = form.querySelectorAll('.js-adress-property');
			var adress = [];

			if(adressInputs.length){
				for (var i = 0 ; i < adressInputs.length; i++) {
					switch (adressInputs[i].nodeName) {
						case 'INPUT':
							if(adressInputs[i].value.length){
								adress.push(adressInputs[i].value);
							}
							break;
						
						case 'SELECT':
							if(adressInputs[i].value.length){
								adress.push(adressInputs[i].options[adressInputs[i].selectedIndex].text);
							}
							break;
					}
				}
			}

			adress = adress.join(', ');
			
			if(adress.length > 0){
				ymaps.geocode(adress).then(function (res) {
					var firstGeoObject = res.geoObjects.get(0);
					var coords = firstGeoObject.geometry.getCoordinates();

					if (self.placemark) {
						self.placemark.geometry.setCoordinates(coords);
					} else {
						self.placemark = self.createPlacemark(coords);
						self.map.geoObjects.add(self.placemark);
					}

					self.getAddress(coords);
				});
			}
		};

	    ymaps.ready(this.init);
    };
}());

