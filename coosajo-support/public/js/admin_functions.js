// configuracion para la erramienta de subir imagenes
$(".custom-file-input").on("change", function() {
    var fileName = $(this).val().split("\\").pop();
    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});

$(document).ready(function(){
    // Tabla de ususarios, arreglar visualmente
    $("#tableUsers").tablesorter({
        theme : "bootstrap",
        widthFixed: true    
      });

    // Mostrar el autocompletado de la busqueda
    $('#search').typeahead({        
        source: function (query, result) {
            $.ajax({
                url: "search.php",
                method: "POST",
                data: {query:query}, 
                dataType: "json",                       
                success: function (data) {
                    result($.map(data, function (item) {
                        return item;
                    }));
                }
            });
        }
    });

    // Mostrar/ocultar opciones al crear/editar un paso
    $('input[name=addImage]').click(function(){
        var inputValue = $(this).attr("value");
        var fileInput = document.getElementById("divImage");
        if(inputValue==="yes"){
            fileInput.removeAttribute("hidden");
        }else{
            fileInput.setAttribute("hidden", false); // no effect
        }        
    });

    // Mostrar/ocultar opciones al crear/editar un paso
    $('input[name=delImage]').click(function(){
        var inputValue = $(this).attr("value");
        var fileInput = document.getElementById("divAddSourceImage");
        if(inputValue==="no"){
            fileInput.removeAttribute("hidden");
        }else{
            fileInput.setAttribute("hidden", false); // no effect
        }        
    });

    // Mostrar/ocultar opciones al crear/editar un paso
    $('input[name=addURL]').click(function(){
        var inputValue = $(this).attr("value");
        
        var fileInput = document.getElementById("divFileInput");
        var urlInput = document.getElementById("divImageOrURL");
        if(inputValue==="image"){
            fileInput.removeAttribute("hidden");
            urlInput.setAttribute("hidden", false);
        }else if(inputValue==="URL"){
            fileInput.setAttribute("hidden", false); 
            urlInput.removeAttribute("hidden");
        }        
    });

    // Boton mostrar y ocultar filtros
    var hiddenFilters = true;  // Por defecto los filtros estan ocultos
    $('#btn_hidden_filters').click(function(){                
        var pathIconDown = document.getElementById("path_down_icon");
        var pathIconUp = document.getElementById("path_up_icon");
        var divFilters = document.getElementById("div_filters");
        if(hiddenFilters){
            pathIconDown.setAttribute("hidden", true);
            pathIconUp.removeAttribute("hidden");
            divFilters.removeAttribute("hidden");
            hiddenFilters=false;
        }else{
            pathIconDown.removeAttribute("hidden");
            pathIconUp.setAttribute("hidden", true);
            divFilters.setAttribute("hidden", true);
            hiddenFilters=true;
        }        
    });

    
    const hiddenAll = $('#hiddenAll');
    const divPrintCards = $('#div_print_cards');
    const selectTendencias = $('#select_tendencias');
    const selectTipoIncidencia = $('#select_tipo_incidencia');
    const selectIncidencia = $('#select_incidencia');    

    // Deteccion del cambio de filtro tendencias
    selectTendencias.change(function (){
        var jt = JSON.parse(selectTipoIncidencia.val());
        var ji = JSON.parse(selectIncidencia.val());
        //console.log(`${selectTendencias.val()} | ${jt.id} | ${ji.id}`);
                
        divPrintCards.load('ajax_admin_support.php', 
        {
            tendencia: selectTendencias.val(),
            tipoIncidencia: `${jt.id}`,
            incidencia: `${ji.id}`
        }, PrintResponse);
    });

    // Deteccion del cambio de filtro tipo incidencia
    selectTipoIncidencia.change(function (){
        var jt = JSON.parse(selectTipoIncidencia.val());
        //console.log(`${selectTendencias.val()} | ${jt.id} | all | ${hiddenAll.val()}`); 
        //console.log(` ${jt.id} | all | ${hiddenAll.val()}`); 

        divPrintCards.load('ajax_admin_support.php', 
        {
            tendencia: selectTendencias.val(),
            tipoIncidencia: `${jt.id}`,
            incidencia: "all"
        }, PrintResponse);

        selectIncidencia.load('ajax_ticket.php',
        {
            other: "no",
            hiddenAll: hiddenAll.val(),
            tipoIncidencia: `${jt.id}`
        }, PrintResponse);
    });

    // Deteccion del cambio de filtro incidencia
    selectIncidencia.change(function (){
        var jt = JSON.parse(selectTipoIncidencia.val());
        var ji = JSON.parse(selectIncidencia.val());
        //console.log(`${selectTendencias.val()} | ${jt.id} | ${ji.id}`); 
        //console.log(` ${jt.id} | ${ji.id}`); 

        divPrintCards.load('ajax_admin_support.php', 
        {
            tendencia: selectTendencias.val(),
            tipoIncidencia: `${jt.id}`,
            incidencia: `${ji.id}`
        }, PrintResponse);
    });

    function PrintResponse (responseText, textStatus, request) {
        //console.log(responseText);
        //console.log(responseText);
        //console.log(responseText);
    }
});
