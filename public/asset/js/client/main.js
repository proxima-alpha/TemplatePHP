/**
 * @file main.php
 */

/**
 * 스토리지 제어 함수 정의
 * @type {{set: handleStorage.set, has: (function(*): boolean)}}
 */
let handleStorage = {
    // 스토리지에 데이터 쓰기(이름, 만료일)
    set: function (name, exp) {
        // 만료 시간 구하기(exp를 ms단위로 변경)
        let date = new Date();
        date = date.setTime(date.getTime() + exp * 24 * 60 * 60 * 1000);

        // 로컬 스토리지에 저장하기
        // (값을 따로 저장하지 않고 만료 시간을 저장)
        localStorage.setItem(name, date)
    },
    // 스토리지 읽어오기
    has: function (name) {
        let now = new Date();
        now = now.setTime(now.getTime());
        // 현재 시각과 스토리지에 저장된 시각을 각각 비교하여
        // 시간이 남아 있으면 true, 아니면 false 리턴
        return parseInt(localStorage.getItem(name)) > now
    }
};

$(document).ready(function () {
    resizeWindow();
    checkPagePopup();

    // activate slick
    $('#page-start .main-slider-wrap .slick').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        prevArrow: false,
        nextArrow: false,
        autoplaySpeed: 2000,
        accessibility: false,
    });

    $('video').on('mouseenter', event => {
        event.target.play();
        event.target.setAttribute("controls", "controls")
    })
    $('video').on('mouseleave', event => {
        event.target.pause();
        if (event.target.hasAttribute("controls")) {
            event.target.removeAttribute("controls")
        }
    })
});

/**
 * popup 끄기 기능
 * @param className
 */
function closePagePopup(className) {
    let $parent = $(`.${className}`)
    $parent.remove()
    resizePagePopupWindow();
}

/**
 * page 용 popup 체크 기능
 */
function checkPagePopup() {
    apiRequest({
        type: 'GET',
        url: `/api/board/topic/get/popup`,
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) return;
            let array = response.array;
            for (let i in array) {
                let item = array[i]
                if (!handleStorage.has(`main-page-popup-${item['id']}`) && item.files.length > 0) {
                    let className = `page-popup-${hash()}`;
                    let html = `
                    <div class="slider-wrap">
                        <div class="slick">`;
                    //TODO add loop
                    for (let index in item.files) {
                        let file = item.files[index];
                        html += `<div class="slick-item" style="background: url('/file/${file['id']}') no-repeat center; background-size: cover; font-size: 0;">Slider #${index}</div>`
                    }
                    html += `
                        </div>
                    </div>
                    <div class="button-wrap">
                        <a href="javascript:closePagePopupTodayDisabled('${className}', ${item['id']})" class="button">
                            <span>${lang('message_popup_page')}</span>
                        </a>
                    </div>`
                    openPagePopup(className, null, html, function () {
                        $(`.page-popup.${className} .slick`).slick({
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            autoplay: true,
                            arrows: false,
                            autoplaySpeed: 5000,
                        });
                    })
                }
            }
            resizePagePopupWindow();
        },
        error: function (response, status, error) {
        },
    });
}

/**
 * page 용 popup 열기 기능
 */
function openPagePopup(className, style, html, callback) {
    $('body').append(`
    <div class="page-popup ${className}">
    ${style ?? ''}
        <div class="interface">
            <a href="javascript:closePagePopup('${className}')" class="button close">
                <img src="/asset/images/icon/cancel_white.png"/>
            </a>
        </div>
        <div class="popup-box">
            <div class="popup-inner">
            ${html ?? ''}
            </div>
        </div>
    </div>`)
    if (callback && typeof callback == 'function') callback();
}

/**
 * page 용 popup 재정렬 기능
 * resize 시 호출 되는 기능
 */
function resizePagePopupWindow() {
    let $popups = $('.page-popup')
    let left = 20;
    for (let i = 0; i < $popups.length; i++) {
        let popup = $popups.get(i);
        popup.style = `top: 20px; left: ${left}px`;
        left += 470;
    }
}

/**
 * page 용 popup '오늘 더이상 보지 않기' 기능
 * @param className
 * @param id
 */

function closePagePopupTodayDisabled(className, id) {
    let $popupWrap = $(`.${className}`)
    $popupWrap.remove()
    handleStorage.set(`main-page-popup-${id}`, 1)
    resizePagePopupWindow();
}

/**
 * window resize
 */
function resizeWindow() {
    // let $pageInners = $('.section .page-inner').not('.whole-page');
    // for (let i in $pageInners) {
    //
    //     let paddingTop = $pageInners.eq(i).css('padding-top') ?? '0';
    //     paddingTop = paddingTop.replaceAll('px', '');
    //
    //     let height = window.innerHeight - paddingTop;
    //     let $footer = $pageInners.eq(i).parent().find('#footer')
    //     if ($footer.length > 0) {
    //         height -= $footer.height();
    //         $pageInners.eq(i).css({
    //             'padding-bottom': `${$footer.height()}px`
    //         })
    //     }
    //     $pageInners.eq(i).css({
    //         'line-height': `${height}px`,
    //     })
    // }

    resizePagePopupWindow();
}

addEventListener("resize", (event) => {
    resizeWindow();
});

function onClickScrollLeft(element) {
    const $parent = $(element).parent().parent();
    const $container = $parent.find('.content-wrap');
    let offset = 0;
    if ($container.scrollLeft()) offset = $container.scrollLeft()
    offset -= $container.width()
    if (offset < 0) offset = 0
    $container.animate({scrollLeft: offset}, 500);
}

function onClickScrollRight(element) {
    const $parent = $(element).parent().parent();
    const $container = $parent.find('.content-wrap');
    let offset = 0;
    if ($container.scrollLeft()) offset = $container.scrollLeft()
    offset += $container.width()
    $container.animate({scrollLeft: offset}, 500);
}
