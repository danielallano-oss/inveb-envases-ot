// OLD: si no se reutiliza al final se debe borrar
$(document).ready(function () {
    // // animationSpeed=32&angle=-20&lineWidth=20&radiusScale=100&pointer.length=60&pointer.strokeWidth=35&fontSize=24&divisions=5&divLength=70&divColor=333333&divWidth=11&subDivisions=3&subLength=50&subColor=666666&subWidth=6
    // var opts = {
    //     angle: -0.2, // The span of the gauge arc
    //     lineWidth: 0.2, // The line thickness
    //     radiusScale: 1, // Relative radius
    //     pointer: {
    //         length: 0.6, // // Relative to gauge radius
    //         strokeWidth: 0.035, // The thickness
    //         color: "#000000" // Fill color
    //     },
    //     limitMax: false, // If false, max value increases automatically if value > maxValue
    //     limitMin: false, // If true, the min value of the gauge will be fixed
    //     colorStart: "#6FADCF",
    //     colorStop: "#8FC0DA",
    //     strokeColor: "#E0E0E0",
    //     // colorStart: "#6FADCF", // Colors
    //     // colorStop: "#8FC0DA", // just experiment with them
    //     // strokeColor: "#E0E0E0", // to see which ones work best for you
    //     generateGradient: true,
    //     highDpiSupport: true, // High resolution support
    //     // percentColors: [
    //     //     [0.0, "#a9d70b"],
    //     //     [0.5, "#f9c802"],
    //     //     [1.0, "#ff0000"]
    //     // ]
    //     // staticLabels: {
    //     //     font: "10px sans-serif", // Specifies font
    //     //     labels: [100, 130, 150, 220.1, 260, 300], // Print labels at these values
    //     //     color: "#000000", // Optional: Label text color
    //     //     fractionDigits: 0 // Optional: Numerical precision. 0=round off.
    //     // }
    //     staticZones: [
    //         { strokeStyle: "#104203", min: 0, max: 17 }, // verde oscuro
    //         { strokeStyle: "#41d11b", min: 17, max: 33 }, // verde manzana
    //         { strokeStyle: "#e0dd1d", min: 33, max: 50 }, // amarillo
    //         { strokeStyle: "#e0af1d", min: 50, max: 67 }, // naranja
    //         { strokeStyle: "#e07b1d", min: 67, max: 83 }, // naranja oscuro
    //         { strokeStyle: "#e04350", min: 83, max: 100 } // Rojo
    //     ],
    //     maxValue: 100,
    //     minValue: 0,
    //     animationSpeed: 32
    // };
    // // var target = $("#velocimetro"); // your canvas element
    // var target1 = document.getElementById("velocimetro1"); // your canvas element
    // var target2 = document.getElementById("velocimetro2"); // your canvas element
    // var target3 = document.getElementById("velocimetro3"); // your canvas element
    // var target4 = document.getElementById("velocimetro4"); // your canvas element
    // var target5 = document.getElementById("velocimetro5"); // your canvas element
    // var target6 = document.getElementById("velocimetro6"); // your canvas element
    // var gauge1 = new Gauge(target1).setOptions(opts); // create sexy gauge!
    // var gauge2 = new Gauge(target2).setOptions(opts); // create sexy gauge!
    // var gauge3 = new Gauge(target3).setOptions(opts); // create sexy gauge!
    // var gauge4 = new Gauge(target4).setOptions(opts); // create sexy gauge!
    // var gauge5 = new Gauge(target5).setOptions(opts); // create sexy gauge!
    // var gauge6 = new Gauge(target6).setOptions(opts); // create sexy gauge!
    // const setGauge = gauge => {
    //     gauge.maxValue = 100; // set max gauge value
    //     gauge.setMinValue(0); // Prefer setter over gauge.minValue = 0
    //     gauge.animationSpeed = 32; // set animation speed (32 is default value)
    //     gauge.set(50); // set actual value
    // };
    // gauge1 = setGauge(gauge1);
    // gauge2 = setGauge(gauge2);
    // gauge3 = setGauge(gauge3);
    // gauge4 = setGauge(gauge4);
    // gauge5 = setGauge(gauge5);
    // gauge6 = setGauge(gauge6);
    // //  DONAS
    // new Chart(document.getElementById("solicitudes_vigentes1"), {
    //     type: "doughnut",
    //     data: {
    //         labels: ["Cerradas", "Vigentes", "Catalogadas"],
    //         datasets: [
    //             {
    //                 label: "Population (millions)",
    //                 backgroundColor: ["#104203", "#41d11b", "#239638"],
    //                 data: [2478, 5267, 734]
    //             }
    //         ]
    //     },
    //     options: {
    //         title: {
    //             display: false,
    //             text: "Solicitudes Vigentes, Catalogadas y Cerradas"
    //         },
    //         legend: {
    //             display: false,
    //             position: "bottom"
    //         }
    //     }
    // });
    // new Chart(document.getElementById("solicitudes_vigentes2"), {
    //     type: "doughnut",
    //     data: {
    //         labels: ["Cerradas", "Vigentes", "Catalogadas"],
    //         datasets: [
    //             {
    //                 label: "Population (millions)",
    //                 backgroundColor: ["#104203", "#41d11b", "#239638"],
    //                 data: [2478, 5267, 1600]
    //             }
    //         ]
    //     },
    //     options: {
    //         title: {
    //             display: false,
    //             text: "Solicitudes Vigentes, Catalogadas y Cerradas"
    //         },
    //         legend: {
    //             display: false,
    //             position: "bottom"
    //         }
    //     }
    // });
    const tipo_vendedor = $("#tipo_vendedor");
    tipo_vendedor.on("change", function () {

        var val = tipo_vendedor.val();

        return $.ajax({
            type: "GET",
            url: "/getTiposVendedores",
            data: "tipo_vendedor=" + val,
            success: function (data) {
                data = $.parseHTML(data);
                $("#vendedor_id")
                    .empty()
                    .append(data)
                    .selectpicker("refresh");

                //getIndicacionesEspeciales(val);
            },
            error: function (e) {
                console.log(e.responseText);
            },
            async: true
        });

    });
});

// ******************************* REPORTS: ********************************************
// ******************************* REPORTS: ********************************************

// REPORT: Gestión de OT por mes:
function generar_reporte_gestion_carga_ot_por_mesCantidad(
    mesesSeleccionados,
    solicitudesTotalesUltimosMeses,
    // cotizaSinCadSolicitudesUltimosMeses,
    //cotizaConCadSolicitudesTotalesUltimosMeses,
    muestraSolicitudesTotalesUltimosMeses,
    desarrolloCompletoSolicitudesTotalesUltimosMeses,
    arteSolicitudesTotalesUltimosMeses,
    otrasDesarrolloSolicitudesTotalesUltimosMeses,
    proyectoInnovacionSolicitudesTotalesUltimosMeses
) {
    // CANTIDAD VS TODAS LAS SOLICITUDES: +++++++++++++++++++++++++++++++++++++++++++
    var ctx = document.getElementById("myChart").getContext("2d");
    var data = {
        labels: mesesSeleccionados,
        datasets: [
            {
                label: "Todas las Solicitudes",
                backgroundColor: "#3aaa35",
                borderColor: "rgb(255, 99, 132)",
                data: solicitudesTotalesUltimosMeses, //valores de [may,jun,jul,ago,sep] ultimos 5 meses
            },
        ],
    };
    var options = {
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Cantidad",
                    },
                },
            ],
        },
        legend: {
            display: false,
            position: "bottom",
        },
        title: {
            display: true,
            text: "",
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var chart = new Chart(ctx, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data,
        // Configuration options go here
        options: options,
    });

    // CANTIDAD VS CADA TIPO DE SOLICITUD: +++++++++++++++++++++++++++++++++++++++++++
    var ctx2 = document.getElementById("myChart2").getContext("2d");
    var data2 = {
        labels: mesesSeleccionados,
        datasets: [
            /*{
                label: "Cotiza Sin CAD",
                backgroundColor: "#14880f",
                borderColor: "rgb(255, 99, 132)",
                data: cotizaSinCadSolicitudesUltimosMeses, //valores de [may,jun,jul,ago,sep]
            },
            {
                label: "Cotiza Con CAD",
                backgroundColor: "#22951d",
                borderColor: "rgb(255, 99, 132)",
                data: cotizaConCadSolicitudesTotalesUltimosMeses, //valores de [may,jun,jul,ago,sep]
            },*/
            {
                label: "Muestra Con CAD",
                backgroundColor: "#14880f",
                borderColor: "rgb(255, 99, 132)",
                data: muestraSolicitudesTotalesUltimosMeses, //valores de [may,jun,jul,ago,sep]
            },
            {
                label: "Arte Con Material",
                backgroundColor: "#44a840",
                borderColor: "rgb(255, 99, 132)",
                data: arteSolicitudesTotalesUltimosMeses, //valores de [may,jun,jul,ago,sep]
            },
            {
                label: "Desarrollo Completo",
                backgroundColor: "#6ad766",
                borderColor: "rgb(255, 99, 132)",
                data: desarrolloCompletoSolicitudesTotalesUltimosMeses, //valores de [may,jun,jul,ago,sep]
            },
            {
                label: "Otras Solicitudes Desarrollo",
                backgroundColor: "#a0f09d",
                borderColor: "rgb(255, 99, 132)",
                data: otrasDesarrolloSolicitudesTotalesUltimosMeses, //valores de [may,jun,jul,ago,sep]
            },
            {
                label: "Proyecto Innovacion",
                backgroundColor: "#c6fac3",
                borderColor: "rgb(255, 99, 132)",
                data: proyectoInnovacionSolicitudesTotalesUltimosMeses, //valores de [may,jun,jul,ago,sep]
            },
        ],
    };
    var options2 = {
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Cantidad",
                    },
                },
            ],
        },
        legend: {
            display: false,
            position: "bottom",
        },
        title: {
            display: true,
            text: "",
        },
        // events: false,
        tooltips: {
            enabled: true,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx2, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data2,
        // Configuration options go here
        options: options2,
    });
}

function generar_reporte_gestion_carga_ot_por_mesDias(
    mesesSeleccionados,
    diasPorSolicitudUltimosMeses,
    //cotizaSinCadPromedioDiasUltimosMeses,
    // cotizaConCadPromedioDiasTotalesUltimosMeses,
    muestraPromedioDiasTotalesUltimosMeses,
    desarrolloCompletoPromedioDiasTotalesUltimosMeses,
    artePromedioDiasTotalesUltimosMeses,
    otrasDesarrolloPromedioDiasTotalesUltimosMeses,
    proyectoInnovacionPromedioDiasTotalesUltimosMeses
) {
    // DIAS VS TODAS LAS SOLICITUDES: +++++++++++++++++++++++++++++++++++++++++++
    var ctx3 = document.getElementById("myChart3").getContext("2d");
    var data3 = {
        labels: mesesSeleccionados,
        datasets: [
            {
                label: "Todas las Solicitudes",
                backgroundColor: "#3aaa35",
                borderColor: "rgb(255, 99, 132)",
                data: diasPorSolicitudUltimosMeses, //valores de [may,jun,jul,ago,sep]
            },
        ],
    };
    var options3 = {
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Días",
                    },
                },
            ],
        },
        legend: {
            display: false,
            position: "bottom",
        },
        title: {
            display: true,
            text: "",
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var chart = new Chart(ctx3, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data3,
        // Configuration options go here
        options: options3,
    });

    // DIAS VS CADA TIPO DE SOLICITUD: +++++++++++++++++++++++++++++++++++++++++++
    var ctx4 = document.getElementById("myChart4").getContext("2d");
    var data4 = {
        labels: mesesSeleccionados,
        datasets: [
            /* {
                 label: "Cotiza Sin CAD",
                 backgroundColor: "#14880f",
                 borderColor: "rgb(255, 99, 132)",
                 data: cotizaSinCadPromedioDiasUltimosMeses, //valores de [may,jun,jul,ago,sep]
             },
             {
                 label: "Cotiza Con CAD",
                 backgroundColor: "#22951d",
                 borderColor: "rgb(255, 99, 132)",
                 data: cotizaConCadPromedioDiasTotalesUltimosMeses, //valores de [may,jun,jul,ago,sep]
             },*/
            {
                label: "Muestra Con CAD",
                backgroundColor: "#44a840",
                borderColor: "rgb(255, 99, 132)",
                data: muestraPromedioDiasTotalesUltimosMeses, //valores de [may,jun,jul,ago,sep]
            },
            {
                label: "Arte Con Material",
                backgroundColor: "#6ad766",
                borderColor: "rgb(255, 99, 132)",
                data: artePromedioDiasTotalesUltimosMeses, //valores de [may,jun,jul,ago,sep]
            },
            {
                label: "Desarrollo Completo",
                backgroundColor: "#a0f09d",
                borderColor: "rgb(255, 99, 132)",
                data: desarrolloCompletoPromedioDiasTotalesUltimosMeses, //valores de [may,jun,jul,ago,sep]
            },
            {
                label: "Otras Solicitudes Desarrollo",
                backgroundColor: "#a0f09d",
                borderColor: "rgb(255, 99, 132)",
                data: otrasDesarrolloPromedioDiasTotalesUltimosMeses, //valores de [may,jun,jul,ago,sep]
            },
            {
                label: "Proyecto Innovación",
                backgroundColor: "#a0f09d",
                borderColor: "rgb(255, 99, 132)",
                data: proyectoInnovacionPromedioDiasTotalesUltimosMeses, //valores de [may,jun,jul,ago,sep]
            },
        ],
    };
    var options4 = {
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Días",
                    },
                },
            ],
        },
        legend: {
            display: false,
            position: "bottom",
        },
        title: {
            display: true,
            text: "",
        },
        // events: false,
        tooltips: {
            enabled: true,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx4, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data4,
        // Configuration options go here
        options: options4,
    });
}

// REPORTE CONVERSION 5

