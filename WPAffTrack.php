<?php  
/*
Plugin Name: WPAffTrack
Plugin URI: http://wpafftrack.com
Description: Cloak Your Affiliate Links with WordPress and Track them!
Version: 1.0.0.0
Author:Sohag Hasan
Author URI: http://hasan-sohag.blogspot.com
*/

$wplink = new wplink();
if(isset($wplink)) {
	register_activation_hook( __FILE__, array($wplink,'table_creation'));
	add_action('admin_print_scripts-toplevel_page_WP-aff-Setting',array($wplink,'javascript_adition'));
	add_action('admin_print_scripts-wp-affiliate-links_page_tracked-result',array($wplink,'javascript_adition'));
	add_action('admin_print_scripts-wp-affiliate-links_page_advanced-search',array($wplink,'javascript_adition'));
	//add_action('admin_enqueue_scripts',array($wplink,'javascript_adition'));
	add_action('init', array($wplink,'redirect'), 1);
	add_action('admin_menu', array($wplink,'CreateMenu'));
	//ajax for clear status
	add_action( 'wp_ajax_nopriv_tracking_ajax_data',array($wplink,'myajax_data'));
	add_action( 'wp_ajax_tracking_ajax_data',array($wplink,'myajax_data'));
	//ajax for pagination
	add_action( 'wp_ajax_pagination_ajax_data',array($wplink,'pagination_ajax'));
	add_action( 'wp_ajax_advanced-search-trackig',array($wplink,'search_ajax'));
	//add_action( 'wp_ajax_pagination_ajax_data',array($wplink,'pagination_ajax'));
	//add_action('adimn_print_styles-toplevel_page_WP-aff-Setting',array($wplink,'adding_css'),20);
	add_action('admin_enqueue_scripts',array($wplink,'adding_css'),20);

	if (isset($_POST['SubmitLinks'])) {$wplink->add_link($_POST['AffiliateLink']);
	}  
}   
class wplink{
	function adding_css(){	
		if(strstr($_SERVER['REQUEST_URI'], 'WP-aff-Setting') || strstr($_SERVER['REQUEST_URI'], 'tracked-result')){
		wp_register_style('wp_aff_style_css_sohag',plugins_url(basename(__DIR__)).'/css/style.css');
		wp_enqueue_style('wp_aff_style_css_sohag');
		wp_register_style('wp_aff_style_css_datepick',plugins_url(basename(__DIR__)).'/jquery-ui-datepicker/css/smoothness/jquery-ui-1.8.11.custom.css');
		wp_enqueue_style('wp_aff_style_css_datepick');
	}
	}
			
	
	function CreateMenu(){
	$a = add_menu_page(__('WP Affiliate Links'),__('WP Affiliate Links'),'activate_plugins','WP-aff-Setting',array($this,'OptionsPage'));
	
		add_submenu_page('WP-aff-Setting',__('result-default'),__('Tracking-Result'),'activate_plugins','tracked-result',array($this,'result'));
		add_submenu_page('WP-aff-Setting',__('link-structure'),__('Link Structure'),'activate_plugins','link-structure',array($this,'link_structure'));
				
				
	}
	//link sturcute function
	function link_structure(){
		if(isset($_REQUEST['link_structure'])){
			$link = trim($_REQUEST['wpafftrack-link']);
			$link = rtrim($link,'/');
			update_option('link_information',$link);
		}
		$data = get_option('link_information');
		//starin html form
	?>
		<div class="wrap">
			<?php screen_icon('options-general'); ?>
			<h2>IMdb Plugin's settings</h2>
			<form action="" method="post">
								
				<table class="form-table">						
						<tr>
							<td colspan="3"> Please Insert the link structure(home-url/folder1.../folder2/.../...) you want(e.g: home-url/baby) </td>
							
						</tr>
						<tr valign="top"><th scope="row">LikStruecutur(folder name) </th>
												
							<td colspan="3"> <input name="wpafftrack-link" type="text" value= "<?php echo $data; ?>" /></td>												
						</tr>
						<tr>
							<td>
							<input name="link_structure" type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
							</td>
						<tr>
					</table>
			</form>
		</div>
		
	<?php
	}
		
	
	function result(){
		?>	
		<div class="wrap">			
			
			
			<div style="float:left;margin-right:10px"><h2 id="header-tracking">Tracking Results By</h2>
			</div>
			<select name="result-select" id="advanced-select">
				<option value="default">Default</option>
				<option value="date">Date</option>
			</select>
			<form class="form-hiding" id="tracking-result-form">
				<p class="good-pagination">YYYY-MM-DD</p>
				Slug(optional)<input id="slug-ajax" type="text" value="" />&nbsp&nbspFrom *<input type="text" value="" id="ssdate">&nbsp;&nbsp; To *<input type="text" value="" id="eedate">
				<input type="button" value="go!" id="sseesubmit">
			</form>
			
				<div id="default-result">
					<table class="widefat">
						<thead>
							<tr>
								<th>Slug Name</th>								
								<th>Total Clicks</th>
								<th>Unique Clicks</th>								
								<th>Last Clicked</th>
								<th id="remove-affdata-all" class="remove-data-afflink-single">
									<a href="#">Clear All Stats</a>
								</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Slug Name</th>								
								<th>Total Clicks</th>
								<th>Unique Clicks</th>
								<th>Last Clicked</th>
								<th id="remove-affdata-all" class="remove-data-afflink-single">
									<a href="#">Clear All Stats</a>
								</th>
							</tr>
						</tfoot>
						<tbody>
							
							<?php
								global $wpdb;
								
								$result = mysql_query("SELECT * FROM wp_affiliate ORDER BY LCASE(name) ASC",$wpdb->dbh);
								
								if(!empty($result)) : 
									while ($slug = mysql_fetch_assoc($result)) {
									
								?>
										<tr>
											<td>&nbsp<?php echo $slug["name"]; ?></td>
											<td>
												&nbsp &nbsp &nbsp										
												<?php echo ($slug['countt']) ? $slug['countt']:0 ; ?>
											</td>
											<td>&nbsp &nbsp &nbsp										
												<?php echo ($slug['countu']) ? $slug['countu']:0 ; ?>
											</td>
											<td> 										
												<?php echo ($slug['clktime']!=0)?date("F j,Y",$slug['clktime']):'Not yet clicked'  ; ?>
											</td>
											<td id="<?php echo 'wpaff_'.$slug["name"]; ?>" class="remove-data-afflink-single" >
												<input type="button" value="Clear Stats" class="button-highlighted" />
											</td>
										</tr>
									
								<?php	
									}
									
								endif;
								
							?>

						</tbody>
					</table>
					</div>
				</div>
		
	<?php 
		}
		
