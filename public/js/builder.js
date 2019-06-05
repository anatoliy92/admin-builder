$(document).ready(function () {
	if ($("#builder-table").length) {
		var builder = new Vue({
			el: '#builder-table',
			delimiters: ['@[[',']]@'],
			data: {
				currentLang: 'ru',
				sectionId: 0,
				existTable: false,

				setHeaders: false,
				selectedTemplate: 0,
				templates: [],

				names: []
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
							url: '/sections/' + this.sectionId + '/builder/' + this.sectionId,
							type: 'PUT',
							async: false,
							dataType: 'json',
							data : {
								_token: $('meta[name="_token"]').attr('content'),
								names: this.names,
							},
							success: function(data) {
								if (data.errors) {
									messageError(data.errors);
								} else {
									messageSuccess(data.success);
									self.existTable = true;
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

				/**
				 * Get data for created table
				 * @return this.names
				 */
				getData: function () {
					var self = this;
					$.ajax({
							url: '/sections/' + this.sectionId + '/builder/getData',
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
									self.names = data.names;
								}
							}
					});
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
									// messageError(data.errors);
								} else {
									self.names = data.names;
								}
							}
					});

				}

			},

			mounted: function () {
				this.$nextTick(function () {
					this.sectionId = $("#section--id").val();
					this.existTable = $("#exist-table").val();

					this.getTemplates();

					if (this.existTable) {
						this.getData();
					}
				});
			},

			updated: function () {
				// this.$nextTick(function () {
				// 	if (this.names.length) {
				// 		var table = $('#constructor-table-table')[0];
				// 		var hasHorizontalScrollbar = table.scrollWidth > table.clientWidth;
				//
				// 		if (hasHorizontalScrollbar) {
				// 			$('#constructor-table-table').css({'padding-bottom': '20px'});
				// 			$('.constructor-table__btn--row').css({'bottom': '29px'});
				// 		}
				// 	}
				// })
			}
		});
	}

});
