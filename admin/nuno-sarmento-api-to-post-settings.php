<?php defined('ABSPATH') or die();

class Nuno_Sarmento_ATP_OptionsPage {

		private $options_general;
	  private $options_about;
		private $options_report;

		public function __construct() {
      add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
      add_action( 'admin_init', array( $this, 'ns_atp_options_init' ) );
    }

    public function add_plugin_page(){
    	add_menu_page(
				'NS API To Post',
				'NS API To Post',
				'manage_options',
				'ns-atp',
				array( $this,'ns_apt_admin_nscodes'),
				'dashicons-external'
			);
    }

    public function ns_apt_admin_nscodes() {

		$this->options_general = get_option( 'ns_atp_general' );
		$this->options_about = get_option( 'ns_atp_about' );
		$this->options_report = get_option( 'ns_atp_report' );
		$about_Screen = ( isset( $_GET['action'] ) && 'about' == $_GET['action'] ) ? true : false;
    $report_Screen = ( isset( $_GET['action'] ) && 'report' == $_GET['action'] ) ? true : false;

		?>
		<style media="screen">
		.header__ns_nsss:after { content: " "; display: block; height: 29px; width: 15%; position: absolute;
			top: 3%; right: 25px; background-image: url(//ps.w.org/nuno-sarmento-social-icons/assets/icon-128x128.png?rev=1588574); background-size:128px 128px; height: 128px; width: 128px;
		}
		.header__ns_nsss{ background: white; height: 150px; width: 100%; float: left;}
		.header__ns_nsss h2 {padding: 35px;font-size: 27px;}
		@media only screen and (max-width: 480px) {
			.header__ns_nsss:after { content: " "; display: block; height: 29px; width: 15%; position: absolute;
				top: 6%; right: 25px; background-image: url(//ps.w.org/nuno-sarmento-social-icons/assets/icon-128x128.png?rev=1588574); background-size:50px 50px; height: 50px; width: 50px;
			}
		}
		</style>

		<div class="wrap">
		<div class="header__ns_nsss">
			<h2><?php echo NUNO_SARMENTO_API_TO_POST_NAME; ?></h2>
		</div>
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo admin_url( 'admin.php?page=ns-atp' ); ?>" class="nav-tab<?php if ( ! isset( $_GET['action'] ) || isset( $_GET['action'] ) && 'about' != $_GET['action']  && 'report' != $_GET['action'] ) echo ' nav-tab-active'; ?>"><?php esc_html_e( 'Settings' ); ?></a>
				<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'about' ), admin_url( 'admin.php?page=ns-atp' ) ) ); ?>" class="nav-tab<?php if ( $about_Screen ) echo ' nav-tab-active'; ?>"><?php esc_html_e( 'Other Plugins' ); ?></a>
				<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'report' ), admin_url( 'admin.php?page=ns-atp' ) ) ); ?>" class="nav-tab<?php if ( $report_Screen ) echo ' nav-tab-active'; ?>"><?php esc_html_e( 'System Report' ); ?></a>
			</h2>
		 <form method="post" action="options.php">
			 <?php
				 if($about_Screen) {
					settings_fields( 'ns_atp_about' );
					do_settings_sections( 'ns-atp-setting-about' );

				} elseif($report_Screen) {
					settings_fields( 'ns_atp_report' );
					do_settings_sections( 'ns-atp-setting-report' );

				}else {
					settings_fields( 'ns_atp_general' );
					do_settings_sections( 'ns-atp-setting-admin' );
					submit_button();
				}
			?>
		</form>
	 </div>
	<?php
	}

  public function ns_atp_options_init() {
		// URL field register
    register_setting(
          'ns_atp_general', // Option group
          'ns_atp_general', // Option name
          array( $this, 'sanitize' ) // Sanitize
      	);

      	add_settings_section(
          'setting_section_id', // ID
          'Add your API URL below', // Title
          array( $this, 'print_section_info' ), // Callback
          'ns-atp-setting-admin' // Page
      	);
				add_settings_field(
					'ns_atp_url', // id
					__( 'WP API URL', 'nuno-sarmento-api-to-post' ), // title
					array( $this, 'ns_atp_url_callback' ), // callback
					'ns-atp-setting-admin', // page
					'setting_section_id' // section
				);


				// About Page register
				register_setting(
            'ns_atp_about', // Option group
            'ns_atp_about', // Option name
            array( $this, 'ns_atp_about_callback' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            '', // Title
            array( $this, 'ns_atp_about_callback' ), // Callback
            'ns-atp-setting-about' // Page
        );

				// Sytem Report register
				register_setting(
		         'ns_atp_report', // Option group
		         'ns_atp_report', // Option name
		         array( $this, 'ns_atp_snapshot_report' ) // Sanitize
		    );

		    add_settings_section(
	          'setting_section_id', // ID
	          '', // Title
	          array( $this, 'ns_atp_snapshot_report' ), // Callback
	          'ns-atp-setting-report' // Page
	      );

			}

			public function ns_atp_url_callback() {
					printf(
						'<input class="regular-text" type="text" name="ns_atp_general[ns_atp_url]" id="ns_atp_url" value="%s">',
						isset( $this->options_general['ns_atp_url'] ) ? esc_attr( $this->options_general['ns_atp_url']) : ''
					);
			}

			public function print_section_info(){
					echo ('<h4><strong>Example:</strong></h4>http://test.dev/wp-json/wp/v2/posts/?_embed');
			}

		/************************************* SYSTEM REPORT *************************************************/

		/**
		 * helper function for number conversions
		 *
		 * @access public
		 * @param mixed $v
		 * @return void
		 */

		public function ns_atp_num_convt( $v ) {
			$l   = substr( $v, -1 );
			$ret = substr( $v, 0, -1 );

			switch ( strtoupper( $l ) ) {
				case 'P': // fall-through
				case 'T': // fall-through
				case 'G': // fall-through
				case 'M': // fall-through
				case 'K': // fall-through
					$ret *= 1024;
					break;
				default:
					break;
			}

			return $ret;
		}


		public function ns_atp_about_callback() {

			?>
			<h1>'Nuno Sarmento' Plugins Colection</h1>

				<div class="wrap">

							<p class="clear"></p>

							<div class="plugin-group">

							<div class="plugin-card">

								 <div class="plugin-card-top">

										 <a href="https://en-gb.wordpress.org/plugins/nuno-sarmento-slick-slider/" class="plugin-icon" target="_blank">
										 	 <style type="text/css">#plugin-icon-nuno-sarmento-slick-slider { width:128px; height:128px; background-image: url(//ps.w.org/nuno-sarmento-slick-slider/assets/icon-128x128.png?rev=1588561); background-size:128px 128px; }@media only screen and (-webkit-min-device-pixel-ratio: 1.5) { #plugin-icon-nuno-sarmento-slick-slider { background-image: url(//ps.w.org/nuno-sarmento-slick-slider/assets/icon-256x256.png?rev=1588561); } }</style>
											 <div class="plugin-icon" id="plugin-icon-nuno-sarmento-slick-slider" style="float:left; margin: 3px 6px 6px 0px;"></div>
										 </a>

										 <div class="name column-name" style="float: right;">
										    <h4><a href="https://en-gb.wordpress.org/plugins/nuno-sarmento-slick-slider/" target="_blank">Nuno Sarmento Slick Slider</a></h4>
									 	 </div>

								</div>

								<div class="plugin-card-bottom">
									<p class="authors"><cite>By: <a href="//profiles.wordpress.org/nunosarmento/" target="_blank">Nuno Morais Sarmento</a>.</cite></p>
								</div>

							</div>

							<div class="plugin-card">

								 <div class="plugin-card-top">

										 <a href="https://en-gb.wordpress.org/plugins/nuno-sarmento-custom-css-js/" class="plugin-icon" target="_blank">
										 	 <style type="text/css">#plugin-icon-nuno-sarmento-custom-css-js { width:128px; height:128px; background-image: url(//ps.w.org/nuno-sarmento-custom-css-js/assets/icon-128x128.png?rev=1588566); background-size:128px 128px; }@media only screen and (-webkit-min-device-pixel-ratio: 1.5) { #plugin-icon-nuno-sarmento-custom-css-js { background-image: url(//ps.w.org/nuno-sarmento-custom-css-js/assets/icon-256x256.png?rev=1588566); } }</style>
											 <div class="plugin-icon" id="plugin-icon-nuno-sarmento-custom-css-js" style="float:left; margin: 3px 6px 6px 0px;"></div>
										 </a>

										 <div class="name column-name" style="float: right;">
										 		<h4><a href="https://en-gb.wordpress.org/plugins/nuno-sarmento-custom-css-js/" target="_blank">Nuno Sarmento Custom CSS - JS</a></h4>
									 	 </div>

								</div>

								<div class="plugin-card-bottom">
									<p class="authors"><cite>By: <a href="//profiles.wordpress.org/nunosarmento/" target="_blank">Nuno Morais Sarmento</a>.</cite></p>
								</div>

							</div>

							<div class="plugin-card">

								 <div class="plugin-card-top">

										 <a href="https://en-gb.wordpress.org/plugins/nuno-sarmento-popup/" class="plugin-icon" target="_blank">
											 <style type="text/css">#plugin-icon-nuno-sarmento-popup { width:128px; height:128px; background-image: url(//ps.w.org/nuno-sarmento-popup/assets/icon-128x128.png?rev=1593940); background-size:128px 128px; }@media only screen and (-webkit-min-device-pixel-ratio: 1.5) { #plugin-icon-nuno-sarmento-popup { background-image: url(//ps.w.org/nuno-sarmento-popup/assets/icon-256x256.png?rev=1593940); } }</style>
											 <div class="plugin-icon" id="plugin-icon-nuno-sarmento-popup" style="float:left; margin: 3px 6px 6px 0px;"></div>
										 </a>

										 <div class="name column-name" style="float: right;">
										    <h4><a href="https://en-gb.wordpress.org/plugins/nuno-sarmento-popup/" target="_blank" >Nuno Sarmento PopUp</a></h4>
									   </div>

								</div>

								<div class="plugin-card-bottom">
									<p class="authors"><cite>By: <a href="//profiles.wordpress.org/nunosarmento/" target="_blank">Nuno Morais Sarmento</a>.</cite></p>
								</div>

						 </div>


						 <div class="plugin-card">

						 	 <div class="plugin-card-top">

						 		 <a href="https://en-gb.wordpress.org/plugins/nuno-sarmento-api-to-post/" class="plugin-icon">
									 <style type="text/css">#plugin-icon-nuno-sarmento-api-to-post { width:128px; height:128px; background-image: url(//ps.w.org/nuno-sarmento-api-to-post/assets/icon-128x128.png?rev=1594469); background-size:128px 128px; }@media only screen and (-webkit-min-device-pixel-ratio: 1.5) { #plugin-icon-nuno-sarmento-api-to-post { background-image: url(//ps.w.org/nuno-sarmento-api-to-post/assets/icon-256x256.png?rev=1594469); } }</style>
									 <div class="plugin-icon" id="plugin-icon-nuno-sarmento-api-to-post" style="float:left; margin: 3px 6px 6px 0px;"></div>
							 	 </a>

						 		 <div class="name column-name">
						 			 <h4><a href="https://en-gb.wordpress.org/plugins/nuno-sarmento-api-to-post/">Nuno Sarmento API To Post</a></h4>
						 		 </div>

						 	</div>

							<div class="plugin-card-bottom">
								<p class="authors"><cite>By: <a href="//profiles.wordpress.org/nunosarmento/" target="_blank">Nuno Morais Sarmento</a>.</cite></p>
							</div>

						 </div>

						 <div class="plugin-card">

						 	 <div class="plugin-card-top">

						 		 <a href="https://wordpress.org/plugins/change-wp-admin-login/" class="plugin-icon">
									 <style type="text/css">#plugin-icon-change-wp-admin-login { width:128px; height:128px; background-image: url(//ps.w.org/change-wp-admin-login/assets/icon-256x256.png?rev=2040699); background-size:128px 128px; }@media only screen and (-webkit-min-device-pixel-ratio: 1.5) { #plugin-icon-nuno-sarmento-api-to-post { background-image: url(//ps.w.org/change-wp-admin-login/assets/icon-256x256.png?rev=2040699); } }</style>
									 <div class="plugin-icon" id="plugin-icon-change-wp-admin-login" style="float:left; margin: 3px 6px 6px 0px;"></div>
							 	 </a>

						 		 <div class="name column-name">
						 			 <h4><a href="https://wordpress.org/plugins/change-wp-admin-login/">Change wp-admin login</a></h4>
						 		 </div>

						 	</div>

							<div class="plugin-card-bottom">
								<p class="authors"><cite>By: <a href="//profiles.wordpress.org/nunosarmento/" target="_blank">Nuno Morais Sarmento</a>.</cite></p>
							</div>

						 </div>


					</div>

			  </div>

			<?php

		}

	public function ns_atp_snapshot_report() {

		?>
			<div class="wrap nuno-sarmento-system-wrap">
				<div class="icon32" id="icon-tools"><br></div>
				<h2><?php _e( 'System Report ', 'nuno-sarmento-system-report' ) ?></h2>
				<p><?php echo $this->nuno_sarmento_atp_snapshot_data(); ?></p>
			</div>
	 <?php

	}

	public function nuno_sarmento_atp_snapshot_data() {

		// call WP database
		global $wpdb;

		// check for browser class add on
		if ( ! class_exists( 'Browser' ) ) {
			require_once NUNO_SARMENTO_API_TO_POST_BASE_PATH . 'includes/nuno-sarmento-api-to-post-browser.php';
		}

		// do WP version check and get data accordingly
		$browser = new Browser();
		if ( get_bloginfo( 'version' ) < '3.4' ) :
			$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );
			$theme      = $theme_data['Name'] . ' ' . $theme_data['Version'];
		else:
			$theme_data = wp_get_theme();
			$theme      = $theme_data->Name . ' ' . $theme_data->Version;
		endif;

		// data checks for later
		$frontpage	= get_option( 'page_on_front' );
		$frontpost	= get_option( 'page_for_posts' );
		$mu_plugins = get_mu_plugins();
		$plugins	= get_plugins();
		$active		= get_option( 'active_plugins', array() );

		// multisite details
		$nt_plugins	= is_multisite() ? wp_get_active_network_plugins() : array();
		$nt_active	= is_multisite() ? get_site_option( 'active_sitewide_plugins', array() ) : array();
		$ms_sites	= is_multisite() ? get_blog_list() : null;

		// yes / no specifics
		$ismulti	= is_multisite() ? __( 'Yes', 'nuno-sarmento-system-report' ) : __( 'No', 'nuno-sarmento-system-report' );
		$safemode	= ini_get( 'safe_mode' ) ? __( 'Yes', 'nuno-sarmento-system-report' ) : __( 'No', 'nuno-sarmento-system-report' );
		$wpdebug	= defined( 'WP_DEBUG' ) ? WP_DEBUG ? __( 'Enabled', 'nuno-sarmento-system-report' ) : __( 'Disabled', 'nuno-sarmento-system-report' ) : __( 'Not Set', 'nuno-sarmento-system-report' );
		$tbprefx	= strlen( $wpdb->prefix ) < 16 ? __( 'Acceptable', 'nuno-sarmento-system-report' ) : __( 'Too Long', 'nuno-sarmento-system-report' );
		$fr_page	= $frontpage ? get_the_title( $frontpage ).' (ID# '.$frontpage.')'.'' : __( 'n/a', 'nuno-sarmento-system-report' );
		$fr_post	= $frontpage ? get_the_title( $frontpost ).' (ID# '.$frontpost.')'.'' : __( 'n/a', 'nuno-sarmento-system-report' );
		$errdisp	= ini_get( 'display_errors' ) != false ? __( 'On', 'nuno-sarmento-system-report' ) : __( 'Off', 'nuno-sarmento-system-report' );

		$jquchk		= wp_script_is( 'jquery', 'registered' ) ? $GLOBALS['wp_scripts']->registered['jquery']->ver : __( 'n/a', 'nuno-sarmento-system-report' );

		$sessenb	= isset( $_SESSION ) ? __( 'Enabled', 'nuno-sarmento-system-report' ) : __( 'Disabled', 'nuno-sarmento-system-report' );
		$usecck		= ini_get( 'session.use_cookies' ) ? __( 'On', 'nuno-sarmento-system-report' ) : __( 'Off', 'nuno-sarmento-system-report' );
		$useocck	= ini_get( 'session.use_only_cookies' ) ? __( 'On', 'nuno-sarmento-system-report' ) : __( 'Off', 'nuno-sarmento-system-report' );
		$hasfsock	= function_exists( 'fsockopen' ) ? __( 'Supports fsockopen.', 'nuno-sarmento-system-report' ) : __( 'Not support fsockopen.', 'nuno-sarmento-system-report' );
		$hascurl	= function_exists( 'curl_init' ) ? __( 'Supports cURL.', 'nuno-sarmento-system-report' ) : __( 'Not support cURL.', 'nuno-sarmento-system-report' );
		$hassoap	= class_exists( 'SoapClient' ) ? __( 'SOAP Client enabled.', 'nuno-sarmento-system-report' ) : __( 'Does not have the SOAP Client enabled.', 'nuno-sarmento-system-report' );
		$hassuho	= extension_loaded( 'suhosin' ) ? __( 'Server has SUHOSIN installed.', 'nuno-sarmento-system-report' ) : __( 'Does not have SUHOSIN installed.', 'nuno-sarmento-system-report' );
		$openssl	= extension_loaded('openssl') ? __( 'OpenSSL installed.', 'nuno-sarmento-system-report' ) : __( 'Does not have OpenSSL installed.', 'nuno-sarmento-system-report' );

		// start generating report
		$report	= '';
		$report	.= '<textarea readonly="readonly" id="nuno-sarmento-system-textarea" name="nuno-sarmento-system-textarea">';
		$report	.= '--- Begin System Info ---'."\n";
		// add filter for adding to report opening
		$report	.= apply_filters( 'snapshot_report_before', '' );

		$report	.= "\n\t".'-- SERVER DATA --'."\n";
		$report	.= 'jQuery Version'."\t\t\t\t".$jquchk."\n";
		$report	.= 'PHP Version:'."\t\t\t\t".PHP_VERSION."\n";
		$report	.= 'MySQL Version:'."\t\t\t\t".$wpdb->db_version()."\n";
		$report	.= 'Server Software:'."\t\t\t".$_SERVER['SERVER_SOFTWARE']."\n";

		$report	.= "\n\t".'-- PHP CONFIGURATION --'."\n";
		$report	.= 'Safe Mode:'."\t\t\t\t".$safemode."\n";
		$report	.= 'Memory Limit:'."\t\t\t\t".ini_get( 'memory_limit' )."\n";
		$report	.= 'Upload Max:'."\t\t\t\t".ini_get( 'upload_max_filesize' )."\n";
		$report	.= 'Post Max:'."\t\t\t\t".ini_get( 'post_max_size' )."\n";
		$report	.= 'Time Limit:'."\t\t\t\t".ini_get( 'max_execution_time' )."\n";
		$report	.= 'Max Input Vars:'."\t\t\t\t".ini_get( 'max_input_vars' )."\n";
		$report	.= 'Display Errors:'."\t\t\t\t".$errdisp."\n";
		$report	.= 'Sessions:'."\t\t\t\t".$sessenb."\n";
		$report	.= 'Session Name:'."\t\t\t\t".esc_html( ini_get( 'session.name' ) )."\n";
		$report	.= 'Cookie Path:'."\t\t\t\t".esc_html( ini_get( 'session.cookie_path' ) )."\n";
		$report	.= 'Save Path:'."\t\t\t\t".esc_html( ini_get( 'session.save_path' ) )."\n";
		$report	.= 'Use Cookies:'."\t\t\t\t".$usecck."\n";
		$report	.= 'Use Only Cookies:'."\t\t\t".$useocck."\n";
		$report	.= 'FSOCKOPEN:'."\t\t\t\t".$hasfsock."\n";
		$report	.= 'cURL:'."\t\t\t\t\t".$hascurl."\n";
		$report	.= 'SOAP Client:'."\t\t\t\t".$hassoap."\n";
		$report	.= 'SUHOSIN:'."\t\t\t\t".$hassuho."\n";
		$report	.= 'OpenSSL:'."\t\t\t\t".$openssl."\n";

		$report	.= "\n\t".'-- WORDPRESS DATA --'."\n";
		$report	.= 'Multisite:'."\t\t\t\t".$ismulti."\n";
		$report	.= 'SITE_URL:'."\t\t\t\t".site_url()."\n";
		$report	.= 'HOME_URL:'."\t\t\t\t".home_url()."\n";
		$report	.= 'WP Version:'."\t\t\t\t".get_bloginfo( 'version' )."\n";
		$report	.= 'Permalink:'."\t\t\t\t".get_option( 'permalink_structure' )."\n";
		$report	.= 'Cur Theme:'."\t\t\t\t".$theme."\n";
		$report	.= 'Post Types:'."\t\t\t\t".implode( ', ', get_post_types( '', 'names' ) )."\n";
		$report	.= 'Post Stati:'."\t\t\t\t".implode( ', ', get_post_stati() )."\n";
		$report	.= 'User Count:'."\t\t\t\t".count( get_users() )."\n";

		$report	.= "\n\t".'-- WORDPRESS CONFIG --'."\n";
		$report	.= 'WP_DEBUG:'."\t\t\t\t".$wpdebug."\n";
		$report	.= 'WP Memory Limit:'."\t\t\t".$this->ns_atp_num_convt( WP_MEMORY_LIMIT )/( 1024 ).'MB'."\n";
		$report	.= 'Table Prefix:'."\t\t\t\t".$wpdb->base_prefix."\n";
		$report	.= 'Prefix Length:'."\t\t\t\t".$tbprefx.' ('.strlen( $wpdb->prefix ).' characters)'."\n";
		$report	.= 'Show On Front:'."\t\t\t\t".get_option( 'show_on_front' )."\n";
		$report	.= 'Page On Front:'."\t\t\t\t".$fr_page."\n";
		$report	.= 'Page For Posts:'."\t\t\t\t".$fr_post."\n";

		if ( is_multisite() ) :
			$report	.= "\n\t".'-- MULTISITE INFORMATION --'."\n";
			$report	.= 'Total Sites:'."\t\t\t\t".get_blog_count()."\n";
			$report	.= 'Base Site:'."\t\t\t\t".$ms_sites[0]['domain']."\n";
			$report	.= 'All Sites:'."\n";
			foreach ( $ms_sites as $site ) :
				if ( $site['path'] != '/' )
					$report	.= "\t\t".'- '. $site['domain'].$site['path']."\n";

			endforeach;
			$report	.= "\n";
		endif;

		$report	.= "\n\t".'-- BROWSER DATA --'."\n";
		$report	.= 'Platform:'."\t\t\t\t".$browser->getPlatform()."\n";
		$report	.= 'Browser Name'."\t\t\t\t". $browser->getBrowser() ."\n";
		$report	.= 'Browser Version:'."\t\t\t".$browser->getVersion()."\n";
		$report	.= 'Browser User Agent:'."\t\t\t".$browser->getUserAgent()."\n";

		$report	.= "\n\t".'-- PLUGIN INFORMATION --'."\n";
		if ( $plugins && $mu_plugins ) :
			$report	.= 'Total Plugins:'."\t\t\t\t".( count( $plugins ) + count( $mu_plugins ) + count( $nt_plugins ) )."\n";
		endif;

		// output must-use plugins
		if ( $mu_plugins ) :
			$report	.= 'Must-Use Plugins: ('.count( $mu_plugins ).')'. "\n";
			foreach ( $mu_plugins as $mu_path => $mu_plugin ) :
				$report	.= "\t".'- '.$mu_plugin['Name'] . ' ' . $mu_plugin['Version'] ."\n";
			endforeach;
			$report	.= "\n";
		endif;

		// if multisite, grab active network as well
		if ( is_multisite() ) :
			// active network
			$report	.= 'Network Active Plugins: ('.count( $nt_plugins ).')'. "\n";

			foreach ( $nt_plugins as $plugin_path ) :
				if ( array_key_exists( $plugin_base, $nt_plugins ) )
					continue;

				$plugin = get_plugin_data( $plugin_path );

				$report	.= "\t".'- '.$plugin['Name'] . ' ' . $plugin['Version'] ."\n";
			endforeach;
			$report	.= "\n";

		endif;

		// output active plugins
		if ( $plugins ) :
			$report	.= 'Active Plugins: ('.count( $active ).')'. "\n";
			foreach ( $plugins as $plugin_path => $plugin ) :
				if ( ! in_array( $plugin_path, $active ) )
					continue;
				$report	.= "\t".'- '.$plugin['Name'] . ' ' . $plugin['Version'] ."\n";
			endforeach;
			$report	.= "\n";
		endif;

		// output inactive plugins
		if ( $plugins ) :
			$report	.= 'Inactive Plugins: ('.( count( $plugins ) - count( $active ) ).')'. "\n";
			foreach ( $plugins as $plugin_path => $plugin ) :
				if ( in_array( $plugin_path, $active ) )
					continue;
				$report	.= "\t".'- '.$plugin['Name'] . ' ' . $plugin['Version'] ."\n";
			endforeach;
			$report	.= "\n";
		endif;

		// add filter for end of report
		$report	.= apply_filters( 'snapshot_report_after', '' );

		// end it all
		$report	.= "\n".'--- End System Info ---';
		$report	.= '</textarea>';

		return $report;
	}


   public function sanitize( $input )  {
        $new_input = array();

				if( isset( $input['ns_atp_url'] ) )
			      $new_input['ns_atp_url'] = sanitize_text_field( $input['ns_atp_url'] );

        return $new_input;
    }
}

if( is_admin() )
 $settings_page = new Nuno_Sarmento_ATP_OptionsPage();

/*
 * Retrieve this value with:
 * $nuno_sarmento_api_to_post_options = get_option( 'nuno_sarmento_api_to_post_option_name' ); // Array of All Options
 * $nuno_sarmento_api_to_post_url_0 = $nuno_sarmento_api_to_post_options['nuno_sarmento_api_to_post_url_0']; // Nuno Sarmento API To Post URL
 */