		function search(){
			echo '<h2>under construction</h2>';
		}
	
		function OptionsPage(){
			global $wpdb;
				$rows = array(); 
				$myrows = $wpdb->get_results( "SELECT id FROM wp_affiliate",ARRAY_A);
				
				$rows = array();
				foreach($myrows as $key){
					$rows[]=$key['id'];
				}
				$page_number = ceil(count($rows)/10);
				for($i=1;$i<=$page_number;$i++){
					$pagination .= '<a href="#" id ="page-numbers_'.$i.'" class="page-numbers">'.$i.'</a>';
					$pagination1 .= '<a href="#" id ="page-numbers'.$i.'" class="page-numbers">'.$i.'</a>';
				}
				
				if(!empty($rows)){
					$new_entry = max($rows)+1;
				}
	?>
			<div  id="pagination-class-sanitize" class="wrap">
					<div class="tablenav">
						<div class="tablenav-pages">
							<span id="displaying-num-ajax" class="displaying-num">
								Displaying 1-10 of 
							</span>
							<span class="displaying-num"><b><?php echo count($rows); ?></b></span>
							<?php echo $pagination; ?>
							
						</div>
					</div>
				</div>
				
			<div class="wrap"><h2>WP Aff Track - Version 1.0.0</h2>
			<form method="post" action="admin.php?page=WP-aff-Setting">
			<div id="table-div-for-ajax" class="table-div-ajax">
				<table cellpadding=3>
				<tr>
				<td><b>Slug:</b></td>
				<td><b>Affiliate Link:</b></td>
				<td><b>Cloaked Link:<b></td>
				<td><b>Description:<b></td>
				</tr>
				
				<?php 
					echo $this->get_links();
				?>
				
				<tr>
					<td colspan="30px"><h2>Add New Link<h2></td>
				</tr>
				<tr>				
					<td>
					<input type="text" name="AffiliateLink[slug][<?php $new_entry; ?>]" value="" style="width:10em" />&nbsp;</td><td>
					<input type="text" name="AffiliateLink[destination][<?php $new_entry; ?>]" value="" style="width:20em;" /></td>
					<td>&nbsp&nbsp&nbsp</td>
					<td><textarea name="AffiliateLink[des][<?php $new_entry; ?>]" cols="25" rows="2"></textarea></td>
				</tr>
				</table>
			</div>
			<p class="submit"><input id="affiliateSubmit" type="submit" name="SubmitLinks" class="button-primary" value="<?php _e('Save Links') ?>" /></p>			
			</form>			
					<div  id="pagination-class-sanitize" class="wrap">
					<div class="tablenav">
						<div class="tablenav-pages">
							<span id="displaying-num-ajaxx" class="displaying-num">
								Displaying 1-10 of 
							</span>
							<span class="displaying-num"><b><?php echo count($rows); ?></b></span>
							<?php echo $pagination1; ?>
							
						</div>
					</div>
				</div>
				
				<iframe src="http://wpaffiliatelinks.com/updates.htm" name="update" frameborder="0" scrolling="no" height="200" width="600"></iframe>
		
	<?php
	}  
	
