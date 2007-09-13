<?php
$plugin['version'] = ' Alpha 0.2';
$plugin['author'] = 'Levi Nunnink, JR Chew';
$plugin['author_uri'] = 'http://homeplatewp.com/TextCommerce/';
$plugin['description'] = 'Required core products library.';
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

function products_list($event='', $step='', $message=''){

		global $statuses, $comments_disabled_after, $step, $txp_user, $currency;
		$message = '';
		pagetop(gTxt('tab_list'), $message);

		echo poweredit_products(); //echo the poweredit js

		extract(get_prefs());

		extract(gpsa(array('page', 'sort', 'dir', 'crit', 'search_method')));

		$sesutats = array_flip($statuses);

		$dir = ($dir == 'desc') ? 'desc' : 'asc';

		echo '<script type="text/javascript" src="http://'.$siteurl.'/jquery.js"></script>';
		echo '<script type="text/javascript" src="http://'.$siteurl.'/jquery.js"></script>';

		switch ($sort)
		{
			case 'id':
				$sort_sql = 'ID '.$dir;
			break;

			case 'posted':
				$sort_sql = 'Posted '.$dir;
			break;

			case 'title':
				$sort_sql = 'Title '.$dir.', Posted desc';
			break;

			case 'section':
				$sort_sql = 'Section '.$dir.', Posted desc';
			break;

			case 'category1':
				$sort_sql = 'Category1 '.$dir.', Posted desc';
			break;

			case 'category2':
				$sort_sql = 'Category2 '.$dir.', Posted desc';
			break;

			case 'status':
				$sort_sql = 'Status '.$dir.', Posted desc';
			break;

			case 'author':
				$sort_sql = 'AuthorID '.$dir.', Posted desc';
			break;

			case 'comments':
				$sort_sql = 'comments_count '.$dir.', Posted desc';
			break;

			default:
				$dir = 'desc';
				$sort_sql = 'Posted '.$dir;
			break;
		}

		$switch_dir = ($dir == 'desc') ? 'asc' : 'desc';

		$criteria = "section = 'store'";

		if ($search_method and $crit)
		{
			$crit_escaped = doSlash($crit);

			$critsql = array(
				'id'         	=> "ID = '$crit_escaped'",
				'title_body' 	=> "Title rlike '$crit_escaped' or Body rlike '$crit_escaped'",
				'section'		=> "Section rlike '$crit_escaped'",
				'categories' 	=> "Category1 rlike '$crit_escaped' or Category2 rlike '$crit_escaped'",
				'status'		=> "Status = '".(@$sesutats[gTxt($crit_escaped)])."'",
				'author'		=> "AuthorID rlike '$crit_escaped'",
			);

			if (array_key_exists($search_method, $critsql))
			{
				$criteria = $critsql[$search_method];
				$limit = 500;
			}

			else
			{
				$search_method = '';
				$crit = '';
			}
		}

		else
		{
			$search_method = '';
			$crit = '';
		}

		$total = safe_count('textpattern', "$criteria");

		if ($total < 1)
		{
			if ($criteria != 1)
			{
				echo n.list_search_form_products($crit, $search_method).
					n.graf("No products found", ' style="text-align: center;"');
			}

			else
			{
				echo graf("No products found", ' style="text-align: center;"');
			}

			return;
		}

		$limit = max(@$article_list_pageby, 15);

		list($page, $offset, $numPages) = pager($total, $limit, $page);
		
		
		echo n.list_search_form_products($crit, $search_method);

		$rs = safe_rows_start('*, unix_timestamp(Posted) as posted', 'textpattern',
			"$criteria order by $sort_sql limit $offset, $limit"
		);
		
		if ($rs)
		{
			$total_comments = array();

			// fetch true comment count, not the public comment count
			// maybe we should have another row in the db?
			$rs2 = safe_rows_start('parentid, count(*) as num', 'txp_discuss', "1 group by parentid order by parentid");

			if ($rs2)
			{
				while ($a = nextRow($rs2))
				{
					$pid = $a['parentid'];
					$num = $a['num'];

					$total_comments[$pid] = $num;
				}
			}

			echo n.n.'<form name="longform" method="post" action="index.php" onsubmit="return verify(\''.gTxt('are_you_sure').'\')">'.

				n.startTable('list','','','','700').
				n.tr(
					hCell().
					//n.column_head('ID', 'id', 'products', true, $switch_dir, $crit, $search_method).
					column_head('category2', 'category2', 'products', true, $switch_dir, $crit, $search_method).
					n.column_head('title', 'title', 'products', true, $switch_dir, $crit, $search_method).
					column_head('category1', 'category1', 'products', true, $switch_dir, $crit, $search_method).
					column_head('price', 'price', 'products', true, $switch_dir, $crit, $search_method).
					column_head('stock', 'stock', 'products', true, $switch_dir, $crit, $search_method).
					column_head('status', 'status', 'products', true, $switch_dir, $crit, $search_method).
					hCell()
				);

			include_once txpath.'/publish/taghandlers.php';

			while ($a = nextRow($rs))
			{
				extract($a);

				if (empty($Title))
				{
					$Title = '<em>'.eLink('product', 'edit', 'ID', $ID, gTxt('untitled')).'</em>';
				}
				else
				{
					$Title = eLink('product', 'edit', 'ID', $ID, $Title);
				}
				if(!empty($Image)){
					$Image = "<img src='".hu."images/$Image' alt='Product Image' width='20' height='20'/>";
				}
				$Category1 = '<span title="'.htmlspecialchars(fetch_category_title($Category1)).'">'.$Category1.'&nbsp;</span>';
				$Category2 = '<span title="'.htmlspecialchars(fetch_category_title($Category2)).'">'.$Category2.'&nbsp;</span>';
				$manage = n.'<ul class="articles_detail">'.
						n.t.'<li>'.eLink('product', 'edit', 'ID', $ID, gTxt('edit')).'</li>'.
						( ($Status == 4 or $Status == 5) ? n.t.'<li><a href="'.permlinkurl($a).'">'.gTxt('view').'</a></li>' : '' ).
						n.'</ul>';

				$Status = !empty($Status) ? $statuses[$Status] : '';

				$comments = gTxt('none');

				if (isset($total_comments[$ID]) and $total_comments[$ID] > 0)
				{
					$comments = href(gTxt('manage'), 'index.php?event=discuss'.a.'step=list'.a.'search_method=parent'.a.'crit='.$ID).
						' ('.$total_comments[$ID].')';
				}

				$comment_status = ($Annotate) ? gTxt('on') : gTxt('off');

				if ($comments_disabled_after)
				{
					$lifespan = $comments_disabled_after * 86400;
					$time_since = time() - $posted;

					if ($time_since > $lifespan)
					{
						$comment_status = gTxt('expired');
					}
				}

				$comments = n.'<ul>'.
					n.t.'<li>'.$comment_status.'</li>'.
					n.t.'<li>'.$comments.'</li>'.
					n.'</ul>';

				echo n.n.tr(

					n.td($Image, 15).


					//td(eLink('product', 'edit', 'ID', $ID, $ID).$manage).

					/*td(
						safe_strftime('%d %b %Y %I:%M %p', $posted)
					).*/
					
					td($Category2, 100).
					
					td($Title).

					/*td(
						'<span title="'.htmlspecialchars(fetch_section_title($Section)).'">'.$Section.'&nbsp;</span>'
					, 75).*/

					td($Category1, 100).
					
					td($currency.$custom_1).
					
					td($custom_4).
					
					td(($a['Status'] < 4 ? $Status : '<a href="'.permlinkurl($a).'">'.$Status.'</a>'), 50).

					/*td(
						'<span title="'.htmlspecialchars(get_author_name($AuthorID)).'">'.$AuthorID.'</span>'
					).*/

					td((
						(  ($a['Status'] >= 4 and has_privs('article.edit.published'))
						or ($a['Status'] >= 4 and $AuthorID == $txp_user
											     and has_privs('article.edit.own.published'))
						or ($a['Status'] < 4 and has_privs('article.edit'))
						or ($a['Status'] < 4 and $AuthorID == $txp_user and has_privs('article.edit.own'))
						)
						? fInput('checkbox', 'selected[]', $ID)
						: '&nbsp;'
					))
				);
			}

			echo n.n.tr(
				tda(
					toggle_box('articles_detail'),
					' colspan="2" style="text-align: left; border: none;"'
				).

				tda(
					select_buttons().
					product_multiedit_form($page, $sort, $dir, $crit, $search_method)
				,' colspan="5" style="text-align: right; border: none;"')
			).

			n.endTable().
			n.'</form>'.
	    n.'<h4 style="font-weight:normal; text-align:center; width:100%;"><a href="#" class="navlink" onclick="$(\'#uploadCSV\').toggle()">Import Products</a>';
					//n.
			
		  $instructions = tag(tag('<li>Using FTP, upload your product images to <pre>/txp_site_root/images/_import/</pre></li><li>Upload a correctly formatted CSV file using the form below. (CSV must be in UTF-8 character encoding with DOS or UNIX line breaks.)</li><li>Sit back and watch the magic</li>',"ol"), "div", ' id="instructions" style="display:none; width:380px; text-align:left; margin:0 auto;"');
    		
				echo tag('<h4 style="font-weight:normal; text-align:center; width:100%;"><small><a href="http://homeplatewp.com/TextCommerce/file_download/3">Download Example CSV</a> | <a href="javascript:void(0)" onclick="$(\'#instructions\').toggle()">Import Instructions</a></small></h4>'.$instructions.upload_form("Browse for CSV:", '', 'product_import', 'product'), 'div', ' id="uploadCSV" style="display:none;"');
			
			echo n.nav_form('list', $page, $numPages, $sort, $dir, $crit, $search_method).

			n.pageby_form('list', $article_list_pageby);
		}
		
		

	}//---- end products_list()
	
