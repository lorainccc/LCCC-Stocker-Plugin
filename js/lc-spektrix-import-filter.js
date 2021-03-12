jQuery('#filterDate').click(function(){

    alert('heeeelllloooooo');


    $month = jQuery( "#month option:selected" ).text();

    location.href = '/stocker/wp-admin/edit.php?post_type=lccc_events&page=lc-event-import&month=' + $month;
});    

