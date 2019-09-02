$(document).ready(function () {
	if ($("#builder-table").length) {
		var builder = new Vue({
			el: '#builder-table',
			delimiters: ['@[[',']]@'],

			data: {
				showBlock: 'table',
				currentLang: 'ru',
				sectionId: 0,
				existTable: false,

				setHeaders: false,
				selectedTemplate: 0,
				templates: [],

				currentTable: 0,
				names: [],

				massFilling: '',

				settings: {},
				tables: []
			},

			methods: {

				/**
				 * Add col to end table
				 * @param Event
				 */
				addCol: function (e) {
					e.preventDefault();

					var self = this;
					$.each(self.names, function (key, value) {
						self.names[key].push({
							translates: {
								'ru': '',
								'kz': '',
								'en': ''
							},
							head: false
						});
					});
				},

				/**
				 * Deletion last col
				 * @param Event
				 */
				delCol: function (e) {
					e.preventDefault();

					var self = this.names;
					$.each(self, function (key, value) {
						self[key].pop();
					});
				},

				/**
				 * Add row to end table
				 * @param Event
				 */
				addRow: function (e) {
					e.preventDefault();
					var self = this.names;
					var addElementRows = [];

					$.each(self[0], function (key, value) {
						addElementRows.push({
							translates: {
								'ru': '',
								'kz': '',
								'en': ''
							},
							head: false
						});
					});

					self.push(addElementRows);

				},

				/**
				 * Deletion last row
				 * @param Event
				 */
				delRow: function (e) {
					e.preventDefault();

					this.names.pop();
				},

				/**
				 * Update table
				 * @param Event
				 * @return success
				 */
				saveTable: function (e) {
					e.preventDefault();

					var self = this;
					$.ajax({
							url: '/sections/' + this.sectionId + '/builder/' + this.currentTable,
							type: 'PUT',
							async: false,
							dataType: 'json',
							data : {
								_token: $('meta[name="_token"]').attr('content'),
								names: this.names,
								settings: this.settings
							},
							success: function(data) {
								if (data.errors) {
									messageError(data.errors);
								} else {
									messageSuccess(data.success);
									self.existTable = true;

									self.getTables();
								}
							}
					});
				},

				/**
				 * Create new table
				 * @return {[names]}
				 */
				createNewTable: function (e) {
					e.preventDefault();

					for (var i = 0; i < 2; i++) {
						var addElementRows = [];

						for (var j = 0; j < 2; j++) {
							addElementRows.push({
								translates: {
									'ru': '',
									'kz': '',
									'en': ''
								},
								head: false
							});
						}

						this.names.push(addElementRows);
						addElementRows = [];
					}

				},

				createClearTable: function (e) {
					this.currentTable = 0;
					this.names = [];
					this.createNewTable(e);
					this.settings = {};
				},

				/**
				 * Get data for created table
				 * @return this.names
				 */
				getData: function (id) {
					var self = this;

					$.ajax({
							url: '/sections/' + this.sectionId + '/builder/getData' + (id ? '/' + id : ''),
							type: 'POST',
							async: false,
							dataType: 'json',
							data : {
								_token: $('meta[name="_token"]').attr('content')
							},
							success: function(data) {
								if (data.errors) {
									messageError(data.errors);
								} else {
									self.currentTable = data.settings.id;
									self.names = data.names;
									self.settings = data.settings;
								}
							}
					});
				},

				/**
				 * Get all tables in section
				 */
				getTables: function () {
					var self = this;
					$.ajax({
							url: '/sections/' + this.sectionId + '/builder/getTables',
							type: 'POST',
							async: false,
							dataType: 'json',
							data : {
								_token: $('meta[name="_token"]').attr('content')
							},
							success: function(data) {
								if (data.errors) {
									messageError(data.errors);
								} else {
									self.tables = data.tables;
								}
							}
					});
				},

				switchTable: function (id) {
					this.getData(id);
				},

				/**
				 * Get default templates
				 * @return {[array]}
				 */
				getTemplates: function () {
					var self = this;
					$.ajax({
							url: '/constructor',
							type: 'GET',
							async: false,
							dataType: 'json',
							data : {
								_token: $('meta[name="_token"]').attr('content')
							},
							success: function(data) {
								self.templates = data;
							}
					});
				},

				/**
				 * Получение списка шаблонов
				 * @return {[array]}
				 */
				getTemplateData: function (e) {
					e.preventDefault();

					var self = this;
					$.ajax({
							url: '/constructor/getData/' + this.selectedTemplate,
							type: 'POST',
							async: false,
							dataType: 'json',
							data : {
								_token: $('meta[name="_token"]').attr('content')
							},
							success: function(data) {
								if (data.errors) {
									self.names = [];
								} else {
									self.names = data.names;
								}
							}
					});

				},

				/**
				 * Initialize timepicker
				 */
				initTimePicker: function () {
					var self = this;

					$('#timepicker-tab-settings').timepicker({
						showAnim: 'blind',
						hourText: 'Часы',             // Define the locale text for "Hours"
						minuteText: 'Минуты',         // Define the locale text for "Minute"
						amPmText: ['', ''],
						minutes: {
							starts: 0,                // First displayed minute
							ends: 55,                 // Last displayed minute
							interval: 5               // Interval of displayed minutes
						},
						onClose: function (time) {
							self.settings.time = time;
						}
					});
				},

				/**
				 * Initialize datepicker
				 */
				initDatepicker: function () {
					var self = this;

					$("#datepicker-tab-settings").datepicker({
						changeMonth: true,
						changeYear: true,
						dateFormat: 'yy-mm-dd',
						yearRange: "2000:",
						monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
						monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'],
						dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
						dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
						dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
						onClose: function (date) {
							self.settings.date = date;
						}
					});
				},

				readMass: function () {
					var data = this.massFilling;
					var rows = data.split("\n");

					var table = $('<table />');

					for(var y in rows) {
					    var cells = rows[y].split("\t");
					    var row = $('<tr />');
					    for(var x in cells) {
					        row.append('<td>'+cells[x]+'</td>');
					    }
					    table.append(row);
					}

					// Insert into DOM
					$('#excel_table').html(table);
				}
			},

			mounted: function () {
				this.$nextTick(function () {
					this.sectionId = $("#section--id").val();
					this.existTable = $("#exist-table").val();

					this.getTemplates();
					this.getTables();

					if (this.existTable) {
						this.getData();
					}
				});
			},

			updated: function () {
				this.$nextTick(function () {

					if (this.showBlock === 'settings') {
						this.initTimePicker();

						this.initDatepicker();
					}
				})
			}
		});
	}
});