function product_import(){
		global $txp_user, $textile;

		define("TEMP_IMPATH",'../images/_import/');

		if(isset($_FILES["thefile"])){
			$thefile = $_FILES["thefile"]["tmp_name"];
			move_uploaded_file($thefile, "../files/import.csv");
			chmod("../files/import.csv", 0666);
			$data = parse_csv("../files/import.csv", true);
			foreach($data as $row){
				$title = implode(",", $row);
				$title = explode(",", $title);
				$title = $title[0];
				extract($row);
				if($row['STATUS'] == 'Live'){
					$status = 4;
				}else if($row['STATUS'] == 'Hidden'){
					$status = 2;
				}else{
					$status = 3;
				}
				
				if(!empty($row['VENDOR'])){
					include_once txpath.'/lib/classTextile.php';
					$textile = new Textile();
					$vendor = dumbDown($textile->TextileThis(trim(doSlash($row['VENDOR'])),1));
					$vendor = preg_replace("/[^[:alnum:]\-_]/", "", str_replace(" ","-",$row['VENDOR']));
				}
				if(!empty($CATEGORY_1)){
					$CATEGORY_1 = preg_replace("/[^[:alnum:]\-_]/", "", str_replace(" ","-",$CATEGORY_1));
				}
				if(!empty($CATEGORY_2)){
					$CATEGORY_2 = preg_replace("/[^[:alnum:]\-_]/", "", str_replace(" ","-",$CATEGORY_2));
				}
				safe_insert(
			   		"textpattern",
				   "Title           = '$title',
					Body            = '$DESCRIPTION',
					Status          =  $status,
					Posted          =  now(),
					LastMod         =  now(),
					AuthorID        = '$txp_user',
					Section         = 'store',
					Category1       = '$CATEGORY_1',
					Category2       = '$CATEGORY_2',
					custom_1        = '$PRICE',
					custom_2        = '$WEIGHT',
					custom_3        = '$SKU',
					custom_4        = '$ITEMS_IN_STOCK',
					custom_5        = '$vendor',
					uid				= '".md5(uniqid(rand(),true))."',
					feed_time		= now()"
				);

				
				//echo mysql_error();
				
				$ID = mysql_insert_id();
				
				//echo $ID; print_r($product); die();
				
				if(!empty($row['PRODUCT_IMAGE_1'])){
					$img = TEMP_IMPATH.$row['PRODUCT_IMAGE_1'];
					upload_image($img, '1', $ID);
				}
				if(!empty($row['PRODUCT_IMAGE_2'])){
					$img = TEMP_IMPATH.$row['PRODUCT_IMAGE_2'];
					upload_image($img, '2', $ID);
				}
				if(!empty($row['PRODUCT_IMAGE_3'])){
					$img = TEMP_IMPATH.$row['PRODUCT_IMAGE_3'];
					upload_image($img, '3', $ID);
				}
				if(!empty($row['PRODUCT_IMAGE_4'])){
					$img = TEMP_IMPATH.$row['PRODUCT_IMAGE_4'];
					upload_image($img, '4', $ID);
				}
				$customFields = '';
				if(!empty($row['CUSTOM_FIELD_LABEL_1'])){
					$field = array(
									'label' => $row['CUSTOM_FIELD_LABEL_1'],
									'value' => $row['CUSTOM_FIELD_VALUE_1']);
					$customFields[] = $field;	
				}
				if(!empty($row['CUSTOM_FIELD_LABEL_2'])){
					$field = array(
									'label' => $row['CUSTOM_FIELD_LABEL_2'],
									'value' => $row['CUSTOM_FIELD_VALUE_2']);
					$customFields[] = $field;	
				}
				if(!empty($row['CUSTOM_FIELD_LABEL_3'])){
					$field = array(
									'label' => $row['CUSTOM_FIELD_LABEL_3'],
									'value' => $row['CUSTOM_FIELD_VALUE_3']);
					$customFields[] = $field;	
				}
				if(!empty($row['CUSTOM_FIELD_LABEL_4'])){
					$field = array(
									'label' => $row['CUSTOM_FIELD_LABEL_4'],
									'value' => $row['CUSTOM_FIELD_VALUE_4']);
					$customFields[] = $field;	
				}
				if(count($customFields) > 0){
					save_custom_fields($customFields, $ID);
				}
			}
			products_list('', '', "Products Imported");
		}else{
			products_list('','','Error: Couldn\'t Find Uploaded File!');
		}
		
	}