	function get_links()  {
		$extention = (get_option('link_information'))?get_option('link_information'):'';
		global $wpdb;
		//$affiliate_link = get_option('AffiliateLink');
		$result = mysql_query("SELECT * FROM wp_affiliate LIMIT 0,10",$wpdb->dbh);
		//$result = $wpdb->query("SELECT * FROM wp_affiliate");
		$links = '';
		$home_url=get_option('home');
		while ($slug = mysql_fetch_assoc($result)) {
					
			$links .= '<tr><td><input type="text" name="AffiliateLink[slug]['.$slug["id"].']" value="'.$slug["name"].'" style="width:10em"/>&nbsp;</td><td><input type="text" name="AffiliateLink[destination]['.$slug["id"].']" value="'.$slug["afflink"].'" style="width:20em;" /></td>
			<td style="width:20em"><a href='.$home_url.$extention.'/'.$slug["name"].' target=_blank>'.$home_url.$extention.'/'.$slug["name"].'</td><td><textarea rows="2" cols="25" name="AffiliateLink[des]['.$slug["id"].']">'.$slug["comment"].'</textarea></td></tr>'; 
			
		}      
		return $links;
	}
	
	//link additon
	function add_link($new_link){
		
		//creting an arry for the indexing
		foreach($new_link["slug"] as $key=>$value){
			$link_no[]=$key;
		}
		
		global $wpdb;		
		
		$loop = count ($link_no)-1;		
		$check = trim($new_link['slug'][$link_no[$loop]]);
		
		//echo $check;		
		$check_array = $wpdb->get_col("SELECT `name` FROM wp_affiliate");
		//var_dump($check_array);				
		foreach($check_array as $value){
			$slugg = trim($value);
			if($check === $slugg){
				add_action('admin_notices',array($this,'donemessage'));
				return;
			}
		}
		
		foreach($link_no as $index=>$id){
			
			$slug = trim($new_link['slug'][$id]);
			$destination = trim($new_link['destination'][$id]);
			$description = strip_tags($new_link['des'][$id]);
			//checking duplicate slug			
			
			//slug empty checking and database updating
			if ($slug == '' || $destination == ''){
				mysql_query("DELETE FROM wp_affiliate WHERE id='$id'",$wpdb->dbh);
				continue;
			}
			if($index == $loop){
					$wpdb->insert('wp_affiliate',array('name'=>$slug,'afflink'=>$destination,'comment'=>$description),array('%s','%s','%s'));
				}
			else{
					//echo $id.$slug.'<br/>';
					mysql_query("UPDATE wp_affiliate SET name='$slug',afflink='$destination',comment='$description' WHERE id='$id'",$wpdb->dbh);
					
				}
			}
		
		//exit;
	} //end of add link      
			
