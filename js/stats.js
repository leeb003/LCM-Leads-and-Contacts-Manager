// stats.js - javascript and jQuery for stats.php (statistics page)

// Source
(function basic_pie(container) {
    var i = 0;
    var dataSource = {};
    $.each(leadSources, function(key, val) {
        i++;
        newVal = parseInt(val, 10);
        if (i <= count) {
            if (i == 1) {
                dataSource[i] = {
                    data: [[0, newVal]],
                    label: key,
                    pie: { explode: 25 }
                }
            } else {
                dataSource[i] = {
                    data: [[0,newVal]],
                    label: key
                }
            }   
        }
    });

    var graph = Flotr.draw(container, dataSource, {

        HtmlText: false,
        grid: {
            verticalLines: false,
            horizontalLines: false
        },
        xaxis: {
            showLabels: false
        },
        yaxis: {
            showLabels: false
        },
        pie: {
            show: true,
            explode: 1 
        },
        mouse: {
            track: true,
            position: 'sw'
        },
        legend: {
            position: 'se',
            backgroundColor: '#D2E8FF'
        },
        title: 'Top Lead Sources',
        colors: ['#333', '#e62727', '#64b252', '#2981e4', '#ffb515']
    });
})(document.getElementById("leadSourceChart"));

   
// Types
i = 0;
var dataType = {};
(function basic_pie(container) {
    $.each(leadTypes, function(key, val) {
        i++;
        newVal = parseInt(val, 10);
        if (i <= count) {
            if (i == 1) {
                dataType[i] = {
                    data: [[0, newVal]],
                    label: key,
                    pie: { explode: 25 }
                }
            } else {
                dataType[i] = {
                    data: [[0,newVal]],
                    label: key
                }
            }   
        }
    });

    var graph = Flotr.draw(container, dataType, {
        HtmlText: false,
        grid: {
            verticalLines: false,
            horizontalLines: false
        },
        xaxis: {
            showLabels: false
        },
        yaxis: {
            showLabels: false
        },
        pie: {
            show: true,
            explode: 1 
        },
        mouse: {
            track: true,
            position: 'sw'
        },
        legend: {
            position: 'se',
            backgroundColor: '#D2E8FF'
        },
        title: 'Top Lead Types',
        colors: ['#333', '#e62727', '#64b252', '#2981e4', '#ffb515']
    });
})(document.getElementById("leadTypeChart"));


// Status
(function basic_bars(container, horizontal) {
    var d1 = [],
    ticks = [], 
    point, // Data point variable declaration
    i,
    inc = 0;

    $.each(leadStatuss, function(key, val) {
        inc++;
        var newVal = parseInt(val, 10);
        point = [inc, newVal];
        d1.push(point);
        ticks.push([inc, key]);
    });

    //alert(ticks.toSource());

    // Draw the graph
    Flotr.draw(
        container, [d1], {
            HtmlText : false,
            bars: {
                show: true,
                horizontal: false,
                shadowSize: 0,
                barWidth: 0.5,
                lineWidth: 1,
                fillColor: {
                    colors: ['#809a7a', '#c9dcc4'],
                    start: 'top',
                    end: 'bottom'
                },
                fillOpacity: 0.7,
                color: '#777'
            },
            markers: {
                show: true,
                position: 'ct'
            },
            mouse: {
                track: false,
                relative: true
            },
            xaxis: {
                noTicks: totalLeadCount,
                ticks: ticks,
                title: 'Status Groups'
            },
            yaxis: {
                min: 0,
                autoscaleMargin: 1
            },
            grid: {
            }
        });
})(document.getElementById("leadStatusChart"));

// Last Twelve Months
(function basic(container) {
    var d1 = [];
    var ticks = [];
    var inc = 0;
    $.each(lastTwelve, function(key, val) {
        inc++;
        var newVal = parseInt(val, 10);
        var point = [inc, newVal];
        d1.push(point);                
        ticks.push([inc, key]);
    });
    // Draw Graph
    graph = Flotr.draw(container, [d1], {
        xaxis: {
            minorTickFreq: 4,
            noTicks: 12,
            ticks: ticks
        },
        grid: {
            minorVerticalLines: true
        }
    });
})(document.getElementById("newLeadsMonth"));
