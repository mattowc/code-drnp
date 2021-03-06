<?php
/**
 * Setup
 *
 * Flow controller for settings management
 *
 * @author Jonathan Davis
 * @version 1.0
 * @copyright Ingenesis Limited, January 6, 2010
 * @package shopp
 * @subpackage shopp
 **/

/**
 * Setup
 *
 * @package shopp
 * @author Jonathan Davis
 **/
class Setup extends AdminController {

	var $screen = false;
	var $url;

	/**
	 * Setup constructor
	 *
	 * @return void
	 * @author Jonathan Davis
	 **/
	function __construct () {
		parent::__construct();

		$pages = explode('-',$_GET['page']);
		$this->screen = end($pages);
		switch ($this->screen) {
			case 'preferences':
				shopp_enqueue_script('jquery-tmpl');
				shopp_enqueue_script('labelset');
				shopp_localize_script( 'labelset', '$sl', array(
					'prompt' => __('Are you sure you want to remove this order status label?','Shopp'),
				));
				break;
			case 'taxes':
				shopp_enqueue_script('ocupload');
				shopp_enqueue_script('jquery-tmpl');
				shopp_enqueue_script('taxrates');
				shopp_enqueue_script('suggest');
				shopp_localize_script( 'taxrates', '$tr', array(
					'confirm' => __('Are you sure you want to remove this tax rate?','Shopp'),
				));

				$this->subscreens = array(
					'rates' => __('Rates','Shopp'),
					'settings' => __('Settings','Shopp')
				);

				if (isset($_GET['sub'])) $this->url = add_query_arg(array('sub'=>esc_attr($_GET['sub'])),$this->url);
				else $_GET['sub'] = shopp_setting_enabled('taxes')?'rates':'settings';

				if (shopp_setting_enabled('taxes'))
					$this->taxrate_ui();

				break;
			case 'system':
				shopp_enqueue_script('jquery-tmpl');
				shopp_enqueue_script('colorbox');
				break;
			case 'pages':
				shopp_enqueue_script('jquery-tmpl');
				shopp_enqueue_script('pageset');
				$this->pages_ui();
				break;
			case 'images':
				shopp_enqueue_script('jquery-tmpl');
				shopp_enqueue_script('imageset');
				shopp_localize_script( 'imageset', '$is', array(
					'confirm' => __('Are you sure you want to remove this image preset?','Shopp'),
				));
				$this->images_ui();
				break;
			case 'payments':
				shopp_enqueue_script('jquery-tmpl');
				shopp_enqueue_script('payments');
				shopp_localize_script( 'payments', '$ps', array(
					'confirm' => __('Are you sure you want to remove this payment system?','Shopp'),
				));

				$this->payments_ui();
				break;
			case 'shipping':
				shopp_enqueue_script('jquery-tmpl');
				shopp_enqueue_script('shiprates');
				shopp_localize_script( 'shiprates', '$ps', array(
					'confirm' => __('Are you sure you want to remove this shipping rate?','Shopp'),
				));

				$this->subscreens = array(
					'rates' => __('Rates','Shopp'),
					'settings' => __('Settings','Shopp')
				);

				if (isset($_GET['sub'])) $this->url = add_query_arg(array('sub'=>esc_attr($_GET['sub'])),$this->url);
				else $_GET['sub'] = shopp_setting_enabled('taxes')?'rates':'settings';

				if (shopp_setting_enabled('shipping'))
					$this->shipping_ui();

				break;
			case 'settings':
				shopp_enqueue_script('setup');

				$customer_service = ' '.sprintf(__('Contact %s customer service %s.','Shopp'),'<a href="'.SHOPP_CUSTOMERS.'" target="_blank">','</a>');

				$this->keystatus = array(
					'ks_inactive' => sprintf(__('Activate your Shopp access key for automatic updates and official support services. If you don\'t have a Shopp key, feel free to support the project by %s purchasing a key from the Shopp Store %s.','Shopp'),'<a href="'.SHOPP_HOME.'store/'.'">','</a>'), // No key is activated yet
					'k_000' => __('The server could not be reached because of a connection problem.','Shopp'), 		// Cannot communicate with the server, config?, firewall?
					'k_001' => __('The server is experiencing problems.','Shopp').$customer_service,			// The server did not provide a valid response? Uncovered maintenance?
					'ks_1' => __('An unkown error occurred.','Shopp'),											// Absolutely no clue what happened
					'ks_2' => __('The activation server is currently down for maintenance.','Shopp'),			// The server is giving a maintenance code
					'ks0' => __('This site has been deactivated.','Shopp'),										// Successful deactivation
					'ks1' => __('This site has been activated.','Shopp'),										// Successful activation
					'ks_100' => __('An unknown activation error occurred.','Shopp').$customer_service,			// Unknown activation problem
					'ks_101' => __('The key provided is not valid.','Shopp').$customer_service,
					'ks_102' => __('This site is not valid to activate the key.','Shopp').$customer_service,
					'ks_103' => __('The key provided could not be validated by shopplugin.net.','Shopp').$customer_service,
					'ks_104' => __('The key provided is already active on another site.','Shopp').$customer_service,
					'ks_200' => __('An unkown deactivation error occurred.','Shopp').$customer_service,
					'ks_201' => __('The key provided is not valid.','Shopp').$customer_service,
					'ks_202' => __('The site is not valid to be able to deactivate the key.','Shopp').$customer_service,
					'ks_203' => __('The key provided could not be validated by shopplugin.net.','Shopp').$customer_service
				);

				$l10n = array(
					'activate_button' => __('Activate Key','Shopp'),
					'deactivate_button' => __('De-activate Key','Shopp'),
					'connecting' => __('Connecting','Shopp')

				);
				$l10n = array_merge($l10n,$this->keystatus);
				shopp_localize_script( 'setup', '$sl', $l10n);
				break;
		}

	}