	function redirect(){
		$extention = (get_option('link_information'))? get_option('link_information'):'';
		$home_url = $this->get_url(get_option('home').$extention,'',$this->cloak());		
		$home_url = rtrim($home_url,'/');
		//var_dump($home_url);
		//exit;		
		global $wpdb;
		$time = time();
		//$affiliate_link = get_option('AffiliateLink');
		$result = mysql_query("SELECT * FROM wp_affiliate",$wpdb->dbh);	
		if($result) :
		while ($goto = mysql_fetch_assoc($result)) {
					
				if(urldecode(trim($home_url, '/')) == trim($goto["name"],'/')){
		
				$slug = urldecode(trim($home_url,'/'));
				
				if(!current_user_can('activate_plugins')) : 
					
					//retriveving cookie
					$cookie = ($_COOKIE['simple_affiliate_tracking'])?$_COOKIE['simple_affiliate_tracking']:'';
					
					$new_cookie = $cookie.'_'.$slug;
					$vri = explode('_',$cookie);
					if(in_array($slug,$vri)){
						$countt = $goto["countt"]+1;
						mysql_query("UPDATE wp_affiliate SET countt='$countt',clktime='$time' WHERE name='$slug'",$wpdb->dbh);
					}
					else{
						$countt = $goto["countt"]+1;
						$countu = $goto["countu"]+1;
						if(setcookie('simple_affiliate_tracking',$new_cookie,time()+300*24*3600)){
						mysql_query("UPDATE wp_affiliate SET countt='$countt',countu='$countu',clktime='$time' WHERE name='$slug'",$wpdb->dbh);
						}
					}
				
				endif;
				
				header ('Location: ' . $goto["afflink"]);
				exit;
				
				}
			}
			endif;
	}
	
	function cloak(){
		$url = $_SERVER['HTTPS']== 'on' ? 'https' : 'http';
		return $url.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}        
		
	function get_url($A,$B,$C){
		$D = chr(1);
		$E= strtolower($C);
		$F = strtolower($A);
		while (($G=strpos($E,$F))!==FALSE){ $C = substr_replace($C,$D,$G,strlen($A));

		$E = substr_replace($E,$D,$G,strlen($A));}
		$C = str_replace($D,$B,$C);
		return $C;
	}
	
	
	//function for adding javascript
	function javascript_adition(){
		wp_enqueue_script('jquery');
		
		wp_enqueue_script('datepicking_js',plugins_url(basename(__DIR__)).'/jquery-ui-datepicker/jquery-ui-1.8.11.custom.min.js',array('jquery'));
				
		wp_enqueue_script('affiliate_js',plugins_url(basename(__DIR__)).'/js/afflink.js',array('jquery'));
		
		global $wpdb;
		$nonce=wp_create_nonce('wp-affiliate-tracking');
		$rows = $wpdb->get_results( "SELECT id FROM wp_affiliate",ARRAY_A);
		$page_number = ceil(count($rows)/10);
		//localising scripts
		wp_localize_script( 'affiliate_js', 'AffAjax', array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonce' => $nonce,
					'pluginsurl' => plugins_url(basename(__DIR__)),
					'pageurl' => admin_url('/admin.php?page=tracked-result'),
					'pageno' => $page_number
				));

	}
	
	/*****************************************************************************************************
						AJAX DATA MANIPULATION FOR RESETING COUNT
	 * ****************************************************************************************************/
	 function myajax_data(){
		 global $wpdb;
		$nonce = $_REQUEST['nonce'];
		$message = null;
		$id = $_REQUEST['id'];
		$name = preg_replace('/^wpaff_/','',$id);
		if(wp_verify_nonce($nonce, 'wp-affiliate-tracking')){
			if($id == "remove-affdata-all"){
				$wpdb->query("UPDATE wp_affiliate SET countt=0,countu=0,clktime=0 WHERE countt!=0 OR countu!=0");
				$message = 'All records are empty!';
			}
		
			else {
				$wpdb->query("UPDATE wp_affiliate SET countt=0,countu=0,clktime=0 WHERE name='$name'");
				$message = 'Operation successfull!';
			}
			
		}
		else{
			$message = 'security error!';
		}
		//sending alert message
		echo $message;
		exit;
	}
	
