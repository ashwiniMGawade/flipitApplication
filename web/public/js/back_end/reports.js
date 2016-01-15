$(function () {
    $('#onlineShopsContainer').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45
            }
        },
        credits: {
            enabled: false
        },
        title: {
            text: 'Total Online Shops'
        },
        subtitle: {
            text: 'Current Online Shops'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 45,
                innerSize: 120,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}: {point.y}'
                }
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
        },
        series: [{
            name: 'Online Shops',
            data: [
                ['A', 1220],
                ['A+', 91],
                ['AA', 67],
                ['AA+', 35],
                ['AAA', 20]
            ]
        }]
    });

    $('#totalShopsInYearContainer').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45
            }
        },
        credits: {
            enabled: false
        },
        title: {
            text: 'Total Money V/s No Money Shops'
        },
        subtitle: {
            text: 'Only Online Shops'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 45,
                innerSize: 120,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}: {point.y}'
                }
            },
            series: {
                dataLabels: {
                    enabled: true,
                    format: '{point.name}: {point.y}'
                }
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
        },
        series: [{
            name: 'Delivered amount',
            data: [
                ['Money', 1280],
                ['No Money', 700]
            ]
        }]
    });

    $('#monthlyShopsContainer').highcharts({
        chart: {
            type: 'column',
            options3d: {
                enabled: true,
                alpha: 0,
                beta: 0,
                viewDistance: 25,
                depth: 40
            },
            marginTop: 80,
            marginRight: 0
        },
        title: {
            text: 'Shops Added in Last 6 Months'
        },
        xAxis: {
            categories: ['Jan', 'Feb', 'March', 'April', 'May', 'June']
        },
        yAxis: {
            allowDecimals: false,
            min: 0,
            title: {
                text: 'Number of shops added/removed'
            }
        },
        credits: {
            enabled: false
        },
        tooltip: {
            headerFormat: '<b>{point.key}</b><br>',
            pointFormat: '<span style="color:{series.color}">\u25CF</span> {series.name}: {point.y} / {point.stackTotal}'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                depth: 20
            }
        },
        series: [{
            name: 'Money Shops',
            data: [3, 4, 2, 5, 3, 1],
            stack: 'added',
            color: Highcharts.getOptions().colors[0]
        }, {
            name: 'No Money Shops',
            data: [7, 1, 2, 3, 4, 7],
            stack: 'added',
            color: Highcharts.getOptions().colors[2]
        }]
    });

    $('#onlineOfferContainer').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45
            }
        },
        credits: {
            enabled: false
        },
        title: {
            text: 'Total Online Offers'
        },
        subtitle: {
            text: 'Current Online offers'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 45,
                innerSize: 120,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}: {point.y}'
                }
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
        },
        series: [{
            name: 'Online Count',
            data: [
                ['Coupons', 1200],
                ['Sales', 700],
                ['Printable Offers', 100],
                ['Exclusive', 200]
            ]
        }]
    });

    $('#totalCouponsInYearContainer').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45
            }
        },
        credits: {
            enabled: false
        },
        title: {
            text: 'Offers Added In Last 12 Months'
        },
        subtitle: {
            text: ''
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 45,
                innerSize: 120,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}: {point.y}'
                }
            },
            series: {
                dataLabels: {
                    enabled: true,
                    format: '{point.name}: {point.y}'
                }
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
        },
        series: [{
            name: 'Offers Added In last 12 Months',
            data: [
                ['Coupons', 2880],
                ['Sales', 700],
                ['Printable Coupons', 140],
                ['Exclusive', 410]
            ]
        }]
    });

    $('#monthlyOfferContainer').highcharts({
        chart: {
            type: 'column',
            options3d: {
                enabled: true,
                alpha: 0,
                beta: 0,
                viewDistance: 25,
                depth: 40
            },
            marginTop: 80,
            marginRight: 0
        },
        title: {
            text: 'Offers transaction in last 6 months'
        },
        xAxis: {
            categories: ['January', 'Feb', 'March', 'April', 'May', 'June']
        },
        yAxis: {
            allowDecimals: false,
            min: 0,
            title: {
                text: 'Number of offers added/expired '
            }
        },
        credits: {
            enabled: false
        },
        tooltip: {
            headerFormat: '<b>{point.key}</b><br>',
            pointFormat: '<span style="color:{series.color}">\u25CF</span> {series.name}: {point.y} / {point.stackTotal}'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                depth: 20
            }
        },
        series: [{
            name: 'Coupons Added',
            data: [235, 352, 401, 730, 292, 309],
            stack: 'added',
            color: Highcharts.getOptions().colors[0]
        }, {
            name: 'Sales Added',
            data: [332, 254, 214, 132, 155, 117],
            stack: 'added',
            color: Highcharts.getOptions().colors[3]
        }, {
            name: 'Printable Added',
            data: [112, 59, 69, 82, 71, 101],
            stack: 'added',
            color: Highcharts.getOptions().colors[2]
        }, {
            name: 'Offers Expired',
            data: [92, 45, 49, 31, 67, 21],
            stack: 'expired',
            color: Highcharts.getOptions().colors[4]
        }, {
            name: 'Sales Expired',
            data: [92, 31, 69, 51, 52, 31],
            stack: 'expired',
            color: Highcharts.getOptions().colors[7]
        }, {
            name: 'Printable Expired',
            data: [92, 71, 89, 85, 39, 19],
            stack: 'expired',
            color: Highcharts.getOptions().colors[6]
        }]
    });
});