	/**
	 * Parses settings interface requests
	 *
	 * @return void
	 * @author Jonathan Davis
	 **/
	function admin () {
		switch($this->screen) {
			case 'catalog': 		$this->catalog(); break;
			case 'cart': 			$this->cart(); break;
			case 'payments': 		$this->payments(); break;
			case 'shipping': 		$this->shipping(); break;
			case 'taxes': 			$this->taxes(); break;
			case 'pages':			$this->pages(); break;
			case 'images':			$this->images(); break;
			case 'presentation':	$this->presentation(); break;
			case 'preferences': 	$this->preferences(); break;
			case 'system':			$this->system(); break;
			case 'update':			$this->update(); break;
			default: 				$this->setup();
		}
	}

	/**
	 * Displays the General Settings screen and processes updates
	 *
	 * @author Jonathan Davis
	 * @since 1.0
	 *
	 * @return void
	 **/
	function setup () {
		if ( !(current_user_can('manage_options') && current_user_can('shopp_settings')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		// Welcome screen handling
		if (!empty($_POST['setup'])) {
			$_POST['settings']['display_welcome'] = 'off';
			$this->settings_save();
		}

		$country = (isset($_POST['settings']) && isset($_POST['settings']['base_operations']))?$_POST['settings']['base_operations']['country']:'';
		$countries = array();
		$countrydata = Lookup::countries();
		$country_zones = Lookup::country_zones();
		foreach ($countrydata as $iso => $c) {
			if ($country == $iso) $base_region = $c['region'];
			$countries[$iso] = $c['name'];
		}

		// Key activation
		if (!empty($_POST['activation'])) {
			check_admin_referer('shopp-settings-activation');
			$sitekey = Shopp::keysetting();
			$key = $_POST['updatekey'];
			if ($key == str_repeat('0',40)) $key = $sitekey['k'];
			Shopp::key($_POST['activation'],$key);
		}

		$sitekey = Shopp::keysetting();
		$activated = Shopp::activated();
		$key = $sitekey['k'];
		$status = $sitekey['s'];

		$type = 'text';
		$action = 'activate';
		$button = __('Activate Key','Shopp');

		if ($activated) {
			$button = __('De-activate Key','Shopp');
			$action = 'deactivate';
			$type = 'password';
			$key = str_repeat('0',strlen($key));
			$keystatus = $this->keystatus['ks1'];
		} else {
			if (str_repeat('0',40) == $key) $key = '';
		}

		$status_class = ($status < 0)?'activating':'';
		$keystatus = '';
		if (empty($key)) $keystatus = $this->keystatus['ks_inactive'];
		if (!empty($_POST['activation'])) $keystatus = $this->keystatus['ks'.str_replace('-','_',$status)];

		// Save settings
		if (!empty($_POST['save']) && isset($_POST['settings'])) {
			check_admin_referer('shopp-settings-general');

			if (isset($_POST['settings']['base_operations'])) {
				$baseop = &$_POST['settings']['base_operations'];

				$zone = isset($baseop['zone']) && isset($country_zones[ $country ]) && isset($country_zones[ $country ][ $baseop['zone'] ]) ? $baseop['zone']:false;
				if (isset($countrydata[$country])) $baseop = $countrydata[$country];
				$baseop['country'] = $country;
				$baseop['zone'] = $zone;
				$baseop['currency']['format'] = scan_money_format($baseop['currency']['format']);

				shopp_set_setting('tax_inclusive', // Automatically set the inclusive tax setting
					(in_array($country,Lookup::tax_inclusive_countries()) ? 'on' : 'off')
				);
			}

			if (!isset($_POST['settings']['target_markets']))
				asort($_POST['settings']['target_markets']);

			$this->settings_save();
			$updated = __('Shopp settings saved.', 'Shopp');
		}

		$operations = shopp_setting('base_operations');
		if (isset($country_zones[ $operations['country'] ]))
			$zones = $country_zones[ $operations['country'] ];

		$targets = shopp_setting('target_markets');
		if (is_array($targets))	$targets = array_map('stripslashes',$targets);
		if (!$targets) $targets = array();

		include(SHOPP_ADMIN_PATH.'/settings/setup.php');
	}

	function presentation () {
		if ( !(current_user_can('manage_options') && current_user_can('shopp_settings_presentation')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		$builtin_path = SHOPP_PATH.'/templates';
		$theme_path = sanitize_path(STYLESHEETPATH.'/shopp');

		if (!empty($_POST['save'])) {
			check_admin_referer('shopp-settings-presentation');
			$updated = __('Shopp presentation settings saved.','Shopp');

			if (isset($_POST['settings']['theme_templates'])
				&& $_POST['settings']['theme_templates'] == 'on'
				&& !is_dir($theme_path)) {
					$_POST['settings']['theme_templates'] = 'off';
					$updated = __('Shopp theme templates can\'t be used because they don\'t exist.','Shopp');
			}

			if (empty($_POST['settings']['catalog_pagination']))
				$_POST['settings']['catalog_pagination'] = 0;
			$this->settings_save();
		}


		// Copy templates to the current WordPress theme
		if (!empty($_POST['install'])) {
			check_admin_referer('shopp-settings-presentation');
			copy_shopp_templates($builtin_path,$theme_path);
		}

		$status = 'available';
		if (!is_dir($theme_path)) $status = 'directory';
		else {
			if (!is_writable($theme_path)) $status = 'permissions';
			else {
				$builtin = array_filter(scandir($builtin_path),'filter_dotfiles');
				$theme = array_filter(scandir($theme_path),'filter_dotfiles');
				if (empty($theme)) $status = 'ready';
				else if (array_diff($builtin,$theme)) $status = 'incomplete';
			}
		}

		$category_views = array('grid' => __('Grid','Shopp'),'list' => __('List','Shopp'));
		$row_products = array(2,3,4,5,6,7);
		$productOrderOptions = ProductCategory::sortoptions();
		$productOrderOptions['custom'] = __('Custom','Shopp');

		$orderOptions = array('ASC' => __('Order','Shopp'),
							  'DESC' => __('Reverse Order','Shopp'),
							  'RAND' => __('Shuffle','Shopp'));

		$orderBy = array('sortorder' => __('Custom arrangement','Shopp'),
						 'created' => __('Upload date','Shopp'));


		include(SHOPP_ADMIN_PATH.'/settings/presentation.php');
	}

	function preferences () {
		global $Shopp;

		$db =& DB::get();
		if ( !(current_user_can('manage_options') && current_user_can('shopp_settings_checkout')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		$purchasetable = DatabaseObject::tablename(Purchase::$table);
		$next = $db->query("SELECT IF ((MAX(id)) > 0,(MAX(id)+1),1) AS id FROM $purchasetable LIMIT 1");
		$next_setting = shopp_setting('next_order_id');

		if ($next->id > $next_setting) $next_setting = $next->id;

		if (!empty($_POST['save'])) {
			check_admin_referer('shopp-settings-preferences');

			$next_order_id = $_POST['settings']['next_order_id'] = intval($_POST['settings']['next_order_id']);

			if ($next_order_id >= $next->id) {
				if ($db->query("ALTER TABLE $purchasetable AUTO_INCREMENT=".$db->escape($next_order_id)))
					$next_setting = $next_order_id;
			}


			$this->settings_save();
			$updated = __('Shopp checkout settings saved.','Shopp');
		}

		$downloads = array('1','2','3','5','10','15','25','100');
		$promolimit = array('1','2','3','4','5','6','7','8','9','10','15','20','25');
		$time = array(
			'1800' => __('30 minutes','Shopp'),
			'3600' => __('1 hour','Shopp'),
			'7200' => __('2 hours','Shopp'),
			'10800' => __('3 hours','Shopp'),
			'21600' => __('6 hours','Shopp'),
			'43200' => __('12 hours','Shopp'),
			'86400' => __('1 day','Shopp'),
			'172800' => __('2 days','Shopp'),
			'259200' => __('3 days','Shopp'),
			'604800' => __('1 week','Shopp'),
			'2678400' => __('1 month','Shopp'),
			'7952400' => __('3 months','Shopp'),
			'15901200' => __('6 months','Shopp'),
			'31536000' => __('1 year','Shopp'),
			);

		$states = array(
			__('Map the label to an order state:','Shopp') => array_merge(array('' => ''),Lookup::txnstatus_labels())
		);
		$statusLabels = shopp_setting('order_status');
		$statesLabels = shopp_setting('order_states');
		$reasonLabels = shopp_setting('cancel_reasons');

		if (empty($reasonLabels)) $reasonLabels = array(
			__('Not as described or expected','Shopp'),
			__('Wrong size','Shopp'),
			__('Found better prices elsewhere','Shopp'),
			__('Product is missing parts','Shopp'),
			__('Product is defective or damaaged','Shopp'),
			__('Took too long to deliver','Shopp'),
			__('Item out of stock','Shopp'),
			__('Customer request to cancel','Shopp'),
			__('Item discontinued','Shopp'),
			__('Other reason','Shopp')
		);

		include(SHOPP_ADMIN_PATH.'/settings/preferences.php');
	}

	/**
	 * Renders the shipping settings screen and processes updates
	 *
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @return void
	 **/
	function shipping () {

		if ( !(current_user_can('manage_options') && current_user_can('shopp_settings_shipping')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));


		global $Shopp;

		$sub = 'settings';
		if (shopp_setting_enabled('shipping')) $sub = 'rates';
		if ( isset($_GET['sub']) && in_array( $_GET['sub'],array_keys($this->subscreens) ) )
			$sub = $_GET['sub'];

		if (!empty($_POST['save']) && empty($_POST['module']) ) {
			check_admin_referer('shopp-settings-shipping');

			$_POST['settings']['order_shipfee'] = floatvalue($_POST['settings']['order_shipfee']);

	 		$this->settings_save();
			$updated = __('Shipping settings saved.','Shopp');
		}

		// Handle ship rates UI
		if ('rates' == $sub && 'on' == shopp_setting('shipping')) return $this->shiprates();

		$base = shopp_setting('base_operations');
		$regions = Lookup::regions();
		$region = $regions[$base['region']];
		$useRegions = shopp_setting('shipping_regions');

		$areas = Lookup::country_areas();
		if (is_array($areas[$base['country']]) && $useRegions == 'on')
			$areas = array_keys($areas[$base['country']]);
		else $areas = array($base['country'] => $base['name']);
		unset($countries,$regions);

		$carrierdata = Lookup::shipcarriers();
		$serviceareas = array('*',$base['country']);
		foreach ($carrierdata as $c => $record) {
			if (!in_array($record->areas,$serviceareas)) continue;
			$carriers[$c] = $record->name;
		}
		unset($carrierdata);
		$shipping_carriers = shopp_setting('shipping_carriers');
		if (empty($shipping_carriers)) $shipping_carriers = array_keys($carriers);

		$rates = shopp_setting('shipping_rates');
		if (!empty($rates)) ksort($rates);

		$lowstock = shopp_setting('lowstock_level');
		if (empty($lowstock)) $lowstock = 0;

		include(SHOPP_ADMIN_PATH.'/settings/shipping.php');
	}

	function shiprates () {
		global $Shopp;
		$Shipping = $Shopp->Shipping;
		$Shipping->settings(); // Load all installed shipping modules for settings UIs

		$methods = $Shopp->Shipping->methods;

		$active = shopp_setting('active_shipping');
		if (!$active) $active = array();

		if (!empty($_GET['delete'])) {
			check_admin_referer('shopp_delete_shiprate');
			$delete = $_GET['delete'];
			$index = false;
			if (strpos($delete,'-') !== false)
				list($delete,$index) = explode('-',$delete);

			if (array_key_exists($delete,$active))  {
				if (is_array($active[$delete])) {
					if (array_key_exists($index,$active[$delete])) {
						unset($active[$delete][$index]);
						if (empty($active[$delete])) unset($active[$delete]);
					}
				} else unset($active[$delete]);
				$updated = __('Shipping method setting removed.','Shopp');

				shopp_set_setting('active_shipping',$active);
			}
		}

		if (isset($_POST['module'])) {
			check_admin_referer('shopp-settings-shiprate');

			$setting = false;
			$module = isset($_POST['module'])?$_POST['module']:false;
			$id = isset($_POST['id'])?$_POST['id']:false;

			if ($id == $module) {
				if (isset($_POST['settings'])) $this->settings_save();
				/** Save shipping service settings **/
				$active[$module] = true;
				shopp_set_setting('active_shipping',$active);
				$updated = __('Shipping settings saved.','Shopp');
				// Cancel editing if saving
				if (isset($_POST['save'])) unset($_REQUEST['id']);

				$Errors = &ShoppErrors();
				do_action('shopp_verify_shipping_services');

				if ($Errors->exist()) {
					// Get all addon related errors
					$failures = $Errors->level(SHOPP_ADDON_ERR);
					if (!empty($failures)) {
						$updated = __('Shipping settings saved but there were errors: ','Shopp');
						foreach ($failures as $error)
							$updated .= '<p>'.$error->message(true,true).'</p>';
					}
				}

			} else {
				/** Save shipping calculator settings **/

				$setting = $_POST['id'];
				if (empty($setting)) { // Determine next available setting ID
					$index = 0;
					if (is_array($active[$module])) $index = count($active[$module]);
					$setting = "$module-$index";
				}

				// Cancel editing if saving
				if (isset($_POST['save'])) unset($_REQUEST['id']);

				$setting_module = $setting; $id = 0;
				if (false !== strpos($setting,'-'))
					list($setting_module,$id) = explode('-',$setting);

				// Prevent fishy stuff from happening
				if ($module != $setting_module) $module = false;

				// Save shipping calculator settings
				$Shipper = $Shipping->get($module);
				if ($Shipper && isset($_POST[$module])) {
					$Shipper->setting($id);

					$_POST[$module]['label'] = stripslashes($_POST[$module]['label']);

					// Sterilize $values
					foreach ($_POST[$module]['table'] as $i => &$row) {

						if (isset($row['rate'])) $row['rate'] = floatvalue($row['rate']);
						if (!isset($row['tiers'])) continue;

						foreach ($row['tiers'] as &$tier) {
							if (isset($tier['rate'])) $tier['rate'] = floatvalue($tier['rate']);
						}

					}

					shopp_set_setting($Shipper->setting,$_POST[$module]);
					if (!array_key_exists($module,$active)) $active[$module] = array();
					$active[$module][(int)$id] = true;
					shopp_set_setting('active_shipping',$active);
					$updated = __('Shipping settings saved.','Shopp');
				}

			}
		}

		$Shipping->ui(); // Setup setting UIs
		$installed = array();
		$shiprates = array();	// Registry for activated shipping rate modules
		$settings = array();	// Registry of loaded settings for table-based shipping rates for JS

		foreach ($Shipping->active as $name => $module) {
			if (version_compare($Shipping->modules[$name]->since,'1.2') == -1) continue; // Skip 1.1 modules, they are incompatible

			$default_name = strtolower($name);
			$fullname = $module->methods();
			$installed[$name] = $fullname;

			if ($module->ui->tables) {
				$defaults[$default_name] = $module->ui->settings();
				$defaults[$default_name]['name'] = $fullname;
				$defaults[$default_name]['label'] = __('Shipping Method','Shopp');
			}

			if (array_key_exists($name,$active)) $ModuleSetting = $active[$name];
			else continue; // Not an activated shipping module, go to the next one

			// Setup shipping service shipping rate entries and settings
			if (!is_array($ModuleSetting)) {
				$shiprates[$name] = $name;
				continue;
			}

			// Setup shipping calcualtor shipping rate entries and settings
			foreach ($ModuleSetting as $id => $m) {
				$setting = "$name-$id";
				$shiprates[$setting] = $name;

				$settings[$setting] = shopp_setting($setting);
				$settings[$setting]['id'] = $setting;
				$settings[$setting] = array_merge($defaults[$default_name],$settings[$setting]);
			}

		}

		if ( isset($_REQUEST['id']) ) {
			$edit = $_REQUEST['id'];
			$id = false;
			if (strpos($edit,'-') !== false)
				list($module,$id) = explode('-',$edit);
			else $module = $edit;
			if (isset($Shipping->active[ $module ]) ) {
				$Shipper = $Shipping->get($module);
				if (!$Shipper->singular) {
					$Shipper->setting($id);
					$Shipper->initui($Shipping->modules[$module]->name); // Re-init setting UI with loaded settings
				}
				$editor = $Shipper->ui();
			}

		}

		asort($installed);

		$countrydata = Lookup::countries();
		$countries = $regionmap = $postcodes = array();
		$postcodedata = Lookup::postcodes();
		foreach ($countrydata as $code => $country) {
			$countries[$code] = $country['name'];
			if ( !isset($regionmap[ $country['region'] ]) ) $regionmap[ $country['region'] ] = array();
			$regionmap[ $country['region'] ][] = $code;
			if ( isset($postcodedata[$code])) {
				if ( !isset($postcodes[ $code ]) ) $postcodes[ $code ] = array();
				$postcodes[$code] = true;
			}
		}
		unset($countrydata);
		unset($postcodedata);


		$lookup = array(
			'regions' => array_merge(array('*' => __('Anywhere','Shopp')),Lookup::regions()),
			'regionmap' => $regionmap,
			'countries' => $countries,
			'areas' => Lookup::country_areas(),
			'zones' => Lookup::country_zones(),
			'postcodes' => $postcodes
		);

		$ShippingTemplates = new TemplateShippingUI();
		add_action('shopp_shipping_module_settings',array($Shipping,'templates'));
		include(SHOPP_ADMIN_PATH.'/settings/shiprates.php');

	}

	function shipping_menu () {
		if (!shopp_setting_enabled('shipping')) return;
		?>
		<ul class="subsubsub">
			<?php $i = 0; foreach ($this->subscreens as $screen => $label):  $url = add_query_arg(array('sub'=>$screen),$this->url); ?>
				<li><a href="<?php echo esc_url($url); ?>"<?php if ($_GET['sub'] == $screen) echo ' class="current"'; ?>><?php echo $label; ?></a><?php if (count($this->subscreens)-1!=$i++): ?> | <?php endif; ?></li>
			<?php endforeach; ?>
		</ul>
		<br class="clear" />
		<?php
	}

	function shipping_ui () {
		register_column_headers('shopp_page_shopp-settings-shipping', array(
			'name'=>__('Name','Shopp'),
			'type'=>__('Type','Shopp'),
			'destinations'=>__('Destinations','Shopp')
		));
	}

	function taxes () {
		if ( !(current_user_can('manage_options') && current_user_can('shopp_settings_taxes')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		$sub = 'settings';
		if (shopp_setting_enabled('taxes')) $sub = 'rates';
		if ( isset($_GET['sub']) && in_array( $_GET['sub'],array_keys($this->subscreens) ) )
			$sub = $_GET['sub'];

		if (!empty($_POST['save'])) {
			check_admin_referer('shopp-settings-taxes');
			$this->settings_save();
			$updated = __('Tax settings saved.','Shopp');
		}

		// Handle ship rates UI
		if ('rates' == $sub && 'on' == shopp_setting('taxes')) return $this->taxrates();

		include(SHOPP_ADMIN_PATH.'/settings/taxes.php');
	}

	function taxes_menu () {
		if (!shopp_setting_enabled('taxes')) return;
		?>
		<ul class="subsubsub">
			<?php $i = 0; foreach ($this->subscreens as $screen => $label):  $url = add_query_arg(array('sub'=>$screen),$this->url); ?>
				<li><a href="<?php echo esc_url($url); ?>"<?php if ($_GET['sub'] == $screen) echo ' class="current"'; ?>><?php echo $label; ?></a><?php if (count($this->subscreens)-1!=$i++): ?> | <?php endif; ?></li>
			<?php endforeach; ?>
		</ul>
		<br class="clear" />
		<?php
	}

	function taxrate_ui () {
		register_column_headers('shopp_page_shopp-settings-taxrates', array(
			'rate'=>__('Rate','Shopp'),
			'local'=>__('Local Rates','Shopp'),
			'conditional'=>__('Conditional','Shopp')
		));
	}

	function taxrates () {
		if ( !(current_user_can('manage_options') && current_user_can('shopp_settings_taxes')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		$edit = false;
		if (isset($_REQUEST['id'])) $edit = (int)$_REQUEST['id'];
		$localerror = false;

		$rates = shopp_setting('taxrates');
		if (!is_array($rates)) $rates = array();

		if (isset($_GET['delete'])) {
			check_admin_referer('shopp_delete_taxrate');
			$delete = (int)$_GET['delete'];
			if (isset($rates[$delete]))
				array_splice($rates,$delete,1);
			shopp_set_setting('taxrates',$rates);
		}

		if (isset($_POST['editing'])) $rates[$edit] = $_POST['settings']['taxrates'][ $edit ];
		if (isset($_POST['addrule'])) $rates[$edit]['rules'][] = array('p'=>'','v'=>'');
		if (isset($_POST['deleterule'])) {
			check_admin_referer('shopp-settings-taxrates');
			list($rateid,$row) = explode(',',$_POST['deleterule']);
			if (isset($rates[$rateid]) && isset($rates[$rateid]['rules'])) {
				array_splice($rates[$rateid]['rules'],$row,1);
				shopp_set_setting('taxrates',$rates);
			}
		}

		if (isset($rates[$edit]['haslocals']))
			$rates[$edit]['haslocals'] = ($rates[$edit]['haslocals'] == 'true' || $rates[$edit]['haslocals'] == '1');
		if (isset($_POST['add-locals'])) $rates[$edit]['haslocals'] = true;
		if (isset($_POST['remove-locals'])) {
			$rates[$edit]['haslocals'] = false;
			$rates[$edit]['locals'] = array();
		}

		$upload = $this->taxrate_upload();
		if ($upload !== false) {
			if (isset($upload['error'])) $localerror = $upload['error'];
			else $rates[$edit]['locals'] = $upload;
		}

		if (isset($_POST['editing'])) {
			// Resort taxes from generic to most specific
			usort($rates,array($this,'taxrates_sorting'));
			shopp_set_setting('taxrates',$rates);
		}
		if (isset($_POST['addrate'])) $edit = count($rates);
		if (isset($_POST['submit'])) $edit = false;

		$base = shopp_setting('base_operations');
		$countries = array_merge(array('*' => __('All Markets','Shopp')),(array)shopp_setting('target_markets'));
		$zones = Lookup::country_zones();

		include(SHOPP_ADMIN_PATH.'/settings/taxrates.php');
	}

	/**
	 * Helper to sort tax rates from most specific to most generic
	 *
	 * (more specific) <------------------------------------> (more generic)
	 * more/less conditions, local taxes, country/zone, country, All Markets
	 *
	 * @author Jonathan Davis
	 * @since 1.2
	 *
	 * @param array $rates The tax rate settings to sort
	 * @return void
	 **/
	function taxrates_sorting ($a, $b) {

		$args = array('a' => $a, 'b' => $b);
		$scoring = array('a' => 0 ,'b' => 0);

		foreach ($args as $key => $rate) {
			$score = &$scoring[$key];

			// More conditional rules are more specific
			$score += count($rate['rules']);

			// If there are local rates add to specificity
			if (isset($rate['haslocals']) && $rate['haslocals']) $score++;

			if (isset($rate['zone']) && $rate['zone']) $score++;

			if ('*' != $rate['country']) $score++;
		}

		if ($scoring['a'] < $scoring['b']) return 1;
		else return -1;

	}

	function taxrate_upload () {
		if (!isset($_FILES['ratefile'])) return false;

		$upload = $_FILES['ratefile'];
		$filename = $upload['tmp_name'];
		if (empty($filename) && empty($upload['name']) && !isset($_POST['upload'])) return false;

		$error = false;

		if ($upload['error'] != 0) return array('error' => Lookup::errors('uploads',$upload['error']));
		if (!is_readable($filename)) return array('error' => Lookup::errors('uploadsecurity','is_readable'));
		if (empty($upload['size'])) return array('error' => Lookup::errors('uploadsecurity','is_empty'));
		if ($upload['size'] != filesize($filename)) return array('error' => Lookup::errors('uploadsecurity','filesize_mismatch'));
		if (!is_uploaded_file($filename)) return array('error' => Lookup::errors('uploadsecurity','is_uploaded_file'));

		$data = file_get_contents($upload['tmp_name']);

		$formats = array(0=>false,3=>'xml',4=>'tab',5=>'csv');
		preg_match('/((<[^>]+>.+?<\/[^>]+>)|(.+?\t.+?\n)|(.+?,.+?\n))/',$data,$_);
		$format = $formats[count($_)];
		if (!$format) return array('error' => __('The uploaded file is not properly formatted as an XML, CSV or tab-delimmited file.','Shopp'));

		$_ = array();
		switch ($format) {
			case 'xml':
				/*
				Example XML import file:
					<localtaxrates>
						<taxrate name="Kent">1</taxrate>
						<taxrate name="New Castle">0.25</taxrate>
						<taxrate name="Sussex">1.4</taxrate>
					</localtaxrates>

				Taxrate record format:
					<taxrate name="(Name of locality)">(Percentage of the supplemental tax)</taxrate>

				Tax rate percentages should be represented as percentage numbers, not decimal percentages:
					1.25	= 1.25%	(0.0125)
					10		= 10%	(0.1)
				*/
				if (!class_exists('xmlQuery'))
					require(SHOPP_MODEL_PATH.'/XML.php');
				$XML = new xmlQuery($data);
				$taxrates = $XML->tag('taxrate');
				while($rate = $taxrates->each()) {
					$name = $rate->attr(false,'name');
					$value = $rate->content();
					$_[$name] = $value;
				}
				break;
			case 'csv':
				if (($csv = fopen($upload['tmp_name'], 'r')) === false)
					return array('error' => Lookup::errors('uploadsecurity','is_readable'));
				while ( $data = fgetcsv($csv, 1000, ',') !== false )
					$_[$data[0]] = !empty($data[1])?$data[1]:0;
				fclose($csv);
				break;
			case 'tab':
			default:
				$lines = explode("\n",$data);
				foreach ($lines as $line) {
					list($key,$value) = explode("\t",$line);
					$_[$key] = $value;
				}
		}

		if (empty($_)) array('error' => __('No useable tax rates could be found. The uploaded file may not be properly formatted.','Shopp'));

		return apply_filters('shopp_local_taxrates_upload',$_);
	}

	function payments () {
		if ( !(current_user_can('manage_options') && current_user_can('shopp_settings_payments')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		global $Shopp;
		$Gateways = $Shopp->Gateways;

	 	$active_gateways = shopp_setting('active_gateways');
		if (!$active_gateways) $gateways = array();
		else $gateways = explode(',',$active_gateways);

		$Gateways->settings();	// Load all installed gateways for settings UIs
		do_action('shopp_setup_payments_init');

		if (!empty($_GET['delete'])) {
			$delete = $_GET['delete'];
			check_admin_referer('shopp_delete_gateway');
			if (in_array($delete,$gateways))  {
				$position = array_search($delete,$gateways);
				array_splice($gateways,$position,1);
				shopp_set_setting('active_gateways',join(',',$gateways));
			}
		}

		if (!empty($_POST['save'])) {
			check_admin_referer('shopp-settings-payments');
			do_action('shopp_save_payment_settings');

			if (isset($_POST['gateway'])) {
				$gateway = $_POST['gateway'];

				// Handle Multi-instance payment systems
				$indexed = false;
				if (preg_match('/\[(\d+)\]/',$gateway,$matched)) {
					$indexed = '-'.$matched[1];
					$gateway = str_replace($matched[0],'',$gateway);
					if (isset($Gateways->active[ $gateway ])) {
						$Gateway = $Gateways->active[ $gateway ];
						$_POST['settings'][$gateway] = $_POST['settings'][$gateway]+$Gateway->settings;
					}
				}

				if ( !empty($gateway) && isset($Gateways->active[ $gateway ]) ) {
					if ( !in_array($gateway.$indexed,$gateways) ) {
						$gateways[] =  $gateway.$indexed;
						shopp_set_setting('active_gateways',join(',',$gateways));
					}
				}
			}

			$this->settings_save();
			$Gateways->settings();	// Load all installed gateways for settings UIs
			$updated = __('Shopp payments settings saved.','Shopp');
		}

		$installed = array();
		foreach($Gateways->modules as $slug => $module)
			$installed[$slug] = $module->name;

		$edit = false;
		$Gateways->ui();		// Setup setting UIs

		if ( isset($_REQUEST['id']) ) {
			$edit = $_REQUEST['id'];
			$gateway = $edit;
			$id = false;		// Instance ID for multi-instance gateways
			if (false !== strpos($edit,'-')) list($gateway,$id) = explode('-',$gateway);
			if (isset($Gateways->active[ $gateway ]) ) {
				$Gateway = $Gateways->get($gateway);
				if ($Gateway->multi && false === $id) {
					unset($Gateway->settings['cards'],$Gateway->settings['label']);
					$id = count($Gateway->settings);
				}
				$editor = $Gateway->ui($id);
			}
		}

		add_action('shopp_gateway_module_settings',array($Gateways,'templates'));
		include(SHOPP_ADMIN_PATH.'/settings/payments.php');
	}

	function payments_ui () {
		register_column_headers('shopp_page_shopp-settings-payments', array(
			'name'=>__('Name','Shopp'),
			'processor'=>__('Processor','Shopp'),
			'payments'=>__('Payments','Shopp'),
			'ssl'=>__('SSL','Shopp'),
			'captures'=>__('Captures','Shopp'),
			'recurring'=>__('Recurring','Shopp'),
			'refunds'=>__('Refunds','Shopp')
		));
	}

	function pages () {

		if ( !(current_user_can('manage_options') && current_user_can('shopp_settings')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		if (!empty($_POST['save'])) {
			check_admin_referer('shopp-settings-pages');
			$catalog_slug = Storefront::slug();
			$_POST['settings']['storefront_pages'] = Storefront::pages_settings($_POST['settings']['storefront_pages']);
			$this->settings_save();

			// Re-register page, collection, taxonomies and product rewrites
			// so that the new slugs work immediately
			global $Shopp;
			$Shopp->pages();
			$Shopp->collections();
			$Shopp->taxonomies();
			$Shopp->products();

			// If the catalog slug changes
			// $hardflush is false (soft flush... plenty of fiber, no .htaccess update needed)
			$hardflush = ($catalog_slug != Storefront::slug());
			flush_rewrite_rules($hardflush);
		}

		$pages = Storefront::pages_settings();
		include(SHOPP_ADMIN_PATH.'/settings/pages.php');

	}

	function pages_ui () {
		register_column_headers('shopp_page_shopp-settings-pages', array(
			'title'=>__('Title','Shopp'),
			'slug'=>__('Slug','Shopp'),
			'decription'=>__('Description','Shopp')
		));
	}

	function images () {

		if ( !(current_user_can('manage_options') && current_user_can('shopp_settings')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		$defaults = array(
			'paged' => 1,
			'per_page' => 25,
			'action' => false,
			'selected' => array(),
		);
		$args = array_merge($defaults,$_REQUEST);
		extract($args,EXTR_SKIP);

		$edit = false;
		if (isset($_GET['id']))  {
			$edit = (int)$_GET['id'];
			if ('new' == $_GET['id']) $edit = 'new';
		}

		if (isset($_GET['delete']) || 'delete' == $action) {
			check_admin_referer('shopp-settings-images');

			if (!empty($_GET['delete'])) $selected[] = (int)$_GET['delete'];
			$selected = array_filter($selected);
			foreach ($selected as $delete) {
				$Record = new ImageSetting( (int)$delete );
				$Record->delete();
			}
		}

		if (!empty($_POST['save'])) {
			check_admin_referer('shopp-settings-images');

			$ImageSetting = new ImageSetting($edit);
			$_POST['name'] = sanitize_title_with_dashes($_POST['name']);
			$_POST['sharpen'] = floatval(str_replace('%','',$_POST['sharpen']));
			$ImageSetting->updates($_POST);
			if (!empty($ImageSetting->name)) $ImageSetting->save();
		}

		$start = ($per_page * ($paged-1));

		$ImageSetting = new ImageSetting($edit);
		$table = $ImageSetting->_table;
		$columns = 'SQL_CALC_FOUND_ROWS *';
		$where = array(
			"type='$ImageSetting->type'",
			"context='$ImageSetting->context'"
		);
		$limit = "$start,$per_page";

		$options = compact('columns','useindex','table','joins','where','groupby','having','limit','orderby');
		$query = DB::select($options);
		$settings = DB::query($query,'array',array($ImageSetting,'loader'));
		$total = DB::found();

		$num_pages = ceil($total / $per_page);
		$ListTable = ShoppUI::table_set_pagination( $this->screen, $total, $num_pages, $per_page );

		$fit_menu = $ImageSetting->fit_menu();
		$quality_menu = $ImageSetting->quality_menu();

		$actions_menu = array(
			'delete' => __('Delete','Shopp')
		);

		$json_settings = array();
		$skip = array('created','modified','numeral','context','type','sortorder','parent');
		foreach ($settings as &$Setting)
			if (method_exists($Setting,'json'))
				$json_settings[$Setting->id] = $Setting->json($skip);

		include(SHOPP_ADMIN_PATH.'/settings/images.php');
	}

	function images_ui () {
		ShoppUI::register_column_headers('shopp_page_shopp-settings-images', array(
			'cb'=>'<input type="checkbox" />',
			'name'=>__('Name','Shopp'),
			'dimensions'=>__('Dimensions','Shopp'),
			'fit'=>__('Fit','Shopp'),
			'quality'=>__('Quality','Shopp'),
			'sharpness'=>__('Sharpness','Shopp')
		));
	}

	function system () {
		if ( !(current_user_can('manage_options') && current_user_can('shopp_settings_system')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		global $Shopp;
		$Storage = $Shopp->Storage;
		$Storage->settings();	// Load all installed storage engines for settings UIs

		if (!empty($_POST['save'])) {
			check_admin_referer('shopp-settings-system');

			if (!isset($_POST['settings']['error_notifications']))
				$_POST['settings']['error_notifications'] = array();

			$this->settings_save();

			// Reinitialize Error System
			ShoppErrors()->set_loglevel();
			ShoppErrorLogging()->set_loglevel();
			ShoppErrorNotification()->set_notifications();

			// Re-initialize Storage Engines with new settings
			$Storage->settings();

			$updated = __('Shopp system settings saved.','Shopp');
		} elseif (!empty($_POST['rebuild'])) {
			$assets = DatabaseObject::tablename(ProductImage::$table);
			$query = "DELETE FROM $assets WHERE context='image' AND type='image'";
			if (DB::query($query))
				$updated = __('All cached images have been cleared.','Shopp');
		} elseif (isset($_POST['image-settings']) || isset($_POST['download-settings'])) {
			check_admin_referer('shopp-settings-system');

			$this->settings_save();
			$Storage->settings();

		}

		if (isset($_POST['resetlog'])) {
			check_admin_referer('shopp-settings-system');
			ShoppErrorLogging()->reset();
		}

		$notifications = shopp_setting('error_notifications');
		if (empty($notifications)) $notifications = array();

		$notification_errors = array(
			SHOPP_TRXN_ERR => __('Transaction Errors','Shopp'),
			SHOPP_AUTH_ERR => __('Login Errors','Shopp'),
			SHOPP_ADDON_ERR => __('Add-on Errors','Shopp'),
			SHOPP_COMM_ERR => __('Communication Errors','Shopp'),
			SHOPP_STOCK_ERR => __('Inventory Warnings','Shopp')
			);

		$errorlog_levels = array(
			0 => __('Disabled','Shopp'),
			SHOPP_ERR => __('General Shopp Errors','Shopp'),
			SHOPP_TRXN_ERR => __('Transaction Errors','Shopp'),
			SHOPP_AUTH_ERR => __('Login Errors','Shopp'),
			SHOPP_ADDON_ERR => __('Add-on Errors','Shopp'),
			SHOPP_COMM_ERR => __('Communication Errors','Shopp'),
			SHOPP_STOCK_ERR => __('Inventory Warnings','Shopp'),
			SHOPP_ADMIN_ERR => __('Admin Errors','Shopp'),
			SHOPP_DB_ERR => __('Database Errors','Shopp'),
			SHOPP_PHP_ERR => __('PHP Errors','Shopp'),
			SHOPP_ALL_ERR => __('All Errors','Shopp'),
			SHOPP_DEBUG_ERR => __('Debugging Messages','Shopp')
			);

		$loading = array('shopp' => __('Load on Shopp-pages only','Shopp'),'all' => __('Load on entire site','Shopp'));


		// Build the storage options menu
		$storage = $engines = $storageset = array();
		foreach ($Storage->active as $module) {
			$storage[$module->module] = $module->name;
			$engines[$module->module] = sanitize_title_with_dashes($module->module);
			$storageset[$module->module] = $Storage->get($module->module)->settings;
		}

		$Storage->ui();		// Setup setting UIs

		$ImageStorage = false;
		$DownloadStorage = false;
		if (isset($_POST['image-settings']))
			$ImageStorage = $Storage->get(shopp_setting('image_storage'));

		if (isset($_POST['download-settings']))
			$DownloadStorage = $Storage->get(shopp_setting('product_storage'));

		add_action('shopp_storage_engine_settings',array($Storage,'templates'));

		include(SHOPP_ADMIN_PATH.'/settings/system.php');
	}

	function storage_ui () {
		global $Shopp;
		$Shopp->Storage->settings();
		$Shopp->Storage->ui();
	}


	function settings_save () {
		if (empty($_POST['settings']) || !is_array($_POST['settings'])) return false;
		foreach ($_POST['settings'] as $setting => $value)
			shopp_set_setting($setting,$value);
	}

} // END class Setup

?>