		var chart; // global
        var chart2;
		
		/**
		 * Request data from the server, add it to the graph and set a timeout to request again
		 */
		function requestData() {
			$.ajax({
				url: chartconfig.url,
                dataType: 'json',
				success: function(points) {
					var series = chart.series[0],
						shift = series.data.length > 20; // shift if the series is longer than 20
		
					// add the point
					chart.series[0].addPoint([points.time, points.cpu.Value], true, shift);
					chart.series[1].addPoint([points.time, points.gpu1.Value], true, shift);
                    chart.series[2].addPoint([points.time, points.gpu2.Value], true, shift);
                    
                    //chart2.series[0].setData();
                    chart2.series[0].setData([[points.cpu.Min, points.cpu.Max]],true);
                    chart2.series[1].setData([[points.gpu1.Min, points.gpu1.Max]],true);
                    chart2.series[2].setData([[points.gpu2.Min, points.gpu2.Max]],true);
                  
                    
					// call it again after two seconds
					setTimeout(requestData, 2000);	
				},
				cache: false
			});
		}
			
		$(document).ready(function() {

            Highcharts.setOptions({
                global: {
                    useUTC : false
                }
            });

// Apply the theme
Highcharts.setOptions(Highcharts.theme);


chart = new Highcharts.Chart({
				chart: {
					renderTo: 'container',
					defaultSeriesType: 'spline',
					events: {
						load: requestData
					}
				},
				title: {
					text: ''
				},
                credits: {
                    enabled: false
                },
                xAxis: {
					type: 'datetime',
					tickPixelInterval: 200,
					maxZoom: 20 * 1000,
                    labels: {
                        style: {
                            fontSize:'1.3rem'
                        }
                    }
				},
				yAxis: {
					minPadding: 0.2,
					maxPadding: 0.2,
					title: {
						text: 'Temperature ( °C )',
                        style: {
                            fontSize: '1.5rem'
                        }
                    },
                    labels: {
                        style: {
                            fontSize:'1.3rem'
                        }
                    }
				},
				series: [{
					name: 'CPU',
					data: []
				},{
					name: 'GPU1',
					data: []
				},{
					name: 'GPU2',
					data: []
				}]
			});		
		});
        
        


chart2 = new Highcharts.Chart({

    chart: {
        renderTo: 'container2',
        type: 'columnrange',
        inverted: false
    },
    credits: {
                    enabled: false
                },
    title: {
        text: ''
    },

    subtitle: {
        text: ''
    },

    xAxis: {
        labels: {
        enabled: false
        }
    },
    yAxis: {
        title: {
            text: 'Temperature ( °C )',
            style: {
                fontSize: '1.5rem'
            }
        }
    },

    tooltip: {
        valueSuffix: '°C'
    },

    plotOptions: {
        columnrange: {
            dataLabels: {
                enabled: true,
                formatter: function () {
                    return this.y + '°C';
                }
            }
        }
    },

    legend: {
        enabled: true
    },

    series: [{
        name: 'CPU',
        data: []
    },{
        name: 'GPU1',
        data: []
    },{
        name: 'GPU2',
        data: []
    }
    
    
    ]

}); 