// REPORT: Gestión de OT por mes:
function generar_reporte_ots_completadas(
    mesesSeleccionados,
    solicitudesTotalesUltimosMeses,
    desarrolloCompletoSolicitudesTotalesUltimosMeses,
    desarrolloCompletoSolicitudesTotalesUltimosMesesPorcentaje,
    artesCreadosUltimosMeses,
    artesTerminadosUltimosMeses,
    artesTerminadosUltimosMesesPorcentaje,
    totalMaterialesCreadosUltimosMeses,
    totalMaterialesCreadosUltimosMesesPorcentaje

) {
    // creados VS terminados: +++++++++++++++++++++++++++++++++++++++++++
    var ctx = document.getElementById("myChart").getContext("2d");
    var data = {
        labels: mesesSeleccionados,
        datasets: [
            {
                label: "Desarrollos Completos Creados",
                backgroundColor: "#3aaa35",
                borderColor: "rgb(255, 99, 132)",
                data: solicitudesTotalesUltimosMeses, //valores de [may,jun,jul,ago,sep] ultimos 5 meses
            },
            {
                label: "Desarrollos Completos Terminados",
                backgroundColor: "#a0f09d",
                borderColor: "rgb(255, 99, 132)",
                data: desarrolloCompletoSolicitudesTotalesUltimosMeses, //valores de [may,jun,jul,ago,sep] ultimos 5 meses
            },
            {
                label: "Arte con Material Creados",
                backgroundColor: "#3576ab",
                borderColor: "rgb(255, 99, 132)",
                data: artesCreadosUltimosMeses, //valores de [may,jun,jul,ago,sep] ultimos 5 meses
            },
            {
                label: "Arte con Material Terminados",
                backgroundColor: "#9dccf2",
                borderColor: "rgb(255, 99, 132)",
                data: artesTerminadosUltimosMeses, //valores de [may,jun,jul,ago,sep] ultimos 5 meses
            },
        ],
    };
    var options = {
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Cantidad",
                    },
                },
            ],
        },
        legend: {
            position: "bottom",
        },
        title: {
            display: true,
            text: "",
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var chart = new Chart(ctx, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data,
        // Configuration options go here
        options: options,
    });

    // creados vs terminados por mes: +++++++++++++++++++++++++++++++++++++++++++
    var ctx2 = document.getElementById("myChart2").getContext("2d");
    /*var data2 = {
        labels: mesesSeleccionados,
        datasets: [

            {
                type: 'bar',
                label: "Ratio Conversión Desarrollos",
                backgroundColor: "#a0f09d",
                borderColor: "rgb(255, 99, 132)",
                data: desarrolloCompletoSolicitudesTotalesUltimosMesesPorcentaje,
            },
            {
                type: 'bar',
                label: "Ratio Conversión Arte",
                backgroundColor: "#9dccf2",
                borderColor: "rgb(255, 99, 132)",
                data: artesTerminadosUltimosMesesPorcentaje,
            },
            {
                type: 'line',
                label: 'Line Dataset',
                data: [50, 50, 50, 50],
            }],
           /* {
                label: "Ratio Conversión Desarrollos",
                backgroundColor: "#a0f09d",
                borderColor: "rgb(255, 99, 132)",
                data: desarrolloCompletoSolicitudesTotalesUltimosMesesPorcentaje, //valores de [may,jun,jul,ago,sep]
            },
            {
                label: "Ratio Conversión Arte",
                backgroundColor: "#9dccf2",
                borderColor: "rgb(255, 99, 132)",
                data: artesTerminadosUltimosMesesPorcentaje, //valores de [may,jun,jul,ago,sep]
            },
        ],
    };*/

    // invocar grafico:
    /* var myBarChart = new Chart(ctx2, {
         // The type of chart we want to create
         // The data for our dataset
         data: data2,
         // Configuration options go here
         options: options2,
     });*/
    var areaChartData = {
        labels: mesesSeleccionados,
        datasets: [
            {
                label: 'Ratio Conversión Desarrollos',
                backgroundColor: '#a0f09d', // Transparent background
                borderColor: '#a0f09d',
                data: desarrolloCompletoSolicitudesTotalesUltimosMesesPorcentaje,
                order: 1,
            },
            {
                label: 'Ratio Conversión Arte',
                backgroundColor: '#9dccf2', // Transparent background
                borderColor: '#9dccf2',
                data: artesTerminadosUltimosMesesPorcentaje,
                order: 2,
            },
            {
               label: 'OTs Desarrollos y Arte con material / Materiales Creados',
                backgroundColor: 'rgba(255, 0, 0, 1)', // Transparent background
                borderColor: 'rgb(255, 0, 0)', // Red color for points
                data: totalMaterialesCreadosUltimosMesesPorcentaje,
                // type: 'line',
                // pointBackgroundColor: 'rgb(245, 236, 236)', // Red color for points
                // pointBorderColor: 'rgb(255, 0, 0)', // Red color for points
                // fill: false, // No fill under the line
                order: 3,
            },
        ],
    };

    var options2 = {
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "%",
                    },
                },
            ],
        },
        legend: {
            position: "bottom",
        },
        title: {
            display: true,
            text: "",
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.fillStyle = "#000000"; // Set font color to dark black
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // var barChartCanvas = bar_Chart.getContext('2d');

    var barChart = new Chart(ctx2, {
        type: 'bar',
        data: areaChartData,
        options: options2,
    });
    /* var barChart = new Chart(ctx2, {
       // The type of chart we want to create
       // The data for our dataset
     //  data: data2,
       // Configuration options go here
       options: options2,
   });*/
}

// FIN REPORTE CONVERSION

// REPORTE CONVERSION ENTRE FECHAS: Gestión de OT ENTRE FECHAS:
function generar_reporte_ots_completadas_entre_fechas(
    solicitudesTotalesUltimosMeses,
    desarrolloCompletoSolicitudesTotalesUltimosMeses,
    desarrolloCompletoSolicitudesTotalesUltimosMesesPorcentaje,
    artesCreadosUltimosMeses,
    artesTerminadosUltimosMeses,
    artesTerminadosUltimosMesesPorcentaje
) {
    // creados VS terminados: +++++++++++++++++++++++++++++++++++++++++++
    var ctx = document.getElementById("myChart3").getContext("2d");
    var data = {
        labels: ["Entre Fechas"],
        datasets: [
            {
                label: "Desarrollos Completos Creados",
                backgroundColor: "#3aaa35",
                borderColor: "rgb(255, 99, 132)",
                data: [solicitudesTotalesUltimosMeses], //valores de [may,jun,jul,ago,sep] ultimos 5 meses
            },
            {
                label: "Desarrollos Completos Terminados",
                backgroundColor: "#a0f09d",
                borderColor: "rgb(255, 99, 132)",
                data: [desarrolloCompletoSolicitudesTotalesUltimosMeses], //valores de [may,jun,jul,ago,sep] ultimos 5 meses
            },
            {
                label: "Arte con Material Creados",
                backgroundColor: "#3576ab",
                borderColor: "rgb(255, 99, 132)",
                data: [artesCreadosUltimosMeses], //valores de [may,jun,jul,ago,sep] ultimos 5 meses
            },
            {
                label: "Arte con Material Terminados",
                backgroundColor: "#9dccf2",
                borderColor: "rgb(255, 99, 132)",
                data: [artesTerminadosUltimosMeses], //valores de [may,jun,jul,ago,sep] ultimos 5 meses
            },
        ],
    };
    var options = {
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Cantidad",
                    },
                },
            ],
        },
        legend: {
            position: "bottom",
        },
        title: {
            display: true,
            text: "",
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var chart = new Chart(ctx, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data,
        // Configuration options go here
        options: options,
    });

    // creados vs terminados por mes: +++++++++++++++++++++++++++++++++++++++++++
    var ctx2 = document.getElementById("myChart4").getContext("2d");
    var data2 = {
        labels: ["Entre Fechas"],
        datasets: [
            {
                label: "Ratio Conversión Desarrollos",
                backgroundColor: "#a0f09d",
                borderColor: "rgb(255, 99, 132)",
                data: [
                    desarrolloCompletoSolicitudesTotalesUltimosMesesPorcentaje,
                ], //valores de [may,jun,jul,ago,sep]
            },
            {
                label: "Ratio Conversión Arte",
                backgroundColor: "#9dccf2",
                borderColor: "rgb(255, 99, 132)",
                data: [artesTerminadosUltimosMesesPorcentaje], //valores de [may,jun,jul,ago,sep]
            },
        ],
    };
    var options2 = {
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "%",
                    },
                },
            ],
        },
        legend: {
            position: "bottom",
        },
        title: {
            display: true,
            text: "",
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx2, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data2,
        // Configuration options go here
        options: options2,
    });
}

// FIN REPORTE CONVERSION ENTRE FECHAS

// REPORTE 3
// REPORT: Tiempos por areas de OT por mes:
function generar_reporte_tiempos_por_area_ot_por_mesDias(
    mesesSeleccionados,
    diasPorSolicitudUltimosMeses,
    ventaPromedioDiasUltimosMeses,
    desarrolloPromedioDiasTotalesUltimosMeses,
    muestrasPromedioDiasTotalesUltimosMeses,
    diseñoPromedioDiasTotalesUltimosMeses,
    catalogacionPromedioDiasTotalesUltimosMeses,
    precatalogacionPromedioDiasTotalesUltimosMeses
) {
    // DIAS VS TODAS LAS SOLICITUDES: +++++++++++++++++++++++++++++++++++++++++++
    var ctx3 = document.getElementById("myChart3").getContext("2d");
    var data3 = {
        labels: mesesSeleccionados,
        datasets: [
            {
                label: "",
                backgroundColor: "#3AAA35",
                borderColor: "rgb(255, 99, 132)",
                data: diasPorSolicitudUltimosMeses, //valores de [may,jun,jul,ago,sep]
            },
        ],
    };
    var options3 = {
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 20,
                bottom: 0,
            },
        },
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Días",
                    },
                },
            ],
        },
        legend: {
            display: false,
            position: "bottom",
        },
        title: {
            display: false,
            text: "Title",
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var chart = new Chart(ctx3, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data3,
        // Configuration options go here
        options: options3,
    });
    // DIAS VS CADA TIPO DE SOLICITUD: +++++++++++++++++++++++++++++++++++++++++++
    var ctx4 = document.getElementById("myChart4").getContext("2d");
    var data4 = {
        labels: mesesSeleccionados,
        datasets: [
            {
                label: "Venta",
                backgroundColor: "#4A4231",
                // borderColor: "rgb(255, 99, 132)",
                data: ventaPromedioDiasUltimosMeses, //valores de [may,jun,jul,ago,sep]
            },
            {
                label: "Dis. Estructural",
                backgroundColor: "#B2841E",
                // borderColor: "rgb(255, 99, 132)",
                data: desarrolloPromedioDiasTotalesUltimosMeses, //valores de [may,jun,jul,ago,sep]
            },
            {
                label: "Sala de Muestras",
                backgroundColor: "#F09606",
                // borderColor: "rgb(255, 99, 132)",
                data: muestrasPromedioDiasTotalesUltimosMeses, //valores de [may,jun,jul,ago,sep]
            },
            {
                label: "Dis. Gráfico",
                backgroundColor: "#F0CC06",
                // borderColor: "rgb(255, 99, 132)",
                data: diseñoPromedioDiasTotalesUltimosMeses, //valores de [may,jun,jul,ago,sep]
            },
            {
                label: "Precatalogación",
                backgroundColor: "#9EC304",
                // borderColor: "rgb(255, 99, 132)",
                data: precatalogacionPromedioDiasTotalesUltimosMeses, //valores de [may,jun,jul,ago,sep]
            },
            {
                label: "Catalogación",
                backgroundColor: "#238407",
                // borderColor: "rgb(255, 99, 132)",
                data: catalogacionPromedioDiasTotalesUltimosMeses, //valores de [may,jun,jul,ago,sep]
            },
        ],
    };
    var options4 = {
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 20,
                bottom: 0,
            },
        },
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Días",
                    },
                },
            ],
        },
        legend: {
            position: "bottom",
        },
        title: {
            display: false,
            text: "Title",
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx4, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data4,
        // Configuration options go here
        options: options4,
    });
}

function generar_reporte_tiempos_por_area_ot_por_mesCantidad(
    mesesSeleccionados,
    solicitudesTotalesUltimosMeses
) {
    // CANTIDAD VS TODAS LAS SOLICITUDES: +++++++++++++++++++++++++++++++++++++++++++
    var ctx = document.getElementById("myChart").getContext("2d");
    var data = {
        labels: mesesSeleccionados,
        datasets: [
            {
                label: "",
                backgroundColor: "#3AAA35",
                borderColor: "rgb(255, 99, 132)",
                data: solicitudesTotalesUltimosMeses, //valores de [may,jun,jul,ago,sep]
            },
        ],
    };
    var options = {
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 20,
                bottom: 0,
            },
        },
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Cantidad",
                    },
                },
            ],
        },
        legend: {
            display: false,
            position: "bottom",
        },
        title: {
            display: false,
            text: "Title",
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var chart = new Chart(ctx, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data,
        // Configuration options go here
        options: options,
    });
}

// REPORT: Motivos de Rechazos por areas de OT por mes:

function generar_reporte_motivos_rechazos_por_area_ot_por_mes(
    motivosCompletos,
    motivosIngenieriaAVentas,
    motivosIngenieriaAMuestras,
    motivosMuestrasAIngenieria,
    motivosDiseñoAVentas,
    motivosDiseñoAIngenieria,
    motivosCatalogacionAVentas,
    motivosCatalogacionAIngenieria,
    motivosCatalogacionADiseño,
    motivosPrecatalogacionAVentas,
    motivosPrecatalogacionAIngenieria,
    motivosPrecatalogacionADiseño
) {
    // Configuracion general:++++++++++++++++++++++++++++++++++++++++++++++++++++
    Chart.defaults.doughnutLabels = Chart.helpers.clone(
        Chart.defaults.doughnut
    );
    var helpers = Chart.helpers;
    var defaults = Chart.defaults;
    Chart.controllers.doughnutLabels = Chart.controllers.doughnut.extend({
        updateElement: function (arc, index, reset) {
            var _this = this;
            var chart = _this.chart,
                chartArea = chart.chartArea,
                opts = chart.options,
                animationOpts = opts.animation,
                arcOpts = opts.elements.arc,
                centerX = (chartArea.left + chartArea.right) / 2,
                centerY = (chartArea.top + chartArea.bottom) / 2,
                startAngle = opts.rotation, // non reset case handled later
                endAngle = opts.rotation, // non reset case handled later
                dataset = _this.getDataset(),
                circumference =
                    reset && animationOpts.animateRotate
                        ? 0
                        : arc.hidden
                            ? 0
                            : _this.calculateCircumference(dataset.data[index]) *
                            (opts.circumference / (2.0 * Math.PI)),
                innerRadius =
                    reset && animationOpts.animateScale ? 0 : _this.innerRadius,
                outerRadius =
                    reset && animationOpts.animateScale ? 0 : _this.outerRadius,
                custom = arc.custom || {},
                valueAtIndexOrDefault = helpers.getValueAtIndexOrDefault;

            helpers.extend(arc, {
                // Utility
                _datasetIndex: _this.index,
                _index: index,

                // Desired view properties
                _model: {
                    x: centerX + chart.offsetX,
                    y: centerY + chart.offsetY,
                    startAngle: startAngle,
                    endAngle: endAngle,
                    circumference: circumference,
                    outerRadius: outerRadius,
                    innerRadius: innerRadius,
                    label: valueAtIndexOrDefault(
                        dataset.label,
                        index,
                        chart.data.labels[index]
                    ),
                },

                draw: function () {
                    var ctx = this._chart.ctx,
                        vm = this._view,
                        sA = vm.startAngle,
                        eA = vm.endAngle,
                        opts = this._chart.config.options;

                    var labelPos = this.tooltipPosition();
                    var segmentLabel =
                        (vm.circumference / opts.circumference) * 100;

                    ctx.beginPath();

                    ctx.arc(vm.x, vm.y, vm.outerRadius, sA, eA);
                    ctx.arc(vm.x, vm.y, vm.innerRadius, eA, sA, true);

                    ctx.closePath();
                    ctx.strokeStyle = vm.borderColor;
                    ctx.lineWidth = vm.borderWidth;

                    ctx.fillStyle = vm.backgroundColor;

                    ctx.fill();
                    ctx.lineJoin = "bevel";

                    if (vm.borderWidth) {
                        ctx.stroke();
                    }

                    if (vm.circumference > 0.15) {
                        // Trying to hide label when it doesn't fit in segment
                        ctx.beginPath();
                        ctx.font = helpers.fontString(
                            opts.defaultFontSize,
                            opts.defaultFontStyle,
                            opts.defaultFontFamily
                        );
                        ctx.fillStyle = "#000";
                        ctx.textBaseline = "top";
                        ctx.textAlign = "center";

                        // Round percentage in a way that it always adds up to 100%
                        var pos_y = labelPos.y - 10;
                        ctx.fillText(
                            "" + dataset.data[index],
                            labelPos.x,
                            pos_y
                        );
                        ctx.fillText(
                            segmentLabel.toFixed(0) + "%",
                            labelPos.x,
                            pos_y + 12
                        );
                    }
                },
            });

            var model = arc._model;
            model.backgroundColor = custom.backgroundColor
                ? custom.backgroundColor
                : valueAtIndexOrDefault(
                    dataset.backgroundColor,
                    index,
                    arcOpts.backgroundColor
                );
            model.hoverBackgroundColor = custom.hoverBackgroundColor
                ? custom.hoverBackgroundColor
                : valueAtIndexOrDefault(
                    dataset.hoverBackgroundColor,
                    index,
                    arcOpts.hoverBackgroundColor
                );
            model.borderWidth = custom.borderWidth
                ? custom.borderWidth
                : valueAtIndexOrDefault(
                    dataset.borderWidth,
                    index,
                    arcOpts.borderWidth
                );
            model.borderColor = custom.borderColor
                ? custom.borderColor
                : valueAtIndexOrDefault(
                    dataset.borderColor,
                    index,
                    arcOpts.borderColor
                );

            // Set correct angles if not resetting
            if (!reset || !animationOpts.animateRotate) {
                if (index === 0) {
                    model.startAngle = opts.rotation;
                } else {
                    model.startAngle = _this.getMeta().data[
                        index - 1
                    ]._model.endAngle;
                }

                model.endAngle = model.startAngle + model.circumference;
            }

            arc.pivot();
        },
    });
    // Todas las Areas: ++++++++++++++++++++++++++++++++++++++++++++++++++++
    var ctx = document.getElementById("myChart").getContext("2d");

    var data = {
        datasets: [
            {
                label: "",
                backgroundColor: [
                    "#FAA43A", //'Descripción de Producto',
                    "#6e6e6e", //'Error de Digitación',
                    "#F17CB0", //'Error Tipo Sustrato',
                    "#60BD68", //'Falta Informacion ',
                    "#806939", //'Falta Muestra Fisica',
                    "#73e2e6", //'Formato Imagen Inadecuado',
                    "#5DA5DA", //'Informacion Erronea',
                    "#DECF3F", //'Medida Erronea',
                    "#F15854", //'No Viable Por Restricciones',
                    "#a668f2", //'Plano Mal Acotado'
                    "#3f95a6", //'Falta CAD para corte'
                    "#7b36e3", //'Falta OT Chileexpress'
                    "#bd6b1e", //'Falta OT Laboratorio'
                ],
                borderColor: "#fff",
                data: motivosCompletos, //Descripción de producto ,Error de digitación, Error tipo Sustrato, Falta Informacion , Falta Muestra Fisica, Formato Imagen Inadecuado, Informacion Erronea, Medida Erronea, No viable por Restricciones, Plano mal acotado
            },
        ],

        // These labels appear in the legend and in the tooltips when hovering different arcs
        labels: [
            "Descripción de Producto",
            "Error de Digitación",
            "Error Tipo Sustrato",
            "Falta Informacion ",
            "Falta Muestra Fisica",
            "Formato Imagen Inadecuado",
            "Informacion Erronea",
            "Medida Erronea",
            "No Viable Por Restricciones",
            "Plano Mal Acotado",
            "Falta CAD para corte",
            "Falta OT Chileexpress",
            "Falta OT Laboratorio",
        ],
    };

    var options = {
        responsive: true,
        legend: {
            display: false,
            position: "top",
        },
        title: {
            display: true,
            text: "Todos Los Motivos de Rechazos",
            position: "bottom",
            fontSize: "18",
            fontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
            fontColor: "#000",
        },
        animation: {
            animateScale: true,
            animateRotate: true,
        },
        // events: false,
        // tooltips: {
        //     enabled: false
        // },
        // hover: {
        //     animationDuration: 0
        // },
        // animation: {
        //     duration: 1,
        //     onComplete: function () {
        //         var chartInstance = this.chart,
        //             ctx = chartInstance.ctx;
        //         ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
        //         ctx.textAlign = 'center';
        //         ctx.textBaseline = 'bottom';

        //         this.data.datasets.forEach(function (dataset, i) {
        //             var meta = chartInstance.controller.getDatasetMeta(i);
        //             meta.data.forEach(function (bar, index) {
        //                 var data = dataset.data[index];
        //                 ctx.fillText(data, bar._model.x, bar._model.y - 5);
        //             });
        //         });
        //     }
        // }
    };

    // invocar grafico:
    var myDoughnutChart = new Chart(ctx, {
        // The type of chart we want to create
        type: "doughnutLabels",
        // The data for our dataset
        data: data,
        // Configuration options go here
        options: options,
        plugins: [
            {
                id: "total",
                beforeDraw: function (chart) {
                    const width = chart.chart.width;
                    const height = chart.chart.height;
                    const ctx = chart.chart.ctx;
                    ctx.restore();
                    const fontSize = (height / 114).toFixed(2);
                    ctx.font = fontSize + "em sans-serif";
                    ctx.textBaseline = "middle";
                    var total = data.datasets[0].data.reduce(function (
                        previousValue,
                        currentValue,
                        currentIndex,
                        array
                    ) {
                        return previousValue + currentValue;
                    });
                    const text = total;
                    const textX = Math.round(
                        (width - ctx.measureText(text).width) / 2
                    );
                    const textY = height / 2.1;
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                },
            },
        ],
    });

    // Area 1 Diseño Estructural a Ventas: ++++++++++++++++++++++++++++++++++++++++++++++++++++
    var ctx1 = document.getElementById("myChart1").getContext("2d");
    var data1 = {
        datasets: [
            {
                label: "",
                backgroundColor: ["#60BD68", "#5DA5DA", "#806939", "#F15854"],
                borderColor: "#fff",
                data: motivosIngenieriaAVentas,
            },
        ],
        labels: [
            "Falta Informacion ",
            "Informacion Erronea",
            "Falta Muestra Fisica",
            "No Viable Por Restricciones",
        ],
    };
    var options1 = {
        responsive: true,
        legend: {
            display: false,
            position: "top",
        },
        title: {
            display: true,
            text: "DEV: Diseño Estructural a Ventas",
            position: "bottom",
            fontSize: "12",
            fontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
            fontColor: "#000",
        },
    };
    // invocar grafico:
    var myDoughnutChart1 = new Chart(ctx1, {
        type: "doughnutLabels",
        data: data1,
        options: options1,
        plugins: [
            {
                id: "total",
                beforeDraw: function (chart) {
                    const width = chart.chart.width;
                    const height = chart.chart.height;
                    const ctx = chart.chart.ctx;
                    ctx.restore();
                    const fontSize = (height / 114).toFixed(2);
                    ctx.font = fontSize + "em sans-serif";
                    ctx.textBaseline = "middle";
                    var total = data1.datasets[0].data.reduce(function (
                        previousValue,
                        currentValue,
                        currentIndex,
                        array
                    ) {
                        return previousValue + currentValue;
                    });
                    const text = total;
                    const textX = Math.round(
                        (width - ctx.measureText(text).width) / 2
                    );
                    const textY = height / 2.3;
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                },
            },
        ],
    });
    // Area 2 Diseño Estructural a Muestras: ++++++++++++++++++++++++++++++++++++++++++++++++++++
    var ctx10 = document.getElementById("myChart10").getContext("2d");
    var data10 = {
        datasets: [
            {
                label: "",
                backgroundColor: ["#60BD68", "#5DA5DA"],
                borderColor: "#fff",
                data: motivosIngenieriaAMuestras,
            },
        ],
        labels: ["Falta Informacion ", "Informacion Erronea"],
    };
    var options10 = {
        responsive: true,
        legend: {
            display: false,
            position: "top",
        },
        title: {
            display: true,
            text: "DEV: Diseño Estructural a Muestras",
            position: "bottom",
            fontSize: "12",
            fontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
            fontColor: "#000",
        },
    };
    // invocar grafico:
    var myDoughnutChart10 = new Chart(ctx10, {
        type: "doughnutLabels",
        data: data10,
        options: options10,
        plugins: [
            {
                id: "total",
                beforeDraw: function (chart) {
                    const width = chart.chart.width;
                    const height = chart.chart.height;
                    const ctx = chart.chart.ctx;
                    ctx.restore();
                    const fontSize = (height / 114).toFixed(2);
                    ctx.font = fontSize + "em sans-serif";
                    ctx.textBaseline = "middle";
                    var total = data10.datasets[0].data.reduce(function (
                        previousValue,
                        currentValue,
                        currentIndex,
                        array
                    ) {
                        return previousValue + currentValue;
                    });
                    const text = total;
                    const textX = Math.round(
                        (width - ctx.measureText(text).width) / 2
                    );
                    const textY = height / 2.3;
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                },
            },
        ],
    });
    // Area 6 Muestras a Diseño Estructural : ++++++++++++++++++++++++++++++++++++++++++++++++++++
    var ctx11 = document.getElementById("myChart11").getContext("2d");
    var data11 = {
        datasets: [
            {
                label: "",
                backgroundColor: [
                    "#60BD68",
                    "#5DA5DA",
                    "#3f95a6",
                    "#7b36e3",
                    "#bd6b1e",
                ],
                borderColor: "#fff",
                data: motivosMuestrasAIngenieria,
            },
        ],
        labels: [
            "Falta Informacion ",
            "Informacion Erronea",
            "Falta CAD para corte",
            "Falta OT Chileexpress",
            "Falta OT Laboratorio",
        ],
    };
    var options11 = {
        responsive: true,
        legend: {
            display: false,
            position: "top",
        },
        title: {
            display: true,
            text: "DEV: Muestras a Diseño Estructural",
            position: "bottom",
            fontSize: "12",
            fontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
            fontColor: "#000",
        },
    };
    // invocar grafico:
    var myDoughnutChart11 = new Chart(ctx11, {
        type: "doughnutLabels",
        data: data11,
        options: options11,
        plugins: [
            {
                id: "total",
                beforeDraw: function (chart) {
                    const width = chart.chart.width;
                    const height = chart.chart.height;
                    const ctx = chart.chart.ctx;
                    ctx.restore();
                    const fontSize = (height / 114).toFixed(2);
                    ctx.font = fontSize + "em sans-serif";
                    ctx.textBaseline = "middle";
                    var total = data11.datasets[0].data.reduce(function (
                        previousValue,
                        currentValue,
                        currentIndex,
                        array
                    ) {
                        return previousValue + currentValue;
                    });
                    const text = total;
                    const textX = Math.round(
                        (width - ctx.measureText(text).width) / 2
                    );
                    const textY = height / 2.3;
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                },
            },
        ],
    });
    // Area 2 Diseño Gráfico  a Ventas: ++++++++++++++++++++++++++++++++++++++++++++++++++++
    var ctx2 = document.getElementById("myChart2").getContext("2d");
    var data2 = {
        datasets: [
            {
                label: "",
                backgroundColor: [
                    "#60BD68",
                    "#5DA5DA",
                    "#806939",
                    "#73e2e6",
                    "#F15854",
                ],
                borderColor: "#fff",
                data: motivosDiseñoAVentas,
            },
        ],
        labels: [
            "Falta Informacion ",
            "Informacion Erronea",
            "Falta Muestra Fisica",
            "Formato Imagen Inadecuado",
            "No Viable Por Restricciones",
        ],
    };
    var options2 = {
        responsive: true,
        legend: {
            display: false,
            position: "top",
        },
        title: {
            display: true,
            text: "DGV: Diseño Gráfico a Ventas",
            position: "bottom",
            fontSize: "12",
            fontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
            fontColor: "#000",
        },
    };
    // invocar grafico:
    var myDoughnutChart2 = new Chart(ctx2, {
        type: "doughnutLabels",
        data: data2,
        options: options2,
        plugins: [
            {
                id: "total",
                beforeDraw: function (chart) {
                    const width = chart.chart.width;
                    const height = chart.chart.height;
                    const ctx = chart.chart.ctx;
                    ctx.restore();
                    const fontSize = (height / 114).toFixed(2);
                    ctx.font = fontSize + "em sans-serif";
                    ctx.textBaseline = "middle";
                    var total = data2.datasets[0].data.reduce(function (
                        previousValue,
                        currentValue,
                        currentIndex,
                        array
                    ) {
                        return previousValue + currentValue;
                    });
                    const text = total;
                    const textX = Math.round(
                        (width - ctx.measureText(text).width) / 2
                    );
                    const textY = height / 2.3;
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                },
            },
        ],
    });
    // Area 3 Diseño Gráfico a Diseño Estructural: ++++++++++++++++++++++++++++++++++++++++++++++++++++
    var ctx3 = document.getElementById("myChart3").getContext("2d");
    var data3 = {
        datasets: [
            {
                label: "",
                backgroundColor: ["#5DA5DA", "#DECF3F", "#F15854"],
                borderColor: "#fff",
                data: motivosDiseñoAIngenieria,
            },
        ],
        labels: [
            "Informacion Erronea",
            "Medida Erronea",
            "No Viable Por Restricciones",
        ],
    };
    var options3 = {
        responsive: true,
        legend: {
            display: false,
            position: "top",
        },
        title: {
            display: true,
            text: "DGDE: Diseño Gráfico a Diseño Estructural",
            position: "bottom",
            fontSize: "12",
            fontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
            fontColor: "#000",
        },
    };
    // invocar grafico:
    var myDoughnutChart3 = new Chart(ctx3, {
        type: "doughnutLabels",
        data: data3,
        options: options3,
        plugins: [
            {
                id: "total",
                beforeDraw: function (chart) {
                    const width = chart.chart.width;
                    const height = chart.chart.height;
                    const ctx = chart.chart.ctx;
                    ctx.restore();
                    const fontSize = (height / 114).toFixed(2);
                    ctx.font = fontSize + "em sans-serif";
                    ctx.textBaseline = "middle";
                    var total = data3.datasets[0].data.reduce(function (
                        previousValue,
                        currentValue,
                        currentIndex,
                        array
                    ) {
                        return previousValue + currentValue;
                    });
                    const text = total;
                    const textX = Math.round(
                        (width - ctx.measureText(text).width) / 2
                    );
                    const textY = height / 2.3;
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                },
            },
        ],
    });
    // Area 4 Pre Catalogación a Ventas: ++++++++++++++++++++++++++++++++++++++++++++++++++++
    var ctx4 = document.getElementById("myChart4").getContext("2d");
    var data4 = {
        datasets: [
            {
                label: "",
                backgroundColor: ["#60BD68", "#5DA5DA", "#FAA43A"],
                borderColor: "#fff",
                data: motivosPrecatalogacionAVentas,
            },
        ],
        labels: [
            "Falta Informacion ",
            "Informacion Erronea",
            "Descripción de Producto",
        ],
    };
    var options4 = {
        responsive: true,
        legend: {
            display: false,
            position: "top",
        },
        title: {
            display: true,
            text: "PCV: Pre Catalogación a Ventas",
            position: "bottom",
            fontSize: "12",
            fontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
            fontColor: "#000",
        },
    };
    // invocar grafico:
    var myDoughnutChart4 = new Chart(ctx4, {
        type: "doughnutLabels",
        data: data4,
        options: options4,
        plugins: [
            {
                id: "total",
                beforeDraw: function (chart) {
                    const width = chart.chart.width;
                    const height = chart.chart.height;
                    const ctx = chart.chart.ctx;
                    ctx.restore();
                    const fontSize = (height / 114).toFixed(2);
                    ctx.font = fontSize + "em sans-serif";
                    ctx.textBaseline = "middle";
                    var total = data4.datasets[0].data.reduce(function (
                        previousValue,
                        currentValue,
                        currentIndex,
                        array
                    ) {
                        return previousValue + currentValue;
                    });
                    const text = total;
                    const textX = Math.round(
                        (width - ctx.measureText(text).width) / 2
                    );
                    const textY = height / 2.3;
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                },
            },
        ],
    });
    // Area 5 Pre Catalogación a Diseño Estructural: ++++++++++++++++++++++++++++++++++++++++++++++++++++
    var ctx5 = document.getElementById("myChart5").getContext("2d");
    var data5 = {
        datasets: [
            {
                label: "",
                backgroundColor: ["#60BD68", "#DECF3F", "#a668f2", "#6e6e6e"],
                borderColor: "#fff",
                data: motivosPrecatalogacionAIngenieria,
            },
        ],
        labels: [
            "Falta Información",
            "Medida Erronea",
            "Plano Mal Acotado",
            "Error de Digitación",
        ],
    };
    var options5 = {
        responsive: true,
        legend: {
            display: false,
            position: "top",
        },
        title: {
            display: true,
            text: "PCDE: Pre Catalogación a Diseño Estructural",
            position: "bottom",
            fontSize: "12",
            fontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
            fontColor: "#000",
        },
    };
    // invocar grafico:
    var myDoughnutChart5 = new Chart(ctx5, {
        type: "doughnutLabels",
        data: data5,
        options: options5,
        plugins: [
            {
                id: "total",
                beforeDraw: function (chart) {
                    const width = chart.chart.width;
                    const height = chart.chart.height;
                    const ctx = chart.chart.ctx;
                    ctx.restore();
                    const fontSize = (height / 114).toFixed(2);
                    ctx.font = fontSize + "em sans-serif";
                    ctx.textBaseline = "middle";
                    var total = data5.datasets[0].data.reduce(function (
                        previousValue,
                        currentValue,
                        currentIndex,
                        array
                    ) {
                        return previousValue + currentValue;
                    });
                    const text = total;
                    const textX = Math.round(
                        (width - ctx.measureText(text).width) / 2
                    );
                    const textY = height / 2.3;
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                },
            },
        ],
    });
    // Area 6 Pre Catalogación a Diseño Gráfico: ++++++++++++++++++++++++++++++++++++++++++++++++++++
    var ctx6 = document.getElementById("myChart6").getContext("2d");
    var data6 = {
        datasets: [
            {
                label: "",
                backgroundColor: ["#5DA5DA", "#F17CB0"],
                borderColor: "#fff",
                data: motivosPrecatalogacionADiseño,
            },
        ],
        labels: ["Informacion Erronea", "Error Tipo Sustrato"],
    };
    var options6 = {
        responsive: true,
        legend: {
            display: false,
            position: "top",
        },
        title: {
            display: true,
            text: "PCDG: Pre Catalogación a Diseño Gráfico",
            position: "bottom",
            fontSize: "12",
            fontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
            fontColor: "#000",
        },
    };
    // invocar grafico:
    var myDoughnutChart6 = new Chart(ctx6, {
        type: "doughnutLabels",
        data: data6,
        options: options6,
        plugins: [
            {
                id: "total",
                beforeDraw: function (chart) {
                    const width = chart.chart.width;
                    const height = chart.chart.height;
                    const ctx = chart.chart.ctx;
                    ctx.restore();
                    const fontSize = (height / 114).toFixed(2);
                    ctx.font = fontSize + "em sans-serif";
                    ctx.textBaseline = "middle";
                    var total = data6.datasets[0].data.reduce(function (
                        previousValue,
                        currentValue,
                        currentIndex,
                        array
                    ) {
                        return previousValue + currentValue;
                    });
                    const text = total;
                    const textX = Math.round(
                        (width - ctx.measureText(text).width) / 2
                    );
                    const textY = height / 2.3;
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                },
            },
        ],
    });
    // Area 7 Catalogacion a Ventas: ++++++++++++++++++++++++++++++++++++++++++++++++++++
    var ctx7 = document.getElementById("myChart7").getContext("2d");
    var data7 = {
        datasets: [
            {
                label: "",
                backgroundColor: ["#60BD68", "#5DA5DA", "#FAA43A"],
                borderColor: "#fff",
                data: motivosCatalogacionAVentas,
            },
        ],
        labels: [
            "Falta Informacion ",
            "Informacion Erronea",
            "Descripción de Producto",
        ],
    };
    var options7 = {
        responsive: true,
        legend: {
            display: false,
            position: "top",
        },
        title: {
            display: true,
            text: "CAV: Catalogacion a Ventas",
            position: "bottom",
            fontSize: "12",
            fontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
            fontColor: "#000",
        },
    };
    // invocar grafico:
    var myDoughnutChart7 = new Chart(ctx7, {
        type: "doughnutLabels",
        data: data7,
        options: options7,
        plugins: [
            {
                id: "total",
                beforeDraw: function (chart) {
                    const width = chart.chart.width;
                    const height = chart.chart.height;
                    const ctx = chart.chart.ctx;
                    ctx.restore();
                    const fontSize = (height / 114).toFixed(2);
                    ctx.font = fontSize + "em sans-serif";
                    ctx.textBaseline = "middle";
                    var total = data7.datasets[0].data.reduce(function (
                        previousValue,
                        currentValue,
                        currentIndex,
                        array
                    ) {
                        return previousValue + currentValue;
                    });
                    const text = total;
                    const textX = Math.round(
                        (width - ctx.measureText(text).width) / 2
                    );
                    const textY = height / 2.3;
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                },
            },
        ],
    });
    // Area 8 Catalogación a Diseño Estructural: ++++++++++++++++++++++++++++++++++++++++++++++++++++
    var ctx8 = document.getElementById("myChart8").getContext("2d");
    var data8 = {
        datasets: [
            {
                label: "",
                backgroundColor: ["#60BD68", "#DECF3F", "#a668f2", "#6e6e6e"],
                borderColor: "#fff",
                data: motivosCatalogacionAIngenieria,
            },
        ],
        labels: [
            "Falta Información",
            "Medida Erronea",
            "Plano Mal Acotado",
            "Error de Digitación",
        ],
    };
    var options8 = {
        responsive: true,
        legend: {
            display: false,
            position: "top",
        },
        title: {
            display: true,
            text: "CDE: Catalogación a Diseño Estructural",
            position: "bottom",
            fontSize: "12",
            fontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
            fontColor: "#000",
        },
    };
    // invocar grafico:
    var myDoughnutChart8 = new Chart(ctx8, {
        type: "doughnutLabels",
        data: data8,
        options: options8,
        plugins: [
            {
                id: "total",
                beforeDraw: function (chart) {
                    const width = chart.chart.width;
                    const height = chart.chart.height;
                    const ctx = chart.chart.ctx;
                    ctx.restore();
                    const fontSize = (height / 114).toFixed(2);
                    ctx.font = fontSize + "em sans-serif";
                    ctx.textBaseline = "middle";
                    var total = data8.datasets[0].data.reduce(function (
                        previousValue,
                        currentValue,
                        currentIndex,
                        array
                    ) {
                        return previousValue + currentValue;
                    });
                    const text = total;
                    const textX = Math.round(
                        (width - ctx.measureText(text).width) / 2
                    );
                    const textY = height / 2.3;
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                },
            },
        ],
    });
    // Area 9 Catalogación a Diseño Gráfico: ++++++++++++++++++++++++++++++++++++++++++++++++++++
    var ctx9 = document.getElementById("myChart9").getContext("2d");
    var data9 = {
        datasets: [
            {
                label: "",
                backgroundColor: ["#5DA5DA", "#F17CB0"],
                borderColor: "#fff",
                data: motivosCatalogacionADiseño,
            },
        ],
        labels: ["Informacion Erronea", "Error Tipo Sustrato"],
    };
    var options9 = {
        responsive: true,
        legend: {
            display: false,
            position: "top",
        },
        title: {
            display: true,
            text: "CDG: Catalogación a Diseño Gráfico",
            position: "bottom",
            fontSize: "12",
            fontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
            fontColor: "#000",
        },
    };
    // invocar grafico:
    var myDoughnutChart9 = new Chart(ctx9, {
        type: "doughnutLabels",
        data: data9,
        options: options9,
        plugins: [
            {
                id: "total",
                beforeDraw: function (chart) {
                    const width = chart.chart.width;
                    const height = chart.chart.height;
                    const ctx = chart.chart.ctx;
                    ctx.restore();
                    const fontSize = (height / 114).toFixed(2);
                    ctx.font = fontSize + "em sans-serif";
                    ctx.textBaseline = "middle";
                    var total = data9.datasets[0].data.reduce(function (
                        previousValue,
                        currentValue,
                        currentIndex,
                        array
                    ) {
                        return previousValue + currentValue;
                    });
                    const text = total;
                    const textX = Math.round(
                        (width - ctx.measureText(text).width) / 2
                    );
                    const textY = height / 2.3;
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                },
            },
        ],
    });
}

