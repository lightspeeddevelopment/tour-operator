<?php

define('LSX_TO_POSTEXPIRATOR_DATEFORMAT',__('l F jS, Y','lsx-tour-operators'));
define('LSX_TO_POSTEXPIRATOR_TIMEFORMAT',__('g:ia','lsx-tour-operators'));
define('LSX_TO_POSTEXPIRATOR_TYPES',array('special','tour'));

function lsx_to_expirationdate_add_column($columns,$type) {
	if (in_array($type, LSX_TO_POSTEXPIRATOR_TYPES)) {
	  	$columns['lsx_to_expirationdate'] = __('Expires','lsx-tour-operators');
	}

  	return $columns;
}
add_filter('manage_posts_columns', 'lsx_to_expirationdate_add_column', 10, 2);

function lsx_to_expirationdate_show_value($column_name) {
	global $post;
	$id = $post->ID;
	
	if ($column_name === 'lsx_to_expirationdate') {
		$ed = get_post_meta($id,'_lsx_to_expiration-date',true);
		echo ($ed ? get_date_from_gmt(gmdate('Y-m-d H:i:s',$ed),get_option('date_format').' '.get_option('time_format')) : __("Never",'lsx-tour-operators'));
  	}
}
add_action ('manage_posts_custom_column', 'lsx_to_expirationdate_show_value');
add_action ('manage_pages_custom_column', 'lsx_to_expirationdate_show_value');

function lsx_to_expirationdate_meta_custom() {
	$custom_post_types = LSX_TO_POSTEXPIRATOR_TYPES;
	
	foreach ($custom_post_types as $t) {
		add_meta_box('lsxtoexpirationdatediv', __('Expires','lsx-tour-operators'), 'lsx_to_expirationdate_meta_box', $t, 'side', 'core');
	}
}
add_action('add_meta_boxes','lsx_to_expirationdate_meta_custom');

