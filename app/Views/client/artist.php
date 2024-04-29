<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= $lang == 'ko' ? $code['name'] : $code['name_en'] ?>
        </h3>
        <div class="content-wrap">
            <?php if (\App\Helpers\HtmlHelper::showDataEmpty($array ?? null, 340)) {
                foreach ($array as $index => $item) {
                    $url = isset($item['profile_id']) ? '/file/' . $item['profile_id'] : '/asset/images/custom/object.svg';
                    ?>
                    <div class="content-item">
                        <div class="image-item-wrap">
                            <div class="image-item"
                                 style="background: url('<?= $url ?>') no-repeat center; background-size: cover; font-size: 0;"></div>
                        </div>
                        <div class="text-item-wrap">
                            <p class="item-title"><?= $lang == 'ko' ? $item['name'] : $item['name_en'] ?></p>
                            <p class="item-content"><?= $lang == 'ko' ? $item['job'] : $item['job_en'] ?></p>
                        </div>
                    </div>
                <?php }
            } ?>
        </div>
    </div>
</div>