function product_edit($event, $step, $message='') {

		global $vars, $txp_user, $comments_disabled_after, $txpcfg, $prefs, $general_settings;

		extract($prefs);

		define("IMPATH",$path_to_site.'/'.$img_dir.'/');


		extract(gpsa(array('view','from_view','step')));

		if(!empty($GLOBALS['ID'])) { // newly-saved article
			$ID = $GLOBALS['ID'];
			$step = 'edit';
		} else {
			$ID = gps('ID');
		}

		//GET ARTICLE DATA FOR 'EDIT' & 'SAVE' STEP
		//==================================
		if ($step == "edit" || $step=="save"
			&& !empty($ID)) {

			$pull = true;          //-- it's an existing article - off we go to the db
			$ID = assert_int($ID);

			$rs = safe_row(
				"*, unix_timestamp(Posted) as sPosted,
				unix_timestamp(LastMod) as sLastMod",
				"textpattern",
				"ID=$ID"
			);
			extract($rs);

			if ($AnnotateInvite!= $comments_default_invite) {
				$AnnotateInvite = $AnnotateInvite;
			} else {
				$AnnotateInvite = $comments_default_invite;
			}
			
			$custom_fields = safe_rows("*", "product_custom_fields", "articleID = $ID ORDER BY id DESC");
			
			$step = "save";

		}else if(empty($step)){
			$step = "create";
		}

		$textile = new Textile();

		$textile_body = $use_textile;
		$textile_excerpt = $use_textile;

		$page_title = "Add a new product";
		$message = "";

		pagetop($page_title, $message);
		
		if(count($custom_fields) > 0){
			$startFieldNum = ($custom_fields[0]['id']+1);
		}else{
			$startFieldNum = 0;
		}
		echo "<script type='text/javascript'>var fieldNum = $startFieldNum</script>";
		//JS INCLUDES
		//==================================
		//print_r($prefs);
		echo '<script type="text/javascript" src="http://'.$siteurl.'/jquery.js"></script>';
		echo '<script type="text/javascript" src="http://'.$siteurl.'/jquery.js"></script>';

		//CSS FOR PRODUCT DISPLAY
		//==================================
		echo n.'<style type="text/css">'.
			 n.'h4.productHeading{'.
			 n.'	color:gray;'.
			 n.'	margin-bottom:0px;'.
			 n.'}'.

			 n.'h4.productHeading span{'.
			 n.'	float:right;'.
			 n.'	font-size:9px;'.
			 n.'	font-style:italic;'.
			 n.'	font-weight:normal;'.
			 n.'}'.

			 n.'.product-options-sub{'.
			 n.'	padding: 10px;'.
			 n.'}'.

			 n.'.product-options-sub em{'.
			 n.'	color:gray;'.
			 n.'}'.

			 n.'.product-options-sub legend{'.
			 n.'	font-size:11px;'.
			 n.'}'.
			 n.'.product-options-sub label{'.
			 n.'	font-size:11px;'.
			 n.'	padding-bottom: 5px;'.
			 n.'}'.

			 n.'#images ul.plain-list li{'.
			 n.'	float:left;'.
			 n.'	margin-right:10px;'.
			 n.'	margin-bottom:10px;'.
			 n.'	width:375px;'.
			 n.'}'.

			 n.'#images ul.plain-list li img{'.
			 n.'	border-width: 1px;'.
			 n.'	border-color:#BBBBBB;'.
			 n.'	border-style:solid;'.
			 n.'	padding:3px;'.
			 n.'	background-color: #E0E0E0;'.
			 n.'	float:left;'.
			 n.'}'.

			 n.'#images ul.plain-list li .imageEdit{'.
			 n.'	background-color:#E0E0E0;'.
			 n.'	height: 15px;'.
			 n.'	width: 250px;'.
			 n.'	text-align:left;'.
			 n.'	padding:3px 3px 3px 110px;'.
			 n.'	margin-top:0px;'.
			 n.'}'.

			 n.'#images ul.plain-list li .imageUpload{'.
			 n.'	margin-top: 5px;'.
			 n.'	padding:3px;'.
			 n.'	width: 275px;'.
			 n.'	border-width: 1px;'.
			 n.'	border-color:#E0E0E0;'.
			 n.'	border-style:solid;'.
			 n.'}'.

			 n.'</style>';
		
		doJS();
		
		
		echo n.n.'<form name="product" method="post" action="index.php" enctype="multipart/form-data">';


		echo '<input type="hidden" name="textile_body" value="1"/>';
		echo '<input type="hidden" name="textile_excerpt" value="1"/>';
		echo '<input type="hidden" name="Section" value="store"/>';

		echo hInput('ID', $ID).
			eInput('product').
			sInput($step).
			'<input type="hidden" name="view" />'.

			startTable('edit').

  		'<tr>'.n;

		//if ($view == 'text')
		//{

		//-- markup help --------------



		echo '<td id="article-main">';
		echo "<h4 class='productHeading'>Product name</h4>";

		echo '<p><input type="text" id="title" name="Title" value="'.cleanfInput($Title).'" class="edit" size="40" tabindex="1" />';

		if ( ($Status == 4 or $Status == 5) and $step != 'create')
		{
			include_once txpath.'/publish/taghandlers.php';

			echo sp.sp.'<a href="'.permlinkurl_id($ID).'">'.gTxt('view').'</a>';
		}

		echo '</p>';
		echo "<h4 class='productHeading'><span>Textile &amp; HTML allowed</span> Describe the product</h4>";
		echo n.graf('<textarea id="body" name="Body" cols="55" rows="31" tabindex="2" style="height: 180px;">'.htmlspecialchars($Body).'</textarea>');

		//PRODUCT OPTIONS
		//================================

		//echo '<div id="productOptions" style="border-width:1px; border-color:#E1E1E1; border-style:solid; padding:5px; background-color:#F3F4F4;">';

			//PRODUCT & PRICE
			//================================

			echo n.n.'<fieldset class="product-options-sub" style="background-color:white">'.
			'<div style="float:left; margin-right: 40px;">'.
			n.graf('<label for="price">Price</label> '.br.

				n.'<input style="width: 50px;" type="text" name="custom_1" id="price" value="'.cleanfInput($custom_1).'"/> <em>'.$general_settings['store_currency'].'</em>').n.
			'</div>'.
			n.graf('<label for="weight">Weight</label>'.br.
				n.'<input type="text" style="width: 50px;" name="custom_2" id="weight" value="'.cleanfInput($custom_2).'"/> <em>kg</em>');
			echo n."</fieldset>";
			
			//CUSTOM FIELDS
			//================================
			
			if(isset($ID)){
				$custom_fields = get_custom_fields($ID);
			}else{
				$custom_fields = '';
			}
			
			echo n.n.'<fieldset class="product-options-sub" style="background-color:white" id="custom_fields">'.
			n.'<legend>Custom Fields <span>[<a href="javascript:addCustomField(0);">Add a new custom field</a>]</span></legend>';
			echo $custom_fields;
			echo n."</fieldset>";
			
			//CATEGORIES
			//================================
			//if(!is_callable("rss_admin_catlist")){
				echo n.n.'<fieldset class="product-options-sub" style="background-color:white">'.
				n.'<legend>Category <small>[<a href="?event=category">edit</a>]</small></legend>'.
				'<div style="float:left; margin-right: 10px;">'.
				n.'<label for="category">Select existing category </label> '.br.
					n.build_list("category", "txp_category", "name", "title", $Category1, "parent='Products'", true, "ORDER BY name").n.
				'</div>'.
					n.graf('<label for="new_category_name">Or create a new category</label>'.br.
					n.'<input id="new_category_name" type="text" name="new_category_name"/>');
				echo n."</fieldset>";
			//}
			//VENDORS
			//================================
			echo n.n.'<fieldset class="product-options-sub" style="background-color:white">'.
			n.'<legend>Vendor <small>[<a href="?event=category">edit</a>]</small></legend>'.
			'<div style="float:left; width:50%;">'.
			n.'<label for="vendor">Select existing vendor </label> '.br.
				n.build_list("vendor", "txp_category", "name", "title", $Category2, "parent='Vendors'", true, "ORDER BY name").n.
			'</div>'.
				n.graf('<label for="new_vendor_name">Or create a new vendor</label>'.br.
				n.'<input id="new_vendor_name" type="text" name="new_vendor_name"/>');
			echo n."</fieldset>";

			//INVENTORY
			//================================
			echo n.'<fieldset class="product-options-sub" style="background-color:white">'.
			n.'<legend>Inventory</legend>';
			echo n.'<label for="sku">SKU <em>Stock keeping unit</em></label>'.n.br.
				   '<input type="text" name="custom_3" id="sku" value="'.$custom_3.'"/>'.br.br;
			if(isset($custom_4) && !empty($custom_4)){
				$showLevel = true;
				$levelSelected = "selected = \"selected\"";
				$levelStyle = 'display:block;';
			}else{
				$showLevel = false;
				$levelSelected = "";
				$levelStyle = 'display:none;';
			}
			
		echo n.'<select name="trackOptions" id="trackOptions" onchange="if(this.value == \'doTrack\'){$(\'#stockLevel\').show();}else{$(\'#stockLevel\').hide();}">'.n.
				 n.'<option value="dontTrack">Don\'t track stock level</option>'.
				 n.'<option value="doTrack" '.$levelSelected.'>Keep track of stock level</option>'.
				 //n.'<option value="virtual">Product is virtual</option>'.
				 n.'</select>'.br.br;
			echo n.'<div id="stockLevel" style="'.$levelStyle.'">'.
				 n.'<label for="items_in_stock">Number of items in stock:</label>'.
				 n.'<input type="text" name="custom_4" id="items_in_stock" style="width:20px;" value="'.cleanfInput($custom_4).'"/>'.
				 n.'</div><!--/stockLevel-->';
			
			echo n."</fieldset>";

			//IMAGES
			//================================

			doJS();
			
			if($step == "create"){
				echo n.'<fieldset class="product-options-sub" style="background-color:white">'.
				n.'<legend>Product images</legend>';
				echo n.'<em>Allowed file types are JPG, GIF &amp; PNG</em>';
				echo n.'<input type="file" name="uploadFile"/>'.n;
				echo n."</fieldset>";
			}else if($step == "edit" || $step=="save" || empty($step)){
				echo n.'<fieldset class="product-options-sub" id="images" style="background-color:white">'.
				n.'<legend>Product images</legend>';
				echo n.'<ul class="plain-list" id="image_list">';
				
				if(isset($Image) && !empty($Image)){
					echo n.'<li id="image1">
							<img src="'.product_image_display($Image, "small").'" alt="Product Image"/> <div class="imageEdit" style="display:block;">
							<a href="javascript:deleteImage(\'1\')">Delete Image</a> | <a href="javascript:updateImage(\'1\')">Update Image</a>

							<div class="imageUpload" id="imageUpload1" style="display:none;">
								Browse for new image: <input type="file" name="uploadFile1"/> and <input type="submit" value="Save"/>&nbsp; <a href="javascript:cancelUpload(\'1\')">Cancel</a>
								<input type="hidden" name="updateImage1" id="updateImage1" value="0"/>
							</div>
							<input id="imageField1" type="hidden" name="Image" value="'.$Image.'"/>

							</div>

							</li>';
				}
				if(isset($custom_6) && !empty($custom_6)){
					echo n.'<li id="image2"><img src="'.product_image_display($custom_6, "small").'" alt="Product Image"/>
						<div class="imageEdit" id="image1Control" style="display:block;">
							<a href="javascript:deleteImage(\'2\')">Delete Image</a> | <a href="javascript:updateImage(\'2\')">Update Image</a>

							<div class="imageUpload" id="imageUpload2" style="display:none;">
								Browse for new image: <input type="file" name="uploadFile2"/> and <input type="submit" value="Save"/> &nbsp; <a href="javascript:cancelUpload(\'2\')">Cancel</a>
								<input type="hidden" name="updateImage2" id="updateImage2" value="0"/>
							</div>

						</div><input id="imageField2" type="hidden" name="custom_6" value="'.$custom_6.'"/></li>';
				}
				if(isset($custom_7) && !empty($custom_7)){
					echo n.'<li id="image3"><img src="'.product_image_display($custom_7, "small").'" alt="Product Image"/>
						<div class="imageEdit" id="image1Control" style="display:block;">
							<a href="javascript:deleteImage(\'3\')">Delete Image</a> | <a href="javascript:updateImage(\'3\')">Update Image</a>

							<div class="imageUpload" id="imageUpload3" style="display:none;">
								Browse for new image: <input type="file" name="uploadFile3"/> and <input type="submit" value="Save"/> &nbsp; <a href="javascript:cancelUpload(\'3\')">Cancel</a>
								<input type="hidden" name="updateImage3" id="updateImage3" value="0"/>
							</div>

							</div>
							<input id="imageField3" type="hidden" name="custom_7" value="'.$custom_7.'"/></li>';
				}
				if(isset($custom_8) && !empty($custom_8)){
					echo n.'<li id="image4"><img src="'.product_image_display($custom_8, "small").'" alt="Product Image"/>
						<div class="imageEdit" id="image1Control" style="display:block;">
						<a href="javascript:deleteImage(\'4\')">Delete Image</a> | <a href="javascript:updateImage(\'4\')">Update Image</a>

							<div class="imageUpload" id="imageUpload4" style="display:none;">
								Browse for new image: <input type="file" name="uploadFile4"/> and <input type="submit" value="Save"/> &nbsp; <a href="javascript:cancelUpload(\'4\')">Cancel</a>
								<input type="hidden" name="updateImage4" id="updateImage4" value="0"/>
							</div>

						</div><input id="imageField4" type="hidden" name="custom_8" value="'.$custom_8.'"/></li>';
				}
				echo n."</ul><br style='clear:both;'/>";
				echo n.'<a href="javascript:void(0);" onclick="document.getElementById(\'otherImageUpload\').style.display = \'block\'">Add an image</a>'.n;
				echo n.'<div id="otherImageUpload" style="display:none; margin-top: 10px;">';
				echo n.'<em>Allowed file types are JPG, GIF &amp; PNG</em>';
				echo n.'<input type="file" name="uploadFile"/> and <input type="submit" value="upload"/>'.n;
				echo n.'</div>';
				echo n."</fieldset>";
				echo n.'<div id="data"></div>';
			}

		//echo '</div>'; // end productOptions

		// end left content area

		echo hInput('from_view',$view),
	'</td>';

	echo '<td id="article-col-2" style="padding-top: 75px;">'; //start article-col-2

		//PRODUCT STATUS
		//================================

		echo n.n.'<fieldset id="write-status">'.
				n.'<legend>'.gTxt('status').'</legend>'.
				n.status_radio_product($Status).
				n.'</fieldset>';





		//-- comments stuff --------------

		if($step=="create") {
			//Avoiding invite disappear when previewing
			$AnnotateInvite = (!empty($store_out['AnnotateInvite']))? $store_out['AnnotateInvite'] : $comments_default_invite;
			if ($comments_on_default==1) { $Annotate = 1; }
		}

		if ($use_comments == 1)
		{
			echo n.n.'<fieldset id="write-comments">'.
				n.'<legend>Allow product reviews</legend>';

			$comments_expired = false;

			if ($step != 'create' && $comments_disabled_after)
			{
				$lifespan = $comments_disabled_after * 86400;
				$time_since = time() - $sPosted;

				if ($time_since > $lifespan)
				{
					$comments_expired = true;
				}
			}

			if ($comments_expired)
			{
				echo n.n.graf(gTxt('expired'));
			}

			else
			{
				echo n.n.graf(
					onoffRadio('Annotate', $Annotate)
				).

				n.n.graf(
					'<label for="comment-invite">'.gTxt('comment_invitation').'</label>'.br.
					fInput('text', 'AnnotateInvite', $AnnotateInvite, 'edit', '', '', '', '', 'comment-invite')
				);
			}

			echo n.n.'</fieldset>';
		}
		/*if(is_callable("rss_admin_catlist")){
			echo "<fieldset id='write-sort'></fieldset>";
			echo rss_admin_catlist();
		}*/
		//wilshireone multipule categories
		
		
		
		//-- publish button --------------
		echo
		(has_privs('article.publish')) ?
		fInput('submit','publish',gTxt('save'),"publish", '', '', '', 4) :
		fInput('submit','publish',gTxt('save'),"publish", '', '', '', 4);

	echo '</td> <!--/article-col-2-->'; //end article-col-2
	echo '</td></tr></table></form>';

	}

