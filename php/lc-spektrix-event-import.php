<?php

function lc_add_event_import_menu_page() {

  add_submenu_page(
  'edit.php?post_type=lccc_events',													        // Parent Slug (Page to nest under)
  __( 'Event Import', 'lorainccc' ),                      	                // Page Title
  'Event Import',                                                      	    // Menu Title
  'manage_options',                                                      	// Capabilities
  'lc-event-import',                                                     	// Menu Slug
  'lc_event_import'                                                     	// Function
 );
}

add_action( 'admin_menu', 'lc_add_event_import_menu_page' );

// Render out Page Templates List

function lc_event_import(){
	?>
    <div style="display:block; width:100%; float:left; border-bottom: 1px solid #696969; margin: 0 0 20px 0;">
        <img style="float:right;" src="<?php echo str_replace('/php/', '', plugin_dir_url( __FILE__ ))?>/assets/images/lccc-logo.png" border="0">
        <h1 style="float:left; padding: 20px 0 0 0;">Events Import from Spektrix</h1>
    </div>
<?php

    //$sDomain = "https://system.spektrix.com/stockerartscenter_run1";
    $sDomain = "https://system.spektrix.com/stockerartscenter";

    $lc_event_import = $_GET['lc_eventimport'];
    $lc_event_sync = $_GET['lc_eventsync'];
    $lc_event_update = $_GET['lc_event_update'];

    if( !empty( $lc_event_import ) ){

        $requestUrl =  $sDomain . "/api/v3/events/" . $lc_event_import;

        $response = wp_remote_get( $requestUrl );

        $json = json_decode( $response['body'] );        

        //Retreive Venue Location
        $lc_venue_Request_Url = $sDomain . "/api/v3/instances/" . $lc_spektrix_id . "/plan";
        $lc_venue_Response = wp_remote_get( $lc_venue_Request_Url );
        $lc_venue_Json = json_decode( $lc_venue_Response['body'] );
        $lc_venue_Name = $lc_venue_Json->name;

        $lc_event_category = getWPCategoryfromSpektrix($json);

        //$lc_cat_id = get_term_by( 'slug', $lc_event_category, 'event_categories' );

        $lc_draftPost = array(
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_author' => $current_user->ID,
            'post_content' => $json->description,
            'post_status' => 'draft',
            'post_title' => $json->name,
            'post_type' => 'lccc_events',
        );
          
         // Insert Post into Database in draft status
         $newId = wp_insert_post($lc_draftPost);          

         wp_set_object_terms($newId, $lc_event_category, 'event_categories', false);


        update_post_meta( $newId, 'event_meta_box_stocker_spektrix_event_id', esc_attr( $json->id ) ); 
        update_post_meta( $newId, 'event_meta_box_event_location', esc_attr( $_POST['event_meta_box_event_location'] ) );
        update_post_meta( $newId, 'event_start_date', esc_attr( date_format( date_create( $json->firstInstanceDateTime ),"m/d/Y" ) ) );
        update_post_meta( $newId, 'start_date', esc_attr( date_format( date_create( $json->firstInstanceDateTime ),"m/d/Y" ) ) ); 
        update_post_meta( $newId, 'event_start_time', esc_attr( date_format( date_create( $json->firstInstanceDateTime ),"h:i:s A" ) ) ); 
        update_post_meta( $newId, 'event_end_date', esc_attr( date_format( date_create( $json->lastInstanceDateTime ),"m/d/Y" ) ) ); 
        update_post_meta( $newId, 'event_end_time', esc_attr( date_format( date_create( $json->lastInstanceDateTime ),"h:i:s A" ) ) ); 	
        update_post_meta( $newId, 'event_meta_box_event_end_date_and_time_', esc_attr( date_format( date_create( $json->lastInstanceDateTime ),"m/d/Y h:i:s A" ) ) );
        update_post_meta( $newId, 'event_meta_box_ticket_price_s_', esc_attr( $json->attribute_TicketPrice ) ); 	
        update_post_meta( $newId, 'event_meta_box_event_location', esc_attr( $lc_venue_Name ) );

        echo '<div>';
        echo '<h2>Importing "' . $json->name . '"</h2>';

        echo '  <p><a href="' . admin_url('post.php?action=edit&post=' . $newId) . '">Edit ' . $json->name . ' Event</a></p>';

        echo '  <p><a href="/stocker/wp-admin/edit.php?post_type=lccc_events&page=lc-event-import">Return to Event Importer</a></p>';
        echo '</div>';

    }elseif( !empty( $lc_event_update ) ){
        echo '<div>';
        $lc_event_temp = explode( "|", $lc_event_update );

        $lc_post_id = $lc_event_temp[0];
        $lc_spektrix_id = $lc_event_temp[1];

        $requestUrl =  $sDomain . "/api/v3/events/" . $lc_spektrix_id;

        $response = wp_remote_get( $requestUrl );

        $json = json_decode( $response['body'] );

        //Retreive Venue Location
        $lc_venue_Request_Url = $sDomain . "/api/v3/instances/" . $lc_spektrix_id . "/plan";
        $lc_venue_Response = wp_remote_get( $lc_venue_Request_Url );
        $lc_venue_Json = json_decode( $lc_venue_Response['body'] );
        $lc_venue_Name = $lc_venue_Json->name;

        $lc_event_category = getWPCategoryfromSpektrix($json);

        $lc_published_post = get_post( $lc_post_id );

        $lc_draftPost = array(
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_author' => $lc_published_post->post_author,
            'post_content' => $lc_published_post->post_content,
            'post_status' => 'draft',
            'post_title' => $lc_published_post->post_title,
            'post_type' => 'lccc_events',
        );

        $newId = wp_insert_post($lc_draftPost); 

        wp_set_object_terms( $newId, $lc_event_category, 'event_categories', false );

        //Retrieve Published Posts Featured Image
        $lc_thumbnail_id = get_post_thumbnail_id( $lc_post_id );

        if( !empty($lc_thumbnail_id ) ){
            set_post_thumbnail( $newId,  $lc_thumbnail_id);
        }

        //Updating Event Details from Spektrix

        update_post_meta( $newId, '_lc_publishedId', $lc_post_id );
        update_post_meta( $newId, 'event_meta_box_stocker_spektrix_event_id', esc_attr( $json->id ) ); 
        update_post_meta( $newId, 'event_start_date', esc_attr( date_format( date_create( $json->firstInstanceDateTime ),"m/d/Y" ) ) );
        update_post_meta( $newId, 'start_date', esc_attr( date_format( date_create( $json->firstInstanceDateTime ),"m/d/Y" ) ) ); 
        update_post_meta( $newId, 'event_start_time', esc_attr( date_format( date_create( $json->firstInstanceDateTime ),"h:i:s A" ) ) ); 
        update_post_meta( $newId, 'event_end_date', esc_attr( date_format( date_create( $json->lastInstanceDateTime ),"m/d/Y" ) ) ); 
        update_post_meta( $newId, 'event_end_time', esc_attr( date_format( date_create( $json->lastInstanceDateTime ),"h:i:s A" ) ) ); 	
        update_post_meta( $newId, 'event_meta_box_event_end_date_and_time_', esc_attr( date_format( date_create( $json->lastInstanceDateTime ),"m/d/Y h:i:s A" ) ) );	
        update_post_meta( $newId, 'event_meta_box_ticket_price_s_', esc_attr( $json->attribute_TicketPrice ) ); 	
        update_post_meta( $newId, 'event_meta_box_event_location', esc_attr( $lc_venue_Name ) );
 
        //Get Other Post Meta Fields
        // Add Post Meta from get_post_meta call array (false = return array)

        $lc_published_post_meta = get_post_meta($lc_post_id, '', false);

        foreach ( $lc_published_post_meta as $key => $value ){
            if ($key != '_edit_lock' && $key != '_edit_last' && $key != '_lc_publishedId' && $key != 'event_meta_box_stocker_spektrix_event_id' && $key != 'event_start_date' && $key != 'start_date' && $key != 'event_start_time' && $key != 'event_end_date' && $key != 'event_end_time' && $key != 'event_meta_box_event_end_date_and_time_' && $key != 'event_meta_box_ticket_price_s_' && $key != 'event_meta_box_event_location'){
                foreach ($value as $newvalue){
                add_post_meta($newId, $key, $newvalue, true);
                }
            }
        }

        echo '<h2>Updating "' . $json->name . '"</h2>';

        echo '  <p><a href="' . admin_url('post.php?action=edit&post=' . $newId) . '">Edit ' . $json->name . ' Event</a></p>';

        echo '  <p><a href="/stocker/wp-admin/edit.php?post_type=lccc_events&page=lc-event-import">Return to Event Importer</a></p>';


        echo '</div>';

    }elseif( !empty($lc_event_sync) )  {
        
        echo '<div>';
        
        $lc_spektrix_id = $lc_event_sync;

        $requestUrl =  $sDomain . "/api/v3/events/" . $lc_spektrix_id;

        $response = wp_remote_get( $requestUrl );

        $json = json_decode( $response['body'] );        

        //Retreive Venue Location
        $lc_venue_Request_Url = $sDomain . "/api/v3/instances/" . $lc_spektrix_id . "/plan";
        $lc_venue_Response = wp_remote_get( $lc_venue_Request_Url );
        $lc_venue_Json = json_decode( $lc_venue_Response['body'] );
        $lc_venue_Name = $lc_venue_Json->name;

        $lc_event_category = getWPCategoryfromSpektrix($json);
        $lc_published_post = get_page_by_title( $json->name, OBJECT, 'lccc_events' );
        $lc_post_id = $lc_published_post->ID;

        $lc_draftPost = array(
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_author' => $lc_published_post->post_author,
            'post_content' => $lc_published_post->post_content,
            'post_status' => 'draft',
            'post_title' => $lc_published_post->post_title,
            'post_type' => 'lccc_events',
        );

        $newId = wp_insert_post($lc_draftPost); 

        wp_set_object_terms( $newId, $lc_event_category, 'event_categories', false );

        //Retrieve Published Posts Featured Image
        $lc_thumbnail_id = get_post_thumbnail_id( $lc_post_id );

        if( !empty($lc_thumbnail_id ) ){
            set_post_thumbnail( $newId,  $lc_thumbnail_id);
        }

        //Updating Event Details from Spektrix
        
        update_post_meta( $newId, '_lc_publishedId', $lc_post_id );
        update_post_meta( $newId, 'event_meta_box_stocker_spektrix_event_id', esc_attr( $json->id ) ); 
        update_post_meta( $newId, 'event_start_date', esc_attr( date_format( date_create( $json->firstInstanceDateTime ),"m/d/Y" ) ) );
        update_post_meta( $newId, 'start_date', esc_attr( date_format( date_create( $json->firstInstanceDateTime ),"m/d/Y" ) ) ); 
        update_post_meta( $newId, 'event_start_time', esc_attr( date_format( date_create( $json->firstInstanceDateTime ),"h:i:s A" ) ) ); 
        update_post_meta( $newId, 'event_end_date', esc_attr( date_format( date_create( $json->lastInstanceDateTime ),"m/d/Y" ) ) ); 
        update_post_meta( $newId, 'event_end_time', esc_attr( date_format( date_create( $json->lastInstanceDateTime ),"h:i:s A" ) ) ); 	
        update_post_meta( $newId, 'event_meta_box_event_end_date_and_time_', esc_attr( date_format( date_create( $json->lastInstanceDateTime ),"m/d/Y h:i:s A" ) ) );	
        update_post_meta( $newId, 'event_meta_box_ticket_price_s_', esc_attr( $json->attribute_TicketPrice ) ); 	
        update_post_meta( $newId, 'event_meta_box_event_location', esc_attr( $lc_venue_Name ) );

        //Get Other Post Meta Fields
        // Add Post Meta from get_post_meta call array (false = return array)

        $lc_published_post_meta = get_post_meta($lc_post_id, '', false);

        foreach ( $lc_published_post_meta as $key => $value ){
            if ($key != '_edit_lock' && $key != '_edit_last' && $key != '_lc_publishedId' && $key != 'event_meta_box_stocker_spektrix_event_id' && $key != 'event_start_date' && $key != 'start_date' && $key != 'event_start_time' && $key != 'event_end_date' && $key != 'event_end_time' && $key != 'event_meta_box_event_end_date_and_time_' && $key != 'event_meta_box_ticket_price_s_' && $key != 'event_meta_box_event_location'){
                foreach ($value as $newvalue){
                add_post_meta($newId, $key, $newvalue, true);
                }
            }
        }

        echo '<h2>Updating "' . $json->name . '"</h2>';

        echo '  <p><a href="' . admin_url('post.php?action=edit&post=' . $newId) . '">Edit ' . $json->name . ' Event</a></p>';

        echo '  <p><a href="/stocker/wp-admin/edit.php?post_type=lccc_events&page=lc-event-import">Return to Event Importer</a></p>';


        echo '</div>';
    }else{
        
        if( $_POST['action'] == 'filterdates' ){
            check_admin_referer( 'filter-date', '_wpnonce_filter-date' );                       
            $requestUrl = $sDomain ."/api/v3/events?\$expand=instances";  
            
            $currentYear = date("Y");

            switch( $_POST['month'] ){
                case 'January':
                    $requestUrl = $sDomain ."/api/v3/events?\$expand=instances&instanceStart_from=1/1/" . $currentYear . "&instanceStart_to=1/31/". $currentYear;
                break;

                case 'February':
                    $requestUrl = $sDomain ."/api/v3/events?\$expand=instances&instanceStart_from=2/1/" . $currentYear . "&instanceStart_to=2/28/". $currentYear;
                break;

                case 'March':
                    $requestUrl = $sDomain ."/api/v3/events?\$expand=instances&instanceStart_from=3/1/" . $currentYear . "&instanceStart_to=3/31/". $currentYear;
                break;

                case 'April':
                    $requestUrl = $sDomain ."/api/v3/events?\$expand=instances&instanceStart_from=4/1/" . $currentYear . "&instanceStart_to=4/30/". $currentYear;
                break;
                
                case 'May':
                    $requestUrl = $sDomain ."/api/v3/events?\$expand=instances&instanceStart_from=5/1/" . $currentYear . "&instanceStart_to=5/31/". $currentYear;
                break;

                case 'June':
                    $requestUrl = $sDomain ."/api/v3/events?\$expand=instances&instanceStart_from=6/1/" . $currentYear . "&instanceStart_to=6/30/". $currentYear;
                break;

                case 'July':
                    $requestUrl = $sDomain ."/api/v3/events?\$expand=instances&instanceStart_from=7/1/" . $currentYear . "&instanceStart_to=7/31/". $currentYear;
                break;

                case 'August':
                    $requestUrl = $sDomain ."/api/v3/events?\$expand=instances&instanceStart_from=8/1/" . $currentYear . "&instanceStart_to=8/31/". $currentYear;
                break;
            
                case 'September':
                    $requestUrl = $sDomain ."/api/v3/events?\$expand=instances&instanceStart_from=9/1/" . $currentYear . "&instanceStart_to=9/30/". $currentYear;
                break;

                case 'October':
                    $requestUrl = $sDomain ."/api/v3/events?\$expand=instances&instanceStart_from=10/1/" . $currentYear . "&instanceStart_to=10/31/". $currentYear;
                break;

                case 'November':
                    $requestUrl = $sDomain ."/api/v3/events?\$expand=instances&instanceStart_from=11/1/" . $currentYear . "&instanceStart_to=11/30/". $currentYear;
                break;

                case 'December':
                    $requestUrl = $sDomain ."/api/v3/events?\$expand=instances&instanceStart_from=12/1/" . $currentYear . "&instanceStart_to=12/31/". $currentYear;
                break;

                default:
                $requestUrl = $sDomain ."/api/v3/events?\$expand=instances";
            }

        }else{
                       
            $requestUrl = $sDomain ."/api/v3/events?\$expand=instances";
        }


    $response = wp_remote_get( $requestUrl );

    $json = json_decode( $response['body'] );

    //echo count( $json ) . "<br />";

/*     echo "<pre>";
    var_dump( $json );
    echo "</pre>"; */

    $rCount = count( $json );
    ?>

    <form id="dates-filter" method="POST" action="">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <select name="month" id="month">
                    <option value="">Choose a Month</option>
                    <option value="January">January</option>
                    <option value="February">February</option>
                    <option value="March">March</option>
                    <option value="April">April</option>
                    <option value="May">May</option>
                    <option value="June">June</option>
                    <option value="July">July</option>
                    <option value="August">August</option>
                    <option value="September">September</option>
                    <option value="October">October</option>
                    <option value="November">November</option>
                    <option value="December">December</option>
                </select>
                <?php wp_nonce_field( 'filter-date', '_wpnonce_filter-date' ) ?>
                <input name="action" type="hidden" value="filterdates" />

                <input type="submit" id="filterDate" class="button action" value="Filter Dates">
            </div>
        </div>
        <table class="wp-list-table widefat fixed striped table-view-list posts">
            <thead>
            <tr>
                <td class="column-cb check-column">&nbsp;</td>
                <th scope="col" id="title">Event Name</th>
                <th scope="col" id="start_date">First Date</th>
                <th scope="col" id="last_date">Last Date</th>
                <th scope="col" id="category">Category</th>
                <th scope="col" id="action">&nbsp;</th>
            </tr>
            </thead>
            <tbody>
    <?php 
            for ($x = 0; $x <= $rCount-1; $x++) {

            $event_ID = $json[$x]->id;

            $lc_event_category = getWPCategoryfromSpektrix($json[$x]);

            $lc_cat_id = get_term_by( 'slug', $lc_event_category, 'event_categories' );

            //echo "<pre>";
            //var_dump( $lc_cat_id );
            //echo "</pre>";

            //$lc_cat_id = $lc_event_category;
    ?>

                <tr>
                    <th class="check-column">&nbsp;</th>
                    <td class="title column-title has-row-actions column-primary page-title" data-name="Event Name">
                    <?php echo $json[$x]->name; ?>
                    </td>
                    <td class="date column-date" data-name="First Date">
                    <?php echo date_format(date_create($json[$x]->firstInstanceDateTime),"m/d/Y"); ?>
                    </td>
                    <td class="date column-date" data-name="Last Date">
                    <?php echo date_format(date_create($json[$x]->lastInstanceDateTime),"m/d/Y"); ?>
                    </td>
                    <td class="title column-title"><?php echo $lc_cat_id->name; ?></td>
                    <td class="" data-name="action"><a href="<?php echo add_query_arg( 'lc_eventimport', $event_ID ); ?>">Import</a> | <a href="<?php echo add_query_arg( 'lc_eventsync', $event_ID ); ?>">Sync Details</a></td>
                </tr>
    <?php 

            }

            ?>
            </tbody>
        </table>
        </form>
    <?php
    }

}

