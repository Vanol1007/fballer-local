<?php
// Защита от прямого доступа к файлу
if ( ! defined('ABSPATH') ) {
	exit;
}
?>

<?php if ( is_active_sidebar('sidebar') ) { ?>
	<?php // dynamic_sidebar('sidebar'); ?>
<?php } ?>