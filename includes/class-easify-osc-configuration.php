<?php
/**
-
  wrapper for configuration options for easify v4 interface

  Author John Ferguson (@BrockleyJohn) john@sewebsites.net
  
	copyright  (c) 2017 SEwebsites

 * installs the required config options into the osc database
 * caters for adding in extra ones in an update
 */

class easify_osc_configuration 
{
  public function __construct()
	{
	  $this->checkConfigs();
	}
	
	private function checkConfigs()
	{
	  $this->config_group = $this->getConfigGroup();
		// use main product setting as a check if installation is needed
		$done = false;
		if (!defined('EASIFY_PRODUCT_MODE')) {
		  $this->install();
		  $done = DEFAULT_SETTINGS_INSTALLED;
		} else {
		  $configs = array_keys($this->getConfigs());
			foreach ($configs as $config) {
			  if (! defined($config)) {
				  $this->install($config);
				  $done = EXTRA_DEFAULTS_INSTALLED;
				}
			}
		}
		if ($done) tep_redirect('configuration.php?gID=' . $this->config_group);
	}
	
	private function install($config)
	{ // install passed setting or all of them
    $configs = $this->getConfigs();
    $sort = 1;
		if (isset($config)) {
		  $cfg_query = tep_db_query('SELECT MAX(sort_order) AS last_sort FROM ' . TABLE_CONFIGURATION . ' WHERE configuration_group_id = ' . $this->config_group);
		  $cfg_row = tep_db_fetch_array($cfg_query);
			$sort = $cfg_row['last_sort'] + 1;
      if (isset($configs[$config])) {
        $configs = array($config => $params[$config]);
      } else {
        $configs = array();
      }
    }
    foreach ($configs as $key => $data) {
      $sql_data_array = array('configuration_title' => $data['title'],
                              'configuration_key' => $key,
                              'configuration_value' => (isset($data['value']) ? $data['value'] : ''),
                              'configuration_description' => $data['desc'],
                              'configuration_group_id' => $this->config_group,
                              'sort_order' => $sort,
                              'date_added' => 'now()');

      if (isset($data['set_func'])) {
        $sql_data_array['set_function'] = $data['set_func'];
      }
      if (isset($data['use_func'])) {
        $sql_data_array['use_function'] = $data['use_func'];
      }
      tep_db_perform(TABLE_CONFIGURATION, $sql_data_array);
			$sort++;
    }
	}
	
	private function getConfigGroup()
	{
		$cfg_grp_query = tep_db_query('SELECT * FROM ' . TABLE_CONFIGURATION_GROUP . ' WHERE configuration_group_title = "Easify Interface" OR configuration_group_title = "EPOS Interface - Easify"');
		if (tep_db_num_rows($cfg_grp_query)) {
		  $cfg_grp_row = tep_db_fetch_array($cfg_grp_query);
			$cfg_grp_id = $cfg_grp_row['configuration_group_id'];
		} else {
		  $cfg_grp_query = tep_db_query('SELECT MAX(sort_order) AS last_sort FROM ' . TABLE_CONFIGURATION_GROUP);
		  $cfg_grp_row = tep_db_fetch_array($cfg_grp_query);
			$sql_data_array = array('configuration_group_title' => 'EPOS Interface - Easify', 'sort_order' => $cfg_grp_row['last_sort'] + 1);
			tep_db_perform(TABLE_CONFIGURATION_GROUP,$sql_data_array);
			$cfg_grp_id = tep_db_insert_id();
		}
	  return $cfg_grp_id;
	}
	
