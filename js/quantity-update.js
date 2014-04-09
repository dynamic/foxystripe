/**
 * Created by nhorstmeier on 4/9/14.
 */

$(document).ready(function(){

    $('.foxycart_qty input').change(function(){
        var newValue = $('.foxycart_qty input').val();
        getUpdatedSHA(newValue);
    });

});

function getUpdatedSHA(newValue){

    $.ajax({
        type: 'GET',
        //data: {'code': code},
        url: pageURL+'quantityUpdate/'+newValue,
        beforeSend: function(){
            return ;
        },
        success: function(updatedValue){
            $('.foxycart_qty input').attr('name', updatedValue);
        },
        error: function(er){
            console.log(er.responseText);
        },
        complete: function(){

        }
    });

}