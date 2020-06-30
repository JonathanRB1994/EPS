// Add the following code if you want the name of the file appear on select
$(".custom-file-input").on("change", function() {
    var fileName = $(this).val().split("\\").pop();
    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});

$(document).ready(function(){
    $("#tableUsers").tablesorter({
        theme : "bootstrap",
        widthFixed: true    
      });

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

    $('input[name=addImage]').click(function(){
        var inputValue = $(this).attr("value");
        var fileInput = document.getElementById("divImage");
        if(inputValue==="yes"){
            fileInput.removeAttribute("hidden");
        }else{
            fileInput.setAttribute("hidden", false); // no effect
        }        
    });

    $('input[name=delImage]').click(function(){
        var inputValue = $(this).attr("value");
        var fileInput = document.getElementById("divAddSourceImage");
        if(inputValue==="no"){
            fileInput.removeAttribute("hidden");
        }else{
            fileInput.setAttribute("hidden", false); // no effect
        }        
    });

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
});

