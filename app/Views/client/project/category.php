<?php

use App\Helpers\HtmlHelper;

?>
<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= $lang == 'ko' ? $code['name'] : $code['name_en'] ?>
        </h3>
        <div class="content-wrap">
            <?php if (HtmlHelper::showDataEmpty($array ?? null, 340)) {
                foreach ($array as $index => $item) {
                    $url = isset($item['image_id']) ? '/file/' . $item['image_id'] : '/asset/images/custom/object.svg'; ?>
                    <div class="content-item">
                        <a href="/project/<?= $item['id'] ?>/view">
                            <div class="image-item-wrap">
                                <div class="image-item"
                                     style="background: url(' <?= $url ?> ') no-repeat center; background-size: cover; font-size: 0;"></div>
                            </div>
                            <div class="text-item-wrap">
                                <p class="item-title"><?= $lang == 'ko' ? $item['title'] :
                                        $item['title_en'] ?></p>
                                <p class="item-date"><?= (HtmlHelper::toDateString($item['start_date'])
                                        . ' ~'
                                        . HtmlHelper::toDateString($item['end_date'])) ?></p>
                                <p class="item-content"><?= $lang == 'ko' ? $item['content'] :
                                        $item['content_en'] ?></p>
                            </div>
                        </a>
                    </div>
                <?php }
            } ?>
        </div>
    </div>
</div>
