<!-- navbar links -->
<div class="elex-rqst-quote-main-links overflow-auto">
	<?php
	$message = ( 'widget' === $active_tab ) ? __('Customize Quote List & Form', 'elex-request-a-quote') : __('Settings', 'elex-request-a-quote');
	$pageurl = add_query_arg(
		array(
			'page' => 'widget' === $active_tab ? 'listpage' : 'settings',
			'tab' => 'widget' === $active_tab ? 'listpage' : 'general'
		),
		admin_url('admin.php')
	);
	if ('widget' === $active_tab) { ?>
		<div class="mb-3 p-2  elex-raq-note">
			<?php /* Translators: %s:  ELEX Request a Quote "Quote List" tab link URL */ ?>
			<?php printf(esc_html__('To customize your website\'s quote request form and quote items listing page, visit  %1$s section.', 'elex-request-a-quote'), '<a href=' . esc_url( $pageurl ) . '>' . esc_html($message) . '</a>'); ?>
		</div>
	<?php } if ('listpage' === $active_tab) { ?>
		<div class="mb-3 p-2  elex-raq-note">
			<?php /* Translators: %s: ELEX Request a Quote "Settings" tab link */ ?>
			<?php printf(esc_html__('To set up request a quote on your site, visit  %1$s section.', 'elex-request-a-quote'), '<a href=' . esc_url( $pageurl ) . '>' . esc_html($message) . '</a>'); ?>
		</div>
	<?php } ?>
	<ul class="nav nav-pills border-bottom border-primary gap-2 flex-nowrap ">

		<?php
		foreach ($sub_tabs as $tab_name) :

			$url = add_query_arg(
				array(
					'page' => $plugin_page,
					'tab' => $tab_name['slug'],
				),
				admin_url('admin.php')
			);

			?>


			<li class=" <?php esc_html_e( ( 'go_premium' === $tab_name['slug'] ) ? 'go_premium' : '' ); ?> mb-0  nav-item rounded-0 rounded-top">
				<a href="<?php echo esc_url($url); ?>" 
				   class="nav-link 
					<?php echo $active_tab === $tab_name['slug'] ? 'active' : ''; ?>
					<?php esc_html_e( ( 'go_premium' === $tab_name['slug'] ) ? 'elex_raq_basic' : '' ); ?>
				  rounded-0  rounded-top text-nowrap">
					<?php echo esc_html_e($tab_name['title']); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
