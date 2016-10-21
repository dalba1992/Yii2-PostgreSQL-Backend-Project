<?php
/**
 * @var $this yii\web\View
 */

use app\models\CustomerSubscriptionOrder;
use app\widgets\chart\DonutChart;
?>
<div class="widget-box">
    <div class="widget-title">
        <span class="icon"><i class="fa fa-area-chart"></i></span><h5>Charts</h5>
    </div>
    <div class="widget-content">
        <div class="row">
            <div class="col-md-6">
                <div id="chart-grant-total">
                    <h3>Grand Total</h3>
                </div>
                <?php

                echo DonutChart::widget([
                    'dataSet' => [
                        ['label' => 'Active', 'count' => 10],
                        ['label' => 'Draft', 'count' => 20],
                        ['label' => 'Finalized', 'count' => 30],
                        ['label' => 'Fulfilled', 'count' => 40],
                    ],
                    'clientOptions' => [
                        'width' => 500,
                        'height' => 250
                    ]
                ]);

                $this->registerJs("
						(function(d3) {
					        'use strict';

					        var dataset = [
					          	{ label: 'Active', count: ".($totalOrders > 0 ? number_format(($activeOrders / $totalOrders * 100), 1) : 0)." },
					          	{ label: 'Draft', count: ".($totalOrders > 0 ? number_format(($draftOrders / $totalOrders * 100), 1) : 0)." },
					          	{ label: 'Finalized', count: ".($totalOrders > 0 ? number_format(($finalizedOrders / $totalOrders * 100), 1) : 0)." },
					          	{ label: 'Fulfilled', count: ".($totalOrders > 0 ? number_format(($fulfilledOrders / $totalOrders * 100), 1) : 0)." },
					          	{ label: 'Returned', count: ".($totalOrders > 0 ? number_format(($returnedOrders / $totalOrders * 100), 1) : 0)." },
					          	{ label: 'Paid', count: ".($totalOrders > 0 ? number_format(($paidOrders / $totalOrders * 100), 1) : 0)." },
					          	{ label: 'Unpaid', count: ".($totalOrders > 0 ? number_format(($unpaidOrders / $totalOrders * 100), 1) : 0)." },
					          	{ label: 'Sent', count: ".($totalOrders > 0 ? number_format(($sentOrders / $totalOrders * 100), 1) : 0)." },
					        ];

					        var width = 350;
					        var height = 350;
					        var radius = Math.min(width, height) / 2;
					        var donutWidth = 75;
					        var legendRectSize = 18;
        					var legendSpacing = 4;

					        var color = d3.scale.ordinal()
  									.range(['rgb(51, 102, 204)', 'rgb(220, 57, 18)', 'rgb(153, 0, 153)', 'rgb(16, 150, 24)', 'rgb(22,152,175)', 'rgb(78,55,142)', 'rgb(106,102,87)', 'rgb(149,155,44)']);

					        var svg = d3.select('#chart-grant-total')
					          	.append('svg')
					          	.attr('width', width)
					          	.attr('height', height)
					          	.append('g')
					          	.attr('transform', 'translate(' + (width / 2) +
					            	',' + (height / 2) + ')');

					        var arc = d3.svg.arc()
					          	.innerRadius(radius - donutWidth)
					          	.outerRadius(radius);

					        var pie = d3.layout.pie()
					          	.value(function(d) { return d.count; })
					          	.sort(null);

					        var g = svg.selectAll('.arc')
						    		.data(pie(dataset))
								    .enter()
								    .append('g')
								    .attr('class', 'arc')
								    .on('click', function(d, i) {

								    });

							g.append('path')
							  	.attr('d', arc)
							  	.attr('fill', function(d, i) {
							    	return color(i);
							  	});

							g.append('text')
						  		.attr('transform', function(d) {
							    	return 'translate(' + arc.centroid(d) + ')';
							  	})
							  	.attr('dy', '.35em')
							  	.style('text-anchor', 'middle')
							  	.attr('fill', '#fff')
							  	.text(function(d,i) { return dataset[i].count + '%'; })

							var legend = svg.selectAll('.legend')
					          		.data(color.domain())
				          			.enter()
				          			.append('g')
					          		.attr('class', 'legend')
					          		.attr('transform', function(d, i) {
					            		var height = legendRectSize + legendSpacing;
					            		var offset =  height * color.domain().length / 2;
					            		var horz = -2 * legendRectSize;
				            			var vert = i * height - offset;
				            			return 'translate(' + horz + ',' + vert + ')';
					          		});

					        legend.append('rect')
					          	.attr('width', legendRectSize)
					          	.attr('height', legendRectSize)
					          	.style('fill', color)
					          	.style('stroke', color);

					        legend.append('text')
					          	.attr('x', legendRectSize + legendSpacing)
					          	.attr('y', legendRectSize - legendSpacing)
					          	.text(function(d, i) { return dataset[i].label; });

					      	})(window.d3);
						");
                ?>
            </div>

            <div class="col-md-6">
                <div id="sales-week">
                    <h3>Sales Week (<?php echo date('m/d/Y', strtotime($monday)); ?> ~ <?php echo date('m/d/Y', strtotime($sunday)); ?>)</h3>
                </div>
                <?php
                $this->registerJs('
						var margin = {top: 40, right: 20, bottom: 30, left: 40},
					    width = 460 - margin.left - margin.right,
					    height = 400 - margin.top - margin.bottom;

						data = [
						        {"letter":"Mon", "frequency":'.$stats[0].'},
						        {"letter":"Tue", "frequency":'.$stats[1].'},
						        {"letter":"Web", "frequency":'.$stats[2].'},
						        {"letter":"Thu", "frequency":'.$stats[3].'},
						        {"letter":"Fri", "frequency":'.$stats[4].'},
						        {"letter":"Sat", "frequency":'.$stats[5].'},
						        {"letter":"Sun", "frequency":'.$stats[6].'},

						      ]

						var formatPercent = d3.format(".0%");

						var x = d3.scale.ordinal()
						    .rangeRoundBands([0, width], .1);

						var y = d3.scale.linear()
						    .range([height, 0]);

						var xAxis = d3.svg.axis()
						    .scale(x)
						    .orient("bottom");

						var yAxis = d3.svg.axis()
						    .scale(y)
						    .orient("left")

						var tip = d3.tip()
						  		.attr("class", "d3-tip")
					  			.offset([-10, 0])
						  		.html(function(d) {
						    		return "<strong>Sales:</strong> <span style=\'color:red\'>" + d.frequency + "</span>";
						  		})

						var svg = d3.select("#sales-week")
						      .append("svg")
						      .attr("width", width + margin.left + margin.right)
						      .attr("height", height + margin.top + margin.bottom)
						      .append("g")
						      .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

						svg.call(tip);

					  	x.domain(data.map(function(d) { return d.letter; }));
					  	y.domain([0, d3.max(data, function(d) { return d.frequency; })]);

					  	svg.append("g")
					      	.attr("class", "x axis")
					      	.attr("transform", "translate(0," + height + ")")
					      	.call(xAxis);

					  	svg.append("g")
					      	.attr("class", "y axis")
					      	.call(yAxis)
					    .append("text")
					      	.attr("transform", "rotate(-90)")
					      	.attr("y", 6)
					      	.attr("dy", ".71em")
					      	.style("text-anchor", "end")
					      	.text("Sales");

					  	svg.selectAll(".bar")
					      	.data(data)
					    .enter().append("rect")
					      	.attr("class", "bar")
					      	.attr("x", function(d) { return x(d.letter); })
					      	.attr("width", x.rangeBand())
					      	.attr("y", function(d) { return y(d.frequency); })
					      	.attr("height", function(d) { return height - y(d.frequency); })
					      	.on("mouseover", tip.show)
					      	.on("mouseout", tip.hide)
					');
                ?>
            </div>
        </div>
    </div>
</div>