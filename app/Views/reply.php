<?php if (!isset($pagination) || !isset($array)) {
    return;
}
$number = $pagination['total'] - ($pagination['page'] - 1) * $pagination['per-page'];

$page = $pagination['page'];
$total_page = $pagination['total-page'];

$start = intval(($page - 1) / 5) * 5 + 1;
$end = (intval(($page - 1) / 5) + 1) * 5;
$end = min($end, $total_page);
?>
<style>
    .reply-wrap {
        width: 100%;
        padding-bottom: 100px;
        font-size: 0;
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
    }

    .reply-wrap .row {
        /*height: 50px;*/
        line-height: 49px;
    }

    .reply-wrap .row .column {
        padding: 0 10px;
        line-height: 35px;
        text-align: left;
        box-sizing: border-box;
        font-size: 16px;
        display: inline-block;
        vertical-align: middle;

        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

    .reply-wrap .row .column.user {
        width: 200px;
        font-weight: 400;
    }

    .reply-wrap .row .column.content {
        width: calc(100% - 400px);
    }

    .reply-wrap .row .column.created-at {
        width: 200px;
        text-align: right;
    }

    .reply-wrap .nested-reply .row {
        padding-left: 20px;
        background: rgba(200, 200, 200, 0.5);
    }

    .reply-wrap .nested-reply .load-previous {
        width: 100%;
        padding: 0;
        line-height: 40px;
        font-size: 20px;
    }

    .reply-wrap .nested-reply .row .column {
        line-height: 25px;
    }

    .reply-wrap .button-wrap.more {
        padding: 0 20px;
        background: rgba(200, 200, 200, 0.5);
    }

    .reply-wrap .reply .button {
        padding: 5px 8px;
        box-sizing: border-box;
        position: relative;
    }

    .reply-wrap .reply .button:hover:after {
        content: "";
        display: inline-block;
        height: 1px;
        position: absolute;
        bottom: 2px;
        left: 2px;
        right: 6px;
        background: #444;
    }

    .reply-wrap .reply .button img {
        margin-left: 4px;
        vertical-align: bottom;
    }

    .reply-wrap .reply .button span {
        line-height: 20px;
        font-size: 14px;
        vertical-align: bottom;
    }

    .reply-wrap .pages {
        height: 100px;
        line-height: 100px;
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
    }

    .reply-wrap .pages span {
        display: inline-block;
        width: 30px;
        height: 30px;
        line-height: 30px;
        font-size: 18px;
        vertical-align: middle;
    }

    .reply-wrap .pages .button.disabled {
        background: none;
    }

    .reply-wrap .pages .button.disabled a {
        cursor: default;
    }

    .reply-wrap .pages .button.left {
        background: url('/asset/images/icon/button_left.png') no-repeat center;
        -webkit-background-size: 60%;
        background-size: 9px 16px;
    }

    .reply-wrap .pages .button.right {
        background: url('/asset/images/icon/button_right.png') no-repeat center;
        -webkit-background-size: 60%;
        background-size: 9px 16px;
    }

    .reply-wrap .pages span a {
        height: 100%;
        display: block;
    }

    .reply-wrap .pages span.now {
        margin: 0 2px -1px 2px;
        width: 26px;
        font-weight: 400;
        border-bottom: 1px solid #333;
    }

    .reply-wrap .input-wrap.reply {
        position: relative;
    }

    .reply-wrap .input-wrap.topic-reply {
        margin-top: -1px;
    }

    .reply-wrap .input-wrap.reply textarea {
        width: 100%;
        height: 80px;
        padding: 10px;
        font-size: 16px;
        font-weight: 200;
        border: none;
    }

    .reply-wrap .input-wrap.reply .button.float {
        text-align: center;
        position: absolute;
        right: 5px;
        bottom: 5px;
        font-size: 0;
    }

    .reply-wrap .list.reply textarea {
        height: 50px;
        padding: 10px 20px;
        background: rgba(200, 200, 200, 0.5);
    }

    .reply-wrap .list.reply .button.float {
        right: 10px;
    }

    @media (max-width: 840px) {
        .reply-wrap .row .column.user {
            width: 100px;
        }

        .reply-wrap .row .column.content {
            width: calc(100% - 100px);
        }

        .reply-wrap .row .column.created-at {
            display: none;
        }
    }
</style>

<div class="reply-wrap">
    <ul class="list reply">
        <!--        --><?php //if (sizeof($array) > 0) { ?>
        <!--            <li class="line"></li>-->
        <!--        --><?php //} ?>
        <?php foreach ($array as $index => $reply) { ?>
            <li id="reply-<?= $reply['id'] ?>" reply-id="<?= $reply['id'] ?>">
                <div class="row selector line-after"
                     onclick="openReplyNested(<?= $reply['id'] ?>)" id="<?= $reply['id'] ?>">
                    <span class="column user"><?= $reply['user_name'] ?></span>
                    <span class="column content"><?= $reply['content'] ?></span>
                    <span class="column created-at"><?= $reply['created_at'] ?></span>
                </div>
            </li>
        <?php } ?>
    </ul>
    <?php if ($is_login) { ?>
        <div class="input-wrap reply topic-reply lines-horizontal">
            <textarea placeholder="Comment"></textarea>
            <a href="javascript:postReply(<?= $topic_id ?>)" class="button float">
                <span><?= lang('Service.send') ?></span>
                <img src="/asset/images/icon/send.png"/>
            </a>
        </div>
    <?php } ?>
    <div class="pages">
        <?php
        if ($start == 1) { ?>
            <span class="button disabled"><a href="#" onclick="return false"></a></span>
        <?php } else { ?>
            <span class="button left"><a
                    href="javascript:loadReply(<?= $topic_id ?>, <?= $start - 5 ?>)"></a></span>
        <?php }
        for ($i = $start; $i <= $end; ++$i) { ?>
            <span class="number <?= $i == $page ? 'now' : '' ?>">
                <a href="javascript:loadReply(<?= $topic_id ?>, <?= $i ?>)"><?= $i ?></a></span>
        <?php }
        if ($total_page == $end) { ?>
            <span class="button disabled"><a href="#" onclick="return false"></a></span>
            <?php
        } else { ?>
            <span class="button right"><a
                    href="javascript:loadReply(<?= $topic_id ?>, <?= $start + 5 ?>)"></a></span>
        <?php } ?>
    </div>
</div>
<script type="text/javascript">
    /**
     * topic view(#container) 에서 여백을 만들어 주는 기능
     * reply view 를 absolute 로 추가해 주기 때문에 생성 후 추가해 준다
     */
    function resizeReplyWrap() {
        let $wrapReply = $('.reply-wrap');
        if ($wrapReply.length > 0) {
            // topic view(#container) 에 이미 최소 높이를 줬기 때문에
            // 그냥 주게되면 텅 빈 화면이 반을 차지하기 때문에댓글이 있는 경우에만 최소 높이 줌
            if ($wrapReply.find('.list').children().length > 0) {
                $wrapReply.find('.list').css({
                    'min-height': '250px'
                })
            }
            $('#container .container-inner').css({
                'padding-bottom': `${$wrapReply.outerHeight()}px`
            })
        }
    }

    /**
     * 댓글 view 를 생성해 주는 기능
     * 댓글 페이징 시에 전체 reload 를 하지 않기 위해 js 로도 구현
     * @param $parent
     * @param array
     */
    function addReplyItems($parent, array) {
        if (typeof $parent === 'string') {
            $parent = $($parent);
        }
        if (!$parent || !$parent.get(0)) {
            return
        }
        let html = `<ul class="list reply">`;
        // html += `<li class="line"></li>`;

        for (let i in array) {
            let item = array[i];
            html += `<li id="reply-${item['id']}" reply-id="${item['id']}">`;
            html += `
                <div class="row selector line-after"
                    onclick="openReplyNested(${item['id']})" id="${item['id']}">
                    <span class="column user">${item['user_name']}</span>
                    <span class="column content">${item['content']}</span>
                    <span class="column created-at">${item['created_at']}</span>
                </div>`;
            html += `</li>`;
        }
        html += `</ul>`;

        $parent.append(html);
    }

    /**
     * 하위 댓글 view 를 생성해 주는 기능
     * @param $parent
     * @param array
     * @param pagination
     */
    function addReplyNestedItems($parent, array, pagination) {
        if (typeof $parent === 'string') {
            $parent = $($parent);
        }
        if (!$parent || !array || array.length == 0) {
            return
        }
        if ($parent.find('.nested-reply').length == 0) {
            $parent.find('.row').after(`<ul class="nested-reply line-after"></ul>`);
        }
        let page = pagination['page'];
        let reply_id = $parent.attr('reply-id');
        let html = ``;
        let $oldPage = $parent.find(`.nested-reply-page-${page}`);

        let $list = $parent.find('.nested-reply .row');
        // 이전 보기 버튼보다 상단에 있는 item 중 마지막
        // 혹은 이전 보기 버튼이 list 내에 없는 경우 모든 item 중 마지막
        let $lastItem = null;
        let $buttonLoadPrevious = null;
        for (let i in $list) {
            if ($list.eq(i).hasClass('load-previous')) {
                if (i > 1) {
                    $lastItem = $list.eq(i - 1);
                }
                $buttonLoadPrevious = $list.eq(i);
            }
        }
        if (!$lastItem) {
            // 새 댓글을 다는 경우 확인을 위해 마지막 페이지를 보여주는데 (생성일자의 DESC 순으로 정렬해서 보여주기 때문에)
            // 첫 번째 페이지만 load 후에 새 댓글을 다는 경우 1 페이지와 last 페이지 간의 간격이 1 이상일 수 있음
            // 해당 경우에도 새로운 데이터 추가 시 '이전 보기 버튼' 의 추가가 필요한 경우를 비교하기 위함
            $lastItem = $list.last();
        }
        if ($lastItem) {
            try {
                // 존재하는 마지막 item 과 페이지 차이가 없는 경우에는 '이전 보기 버튼'을 추가하지 않음
                let previousPage = parseInt($lastItem.attr('page'));
                if (page > 1 && (page - 1 - previousPage) > 0) {
                    html += `
                    <li class="load-previous" page=${page}>
                        <div class="row selector previous" onclick="loadReplyNested(${reply_id}, ${page - 1})">...</div>
                    </li>`;
                }
            } catch (e) {
                // do nothing
            }
        }

        for (let i in array) {
            let item = array[i];
            html += `
            <li class="nested-reply-page-${page}" page=${page}>
                <div class="row">
                    <span class="column user">${item['user_name']}</span>
                    <span class="column content">${item['content']}</span>
                    <span class="column created-at">${item['created_at']}</span>
                </div>
            </li>`;
        }


        if ($oldPage.length > 0) {
            // 만약 refresh 되는 last 페이지가 이미 load 되어 있는 경우
            // item 교체만 수행
            $oldPage.last().after(html);
            $oldPage.remove();
        } else {
            if ($buttonLoadPrevious) {
                // 이전 보기 버튼 누른 경우 (... 버튼)
                // 이전 보기 버튼을 기준으로 새로운 데이터 추가
                $buttonLoadPrevious.before(html);
                $buttonLoadPrevious.remove();
            } else {
                // 마지막 페이지가 아닌 경우 (See More 버튼)
                $parent.find('.nested-reply').append(html);

                let $wrapMoreButton = $parent.find(`.button-wrap`)
                $wrapMoreButton.remove();
                if (pagination['page'] < pagination['total-page']) {
                    $parent.find(`.nested-reply`).append(`
                    <li class="button-wrap more">
                        <a href="javascript:loadReplyNested(${reply_id},${page + 1});" class="button more">
                            <span>See More</span>
                            <img src="/asset/images/icon/plus.png"/>
                        </a>
                    </li>`);
                }
            }
        }
        let offset = $parent.find('.nested-reply li').last().offset();
        window.scrollTo(offset.left, offset.top)
    }

    /**
     * pagination 추가 기능
     * 댓글 페이징 시에 전체 reload 를 하지 않기 위해 js 로도 구현
     * @param $parent
     * @param topic_id
     * @param pagination
     */
    function addReplyPagination($parent, topic_id, pagination) {
        if (typeof $parent === 'string') {
            $parent = $($parent);
        }
        if (!$parent ||
            !$parent.get(0) ||
            !pagination ||
            !pagination['total'] ||
            !pagination['page'] ||
            !pagination['total-page'] ||
            !pagination['per-page']) {
            return;
        }

        let page = pagination['page'];
        let total_page = pagination['total-page'];

        let start = Math.floor((page - 1) / 5) * 5 + 1;
        let end = (Math.floor((page - 1) / 5) + 1) * 5;
        end = Math.min(end, total_page);

        let html = "";

        html += `<div class="pages">`

        if (start == 1) {
            html += `<span class="button disabled"><a href="#" onclick="return false"></a></span>`;
        } else {
            html += `<span class="button left"><a href="javascript:loadReply(${topic_id}, ${start - 5})"></a></span>`;
        }

        for (let i = start; i <= end; ++i) {
            html += `<span class="number ${i == page ? 'now' : ''}"><a href="javascript:loadReply(${topic_id}, ${i})">${i}</a></span>`;
        }
        if (total_page == end) {
            html += `<span class="button disabled"><a href="#" onClick="return false"></a></span>`;
        } else {
            html += `<span class="button left"><a href="javascript:loadReply(${topic_id}, ${start - 5})"></a></span>`;
        }

        html += `</div>`;

        $parent.append(html);
    }

    /**
     * 데이터 조회 하여 [addReplyItems] 을 호출하는 함수
     * 실제 버튼 클릭 시 호출
     * @param topic_id
     * @param page
     */
    function loadReply(topic_id, page) {
        apiRequest({
            type: 'GET',
            url: `/api/topic/${topic_id}/get/reply?page=${page}`,
            dataType: 'json',
            success: function (response, status, request) {
                if (!response.success) return;
                let data = response.data;
                const $parent = $('.reply-wrap');
                $parent.empty()
                addReplyItems($parent, data['array'])

                // 로그인 한 경우 댓글 입력창 추가
                <?php if($is_login) { ?>
                $parent.append(`
                <div class="input-wrap reply topic-reply lines-horizontal">
                    <textarea placeholder="Comment"></textarea>
                    <a href="javascript:postReply(${topic_id})" class="button float">
                        <span>${lang('send')}</span>
                        <img src="/asset/images/icon/send.png"/>
                    </a>
                </div>`)
                <?php } ?>

                addReplyPagination($parent, topic_id, data['pagination'])
                resizeReplyWrap();
            },
            error: function (response, status, error) {
            },
        })
    }

    /**
     * 데이터 조회 하여 [addReplyNestedItems] 을 호출하는 함수
     * 실제 버튼 클릭 시 호출
     * @param reply_id
     * @param page
     * @param callback
     */
    function loadReplyNested(reply_id, page = 1, callback = null) {
        apiRequest({
            type: 'GET',
            url: `/api/topic/reply/${reply_id}/get/nested-reply?page=${page}`,
            dataType: 'json',
            success: function (response, status, request) {
                if (!response.success) return;
                let data = response.data;
                let $parent = $(`#reply-${reply_id}`);
                addReplyNestedItems($parent, data['array'], data['pagination'])

                // 로그인 한 경우 댓글 입력창 추가
                if (callback && typeof callback == 'function') callback();
                $parent.addClass('opened');

                resizeReplyWrap();
            },
            error: function (response, status, error) {
            },
        })
    }

    /**
     * item 클릭 제어 기능
     * @param reply_id
     */
    function openReplyNested(reply_id) {
        // 기존의 active item 과 현재 active item 을 비교
        // active item = 하위 댓글용 input 이 존재
        // (하위 댓글용 input 은 한페이지에 한개씩 열림)
        let $input = $(`.reply-wrap .list.reply .input-wrap.reply`);
        let elementCommentInput = $input.get(0);
        if (elementCommentInput && elementCommentInput.getAttribute('id') == reply_id) {
            $(`#reply-${reply_id} textarea`).focus();
            // 기존의 active item 과 현재 active item 이 같다면 아무 동작 할 필요가 없음
            return;
        }
        $input.remove();

        let $parent = $(`#reply-${reply_id}`);
        let callback;
        <?php if($is_login) {?>
        // 만약 하위 댓글 input 을 열 필요가 있더라도 데이터 조회 이후 추가해 줘야 하기 때문에 callback 으로 전달
        callback = function () {
            let $parent = $(`#reply-${reply_id}`);
            $parent.append(`
            <div class="input-wrap reply line-after" id=${reply_id}>
                <textarea placeholder="Comment"></textarea>
                <a href="javascript:postReplyNested(${reply_id})" class="button float">
                    <span>${lang('send')}</span>
                    <img src="/asset/images/icon/send.png"/>
                </a>
            </div>`)
            $parent.find(`textarea`).focus();
        }
        <?php } ?>
        if ($parent.hasClass('opened')) {
            // 이미 하위 댓글 창이 열려 있는 경우 input 만 추가해준다.
            if (callback && typeof callback == 'function') callback();
        } else {
            // 아닌 경우 하위 댓글 데이터 첫 페이지 조회 호출
            loadReplyNested(reply_id, 1, callback)
        }
    }

    /**
     * 댓글 업로드 기능
     * (댓글이 달릴 topic 의 id 필요)
     * @param topic_id
     */
    function postReply(topic_id) {
        let $inputComment = $(`.reply-wrap .input-wrap.topic-reply textarea`);
        let data = {
            'content': $inputComment.val(),
        };
        <?php if($is_login) { ?>
        data['user_id'] = <?=$user_id?>;
        <?php } ?>
        apiRequest({
            type: 'POST',
            url: `/api/topic/${topic_id}/create/reply`,
            data: data,
            dataType: 'json',
            success: function (response, status, request) {
                if (!response.success) return;
                loadReply(topic_id, 'last');
                $inputComment.val("");
            },
            error: function (response, status, error) {
            },
        })
    }

    /**
     * 하위 댓글 업로드 기능
     * (부모 댓글 id 필요)
     * @param reply_id
     */
    function postReplyNested(reply_id) {
        let $inputComment = $(`#reply-${reply_id} .input-wrap.reply textarea`);
        let data = {
            'content': $inputComment.val(),
        };
        <?php if($is_login) { ?>
        data['user_id'] = <?=$user_id?>;
        <?php } ?>
        apiRequest({
            type: 'POST',
            url: `/api/topic/reply/${reply_id}/create/nested-reply`,
            data: data,
            dataType: 'json',
            success: function (response, status, request) {
                if (!response.success) return;
                loadReplyNested(reply_id, 'last')
                $inputComment.val("");
            },
            error: function (response, status, error) {
            },
        })
    }

    resizeReplyWrap();
</script>
