<?php defined('ABSPATH') or die();

/* === ADMIN SCRIPTS === */
/* ------------------------------------------
// Enqueue CSS & JS---------------------------
--------------------------------------------- */
function ns_atp_load_admin(){
 wp_enqueue_style('report-ns', plugins_url('/assets/css/nuno-sarmento-atp-report.css', dirname(__FILE__)),  array(), NUNO_SARMENTO_API_TO_POST_BASE_VERSION,	'all' );
}
add_action('admin_enqueue_scripts', 'ns_atp_load_admin', 10);

function nuno_sarmento_api_to_post_admin_scripts() {
  wp_enqueue_style('nuno-sarmento-api-to-post-css', plugins_url('/assets/css/nuno-sarmento-atp.css', dirname(__FILE__)),  array(), NUNO_SARMENTO_API_TO_POST_BASE_VERSION,	'all' );
	wp_enqueue_style( 'nuno-sarmento-api-to-post-css' );
  wp_enqueue_script('nuno-sarmento-api-to-post-js', plugins_url('/assets/js/nuno-sarmento-api-to-post-scripts.js', dirname(__FILE__)) );
	wp_enqueue_script('nuno-sarmento-api-to-post-js');

}
add_action('wp_enqueue_scripts', 'nuno_sarmento_api_to_post_admin_scripts', 100);

// Funtion confirmation page form shortcode
function nuno_sarmento_api_to_post_shortcode() {
	ob_start();

 ?>

 <div class="conten-ns-atp">

	<div class="main-ns-atp">

		<?php

			$nuno_sarmento_api_to_post_options = get_option( 'ns_atp_general' ); // Array of All Options
			$nuno_sarmento_api_to_post_url_0 = $nuno_sarmento_api_to_post_options['ns_atp_url']; // Nuno Sarmento API To Post URL
      $base_url = $nuno_sarmento_api_to_post_url_0;

      if ( '' == @file_get_contents($base_url) )
      {
         echo "<script>alert('API URL not setup on your plugin admin area, please go to NS API To Post admin panel and verify your URL');</script>";
         echo 'API URL not setup on your plugin admin area, please go to NS API To Post admin panel and verify your URL';
         return;

      }else{

        $base_url = $nuno_sarmento_api_to_post_url_0;
        $response = file_get_contents($base_url);
        $result = json_decode($response,true);
      }
		?>

  		<div class="page__column-ns-atp">

  			<?php foreach($result as $key => $val): ?>

  					<article class="article-permalink-ns-atp" >

  						<header class="header-ns-atp">
  							<?php if(!empty($result[$key]['_embedded']['wp:featuredmedia'][0]['source_url'])) : ?>
  		              <img src="<?php echo $result[$key]['_embedded']['wp:featuredmedia'][0]['source_url']; ?>">
  		          <?php endif; ?>
  						</header>

  						<div class="entry-summary">
  							<time class="published"><?php echo date("d M Y", strtotime($result[$key]['date'])); ?></time>
  							<h2><?php echo $result[$key]['title']['rendered']; ?></h2>
  							<p><?php echo str_replace('<span class="read-more">', '<span class="read-more"><a href="'.$result[$key]['link'].'">', $result[$key]['excerpt']['rendered']); ?></a></p>
  						</div>

  				</article>

  			<?php endforeach; ?>

  		</div>

	</div>

</div>

<?php

return ob_get_clean();
}
add_shortcode( 'ns_api_to_post', 'nuno_sarmento_api_to_post_shortcode' );
