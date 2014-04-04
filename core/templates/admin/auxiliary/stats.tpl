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

.y.axis line {
	stroke: #fff;
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

circle {
	fill: white;
	stroke-width: 1.5px;
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
			var margin = {top: 10, right: 80, bottom: 30, left: 40},
				width = 900 - margin.left - margin.right,
				height = 200 - margin.top - margin.bottom;

			var x = d3.time.scale.utc().range([0, width]);
			var y = d3.scale.linear().range([height, 0]);
			var color = d3.scale.category10();

			visits.forEach(function (d) {
				d.date = d.date * 1000;
				d.visits = +d.visits;
				d.unique_visits = +d.unique_visits;
			});

			var x_domain = d3.extent(visits, function (d) { return d.date; });
			x.domain(x_domain);
			y.domain([0, d3.max(visits, function (d) { return d.visits; }) * 1.10]);

			var xAxis = d3.svg.axis()
				.scale(x)
				.orient('bottom')
				.tickSize(-height)
	    		.ticks(d3.time.days(x_domain[0], x_domain[1]).length)
				.tickFormat(d3.time.format('%b %e'));

			var yAxis = d3.svg.axis()
				.scale(y)
				.orient('left')
				.tickSize(-width);

			var line = d3.svg.line()
	    		.interpolate('monotone')
				.x(function (d) { return x(d.date); })
				.y(function (d) { return y(d.visits); });

			var line_unique = d3.svg.line()
	    		.interpolate('monotone')
				.x(function (d) { return x(d.date); })
				.y(function (d) { return y(d.unique_visits); });

			var area = d3.svg.area()
	    		.interpolate('monotone')
				.x(function (d) { return x(d.date); })
				.y0(height)
				.y1(function (d) { return y(d.unique_visits); });

			var svg = d3.select('body')
				.append('svg')
					.attr('width', width + margin.left + margin.right)
					.attr('height', height + margin.top + margin.bottom)
				.append('g')
					.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');

			// axis
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

  			// axis text
			svg.append('g')
				.attr('class', 'y axis')
				.call(yAxis)
			.append('text')
				.attr('transform', 'rotate(-90)')
				.attr('x', -5)
				.attr('y', 6)
				.attr('dy', '.71em')
				.style('text-anchor', 'end')
				.text('Visits');

    		// paths and points
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

  			// legend
			svg.append('circle')
				.attr('r', 2.5)
				.attr('cx', width + 15)
				.attr('cy', 10)
  				.style('stroke', function(d) { return color(0); });

			svg.append('text')
				.attr('transform', 'translate(' + width + ',10)')
				.attr('x', 25)
				.attr('dy', '.35em')
				.text('Total');

			svg.append('circle')
				.attr('r', 2.5)
				.attr('cx', width + 15)
				.attr('cy', 30)
  				.style('stroke', function(d) { return color(1); });

			svg.append('text')
				.attr('transform', 'translate(' + width + ',30)')
				.attr('x', 25)
				.attr('dy', '.35em')
				.text('Unique');

			if (!visits.length) {
				svg.append('text')
					.attr('x', width / 2)
					.attr('y', height / 2)
					.attr('class', 'empty')
					.text('No visitor data yet');
			}
		}
	}
</script>