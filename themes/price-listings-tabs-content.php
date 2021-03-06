<?php
$current_term = get_term( (int) $this->current_term, $current_tab );
$download_parents = $this->get_terms();
$url = add_query_arg( array( 'term_parent' => 'new' ), admin_url( 'edit.php?' . $_SERVER['QUERY_STRING'] ) );
$current_url = false;

// Settings.
$settings = sell_media_get_plugin_options();
$default_price = isset( $settings->default_price ) ? $settings->default_price : 1;
$hide_original_price = isset( $settings->hide_original_price ) ? $settings->hide_original_price : 'no';
$default_price_group = isset( $settings->default_price_group ) ? $settings->default_price_group : 0;
?>
<div class="sell-media-pricing">	
	<div class="tab-price-lists">
		<label><?php esc_html_e( 'Select a pricelilst to edit' ) ?></label>
		<select>
			<?php
			$url = admin_url( 'edit.php?' . $_SERVER['QUERY_STRING'] );
			$url = remove_query_arg( 'term_parent' );
			?>
			<option value="<?php echo esc_attr( $url ) ?>"><?php esc_html_e( 'Select' ) ?></option>
			<?php
			$current_pricelist = isset( $_GET['term_parent'] ) ? $_GET['term_parent'] : '';
			foreach ( $download_parents as $slug => $term ) {
				$url = add_query_arg( array( 'term_parent' => $term->term_id ), $url );
				if ( $this->current_term === $term->term_id ) {
					$current_url = $url;
				}
				// echo "<option value='$url' " . selected( (int) $this->current_term, $term->term_id, false ) . ">" . $term->name . '</option>';
				echo "<option value='$url' " . selected( (int) $current_pricelist, $term->term_id, false ) . ">" . $term->name . '</option>';
			}
			?>
		</select>
		<input type="hidden" value="<?php echo !empty( $current_term ) && isset( $current_term->term_id ) ? $current_term->term_id: 'new'; ?>" name="term_id" />
		<input type="hidden" value="" name="deleted_term_ids" />
		<?php
		$delete_url = add_query_arg( array( 'delete' => true ), $current_url );
		$delete_url = wp_nonce_url( $delete_url, 'delete_pricelist_nonce_action', 'delete_pricelist_nonce_name' );
		$current_term_name = isset( $current_term->name ) ? $current_term->name : '';
		if ( isset( $_GET['term_parent'] ) ) {
			?><a href="#" data-href="<?php echo esc_url( $delete_url ); ?>" class="deletion" title="<?php esc_html_e( 'Delete pricelist.', 'sell_media' ); ?>" data-message="<?php printf( __( 'Are you sure you want to delete the pricelist: %s', 'sell_media' ), $current_term_name ); ?>"><?php esc_html_e( 'Delete', 'sell_media' ); ?></a>
		<?php } ?>
	</div>
	<?php
	// if ( ! isset( $_GET['term_parent'] ) || 'new' !== $_GET['term_parent'] ) {
	if ( isset( $_GET['term_parent'] ) ) {
	?><hr/>
	<!-- Price table -->
	<table class="form-table tax-<?php echo esc_attr( $this->taxonomy ); ?>" id="sell-media-price-table">
		<thead>
			<tr>
				<th style="width:15%"><?php _e( 'Name', 'sell_media' ); ?></th>
				<th style="width:15%"><?php _e( 'Description', 'sell_media' ); ?></th>
				<th style="width:10%"><?php _e( 'Width', 'sell_media' ); ?></th>
				<th style="width:10%"><?php _e( 'Height', 'sell_media' ); ?></th>
				<th style="width:10%"><?php _e( 'Price', 'sell_media' ); ?></th>
				<th style="width:1%"></th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	<p class="submit sell-media-pricelisting-form-buttons" style="clear: both;">
		<input type="hidden" name="sell-media-price-list-submit" value="true" />
		<?php
		$attributes = array( 'id' => 'sell-media-add-button' );
		submit_button( __( '+ Add Option', 'sell_media' ), 'secondary', 'submit', false, $attributes  ) ?>
		<!-- <a href="javascript:void(0);" id="sell-media-add-button" class="button-secondary"><?php _e( '+ Add Option', 'sell_media' ); ?></a> -->
	</p>
	<?php
	} ?>
	<div style="clear:both"></div>
</div>
<?php
if ( 'price-group' === $current_tab ) { ?>
	<div class="sell-media-pricing-settings" >
		<h2 class="tab-title">
			<span><?php esc_html_e( 'Download settings', 'sell_media' ); ?></span>						
		</h2>
		<div class="form-group">
			<label><?php esc_html_e( 'High Resolution File Price', 'sell_media' ); ?></label>
			<input type="text" name="settings[default_price]" value="<?php echo esc_attr( $default_price ) ?>">
			<span class="option-description"><?php esc_html_e( 'The original price of new items and bulk uploads. You can set unique prices by editing each individual item.', 'sell_media' ) ?></span>
		</div>
		<div class="form-group">
			<label><?php esc_html_e( 'High Resolution File Availability', 'sell_media' ); ?></label>
			<select name="settings[hide_original_price]">
				<option value="<?php esc_attr_e( 'yes' ); ?>" <?php selected( 'yes', $hide_original_price ) ?>><?php esc_html_e( 'Can be purchased' ); ?></option>
				<option value="<?php esc_attr_e( 'no' ); ?>" <?php selected( 'no', $hide_original_price ) ?>><?php esc_html_e( 'Cannot be purchased' ); ?></option>
			</select>
			<span class="option-description"><?php esc_html_e( 'Select "Can be purchased" if you want to allow buyers to purchase the original high resolution file. Select "Cannot be purchased" if you only want buyers to purchase lower resolution copies (determined by the sizes in your pricelists below).', 'sell_media' ) ?></span>
		</div>
		<div class="form-group">
			<label><?php esc_html_e( 'Default Pricelist', 'sell_media' ); ?></label>
			<select name="settings[default_price_group]">
				<?php
				$price_group = sell_media_settings_price_group('price-group');
				if ( is_array( $price_group ) && count( $price_group ) > 0 ) {
					foreach( $price_group as $group ) {
						?><option value="<?php echo esc_attr( $group['name'] ); ?>" <?php selected( $group['name'], $default_price_group ) ?>><?php echo esc_attr( $group['title'] ); ?></option>
					<?php }
				}?>								
			</select>
			<span class="option-description"><?php esc_html_e( 'This is the default pricelist that will be assigned to all newly uploaded images for sale. You can override this setting on a per-item basis.', 'sell_media' ) ?></span>
		</div>
	</div>
	<hr>
<?php } ?>
<p class="submit sell-media-priclist-submit">
	<input type="submit" name="Submit" id="sell-media-save-button"  class="button-primary" value="<?php _e( 'Save Pricelist', 'sell_media' ); ?>" />
</p>
