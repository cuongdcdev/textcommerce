<?php
// This is a PLUGIN TEMPLATE.

// Copy this file to a new name like abc_myplugin.php.  Edit the code, then
// run this file at the command line to produce a plugin for distribution:
// $ php abc_myplugin.php > abc_myplugin-0.1.txt

// Plugin name is optional.  If unset, it will be extracted from the current
// file name. Uncomment and edit this line to override:
# $plugin['name'] = 'abc_plugin';

$plugin['version'] = '0.0.1';
$plugin['author'] = 'Levi Nunnink';
$plugin['author_uri'] = 'http://culturezoo.com/nwa';
$plugin['description'] = 'Allows users to add detailed vendor information.';
$plugin['type'] = 1; // 0 for regular plugin; 1 if it includes admin-side code

if(isset($_GET['build'])){
@include_once($_SERVER['DOCUMENT_ROOT'].'/TextCommerce/plugins/subplugins/zem_tpl.php');
}
if (0) {
?>
# --- BEGIN PLUGIN HELP ---

h1. Visit "http://homeplatewp.com/TextPattern/":http://homeplatewp.com/TextPattern/ for help documentation.

# --- END PLUGIN HELP ---
<?php
}

# --- BEGIN PLUGIN CODE ---

// ----------------------------------------------------
// Example public side tags


	

// ----------------------------------------------------
// Example admin side plugin
error_reporting(E_ERROR);

