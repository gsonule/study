<script type="text/javascript">
$("#ecp_name").keydown(function(e) {
    if($('#ecp-list li').is(':visible')) {   //scroll code
        var top         = $('#ecp-list').scrollTop(); 
        var ulheight    = $('#ecp-list')[0].scrollHeight; 
        var liheight    = $('#ecp-list').find('.selected').prev().outerHeight();
    } 
    switch (e.which) {
        case 40:
            e.preventDefault(); // prevent moving the cursor
            var next_cont_value = $('#ecp-list li:not(:last-child).selected').removeClass('selected').next().addClass('selected');
            $val = next_cont_value.text(); 
            $val1 = next_cont_value.attr('value'); 
            if($val){
                 $("#ecp_name").val($val);
            }
            if($val1){
                 $("#ecp_id").val($val1);
            }
           //scroll code
            var nextHeight  = top + liheight; 
            var maxHeight   = ulheight - liheight;
            if((nextHeight <= maxHeight)){
                $('#ecp-list').scrollTop(top + liheight);
            } else {
                $('#ecp-list').scrollTop(0); //scrolling
            }
        break;
        case 38:
            e.preventDefault(); // prevent moving the cursor
            var prv_cont_value = $('#ecp-list li:not(:eq(1)).selected').removeClass('selected').prev().addClass('selected');

            $val = prv_cont_value.text(); 
            $val1 = prv_cont_value.attr('value'); 

            if($val){
                 $("#ecp_name").val($val);
            }
            if($val1){
                 $("#ecp_id").val($val1);
            }
            //scroll code
            if(top != 0) {
                $('#ecp-list').scrollTop(top - liheight); //scrolling
            }
        break;
    }
});

$("#ecp_name").keyup(function(e) {    
    if ($(this).val().length >= 3 && e.which != 40 && e.which != 38) {
        $("#ecp_suggesstions").html('');
        
        fetch_ecp_suggestions();     
    }
});
</script>

********************************************************************
<?= (!preg_match("~^(?:f|ht)tps?://~i", $website)) ? 'http://'."$website" : "$website" ?>
********************************************************************