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
    uploadData.push('topic', '<?=$item['id']?>');
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
                <?= \App\Helpers\HtmlHelper::getMultiMediaUploader('topic', $data['files'] ?? null, 'input', 'image/png,image/jpg') ?>
                <div class="info-text-wrap">
                    <?= lang('Service.message_info_drag') ?>
                </div>
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
        data['files'] = uploadData.get('topic');

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
        data['files'] = uploadData.get('topic');

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
