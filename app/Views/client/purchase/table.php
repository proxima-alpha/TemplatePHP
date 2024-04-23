<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= lang('Client.purchased_item_list') ?>
        </h3>
        <div class="reward-box">
            <?php if (\App\Helpers\HtmlHelper::showDataEmpty($array)) { ?>
                <ul>
                    <?php foreach ($array as $index => $item) { ?>
                        <li class="reward-wrap">
                            <div class="image-wrap"
                                 style="background: url('/file/<?= $item['project_image_id'] ?> ?>') no-repeat center; background-size: cover; font-size: 0;">
                            </div>
                            <div class="content-wrap">
                                <p class="project-title"><?= $item['project_title'] ?></p>
                                <p class="title"><?= $item['title'] ?></p>
                                <p class="price"><?= $item['price'] ?> KRW</p>
                            </div>
                            <div class="line"></div>
                            <div class="button-wrap">
                                <a class="button button-fill <?= $item['status'] == 'confirmed' ? '' : 'disabled' ?>"
                                   onclick="onClickDownload()}"><?= lang('Client.status_' . $item['status']) ?>
                                </a>
                                <a class="button button-line"
                                   href="/purchase/<?= $item['id'] ?>/view"><?= lang('Client.show_detail') ?> </a>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>

        <?= \App\Helpers\HtmlHelper::getPagination($pagination, $pagination_link); ?>
    </div>
</div>
<script type="text/javascript">
    function onClickDownload() {
        //todo link
    }
</script>
