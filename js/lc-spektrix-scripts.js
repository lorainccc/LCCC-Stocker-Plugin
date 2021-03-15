// Add buttons to event screen.

		var $product_screen = '.edit-php.post-type-lccc_event';

		console.log($product_screen);
		
		var $title_action   = $product_screen.find( '.page-title-action:first' )

		   $title_action.after(
			   '<a href="post_type=lccc_events&page=product_importer" class="page-title-action">Import</a>'
		   );