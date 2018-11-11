<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WCfM Markeplace Views Store new review form
 *
 * For edit coping this to yourtheme/wcfm/reviews 
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $WCFM, $WCFMmp;

if( !$total_review_count ) return;
if( empty( $latest_reviews ) ) return;

?>

<?php foreach( $latest_reviews as $latest_review ) { ?>
	<div class="review_section">
		<div class="lft user_photo">
			<div class="review_photo">
			  <?php
				$wp_user_avatar_id = get_user_meta( $latest_review->author_id, 'wp_user_avatar', true );
				$wp_user_avatar = wp_get_attachment_url( $wp_user_avatar_id );
				if ( !$wp_user_avatar ) {
					$wp_user_avatar = $WCFM->plugin_url . 'assets/images/avatar.png';
				}
				?>
			  <img src="<?php echo $wp_user_avatar; ?>" alt="Review">
			</div>
			<div class="rated">
				<strong><?php _e('rated', 'wc-multivendor-marketplace' ); ?></strong>
				<div class="user_rated"><?php echo round( $latest_review->review_rating, 1 ); ?></div>
			</div>
		</div>
		<div class="rgt user_review_sec">
			<div class="user_name"><?php echo $latest_review->author_name; ?></div>
			<div class="user_review_area">
				<span><?php echo $WCFMmp->wcfmmp_reviews->get_author_reviews_count($latest_review->author_id); ?> <?php _e( 'reviews', 'wc-multivendor-marketplace' ); ?></span> <span class="user_date"><?php echo date( wc_date_format(), strtotime($latest_review->created) ) ; ?></span>
			</div>
			<div class="user_review_text"><p><?php echo $latest_review->review_description; ?></p></div>
			<?php if( apply_filters( 'wcfm_is_allow_review_reply', false ) ) { ?>
			  <div class="reply_bt"><button><?php _e('Reply', 'wc-multivendor-marketplace' ); ?></button></div>
			<?php } ?>
		</div>
		<div class="spacer"></div>    
	</div>
<?php } ?>