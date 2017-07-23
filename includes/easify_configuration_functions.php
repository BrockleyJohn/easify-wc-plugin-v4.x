<?php
/**
-
  functions for configuration settings for easify v4 interface

  Author John Ferguson (@BrockleyJohn) john@sewebsites.net
  
	copyright  (c) 2017 SEwebsites

 *
 */

	// Function to prevent boxes showing for the output-only test functions
  if( !function_exists( 'sew_cfg_do_nothing' ) ) {
    function sew_cfg_do_nothing() {
      return '';
    }
  }
	
  function sew_cfg_pull_down_top_categories($id, $key = '') {
    global $languages_id;
    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');
    $list_array = array(array('id' => '0', 'text' => TEXT_TOP));
    $list_query = tep_db_query("SELECT categories_id, categories_name FROM " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd WHERE c.parent_id = 0 AND cd.categories_id = c.categories_id AND cd.language_id = '" . (int)$languages_id . "' ORDER BY categories_name");
    while ($items = tep_db_fetch_array($list_query)) {
      $list_array[] = array('id' => $items['categories_id'],
                            'text' => $items['categories_name']);
    }
    return tep_draw_pull_down_menu($name, $list_array, $id);
	}

	function tep_cfg_pull_down_payment_term_list($id) {
    return tep_draw_pull_down_menu('configuration_value', sew_get_ez_lookup_values_with_id('PaymentTerms'), $id);
  }
  function tep_cfg_get_payment_term_name($id) {
  	return sew_cfg_get_ez_lookup_name($id,'PaymentTerms');
  }
  function tep_cfg_pull_down_order_type_list($id) {
    return tep_draw_pull_down_menu('configuration_value', sew_get_ez_lookup_values_with_id('OrderTypes'), $id);
  }
  function tep_cfg_get_order_type_name($id) {
  	return sew_cfg_get_ez_lookup_name($id,'OrderTypes');
  }
  function tep_cfg_pull_down_order_status_list($id) {
    return tep_draw_pull_down_menu('configuration_value', sew_get_ez_lookup_values_with_id('OrderStatuses'), $id);
  }
  function tep_cfg_get_order_status_name($id) {
  	return sew_cfg_get_ez_lookup_name($id,'OrderStatuses');
  }
  function tep_cfg_pull_down_customer_type_list($id) {
    return tep_draw_pull_down_menu('configuration_value', sew_get_ez_lookup_values_with_id('CustomerTypes'), $id);
  }
  function tep_cfg_get_customer_type_name($id) {
  	return sew_cfg_get_ez_lookup_name($id,'CustomerTypes');
  }
  function tep_cfg_pull_down_customer_relationship_list($id) {
    return tep_draw_pull_down_menu('configuration_value', sew_get_ez_lookup_values_with_id('Relationship'), $id);
  }
  function tep_cfg_get_customer_relationship_name($id) {
  	return sew_cfg_get_ez_lookup_name($id,'Relationship');
  }
  function sew_cfg_get_ez_lookup_name($id,$what) {
  	require_once (DIR_FS_ADMIN.'easify/easify_lookups.php');
		$Lookups = new EasifyLookups();
		if ($return = $Lookups->GetValueFromId($id,$what)) {
			return $return;
		} else {
			return $id;
		}
  }
  
  function sew_get_ez_lookup_values_with_id ($what) {
  	require_once (DIR_FS_ADMIN.'easify/easify_lookups.php');
		$return = array();
		$Lookups = new EasifyLookups();
		$LkValues = $Lookups->GetValueList($what);
		foreach ($LkValues as $LkValue) {
			$return[] = array('id' => $LkValue['easify_lookup_id'],
								      'text' => $LkValue['easify_lookup_value']);
		}
		return $return;
  }
	

