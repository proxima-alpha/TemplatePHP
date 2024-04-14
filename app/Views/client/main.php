<?php

if (!isset($links) && !isset($is_login)) return;

$sliderImages = [
    '/asset/images/slider/drilling-rig-4581167_1280.jpeg',
    '/asset/images/slider/film-4613426_1280.jpeg',
    '/asset/images/slider/geometry-7209216_1280.jpeg',
    '/asset/images/slider/record-player-1851576_1280.jpeg',
    '/asset/images/slider/vacation-2302013_1280.jpeg',
];
$logo_url = isset($logos['logo']) ? "/file/{$logos['logo']['id']}" : '/asset/images/custom/logo.svg';
$footer_logo_url = isset($logos['footer_logo']) ? "/file/{$logos['footer_logo']['id']}" : '/asset/images/custom/logo.svg';
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
    <meta property="og:description" content="A shiny red apple with a bite taken out"/>

    <link rel="icon" type="image/x-icon" href="<?= $favicon_url ?>">

    <link rel="stylesheet" type="text/css" href="/asset/font/fonts.css"/>
    <link rel="stylesheet" type="text/css" href="/asset/css/default.css"/>
    <link rel="stylesheet" type="text/css" href="/asset/css/common/animation.css"/>
    <link rel="stylesheet" type="text/css" href="/asset/css/client/style.css"/>
    <link rel="stylesheet" type="text/css" href="/asset/css/client/include.css"/>
    <link rel="stylesheet" type="text/css" href="/asset/css/common/popup.css"/>
    <link rel="stylesheet" type="text/css" href="/asset/css/common/table.css"/>
    <link rel="stylesheet" type="text/css" href="/asset/css/common/grid.css"/>
    <link rel="stylesheet" type="text/css" href="/asset/css/client/main.css"/>

    <script type="text/javascript" src="/asset/js/library/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="/asset/js/default.js"></script>
    <script type="text/javascript" src="/asset/js/library/fullpage/jquery.fullPage.js"></script>
    <script type="text/javascript" src="/asset/js/library/slick/slick.min.js"></script>
    <script type="text/javascript"
            src="//dapi.kakao.com/v2/maps/sdk.js?appkey=<?= $settings['kakao-map-appkey'] ?? '' ?>"></script>

    <script type="text/javascript" src="/asset/js/module/slick_custom.js"></script>
    <script type="text/javascript" src="/asset/js/client/navigation.js"></script>
    <script type="text/javascript" src="/asset/js/module/popup.js"></script>
    <script type="text/javascript" src="/asset/js/common/topic_view.js"></script>
    <script type="text/javascript" src="/asset/js/common/login.js"></script>
    <script type="text/javascript" src="/asset/js/client/main.js"></script>
    <script type="text/javascript">
        addEventListener('customMapLoad', function () {
            let points = [];
            <?php
            if(isset($locations)) {
            foreach ($locations['array'] as $index => $point) { ?>
            points.push({
                name: '<?=$point['name']?>',
                latitude: '<?=$point['latitude']?>',
                longitude: '<?=$point['longitude']?>',
            })
            <?php
            }
            }
            ?>
            setMapPoints(points)
        })
        let video = null;
        <?php if(isset($videos) && sizeof($videos) > 0) {
        $video = $videos[0];?>
        video = {
            id: <?=$video['id']?>,
            mime_type: '<?=$video['mime_type']?>',
        }
        <?php } ?>
    </script>

</head>
<?= \App\Helpers\HtmlHelper::setTranslationsClient(['message_popup_page']) ?>
<body>
<div class="loading-wrap">
    <span class="gadget"></span>
</div>
<div id="container">
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
                    <a href="#" class="button gnb-menu">
                        <?= lang('Service.menu_artist_list') ?>
                    </a>
                </li>
                <li>
                    <a href="#" class="button gnb-menu">
                        <?= lang('Service.menu_user_guide') ?>
                    </a>
                </li>
                <li>
                    <a href="#" class="button gnb-menu">
                        <?= lang('Service.menu_inquiry') ?>
                    </a>
                </li>
            </ul>
        </div>
    </header>
    <div class="section " id="page-start">
        <div class="main-slider-wrap">
            <div class="slider-box">
                <div class="slick">
                    <?php foreach ($slider_images as $index => $image) { ?>
                        <div class="slick-item"
                             style="background: url('/file/<?= $image['id'] ?>') no-repeat center; background-size: cover; font-size: 0;">
                            Slider #<?= $index ?>
                        </div>
                    <?php } ?>
                </div>
                <div class="slider-text-wrap">
                    <div class="text-wrap">
                        <p class="content"><?= $settings['main-content-text'] ?? '' ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer id="footer">
        <div class="footer-inner">
            <a href="/" class="logo"><img src="<?= $footer_logo_url ?>" alt=" footer logo"></a>
            <div class="text-wrap">
                <div class="button-wrap">
                    <ul class="cf">
                        <li><a href="#"><?= lang('Service.artist_registration') ?></a></li>
                        <li><a href="#"><?= lang('Service.label_registration') ?></a></li>
                        <li><a href="#"><?= lang('Service.guide_qna') ?></a></li>
                        <li><a href="#"><?= lang('Service.request_demo') ?></a></li>
                        <li><a href="#"><?= lang('Service.agreement_service') ?></a></li>
                        <li><a href="#"><?= lang('Service.agreement_personal') ?></a></li>
                    </ul>
                </div>
                <div class="company-info">
                    <p class="name"><?= $company_info['name'] ?></p>
                    <ul class="cf">
                        <?php foreach ($company_info as $key => $value) {
                            if ($key != 'name') { ?>
                                <li>
                                    <p class="title"><?= lang('Service.' . $key) ?></p>
                                    <p class="value"><?= $value ?></p>
                                </li>
                            <?php }
                        } ?>
                    </ul>
                </div>
                <ul class="cf">
                    <?php if (isset($settings['footer-text'])) {
                        $texts = preg_split("/\r\n|\n|\r/", $settings['footer-text']);
                        foreach ($texts as $text) { ?>
                            <li><p><?= $text ?></p></li>
                        <?php }
                    } ?>
                </ul>
                <div class="terms">
                    <?php foreach ($terms as $index => $value) { ?>
                        <p><?= $value ?><?= $index == sizeof($terms) - 1 ? '<a href="#">[' . lang('Service.show_information') . ']</a>' : '' ?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
</div>
</footer>
</div>

</body>
</html>
