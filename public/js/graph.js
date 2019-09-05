Vue.component('graph-component', {
	props: ['graph', 'id'],
	data () {
		return {
			chart: null,
			errors: null,
			chartLabels: [],
			chartDatasets: []
		}
	},
	methods: {

		getDataGraph () {
			if (this.id > 0) {
				var self = this;
				$.ajax({
					url: '/builder/get-graph/' + self.id,
					type: 'POST',
					async: false,
					dataType: 'json',
					data : {
						_token: $('meta[name="_token"]').attr('content'),
						graph: self.graph
					},
					success: function(data) {
						if (data.errors) {
							self.errors = data.errors
						}
						if (data.success) {
							self.errors = null;
							self.chartLabels = data.labels;
							self.chartDatasets = data.datasets;
						}
					}
				});
			}

		},

		reinit: function () {
			var self = this;

			if (self.chart) { self.chart.destroy(); }

			self.getDataGraph();

			if (!self.errors) {
				var ctx = document.getElementById('myChart');

				self.chart = new Chart(ctx, {
					type: this.graph.type,
					data: {
						labels: self.chartLabels,
						datasets: self.chartDatasets
					},
					options: {
						scales: {
							yAxes: [{
								ticks: {
									beginAtZero: true
								}
							}]
						}
					}
				});
			}
		}
	},

	mounted: function () {
		this.reinit();
	},

	updated: function () {
		this.$nextTick(function () {
			this.reinit();
		})
	},

	template:
	 		'<div class="row mt-3">' +
				'<div class="col-12">' +
					'<div class="alert alert-danger" v-if="errors">{{ errors }}</div>' +
					'<canvas v-else id="myChart" height="100">{{ graph }}</canvas>' +
				'</div>' +
			'</div>'


});
