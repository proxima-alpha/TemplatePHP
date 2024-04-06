<div class="container-inner">
    <div class="container-wrap">
        <div class="project-wrap">
            <div class="form-wrap project">
                <div class="input-wrap">
                    <p class="input-title"><?= lang('제목') ?></p>
                    <input type="text" name="title" class="editable under-line" value="<?= $data['title'] ?>" readonly/>
                </div>
                <div class="input-wrap">
                    <p class="input-title"><?= lang('내용') ?></p>
                    <textarea class="editable" name="content" onkeydown="resizeInputPopupTextarea(this)"
                              onkeyup="resizeInputPopupTextarea(this)" readonly><?= $data['content'] ?></textarea>
                </div>
                <?php if (isset($data['project_image_id'])) { ?>
                    <div class="line"></div>
                    <div class="input-wrap">
                        <p class="input-title"><?= lang('타이틀 이미지') ?></p>
                        <?= \App\Helpers\HtmlHelper::getImageUploader('project', $data['project_image_id'] ?? null, 'view') ?>
                    </div>
                <?php } ?>
                <div class="line"></div>
                <div class="input-wrap calendar">
                    <p class="input-title"><?= lang('시작일') ?></p>
                    <input class="editable" name="start_date" value="<?= $data['start_date'] ?>" readonly>
                </div>
                <div class="input-wrap calendar">
                    <p class="input-title"><?= lang('마감일') ?></p>
                    <input class="editable" name="end_date" value="<?= $data['end_date'] ?>" readonly>
                </div>
            </div>
            <?php if (isset($data['artists']) || isset($data['rewards'])) { ?>
                <div class="form-wrap extra">
                    <?php if (isset($data['artists'])) { ?>
                        <div class="line black"></div>
                        <div class="input-wrap artist">
                            <p class="input-title"><?= lang('아티스트') ?></p>
                            <?= \App\Helpers\HtmlHelper::getRowUploaderArtist('artist_id', $data['artists'], 'view') ?>
                        </div>
                    <?php }
                    if (isset($data['rewards'])) { ?>
                        <div class="line black"></div>
                        <div class="input-wrap reward">
                            <p class="input-title"><?= lang('가격 및 리워드') ?></p>
                            <?= \App\Helpers\HtmlHelper::getRowUploaderReward('reward', $data['rewards'], 'view') ?>
                        </div>
                    <?php } ?>
                    <div class="line black"></div>
                </div>
            <?php }
            if ($is_login && ($is_admin || $user_id == $data['user_id'])) { ?>
                <div class="control-button-wrap">
                    <a href="<?= $is_admin_page ? '/admin/project/' . $data['id'] . '/edit' : '/project/' . $data['id'] . '/edit' ?>"
                       class="button under-line edit">
                        <img src="/asset/images/icon/edit.png"/>
                        <span><?= lang('Service.edit') ?></span>
                    </a>
                    <a href="javascript:openPopupDelete('/api/project/delete/<?= $data['id'] ?>')"
                       class="button under-line delete">
                        <img src="/asset/images/icon/delete.png"/>
                        <span><?= lang('Service.delete') ?></span>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
