<?php
$logo_url = isset($logos['logo']) ? "/file/{$logos['logo']['id']}" : '/asset/images/custom/logo.svg';
$favicon_url = isset($logos['favicon']) ? "/file/{$logos['favicon']['id']}" : '/asset/images/favicon.ico';
$open_graph_url = isset($logos['open_graph']) ? "/file/{$logos['open_graph']['id']}" : '/asset/images/include/open_graph.png';
?>
<!doctype html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width"/>
    <title><?= $settings['web-title'] ?? '' ?></title>
    <meta property="og:title" content="<?= $settings['web-title'] ?? '' ?>"/>
    <meta property="og:image" content="<?= $open_graph_url ?>"/>
    <link rel="icon" type="image/x-icon" href="<?= $favicon_url ?>">

    <link rel="stylesheet" type="text/css" href="/asset/font/fonts.css"/>
    <link rel="stylesheet" type="text/css" href="/asset/css/default.css"/>
    <link rel="stylesheet" type="text/css" href="/asset/css/common/animation.css"/>
    <link rel="stylesheet" type="text/css" href="/asset/css/client/style.css"/>
    <link rel="stylesheet" type="text/css" href="/asset/css/client/include.css"/>
    <link rel="stylesheet" type="text/css" href="/asset/css/common/popup.css"/>
    <link rel="stylesheet" type="text/css" href="/asset/css/client/popup.css"/>
    <?php
    if (isset($css)) echo $css;
    ?>

    <script type="text/javascript" src="/asset/js/library/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="/asset/js/default.js"></script>
    <script type="text/javascript" src="/asset/js/client/navigation.js"></script>
    <script type="text/javascript" src="/asset/js/module/popup.js"></script>
    <script type="text/javascript" src="/asset/js/client/popup.js"></script>
    <script type="text/javascript" src="/asset/js/common/login.js"></script>
    <?php
    if (isset($js)) echo $js;
    if (isset($javascript)) echo $javascript;
    ?>
</head>
<?= \App\Helpers\HtmlHelper::setTranslationsClient() ?>
<body>
<div class="loading-wrap">
    <span class="gadget"></span>
</div>
<div id="wrap">
    <header id="header">
        <div class="mobile-utill mobile-only">
            <a href="javascript:openNavigation()" class="button navigation menu mobile-only">
                <span class="top" style="transform: rotate(0deg); top: 15px;"></span>
                <span class="middle" style="opacity: 1;"></span>
                <span class="bottom" style="transform: rotate(0deg); top: 25px;"></span>
            </a>
            <h1 class="logo"><a href="/"><img src="<?= $logo_url ?>" alt="header logo"></a></h1>
        </div>
        <div class="line mobile-only"></div>
        <div class="header-inner">
            <h1 class="logo pc-only"><a href="/"><img src="<?= $logo_url ?>" alt="header logo"></a>
            </h1>
            <div class="utill">
                <select onchange="onLanguageChanged(this)">
                    <option value="ko" <?= $lang == 'ko' ? 'selected' : '' ?>>한국어</option>
                    <option value="en" <?= $lang == 'en' ? 'selected' : '' ?>>English</option>
                </select>
                <ul class="cf">
                    <?php if ($is_login) {
                        if ($is_admin) { ?>
                            <li><a href="/admin"><?= lang('Service.admin_page') ?></a></li>
                        <?php } ?>
                        <li><a class="button-fill" href="/profile"><?= lang('Service.profile') ?></a></li>
                        <li><a class="button-line"
                               href="javascript:logout();"><?= lang('Service.logout') ?></a></li>
                    <?php } else { ?>
                        <li><a href="/login" class="button-fill"><?= lang('Service.login') ?></a></li>
                        <li><a href="/registration" class="button-line">
                                <?= lang('Service.register') ?></a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <ul class="gnb cf">
                <li>
                    <a <?= "onclick=\"clickClientNavigation(this, '#')\"" ?>class="button gnb-menu">
                        <?= lang('Client.menu_project_list') ?>
                    </a>
                    <ul class="lnb">
                        <?php foreach ($code_project as $index => $item) { ?>
                            <li>
                                <a <?= "onclick=\"clickClientNavigation(this, '/project/category/" . $item['code'] . "')\"" ?>
                                    href="/project/category/<?= $item['code'] ?>"
                                    class="button lnb-menu">
                                    <?= $lang == 'ko' ? $item['name'] : $item['name_en'] ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
                <li>
                    <a href="#" class="button gnb-menu">
                        <?= lang('Client.menu_user_guide') ?>
                    </a>
                </li>
                <li>
                    <a href="#" class="button gnb-menu">
                        <?= lang('Client.menu_inquiry') ?>
                    </a>
                </li>
                <?php if ($is_login) { ?>
                    <li>
                        <a href="/purchase" class="button gnb-menu highlight">
                            <?= lang('Client.menu_purchase_list') ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </header>
    <div id="container">