// REPORT: Gestión de OT por mes:
function generar_reporte_rechazos(
    mesesSeleccionados,
    faltaInformacion,
    informacionErronea,
    faltaMuestraFisica,
    formatoImagenInadecuado,
    medidaErronea,
    descripcionDeProducto,
    planoMalAcotado,
    errorDeDigitacion,
    errorTipoSustrato,
    noViablePorRestricciones,
    faltaCadParaCorte,
    faltaOTChileexpress,
    faltaOTLaboratorio
) {
    var ctx = document.getElementById("myChart").getContext("2d");
    var myChart = new Chart(ctx, {
        type: "bar",

        data: {
            labels: mesesSeleccionados,
            datasets: [
                {
                    label: "Falta Información",
                    backgroundColor: "#60BD68",
                    borderColor: "#60BD68",
                    data: faltaInformacion, //valores de [may,jun,jul,ago,sep] ultimos 5 meses
                },
                {
                    label: "Información Erronea",
                    backgroundColor: "#5DA5DA",
                    borderColor: "#5DA5DA",
                    data: informacionErronea, //valores de [may,jun,jul,ago,sep] ultimos 5 meses
                },
                {
                    label: "Medida Erronea",
                    backgroundColor: "#DECF3F",
                    borderColor: "#DECF3F",
                    data: medidaErronea, //valores de [may,jun,jul,ago,sep] ultimos 5 meses
                },
                {
                    label: "Falta Muestra Fisica",
                    backgroundColor: "#806939",
                    borderColor: "#806939",
                    data: faltaMuestraFisica, //valores de [may,jun,jul,ago,sep] ultimos 5 meses
                },
                {
                    label: "No Viable Por Restricciones",
                    backgroundColor: "#F15854",
                    borderColor: "#F15854",
                    data: noViablePorRestricciones, //valores de [may,jun,jul,ago,sep] ultimos 5 meses
                },
                {
                    label: "Error de Digitación",
                    backgroundColor: "#6e6e6e",
                    borderColor: "#6e6e6e",
                    data: errorDeDigitacion, //valores de [may,jun,jul,ago,sep] ultimos 5 meses
                },
                {
                    label: "Formato Imagen Inadecuado",
                    backgroundColor: "#73e2e6",
                    borderColor: "#73e2e6",
                    data: formatoImagenInadecuado, //valores de [may,jun,jul,ago,sep] ultimos 5 meses
                },
                {
                    label: "Descripción de Producto",
                    backgroundColor: "#FAA43A",
                    borderColor: "#FAA43A",
                    data: descripcionDeProducto, //valores de [may,jun,jul,ago,sep] ultimos 5 meses
                },
                {
                    label: "Plano mal Acotado",
                    backgroundColor: "#a668f2",
                    borderColor: "#a668f2",
                    data: planoMalAcotado, //valores de [may,jun,jul,ago,sep] ultimos 5 meses
                },
                {
                    label: "Error tipo Sustrato",
                    backgroundColor: "#F17CB0",
                    borderColor: "#F17CB0",
                    data: errorTipoSustrato, //valores de [may,jun,jul,ago,sep] ultimos 5 meses
                },
                {
                    label: "Falta CAD para corte",
                    backgroundColor: "#3f95a6",
                    borderColor: "#3f95a6",
                    data: faltaCadParaCorte, //valores de [may,jun,jul,ago,sep] ultimos 5 meses
                },
                {
                    label: "Falta OT Chileexpress",
                    backgroundColor: "#7b36e3",
                    borderColor: "#7b36e3",
                    data: faltaOTChileexpress, //valores de [may,jun,jul,ago,sep] ultimos 5 meses
                },
                {
                    label: "Falta OT Laboratorio",
                    backgroundColor: "#bd6b1e",
                    borderColor: "#bd6b1e",
                    data: faltaOTLaboratorio, //valores de [may,jun,jul,ago,sep] ultimos 5 meses
                },
            ],
        },
        options: {
            tooltips: {
                displayColors: true,
                callbacks: {
                    mode: "x",
                },
            },
            scales: {
                xAxes: [
                    {
                        stacked: true,
                        gridLines: {
                            display: false,
                        },
                    },
                ],
                yAxes: [
                    {
                        stacked: true,
                        ticks: {
                            beginAtZero: true,
                        },
                        type: "linear",
                        scaleLabel: {
                            display: true,
                            labelString: "Rechazos",
                        },
                    },
                ],
            },
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                display: false,
            },
        },
    });
}

