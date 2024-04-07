<?php

use App\Helpers\Utils;

if (!isset($links) && !isset($is_login)) return;

$sliderImages = [
    '/asset/images/slider/drilling-rig-4581167_1280.jpeg',
    '/asset/images/slider/film-4613426_1280.jpeg',
    '/asset/images/slider/geometry-7209216_1280.jpeg',
    '/asset/images/slider/record-player-1851576_1280.jpeg',
    '/asset/images/slider/vacation-2302013_1280.jpeg',
];
$logo_url = isset($logos['logo']) ? "/file/{$logos['logo']['id']}" : '/asset/images/include/logo.png';
$footer_logo_url = isset($logos['footer_logo']) ? "/file/{$logos['footer_logo']['id']}" : '/asset/images/include/logo_footer.png';
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
    <div class="section " id="page-start">
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
                <div class="utill">
                    <ul class="cf">
                        <?php if ($is_login) {
                            if ($is_admin) { ?>
                                <li><a href="/admin"><?= lang('Service.admin_page') ?></a></li>
                            <?php } ?>
                            <li><a href="/profile"><?= lang('Service.profile') ?></a></li>
                            <li class="last"><a href="javascript:logout();"><?= lang('Service.logout') ?></a></li>
                        <?php } else { ?>
                            <li><a href="/registration"><?= lang('Service.register') ?></a></li>
                            <li class="last"><a href="/login"><?= lang('Service.login') ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
                <h1 class="logo pc-only"><a href="/"><img src="<?= $logo_url ?>" alt="header logo"></a>
                </h1>
                <ul class="gnb cf">
                    <?php foreach ($links as $i => $link) { ?>
                        <li>
                            <a <?= "onclick=\"clickClientNavigation(this, '" . Utils::parseUrl($link['path']) . "')\"" ?>
                                class="button gnb-menu">
                                <?= $link['name'] ?>
                            </a>
                            <ul class="lnb">
                                <?php if ($link['has_local'] == 1) {
                                    foreach ($link['locals'] as $j => $localLink) { ?>
                                        <li>
                                            <a <?= "onclick=\"clickClientNavigation(this, '" . Utils::parseUrl($localLink['path']) . "')\"" ?>
                                                class="button lnb-menu">
                                                <?= $localLink['name'] ?>
                                            </a>
                                        </li>
                                    <?php }
                                } ?>
                            </ul>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </header>
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
                        <p class="title"><?= $settings['main-title-01'] ?? '' ?></p>
                        <p class="content"><?= $settings['main-content-01'] ?? '' ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section" id="page-intro">
        <div class="page-inner whole-page">
            <div class="text-box">
                <div class="text-wrap">
                    <p class="title"><?= $settings['main-title-02'] ?? '' ?></p>
                    <p class="content"><?= $settings['main-content-02'] ?? '' ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="section" id="page-video">
        <div class="page-inner">
            <div class="container-wrap">
                <?php if (\App\Helpers\HtmlHelper::showDataEmpty($settings['main-video-link'] ?? null)) {
                    if (str_starts_with($settings['main-video-link'], "http")) { ?>
                        <iframe class="main-video"
                                src="<?= $settings['main-video-link'] ?>"
                                title="YouTube video player"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen>
                        </iframe>
                    <?php } else {
                        \App\Helpers\HtmlHelper::showMessage('err_wrong_value', 'Wrong value inserted');
                    }
                }
                if (isset($settings['main-title-03']) || isset($settings['main-content-03'])) {
                    echo '<hr>';
                } ?>
                <div class="text-wrap">
                    <p class="page-title"><?= $settings['main-title-03'] ?? '' ?></p>
                    <p class="content"><?= $settings['main-content-03'] ?? '' ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($topics_table)) { ?>
        <div class="section" id="page-notice">
            <div class="page-inner">
                <div class="container-wrap">
                    <h3 class="page-title">
                        <?= $topics_table['board']['alias'] ?>
                    </h3>
                    <div class="table-box">
                        <?php if (\App\Helpers\HtmlHelper::showDataEmpty($topics_table['array'])) {
                            $array = $topics_table['array']; ?>
                            <div class="table-wrap">
                                <div class="row-title">
                                    <div class="row">
                                        <span class="column title"><?= lang('Service.title') ?></span>
                                        <span class="column created-at"><?= lang('Service.created_at') ?></span>
                                    </div>
                                </div>
                                <ul>
                                    <?php foreach ($array as $index => $item) { ?>
                                        <li class=" row">
                                            <a href="javascript:openTopicPopup(<?= $item['id'] ?>);"
                                               class="button row-button">
                                                <span class="column title"><?= $item['title'] ?></span>
                                                <span class="column created-at"><?= $item['created_at'] ?></span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div class="button-wrap">
                                <a href="<?= $topics_table['link'] ?>" class="button more">
                                    <span><?= lang('Service.see_more') ?></span>
                                    <img src="/asset/images/icon/arrow_right.png"/>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (isset($topics_grid)) { ?>
        <div class="section" id="page-preview">
            <div class="page-inner">
                <div class="container-wrap">
                    <h3 class="page-title">
                        <?= $topics_grid['board']['alias'] ?>
                    </h3>
                    <div class="slider-box">
                        <?php if (\App\Helpers\HtmlHelper::showDataEmpty($topics_grid['array'])) {
                            $array = $topics_grid['array']; ?>
                            <div class="slick slider-wrap">
                                <?php foreach ($array as $index => $item) { ?>
                                    <div class="slick-item grid-item button"
                                         onclick="openTopicPopup(<?= $item['id'] ?>)">
                                        <?php if (isset($item['file_id'])) { ?>
                                            <div class="image-wrap"
                                                 style="background: url('/file/<?= $item['file_id'] ?>') no-repeat center; background-size: cover; font-size: 0;">
                                            </div>
                                        <?php } ?>
                                        <div class="text-wrap">
                                            <span class="title"><?= $item['title'] ?></span>
                                            <span class="content"><?= $item['content'] ?></span>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="button-wrap">
                                <a href="<?= $topics_grid['link'] ?>" class="button more">
                                    <span><?= lang('Service.see_more') ?></span>
                                    <img src="/asset/images/icon/arrow_right.png"/>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (isset($locations)) {
        $locationPagination = $locations['pagination']; ?>
        <div class="section" id="page-map">
            <div class="page-inner whole-page">
                <div class="page-title-box mobile-only">
                    <div class="page-title-wrap">
                        <h3 class="page-title"><?= lang('Service.map_location') ?></h3>
                        <a href="javascript:openMobileMapDetailList()" class="button out-line">
                            <span><?= lang('Service.show_detail') ?></span>
                            <img src="/asset/images/icon/button_bottom.png"/>
                        </a>
                    </div>
                </div>
                <div class="location-list-box">
                    <h3 class="page-title pc-only"><?= lang('Service.map_location') ?></h3>
                    <div class="location-list-wrap" page="<?= $locationPagination['page'] ?>"
                         per-page="<?= $locationPagination['per-page'] ?>"
                         total="<?= $locationPagination['total'] ?>"
                         total-page="<?= $locationPagination['total-page'] ?>">
                        <ul>
                            <?php foreach ($locations['array'] as $index => $point) { ?>
                                <li class="button">
                                    <div class="text-wrap">
                                        <div class="title"><?= $point['name'] ?></div>
                                        <div class="content"><?= $point['address'] ?></div>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <div class="map-box">
                    <div id="map" style="width:100%;height:100%;"></div>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="section" id="page-last">
        <div class="page-inner">
            <div class="form-box">
                <div class="form-wrap">
                    <h3 class="title"><?= lang('Service.enquiry_membership') ?></h3>
                    <div class="input-wrap inline">
                        <p class="input-title"><?= lang('Service.name') ?></p>
                        <input type="text" name="name"/>
                    </div>
                    <div class="input-wrap inline">
                        <p class="input-title"><?= lang('Service.phone_number') ?></p>
                        <input type="phone" name="phone_number"/>
                    </div>
                    <div class="input-wrap inline">
                        <p class="input-title"><?= lang('Service.inquiry') ?></p>
                        <textarea class="content" rows="4" name="content"></textarea>
                    </div>
                    <div class="text-wrap agreement">
                        <p><?= $settings['main-agreement'] ?? '' ?></p>
                    </div>
                    <div class="input-wrap inline agreement">
                        <span class="input-title"><?= lang('Service.message_membership_agreement') ?></span>
                        <input type="checkbox" name="agreement" onchange="onMembershipInputValueChanged(this)"/>
                    </div>
                    <div class="button-wrap">
                        <a href="javascript:requestMembership();" class="button submit disabled black">제출</a>
                    </div>
                </div>
            </div>
        </div>
        <footer id="footer">
            <div class="footer-inner">
                <a href="/" class="logo"><img src="<?= $footer_logo_url ?>" alt=" footer logo"></a>
                <div class="text-wrap">
                    <ul class="cf">
                        <?php if (isset($settings['footer-text'])) {
                            $texts = preg_split("/\r\n|\n|\r/", $settings['footer-text']);
                            foreach ($texts as $text) { ?>
                                <li><p><?= $text ?></p></li>
                            <?php }
                        } ?>
                    </ul>
                </div>
                <div class="terms">
                    copyright 2023. <a href="https://github.com/proxima2182" target="_blank">proxima2182</a> all rights
                    reserved.
                </div>
            </div>
        </footer>
    </div>
</div>

</body>
</html>
