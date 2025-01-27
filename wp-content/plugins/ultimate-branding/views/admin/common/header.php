<div class="sui-header">
	<h1 class="sui-header-title"><?php echo esc_html( $title ); ?></h1>
	<div class="sui-actions-right">
<?php if ( $show_manage_all_modules_button ) { ?>
		<button class="sui-button" type="button" data-a11y-dialog-show="branda-manage-all-modules"><?php echo esc_html_x( 'Manage All Modules', 'button', 'ub' ); ?></button>
<?php } ?>
<?php if ( $documentation_chapter && ! empty( $helps ) ): ?>
		<a target="_blank" class="sui-button sui-button-ghost"
		   href="https://premium.wpmudev.org/docs/wpmu-dev-plugins/branda/#<?php echo esc_attr( $documentation_chapter ); ?>">
			<i class="sui-icon-academy"></i>
			<?php esc_html_e( 'View Documentation', 'ub' ); ?>
		</a>
<?php endif; ?>
	</div>
</div>