/**********************************************************************************************
 * 							AJAX DATA TO FOR PAGINATION
 * ****************************************************************************************/	
	function pagination_ajax(){
		$extention = (get_option('link_information'))?get_option('link_information'):'';
		$start = $_REQUEST['start'];
		$end = $_REQUEST['end'];
		$nonce = $_REQUEST['nonce'];
		
		$home_url=get_option('home');
		global $wpdb;
		$rows = array(); 
		$myrows = $wpdb->get_results( "SELECT id FROM wp_affiliate",ARRAY_A);
				
		$rows = array();
		foreach($myrows as $key){
			$rows[]=$key['id'];
		}
		if(!empty($rows)){
			$new_entry = max($rows)+1;
		}
		
		//database query
		$results = $wpdb->get_results("SELECT * FROM wp_affiliate LIMIT $start,10",ARRAY_A);
		
		$table_starting = '<table cellpadding=3>
				<tr>
				<td><b>Slug:</b></td>
				<td><b>Affiliate Link:</b></td>
				<td><b>Cloaked Link:<b></td>
				<td><b>Description:<b></td>
				</tr>';
			$table_ending = '<tr>
					<td colspan="30px"><h2>Add New Link<h2></td>
				</tr>
				<tr>				
					<td>
					<input type="text" name="AffiliateLink[slug]['.$new_entry.']" value="" style="width:10em" />&nbsp;</td><td>
					<input type="text" name="AffiliateLink[destination]['.$new_entry.']" value="" style="width:20em;" /></td>
					<td>&nbsp&nbsp&nbsp</td>
					<td><textarea name="AffiliateLink[des]['.$new_entry.']" cols="25" rows="2"></textarea></td>
				</tr>
				</table>';
		$table_body = '';
		
		foreach($results as $slug){
			
			$table_body .= '<tr><td><input type="text" name="AffiliateLink[slug]['.$slug["id"].']" value="'.$slug["name"].'" style="width:10em"/>&nbsp;</td><td><input type="text" name="AffiliateLink[destination]['.$slug["id"].']" value="'.$slug["afflink"].'" style="width:20em;" /></td>
			<td style="width:20em"><a href='.$home_url.$extention.'/'.$slug["name"].' target=_blank>'.$home_url.$extention.'/'.$slug["name"].'</td><td><textarea rows="2" cols="25" name="AffiliateLink[des]['.$slug["id"].']">'.$slug["comment"].'</textarea></td></tr>'; 
		}
		echo $table_starting.$table_body.$table_ending;
		exit;
	}
	
