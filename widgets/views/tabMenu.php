<?php
use \yii\helpers\Html;
?>

<div class="tab-menu">
      
<ul class="nav nav-tabs">
    <?php foreach ($this->context->getItems() as $item) {?>
        <li <?php echo Html::renderTagAttributes($item['htmlOptions'])?>>
        <?php echo Html::a($item['label'], $item['url']); ?>
    </li>
    <?php }; ?>
</ul>
</div>