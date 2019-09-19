<?php if (!empty($args['val']['icon'])): ?>
    <span class="ti <?php echo $args['val']['icon']; ?>"></span>
<?php endif; ?>
<?php
$args['val'] = empty($args['val']) ? array() : $args['val'];
$format =Tbp_Utils::getDateFormat($args['val']);
$isDate=$args['type']!=='time';
$args=null;
$date = $isDate===true ?get_the_date($format): get_the_time($format);
$time = get_the_time('c');
?>
<time itemprop="datePublished" content="<?php echo $time?>" class="entry-date updated" datetime="<?php echo $time; ?>"><?php echo $date; ?>
    <?php if ($isDate===true): ?>
    <meta itemprop="dateModified" content="<?php echo get_the_modified_time('c') ?>">
    <?php endif; ?>
</time>
