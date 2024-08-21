<?php

use App\Helpers\HtmlHelper;

?>
<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= lang('Service.reply') ?>
        </h3>
        <div class="table-box">
            <div class="table-wrap">
                <?php if (HtmlHelper::showDataEmpty($array)) { ?>
                    <div class="row-title">
                        <div class="row">
                            <span class="column user"><?= lang('Service.user') ?></span>
                            <span class="column content"><?= lang('Service.content') ?></span>
                            <span class="column board"><?= lang('Service.board') ?></span>
                            <span class="column created-at"><?= lang('Service.created_at') ?></span>
                        </div>
                    </div>
                    <ul>
                        <?php foreach ($array as $index => $item) { ?>
                            <li class="row">
                                <a href="javascript:openReplyPopup('<?= $item['id'] ?>')" class="button row-button">
                                    <span class="column user"><?= $item['user_name'] ?></span>
                                    <span class="column content"><?= $item['content'] ?></span>
                                    <span class="column board"><?= $item['board_alias'] ?></span>
                                    <span class="column created-at"><?= $item['created_at'] ?></span>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </div>
        </div>

        <?= HtmlHelper::getPagination($pagination, $pagination_link); ?>
    </div>
</div>
<script type="text/javascript">
    /**
     * admin/popup_input 에서 delete 기능만 가져와서 사용
     */
    initializeInputPopup({
        getDeleteUrl: function (id) {
            return `/api/topic/reply/delete/${id}`
        },
    })

    /**
     * reply 상세 popup 을 여는 기능
     * 본인의 작성물에 대한 수정은 본인만 가능하여야 하므로
     * admin 이 일방적으로 수정은 할 수 없도록 하기 때문에 삭제 기능만 가능
     * @param id
     */
    function openReplyPopup(id) {
        apiRequest({
            type: 'GET',
            url: `/api/topic/reply/get/${id}`,
            dataType: 'json',
            success: async function (response, status, request) {
                if (!response.success)
                    return;
                let data = response.data;
                let className = 'popup-reply';
                let css = await loadStyleFile('/asset/css/common/popup/reply.css', "." + className);
                let html = `
                <div class="control-button-wrap top">
                    <a href="/admin/topic/${data['topic_id']}"
                        class="button under-line detail">
                        <img src="/asset/images/icon/topic.png"/>
                        <span>${data['board_alias']} / ${data['topic_title']}</span>
                    </a>
                </div>
                <div class="row row-title line-after black">
                    <span class="column user link">
                        <a href="javascript:openUserPopup(${data['user_id']});" class="button out-line">
                            <img src="/asset/images/icon/user.png"/>
                            <span>${data['user_name']}</span>
                        </a>
                    </span>
                    <span class="column created-at">${data['created_at']}</span>
                </div>
                <div class="text-wrap">
                    <div class="content">${data['content']}</div>
                </div>
                <div class="control-button-wrap absolute line-before">
                    <div class="control-button-box">
                        <a href="javascript:openInputPopupDelete(${data['id']});"
                            class="button under-line delete">
                            <img src="/asset/images/icon/delete.png"/>
                            <span>${lang('delete')}</span>
                        </a>
                    </div>
                </div>`;
                openPopup({
                    className: className,
                    style: `<style>${css}</style>`,
                    html: html,
                })
            },
            error: function (response, status, error) {
            },
        });
    }
</script>