// REPORTE DE OTS ACTIVAS POR MES Y USUARIO

// REPORT: Gestión de OT por mes:
function generar_reporte_ots_activas_por_area_cantidad(
    totalSolicitudesAsignadasAlArea,
    otsAsignadasPorUsuario
) {
    // CANTIDAD VS TODAS LAS SOLICITUDES: +++++++++++++++++++++++++++++++++++++++++++
    var ctx = document.getElementById("myChart").getContext("2d");
    var data = {
        // labels: mesesSeleccionados,
        datasets: [
            {
                label: "Solicitudes Asignadas",
                backgroundColor: "#3aaa35",
                borderColor: "rgb(255, 99, 132)",
                data: [totalSolicitudesAsignadasAlArea], //valores de [may,jun,jul,ago,sep] ultimos 5 meses
            },
        ],
    };
    var options = {
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Cantidad",
                    },
                },
            ],
        },
        layout: {
            padding: {
                bottom: -50,
            },
        },
        legend: {
            display: false,
        },
        title: {
            display: true,
            text: "Cantidad OT's Gestionadas",
            padding: 15,
        },
        // events: false,
        tooltips: {
            callbacks: {
                title: function () { },
            },
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var chart = new Chart(ctx, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data,
        // Configuration options go here
        options: options,
    });

    // Ots Asignadas Por Usuario: +++++++++++++++++++++++++++++++++++++++++++
    var ctx2 = document.getElementById("myChart2").getContext("2d");
    var data2 = {
        // labels: mesesSeleccionados,
        datasets: otsAsignadasPorUsuario,
    };
    var options2 = {
        scales: {
            xAxes: [
                {
                    barPercentage: 1,
                    categoryPercentage: 1,
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Cantidad",
                    },
                },
            ],
        },
        layout: {
            padding: {
                bottom: -5,
            },
        },
        legend: {
            display: false,
        },
        title: {
            display: true,
            text: "Cantidad OT's Gestionadas por Usuario",
            padding: 15,
        },
        // events: false,
        tooltips: {
            callbacks: {
                title: function () { },
            },
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx2, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data2,
        // Configuration options go here
        options: options2,
    });
}

function generar_reporte_ots_activas_por_area_y_usuario(
    totalSolicitudesEnArea,
    otsAsignadasEnAreaPorUsuario
) {
    // DIAS VS TODAS LAS SOLICITUDES: +++++++++++++++++++++++++++++++++++++++++++
    var ctx3 = document.getElementById("myChart3").getContext("2d");
    var data3 = {
        // labels: mesesSeleccionados,
        datasets: [
            {
                label: "Solicitudes en Area",
                backgroundColor: "#3aaa35",
                borderColor: "rgb(255, 99, 132)",
                data: [totalSolicitudesEnArea], //valores de [may,jun,jul,ago,sep]
            },
        ],
    };
    var options3 = {
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Cantidad",
                    },
                },
            ],
        },
        layout: {
            padding: {
                bottom: -50,
            },
        },
        legend: {
            display: false,
        },
        title: {
            display: true,
            text: "Cantidad OT's en Proceso",
            padding: 15,
        },
        // events: false,
        tooltips: {
            callbacks: {
                title: function () { },
            },
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var chart = new Chart(ctx3, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data3,
        // Configuration options go here
        options: options3,
    });

    // DIAS VS CADA TIPO DE SOLICITUD: +++++++++++++++++++++++++++++++++++++++++++
    var ctx4 = document.getElementById("myChart4").getContext("2d");
    var data4 = {
        // labels: mesesSeleccionados,
        datasets: otsAsignadasEnAreaPorUsuario,
    };
    var options4 = {
        scales: {
            xAxes: [
                {
                    barPercentage: 1,
                    categoryPercentage: 1,
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Cantidad",
                    },
                },
            ],
        },
        layout: {
            padding: {
                bottom: -5,
            },
        },
        legend: {
            display: false,
        },
        title: {
            display: true,
            text: "Cantidad OT's en Proceso por Usuario",
            padding: 15,
        },
        // events: false,
        tooltips: {
            callbacks: {
                title: function () { },
            },
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx4, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data4,
        // Configuration options go here
        options: options4,
    });
}

// REPORTE DE TIEMPOS POR OTS ASIGNADAS
function generar_reporte_ots_activas_tiempos_solicitudes_asignadas(
    tiempoSolicitudesAsignadasAlArea,
    tiempoOtsAsignadasPorUsuario
) {
    // CANTIDAD VS TODAS LAS SOLICITUDES: +++++++++++++++++++++++++++++++++++++++++++
    var ctx = document.getElementById("myChart5").getContext("2d");
    var data = {
        // labels: mesesSeleccionados,
        datasets: [
            {
                label: "Días Solicitudes Asignadas",
                backgroundColor: "#3aaa35",
                borderColor: "rgb(255, 99, 132)",
                data: [tiempoSolicitudesAsignadasAlArea], //valores de [may,jun,jul,ago,sep] ultimos 5 meses
            },
        ],
    };
    var options = {
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Días",
                    },
                },
            ],
        },
        layout: {
            padding: {
                bottom: -50,
            },
        },
        legend: {
            display: false,
        },
        title: {
            display: true,
            text: "Tiempo Total OT's Gestionadas",
            padding: 15,
        },
        // events: false,
        tooltips: {
            callbacks: {
                title: function () { },
            },
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var chart = new Chart(ctx, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data,
        // Configuration options go here
        options: options,
    });

    // Ots Asignadas Por Usuario: +++++++++++++++++++++++++++++++++++++++++++
    var ctx2 = document.getElementById("myChart6").getContext("2d");
    var data2 = {
        // labels: mesesSeleccionados,
        datasets: tiempoOtsAsignadasPorUsuario,
    };
    var options2 = {
        scales: {
            xAxes: [
                {
                    barPercentage: 1,
                    categoryPercentage: 1,
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Días",
                    },
                },
            ],
        },
        layout: {
            padding: {
                bottom: -5,
            },
        },
        legend: {
            display: false,
        },
        title: {
            display: true,
            text: "Tiempo Total OT's Gestionadas por Usuario",
            padding: 15,
        },
        // events: false,
        tooltips: {
            callbacks: {
                title: function () { },
            },
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx2, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data2,
        // Configuration options go here
        options: options2,
    });
}

function generar_reporte_ots_activas_tiempos_solicitudes_asignadas_en_area(
    tiempoSolicitudesEnArea,
    tiempoOtsAsignadasEnAreaPorUsuario
) {
    // DIAS VS TODAS LAS SOLICITUDES: +++++++++++++++++++++++++++++++++++++++++++
    var ctx3 = document.getElementById("myChart7").getContext("2d");
    var data3 = {
        // labels: mesesSeleccionados,
        datasets: [
            {
                label: "Días Solicitudes en Area",
                backgroundColor: "#3aaa35",
                borderColor: "rgb(255, 99, 132)",
                data: [tiempoSolicitudesEnArea], //valores de [may,jun,jul,ago,sep]
            },
        ],
    };
    var options3 = {
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Días",
                    },
                },
            ],
        },

        layout: {
            padding: {
                bottom: -50,
            },
        },
        legend: {
            display: false,
        },
        title: {
            display: true,
            text: "Tiempo Total OT's en Proceso",
            padding: 15,
        },
        // events: false,
        tooltips: {
            callbacks: {
                title: function () { },
            },
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var chart = new Chart(ctx3, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data3,
        // Configuration options go here
        options: options3,
    });

    // DIAS VS CADA TIPO DE SOLICITUD: +++++++++++++++++++++++++++++++++++++++++++
    var ctx4 = document.getElementById("myChart8").getContext("2d");
    var data4 = {
        // labels: mesesSeleccionados,
        datasets: tiempoOtsAsignadasEnAreaPorUsuario,
    };
    var options4 = {
        scales: {
            xAxes: [
                {
                    barPercentage: 1,
                    categoryPercentage: 1,
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Días",
                    },
                },
            ],
        },
        layout: {
            padding: {
                bottom: -5,
            },
        },
        legend: {
            display: false,
        },
        title: {
            display: true,
            text: "Tiempo Total OT's en Proceso por Usuario",
            padding: 15,
        },
        // events: false,
        tooltips: {
            callbacks: {
                title: function () { },
            },
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx4, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data4,
        // Configuration options go here
        options: options4,
    });
}

// REPORTE DE TIEMPOS PROMEDIOS POR OTS ASIGNADAS
function generar_reporte_ots_activas_tiempos_promedio_solicitudes_asignadas(
    tiempoPromedioSolicitudesAsignadasAlArea,
    tiempoPromedioOtsAsignadasPorUsuario
) {
    // CANTIDAD VS TODAS LAS SOLICITUDES: +++++++++++++++++++++++++++++++++++++++++++
    var ctx = document.getElementById("myChart9").getContext("2d");
    var data = {
        // labels: mesesSeleccionados,
        datasets: [
            {
                label: "Días Promedio Solicitudes Asignadas",
                backgroundColor: "#3aaa35",
                borderColor: "rgb(255, 99, 132)",
                data: [tiempoPromedioSolicitudesAsignadasAlArea], //valores de [may,jun,jul,ago,sep] ultimos 5 meses
            },
        ],
    };
    var options = {
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Días Promedio",
                    },
                },
            ],
        },
        layout: {
            padding: {
                bottom: -50,
            },
        },
        legend: {
            display: false,
        },
        title: {
            display: true,
            text: "Tiempo Promedio OT's Gestionadas",
            padding: 15,
        },
        // events: false,
        tooltips: {
            callbacks: {
                title: function () { },
            },
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var chart = new Chart(ctx, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data,
        // Configuration options go here
        options: options,
    });

    // Ots Asignadas Por Usuario: +++++++++++++++++++++++++++++++++++++++++++
    var ctx2 = document.getElementById("myChart10").getContext("2d");
    var data2 = {
        // labels: mesesSeleccionados,
        datasets: tiempoPromedioOtsAsignadasPorUsuario,
    };
    var options2 = {
        scales: {
            xAxes: [
                {
                    barPercentage: 1,
                    categoryPercentage: 1,
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Días Promedio",
                    },
                },
            ],
        },
        layout: {
            padding: {
                bottom: -5,
            },
        },
        legend: {
            display: false,
        },
        title: {
            display: true,
            text: "Tiempo Promedio OT's Gestionadas por Usuario",
            padding: 15,
        },
        // events: false,
        tooltips: {
            callbacks: {
                title: function () { },
            },
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx2, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data2,
        // Configuration options go here
        options: options2,
    });
}

function generar_reporte_ots_activas_tiempos_promedio_solicitudes_asignadas_en_area(
    tiempoPromedioSolicitudesEnArea,
    tiempoPromedioOtsAsignadasEnAreaPorUsuario
) {
    // DIAS VS TODAS LAS SOLICITUDES: +++++++++++++++++++++++++++++++++++++++++++
    var ctx3 = document.getElementById("myChart11").getContext("2d");
    var data3 = {
        // labels: mesesSeleccionados,
        datasets: [
            {
                label: "Días Promedio Solicitudes en Area",
                backgroundColor: "#3aaa35",
                borderColor: "rgb(255, 99, 132)",
                data: [tiempoPromedioSolicitudesEnArea], //valores de [may,jun,jul,ago,sep]
            },
        ],
    };
    var options3 = {
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Días Promedio",
                    },
                },
            ],
        },
        layout: {
            padding: {
                bottom: -50,
            },
        },
        legend: {
            display: false,
        },
        title: {
            display: true,
            text: "Tiempo Promedio OT's en Proceso",
            padding: 15,
        },
        // events: false,
        tooltips: {
            callbacks: {
                title: function () { },
            },
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var chart = new Chart(ctx3, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data3,
        // Configuration options go here
        options: options3,
    });

    // DIAS VS CADA TIPO DE SOLICITUD: +++++++++++++++++++++++++++++++++++++++++++
    var ctx4 = document.getElementById("myChart12").getContext("2d");
    var data4 = {
        // labels: mesesSeleccionados,
        datasets: tiempoPromedioOtsAsignadasEnAreaPorUsuario,
    };
    var options4 = {
        scales: {
            xAxes: [
                {
                    barPercentage: 1,
                    categoryPercentage: 1,
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Días Promedio",
                    },
                },
            ],
        },
        layout: {
            padding: {
                bottom: -5,
            },
        },
        legend: {
            display: false,
        },
        title: {
            display: true,
            text: "Tiempo Promedio OT's en Proceso por Usuario",
            padding: 15,
        },
        // events: false,
        tooltips: {
            callbacks: {
                title: function () { },
            },
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx4, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data4,
        // Configuration options go here
        options: options4,
    });
}
// FINN REPORTE OTS ACTIVAS POR AREA Y USUARIOS