function product_image_display($imageURL, $size){
		$str = str_replace("medium", $size, $imageURL);
		return hu."images/".$str;
	}


function product_save(){
		global $txp_user, $vars, $txpcfg, $prefs;

		extract($prefs);
		
		$incoming = psa($vars);
		define("IMPATH",$path_to_site.'/'.$img_dir.'/');

		$oldArticle = safe_row('Status, url_title, Title, Image, custom_6, custom_7, custom_8','textpattern','ID = '.(int)$incoming['ID']);

		if (! (    ($oldArticle['Status'] >= 4 and has_privs('article.edit.published'))
				or ($oldArticle['Status'] >= 4 and $incoming['AuthorID']==$txp_user and has_privs('article.edit.own.published'))
		    	or ($oldArticle['Status'] < 4 and has_privs('article.edit'))
				or ($oldArticle['Status'] < 4 and $incoming['AuthorID']==$txp_user and has_privs('article.edit.own'))))
		{
				// Not allowed, you silly rabbit, you shouldn't even be here.
				// Show default editing screen.
			product_edit();
			return;
		}
		$wrapper = new TXP_Wrapper();
		
		$incoming = $wrapper->textile_main_fields($incoming, $use_textile);
		
		//dmp($incoming);
		extract(doSlash($incoming));
		extract(array_map('assert_int', psa(array('ID', 'Status', 'textile_body', 'textile_excerpt'))));
		$Annotate = ( ps( 'Annotate')) ? assert_int( ps( 'Annotate')) : 0;

		if (!has_privs('article.publish') && $Status>=4) $Status = 3;

		if($reset_time) {
			$whenposted = "Posted=now()";
		} else {
			$when = strtotime($year.'-'.$month.'-'.$day.' '.$hour.':'.$minute.':'.$second)-tz_offset();
			$when = "from_unixtime($when)";
			$whenposted = "Posted=$when";
		}
		
		if (isset($new_category_name) && !empty($new_category_name)){
			$Category1 = $new_category_name;
			category_create($Category1,"Products");
		}else if (isset($category)){
			$Category1 = $category;
		}
		
		if (isset($new_vendor_name) && !empty($new_vendor_name)){
			$Category2 = $new_vendor_name;
			category_create($Category2,"Vendors");
		}else if (isset($vendor)){
			$Category2 = $vendor;
		}
		
		//Auto-Update custom-titles according to Title, as long as unpublished and NOT customized
		if ( empty($url_title)
			  || ( ($oldArticle['Status'] < 4)
					&& ($oldArticle['url_title'] == $url_title )
					&& ($oldArticle['url_title'] == stripSpace($oldArticle['Title'],1))
					&& ($oldArticle['Title'] != $Title)
				 )
		   )
		{
			$url_title = stripSpace($Title_plain, 1);
		}
		if (!$Annotate) $Annotate = 0;

		//IMAGE DELETE/UPDATE FUNCTIONALITY
		//=======================================
		if(strtolower($Image) == "delete"){

			$delPath = IMPATH.$oldArticle['Image'];
			
			unlink($delPath);
			unlink(str_replace("medium", "small", $delPath));
			unlink(str_replace("gif", "jpg", str_replace("medium", "large", $delPath)));
			$Image = "";
		}
		if(strtolower($custom_6) == "delete"){

			$delPath = IMPATH.$oldArticle['Image'];
			unlink($delPath);
			unlink(str_replace("medium", "small", $delPath));
			unlink(str_replace("gif", "jpg", str_replace("medium", "large", $delPath)));
			$custom_6 = "";
		}
		if(strtolower($custom_7) == "delete"){

			$delPath = IMPATH.$oldArticle['Image'];
			unlink($delPath);
			unlink(str_replace("medium", "small", $delPath));
			unlink(str_replace("gif", "jpg", str_replace("medium", "large", $delPath)));
			$custom_7 = "";
		}
		if(strtolower($custom_8) == "delete"){

			$delPath = IMPATH.$oldArticle['Image'];
			unlink($delPath) or die("can't delete file: ".$delPath);
			unlink(str_replace("medium", "small", $delPath));
			unlink(str_replace("gif", "jpg", str_replace("medium", "large", $delPath)));
			$custom_8 = "";
		}
		
		//UPDATE ARTICLE
		//=======================================
		safe_update("textpattern",
		   "Title           = '$Title',
			Body            = '$Body',
			Body_html       = '$Body_html',
			Excerpt         = '$Excerpt',
			Excerpt_html    = '$Excerpt_html',
			Keywords        = '$Keywords',
			Image           = '$Image',
			Status          =  $Status,
			LastMod         =  now(),
			LastModID       = '$txp_user',
			Section         = '$Section',
			Category1       = '$Category1',
			Category2       = '$Category2',
			Annotate        =  $Annotate,
			textile_body    =  $textile_body,
			textile_excerpt =  $textile_excerpt,
			override_form   = '$override_form',
			url_title       = '$url_title',
			AnnotateInvite  = '$AnnotateInvite',
			custom_1        = '$custom_1',
			custom_2        = '$custom_2',
			custom_3        = '$custom_3',
			custom_4        = '$custom_4',
			custom_5        = '$custom_5',
			custom_6        = '$custom_6',
			custom_7        = '$custom_7',
			custom_8        = '$custom_8',
			custom_9        = '$custom_9',
			custom_10       = '$custom_10',
			$whenposted",
			"ID = $ID"
		);

		save_custom_fields($_REQUEST['custom_fields'], $ID);


		//IMAGE UPLOAD
		//=======================
		
		if($_FILES['uploadFile']['error'] == "0"){
			$file = $_FILES['uploadFile'];
		}else if($_FILES['uploadFile1']['error'] == "0"){
			$file = $_FILES['uploadFile1'];
		}else if($_FILES['uploadFile2']['error'] == "0"){
			$file = $_FILES['uploadFile2'];
		}else if($_FILES['uploadFile3']['error'] == "0"){
			$file = $_FILES['uploadFile3'];
		}else if($_FILES['uploadFile4']['error'] == "0"){
			$file = $_FILES['uploadFile4'];
		}
		
		if ($file["type"] == "image/gif" || $file["type"] == "image/jpeg" || $file["type"] == "image/png"){
			// prepare the image for insertion

			//we need to check what images have been uploaded already
			$article_updated = safe_row("Image, custom_6, custom_7, custom_8", "textpattern", "ID = $ID");

			extract($article_updated);

			if(empty($Image)){
				$image_num = "";

			}else if(empty($custom_6)){
				$image_num = "2";

			}else if(empty($custom_7)){
				$image_num = "3";

			}else if(empty($custom_8)){
				$image_num = "4";
			}
			echo "Uploading image: ".$file['name'];
			
			$img = $file['tmp_name'];
			upload_image($img, $image_num, $ID);
			//echo "here"; die();
		}
		//END IMAGE UPLOAD
		//=======================


		if($Status >= 4) {
			if ($oldArticle['Status'] < 4) {
				if(!function_exists("do_pings")){
					require_once(txpath.'/include/txp_article.php');
					do_pings();
				}
			}
			update_lastmod();
		}
		
		product_edit("","","Product Saved");
		

	}