if (@txpinterface == 'admin') {
	tc_vendors_install();

	$vendors_event = 'vendors';
	$vendors_name = 'Designers';


	if(isset($_REQUEST['step'])){
		$step = $_REQUEST['step'];
	}else{
		$step = "";
	}

	if(isset($_REQUEST['event'])){
		$event = $_REQUEST['event'];
	}
	
	if($event == "vendors"){
		switch(strtolower($step)) {
			// 'zem_admin_test' will be called to handle the new event
			case "":  		register_callback("tc_vendors_list", $vendors_event); break;
			case "list":  	register_callback("tc_vendors_list", $vendors_event); break;
			case "edit":    register_callback("tc_vendors_edit", $vendors_event); break;
			case "save":  	register_callback("tc_vendors_save", $vendors_event); break;
			case "update":  register_callback("tc_vendors_save", $vendors_event); break;
			case "delete":  register_callback("tc_vendors_delete", $vendors_event); break;
		}
	}

	register_tab("store", $vendors_event, $vendors_name);


}
	
	function tc_vendors_install(){
		if (!getThings("show tables like 'vendors'")){
			//Create the vendors table
			safe_query("CREATE TABLE vendors (
				  		id int(11) NOT NULL auto_increment,
				  		name varchar(200) default NULL,
				  		website_url varchar(200) default NULL,
				  		image varchar(250) NOT NULL default '',
				  		description text NOT NULL,
				  		description_html text NOT NULL,
				  		category_name varchar(64) NOT NULL default '',
				  		PRIMARY KEY  (id)
						);");
		}
	}
	
	function tc_vendors_delete($event, $step){

		global $txp_user, $vars, $txpcfg, $prefs;

		extract($prefs);

		extract(doSlash($_REQUEST));

		$id = assert_int($id);

		safe_delete("vendors", "id = $id");

		tc_vendors_list('', '', "vendor deleted");

	}
	
	function tc_vendors_save($step, $event){
		
		global $txp_user, $txpcfg, $prefs;

		extract($prefs);

		extract(doSlash($_REQUEST));
		
		include_once txpath.'/lib/classTextile.php';
		include_once txpath.'/lib/class.thumb.php';
		$textile = new Textile();
		$description_html = $textile->TextileThis(trim($_REQUEST['description']));
		$category_name = dumbDown($textile->TextileThis(trim(doSlash($name)),1));
		$category_name = preg_replace("/[^[:alnum:]\-_]/", "", str_replace(" ","-",$category_name));
		$dest = '';
		if(!empty($_FILES['vendor_logo']['tmp_name'])){
			$image = "logo_".$category_name.".".getExtension($_FILES['vendor_logo']['name']);
			$ext = getExtension($_FILES['vendor_logo']['name']);
			$dest = $prefs['path_to_site']."/".$prefs['img_dir']."/".$image;
			$orig = $_FILES['vendor_logo']['tmp_name'];
			if($ext == 'jpg' or $ext == 'gif' or $ext == 'png'){
				if(!move_uploaded_file($orig, $dest)){
					echo "Could not upload: ".$dest;
				}
			}else{
				echo $_FILES['vendor_logo']['name']. " is not a valid image file.";
			}
			
		}
		
		if(!empty($id)){
		
			$category_name_old = safe_field("category_name", "vendors", "id=$id");
			
			$check = safe_field("name", "txp_category", "name='$category_name_old' and type='article'");
			
			if (!$check) {
				$q = safe_insert("txp_category", "name='$category_name', title='$name', type='article', parent='Vendors'");
				rebuild_tree('root', 1, "article");
			}else{
				safe_update("txp_category", "name='$category_name', title='$name'", "name='$category_name_old'");
			}
			
			$rs = safe_update('vendors', 
								"
								name 			='$name',
								website_url 	='$website_url',
								image 			='$image',
								description 	='$description',
								description_html='$description_html',
								category_name 	='$category_name'
								", "id=$id");
			if(!$rs){
				tc_vendors_list("","","There was an error trying to update: ".mysql_error());
			}else{
				tc_vendors_list("","","Updated vendor '$name'");
			}
		
		}else{
			
			$check = safe_field("name", "txp_category", "name='$category_name' and type='article'");
			if (!$check) {
				$q = safe_insert("txp_category", "name='$category_name', title='$name', type='article', parent='Vendors'");
				rebuild_tree('root', 1, "article");
			}
			
			$rs = safe_insert('vendors', 
								"
								name 			='$name',
								website_url 	='$website_url',
								image 			='$image',
								description 	='$description',
								description_html='$description_html',
								category_name 	='$category_name'
								");
			
			if(!$rs){
				tc_vendors_list("","","There was an error trying to save: ".mysql_error());
			}else{
				tc_vendors_list("","","Saved vendor '$name'");
			}
			
		}

		
	}
	
	function tc_vendors_edit($step, $event, $message){
		global $step, $txp_user, $prefs;
		
		if(isset($_REQUEST['id'])){
			$id = intval($_REQUEST['id']);
			$vendor = safe_row("*", "vendors", "id = $id");
			extract($vendor);
			$step = "update";
		}else{
			$step = "save";
		}
		
		pagetop("Vendor", $message);
		
		//CSS FOR CUSTOMER EDIT
		//==================================
		echo n.'<style type="text/css">'.
			  n.'.customerEdit legend{'.
			 n.'	font-size: 11px;'.
			 n.'	font-weight: bold;'.
			 n.'}'.

			 n.'.customerEdit label{'.
			 n.'	float: left;'.
			 n.'	width: 100px;'.
			 n.'	text-align: right;'.
			 n.'	padding: 5px;'.
			 n.'}'.

			 n.'.customerEdit br{'.
			 n.'	clear: both;'.
			 n.'}'.

			 n.'.customerEdit input{'.
			 n.'	margin: 5px 0 5px 0;'.
			 n.'	font-size: 11px;'.
			 n.'}'.


			 n.'</style>';

		
		echo n.n.'<form name="product" method="post" action="index.php" enctype="multipart/form-data">';

		echo hInput('id', $id).
			eInput('vendors').
			sInput($step).
			'<input type="hidden" name="view" />'.

			startTable('edit').

  		'<tr>'.n;

		//if ($view == 'text')
		//{

					//-- markup help --------------



		echo '<td id="article-main">'.n;
		echo "<fieldset class='customerEdit'>".
			  n."<legend>Designer Details</legend>".
			  n.'<label for="name">Name</label>'.
			  n.'<input id="name" name="name" value="'.$name.'"/>'.br.
			  n.'<label for="website_url">Website URL</label>'.
			  n.'<input id="website_url" name="website_url" value="'.$website_url.'"/>'.
			  n.'<label for="description">Description <small>Textile is allowed</small></label>'.
			  n.'<textarea name="description" id="description" rows="10" style="width:260px;">'.stripslashes(htmlspecialchars($description))."</textarea>".br.
			  n.'</fieldset>';	
		
		if(!empty($image)){
			echo '<input type="hidden" name="image" value="'.$image.'"/>';
			echo n.'<fieldset class="customerEdit" style="background-color:white">'.
				 n.'<legend>Current Logo</legend>';

			echo n.'<img src="http://'.$prefs['siteurl'].'/'.$prefs['img_dir']."/".$image.'" alt="Logo"/>';
			echo n."</fieldset>";
			
			echo n.'<fieldset class="customerEdit" style="background-color:white">'.
				n.'<legend>Update Logo</legend>';
				echo n.'<em>Allowed file types are JPG, GIF &amp; PNG</em>';
				echo n.'<input type="file" name="vendor_logo"/>'.n;
				echo n."</fieldset>";
		}else{
			echo n.'<fieldset class="customerEdit" style="background-color:white">'.
				n.'<legend>Upload Logo</legend>';
				echo n.'<em>Allowed file types are JPG, GIF &amp; PNG</em>';
				echo n.'<input type="file" name="vendor_logo"/>'.n;
				echo n."</fieldset>";
		}
		echo '</td>';
		
		echo '<td id="article-col-2" style="padding-top: 13px;">'; //start article-col-2
		if(isset($id)){
			echo n.'<a href="?event=vendors&step=delete&id='.$id.'" onclick="if(!confirm(\'Are you sure?\')){return false;}" style="color:#990000">Delete this designer</a>'.br.br;
		}
		//-- publish button --------------
		echo
		(has_privs('article.publish')) ?
		fInput('submit','publish',gTxt('save'),"publish", '', '', '', 4) :
		fInput('submit','publish',gTxt('save'),"publish", '', '', '', 4);

		if($user_id){

			$orders = safe_rows("*", "orders", "user_id = $user_id ORDER BY last_updated DESC");
			if(count($orders) > 0){
				echo br.br."<fieldset>".
						n.'<legend>Order History</legend>'.
						n.'<ul class="plain-list">';

						foreach($orders as $order){
							echo n.'<li><a href="?event=orders&step=edit_order&id='.$order['id'].'">ORDER #'.$order['id'].'</a></li>';
						}
						n.'</ul>';
				echo '</fieldset>';
			}

		 }

		 echo '</td></tr></table></form>';
		
	}
	function tc_vendors_list($step, $event, $message){
		global $statuses, $comments_disabled_after, $step, $txp_user;
		
		pagetop("Designers", $message);
		echo poweredit_products(); //echo the poweredit js

		extract(get_prefs());

		extract(gpsa(array('page', 'sort', 'dir', 'crit', 'search_method')));

		$sesutats = array_flip($statuses);

		$dir = ($dir == 'desc') ? 'desc' : 'asc';

		echo '<script type="text/javascript" src="http://'.$siteurl.'/js/prototype.js"></script>';
		echo '<script type="text/javascript" src="http://'.$siteurl.'/js/scriptaculous.js"></script>';

		$sort_sql = 'name '.$dir.', id desc';

		$switch_dir = ($dir == 'desc') ? 'asc' : 'desc';

		$total = safe_count('vendors', "1");

		if ($total < 1)
		{
			
			echo graf("No Designers found", ' style="text-align: center;"').br;
			echo graf("<a href='?event=vendors&step=edit' class='navlink'>Add a new designer</a>", ' style="text-align: center;"').br;
			return;
		}

		$limit = max(@$article_list_pageby, 15);

		list($page, $offset, $numPages) = pager($total, $limit, $page);
		
		$rs = safe_rows_start('*', 'vendors',
			" 1 order by $sort_sql limit $offset, $limit"
		);
		
		if ($rs)
		{

			echo n.n.'<form name="longform" method="post" action="index.php" onsubmit="return verify(\''.gTxt('are_you_sure').'\')">'.
			
			n.startTable('list','','','','700').
			n.tr(
				n.tda("&nbsp;", ' colspan="2" style="border: none; padding-bottom: 15px;"').
				n.tda('<a href="?event=vendors&step=edit" class="navlink">Add a new designer</a>', ' colspan="2" style="text-align: right; border: none; padding-bottom: 15px;"')
			).

			n.tr(
				n.column_head('name', 'name', 'vendors', true, $switch_dir, $crit, $search_method).
				column_head('website').
				column_head('description')
			);
			
			include_once txpath.'/publish/taghandlers.php';
			
			while ($a = nextRow($rs))
			{
				extract($a);
				
				
				$name = eLink('vendors', 'edit', 'id', $id, $name);
				if(strlen($website_url) > 50){
					$website_url_display = substr($website_url, 0, 50)."...";
				}else{
					$website_url_display = $website_url;
				}
				$website = tag($website_url_display, "a", " href={$website_url_display}");
				
				$description = stripslashes(substr($description, 0, 60))."...";

				
				echo n.n.tr(
				
					n.td($name).
				
					td($website).
					
					td($description)
				);
			}
			
			
			echo n.endTable().
				 n.'</form>';
			
			echo n.nav_form('list', $page, $numPages, $sort, $dir, $crit, $search_method).
			
			n.pageby_form('list', $article_list_pageby);
			}
		
		
	}
	
	function getExtension($filename) {
		$ext = substr($filename, strrpos($filename, '.') + 1);
		return $ext;
	}
	
	/*----
	PUBLIC FACING VENDOR TAGS
	----*/
	
	function tc_vendor_name($atts) {
	   global $pretext;
	   if(!empty($pretext['c'])){
	   		$category_name = $pretext['c'];
	   		$name = safe_field("name", "vendors", "category_name='$category_name'");
	   		return $name;
	   }else{
	   		return "";
	   }
	}
	
	function tc_vendor_description() {
	   global $pretext;
	   if(!empty($pretext['c'])){
	   		$category_name = $pretext['c'];
	   		$description_html = safe_field("description_html", "vendors", "category_name='$category_name'");
			return $description_html;
	   }else{
	   		return "";
	   }
	}
	
	
	
	function tc_vendor_logo() {
	   global $pretext, $img_dir;
	   
	   $category_name = $pretext['c'];
	   
	   $image = safe_field("image", "vendors", "category_name='$category_name'");

	   extract(lAtts(array(
			'class'     => '',
			'html_id'   => '',
			'style' 	  => '', // remove in crockery?
			'wraptag'   => '',
		), $atts));

		if($image){
			$out = '<img src="'.hu.$img_dir.'/'.$image.'" style="'.$style.'" />';

		}else{
			trigger_error(gTxt('unknown_image'));
			return;
		}

		return ($wraptag) ? doTag($out, $wraptag, $class, '', $html_id) : $out;
				
			
	}
	
	
	function tc_vendor_link($atts) 
	{
		global $thisarticle, $pretext;
		assert_article();
		
		extract(lAtts(array(
			'section'     => '',
			'class'     => ''
		), $atts));
		
		if(!empty($section)){
			$section = $section."/";
		}else{
			$section = $pretext['s']."/";
		}
		
		$vendor = safe_field("custom_5", "textpattern", "ID = ".$thisarticle['thisid']);
		$vendor_name = safe_field("Title", "txp_category", "name = '$vendor'");
		
		$link = hu.$section.$vendor;
		$return = tag($vendor_name, "a", ' href="'.$link.'" '.$class.'');
		return $return;
	}	
	
	function tc_vendor_product_list($atts) {
		global $prefs, $thisarticle, $id, $pretext;
		extract(lAtts(array(
			'section' => '',
			'form' => 'default',
			'limit'     => 999,
			'offset'    => 0,
			'sortby' => 'uPosted',
			'sortdir' => 'desc'
		),$atts));
		
		if(!empty($section)){
			$section = "Section = '$section'";
		}
		
		$category_name = $pretext['c'];

		if(!empty($category_name)){
			$rs = safe_rows("*, UNIX_TIMESTAMP(Posted) as uPosted", "textpattern",
							"custom_5 = '$category_name' $section ORDER BY $sortby $sortdir LIMIT $offset, $limit");
			
			
			if ($rs) {
				$count = 0;
				$articles = array();
				foreach($rs as $a) {
					++$count;
					$comparing = $a['ID'];
					populateArticleData($a);
					global $thisarticle, $uPosted, $limit;
					// define the article form
					$article = ($prefs['allow_form_override'] && $a['override_form']) ? fetch_form($a['override_form']) : fetch_form($form);
					$articles[] = parse($article);

					// sending these to paging_link(); Required?
					$uPosted = $a['uPosted'];
					$limit = $limit;

					unset($GLOBALS['thisarticle']);
					unset($GLOBALS['theseatts']);//Required?
				}
				$has_article_tag = true;
				return join('',$articles);
			}
		}
	}


	
	
# --- END PLUGIN CODE ---

?>