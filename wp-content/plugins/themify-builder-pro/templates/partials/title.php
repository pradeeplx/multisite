<?php
$tag = $args['html_tag'];
$hasLink=$args['link']!=='none';
if($hasLink===true){
    $link_attr=Tbp_Utils::getLinkParams($args,get_permalink());
    if(isset($link_attr['class'])){
	$link_attr['class'].=' tbp_link';
    }
    else{
	$link_attr['class']='tbp_link';
    }
}
unset($args);
if($hasLink===true && !isset($link_attr['href'])){
    $hasLink=false;
}
?>
<<?php echo $tag?> itemprop="headline" class="tbp_title">
    <?php if($hasLink===true):?>
	<a <?php echo self::get_element_attributes($link_attr); ?>>
    <?php endif;?>
    <?php the_title();?>
    <?php if($hasLink===true):?>
	</a>
    <?php endif;?>
</<?php echo $tag?>>