/*********************************************************************************************
 * 					AJAX FOR SEARCHING
 * **************************************************************************************/	
	function search_ajax(){
		$sdate = strtotime($_REQUEST['sdate']);
		$edate = strtotime($_REQUEST['edate']);
		$name = trim($_REQUEST['slug']);
		global $wpdb;
		//echo $name;
		//echo $sdate.'<br/>'.$edate.'<br/>'.$name;
		
		if(isset($sdate) && ($edate)){	
			
			$table_head_tail = '
					<table class="widefat">
						<thead>
							<tr>
								<th>Slug Name</th>								
								<th>Total Clicks</th>
								<th>Unique Clicks</th>								
								<th>Last Clicked</th>
								<th id="remove-affdata-all" class="remove-data-afflink-single">
									<a href="#">Clear All Stats</a>
								</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Slug Name</th>								
								<th>Total Clicks</th>
								<th>Unique Clicks</th>
								<th>Last Clicked</th>
								<th id="remove-affdata-all" class="remove-data-afflink-single">
									<a href="#">Clear All Stats</a>
								</th>
							</tr>
						</tfoot>
						<tbody>';
			$table_final = '</tbody>
					</table>';
			$table_body = '';
			$results = $wpdb->get_results( "SELECT * FROM wp_affiliate ORDER BY name ASC",ARRAY_A);
			
			if(!empty($results)){
				foreach($results as $slug){
					
					if(($slug['clktime'] >= $sdate)){
						if(($slug['clktime'] <= $edate)){
							if(empty($name)){
								$slug_countt = ($slug['countt'])?$slug['countt']:0;
								$slug_countu = ($slug['countu'])?$slug['countu']:0;
								$clkdate = ($slug['clktime'])?date("F j,Y",$slug['clktime']):'Not yet clicked';
								$table_body .= '<tr>
										<td>&nbsp'.$slug["name"].'</td>
										<td>
										&nbsp &nbsp &nbsp'.$slug_countt.'</td>
										<td>&nbsp &nbsp &nbsp'.$slug_countu.'</td><td>
										'.$clkdate.'</td><td id="wpaff_'.$slug["name"].'" class="remove-data-afflink-single">
										<input type="button" value="Clear Stats" class="button-highlighted" />
										</td>
									</tr>';
							}
							else{
								if($name == $slug['name']){
									$slug_countt = ($slug['countt'])?$slug['countt']:0;
									$slug_countu = ($slug['countu'])?$slug['countu']:0;
									$clkdate = ($slug['clktime'])?date("F j,Y",$slug['clktime']):'Not yet clicked';
									$table_body .= '<tr>
										<td>&nbsp'.$slug["name"].'</td>
										<td>
										&nbsp &nbsp &nbsp'.$slug_countt.'</td>
										<td>&nbsp &nbsp &nbsp'.$slug_countu.'</td><td>
										'.$clkdate.'</td><td id="wpaff_'.$slug["name"].'" class="remove-data-afflink-single">
										<input type="button" value="Clear!" class="button-highlighted" />
										</td>
									</tr>';
								}
							}
						}
					}
				}
			}
			if($table_body != ''){
				//echo $table_head_tail.$table_body.$table_final;
				$message = $table_head_tail.$table_body.$table_final;
			}
			else{
				$message = 'nor';
			}
			
		}
		else {
			$message = 'datep';
		}
		echo $message;
		exit;
	} 
/*************************************************************************************************
				database table creation
 * **********************************************************************************************/
		function table_creation(){
			global $wpdb;
			$table = $wpdb->prefix.'affiliate';
			$sql = "CREATE TABLE IF NOT EXISTS `$table`(
				`id` int unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(20) NOT NULL collate utf8_bin,
				`afflink` varchar(50) NOT NULL,	
				`countt` int unsigned DEFAULT 0,
				`countu` int unsigned DEFAULT 0,
				`clktime` int unsigned DEFAULT 0,
				`comment` varchar(254) DEFAULT '',
				PRIMARY KEY(id),
				UNIQUE(name)
				)";
			//loading the dbDelta function manually
			require_once(ABSPATH.'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			
		}
		 
		 //function to show an error
		 function donemessage(){
			echo '<div id="message" class="error"><p>Sorry! Duplicate Slug found</p></div>';
			
		}

}


?>
