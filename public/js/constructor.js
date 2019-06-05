$(document).ready(function () {
	if ($("#constructor-table").length) {
		var constructor = new Vue({
			el: '#constructor-table',
			delimiters: ['@[[',']]@'],
			data: {
				currentLang: 'ru',
				tableId: null,
				tableName: '',
				setHeaders: false,
				names: [ ],
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
				addRow: function () {

					var self = this;
					var names = this.names;
					var addElementRows = [];

					$.each(names[0], function (key, value) {
						addElementRows.push({
							translates: {
								'ru': '',
								'kz': '',
								'en': ''
							},
							head: false
						});
					});

					names.push(addElementRows);
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
				 * Save new table
				 * @param Event
				 * @return success
				 */
				createTable: function (e) {
					e.preventDefault();

					$.ajax({
							url: '/constructor',
							type: 'POST',
							async: false,
							dataType: 'json',
							data : {
								_token: $('meta[name="_token"]').attr('content'),
								tableName: this.tableName,
								names: this.names,
							},
							success: function(data) {
								if (data.errors) {
									messageError(data.errors);
								} else {
									messageSuccess(data.success);

									setTimeout(function () {
										window.location.replace(data.redirect);
									}, 3000);
								}
							}
					});

				},

				/**
				 * Update table
				 * @param Event
				 * @return success
				 */
				updateTable: function (e) {
					e.preventDefault();

					var self = this;
					$.ajax({
							url: '/constructor/' + this.tableId,
							type: 'PUT',
							async: false,
							dataType: 'json',
							data : {
								_token: $('meta[name="_token"]').attr('content'),
								tableName: this.tableName,
								names: this.names,
							},
							success: function(data) {
								if (data.errors) {
									messageError(data.errors);
								} else {
									messageSuccess(data.success);
								}
							}
					});
				},

				/**
				 * Get data for created table
				 * @return this.names
				 */
				getData: function () {
					var self = this;
					$.ajax({
							url: '/constructor/getData/' + this.tableId,
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
									self.tableName = data.tableName;
									self.names = data.names;
								}
							}
					});
				},

				setDefaultNames: function () {
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
				}

			},

			mounted: function () {
				this.$nextTick(function () {
					this.tableId = $("#tableId").val();

					if (this.tableId) {
						this.getData();
					}

					if (this.names.length == 0) {
						this.setDefaultNames();
					}

				});
			},

			updated: function () {
				this.$nextTick(function () {
					if (this.names.length) {
						var table = $('#constructor-table-table')[0];
						var hasHorizontalScrollbar = table.scrollWidth > table.clientWidth;

						if (hasHorizontalScrollbar) {
							$('#constructor-table-table').css({'padding-bottom': '20px'});
							$('.constructor-table__btn--row').css({'bottom': '29px'});
						}
					}
				})
			}
		});
	}

	if ($(".remove--table").length) {
		$("body").on('click', '.remove--table', function (e) {
			e.preventDefault();
			var id = $(this).attr('data-id');

			$.ajax({
					url: '/constructor/' + id,
					type: 'DELETE',
					async: false,
					dataType: 'json',
					data : { _token: $('meta[name="_token"]').attr('content')},
					success: function(data) {
						if (data.success) {
							$("#table--item-" + id).remove();
							messageSuccess(data.success);
						} else {
							messageError(data.errors);
						}
					}
			});
		});
	}

});
