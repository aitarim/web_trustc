<tr>
	<td>
		<table>
			<tr>
				<td>
					<img src="<?php echo isset( $quote_values['image_url'] ) ? esc_url( $quote_values['image_url'] ) : ''; ?>"
						style="width:50px;height:50px;object-fit: cover;float: left; margin-right: 10px;" alt="">
				</td>
				<td>
					<p style="margin: 5px 0">
						<?php echo esc_html( $quote_values['title'] ); ?>
					</p>
					<p style="margin: 5px 0">
						<?php echo esc_html( ! empty( $product->get_sku() ) ? 'SKU: ' . $product->get_sku() : '-' ); ?>
					</p>
				</td>
			</tr>
		</table>
	</td>
	<td>
		<?php echo esc_html( $product->get_price() ); ?>
	</td>
	<td>
		<?php echo esc_html( $item->get_quantity() ); ?>
	</td>
	<td>
		<?php echo esc_html( $item->get_subtotal() . ' ' . get_woocommerce_currency() ); ?>
	</td>
</tr>
