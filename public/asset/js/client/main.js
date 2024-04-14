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

    $('#page-media .slick').slick({
        infinite: false,
        autoplay: true,
        draggable: true,
        slidesToShow: 6,
        duration: 2000
    })
    // // slick item 이 없는 경우 setOnResolutionChanged 이 작동하지 않으므로
    // // slick과 연관없는 기능들은 body에 연결해준다
    // $('body').setOnResolutionChanged((event) => {
    //     let isMobile = event.detail.isMobile;
    //
    //     setMainPageHeaderShape(mainPageIndex, mainPageNextIndex, isMobile)
    // })

    checkPagePopup();
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

function setMainPageHeaderShape(index, nextIndex, isMobile = false) {
    let $header = $('#header');
    $header.css({
        'animation-duration': '',
        'animation-name': '',
    })
    if (isMobile) {
        $header.removeClass('downsized');
    }
    if (index === 1) {
        let $body = $('body')
        if ($body && $header) {
            $header.remove()
            if (!isMobile) {
                $header.addClass('downsized');
            }
            $body.prepend($header)
            if (!isMobile) {
                $header.css({
                    'animation-duration': '0.2s',
                    'animation-name': 'headerSlideOut',
                });
            }
        }
    } else if (nextIndex == 1) {
        let $sectionStart = $('#page-start');
        if ($sectionStart && $header) {
            $header.remove();
            $header.removeClass('downsized')
            $sectionStart.prepend($header)
        }
    } else {
        if (!isMobile && !$header.hasClass('downsized')) {
            $header.addClass('downsized');
        }
    }
}

addEventListener("resize", (event) => {
    resizeWindow();
});


let bounds;

/**
 * map 에 포인트 추가하는 기능
 * points 는 따로 저장하지않고, bounds 만 저장 해 새 포인트 추가 시
 * 기존 map 을 재활용 하여 사용
 * @param points
 */
function setMapPoints(points) {
    if (!bounds) {
        bounds = new kakao.maps.LatLngBounds();
    }
    // let bounds = new kakao.maps.LatLngBounds();

    let i, marker;


    var imageSize = new kakao.maps.Size(20, 40),
        imageOptions = {
            spriteOrigin: new kakao.maps.Point(0, 0),
            spriteSize: new kakao.maps.Size(20, 40)
        };
    // 마커이미지와 마커를 생성합니다
    var markerImage = new kakao.maps.MarkerImage("/asset/images/icon/marker.png", imageSize, imageOptions);

    for (i = 0; i < points.length; i++) {
        // 배열의 좌표들이 잘 보이게 마커를 지도에 추가합니다
        let point = points[i];
        let mapPoint = new kakao.maps.LatLng(point.latitude, point.longitude);
        marker = new kakao.maps.Marker({
            position: mapPoint,
            image: markerImage,
        });
        marker.setMap(map);

        // LatLngBounds 객체에 좌표를 추가합니다
        bounds.extend(new kakao.maps.LatLng(point.latitude, point.longitude));
    }
    map.setMaxLevel(15);
    if (isMobile()) {
        map.setBounds(bounds, 0, 0, 0, 0);
    } else {
        map.setBounds(bounds, 0, 0, 0, 340);
    }
    // map.setLevel(map.getLevel() + 1);
    // map.setMinLevel(12)
    // map.setMaxLevel(13)
}

/**
 * main 페이지에서 가맹 문의 보내는 기능
 */
function requestMembership() {
    let inputData = parseInputToData($(`#page-last .form-wrap input, #page-last .form-wrap textarea`))
    let keys = ['name', 'phone_number', 'content'];
    for (let i in keys) {
        let key = keys[i];
        if (isEmpty(inputData[key])) {
            //todo error message
            return;
        }
    }
    apiRequest({
        type: 'POST',
        url: `/api/reservation/request`,
        data: {
            reservation_board_code: 'enquiry-membership',
            temp_name: inputData['name'],
            temp_phone_number: inputData['phone_number'],
            question_comment: inputData['content'],
        },
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) {
                openPopupErrors('popup-error', response, status, request);
                return;
            }
            $(`#page-last .form-wrap input, #page-last .form-wrap textarea`).val('');
            $(`#page-last .form-wrap input[type=checkbox]`).prop("checked", false);
            $(`#page-last .button-wrap .button`).addClass('disabled');
        },
        error: function (response, status, error) {
            openPopupErrors('popup-error', response, status, error);
        },
    });
}

/**
 * checkbox value 변경 listener
 * 약관 동의 시에만 예약 요청을 할 수 있도록 만들기 위한 기능
 * @param element
 */
function onMembershipInputValueChanged(element) {
    let $button = $(`#page-last .button-wrap .button`);
    if (!element.checked) {
        $button.addClass('disabled');
    } else {
        $button.removeClass('disabled');
    }
}

let mainPageMapTimeoutId;

function clearMobileMapTimeout() {
    if (mainPageMapTimeoutId) {
        clearTimeout(mainPageMapTimeoutId);
        mainPageMapTimeoutId = undefined;
    }
}

function openMobileMapDetailList() {
    clearMobileMapTimeout();
    if ($('#page-map .location-list-box').css('display') == 'none') {
        $('#page-map .location-list-box').css({
            'animation-duration': '0.2s',
            'animation-name': 'mainMapSlideDown',
            'display': 'block'
        });
        $('#page-map .page-title-wrap .button img').attr({
            'src': '/asset/images/icon/button_top.png'
        })
    } else {
        $('#page-map .location-list-box').css({
            'animation-duration': '0.2s',
            'animation-name': 'mainMapSlideUp',
        });
        adminNavigationTimeoutId = setTimeout(function () {
            $('#page-map .location-list-box').css({
                'display': 'none'
            });
            clearMobileMapTimeout();
        }, 200);
        $('#page-map .page-title-wrap .button img').attr({
            'src': '/asset/images/icon/button_bottom.png'
        })
    }
}
