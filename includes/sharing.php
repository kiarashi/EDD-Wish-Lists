<?php
/**
 * Sharing
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Check that each social network is enabled
 * @param  string  $network
 * @return boolean
 * @since  1.0
 */
function edd_wl_sharing_is_enabled( $network = '' ) {
	global $edd_options;

	$networks = edd_get_option( 'edd_wl_services', '' );

	// if network is passed as parameter
	if ( $network ) {
		switch ( $network ) {

			case 'twitter':
				return isset( $networks[$network] );
				break;

			case 'facebook':
				return isset( $networks[$network] );
				break;
				
			case 'googleplus':
				return isset( $networks[$network] );
				break;
				
			case 'linkedin':
				return isset( $networks[$network] );
				break;			
			
		}
	}
	elseif ( $networks ) {
		return true;
	}

}

/**
 * Main share box that is displayed on the page
 * 
 * @param  string $id 		post/page/download ID
 * @param  string $title 	custom title
 * @param  string $message 	custom message
 * @param  string $tweet 	custom tweet message
 * @return void
 * @since  2.0
 */
function edd_wl_sharing_services() {
	global $edd_options;

	// get list ID
	$list_id = get_query_var( 'view' );

	// return if our share box has been turned off
	// if ( ! self::$share_box_enabled )
	// 	return;

	$edd_wish_lists = edd_wish_lists();
	$edd_wish_lists::$add_script = true;

	$sharing_layout = apply_filters( 'edd_wl_sharing_layout', 'vertical' );

	if ( 'vertical' == $sharing_layout ) {
		$twitter_layout 	= 'data-count="vertical"';
		$facebook_layout 	= 'data-layout="box_count"';
		$googleplus_layout 	= 'data-size="tall"';
		$linkedin_layout 	= 'data-counter="top"';
	}
	elseif ( 'horizontal' == $sharing_layout ) {
		$twitter_layout 	= 'data-count="horizontal"';
		$facebook_layout 	= 'data-layout="button_count"';
		$googleplus_layout 	= 'data-size="medium"';
		$linkedin_layout 	= 'data-counter="right"';
	}
	else {
		$twitter_layout 	= '';
		$facebook_layout 	= '';
		$googleplus_layout 	= '';
		$linkedin_layout 	= '';
	}

	// twitter message
	$twitter_text 	= apply_filters( 'edd_wl_twitter_text', get_the_title( $list_id ) );
	
	// URL to share. Uses shortlink
	$share_url 		= apply_filters( 'edd_wl_share_url', wp_get_shortlink( get_query_var( 'view' ) ) );

	// get services
	$services 		= edd_get_option( 'edd_wl_services', '' );

	// return if there are no services
	if ( empty( $services ) )
		return;
	
	ob_start();
	?>
	<div class="<?php echo apply_filters( 'edd_wl_share_classes', 'edd-wl-share' ); ?>">
		<?php do_action( 'edd_wl_before_share_box' ); ?>

		<?php if ( edd_wl_sharing_is_enabled( 'twitter' ) ) :
			$locale 				= apply_filters( 'edd_wl_twitter_locale', 'en' );
			$twitter_button_size 	= apply_filters( 'edd_wl_twitter_button_size', 'medium' );
		?>
		<div class="edd-wl-service twitter">
			<a href="https://twitter.com/share" data-width="100" data-lang="<?php echo $locale; ?>" class="twitter-share-button" <?php echo $twitter_layout; ?> data-size="<?php echo $twitter_button_size; ?>" data-counturl="<?php echo post_permalink( get_query_var( 'view' ) ); ?>" data-url="<?php echo $share_url; ?>" data-text="<?php echo $twitter_text; ?>" data-related=""><?php _e( 'Share', 'edd-wish-lists' ); ?></a>
		</div>
		<?php endif; ?>

		<?php if ( edd_wl_sharing_is_enabled( 'facebook' ) ) :
			$data_share = apply_filters( 'edd_wl_facebook_share_button', 'false' );
			$layout 	= isset ( $sharing_layout ) ? 'data-layout="' . $sharing_layout . '"' : '';
		?>
		
		<div class="edd-wl-service facebook">
			<div class="fb-like" data-href="<?php echo $share_url; ?>" data-send="true" data-action="like" <?php echo $facebook_layout; ?> data-share="<?php echo $data_share; ?>" data-width="" data-show-faces="false"></div>
		</div>
		<?php endif; ?>

		<?php if ( edd_wl_sharing_is_enabled( 'googleplus' ) ) : 
			$google_button_annotation 		= apply_filters( 'edd_wl_googleplus_button_annotation', 'bubble' );
			$google_button_recommendations 	= apply_filters( 'edd_wl_googleplus_button_recommendations', 'false' );
		?>
		<div class="edd-wl-service googleplus">
			<div class="g-plusone" data-recommendations="<?php echo $google_button_recommendations; ?>" data-annotation="<?php echo $google_button_annotation;?>" data-callback="plusOned" <?php echo $googleplus_layout; ?> data-href="<?php echo $share_url; ?>"></div>
		</div>
		<?php endif; ?>

		<?php if ( edd_wl_sharing_is_enabled( 'linkedin' ) ) :
			$locale = apply_filters( 'edd_wl_linkedin_locale', 'en_US' );
		?>
		<div class="edd-wl-service linkedin">
		<script src="http://platform.linkedin.com/in.js" type="text/javascript">lang: <?php echo $locale; ?></script>
		<script type="IN/Share" <?php echo $linkedin_layout; ?> data-onSuccess="share" data-url="<?php echo $share_url; ?>"></script>
		</div>
		<?php endif; ?>

		<?php do_action( 'edd_wl_after_share_box' ); ?>
	</div>

<?php 
	$share_box = ob_get_clean();
	return apply_filters( 'edd_wl_share_box', $share_box );
}

