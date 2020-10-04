$(document).ready(function(){
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
        //console.log(`${selectTendencias.val()} | ${jt.id} | all`);         
        divPrintCards.load('ajax_support.php', 
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
        divPrintCards.load('ajax_support.php', 
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
        divPrintCards.load('ajax_support.php', 
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
