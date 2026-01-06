$(document).ready(function() {
    //Funcionalidades bootstrap
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover();

    //setear version de bootstrap
    $.fn.selectpicker.Constructor.BootstrapVersion = "4";

    // Default options for bootstrap select
    $.fn.selectpicker.Constructor.DEFAULTS.actionsBox = true;

    // Inicia el selector multiple
    $("select[multiple]").selectpicker();

    //Inicia el datepicker
    $(".datepicker").datepicker({
        language: "es"
    });

    //Filtro sencillo de tipos de archivos cargados
    $('[name="filetype-areas"]').on("change", function() {
        var area = $(this).val();
        $('#area_selected').val(area);
        var type = $('#option_selected').val();

        $('[data-container="files"]')
            .find('[data-component="archivo_area_1"]')
            .hide();
        $('[data-container="files"]')
            .find('[data-component="archivo_area_2"]')
            .hide();
        $('[data-container="files"]')
            .find('[data-component="archivo_area_3"]')
            .hide();
        $('[data-container="files"]')
            .find('[data-component="archivo_area_4"]')
            .hide();
        $('[data-container="files"]')
            .find('[data-component="archivo_area_5"]')
            .hide();
        $('[data-container="files"]')
            .find('[data-component="archivo_area_6"]')
            .hide();
        if (type == "all") {
            $('[data-container="files"]')
                .find('[data-component="archivo_'+area+'"]')
                .show();
        } else {
            $('[data-container="files"]')
                .find('[data-component="archivo_'+area+'"]')
                .hide();
            $('[data-container="files"]')
                .find('[data-file-type="'+area+'_'+type+'"]')
                .show();
        }
    }); 

    //Filtro sencillo de tipos de archivos cargados
    $('[name="filetype-options"]').on("change", function() {
        var type = $(this).val();
        $('#option_selected').val(type);
        var area =  $('#area_selected').val();
       
        if (type == "all") {
            $('[data-container="files"]')
                .find('[data-component="archivo_'+area+'"]')
                .show();
        }else {
            
            $('[data-container="files"]')
                .find('[data-component="archivo_'+area+'"]')
                .hide();
            $('[data-container="files"]')
                .find('[data-file-type="'+area+'_'+type+'"]')
                .show();
        }
    }); 

    $('#area_selected').val("area_1");
    $('#option_selected').val("all");
    
    $('[data-container="files"]')
        .find('[data-component="archivo_area_2"]')
        .hide();
    $('[data-container="files"]')
        .find('[data-component="archivo_area_3"]')
        .hide();
    $('[data-container="files"]')
        .find('[data-component="archivo_area_4"]')
        .hide();
    $('[data-container="files"]')
        .find('[data-component="archivo_area_5"]')
        .hide();
    $('[data-container="files"]')
        .find('[data-component="archivo_area_6"]')
        .hide();

   
    
   


    //Al cambiar el tipo de gestion, bloquea a qué area dirigirse si es carga de archivos
    // $('[name="gestion-form-tipo"]').on("change", function() {
    //     var value = $(this).val();
    //     if (value == "carga") {
    //         $('[name="gestion-form-area"]').prop("disabled", true);
    //     }
    // });

    $("#custom-files-container").on(
        "click",
        '[data-action="delete"]',
        function() {
            var parent = $(this).closest(".custom-file-item");
            parent.remove();
        }
    );

    //Comportamiento de menú header
    //==========================================================>
    $(".dropmenu").on("click", function() {
        $(this)
            .find("ul")
            .slideDown();
    });
    $(".dropmenu").on("mouseleave", function() {
        $(this)
            .find("ul")
            .slideUp();
    });
    $(document).on("click", function(e) {
        var target = e.target;
        condition1 = $(target).is(".dropmenu");
        condition2 = $(target)
            .parents()
            .is(".dropmenu");
        if (!(condition1 || condition2)) {
            $(".dropmenu ul").slideUp();
        }
    });

    //Inicia el datepicker
    $(".datepicker").datepicker({
        language: "es"
    });

    //Con este código no permite que dos datepickers sean incoherentes
    //==========================================================>
    $(document).ready(function() {
        $("#datePicker1").on("changeDate", function(e) {
            var minDate = new Date(e.date.valueOf());
            $("#datePicker2").datepicker("setStartDate", minDate);
            // $('#datePicker2').data("DateTimePicker").minDate(e.date);
        });
        $("#datePicker2").on("changeDate", function(e) {
            var maxDate = new Date(e.date.valueOf());
            $("#datePicker1").datepicker("setEndDate", maxDate);
            // $('#datePicker1').data("DateTimePicker").maxDate(e.date);
        });
    });
});