function lsx_to_expirationdate_meta_box($post) { 
	$expirationdatets = get_post_meta($post->ID,'_lsx_to_expiration-date',true);
	$firstsave = get_post_meta($post->ID,'_lsx_to_expiration-date-status',true);
	$expireType = '';
	
	if (empty($expirationdatets)) {
		$defaultmonth 	=	date_i18n('m');
		$defaultday 	=	date_i18n('d');
		$defaulthour 	=	date_i18n('H');
		$defaultyear 	=	date_i18n('Y');
		$defaultminute 	= 	date_i18n('i');

		$enabled = '';
		$disabled = ' disabled="disabled"';
	} else {
		$defaultmonth 	=	get_date_from_gmt(gmdate('Y-m-d H:i:s',$expirationdatets),'m');
		$defaultday 	=	get_date_from_gmt(gmdate('Y-m-d H:i:s',$expirationdatets),'d');
		$defaultyear 	=	get_date_from_gmt(gmdate('Y-m-d H:i:s',$expirationdatets),'Y');
		$defaulthour 	=	get_date_from_gmt(gmdate('Y-m-d H:i:s',$expirationdatets),'H');
		$defaultminute 	=	get_date_from_gmt(gmdate('Y-m-d H:i:s',$expirationdatets),'i');
		$enabled 	= 	' checked="checked"';
		$disabled 	= 	'';
		$opts 		= 	get_post_meta($post->ID,'_lsx_to_expiration-date-options',true);
		
		if (isset($opts['expireType'])) {
			$expireType = $opts['expireType'];
		}
	}

	$rv = array();
	$rv[] = '<p><input type="checkbox" name="enable-lsx-to-expirationdate" id="enable-lsx-to-expirationdate" value="checked"'.$enabled.' onclick="lsx_to_expirationdate_ajax_add_meta(\'enable-lsx-to-expirationdate\')" />';
	$rv[] = '<label for="enable-lsx-to-expirationdate">'.__('Enable Post Expiration','lsx-tour-operators').'</label></p>';

	$rv[] = '<table><tr>';
	$rv[] = '<th style="text-align: left;">'.__('Year','lsx-tour-operators').'</th>';
	$rv[] = '<th style="text-align: left;">'.__('Month','lsx-tour-operators').'</th>';
	$rv[] = '<th style="text-align: left;">'.__('Day','lsx-tour-operators').'</th>';
	$rv[] = '</tr><tr>';
	$rv[] = '<td>';	
	$rv[] = '<select name="lsx_to_expirationdate_year" id="lsx_to_expirationdate_year"'.$disabled.'>';
	$currentyear = date('Y');

	if ($defaultyear < $currentyear) $currentyear = $defaultyear;

	for($i = $currentyear; $i < $currentyear + 8; $i++) {
		if ($i == $defaultyear)
			$selected = ' selected="selected"';
		else
			$selected = '';
		$rv[] = '<option'.$selected.'>'.($i).'</option>';
	}
	$rv[] = '</select>';
	$rv[] = '</td><td>';
	$rv[] = '<select name="lsx_to_expirationdate_month" id="lsx_to_expirationdate_month"'.$disabled.'>';

	for($i = 1; $i <= 12; $i++) {
		if ($defaultmonth == date_i18n('m',mktime(0, 0, 0, $i, 1, date_i18n('Y'))))
			$selected = ' selected="selected"';
		else
			$selected = '';
		$rv[] = '<option value="'.date_i18n('m',mktime(0, 0, 0, $i, 1, date_i18n('Y'))).'"'.$selected.'>'.date_i18n('F',mktime(0, 0, 0, $i, 1, date_i18n('Y'))).'</option>';
	}

	$rv[] = '</select>';	 
	$rv[] = '</td><td>';
	$rv[] = '<input type="text" id="lsx_to_expirationdate_day" name="lsx_to_expirationdate_day" value="'.$defaultday.'" size="2"'.$disabled.' />,';
	$rv[] = '</td></tr><tr>';
	$rv[] = '<th style="text-align: left;"></th>';
	$rv[] = '<th style="text-align: left;">'.__('Hour','lsx-tour-operators').'('.date_i18n('T',mktime(0, 0, 0, $i, 1, date_i18n('Y'))).')</th>';
	$rv[] = '<th style="text-align: left;">'.__('Minute','lsx-tour-operators').'</th>';
	$rv[] = '</tr><tr>';
	$rv[] = '<td>@</td><td>';
 	$rv[] = '<select name="lsx_to_expirationdate_hour" id="lsx_to_expirationdate_hour"'.$disabled.'>';

	for($i = 1; $i <= 24; $i++) {
		if ($defaulthour == date_i18n('H',mktime($i, 0, 0, date_i18n('n'), date_i18n('j'), date_i18n('Y'))))
			$selected = ' selected="selected"';
		else
			$selected = '';
		$rv[] = '<option value="'.date_i18n('H',mktime($i, 0, 0, date_i18n('n'), date_i18n('j'), date_i18n('Y'))).'"'.$selected.'>'.date_i18n('H',mktime($i, 0, 0, date_i18n('n'), date_i18n('j'), date_i18n('Y'))).'</option>';
	}

	$rv[] = '</select></td><td>';
	$rv[] = '<input type="text" id="lsx_to_expirationdate_minute" name="lsx_to_expirationdate_minute" value="'.$defaultminute.'" size="2"'.$disabled.' />';
	$rv[] = '</td></tr></table>';
	
	$rv[] = '<input type="hidden" name="lsx_to_expirationdate_formcheck" value="true" />';
	echo implode("\n",$rv);

	echo '<br/>'.__('How to expire','lsx-tour-operators').': ';
	echo lsx_to_post_expirator_expire_type(array('type' => $post->post_type, 'name'=>'lsx_to_expirationdate_expiretype','selected'=>$expireType,'disabled'=>$disabled));
}

function lsx_to_expirationdate_js_admin_header() {
	?>
	<script type="text/javascript">
		//<![CDATA[
		function lsx_to_expirationdate_ajax_add_meta(expireenable) {
			var expire = document.getElementById(expireenable);

			if (expire.checked == true) {
				if (document.getElementById('lsx_to_expirationdate_month')) {
					document.getElementById('lsx_to_expirationdate_month').disabled = false;
					document.getElementById('lsx_to_expirationdate_day').disabled = false;
					document.getElementById('lsx_to_expirationdate_year').disabled = false;
					document.getElementById('lsx_to_expirationdate_hour').disabled = false;
					document.getElementById('lsx_to_expirationdate_minute').disabled = false;
				}
				
				document.getElementById('lsx_to_expirationdate_expiretype').disabled = false;
			} else {
				if (document.getElementById('lsx_to_expirationdate_month')) {
					document.getElementById('lsx_to_expirationdate_month').disabled = true;
					document.getElementById('lsx_to_expirationdate_day').disabled = true;
					document.getElementById('lsx_to_expirationdate_year').disabled = true;
					document.getElementById('lsx_to_expirationdate_hour').disabled = true;
					document.getElementById('lsx_to_expirationdate_minute').disabled = true;
				}
				
				document.getElementById('lsx_to_expirationdate_expiretype').disabled = true;
			}
			
			return true;
		}
		//]]>
	</script>
<?php
}
add_action('admin_head', 'lsx_to_expirationdate_js_admin_header' );

