<div class="container-inner">
    <div class="container-wrap">
        <div class="project-wrap">
            <div class="form-wrap project">
                <div class="tab-box">
                    <div class="tab-button-wrap">
                        <a class="button ko active" onclick="clickTab(this,'ko')">한국어</a>
                        <a class="button en" onclick="clickTab(this,'en')">English</a>
                    </div>
                    <div class="tab-wrap ko active">
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.title') ?></p>
                            <input type="text" name="title" class="editable under-line" value="<?= $data['title'] ?>"
                                   readonly/>
                        </div>
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.content') ?></p>
                            <textarea class="editable" name="content" onkeydown="resizeInputPopupTextarea(this)"
                                      onkeyup="resizeInputPopupTextarea(this)"
                                      readonly><?= $data['content'] ?></textarea>
                        </div>
                    </div>
                    <div class="tab-wrap en">
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.title') ?></p>
                            <input type="text" name="title_en" class="editable under-line" value="<?= $data['title_en'] ?>"
                                   readonly/>
                        </div>
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.content') ?></p>
                            <textarea class="editable" name="content_en" onkeydown="resizeInputPopupTextarea(this)"
                                      onkeyup="resizeInputPopupTextarea(this)"
                                      readonly><?= $data['content_en'] ?></textarea>
                        </div>
                    </div>
                </div>
                <?php if (isset($data['project_image_id'])) { ?>
                    <div class="line"></div>
                    <div class="input-wrap">
                        <p class="input-title"><?= lang('Service.project_image') ?></p>
                        <?= \App\Helpers\HtmlHelper::getImageUploader('project', $data['project_image_id'] ?? null, 'view') ?>
                    </div>
                <?php } ?>
                <div class="line"></div>
                <div class="input-wrap calendar">
                    <p class="input-title"><?= lang('Service.start_date') ?></p>
                    <input class="editable" name="start_date"
                           value="<?= \App\Helpers\HtmlHelper::toDateString($data['start_date']) ?>" readonly>
                </div>
                <div class="input-wrap calendar">
                    <p class="input-title"><?= lang('Service.end_date') ?></p>
                    <input class="editable" name="end_date"
                           value="<?= \App\Helpers\HtmlHelper::toDateString($data['end_date']) ?>" readonly>
                </div>
                <div class="line"></div>
                <div class="input-wrap inline status">
                    <p class="input-title"><?= lang('service.status') ?></p>
                    <select class="editable" name="status" disabled>`
                        <option
                            value="open" <?= isset($data['status']) && $data['status'] == 'open' ? 'selected' : '' ?>><?= lang('Service.opened') ?></option>
                        <option
                            value="close" <?= isset($data['status']) && $data['status'] == 'close' ? 'selected' : '' ?>><?= lang('Service.closed') ?></option>
                    </select>
                </div>
            </div>
            <?php if (isset($data['artists']) || isset($data['rewards'])) { ?>
                <div class="form-wrap extra">
                    <?php if (isset($data['artists'])) { ?>
                        <div class="line black"></div>
                        <div class="input-wrap artist">
                            <p class="input-title"><?= lang('Service.artist') ?></p>
                            <?= \App\Helpers\HtmlHelper::getArtistRow('artist_id', $data['artists'], $lang, 'view') ?>
                        </div>
                    <?php }
                    if (isset($data['rewards'])) { ?>
                        <div class="line black"></div>
                        <div class="input-wrap reward">
                            <p class="input-title"><?= lang('Service.price_reward') ?></p>
                            <?= \App\Helpers\HtmlHelper::getRewardRow('reward', $data['rewards'], 'view') ?>
                        </div>
                    <?php } ?>
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
