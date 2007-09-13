<?php
$plugin['version'] = ' Alpha 0.2';
$plugin['author'] = 'Levi Nunnink, JR Chew';
$plugin['author_uri'] = 'http://homeplatewp.com/TextCommerce/';
$plugin['description'] = 'Required core store library.';
$plugin['type'] = 1; // 0 for regular plugin; 1 if it includes admin-side code

@include_once('zem_tpl.php');

if (0) {
?>
# --- BEGIN PLUGIN HELP ---

For display information visit: 
http://homeplatewp.com/TextCommerce/article/67/tc_product_display-001

# --- END PLUGIN HELP ---
<?php
}


// TextCommerce Version alpha 0.0.6.8
// Culturezoo LLC - www.culturezoo.com

function store_categories($event, $step){
		global $statuses, $comments_disabled_after, $step, $txp_user;

		pagetop("Categories", $message);

		//print_r($hits);
		echo "<h4 style='text-align:center;'>Coming soon</h4>";

	}






?>