/**
 * Print scripts
 *
 * @since 1.0
*/
function edd_wl_sharing_print_scripts() {
	global $edd_options;

	$edd_wish_lists = edd_wish_lists();

	if ( ! $edd_wish_lists::$add_script )
		return;
	?>
	<script type="text/javascript">

	<?php 
	/**
	 * Twitter
	 *
	 * @since 1.0
	*/
	if ( edd_wl_sharing_is_enabled( 'twitter' ) ) : 
		?>
	  	window.twttr = (function (d,s,id) {
		  var t, js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return; js=d.createElement(s); js.id=id;
		  js.src="https://platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs);
		  return window.twttr || (t = { _e: [], ready: function(f){ t._e.push(f) } });
		}(document, "script", "twitter-wjs"));

		twttr.ready(function (twttr) {
		    twttr.events.bind('tweet', function (event) {
		        jQuery.event.trigger({
		            type: "listShared",
		            url: event.target.baseURI
		        });
		    });
		});

		<?php endif; ?>

		<?php 
		/**
		 * Google +
		 *
		 * @since 1.0
		*/
		if ( edd_wl_sharing_is_enabled( 'googleplus' ) ) : 
			$locale = apply_filters( 'edd_wl_googleplus_locale', 'en-US' );
		?>
			window.___gcfg = {
			  lang: '<?php echo $locale; ?>',
			  parsetags: 'onload'
			};

			(function() {
			    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
			    po.src = 'https://apis.google.com/js/plusone.js';
			    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
			  })();

			function plusOned(obj) {
				console.log(obj);
				jQuery.event.trigger({
				    type: "listShared",
				    url: obj.href
				});
			}
		<?php endif; ?>

		<?php 
		/**
		 * LinkedIn
		 *
		 * @since 1.0
		*/
		if ( edd_wl_sharing_is_enabled( 'linkedin' ) ) : ?>
			function share(url) {
				console.log(url);
			 	jQuery.event.trigger({
		            type: "listShared",
		            url: url
		        });
			}
		
		<?php endif; ?>

		<?php
		/**
		 * Facebook
		 *
		 * @since 1.0
		*/
		if ( edd_wl_sharing_is_enabled( 'facebook' ) ) : 
			// defaults to en_US if left blank
			$locale = apply_filters( 'edd_wl_facebook_locale', 'en_US' );
			?>

			(function(d, s, id) {
			     var js, fjs = d.getElementsByTagName(s)[0];
			     if (d.getElementById(id)) {return;}
			     js = d.createElement(s); js.id = id;
			     js.src = "//connect.facebook.net/<?php echo $locale; ?>/all.js";
			     fjs.parentNode.insertBefore(js, fjs);
			 }(document, 'script', 'facebook-jssdk'));

			window.fbAsyncInit = function() {
			    // init the FB JS SDK
			    FB.init({
			      status	: true,
			      cookie	: true,                               
			      xfbml		: true                              
			    });

			    FB.Event.subscribe('edge.create', function(href, widget) {
			        jQuery.event.trigger({
			            type: "listShared",
			            url: href
			        });     
			    });
			};
		<?php endif; ?>

		<?php 
		/**
		 * Listen for the productShared event
		 *
		 * @since 1.0
		*/
		if ( edd_wl_sharing_is_enabled() ) : ?>

		/* <![CDATA[ */
		var edd_wl_vars = {
			"ajaxurl": 		"<?php echo edd_get_ajax_url(); ?>",
			"edd_wl_nonce": "<?php echo wp_create_nonce( 'edd_wl_nonce' ); ?>"
		};
		/* ]]> */

		jQuery(document).ready(function ($) {

			jQuery(document).on( 'listShared', function(e) {

				if( e.url == window.location.href ) {

			    	var postData = {
			            action: 'share_list',
			            list_id: <?php echo get_the_ID(); ?>, 
			            nonce: edd_scripts.ajax_nonce
			        };

			    	$.ajax({
			            type: "POST",
			            data: postData,
			            dataType: "json",
			            url: edd_wl_vars.ajaxurl,
			            success: function ( share_response ) {

		            	}
				        }).fail(function (data) {
				            console.log( data );
				        });
				}
			});
		});
	<?php endif; ?>
	</script>
	<?php
}
add_action( 'wp_footer', 'edd_wl_sharing_print_scripts' );