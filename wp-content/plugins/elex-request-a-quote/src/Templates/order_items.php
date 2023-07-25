<tr>
	<td style="width: 100%; padding-bottom: 10px;">
		<table style="width: 100%;text-align: start; border-collapse: collapse;;border-bottom: 1px solid #c1c1c1;">
			<thead>
				<th style="text-align: start; width: calc(100% - 300px);border-bottom: 1px solid #c1c1c1">
					<?php esc_html_e( 'Products', 'elex-request-a-quote' ); ?>
				</th>
				<th style="text-align: start; width: 100px;border-bottom: 1px solid #c1c1c1">
					<?php esc_html_e( 'Price', 'elex-request-a-quote' ); ?>
				</th>
				<th style="text-align: start; width: 100px; border-bottom: 1px solid #c1c1c1">
					<?php esc_html_e( 'Qty.', 'elex-request-a-quote' ); ?>
				</th>
				<th style="text-align: start; width: 100px;border-bottom: 1px solid #c1c1c1">
					<?php esc_html_e( 'Subtotal', 'elex-request-a-quote' ); ?>
				</th>
			</thead>
			<tbody>
				<?php

				foreach ( $order->get_items() as $item ) {
					$quote_data = $order->get_meta( 'elex_quote_data' );
					$image_url  = wp_get_attachment_image_src( get_post_thumbnail_id( $item->get_product_id() ), 'single-post-thumbnail' );

					$product = wc_get_product( $item->get_product_id() );

					if ( 'composite' === $product->get_type() ) {

						foreach ( $quote_data['items'] as $data ) {
							if ( 'composite' !== $data['type'] ) {
								continue;
							}

							?>
							<tr>
								<td>
									<table>
										<tr>
											<td>
												<img src="<?php echo isset( $data['image_url'] ) ? esc_url( $data['image_url'] ) : ''; ?>"
													style="width:50px;height:50px;object-fit: cover;float: left; margin-right: 10px;"
													alt="">
											</td>
											<td>
												<p style="margin: 5px 0">
													<?php echo esc_html( $data['title'] ); ?>
												</p>
												<p style="margin: 5px 0">
													<?php echo esc_html( ! empty( $product->get_sku() ) ? 'SKU: ' . $product->get_sku() : '-' ); ?>
												</p>
											</td>
										</tr>
									</table>
								</td>
								<td>
									<?php echo esc_html( false === $data['child'] ? $product->get_price() : '' ); ?>
								</td>
								<td>
									<?php echo esc_html( $data['quantity'] ); ?>
								</td>
								<td>
									<?php echo esc_html( false === $data['child'] ? $item->get_subtotal() . ' ' . get_woocommerce_currency() : '' ); ?>
								</td>
							</tr>

						<?php 
						}
					} elseif ( 'composite' !== $product->get_type() ) {

						if ( 'simple' === $product->get_type() ) {

							foreach ( $quote_data['items'] as $quote_values ) {

								if ( ! empty( $item->get_subtotal() ) && 'simple' === $quote_values['type'] && $product->get_id() == $quote_values['product_id'] && 'simple' === $product->get_type() ) {
									ob_start();
									require SRC_PATH . 'simpleProductContent.php';
									$content = ob_get_clean();
									echo wp_kses_post( $content );
								}
							}                       
						} elseif ( 'variable' == $product->get_type() ) {

							$variation_id = $item->get_variation_id();

							$product_variation = new WC_Product_Variation( $variation_id );

							foreach ( $quote_data['items'] as $quote_values ) {
								if ( 'variable' === $product->get_type() && ! empty( $item->get_subtotal() ) && 'variable' === $quote_values['type'] && $variation_id == $quote_values['variation_id'] ) { 

									ob_start();
									require SRC_PATH . 'variableProductContent.php';
									$content = ob_get_clean();
									echo wp_kses_post( $content );
								}
							}                       
						}                   
					}
				}

				?>

			</tbody>
		</table>
	</td>
</tr>
<tr>
	<td style="padding: 5px 0;">
		<table style="margin-left: auto;">
			<tr>
				<td style="width: 100px;">
					<?php esc_html_e( 'Subtotal', 'elex-request-a-quote' ); ?>
				</td>
				<td style="width: 100px;">
					<?php echo esc_html( $order->get_subtotal() . ' ' . get_woocommerce_currency() ); ?>
				</td>
			</tr>
			<tr>
				<td style="width: 100px;">
					<?php esc_html_e( 'Fees', 'elex-request-a-quote' ); ?>
				</td>

				<td style="width: 100px;">
					<?php echo esc_html( ! empty( $order->get_total_fees() ) ? $order->get_total_fees() . ' ' . get_woocommerce_currency() : '-' ); ?>
				</td>
			</tr>
			<tr>
				<td style="width: 100px;">
					<?php esc_html_e( 'Discount', 'elex-request-a-quote' ); ?>
				</td>

				<td style="width: 100px;">
					<?php echo esc_html( ! empty( $order->get_discount_total() ) ? $order->get_discount_total() . ' ' . get_woocommerce_currency() : '-' ); ?>
				</td>
			</tr>
			<tr>
				<td style="width: 100px;">
					<?php esc_html_e( 'Tax', 'elex-request-a-quote' ); ?>
				</td>

				<td style="width: 100px;">
					<?php echo esc_html( ! empty( $order->get_total_tax() ) ? $order->get_total_tax() . ' ' . get_woocommerce_currency() : '-' ); ?>
				</td>
			</tr>
			<tr>
				<td style="width: 100px;color: #10518D;">
					<?php esc_html_e( 'Total', 'elex-request-a-quote' ); ?>
				</td>
				<td style="width: 100px;color: #10518D;">
					<?php echo esc_html( $order->get_total() . ' ' . get_woocommerce_currency() ); ?>
				</td>
			</tr>
		</table>
		
	</td>
</tr>
<?php if ( ( true === $terms_enabled ) && ! empty( $terms ) ) { ?>
	<tr>
		<td>
			<h6 style="font-size: 16px; margin: 10px 0; font-weight: 600;">
				<?php esc_html_e( 'Terms & Conditions', 'elex-request-a-quote' ); ?>

			</h6>
			<p style="font-size: 16px; margin: 10px 0;font-weight: 500;">
				<?php esc_html_e( $terms ); ?>
			</p>
		</td>
	</tr>
<?php } ?>
