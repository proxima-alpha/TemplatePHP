<div class="container-inner">
    <div class="container-wrap">
        <h4 class="message"><?= isset($message) ? $message : lang('Client.project_blocked_message') ?></>
        <?php if (isset($error)) { ?>
            <div class="error-message-wrap">
                <p><?= $error ?></p>
            </div>
        <?php } ?>
        <div class="button-wrap">
            <a class="button button-fill"
               href="/"><?= lang('Client.project_blocked_next') ?></a>
        </div>
    </div>
</div>
