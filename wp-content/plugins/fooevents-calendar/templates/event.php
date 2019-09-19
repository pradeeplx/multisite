<?php if(!empty($thumbnail)) :?>
<img src="<?php echo $thumbnail; ?>" />
<?php endif; ?>
<a href="<?php $permalink = get_the_permalink($event->ID); echo $permalink; ?>"><h3><?php echo $event->post_title; ?></h3></a>
<?php if(!empty($event->post_excerpt)) : ?>
<?php echo $event->post_excerpt; ?>
<?php endif; ?>
<p><a class="button" href="<?php echo $permalink; ?>" rel="nofollow"><?php echo $ticketTerm; ?></a></p>