<?php

use Crisu83\ShortId\ShortId;

if ($type == 'create') {
    $data['title'] = '';
    $data['content'] = '';
}
$shortid = ShortId::create();
$identifier = $shortid->generate();
?>
<script type="text/javascript">
    default_identifier = '<?=$identifier?>';
    <?php if (isset($data['files'])) {
    foreach ($data['files'] as $index => $item) { ?>
    files.push('topic', '<?=$item['id']?>');
    <?php }
    }?>
</script>
<div class="container-inner topic-input-container">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= $board['alias'] ?>
        </h3>
        <div class="topic-wrap">
            <div class="form-wrap">
                <div class="row row-title line-after black">
                    <input placeholder="<?= lang('Service.title') ?>"
                           type="text" name="title"
                           class="column title editable"
                           value="<?= $data['title'] ?>"/>
                </div>
                <div class="text-wrap line-after">
                    <textarea placeholder="<?= lang('Service.content') ?>"
                              name="content"
                              class="content editable"><?= $data['content'] ?></textarea>
                </div>
                <input hidden type="text" name="board_id" class="editable" value="<?= $board['id'] ?>"/>
                <input hidden type="text" name="identifier" class="editable" value="<?= $identifier ?>"/>
                <input hidden type="text" name="user_id" class="editable" value="<?= $user_id ?>">
            </div>
            <div class="slider-box">
                <div class="slider-wrap">
                    <div class="slick uploader topic">
                        <?php if (isset($data['files'])) {
                            foreach ($data['files'] as $index => $file) { ?>
                                <div class="slick-item draggable-item upload-item" draggable="true"
                                     style="background: url('/file/<?= $file['id'] ?>') no-repeat center; background-size: cover; font-size: 0;">
                                    Slider #<?= $file['id'] ?>
                                    <input hidden type="text" name="id" value="<?= $file['id'] ?>">
                                    <div class="upload-item-hover">
                                        <a href="javascript:deleteUploadedSlickFile('<?= $file['id'] ?>')"
                                           class="button delete-image black">
                                            <img src="/asset/images/icon/cancel_white.png"/>
                                        </a>
                                    </div>
                                </div>
                            <?php }
                        } ?>
                        <div class="slick-item upload-item-add"
                             style="background: url('/asset/images/icon/plus_circle_big.png') no-repeat center; font-size: 0;">
                            <label for="file" class="button"></label>
                            <input type="file" name="file" multiple id="file"
                                   onchange="onFileUpload(this);"
                                   accept="video/*,image/png,image/jpg"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="info-text-wrap">
                <?= lang('Service.message_info_drag') ?>
            </div>
            <div class="button-wrap">
                <a href="<?= $type == 'create' ? 'javascript:confirmCreateTopic()' : 'javascript:confirmEditTopic(' . $data['id'] . ')' ?>"
                   class="button confirm black"><?= lang('Service.confirm') ?></a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function confirmEditTopic(id) {
        let data = parseInputToData($(`.topic-wrap .form-wrap .editable`))
        data['files'] = files.get('topic');

        apiRequest({
            type: 'POST',
            url: `/api/topic/update/${id}`,
            data: data,
            dataType: 'json',
            success: function (response, status, request) {
                if (!response.success) {
                    openPopupErrors('popup-error', response, status, request);
                    return;
                }
                history.back();
            },
            error: function (response, status, error) {
                openPopupErrors('popup-error', response, status, error);
            },
        });
    }

    function confirmCreateTopic() {
        let data = parseInputToData($(`.topic-wrap .form-wrap .editable`))
        data['files'] = files.get('topic');

        apiRequest({
            type: 'POST',
            url: `/api/topic/create`,
            data: data,
            dataType: 'json',
            success: function (response, status, request) {
                if (!response.success) {
                    openPopupErrors('popup-error', response, status, request);
                    return;
                }
                history.back();
            },
            error: function (response, status, error) {
                openPopupErrors('popup-error', response, status, error);
            },
        });
    }
</script>