// REPORTES SECUNDARIO GESTIONES ACTIVAS
function generar_reporte_secundario_gestiones_activas(
    estados,
    cantidadPorEstado,
    tiempoPromedioPorEstado
) {
    // DIAS VS TODAS LAS SOLICITUDES: +++++++++++++++++++++++++++++++++++++++++++
    var ctx3 = document.getElementById("myChart3").getContext("2d");
    var data3 = {
        labels: estados,
        datasets: [
            {
                label: "",
                backgroundColor: "#3AAA35",
                borderColor: "rgb(255, 99, 132)",
                data: cantidadPorEstado, //valores de [may,jun,jul,ago,sep]
            },
        ],
    };
    var options3 = {
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 20,
                bottom: 0,
            },
        },
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Cantidad",
                    },
                },
            ],
        },
        legend: {
            display: false,
            position: "bottom",
        },
        title: {
            display: false,
            text: "Title",
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var chart = new Chart(ctx3, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data3,
        // Configuration options go here
        options: options3,
    });
    // DIAS VS CADA TIPO DE SOLICITUD: +++++++++++++++++++++++++++++++++++++++++++
    var ctx4 = document.getElementById("myChart4").getContext("2d");
    var data4 = {
        labels: estados,
        datasets: [
            {
                label: "",
                backgroundColor: "#14880F",
                borderColor: "rgb(255, 99, 132)",
                data: tiempoPromedioPorEstado, //valores de [may,jun,jul,ago,sep]
            },
        ],
    };
    var options4 = {
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 20,
                bottom: 0,
            },
        },
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Tiempo Promedio (Días)",
                    },
                },
            ],
        },
        legend: {
            display: false,
            position: "bottom",
        },
        title: {
            display: false,
            text: "Title",
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx4, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data4,
        // Configuration options go here
        options: options4,
    });
}

// REPORT: GRAFICO DE GESTIONES ACTIVAS POR ESTADO POR VENDEDOR

function generar_reporte_estados_por_vendedor(estados, responsables) {
    // Configuracion general:++++++++++++++++++++++++++++++++++++++++++++++++++++
    Chart.defaults.doughnutLabels = Chart.helpers.clone(
        Chart.defaults.doughnut
    );
    var helpers = Chart.helpers;
    var defaults = Chart.defaults;
    Chart.controllers.doughnutLabels = Chart.controllers.doughnut.extend({
        updateElement: function (arc, index, reset) {
            var _this = this;
            var chart = _this.chart,
                chartArea = chart.chartArea,
                opts = chart.options,
                animationOpts = opts.animation,
                arcOpts = opts.elements.arc,
                centerX = (chartArea.left + chartArea.right) / 2,
                centerY = (chartArea.top + chartArea.bottom) / 2,
                startAngle = opts.rotation, // non reset case handled later
                endAngle = opts.rotation, // non reset case handled later
                dataset = _this.getDataset(),
                circumference =
                    reset && animationOpts.animateRotate
                        ? 0
                        : arc.hidden
                            ? 0
                            : _this.calculateCircumference(dataset.data[index]) *
                            (opts.circumference / (2.0 * Math.PI)),
                innerRadius =
                    reset && animationOpts.animateScale ? 0 : _this.innerRadius,
                outerRadius =
                    reset && animationOpts.animateScale ? 0 : _this.outerRadius,
                custom = arc.custom || {},
                valueAtIndexOrDefault = helpers.getValueAtIndexOrDefault;

            helpers.extend(arc, {
                // Utility
                _datasetIndex: _this.index,
                _index: index,

                // Desired view properties
                _model: {
                    x: centerX + chart.offsetX,
                    y: centerY + chart.offsetY,
                    startAngle: startAngle,
                    endAngle: endAngle,
                    circumference: circumference,
                    outerRadius: outerRadius,
                    innerRadius: innerRadius,
                    label: valueAtIndexOrDefault(
                        dataset.label,
                        index,
                        chart.data.labels[index]
                    ),
                },

                draw: function () {
                    var ctx = this._chart.ctx,
                        vm = this._view,
                        sA = vm.startAngle,
                        eA = vm.endAngle,
                        opts = this._chart.config.options;

                    var labelPos = this.tooltipPosition();
                    var segmentLabel =
                        (vm.circumference / opts.circumference) * 100;

                    ctx.beginPath();

                    ctx.arc(vm.x, vm.y, vm.outerRadius, sA, eA);
                    ctx.arc(vm.x, vm.y, vm.innerRadius, eA, sA, true);

                    ctx.closePath();
                    ctx.strokeStyle = vm.borderColor;
                    ctx.lineWidth = vm.borderWidth;

                    ctx.fillStyle = vm.backgroundColor;

                    ctx.fill();
                    ctx.lineJoin = "bevel";

                    if (vm.borderWidth) {
                        ctx.stroke();
                    }

                    if (vm.circumference > 0.15) {
                        // Trying to hide label when it doesn't fit in segment
                        ctx.beginPath();
                        ctx.font = helpers.fontString(
                            opts.defaultFontSize,
                            opts.defaultFontStyle,
                            opts.defaultFontFamily
                        );
                        ctx.fillStyle = "#000";
                        ctx.textBaseline = "top";
                        ctx.textAlign = "center";

                        // Round percentage in a way that it always adds up to 100%
                        var pos_y = labelPos.y - 10;
                        ctx.fillText(
                            "" + dataset.data[index],
                            labelPos.x,
                            pos_y
                        );
                        ctx.fillText(
                            segmentLabel.toFixed(0) + "%",
                            labelPos.x,
                            pos_y + 12
                        );
                    }
                },
            });

            var model = arc._model;
            model.backgroundColor = custom.backgroundColor
                ? custom.backgroundColor
                : valueAtIndexOrDefault(
                    dataset.backgroundColor,
                    index,
                    arcOpts.backgroundColor
                );
            model.hoverBackgroundColor = custom.hoverBackgroundColor
                ? custom.hoverBackgroundColor
                : valueAtIndexOrDefault(
                    dataset.hoverBackgroundColor,
                    index,
                    arcOpts.hoverBackgroundColor
                );
            model.borderWidth = custom.borderWidth
                ? custom.borderWidth
                : valueAtIndexOrDefault(
                    dataset.borderWidth,
                    index,
                    arcOpts.borderWidth
                );
            model.borderColor = custom.borderColor
                ? custom.borderColor
                : valueAtIndexOrDefault(
                    dataset.borderColor,
                    index,
                    arcOpts.borderColor
                );

            // Set correct angles if not resetting
            if (!reset || !animationOpts.animateRotate) {
                if (index === 0) {
                    model.startAngle = opts.rotation;
                } else {
                    model.startAngle = _this.getMeta().data[
                        index - 1
                    ]._model.endAngle;
                }

                model.endAngle = model.startAngle + model.circumference;
            }

            arc.pivot();
        },
    });
    var ctxx = [];
    var myDoughnutChart = [];
    var data = [];
    for (const responsable in responsables) {
        if (responsables.hasOwnProperty(responsable)) {
            // Por cada vendedor activamos su grafica

            // Todas las Areas: ++++++++++++++++++++++++++++++++++++++++++++++++++++

            ctxx[responsable] = document
                .getElementById("myChartVendedor" + responsable)
                .getContext("2d");

            data[responsable] = {
                datasets: [
                    {
                        label: "",
                        backgroundColor: [
                            "#60BD68", //"Proceso de Ventas"
                            "#5DA5DA", //"Consulta Cliente",
                            "#F15854", //"Rechazada",
                            "#FAA43A", //"Espera de OC",
                            "#806939", //"Falta definición del Cliente",
                            "#73e2e6", //"Visto Bueno Cliente",
                            // "#5DA5DA", //'Informacion Erronea',
                            // "#DECF3F", //'Medida Erronea',
                            // "#F15854", //'No Viable Por Restricciones',
                            // "#a668f2", //'Plano Mal Acotado'
                        ],
                        borderColor: "#fff",
                        data: responsables[responsable].estados, //Descripción de producto ,Error de digitación, Error tipo Sustrato, Falta Informacion , Falta Muestra Fisica, Formato Imagen Inadecuado, Informacion Erronea, Medida Erronea, No viable por Restricciones, Plano mal acotado
                    },
                ],

                // These labels appear in the legend and in the tooltips when hovering different arcs
                labels: [
                    "Proceso de Ventas",
                    "Consulta Cliente",
                    "Rechazada",
                    "Espera de OC",
                    "Falta definición del Cliente",
                    "Visto Bueno Cliente",
                ],
            };

            var options = {
                responsive: true,
                legend: {
                    display: false,
                    position: "top",
                },
                title: {
                    display: true,
                    text: responsables[responsable].fullname,
                    position: "bottom",
                    fontSize: "16",
                    fontFamily:
                        "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
                    fontColor: "#000",
                },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                },
            };

            // invocar grafico:
            myDoughnutChart[responsable] = new Chart(ctxx[responsable], {
                // The type of chart we want to create
                type: "doughnutLabels",
                // The data for our dataset
                data: data[responsable],
                // Configuration options go here
                options: options,
                plugins: [
                    {
                        id: "total",
                        beforeDraw: function (chart) {
                            const width = chart.chart.width;
                            const height = chart.chart.height;
                            const ctx = chart.chart.ctx;
                            ctx.restore();
                            const fontSize = (height / 114).toFixed(2);
                            ctx.font = fontSize + "em sans-serif";
                            ctx.textBaseline = "middle";
                            var total = data[
                                responsable
                            ].datasets[0].data.reduce(function (
                                previousValue,
                                currentValue,
                                currentIndex,
                                array
                            ) {
                                return previousValue + currentValue;
                            });
                            const text = total;
                            const textX = Math.round(
                                (width - ctx.measureText(text).width) / 2
                            );
                            const textY = height / 2.4;
                            ctx.fillText(text, textX, textY);
                            ctx.save();
                        },
                    },
                ],
            });
            // debugger
        }
    }
}

// REPORT: INDICADORES SALA DE MUESTRA
function generar_reporte_indicadores_sala_muestra(
    mesesSeleccionados,
    muestrasTerminadas,
    muestrasTerminadasPorOt,
    muestrasTerminadasGrafica
) {
    // Cantidad de Sumatoria de Muestreas: +++++++++++++++++++++++++++++++++++++++++++
    console.log(muestrasTerminadasGrafica);
    var ctx = document.getElementById("myChart").getContext("2d");
    var data = {
        labels: mesesSeleccionados,
        datasets: [
            {
                label: "Cantidad De Muestras",
                backgroundColor: "#22951d",
                borderColor: "rgb(255, 99, 132)",
                data: muestrasTerminadasGrafica['muestrasTerminadas'],
            },
            {
                label: "OT Con Muestras",
                backgroundColor: "#6ad766",
                borderColor: "rgb(255, 99, 132)",
                data: muestrasTerminadasGrafica['muestrasTerminadasPorOt'],
            },
        ],
    };
    var options = {
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Cantidad",
                    },
                },
            ],
        },
        legend: {
            display: false,
            position: "bottom",
        },
        title: {
            display: true,
            text: "",
        },
        // events: false,
        tooltips: {
            enabled: true,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };

    // invocar grafico:
    var chart = new Chart(ctx, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data,
        // Configuration options go here
        options: options,
    });

}

// FIN REPORTES SECUNDARIO GESTIONES ACTIVAS

// REPORTE DISEÑO ESTRUCTURAL Y SALA DE MUESTRA

function generar_reporte_cantidad_ot_por_area_mes_actual(array_cantidad_ot_por_area, array_keys_ot_por_area) {
    // CANTIDAD: +++++++++++++++++++++++++++++++++++++++++++
    var ctx = document.getElementById("myChart").getContext("2d");
    var data = {
        labels: array_keys_ot_por_area,
        // labels: ['DE', 'SM','DG','PC','C'],
        // datasets: [
        //     {
        //         label: "",
        //         backgroundColor: "#3AAA35",
        //         borderColor: "rgb(255, 99, 132)",
        //         data: array_cantidad_ot_por_area,
        //     },
        // ],
        datasets: [
            {
                label: array_keys_ot_por_area,
                backgroundColor: ["#B2841E", "#F09606", "#F0CC06", "#9EC304", "#238407"],
                data: array_cantidad_ot_por_area,
            },

        ],
    };
    var options = {
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 20,
                bottom: 0,
            },
        },
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Cantidad",
                    },
                },
            ],
        },
        legend: {
            display: false,
            position: "bottom",
        },
        title: {
            display: true,
            text: "OT QUE ESTÁN EN CADA ÁREA MES ACTUAL",
            padding: 15,
        },
        events: false,
        tooltips: {
            enabled: true,
        },
        hover: {
            animationDuration: 0,
        },

        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var chart = new Chart(ctx, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data,
        // Configuration options go here
        options: options,
    });

}