function product_post(){
		global $txp_user, $vars, $txpcfg, $prefs;

		extract($prefs);

		define("IMPATH",$path_to_site.'/'.$img_dir.'/');

		$incoming = psa($vars);
		$import = false;
		
		$message='';
		
		$wrapper = new TXP_Wrapper();
		$incoming = $wrapper->textile_main_fields($incoming, $use_textile);
		
		extract(doSlash($incoming));
		
		extract(array_map('assert_int', psa(array( 'Status', 'textile_body', 'textile_excerpt'))));
		$Annotate = ( ps( 'Annotate')) ? assert_int( ps( 'Annotate')) : 0;
		
		if($import){
			$Status = $product['Status'];
		}
		
		$when = 'now()';
		
		if ($Title or $Body or $Excerpt) {

			if (!has_privs('article.publish') && $Status>=4) $Status = 3;
			if (empty($url_title)) $url_title = stripSpace($Title_plain, 1);
			if (!$Annotate) $Annotate = 0;

			
			if (isset($new_category_name) && !empty($new_category_name)){
				$Category1 = $new_category_name;
				category_create($Category1,"Products");
			}else if (isset($category)){
				$Category1 = $category;
			}

			if (isset($new_vendor_name) && !empty($new_vendor_name)){
				$Category2 = $new_vendor_name;
				category_create($Category2,"Vendors");
			}else if (isset($vendor)){
				$Category2 = $vendor;
			}

			safe_insert(
			   "textpattern",
			   "Title           = '$Title',
				Body            = '$Body',
				Body_html       = '$Body_html',
				Excerpt         = '$Excerpt',
				Excerpt_html    = '$Excerpt_html',
				Image           = '$Image',
				Keywords        = '$Keywords',
				Status          =  $Status,
				Posted          =  $when,
				LastMod         =  now(),
				AuthorID        = '$txp_user',
				Section         = '$Section',
				Category1       = '$Category1',
				Category2       = '$Category2',
				textile_body    =  $textile_body,
				textile_excerpt =  $textile_excerpt,
				Annotate        =  $Annotate,
				override_form   = '$override_form',
				url_title       = '$url_title',
				AnnotateInvite  = '$AnnotateInvite',
				custom_1        = '$custom_1',
				custom_2        = '$custom_2',
				custom_3        = '$custom_3',
				custom_4        = '$custom_4',
				custom_5        = '$custom_5',
				custom_6        = '$custom_6',
				custom_7        = '$custom_7',
				custom_8        = '$custom_8',
				custom_9        = '$custom_9',
				custom_10       = '$custom_10',
				uid				= '".md5(uniqid(rand(),true))."',
				feed_time		= now()"
			);

			$GLOBALS['ID'] = mysql_insert_id();
			$ID = $GLOBALS['ID'];
			//print_r($_FILES);
			
			//CUSTOM FIELDS
			save_custom_fields($_REQUEST['custom_fields'], $ID);
			
			//IMAGE UPLOAD
			//=======================
			if ($_FILES["uploadFile"]["type"] == "image/gif" || $_FILES["uploadFile"]["type"] == "image/jpeg" || $_FILES["uploadFile"]["type"] == "image/png"){
			// prepare the image for insertion
				$img = $_FILES['uploadFile']['tmp_name'];

				upload_image($img, 1, $ID);

			}
			//END IMAGE UPLOAD
			//=======================

			if ($Status>=4) {

				if(!function_exists("do_pings")){
					require_once(txpath.'/include/txp_article.php');
					do_pings();
				}

				update_lastmod();
			}
			product_edit("","","Product Saved");
		} else product_edit();
	}

