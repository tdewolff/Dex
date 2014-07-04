<style>
html, body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 85%;
	margin: 0;
	background-color: white;
	height: 100%;
}

svg {
	width: 100%;
	height: 100%;
}

.axis path,
.axis line {
	fill: none;
	stroke: #000;
	shape-rendering: crispEdges;
}

.y.axis line {
	stroke: #fff;
}

.x.axis line {
	stroke: #fff;
}

path.visits {
	fill: none;
	stroke-width: 1.5px;
}

path.unique {
	fill: none;
	stroke-width: 2.5px;
}

path.area {
	opacity: 0.1;
	stroke-width: 0;
}

circle {
	fill: white;
	stroke-width: 1.5px;
}

circle.unique {
	stroke-width: 2.5px;
}

.empty {
	text-anchor: middle;
	font-size: 12px;
	font-style: italic;
	fill: #D0D0D0;
}
</style>

<script src="http://d3js.org/d3.v3.min.js"></script>
<script>
	function drawStats(visits) {
		if (visits) {
			visits.forEach(function (d) {
				d.date = d.date * 1000;
				d.visits = +d.visits;
				d.unique_visits = +d.unique_visits;
			});

			var h = d3.max(visits, function (d) { return d.visits; });
			var hLog = (h > 0 ? Math.floor(Math.log(h) / Math.log(10)) : 0);

			var margin = {top: 10, right: 75, bottom: 55, left: 20 + hLog * 8},
				width = parseInt(d3.select('body').style('width')) - margin.left - margin.right,
				height = 250 - margin.top - margin.bottom;

			var format = d3.locale({
				'decimal': '<?php echo __('.'); ?>',
				'thousands': '<?php echo __(','); ?>',
				'grouping': [3],
				'currency': ['$', ''],
				'dateTime': '%a %b %e %X %Y',
				'date': '%m/%d/%Y',
				'time': '%H:%M:%S',
				'periods': ['AM', 'PM'],
				'days': ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
				'shortDays': ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
				'months': ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
				'shortMonths': <?php echo __("['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']"); ?>
			});

			var xDomain = d3.extent(visits, function (d) { return d.date; });
			var x = d3.time.scale.utc().range([0, width]).domain(xDomain);
			var xAxis = d3.svg.axis()
				.scale(x)
				.orient('bottom')
				.ticks(d3.time.days.utc, 3)
				.tickSize(-height + 1)
				.tickFormat(format.timeFormat('%e %b'));

			var yDomain = (h > 10 ? [0, d3.round(h * 1.10)] : [0, h]);
			var y = d3.scale.linear().range([height, 0]).domain(yDomain);
			var yAxis = d3.svg.axis()
				.scale(y)
				.orient('left')
				.ticks(Math.min(h, 10))
				.tickSize(-width - 1)
				.tickPadding(8)
				.tickFormat(format.numberFormat('n'));
			if (visits.length < 2) {
				yAxis.ticks(1);
			}

			var svg = d3.select('body')
				.append('svg')
				.append('g')
					.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');

			var color = d3.scale.category10();

			var line = d3.svg.line()
				.interpolate('cardinal')
				.x(function (d) { return x(d.date); })
				.y(function (d) { return y(d.visits); });

			var line_unique = d3.svg.line()
				.interpolate('cardinal')
				.x(function (d) { return x(d.date); })
				.y(function (d) { return y(d.unique_visits); });

			var area = d3.svg.area()
				.interpolate('cardinal')
				.x(function (d) { return x(d.date); })
				.y0(height)
				.y1(function (d) { return y(d.unique_visits); });

			// fill
			svg.append('path')
				.attr('class', 'area')
				.attr('d', area(visits))
				.style('fill', function(d) { return color(1); });

			// y axis
			svg.append('g')
				.attr('class', 'y axis')
				.call(yAxis);

			// x axis
			svg.append('g')
				.attr('class', 'x axis')
				.attr('transform', 'translate(0,' + height + ')')
				.call(xAxis)
			.selectAll('text')
				.style("text-anchor", "end")
				.attr("dx", "-.8em")
				.attr("dy", ".15em")
				.attr("transform", function(d) {
					return "rotate(-60)"
				});

			// y axis text
			svg.append('text')
				.attr('transform', 'rotate(-90)')
				.attr('x', -5)
				.attr('y', 6)
				.attr('dy', '.71em')
				.style('text-anchor', 'end')
				.text('<?php echo __('Visits'); ?>');

			if (visits.length > 1) {
				// paths and points
				svg.append('path')
					.attr('class', 'unique')
					.attr('d', line_unique(visits))
					.style('stroke', function(d) { return color(1); });

				svg.append('path')
					.attr('class', 'visits')
					.attr('d', line(visits))
					.style('stroke', function(d) { return color(0); });

				svg.selectAll('dot')
					.data(visits)
				.enter().append('circle')
					.attr('class', 'unique')
					.attr('r', 3)
					.attr('cx', function(d) { return x(d.date); })
					.attr('cy', function(d) { return y(d.unique_visits); })
					.style('stroke', function(d) { return color(1); });

				svg.selectAll('dot')
					.data(visits)
				.enter().append('circle')
					.attr('class', 'visits')
					.attr('r', 2.5)
					.attr('cx', function(d) { return x(d.date); })
					.attr('cy', function(d) { return y(d.visits); })
					.style('stroke', function(d) { return color(0); });
			}

			// legend
			svg.append('circle')
				.attr('class', 'legend')
				.attr('r', 2.5)
				.attr('cx', width + 15)
				.attr('cy', 10)
				.style('stroke', function(d) { return color(0); });

			svg.append('text')
				.attr('class', 'legend')
				.attr('transform', 'translate(' + width + ',0)')
				.attr('x', 25)
				.attr('y', 10)
				.attr('dy', '.35em')
				.text('<?php echo __('Total'); ?>');

			svg.append('circle')
				.attr('class', 'legend')
				.attr('r', 2.5)
				.attr('cx', width + 15)
				.attr('cy', 30)
				.style('stroke', function(d) { return color(1); });

			svg.append('text')
				.attr('class', 'legend')
				.attr('transform', 'translate(' + width + ',0)')
				.attr('x', 25)
				.attr('y', 30)
				.attr('dy', '.35em')
				.text('<?php echo __('Unique'); ?>');

			if (visits.length < 2) {
				svg.append('text')
					.attr('class', 'empty')
					.attr('x', width / 2)
					.attr('y', height / 2)
					.attr('class', 'empty')
					.text('<?php echo __('empty'); ?>');
			}

			d3.select(window).on('resize', function () {
				var newWidth = parseInt(d3.select('body').style('width')) - margin.left - margin.right;
				if (newWidth !== width) {
					width = newWidth;

					x.range([0, width]);
					xAxis.ticks(Math.min(Math.max(width / 80, 2), d3.time.days(xDomain[0], xDomain[1]).length));
					svg.select('.x').call(xAxis)
						.selectAll('text').attr('y', '1em');

					yAxis.tickSize(-width);
					svg.select('.y').call(yAxis);

					svg.select('path.area').attr('d', area(visits));

					svg.select('path.visits').attr('d', line(visits));
					svg.selectAll('circle.visits').attr('cx', function(d) { return x(d.date); });
					svg.select('path.unique').attr('d', line_unique(visits));
					svg.selectAll('circle.unique').attr('cx', function(d) { return x(d.date); });

					svg.selectAll('circle.legend').attr('cx', width + 15);
					svg.selectAll('text.legend').attr('transform', 'translate(' + width + ',0)');
					svg.select('text.empty').attr('x', width / 2);
				}
			});
		}
	}
</script>