function generar_reporte_tiempos_ot(
    promedio_anio_anterior_titulo,
    promedio_anio_anterior_desarrollo,
    promedio_anio_anterior_muestra,
    promedio_anio_anterior_diseno,
    promedio_anio_anterior_catalogacion,
    promedio_anio_anterior_precatalogacion,
    promedio_mes_actual_anio_anterior_titulo,
    promedio_mes_actual_anio_anterior_desarrollo,
    promedio_mes_actual_anio_anterior_muestra,
    promedio_mes_actual_anio_anterior_diseno,
    promedio_mes_actual_anio_anterior_catalogacion,
    promedio_mes_actual_anio_anterior_precatalogacion,
    promedio_mes_anterior_al_actual_titulo,
    promedio_mes_anterior_al_actual_desarrollo,
    promedio_mes_anterior_al_actual_muestra,
    promedio_mes_anterior_al_actual_diseno,
    promedio_mes_anterior_al_actual_catalogacion,
    promedio_mes_anterior_al_actual_precatalogacion,
    promedio_mes_actual_titulo,
    promedio_mes_actual_desarrollo,
    promedio_mes_actual_muestra,
    promedio_mes_actual_diseno,
    promedio_mes_actual_catalogacion,
    promedio_mes_actual_precatalogacion,
    promedio_anio_actual_titulo,
    promedio_anio_actual_desarrollo,
    promedio_anio_actual_muestra,
    promedio_anio_actual_diseno,
    promedio_anio_actual_catalogacion,
    promedio_anio_actual_precatalogacion
) {
    // Promedio: +++++++++++++++++++++++++++++++++++++++++++
    var ctx4 = document.getElementById("myChart4").getContext("2d");
    var data4 = {
        labels: [promedio_anio_anterior_titulo, promedio_mes_actual_anio_anterior_titulo, promedio_mes_anterior_al_actual_titulo, promedio_mes_actual_titulo, promedio_anio_actual_titulo],
        datasets: [
            {
                label: "Dis. Estructural",
                backgroundColor: "#B2841E",
                // borderColor: "rgb(255, 99, 132)",
                data: [promedio_anio_anterior_desarrollo, promedio_mes_actual_anio_anterior_desarrollo, promedio_mes_anterior_al_actual_desarrollo, promedio_mes_actual_desarrollo, promedio_anio_actual_desarrollo],
            },
            {
                label: "Sala de Muestras",
                backgroundColor: "#F09606",
                // borderColor: "rgb(255, 99, 132)",
                data: [promedio_anio_anterior_muestra, promedio_mes_actual_anio_anterior_muestra, promedio_mes_anterior_al_actual_muestra, promedio_mes_actual_muestra, promedio_anio_actual_muestra],
            },
            {
                label: "Dis. Gráfico",
                backgroundColor: "#F0CC06",
                // borderColor: "rgb(255, 99, 132)",
                data: [promedio_anio_anterior_diseno, promedio_mes_actual_anio_anterior_diseno, promedio_mes_anterior_al_actual_diseno, promedio_mes_actual_diseno, promedio_anio_actual_diseno],
            },
            {
                label: "Precatalogación",
                backgroundColor: "#9EC304",
                // borderColor: "rgb(255, 99, 132)",
                data: [promedio_anio_anterior_precatalogacion, promedio_mes_actual_anio_anterior_precatalogacion, promedio_mes_anterior_al_actual_precatalogacion, promedio_mes_actual_precatalogacion, promedio_anio_actual_precatalogacion],
            },
            {
                label: "Catalogación",
                backgroundColor: "#238407",
                // borderColor: "rgb(255, 99, 132)",
                data: [promedio_anio_anterior_catalogacion, promedio_mes_actual_anio_anterior_catalogacion, promedio_mes_anterior_al_actual_catalogacion, promedio_mes_actual_catalogacion, promedio_anio_actual_catalogacion],
            },
        ],
    };
    var options4 = {
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 20,
                bottom: 0,
            },
        },
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Días",
                    },
                },
            ],
        },
        legend: {
            position: "bottom",
        },
        title: {
            display: true,
            text: "TIEMPOS OT",
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },

        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx4, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data4,
        // Configuration options go here
        options: options4,
    });

}

function generar_reporte_tiempos_ot_mes_actual(
    promedio_mes_actual_titulo,
    promedio_mes_actual_desarrollo,
    promedio_mes_actual_muestra,
    promedio_mes_actual_diseno,
    promedio_mes_actual_catalogacion,
    promedio_mes_actual_precatalogacion

) {
    // Promedio: +++++++++++++++++++++++++++++++++++++++++++
    var ctx10 = document.getElementById("myChart4").getContext("2d");
    var data10 = {
        labels: [promedio_mes_actual_titulo],
        datasets: [
            {
                label: "Dis. Estructural",
                backgroundColor: "#B2841E",
                // borderColor: "rgb(255, 99, 132)",
                data: [promedio_mes_actual_desarrollo],
            },
            {
                label: "Sala de Muestras",
                backgroundColor: "#F09606",
                // borderColor: "rgb(255, 99, 132)",
                data: [promedio_mes_actual_muestra],
            },
            {
                label: "Dis. Gráfico",
                backgroundColor: "#F0CC06",
                // borderColor: "rgb(255, 99, 132)",
                data: [promedio_mes_actual_diseno],
            },
            {
                label: "Precatalogación",
                backgroundColor: "#9EC304",
                // borderColor: "rgb(255, 99, 132)",
                data: [promedio_mes_actual_precatalogacion],
            },
            {
                label: "Catalogación",
                backgroundColor: "#238407",
                // borderColor: "rgb(255, 99, 132)",
                data: [promedio_mes_actual_catalogacion],
            },
        ],
    };
    var options10 = {
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 20,
                bottom: 0,
            },
        },
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Días(24 Hrs/día)",
                    },
                },
            ],
        },
        legend: {
            position: "bottom",
        },
        title: {
            display: true,
            text: "TIEMPOS OT",
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },

        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx10, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data10,
        // Configuration options go here
        options: options10,
    });

}

function generar_reporte_tiempos_ot_mes_actual_anio_anterior(
    promedio_mes_actual_anio_anterior_titulo,
    promedio_mes_actual_anio_anterior_desarrollo,
    promedio_mes_actual_anio_anterior_muestra,
    promedio_mes_actual_anio_anterior_diseno,
    promedio_mes_actual_anio_anterior_catalogacion,
    promedio_mes_actual_anio_anterior_precatalogacion
) {
    // Promedio: +++++++++++++++++++++++++++++++++++++++++++
    var ctx11 = document.getElementById("myChart11").getContext("2d");
    var data11 = {
        labels: [promedio_mes_actual_anio_anterior_titulo],
        datasets: [
            {
                label: "Dis. Estructural",
                backgroundColor: "#B2841E",
                // borderColor: "rgb(255, 99, 132)",
                data: [promedio_mes_actual_anio_anterior_desarrollo],
            },
            {
                label: "Sala de Muestras",
                backgroundColor: "#F09606",
                // borderColor: "rgb(255, 99, 132)",
                data: [promedio_mes_actual_anio_anterior_muestra],
            },
            {
                label: "Dis. Gráfico",
                backgroundColor: "#F0CC06",
                // borderColor: "rgb(255, 99, 132)",
                data: [promedio_mes_actual_anio_anterior_diseno],
            },
            {
                label: "Precatalogación",
                backgroundColor: "#9EC304",
                // borderColor: "rgb(255, 99, 132)",
                data: [promedio_mes_actual_anio_anterior_precatalogacion],
            },
            {
                label: "Catalogación",
                backgroundColor: "#238407",
                // borderColor: "rgb(255, 99, 132)",
                data: [promedio_mes_actual_anio_anterior_catalogacion],
            },
        ],
    };
    var options11 = {
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 20,
                bottom: 0,
            },
        },
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Días",
                    },
                },
            ],
        },
        legend: {
            position: "bottom",
        },
        title: {
            display: true,
            text: "TIEMPOS OT",
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },

        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx11, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data11,
        // Configuration options go here
        options: options11,
    });

}

function generar_reporte_cantidad_historico_ot_por_area_mes_actual(array_cantidad_historico_ot_por_area, array_keys_historico_ot_por_area) {
    // CANTIDAD: +++++++++++++++++++++++++++++++++++++++++++
    var ctx = document.getElementById("myChart2").getContext("2d");
    var data = {
        labels: array_keys_historico_ot_por_area,
        datasets: [
            {
                label: array_keys_historico_ot_por_area,
                backgroundColor: ["#B2841E", "#F09606", "#F0CC06", "#9EC304", "#238407"],
                data: array_cantidad_historico_ot_por_area,
            },

        ],
    };
    var options = {
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 20,
                bottom: 0,
            },
        },
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Cantidad",
                    },
                },
            ],
        },
        legend: {
            display: false,
            position: "bottom",
        },
        title: {
            display: true,
            text: "Nº OT QUE HAN PASADO POR ÁREA",
            padding: 15,
        },
        events: false,
        tooltips: {
            enabled: true,
        },
        hover: {
            animationDuration: 0,
        },

        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var chart = new Chart(ctx, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data,
        // Configuration options go here
        options: options,
    });

}

function generar_reporte_ot_con_muestras(
    promedio_ot_con_muestras_cortadas_anio_anterior_titulo,
    promedio_ot_con_muestras_cortadas_anio_anterior,
    ot_con_muestras_cortadas_mes_actual_anio_anterior_titulo,
    ot_con_muestras_cortadas_mes_actual_anio_anterior_cantidad,
    ot_con_muestras_cortadas_mes_anio_actual_titulo,
    ot_con_muestras_cortadas_mes_anio_actual_cantidad,
    promedio_ot_con_muestras_cortadas_anio_actual_titulo,
    promedio_ot_con_muestras_cortadas_anio_actual,
    promedio_id_con_muestras_cortadas_anio_anterior_titulo,
    promedio_id_con_muestras_cortadas_anio_anterior,
    id_con_muestras_cortadas_mes_actual_anio_anterior_titulo,
    id_con_muestras_cortadas_mes_actual_anio_anterior_cantidad,
    id_con_muestras_cortadas_mes_anio_actual_titulo,
    id_con_muestras_cortadas_mes_anio_actual_cantidad,
    promedio_id_con_muestras_cortadas_anio_actual_titulo,
    promedio_id_con_muestras_cortadas_anio_actual,
    promedio_muestras_cortadas_anio_anterior_titulo,
    promedio_muestras_cortadas_anio_anterior,
    muestras_cortadas_mes_actual_anio_anterior_titulo,
    muestras_cortadas_mes_actual_anio_anterior_cantidad,
    muestras_cortadas_mes_anio_actual_titulo,
    muestras_cortadas_mes_anio_actual_cantidad,
    promedio_muestras_cortadas_anio_actual_titulo,
    promedio_muestras_cortadas_anio_actual
) {
    // OT CON MUESTRAS  +++++++++++++++++++++++++++++++++++++++++++
    var ctx5 = document.getElementById("myChart5").getContext("2d");
    var data5 = {
        labels: [promedio_ot_con_muestras_cortadas_anio_anterior_titulo,
            ot_con_muestras_cortadas_mes_actual_anio_anterior_titulo,
            ot_con_muestras_cortadas_mes_anio_actual_titulo,
            promedio_ot_con_muestras_cortadas_anio_actual_titulo
        ],
        datasets: [
            {
                backgroundColor: ["#F09606", "#F0CC06", "#9EC304", "#238407"],
                data: [promedio_ot_con_muestras_cortadas_anio_anterior,
                    ot_con_muestras_cortadas_mes_actual_anio_anterior_cantidad,
                    ot_con_muestras_cortadas_mes_anio_actual_cantidad,
                    promedio_ot_con_muestras_cortadas_anio_actual
                ],
            },

        ],
    };
    var options5 = {
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 20,
                bottom: 20,
            },
        },
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Cantidad",
                    },
                },
            ],
        },
        legend: {
            display: false,
            position: "bottom",
        },
        title: {
            display: true,
            text: "OT CON MUESTRAS CORTADAS",
            padding: 20,
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx5, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data5,
        // Configuration options go here
        options: options5,
    });

    // ID CORTADAS: +++++++++++++++++++++++++++++++++++++++++++
    var ctx6 = document.getElementById("myChart6").getContext("2d");
    var data6 = {
        labels: [promedio_id_con_muestras_cortadas_anio_anterior_titulo,
            id_con_muestras_cortadas_mes_actual_anio_anterior_titulo,
            id_con_muestras_cortadas_mes_anio_actual_titulo,
            promedio_id_con_muestras_cortadas_anio_actual_titulo
        ],
        datasets: [
            {
                backgroundColor: ["#F09606", "#F0CC06", "#9EC304", "#238407"],
                data: [promedio_id_con_muestras_cortadas_anio_anterior,
                    id_con_muestras_cortadas_mes_actual_anio_anterior_cantidad,
                    id_con_muestras_cortadas_mes_anio_actual_cantidad,
                    promedio_id_con_muestras_cortadas_anio_actual
                ],
            },

        ],
    };
    var options6 = {
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 20,
                bottom: 20,
            },
        },
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Cantidad",
                    },
                },
            ],
        },
        legend: {
            display: false,
            position: "bottom",
        },
        title: {
            display: true,
            text: "ID SOLICITADAS CORTADAS",
            padding: 20,
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx6, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data6,
        // Configuration options go here
        options: options6,
    });

    // MUESTRAS SOLICITADAS CORTADAS: +++++++++++++++++++++++++++++++++++++++++++
    var ctx7 = document.getElementById("myChart7").getContext("2d");
    var data7 = {
        labels: [promedio_muestras_cortadas_anio_anterior_titulo,
            muestras_cortadas_mes_actual_anio_anterior_titulo,
            muestras_cortadas_mes_anio_actual_titulo,
            promedio_muestras_cortadas_anio_actual_titulo],
        datasets: [
            {
                backgroundColor: ["#F09606", "#F0CC06", "#9EC304", "#238407"],
                data: [promedio_muestras_cortadas_anio_anterior,
                    muestras_cortadas_mes_actual_anio_anterior_cantidad,
                    muestras_cortadas_mes_anio_actual_cantidad,
                    promedio_muestras_cortadas_anio_actual],
            },

        ],
    };
    var options7 = {
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 20,
                bottom: 20,
            },
        },
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {
                    scaleLabel: {
                        display: true,
                        labelString: "Cantidad",
                    },
                },
            ],
        },
        legend: {
            display: false,
            position: "bottom",
        },
        title: {
            display: true,
            text: "MUESTRAS SOLICITADAS CORTADAS",
            padding: 20,
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx7, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data7,
        // Configuration options go here
        options: options7,
    });

}

function generar_reporte_tiempo_primera_muestra(prom_tiempo_creacion, prom_tiempo_DE, cantidad_ot) {

    var ctx = document.getElementById("myChart").getContext("2d");
    var data = {
        labels: ['Creación OT', 'Diseño Estructural'],
        datasets: [
            {
                backgroundColor: ["#F09606", "#F0CC06"],
                data: [prom_tiempo_creacion, prom_tiempo_DE],
            },

        ],
    };
    var options = {
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 15,
                bottom: 15,
            },
        },
        scales: {

            yAxes: [

                {
                    ticks: {
                        min: 0,
                        stepSize: 1,

                    },
                    scaleLabel: {
                        display: true,
                        labelString: "Días (24 Hrs.)",
                    }
                },



            ],
        },
        legend: {
            display: false,
            position: "bottom",
        },
        title: {
            display: true,
            text: "Promedio Duración Días Primera Muestra cantidad de OT: " + cantidad_ot,
            padding: 20,
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data,
        // Configuration options go here
        options: options,
    });


}

