<?php

$is_admin_page = isset($is_admin) && $is_admin;
?>
<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= lang('Service.inquiry') ?>
        </h3>
        <div class="reservation-wrap">
            <?php if (strlen($data['questioner_name'] ?? '') > 0 && strlen($data['questioner_id'] ?? '') > 0) { ?>
                <div class="row-box line-after black">
                    <div class="row bold user link">
                        <a class="button out-line">
                            <img src="/asset/images/icon/user_white.png"/>
                            <span><?= $data['questioner_name'] ?></span>
                        </a>
                    </div>
                </div>
            <?php }
            if (strlen($data['question_comment'] ?? '') > 0) { ?>
                <div class="text-wrap">
                    <div class="comment"><?= $data['question_comment'] ?></div>
                </div>
            <?php }
            if (strlen($data['status'] ?? '') > 0 && $data['status'] != 'requested') {
                if (strlen($data['respondent_id'] ?? '') > 0 && strlen($data['respondent_name'] ?? '') > 0) { ?>

                    <div class="row-box line-after black" style="margin-top: 20px;">
                        <div class="row bold user link">
                            <a class="button out-line">
                                <img src="/asset/images/icon/user_white.png"/>
                                <span><?= $data['respondent_name'] ?></span>
                            </a>
                        </div>
                    </div>
                <?php }
                if (strlen($data['respond_comment'] ?? '') > 0) { ?>
                    <div class="text-wrap">
                        <div class="comment"><?= $data['respond_comment'] ?></div>
                    </div>
                <?php }
            } ?>
        </div>
    </div>
</div>
