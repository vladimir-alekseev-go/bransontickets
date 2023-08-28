<?php if ($items) {?>
<div class="scrollbar-inner">
<div class="compare-list show-list show-list-list">
    <div class="container-list">
	<?php foreach ($items as $model) {echo $this->render('@app/views/components/list-item', [
        'model' => $model,
        'priceAll' => $priceAll,
        'Search' => $Search,
    ]); }?>

</div>
</div>
</div>
<?php }?>
<?php $this->registerJs("try{ $('.scrollbar-inner').scrollbar();} catch(e) {}");?>