function getWPCategoryfromSpektrix($event){

    if( $event->attribute_ArtsAndHumanities ){
        $lc_event_category = 'arts-humanities-division-performances';
    }
    if( $event->attribute_CivicChorale ){
        $lc_event_category = 'chorale-concert-series';
    }
    if( $event->attribute_CivicConcertBand ){
        $lc_event_category = 'civic-concert-band';
    }
    if( $event->attribute_CivicJazzBand ){
        $lc_event_category = 'civic-jazz-band';
    }
    if( $event->attribute_CivicOrchestra ){
        $lc_event_category = 'civic-orchestra';
    }
    if( $event->attribute_CollegiateChoraleAndVocalEnsemble ){
        $lc_event_category = 'collegiate-chorale-and-vocal-ensemble';
    }
    if( $event->attribute_Film ){
        $lc_event_category = 'film-series';
    }
    if( $event->attribute_PerformingArtistsSeries ){
        $lc_event_category = 'performing-artists-series';
    }
    if( $event->attribute_RandomActsSeries ){
        $lc_event_category = 'random-acts-series';
    }
    if( $event->attribute_SignatureRecitalSeries ){
        $lc_event_category = 'signature-recital-series';
    }
    if( $event->attribute_StudentMatineeSeries ){
        $lc_event_category = 'student-matinee-series';
    }
    if( $event->attribute_StudioSessions ){
        $lc_event_category = 'studio-sessions';
    }
    if( $event->attribute_TheatreProgram ){
        $lc_event_category = 'theatre-program';
    }

    return $lc_event_category;
}