	private function getConfigs() 
	{
/*  template
		  'CONFIG_PARAM' => array(
						 'title' => 'name to be used for it in admin',
						 'desc' => 'explanation to be used in admin',
						 'value' => 'True',
						 'use_func' => 'sew_use_me',
						 'set_func' => 'tep_cfg_select_option(array(\'True\', \'False\'), '),
*/	
    return array(
		  'EASIFY_SERVICE_USERNAME' => array(
						 'title' => 'Easify web service user name',
						 'desc' => 'The user name for the Easify v4 eCommerce connector subscription (not the user for logging in to Easify or their web site)',
						 'value' => '',
						 'use_func' => 'sew_cfg_easify_service_settings', 
						 'set_func' => 'sew_cfg_do_nothing('), 
		  'EASIFY_SERVICE_PASSWORD' => array(
						 'title' => 'Easify web service password',
						 'desc' => 'The password for the Easify v4 eCommerce connector subscription',
						 'value' => '',
						 'use_func' => 'sew_cfg_do_nothing', 
						 'set_func' => 'sew_cfg_do_nothing('), 
		  'EASIFY_SERVICE_LOCATION' => array(
						 'title' => 'Easify web service location',
						 'desc' => 'The location of the eCommerce web service (stored automatically)',
						 'value' => '',
						 'use_func' => 'sew_cfg_do_nothing', 
						 'set_func' => 'sew_cfg_do_nothing('), 
		  'EASIFY_COUPON_DISCOUNT_SKU' => array(
						 'title' => 'Coupon Discount on Easify',
						 'desc' => 'SKU for Easify product used for coupon discount.',
						 'value' => ''),
		  'EASIFY_GENERIC_PRODUCT_SKUS' => array(
						 'title' => 'Easify Generic Products',
						 'desc' => 'Easify products that may map to multiple online products (Used, Surplus etc). Online product name gets put in the comment field. Format is sku1|sku2|sku3',
						 'value' => ''),
		  'EASIFY_MANUFACTURER_MODE' => array(
						 'title' => 'Easify new manufacturer mode',
						 'desc' => 'When a new manufacturer comes from easify, what should happen in osc?',
						 'value' => 'create',
						 'set_func' => 'tep_cfg_select_option(array(\'create\', \'match\', \'nothing\'), '),
		  'EASIFY_CATEGORY_MODE' => array(
						 'title' => 'Easify new category mode',
						 'desc' => 'When a new category comes from easify, what should happen in osc?',
						 'value' => 'create',
						 'set_func' => 'tep_cfg_select_option(array(\'create\', \'match\', \'nothing\'), '),
		  'EASIFY_PRODUCT_CATEGORY' => array(
						 'title' => 'Easify top level category',
						 'desc' => 'Hang categories from easify below this category. If 0 they come out as top level categories',
						 'value' => '0'
						 'set_func' => 'sew_cfg_pull_down_top_categories('), // see tep_cfg_pull_down_order_statuses
		  'EASIFY_PRODUCT_MODE' => array(
						 'title' => 'Easify new product mode',
						 'desc' => 'When a new product comes from easify, what should happen in osc?',
						 'value' => 'create',
						 'set_func' => 'tep_cfg_select_option(array(\'create\', \'match\', \'nothing\'), '),
		  'EASIFY_MATCH_DEFAULT_MODE' => array(
						 'title' => 'Easify match/unmatch default mode',
						 'desc' => 'The mode that the Easify matcher opens in.',
						 'value' => 'catalog',
						 'set_func' => 'tep_cfg_select_option(array(\'catalog\', \'easify\'), '), 
		  'EASIFY_PRODUCT_UPDATE_RULES' => array(
						 'title' => 'Easify product update rules',
						 'desc' => 'Which online fields get updated (ie. overwritten) by easify updates',
						 'value' => '',
						 'use_func' => 'sew_cfg_ez_update_rules', //was tep_cfg_get_order_status_name
						 'set_func' => 'sew_cfg_do_nothing('), //was tep_cfg_pull_down_order_status_list
		  'EASIFY_PRICE_DIFFERENCE' => array(
						 'title' => 'Price difference confirmation threshold',
						 'desc' => 'Percentage figure. On the review page, when online prices are set lower by more than this threshold, ask for confirmation.',
						 'value' => '10'),
		  'EASIFY_QUEUE_LOG_LIMIT' => array(
						 'title' => 'Easify queue entry limit',
						 'desc' => 'The number of entries that will be left in the Easify Interface queue log when the tidy function is run. Entries over this limit are deleted.',
						 'value' => '1000'),
		  'EASIFY_ACTION_TIDY_MODE' => array(
						 'title' => 'Easify action log tidy mode',
						 'desc' => 'Set to auto to include action log tidy in the action processor runs, otherwise DIY',
						 'value' => 'auto',
						 'set_func' => 'tep_cfg_select_option(array(\'auto\', \'manual\'), '),
		  'EASIFY_ACTION_LOG_LIMIT' => array(
						 'title' => 'Easify action log entry limit',
						 'desc' => 'The total number of entries that will be left in the Easify Interface action log when the tidy function is run. This includes the number of \'no action\' logs which is specified separately. Entries over this limit are deleted.',
						 'value' => '200'),
		  'EASIFY_NO_ACTION_LIMIT' => array(
						 'title' => 'Easify action log \'no action\' entry limit',
						 'desc' => 'The number of ''no action'' entries that will be kept when the action log is tidied (so you can tell if it runs when you think it should). Entries over this limit are deleted.',
						 'value' => '10'),
		  'EASIFY_LOGGING_DIR' => array(
						 'title' => 'Easify logging directory',
						 'desc' => 'Output log files to this directory if enabled.',
						 'value' => DIR_FS_ADMIN . 'logs',
						 'use_func' => 'sew_cfg_check_output_dir', 
						 'set_func' => 'sew_cfg_choose_output_dir('),
		  'EASIFY_LOGGING_ENABLED' => array(
						 'title' => 'Easify logging enabled',
						 'desc' => 'Enable logging to easify_log.txt',
						 'value' => 'true',
						 'set_func' => 'tep_cfg_select_option(array(\'true\', \'false\'), '),
		  'EASIFY_DEBUG_ENABLED' => array(
						 'title' => 'Easify debug enabled',
						 'desc' => 'enable debug stack trace logging to easify_debug.txt',
						 'value' => 'true',
						 'set_func' => 'tep_cfg_select_option(array(\'true\', \'false\'), '),
		  'EASIFY_ORDER_STATUS_ID' => array(
						 'title' => 'Easify new order status',
						 'desc' => 'Initial order status in Easify for orders created online',
						 'value' => '11',
						 'use_func' => 'sew_cfg_get_ez_order_status_name', //was tep_cfg_get_order_status_name
						 'set_func' => 'sew_cfg_pull_down_ez_order_status_list('), //was tep_cfg_pull_down_order_status_list
		  'EASIFY_ORDER_TYPE_ID' => array(
						 'title' => 'Easify default order type',
						 'desc' => 'Default order type in Easify for orders created online',
						 'value' => '5',
						 'use_func' => 'sew_cfg_get_ez_order_type_name', //was tep_cfg_get_order_type_name
						 'set_func' => 'sew_cfg_pull_down_ez_order_type_list('), //was tep_cfg_pull_down_order_type_list
		  'EASIFY_PAYMENT_TERMS_ID' => array(
						 'title' => 'Easify default order payment terms',
						 'desc' => 'Default payment terms in Easify for orders created online',
						 'value' => '1',
						 'use_func' => 'sew_cfg_get_ez_payment_term_name', //was tep_cfg_get_payment_term_name
						 'set_func' => 'sew_cfg_pull_down_ez_payment_term_list('), //was tep_cfg_pull_down_payment_term_list
		  'EASIFY_CUSTOMER_TYPE_ID' => array(
						 'title' => 'Easify default customer type',
						 'desc' => 'Default customer type in Easify for customers created online',
						 'value' => '1',
						 'use_func' => 'sew_cfg_get_ez_customer_type_name', //was tep_cfg_get_customer_type_name
						 'set_func' => 'sew_cfg_pull_down_ez_customer_type_list('), //was tep_cfg_pull_down_customer_type_list
		  'EASIFY_CUSTOMER_RELATIONSHIP_ID' => array(
						 'title' => 'Easify default customer relationship',
						 'desc' => 'Default customer relationship in Easify for customers created online',
						 'value' => '3',
						 'use_func' => 'sew_cfg_get_ez_customer_type_name', //was tep_cfg_get_customer_type_name
						 'set_func' => 'sew_cfg_pull_down_ez_customer_type_list('), //was tep_cfg_pull_down_customer_type_list
		  'EASIFY_TIMEOUT' => array(
						 'title' => 'Easify timeout',
						 'desc' => 'Easify timeout',
						 'value' => '600'),
		  'EASIFY_TIMEOUT_SHORT' => array(
						 'title' => 'Easify timeout short',
						 'desc' => 'Easify timeout short',
						 'value' => '25'),
		  'EASIFY_DISCOVERY_SERVER_ENDPOINT_URI' => array(
						 'title' => 'Easify discovery server endpoint',
						 'desc' => 'Easify discovery server endpoint',
						 'value' => 'https://www.easify.co.uk/api/Security/GetEasifyServerEndpoint'),
		  'EASIFY_CLOUD_API_URI' => array(
						 'title' => 'Easify cloud api',
						 'desc' => 'Easify cloud api',
						 'value' => 'https://cloudapi.easify.co.uk/api/EasifyCloudApi'),
		  'EASIFY_HELP_BASE_URL' => array(
						 'title' => 'Easify help',
						 'desc' => 'Base url of Easify help',
						 'value' => 'https://www.easify.co.uk'),
		);
	}
}