function lsx_to_expirationdate_update_post_meta($id) {
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		return;

	$posttype = get_post_type($id);
	
	if ( $posttype == 'revision' )
		return;

	if (!isset($_POST['lsx_to_expirationdate_formcheck']))
		return;

	if (isset($_POST['enable-lsx-to-expirationdate'])) {
		$month	 = intval($_POST['lsx_to_expirationdate_month']);
		$day 	 = intval($_POST['lsx_to_expirationdate_day']);
		$year 	 = intval($_POST['lsx_to_expirationdate_year']);
   		$hour 	 = intval($_POST['lsx_to_expirationdate_hour']);
		$minute  = intval($_POST['lsx_to_expirationdate_minute']);
		
		$opts = array();
		$ts = get_gmt_from_date("$year-$month-$day $hour:$minute:0",'U');

		$opts['expireType'] = $_POST['lsx_to_expirationdate_expiretype'];
		$opts['id'] = $id;
	
		lsx_to_schedule_expirator_event($id,$ts,$opts);
	} else {
		lsx_to_unschedule_expirator_event($id);
	}
}
add_action('save_post','lsx_to_expirationdate_update_post_meta');

function lsx_to_schedule_expirator_event($id,$ts,$opts) {
	if (wp_next_scheduled('lsxToPostExpiratorExpire',array($id)) !== false) {
		wp_clear_scheduled_hook('lsxToPostExpiratorExpire',array($id));
	}
		
	wp_schedule_single_event($ts,'lsxToPostExpiratorExpire',array($id)); 

	update_post_meta($id, '_lsx_to_expiration-date', $ts);
	update_post_meta($id, '_lsx_to_expiration-date-options', $opts);
	update_post_meta($id, '_lsx_to_expiration-date-status','saved');
}

function lsx_to_unschedule_expirator_event($id) {
	delete_post_meta($id, '_lsx_to_expiration-date'); 
	delete_post_meta($id, '_lsx_to_expiration-date-options');

	if (wp_next_scheduled('lsxToPostExpiratorExpire',array($id)) !== false) {
		wp_clear_scheduled_hook('lsxToPostExpiratorExpire',array($id));
	}

	update_post_meta($id, '_lsx_to_expiration-date-status','saved');
}

function lsx_to_post_expirator_expire($id) {
	if (empty($id)) { 
		return false;
	}

	if (is_null(get_post($id))) {
		return false;
	}

	$postoptions = get_post_meta($id,'_lsx_to_expiration-date-options',true);
	extract($postoptions);

	if (empty($expireType)) {
		$posttype = get_post_type($id);
		$expireType = apply_filters('lsx_to_postexpirator_custom_posttype_expire', $expireType, $posttype);
	}

	kses_remove_filters();

	// Do Work
	if ($expireType == 'draft') {
		wp_update_post(array('ID' => $id, 'post_status' => 'draft'));
	} elseif ($expireType == 'private') {
		wp_update_post(array('ID' => $id, 'post_status' => 'private'));
	} elseif ($expireType == 'delete') {
		wp_delete_post($id);
	}
}
add_action('lsxToPostExpiratorExpire','lsx_to_post_expirator_expire');

function lsx_to_post_expirator_expire_type($opts) {
	if (empty($opts)) return false;

	extract($opts);
	
	if (!isset($name)) return false;
	if (!isset($id)) $id = $name;
	if (!isset($disabled)) $disabled = false;
	if (!isset($onchange)) $onchange = '';
	if (!isset($type)) $type = '';

	$rv = array();
	$rv[] = '<select name="'.$name.'" id="'.$id.'"'.($disabled == true ? ' disabled="disabled"' : '').' onchange="'.$onchange.'">';
	$rv[] = '<option value="draft" '. ($selected == 'draft' ? 'selected="selected"' : '') . '>'.__('Draft','lsx-tour-operators').'</option>';
	$rv[] = '<option value="delete" '. ($selected == 'delete' ? 'selected="selected"' : '') . '>'.__('Delete','lsx-tour-operators').'</option>';
	$rv[] = '<option value="private" '. ($selected == 'private' ? 'selected="selected"' : '') . '>'.__('Private','lsx-tour-operators').'</option>';
	
	$rv[] = '</select>';
	return implode("<br/>/n",$rv);
}
