var cols, rows, isDraggedBetweenCells = !1,
	isMouseDown = !1,
	mouseDownCell, selectedRowspan, selectedColspan,
	builder = {}; // экземпляр объекта vue

$(document).ready(function () {
	// if ($("#builder-table").length) {
		builder = new Vue({
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
				heads: [],

				settings: {},
				tables: [],

				massFilling: '',
				activeCell: [0, 0],   // координаты текущей ячейки
				showPanelMass: false, // показать/скрыть панель быстрой вставки

				selectedCells: [],

				mergedCells: {},

				graph: []
			},

			methods: {
				selectCell (y, x) {
					// если была выделена данная ячейка
					var index = this.checkExistCoordinate(this.selectedCells, y, x);
					if (index >= 0) {
						this.selectedCells.splice(index, 1);
					} else {
						this.selectedCells.push([y, x]);
					}
				},

				setActiveCell: function (y, x) {
					this.activeCell = [];
					this.activeCell.push(y);
					this.activeCell.push(x);
					this.mergedCells = {};
				},

				isActiveCell (y, x) {
					if ((this.activeCell[0] === y) && (this.activeCell[1] === x)) {
						return true;
					}
					return false;
				},

				/**
				 * Add col to end table
				 * @param Event
				 */
				addCol: function (indexCol, e) {
					e.preventDefault();
					$.each(this.names, function (key, value) {
                        value.splice(indexCol, 0, {
                            translates: { 'ru': '', 'kz': '', 'en': '' },
                            head: false,
							hide: 0
                        });
					});
				},

				/**
				 * Deletion last col
				 * @param Event
				 */
				delCol: function (indexCol, e) {
					e.preventDefault();
					$.each(this.names, function (key, value) {
						value.splice(indexCol, 1);
					});
				},

				/**
				 * Add row to end table
				 * @param Event
				 */
				addRow: function (indexRow, e) {
					e.preventDefault();
					var addElementRows = [];
					$.each(this.names[0], function (key, value) {
						addElementRows.push({
							translates: { 'ru': '', 'kz': '', 'en': '' },
							head: false,
							hide: 0
						});
					});
					this.names.splice(indexRow, 0, addElementRows);
				},

				/**
				 * Deletion last row
				 * @param Event
				 */
				delRow: function (indexRow, e) {
					e.preventDefault();
					this.names.splice(indexRow, 1);
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
								settings: this.settings,
								graph: this.graph
							},
							success: function(data) {
								if (data.errors) {
									messageError(data.errors);
								} else {
									messageSuccess(data.success);
									self.existTable = true;
									self.currentTable = data.table.id;
									self.heads = data.heads;
									self.graph = data.graph;

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
					this.heads = [];
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
                                self.heads = data.heads;
                                self.names = data.names;
                                self.settings = data.settings;
                                self.graph = data.graph;
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

				/**
				 * Remove table and all table values
				 */
				removeTable: function (id) {
					var self = this;
					var $confirm = confirm('Вы действительно желаете удалить таблицу?');
					if ($confirm) {
						$.ajax({
							url: '/sections/' + this.sectionId + '/builder/' + id,
							type: 'DELETE',
							async: false,
							dataType: 'json',
							data : {
								_token: $('meta[name="_token"]').attr('content')
							},
							success: function(data) {
								if (data.success) {
									messageSuccess(data.success);
									self.getTables();
									self.currentTable = 0;
									self.heads = [];
									self.names = [];
									self.settings = {};
								}
								if (data.errors) {
									messageError(data.errors);
								}
							}
						});
					}
				},

				readMass: function (e) {
					var self = this;
					var table = getTableFromExcell(this.massFilling);

					if (table.length > 0) {
						$.each(table, function (y, row) {
							var insertRow = self.activeCell[0] + y;

							$.each(row, function (x, cell) {
								var insertCell = self.activeCell[1] + x;

								// if there is no line, then add
								if (!self.names[insertRow]) { self.addRow(e); }

								// if the row and cell exist, then fill it
								if (self.names[insertRow] && self.names[insertRow][insertCell]) {
									if (window.sharedData.langs) {
										$.each(window.sharedData.langs, function (key, lang) {
											self.names[insertRow][insertCell]['translates'][lang.key] = cell;
										});
									}
								}
							});
						});
					}
					self.massFilling = '';
					self.activeCell = [0, 0];
				},

				checkExistCoordinate (axis, x, y) {
					var index = -1;

					if (Array.isArray(axis[0])) {
						$.each(axis, function (key, value) {
							if ((value[0] == x) && (value[1] == y)) {
								index = key;
							}
						});
					} else {
						if ((axis[0] == x) && (axis[1] == y)) {
							index = 0;
						}
					}

					return index;
				},

				setXCoordinate (x, y) {
					var grapColsX = this.graph.cols.x;

					if (grapColsX.length == 0) {
							grapColsX.push(x);
							grapColsX.push(y);
					} else {
						if ((grapColsX[0] == x) && (grapColsX[1] == y)) {
							this.graph.cols.x = [];
						}
					}
				},

				setYCoordinate (x, y) {
					var graphColsY = this.graph.cols.y;

					var index = this.checkExistCoordinate(graphColsY, x, y);

					if (index >= 0) {
						graphColsY.splice(index, 1);
					} else {
						graphColsY.push([x, y]);
					}
				},

                /**
                 * Get char by iteration index
                 * @param item
                 * @returns char in upper
                 */
                getCharByCode (item) {
				    let char = (item >= 26 ? this.getCharByCode((item / 26 >> 0) - 1) : '') +  'abcdefghijklmnopqrstuvwxyz'[item % 26 >> 0];
				    return char.toUpperCase();
                },

                mergeCell (e) {
                    e.preventDefault();
					let self = this;

                    if (self.mergedCells.merged) {
						let mergedCell = self.mergedCells.merged[0];
						let currentCell = self.names[mergedCell[0]][mergedCell[1]];

						if (self.mergedCells.rowspan) {
							for (let r = mergedCell[0]; r < (mergedCell[0] + self.mergedCells.rowspan); r++) {
								for (let c = mergedCell[1]; c < (mergedCell[1] + self.mergedCells.colspan); c++) {
									if (mergedCell[0] === r) {
										if (mergedCell[1] !== c) {
											self.names[r][c]['hide'] = 1;
										}
									} else {
										self.names[r][c]['hide'] = 1;
									}
								}
							}
						}

						currentCell.colspan = self.mergedCells.colspan;
						currentCell.rowspan = self.mergedCells.rowspan;

						// устанавливаем объединенную ячейку активной
						self.activeCell = [mergedCell[0], mergedCell[1]];
					}
                },

				isCellMerged (y, x) {
                	if (this.mergedCells.merged) {
						let index = this.checkExistCoordinate(this.mergedCells.merged, y, x);
                		return index >= 0;
					}
                	return false;
				},

				/**
				 * Проеряем является ли текущая выделенная ячейка объедененной
				 * @returns boolean
				 */
				checkCellIsMerged () {
					if (this.activeCell.length && this.names.length) {

						let cell = this.names[this.activeCell[0]][this.activeCell[1]];

						if (cell) {
							return (cell.rowspan !== null) && (cell.colspan !== null);
						}
					}
					return false;
				},

				/**
				 * разлипляем объединенную ячейку
				 */
				clearMergeCell () {
					let self = this;
					if (self.checkCellIsMerged()) {
						let currentCell = self.names[self.activeCell[0]][self.activeCell[1]];

						for (let r = self.activeCell[0]; r < (self.activeCell[0] + currentCell.rowspan); r++) {
							for (let c = self.activeCell[1]; c < (self.activeCell[1] + currentCell.colspan); c++) {
								if (self.activeCell[0] === r) {
									if (self.activeCell[1] !== c) {
										self.names[r][c]['hide'] = 0;
									}
								} else {
									self.names[r][c]['hide'] = 0;
								}
							}
						}
						currentCell.colspan = null;
						currentCell.rowspan = null;
					}
				},

				changeInputValue (indexRow, indexCol, langKey, e) {
					this.names[indexRow][indexCol]['translates'][langKey] = e.target.innerHTML;
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

					$("#builder-table .card-body-content").removeClass('d-none');
				});
			},

			updated: function () {
				this.$nextTick(function () {
					if (this.showBlock === 'settings') {
						this.initTimePicker();

						this.initDatepicker();
					}
				});
			}
		});
	// }
});

