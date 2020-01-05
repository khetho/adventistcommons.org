
import Chart from 'chart.js';
import 'chartjs-plugin-colorschemes';

Chart.defaults.global.colours = [
    { // blue
        backgroundColor: "rgba(151,187,205,0.2)",
        borderColor: "rgba(151,187,205,1)",
    },
    { // light grey
        backgroundColor: "rgba(220,220,220,0.2)",
        borderColor: "rgba(220,220,220,1)",
    },
    { // red
        backgroundColor: "rgba(247,70,74,0.2)",
        borderColor: "rgba(247,70,74,1)",
    },
    { // green
        backgroundColor: "rgba(70,191,189,0.2)",
        borderColor: "rgba(70,191,189,1)",
    },
    { // yellow
        backgroundColor: "rgba(253,180,92,0.2)",
        borderColor: "rgba(253,180,92,1)",
    },
    { // grey
        backgroundColor: "rgba(148,159,177,0.2)",
        borderColor: "rgba(148,159,177,1)",
    },
    { // dark grey
        backgroundColor: "rgba(77,83,96,0.2)",
        borderColor: "rgba(77,83,96,1)",
    }
];

var ctx = document.getElementById('contributionChart');
var myChart = new Chart(ctx, {
    type: 'line',
    data: window.contributionsGraphData,
    options: {
    	options: {
		    plugins: {
		        colorschemes: {
		            scheme: 'office.Paired7'
		        }
		    }
		},
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }],
            xAxes: [{
		        type: 'time',
		        time: {
			        unit: 'month',
			        unitStepSize: 1,
			        displayFormats: {
			           'month': 'MMM YY'
			        }
		        }
		    }]
        }
    }
});