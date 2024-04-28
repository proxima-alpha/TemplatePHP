<?php
if (!isset($links) && !isset($is_login)) return;
$is_admin_navigation_closed = isset($is_admin_navigation_closed) && $is_admin_navigation_closed == 1;
$is_not_login_page = !isset($is_login_page) || ($is_login_page == false || $is_login_page == 0);
$is_not_registration_page = !isset($is_registration_page) || ($is_registration_page == false || $is_registration_page == 0);
?>
<!doctype html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width"/>
    <title><?= lang('Service.admin_title') ?></title>

    <link rel="stylesheet" type="text/css" href="/asset/font/fonts.css"/>
    <link rel="stylesheet" type="text/css" href="/asset/css/default.css"/>
    <link rel="stylesheet" type="text/css" href="/asset/css/common/animation.css"/>
    <link rel="stylesheet" type="text/css" href="/asset/css/admin/style.css"/>
    <link rel="stylesheet" type="text/css" href="/asset/css/admin/include.css"/>
    <link rel="stylesheet" type="text/css" href="/asset/css/common/popup.css"/>
    <?php
    if (isset($css)) echo $css;
    ?>

    <script type="text/javascript" src="/asset/js/library/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="/asset/js/default.js"></script>
    <script type="text/javascript" src="/asset/js/module/popup.js"></script>
    <script type="text/javascript" src="/asset/js/admin/navigation.js"></script>
    <?php
    if (isset($js)) echo $js;
    if (isset($javascript)) echo $javascript;
    ?>
</head>
<?= \App\Helpers\HtmlHelper::setTranslationsAdmin() ?>
<body>
<div class="loading-wrap">
    <span class="gadget"></span>
</div>
<div id="wrap">
    <header id="header">
        <div class="header-inner">
            <div class="utill">
                <?php if ($is_not_login_page) { ?>
                    <ul class="cf">
                        <?php if ($is_login) { ?>
                            <li><a href="/"><?= lang('Service.main_page') ?></a></li>
                            <li><a href="/admin/profile"><?= lang('Service.profile') ?></a></li>
                            <li class="last"><a href="javascript:logout();"><?= lang('Service.logout') ?></a></li>
                        <?php } else {
                            if ($is_not_registration_page) { ?>
                                <li><a href="/admin/registration"><?= lang('Service.register') ?></a></li>
                                <li class="last"><a href="/admin/login"><?= lang('Service.login') ?></a></li>
                            <?php } else { ?>
                                <li class="last"><a href="/admin/login"><?= lang('Service.login') ?></a></li>
                            <?php }
                        } ?>
                    </ul>
                <?php } ?>
            </div>
            <?php if ($is_login && $is_not_login_page && $is_not_registration_page) { ?>
                <div class="gnb-wrap fixed<?= $is_admin_navigation_closed ? ' closed' : '' ?>">
                    <div class="gnb">
                        <ul class="cf">
                            <?php foreach ($links as $index => $item) { ?>
                                <li><a href="<?= $item['link'] ?>" class="button"><span><?= $item['name'] ?></span></a>
                                </li>
                            <?php } ?>
                        </ul>
                        <a href="javascript:closeNavigation()" class="button navigation close">
                            <span class="top"></span>
                            <span class="bottom"></span>
                        </a>
                    </div>
                </div>
                <?php if ($is_admin_navigation_closed) { ?>
                    <a href="javascript:openNavigation()" class="button navigation menu">
                        <span class="top" style="transform: rotate(0deg); top: 15px;"></span>
                        <span class="middle" style="opacity: 1;"></span>
                        <span class="bottom" style="transform: rotate(0deg); top: 25px;"></span>
                    </a>
                <?php } else { ?>
                    <a href="javascript:closeNavigation()" class="button navigation menu">
                        <span class="top"></span>
                        <span class="middle"></span>
                        <span class="bottom"></span>
                    </a>
                <?php }
            } ?>
        </div>
    </header>
    <div id="container">
