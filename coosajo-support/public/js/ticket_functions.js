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

    const hiddenAll = $('#hiddenAll');
    const selectTipoIncidencia = $('#select_tipo_incidencia');
    const selectIncidencia = $('#ticket_select_incidencia');    

    // Deteccion del cambio de filtro tipo incidencia
    selectTipoIncidencia.change(function (){        
        var jt = JSON.parse(selectTipoIncidencia.val());        
        console.log(`${jt.id} | all | ${hiddenAll.val()}`); 

        selectIncidencia.load('ajax_ticket.php',
        {
            other: "yes",
            hiddenAll: hiddenAll.val(),
            tipoIncidencia: `${jt.id}`
        }, PrintResponse);
    });

    const ticket_select_incidencia = $('#ticket_select_incidencia');  
    const divOther = document.getElementById("div_other");
    const other = document.getElementById("other");
    const ID_OTROS=255;
    ticket_select_incidencia.change(function (){         
        var ji = JSON.parse(ticket_select_incidencia.val());
        console.log(` ${ji.id} ${ji.name} `); 
        if( ji.id == ID_OTROS ){
            console.log(' Entre IF '); 
            divOther.removeAttribute("hidden");   
            other.required = true;             
        }else{
            console.log(' Entre ELSE'); 
            divOther.setAttribute("hidden", true);
            other.required = false;
        }
    });


    function PrintResponse (responseText, textStatus, request) {
        //console.log(responseText);
        //console.log(responseText);
        //console.log(responseText);
    }
});