function getTableFromExcell (excelTable) {
	var table = [],
			data = excelTable,
			rows = data.split("\n");
	for(var y in rows) {
		table[y] = [];
		var cells = rows[y].split("\t");
		for(var x in cells) {
			table[y][x] = cells[x];
		}
	}
	return table;
}

// for merged cell

function getCellRows(a) {
	return getCellValue(a, "r")
}

function getCellCols(a) {
	return getCellValue(a, "c");
}

function getCellValue(a, e) {
	var b = $(a).attr("class");
		b = b.split(" ");
	let allClassesLength = b.length;
	for (var c = 0; c < allClassesLength; c++) {
        b[c].charAt(0) === e ? b[c] = parseInt(b[c].substr(1, b[c].length - 1), 10) : (b.splice(c, 1), allClassesLength--, c--);
    }
	return b;
}

function selectCells(a, e) {
	var mCell = [];
	for (var b = getCellCols(a), c = getCellRows(a), g = getCellCols(e), f = getCellRows(e), i = b.length, j = c.length, h = g.length, o = f.length, k = 100, l = 0, m = 100, n = 0, d = 0; d < i; d++) {
        b[d] < k && (k = b[d]), b[d] > l && (l = b[d]);
    }
	for (d = 0; d < h; d++) { g[d] < k && (k = g[d]), g[d] > l && (l = g[d]); }
	for (d = 0; d < j; d++) { c[d] < m && (m = c[d]), c[d] > n && (n = c[d]); }
	for (d = 0; d < o; d++) { f[d] < m && (m = f[d]), f[d] > n && (n = f[d]); }
	for (d = m; d <= n; d++) {
        for (c = k; c <= l; c++) {
			mCell.push([d, c]);
        }
    }
	do {
		b = !1;
		f = $(".merge-cells");
		i = f.length;
		g = [];
		c = [];
		for (d = 0; d < i; d++) g = g.concat(getCellCols(f.eq(d))),
			c = c.concat(getCellRows(f.eq(d)));
		d = Math.max.apply(Math, g);
		g = Math.min.apply(Math, g);
		f = Math.max.apply(Math, c);
		c = Math.min.apply(Math, c);
		d > l && (l = d, b = !0);
		g < k && (k = g, b = !0);
		f > n && (n = f, b = !0);
		c < m && (m = c, b = !0);

		if (!b) {
			selectedColspan = l - k + 1;
			selectedRowspan = n - m + 1;
		}
	} while (b)

	builder.mergedCells = {
		merged: mCell,
		colspan: selectedColspan,
		rowspan: selectedRowspan
	};
	builder.activeCell = [];
}

$(function () {
	$(document).on("mousedown", "td", function(a) {
		1 === a.which && (isMouseDown = !0, mouseDownCell = this)
	});
	$(document).on("mousemove", "td", function(a) {
		isMouseDown && mouseDownCell != this && (isDraggedBetweenCells = !0, selectCells(mouseDownCell, this))
	});
	$(document).on("mouseup", function() {
		isMouseDown && (isMouseDown = !1, mouseDownCell = void 0, isDraggedBetweenCells = !1);
	});
});