var lastScaletsData;

function getScaletsList(silence = false) {

    $.ajax({
        type: 'GET',
        url:  "/admin/cloud/index/get_scalets_list",            
        cache: false,
        beforeSend: function() {
            if (!silence) {
                $('#response_status').html('<img src="/public/images/system/ajax-loader.gif" alt="loading"> Refreshing scalets list...');
            }
        },
        success: function(data) {              

            var result = "";

            if (lastScaletsData !== data) {

                lastScaletsData = data;
            
                var items = jQuery.parseJSON(data);              

                items.forEach(function(item) {

                    result = result + '<tr>';
                    result = result + ' <td>'+item.ctid+'</td>';
                    result = result + ' <td style="text-align:center;">'+item.name+'</td>';
                    result = result + ' <td style="text-align:center;">'+item.created+'</td>';
                    result = result + ' <td style="text-align:center;">'+item.locked+'</td>';
                    result = result + ' <td style="text-align:center;">'+item.status+'</td>';
                    result = result + ' <td style="text-align:center;">'+item.public_address.address+'</td>';
                    if (item.locked == true) {
                        result = result + ' <td style="text-align:center;"><img src="/public/images/system/ajax-loader.gif" alt="loading"></td>';
        
                    } else {
                        result = result + ' <td style="text-align:center;"><span class="action action-delete" onclick="deleteScalet('+item.ctid+')">Delete</span></td>';
                    }
                    result = result + '</tr>';

                });             
                $('#scalets-table tbody').html(result);
            }
            if (!silence) {                                                  
                $('#response_status').html('Scalets list updated at ' + new Date()); 
            }               
        },
        error:  function(xhr, str) {
            if (!silence) { 
                $('#response_status').html('Возникла ошибка: ' + xhr.responseCode);
            }
        }
    });

}

function deleteScalet(ctid) {

    $.ajax({
        type: 'POST',
        url:  "/admin/cloud/index/delete_scalet",            
        cache: false,
        data: {
            'ctid': ctid
        },
        beforeSend: function() {
            $('#response_status').html('<img src="/public/images/system/ajax-loader.gif" alt="loading"> Deleting scalet #'+ctid+' ...');
        },
        success: function(data) {
            
            var result = jQuery.parseJSON(data);              

            if (result.ctid == ctid) {
                $('#response_status').html('Deleted successfully ...'); 
                getScaletsList();   
            } else {
                $('#response_status').html('Something goes wrong ...'); 
                getScaletsList();
            }                                                 
                 
        },
        error:  function(xhr, str) {
            $('#response_status').html('Возникла ошибка: ' + xhr.responseCode);
        }
    });  

}

function createVPNScalet() {
    
    $.ajax({
        url:  "/admin/cloud/index/create_vpn_scalet",            
        cache: false,
        beforeSend: function() {
            $('#response_status').html('<img src="/public/images/system/ajax-loader.gif" alt="loading"> Creating new VPN server...');
        },
        success: function(data) {             
            
            getScaletsList();

            //var result = jQuery.parseJSON(data);              

        },
        error:  function(xhr, str) {
            $('#response_status').html('Error: ' + xhr.responseCode);
        }
    });     

}

function getScaletInfo(ctid) {

    return $.ajax({
        url:  "/admin/cloud/index/get_scalet_info",            
        cache: false,
        beforeSend: function() {
            //$('#response_status').text('Createing new VPN server...');
        },
        success: function(data) {                         
            scaletInfo = jQuery.parseJSON(data);   
        },
        error:  function(xhr, str) {
            $('#response_status').html('Error: ' + xhr.responseCode);
        }
    }); 

} 

$(document).ready(function(){

    getScaletsList();

    $(".action-refresh-scalets-table").click(function(){
        getScaletsList();
    });

    $('.action-create-scalet').click(function(){
        createVPNScalet();
    });

    setInterval(getScaletsList, 20000);

});