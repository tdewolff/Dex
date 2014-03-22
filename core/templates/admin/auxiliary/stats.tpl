<style>
html, body {
	font-family:Arial, Helvetica, sans-serif;
	font-size:85%;
}

.axis path,
.axis line {
	fill: none;
	stroke: #000;
	shape-rendering: crispEdges;
}

.x.axis line {
	stroke: #fff;
}

path.line {
	fill: none;
	stroke-width: 1.5px;
}

path.area {
	opacity: 0.1;
	stroke-width: 0;
}
</style>

<script src="http://d3js.org/d3.v3.min.js"></script>
<script>
	function drawStats(visits) {
		var margin = {top: 10, right: 50, bottom: 30, left: 40},
			width = 900 - margin.left - margin.right,
			height = 200 - margin.top - margin.bottom;

		var x = d3.time.scale().range([0, width]);
		var y = d3.scale.linear().range([height, 0]);
		var color = d3.scale.category10();

		var xAxis = d3.svg.axis()
			.scale(x)
			.orient('bottom')
			.tickSize(-height)
			.tickFormat(d3.time.format('%b %e'));

		var yAxis = d3.svg.axis()
			.scale(y)
			.orient('left')
			.ticks(5);

		var line = d3.svg.line()
    		.interpolate('monotone')
			.x(function (d) { return x(d.date); })
			.y(function (d) { return y(d.visits); });

		var line_unique = d3.svg.line()
    		.interpolate('monotone')
			.x(function (d) { return x(d.date); })
			.y(function (d) { return y(d.unique_visits); });

		var area = d3.svg.area()
			.x(function (d) { return x(d.date); })
			.y0(height)
			.y1(function (d) { return y(d.unique_visits); });

		var svg = d3.select('body')
			.append('svg')
				.attr('width', width + margin.left + margin.right)
				.attr('height', height + margin.top + margin.bottom)
			.append('g')
				.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');

		if (visits) {
			visits.forEach(function (d) {
				d.date = d.date * 1000;
				d.visits = +d.visits;
				d.unique_visits = +d.unique_visits;
			});

			x.domain(d3.extent(visits, function (d) { return d.date; }));
			y.domain([0, d3.max(visits, function (d) { return d.visits; })]);

			svg.append('path')
				.attr('class', 'area')
				.attr('d', area(visits))
  				.style('fill', function(d) { return color(1); });

			svg.append('g')
				.attr('class', 'x axis')
				.attr('transform', 'translate(0,' + height + ')')
				.call(xAxis)
    		.selectAll('text')
    			.attr('y', '1em');

			svg.append('path')
				.attr('class', 'line')
				.attr('d', line(visits))
  				.style('stroke', function(d) { return color(0); });

			svg.selectAll('dot')
				.data(visits)
			.enter().append('circle')
				.attr('r', 2.5)
				.attr('cx', function(d) { return x(d.date); })
				.attr('cy', function(d) { return y(d.visits); })
  				.style('stroke', function(d) { return color(0); });

			svg.append('path')
				.attr('class', 'line unique')
				.attr('d', line_unique(visits))
  				.style('stroke', function(d) { return color(1); });

			svg.selectAll('dot')
				.data(visits)
			.enter().append('circle')
				.attr('r', 2.5)
				.attr('cx', function(d) { return x(d.date); })
				.attr('cy', function(d) { return y(d.unique_visits); })
  				.style('stroke', function(d) { return color(1); });

			svg.append('g')
				.attr('class', 'y axis')
				.call(yAxis)
			.append('text')
				.attr('transform', 'rotate(-90)')
				.attr('y', 6)
				.attr('dy', '.71em')
				.style('text-anchor', 'end')
				.text('Visits');

			if (visits.length > 1) {
				if (visits[visits.length - 1].visits != visits[visits.length - 1].unique_visits) {
					svg.append('text')
						.attr('transform', 'translate(' + x(visits[visits.length - 1].date) + ',' + y(visits[visits.length - 1].visits) + ')')
						.attr('x', 5)
						.attr('dy', '.4em')
						.text('Total');
				}

				svg.append('text')
					.attr('transform', 'translate(' + x(visits[visits.length - 1].date) + ',' + y(visits[visits.length - 1].unique_visits) + ')')
					.attr('x', 5)
					.attr('dy', '.4em')
					.text('Unique');
			}
		}
	}
</script>