function generar_reporte_tiempo_primera_muestra_ano(prom_tiempo_creacion_ene, prom_tiempo_de_ene, prom_tiempo_creacion_feb, prom_tiempo_de_feb,
    prom_tiempo_creacion_mar, prom_tiempo_de_mar, prom_tiempo_creacion_abr, prom_tiempo_de_abr,
    prom_tiempo_creacion_may, prom_tiempo_de_may, prom_tiempo_creacion_jun, prom_tiempo_de_jun,
    prom_tiempo_creacion_jul, prom_tiempo_de_jul, prom_tiempo_creacion_ago, prom_tiempo_de_ago,
    prom_tiempo_creacion_sep, prom_tiempo_de_sep, prom_tiempo_creacion_oct, prom_tiempo_de_oct,
    prom_tiempo_creacion_nov, prom_tiempo_de_nov, prom_tiempo_creacion_dic, prom_tiempo_de_dic,
    prom_tiempo_creacion_ano_grafico, prom_tiempo_DE_ano_grafico, ano_selecionado) {
    // Promedio: +++++++++++++++++++++++++++++++++++++++++++
    var ctx4 = document.getElementById("myChartAno").getContext("2d");
    var data4 = {
        labels: ["Ene.", "Feb.", "Mar.", "Abr.", "May.", "Jun.", "Jul.", "Ago.", "Sep.", "Oct.", "Nov.", "Dic.", "Prom. Anual"],
        datasets: [
            {
                label: "Creacion OT",
                backgroundColor: "#F09606",
                // borderColor: "rgb(255, 99, 132)",
                data: [prom_tiempo_creacion_ene,
                    prom_tiempo_creacion_feb,
                    prom_tiempo_creacion_mar,
                    prom_tiempo_creacion_abr,
                    prom_tiempo_creacion_may,
                    prom_tiempo_creacion_jun,
                    prom_tiempo_creacion_jul,
                    prom_tiempo_creacion_ago,
                    prom_tiempo_creacion_sep,
                    prom_tiempo_creacion_oct,
                    prom_tiempo_creacion_nov,
                    prom_tiempo_creacion_dic,
                    prom_tiempo_creacion_ano_grafico],
            },
            {
                label: "Diseño Estructural",
                backgroundColor: "#F0CC06",
                // borderColor: "rgb(255, 99, 132)",
                data: [prom_tiempo_de_ene,
                    prom_tiempo_de_feb,
                    prom_tiempo_de_mar,
                    prom_tiempo_de_abr,
                    prom_tiempo_de_may,
                    prom_tiempo_de_jun,
                    prom_tiempo_de_jul,
                    prom_tiempo_de_ago,
                    prom_tiempo_de_sep,
                    prom_tiempo_de_oct,
                    prom_tiempo_de_nov,
                    prom_tiempo_de_dic,
                    prom_tiempo_DE_ano_grafico],
            },
        ],
    };
    var options4 = {
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 20,
                bottom: 0,
            },
        },
        scales: {
            xAxes: [
                {
                    gridLines: {
                        offsetGridLines: true,
                    },
                },
            ],
            yAxes: [
                {

                    ticks: {
                        min: 0,
                        stepSize: 5,

                    },
                    scaleLabel: {
                        display: true,
                        labelString: "Días (24 Hrs.)",
                    },
                },
            ],
        },
        legend: {
            position: "bottom",
        },
        title: {
            display: true,
            text: "Promedio Duracion Meses del Año " + ano_selecionado,
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },

        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx4, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data4,
        // Configuration options go here
        options: options4,
    });

}

function generar_reporte_ot_osorno_puentealto(cantidad_ot_puente_alto, cantidad_ot_osorno, cantidad_ot_otro) {

    var ctx = document.getElementById("myChart1").getContext("2d");
    var data = {
        labels: ['Osorno', 'Puente Alto', 'Externo'],
        datasets: [
            {
                backgroundColor: ["#F09606", "#F0CC06", "#9EC304"],
                data: [cantidad_ot_osorno, cantidad_ot_puente_alto, cantidad_ot_otro],
            },

        ],
    };
    var options = {
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 15,
                bottom: 15,
            },
        },
        scales: {
            yAxes: [
                {
                    ticks: {
                        min: 0,
                        stepSize: 50,

                    },
                    scaleLabel: {
                        display: true,
                        labelString: "Cantidad",
                    }
                },
            ],
        },
        legend: {
            display: false,
            position: "bottom",
        },
        title: {
            display: true,
            text: "OT por Sala de Corte",
            padding: 20,
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data,
        // Configuration options go here
        options: options,
    });
}

function generar_reporte_muestras_osorno_puentealto(cantidad_muestras_puente_alto, cantidad_muestras_osorno, cantidad_muestras_otro) {

    var ctx = document.getElementById("myChart2").getContext("2d");
    var data = {
        labels: ['Osorno', 'Puente Alto', 'Externo'],
        datasets: [
            {
                backgroundColor: ["#F09606", "#F0CC06", "#9EC304"],
                data: [cantidad_muestras_osorno, cantidad_muestras_puente_alto, cantidad_muestras_otro],
            },

        ],
    };
    var options = {
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 15,
                bottom: 15,
            },
        },
        scales: {
            yAxes: [
                {
                    ticks: {
                        min: 0,
                        stepSize: 100,

                    },
                    scaleLabel: {
                        display: true,
                        labelString: "Muestras",
                    }
                },
            ],
        },
        legend: {
            display: false,
            position: "bottom",
        },
        title: {
            display: true,
            text: "Muestras Solicitadas por Sala de Corte",
            padding: 20,
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data,
        // Configuration options go here
        options: options,
    });


}

function generar_reporte_cortadas_osorno_puentealto(cantidad_muestras_cortadas_puente_alto, cantidad_muestras_osorno_cortadas, cantidad_muestras_cortadas_otro) {

    var ctx = document.getElementById("myChart3").getContext("2d");
    var data = {
        labels: ['Osorno', 'Puente Alto', 'Externo'],
        datasets: [
            {
                backgroundColor: ["#F09606", "#F0CC06", "#9EC304"],
                data: [cantidad_muestras_osorno_cortadas, cantidad_muestras_cortadas_puente_alto, cantidad_muestras_cortadas_otro],
            },
        ],
    };
    var options = {
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 15,
                bottom: 15,
            },
        },
        scales: {
            yAxes: [
                {
                    ticks: {
                        min: 0,
                        stepSize: 100,
                    },
                    scaleLabel: {
                        display: true,
                        labelString: "Cantidad",
                    }
                },
            ],
        },
        legend: {
            display: false,
            position: "bottom",
        },
        title: {
            display: true,
            text: "Muestras Cortadas por Sala de Corte",
            padding: 20,
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data,
        // Configuration options go here
        options: options,
    });


}

function generar_reporte_cantidad_ot_recepcion_diseno(cantidad_recepcion_prinflex, cantidad_recepcion_graphicbox,
    cantidad_recepcion_flexoclean, cantidad_recepcion_artfactory) {

    var ctx = document.getElementById("myChart1").getContext("2d");
    var data = {
        labels: ['Prinflex', 'Graphicbox', 'Flexoclean', 'Artfactory'],
        datasets: [
            {
                backgroundColor: ["#F09606", "#F0CC06", "#9EC304", "#238407"],
                data: [cantidad_recepcion_prinflex, cantidad_recepcion_graphicbox, cantidad_recepcion_flexoclean, cantidad_recepcion_artfactory],
            },

        ],
    };
    var options = {
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 15,
                bottom: 15,
            },
        },
        scales: {
            yAxes: [
                {
                    ticks: {
                        min: 0,
                        stepSize: 50,

                    },
                    scaleLabel: {
                        display: true,
                        labelString: "Cantidad ",
                    }
                },
            ],
        },
        legend: {
            display: false,
            position: "bottom",
        },
        title: {
            display: true,
            text: " OT Recepcionadas mes en curso",
            padding: 20,
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data,
        // Configuration options go here
        options: options,
    });
}

function generar_reporte_cantidad_enviadas_diseno(cantidad_enviadas_prinflex, cantidad_enviadas_graphicbox,
    cantidad_enviadas_flexoclean, cantidad_enviadas_artfactory) {

    var ctx = document.getElementById("myChart4").getContext("2d");
    var data = {
        labels: ['Prinflex', 'Graphicbox', 'Flexoclean', 'Artfactory'],
        datasets: [
            {
                backgroundColor: ["#F09606", "#F0CC06", "#9EC304", "#238407"],
                data: [cantidad_enviadas_prinflex, cantidad_enviadas_graphicbox, cantidad_enviadas_flexoclean, cantidad_enviadas_artfactory],
            },

        ],
    };
    var options = {
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 15,
                bottom: 15,
            },
        },
        scales: {
            yAxes: [
                {
                    ticks: {
                        min: 0,
                        stepSize: 50,

                    },
                    scaleLabel: {
                        display: true,
                        labelString: "Cantidad ",
                    }
                },
            ],
        },
        legend: {
            display: false,
            position: "bottom",
        },
        title: {
            display: true,
            text: "Total OT Enviadas mes en curso",
            padding: 20,
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data,
        // Configuration options go here
        options: options,
    });
}

function generar_reporte_cantidad_ot_pendiente_diseno(cantidad_pendiente_prinflex, cantidad_pendiente_graphicbox,
    cantidad_pendiente_flexoclean, cantidad_pendiente_artfactory) {

    var ctx = document.getElementById("myChart2").getContext("2d");
    var data = {
        labels: ['Prinflex', 'Graphicbox', 'Flexoclean', 'Artfactory'],
        datasets: [
            {
                backgroundColor: ["#F09606", "#F0CC06", "#9EC304", "#238407"],
                data: [cantidad_pendiente_prinflex, cantidad_pendiente_graphicbox, cantidad_pendiente_flexoclean, cantidad_pendiente_artfactory],
            },
        ],
    };

    var options = {
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 15,
                bottom: 15,
            },
        },
        scales: {
            yAxes: [
                {
                    ticks: {
                        min: 0,
                        stepSize: 50,
                    },
                    scaleLabel: {
                        display: true,
                        labelString: "Cantidad",
                    }
                },
            ],
        },
        legend: {
            display: false,
            position: "bottom",
        },
        title: {
            display: true,
            text: "OT Pendientes de Recepcion a la Fecha",
            padding: 20,
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data,
        // Configuration options go here
        options: options,
    });
}

function generar_reporte_cantidad_ot_tiempo_diseno(prom_tiempo_duracion_prinflex, prom_tiempo_duracion_graphicbox,
    prom_tiempo_duracion_flexoclean, prom_tiempo_duracion_artfactory) {

    var ctx = document.getElementById("myChart3").getContext("2d");
    var data = {
        labels: ['Prinflex', 'Graphicbox', 'Flexoclean', 'Artfactory'],
        datasets: [
            {
                backgroundColor: ["#F09606", "#F0CC06", "#9EC304", "#238407"],
                data: [prom_tiempo_duracion_prinflex, prom_tiempo_duracion_graphicbox, prom_tiempo_duracion_flexoclean, prom_tiempo_duracion_artfactory],
            },
        ],
    };

    var options = {
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 15,
                bottom: 15,
            },
        },
        scales: {
            yAxes: [
                {
                    ticks: {
                        min: 0,
                        stepSize: 50,
                    },
                    scaleLabel: {
                        display: true,
                        labelString: "Dias Habiles (9,5 Hrs.)",
                    }
                },
            ],
        },
        legend: {
            display: false,
            position: "bottom",
        },
        title: {
            display: true,
            text: "Promedio Duración",
            padding: 20,
        },
        events: false,
        tooltips: {
            enabled: false,
        },
        hover: {
            animationDuration: 0,
        },
        animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                ctx.font = Chart.helpers.fontString(
                    Chart.defaults.global.defaultFontSize,
                    Chart.defaults.global.defaultFontStyle,
                    Chart.defaults.global.defaultFontFamily
                );
                ctx.textAlign = "center";
                ctx.textBaseline = "bottom";
                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            },
        },
    };
    // invocar grafico:
    var myBarChart = new Chart(ctx, {
        // The type of chart we want to create
        type: "bar",
        // The data for our dataset
        data: data,
        // Configuration options go here
        options: options,
    });
}

/*
function generar_reporte_cantidad_ot_envio_diseno(  cantidad_envio_prinflex,cantidad_envio_graphicbox,
    cantidad_envio_flexoclean,cantidad_envio_artfactory){

var ctx = document.getElementById("myChart1").getContext("2d");
var data = {
labels: ['Prinflex','Graphicbox','Flexoclean','Artfactory'],
datasets: [
{
backgroundColor: ["#F09606", "#F0CC06", "#9EC304", "#238407"],
data: [cantidad_envio_prinflex, cantidad_envio_graphicbox,cantidad_envio_flexoclean,cantidad_envio_artfactory],
},

],
};
var options = {
layout: {
padding: {
left: 0,
right: 0,
top: 15,
bottom: 15,
},
},
scales: {
yAxes: [
{
ticks: {
min: 0,
stepSize: 50,

},
scaleLabel: {
display: true,
labelString: "Cantidad ",
}
},
],
},
legend: {
display: false,
position: "bottom",
},
title: {
display: true,
text: " OT Recepcionadas mes en curso",
padding: 20,
},
events: false,
tooltips: {
enabled: false,
},
hover: {
animationDuration: 0,
},
animation: {
duration: 1,
onComplete: function () {
var chartInstance = this.chart,
ctx = chartInstance.ctx;
ctx.font = Chart.helpers.fontString(
Chart.defaults.global.defaultFontSize,
Chart.defaults.global.defaultFontStyle,
Chart.defaults.global.defaultFontFamily
);
ctx.textAlign = "center";
ctx.textBaseline = "bottom";
this.data.datasets.forEach(function (dataset, i) {
var meta = chartInstance.controller.getDatasetMeta(i);
meta.data.forEach(function (bar, index) {
var data = dataset.data[index];
ctx.fillText(data, bar._model.x, bar._model.y - 5);
});
});
},
},
};
// invocar grafico:
var myBarChart = new Chart(ctx, {
// The type of chart we want to create
type: "bar",
// The data for our dataset
data: data,
// Configuration options go here
options: options,
});
}*/
