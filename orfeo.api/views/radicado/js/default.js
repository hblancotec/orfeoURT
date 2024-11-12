$(function() {

    $('#obtenerRadicado').submit(function() {
    
    
        var url = $(this).attr('action');
        var data = $(this).serialize();
        
        $.post(url, data, function(o) {
            
            console.log("Holaaaaaaaa");
            
            //alert("datos "+o+", Progreso"+status);
            //$('#listInserts').append('<div>' + o.text + '<a class="del" rel="'+ o.id +'" href="#">X</a></div>');        
        },'json');

        return false;
    });

});