function product_category_option_list($Category){
		//$rows = product_category_list();
		$options = "";
		$rows = getTree('Products','article');
		foreach($rows as $cat){
			if($Category == $cat['name']){
				$selected = " selected=\"selected\"";
			}else{
				$selected = "";
			}
			$options .= "<option value=\"".$cat['name']."\"$selected>".str_repeat("&#160;",($cat['level']*2)).$cat['title']."</option>".n;
		}
		return $options;
	}

function product_category_list(){
		$rows = safe_rows("*", "txp_category", "type = 'Article' and name != 'root' parent='Products'");
		return $rows;
	}
function product_multiedit_form($page, $sort, $dir, $crit, $search_method){
		$methods = array(
			'delete'          => gTxt('delete')
		);

		return event_multiedit_form('product', $methods, $page, $sort, $dir, $crit, $search_method);
	}
	
	
function product_multi_edit() {
		
		global $txp_user;

		$selected = ps('selected');

		if (!$selected)
		{
			return products_list();
		}

		$method = ps('edit_method');
		$changed = false;
		$ids = array();

		if ($method == 'delete')
		{
			if (!has_privs('article.delete'))
			{
				$allowed = array();

				if (has_privs('article.delete.own'))
				{
					foreach ($selected as $id)
					{
						$id = assert_int($id);
						$author = safe_field('AuthorID', 'textpattern', "ID = $id");

						if ($author == $txp_user)
						{
							$allowed[] = $id;
						}
					}
				}

				$selected = $allowed;
			}

			foreach ($selected as $id)
			{
				$id = assert_int($id);

				if (safe_delete('textpattern', "ID = $id"))
				{
					$ids[] = $id;
				}
			}

			$changed = join(', ', $ids);
		}

		

		if ($changed)
		{
			return products_list(
				messenger('Product', $changed, (($method == 'delete') ? 'deleted' : 'modified' ))
			);
		}

		return products_list();
	}
	










?>
