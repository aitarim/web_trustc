
<div class="elex-rqst-quote-front-wrap" style="width:100%">
<div class="d-flex justify-content-center gap-2 add_view_quote_btn ">
	<div class="w-100">
		<button data-product-type =<?php echo esc_html( $product_data['type'] ); ?> <?php echo  esc_html( ( 'variable' === $product_data['type'] || 'composite' === $product_data['type'] ) ? 'disabled' : '' ); ?> data-product-id="<?php echo  esc_html( $product_data['id'] ); ?>" data-child-ids=<?php echo  isset( $product_data['child_product_ids'] ) ? esc_html( $product_data['child_product_ids'] ) : 0; ?>  id="<?php echo esc_html( $product_data['id'] ); ?>" style="<?php echo  esc_html( $product_data['button_color'] ); ?>" class="button wp-element-button my-2 text-white btn-sm btn-primary  rounded-2 add_to_quote  ">
		<?php echo esc_html( $product_data['button_label'], 'elex-request-a-quote' ); ?>
	</button>
		<div class="elex-raq-toast-container position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 11">
			<div id="elex-raq-add-sucess-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">

				<div class="toast-body text-center d-flex justify-content-between align-items-center">
					<h6 class="my-2">
						<svg xmlns="http://www.w3.org/2000/svg" width="22.415" height="22.026" viewBox="0 0 22.415 22.026">
							<g id="Icon_feather-check-circle" data-name="Icon feather-check-circle" transform="translate(-1.998 -1.979)">
								<path id="Path_646" data-name="Path 646" d="M23,12.076V13a10,10,0,1,1-5.93-9.139" fill="none" stroke="#28a745" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
								<path id="Path_647" data-name="Path 647" d="M26.5,6l-10,10.009-3-3" transform="translate(-3.5 -1.003)" fill="none" stroke="#28a745" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
							</g>
						</svg>
						<span class="ms-2"><?php echo  isset( $product_data['success_message'] ) ?  esc_html_e( $product_data['success_message'] , 'elex-request-a-quote' ) : ''; ?></span>
					</h6>
					<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
