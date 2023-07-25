<div class="flex-fill">
	<form method="POST">
		<?php wp_nonce_field( 'save_settings', 'req_settings_nonce' ); ?>

		<div class="row ">
			<div class="col-lg-12">
				<!--SMS Notification-->
				<div class="row align-items-center elex-rqust-quote-check-sec ">
					<div class="col-12 ">
						<div class="row mb-3">
							<div class=" col-lg-5 col-md-6 ">
								<div class="d-flex justify-content-between align-items-center">
									<h6 class="mb-0"><?php esc_html_e( 'SMS Notification', 'elex-request-a-quote' ); ?></h6>
									
								</div>
							</div>
							<div class=" col-lg-7 col-md-6 ">
								<div class="d-flex gap-3 align-items-center">
									<label class="elex-switch-btn ">
										<input name="enabled" type="checkbox" id="is_sms_enabled" <?php echo esc_html_e( isset( $settings['enabled'] ) && true === $settings['enabled'] ? 'checked' : '' ); ?> class="elex-rqust-quote-check-sec-input-sms">
										<div class="elex-switch-icon round "></div>
									</label>

									

								</div>
								<div class="text-secondary ">
									<small>
										<?php $link_message = 'here'; ?>

											<?php /* Translators: %s: URL to create Twilio account. */ ?>
			<?php printf( esc_html__( 'Enable to receive SMS notification when the customer makes a quote request. Sign up for a new Twilio account %1$s.', 'elex-request-a-quote' ), '<a  target="_blank" href="https://www.twilio.com/try-twilio">' . esc_html( $link_message ) . '</a>' ); ?>
									</small>
								</div>

							</div>
						</div>


						<div class="elex-rqust-quote-check-content-sms">
							<!-- Twilio SID -->
							<div class="row mb-3 align-items-center">
								<div class=" col-lg-5 col-md-6 ">
									<div class="d-flex justify-content-between align-items-center gap-2">
										<h6 class="mb-0"><?php esc_html_e( 'Twilio SID', 'elex-request-a-quote' ); ?></h6>
										<div type="button" class="" data-bs-toggle="tooltip" data-bs-placement="top" title="Enter Your Twilio SID">
											<svg xmlns="http://www.w3.org/2000/svg " width="26 " height="26 " viewBox="0 0 26 26 ">
												<g id="tt " transform="translate(-384 -226) ">
													<g id="Ellipse_1 " data-name="Ellipse 1 " transform="translate(384 226) " fill="#f5f5f5 " stroke="#000 " stroke-width="1 ">
														<circle cx="13 " cy="13 " r="13 " stroke="none " />
														<circle cx="13 " cy="13 " r="12.5 " fill="none " />
													</g>
													<text id="_ " data-name="? " transform="translate(392 247) " font-size="20 " font-family="Roboto-Bold, Roboto " font-weight="700 ">
														<tspan x="0 " y="0 ">?</tspan>
													</text>
												</g>
											</svg>
										</div>
									</div>
								</div>
								<div class=" col-lg-7 col-md-6 ">
									<div class="row">
										<div class="col-xl-8 col-lg-10 col-md-12">
											<input onchange="" name="twillio_sid" value="<?php echo esc_html_e( isset( $settings['twillio_sid'] ) && ! empty( $settings['twillio_sid'] ) ? $settings['twillio_sid'] : '' ); ?>" type="text" class="form-control">
										</div>
									</div>
								</div>
							</div>

							<!-- Twilio Token -->
							<div class="row mb-3 align-items-center">
								<div class=" col-lg-5 col-md-6 ">
									<div class="d-flex justify-content-between align-items-center gap-2">
										<h6 class="mb-0"><?php esc_html_e( 'Twilio Token', 'elex-request-a-quote' ); ?></h6>
										<div type="button" class="" data-bs-toggle="tooltip" data-bs-placement="top" title="Enter Your Twilio Token">
											<svg xmlns="http://www.w3.org/2000/svg " width="26 " height="26 " viewBox="0 0 26 26 ">
												<g id="tt " transform="translate(-384 -226) ">
													<g id="Ellipse_1 " data-name="Ellipse 1 " transform="translate(384 226) " fill="#f5f5f5 " stroke="#000 " stroke-width="1 ">
														<circle cx="13 " cy="13 " r="13 " stroke="none " />
														<circle cx="13 " cy="13 " r="12.5 " fill="none " />
													</g>
													<text id="_ " data-name="? " transform="translate(392 247) " font-size="20 " font-family="Roboto-Bold, Roboto " font-weight="700 ">
														<tspan x="0 " y="0 ">?</tspan>
													</text>
												</g>
											</svg>
										</div>
									</div>
								</div>
								<div class=" col-lg-7 col-md-6 ">
									<div class="row">
										<div class="col-xl-8 col-lg-10 col-md-12">
											<input onchange="" type="text" name="twillio_token" value="<?php echo esc_html_e( isset( $settings['twillio_token'] ) && ! empty( $settings['twillio_token'] ) ? $settings['twillio_token'] : '' ); ?>" class="form-control">
										</div>
									</div>


								</div>
							</div>


							<!-- Twilio Mobile Number -->
							<div class="row mb-3 align-items-center">
								<div class=" col-lg-5 col-md-6 ">
									<div class="d-flex justify-content-between align-items-center gap-2">
										<h6 class="mb-0"><?php esc_html_e( 'Twilio Mobile Number', 'elex-request-a-quote' ); ?></h6>
										<div type="button" class="" data-bs-toggle="tooltip" data-bs-placement="top" title="Enter the mobile number where you want to get a notification when the customer places a quote request">
											<svg xmlns="http://www.w3.org/2000/svg " width="26 " height="26 " viewBox="0 0 26 26 ">
												<g id="tt " transform="translate(-384 -226) ">
													<g id="Ellipse_1 " data-name="Ellipse 1 " transform="translate(384 226) " fill="#f5f5f5 " stroke="#000 " stroke-width="1 ">
														<circle cx="13 " cy="13 " r="13 " stroke="none " />
														<circle cx="13 " cy="13 " r="12.5 " fill="none " />
													</g>
													<text id="_ " data-name="? " transform="translate(392 247) " font-size="20 " font-family="Roboto-Bold, Roboto " font-weight="700 ">
														<tspan x="0 " y="0 ">?</tspan>
													</text>
												</g>
											</svg>
										</div>
									</div>
								</div>
								<div class=" col-lg-7 col-md-6 ">
									<div class="row">
										<div class="col-xl-8 col-lg-10 col-md-12">
											<input onchange="" type="number" name="twillio_mobile" value="<?php echo esc_html_e( isset( $settings['twillio_mobile'] ) && ! empty( $settings['twillio_mobile'] ) ? $settings['twillio_mobile'] : '' ); ?>" class="form-control">
										</div>
									</div>


								</div>
							</div>


							<!-- Notification Mobile Number -->
							<div class="row mb-3 align-items-center">
								<div class=" col-lg-5 col-md-6 ">
									<div class="d-flex justify-content-between align-items-center gap-2">
										<h6 class="mb-0"><?php esc_html_e( 'Notification Mobile Number', 'elex-request-a-quote' ); ?></h6>
										<div type="button" class="" data-bs-toggle="tooltip" data-bs-placement="top" title="Enter Your Twilio mobile Number">
											<svg xmlns="http://www.w3.org/2000/svg " width="26 " height="26 " viewBox="0 0 26 26 ">
												<g id="tt " transform="translate(-384 -226) ">
													<g id="Ellipse_1 " data-name="Ellipse 1 " transform="translate(384 226) " fill="#f5f5f5 " stroke="#000 " stroke-width="1 ">
														<circle cx="13 " cy="13 " r="13 " stroke="none " />
														<circle cx="13 " cy="13 " r="12.5 " fill="none " />
													</g>
													<text id="_ " data-name="? " transform="translate(392 247) " font-size="20 " font-family="Roboto-Bold, Roboto " font-weight="700 ">
														<tspan x="0 " y="0 ">?</tspan>
													</text>
												</g>
											</svg>
										</div>
									</div>
								</div>
								<div class=" col-lg-7 col-md-6 ">
									<div class="row">
										<div class="col-xl-8 col-lg-10 col-md-12">
											<input onchange="" name="notification_mobile" value="<?php echo esc_html_e( isset( $settings['notification_mobile'] ) && ! empty( $settings['notification_mobile'] ) ? $settings['notification_mobile'] : '' ); ?>" type="number" class="form-control">
										</div>
									</div>


								</div>
							</div>

						</div>

					</div>
				</div>

				<button type="submit" name="submit" class="btn btn-primary"><?php esc_html_e( 'Save Changes', 'elex-request-a-quote' ); ?></button>
			</div>
		</div>
	</form>
</div>
