<?php

use App\Helpers\HtmlHelper;

?>
<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= $lang == 'ko' ? $code['name'] : $code['name_en'] ?>
        </h3>
        <div class="content-wrap">
            <?php if (HtmlHelper::showDataEmpty($array ?? null, 340)) { ?>
                <?= HtmlHelper::getProjectContent($array, $lang); ?>
            <?php } ?>
        </div>
    </div>
</div>
