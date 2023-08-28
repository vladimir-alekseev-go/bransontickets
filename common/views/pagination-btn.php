<?php if (!empty($pagination)) {
	$p = $pagination->getPage();
	$pc = $pagination->getPageCount();
	if ($p+1 < $pc) {
	    $url = $pagination->createUrl($pagination->getPage()+1);
	    ?>
	<div class="view-more-block"><a class="btn btn-green btn-big ajax" data-container="#show-list" data-url="<?= $url?>" rel="next" href="<?= $url?>"></a></div>
<?php }}?>
