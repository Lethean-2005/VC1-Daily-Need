'use strict';
document.addEventListener('DOMContentLoaded', function () {
  setTimeout(function () {
    floatchart();
  }, 500);
});

function floatchart() {
  (function () {
    var options = {
    chart: {
      height: 450,
      type: 'pie',
      toolbar: {
        show: false
      }
    },
    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
    colors: ['#1890ff', '#13c2c2', '#ffc107', '#ff4d4f', '#52c41a', '#722ed1', '#eb2f96'],
    series: [42, 72, 73, 83, 76, 161, 141], // Sum of Page Views and Sessions per day
    responsive: [{
      breakpoint: 480,
      options: {
        chart: {
          width: 300
        },
        legend: {
          position: 'bottom'
        }
      }
    }]
  };

  var chart = new ApexCharts(document.querySelector("#visitor-chart"), options);
  chart.render();
  
    var options1 = {
      chart: {
        height: 450,
        type: 'pie',
        toolbar: {
          show: false
        }
      },
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      colors: ['#1890ff', '#13c2c2', '#ffc107', '#ff4d4f', '#52c41a', '#722ed1', '#eb2f96', '#fa541c', '#2f54eb', '#a0d911', '#13c2c2', '#fa8c16'],
      series: [
        76 + 110,  // Jan
        85 + 60,   // Feb
        101 + 150, // Mar
        98 + 35,   // Apr
        87 + 60,   // May
        105 + 36,  // Jun
        91 + 26,   // Jul
        114 + 45,  // Aug
        94 + 65,   // Sep
        86 + 52,   // Oct
        115 + 53,  // Nov
        35 + 41    // Dec
      ],
      responsive: [{
        breakpoint: 480,
        options: {
          chart: {
            width: 300
          },
          legend: {
            position: 'bottom'
          }
        }
      }]
    };

    var chart = new ApexCharts(document.querySelector('#visitor-chart-1'), options1);
    chart.render();
  })();

  (function () {
    var options = {
      chart: {
        type: 'bar',
        height: 365,
        toolbar: {
          show: false
        }
      },
      colors: ['#13c2c2'],
      plotOptions: {
        bar: {
          columnWidth: '50%',
          borderRadius: 0 // remove rounded corners for traditional look
        }
      },
      dataLabels: {
        enabled: false
      },
      series: [{
        name: 'Income',
        data: [80, 95, 70, 42, 65, 55, 78]
      }],
      stroke: {
        show: false // no border lines around bars
      },
      xaxis: {
        categories: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
        axisBorder: {
          show: true
        },
        axisTicks: {
          show: true
        }
      },
      yaxis: {
        show: true,
        labels: {
          style: {
            colors: '#6e6e6e',
            fontSize: '12px'
          }
        },
        title: {
          text: 'Income',
          style: {
            fontSize: '14px'
          }
        }
      },
      grid: {
        show: true,
        strokeDashArray: 4
      },
      tooltip: {
        enabled: true
      }
    };

    var chart = new ApexCharts(document.querySelector('#income-overview-chart'), options);
    chart.render();

  })();

  
  (function () {
    var options = {
      chart: {
        type: 'donut',
        height: 340,
        toolbar: {
          show: false
        }
      },
      colors: ['#faad14', '#ffc53d', '#ffd666', '#ffe58f', '#fff1b8', '#ffe58f', '#ffd666', '#ffc53d'],
      labels: [
        'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
      ],
      series: [58, 90, 38, 83, 63, 75, 35, 55],
      dataLabels: {
        enabled: true
      },
      legend: {
        position: 'bottom'
      },
      responsive: [{
        breakpoint: 480,
        options: {
          chart: {
            width: 300
          },
          legend: {
            position: 'bottom'
          }
        }
      }]
    };

    var chart = new ApexCharts(document.querySelector('#analytics-report-chart'), options);
    chart.render();

  })();
  (function () {
    var options = {
  chart: {
    type: 'bar',
    height: 430,
    toolbar: {
      show: false
    }
  },
  plotOptions: {
    bar: {
      columnWidth: '40%',
      borderRadius: 0 // Classic graph style (no round corners)
    }
  },
  stroke: {
    show: false // no border stroke
  },
  dataLabels: {
    enabled: false
  },
  legend: {
    position: 'top',
    horizontalAlign: 'right',
    fontFamily: `'Public Sans', sans-serif`,
    offsetX: 10,
    offsetY: 10,
    labels: {
      useSeriesColors: false
    },
    markers: {
      width: 10,
      height: 10,
      radius: 50,
      offsetX: 2,
      offsetY: 2
    },
    itemMargin: {
      horizontal: 15,
      vertical: 5
    }
  },
  colors: ['#faad14', '#1890ff'],
  series: [
    {
      name: 'Net Profit',
      data: [180, 90, 135, 114, 120, 145]
    },
    {
      name: 'Revenue',
      data: [120, 45, 78, 150, 168, 99]
    }
  ],
  xaxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    axisBorder: {
      show: true
    },
    axisTicks: {
      show: true
    }
  },
  yaxis: {
    show: true,
    labels: {
      style: {
        fontSize: '12px'
      }
    },
    title: {
      text: 'Amount',
      style: {
        fontSize: '14px'
      }
    }
  },
  grid: {
    show: true,
    strokeDashArray: 4
  },
  tooltip: {
    enabled: true
  }
};

var chart = new ApexCharts(document.querySelector('#sales-report-chart'), options);
chart.render();

  })();


}
