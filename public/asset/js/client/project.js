$(document).ready(function () {

    $('body').setOnResolutionChanged((event) => {
        let isMobile = event.detail.isMobile;

        const $slick = $('#artists .slick');
        if ($slick.hasClass('slick-initialized')) {
            $slick.slick("unslick");
        }
        if(isMobile) {
            $slick.slick({
                infinite: false,
                autoplay: true,
                draggable: true,
                slidesToShow: 3,
                duration: 2000
            })
        } else {
            $slick.slick({
                infinite: false,
                autoplay: true,
                draggable: true,
                slidesToShow: 6,
                duration: 2000
            })
        }
        $slick.setVideoCoverStyle();
    })
});

function setArtist(id) {
    apiRequest({
        type: 'GET',
        url: `/api/artist/get/${id}`,
        dataType: 'json',
        success: async function (response, status, request) {
            if (!response.success) {
                openPopupErrors('popup-error', response, status, request);
                return;
            }
            let data = response.data
            const language = getCookie('lang');
            $('#artists .selected').removeClass('selected')
            $(`#artist-${id}`).addClass('selected')
            const $container = $('.container-inner .artist-box');
            $container.empty();
            let html = `
                <div class="title-text-wrap">
                    <h3 class="name">${language == 'ko' ? data.name : data.name_en}</h3>
                    <h4 class="job">${language == 'ko' ? data.job : data.job_en}</h4>
                </div>
                <p class="content-text-wrap">${(language == 'ko' ? data.introduction : data.introduction_en).replaceAll('\n', '<br/>')}</p>`;

            if (data.previews.length > 0) {
                html += `
                <div class="content-wrap-inner slider-wrap">
                    <div class="slick-wrap">
                        <div class="slick">`;

                for (let preview of data.previews) {
                    let file_id = preview['id'];
                    let type = preview['type'];
                    let file_url = preview['relative_path'];
                    if (type == 'image') {
                        html += `
                        <div class="slick-item button"
                             style="background: url('${file_url}') no-repeat center; font-size: 0; background-size: cover;"
                             onclick="openImagePopup(${file_id})">
                        </div>`;
                    } else {
                        html += `
                        <div class="slick-item button">
                            <video preload="metadata" controls style="width: 100%; height: 100%;">
                                <source src="${file_url}">
                            </video>
                            <p class="time-string">${secToString(preview['time'])}</p>
                        </div>`
                    }
                }
                html += `
                        </div>
                    </div>
                </div>`;
            }
            $container.append(html);
            const $slick = $container.find('.slick');
            if ($slick.length > 0) {
                $slick.slick({
                    infinite: false,
                    autoplay: false,
                    draggable: true,
                    slidesToShow: 4,
                })
            }
        },
        error: function (response, status, error) {
            openPopupErrors('popup-error', response, status, error);
        },
    });
}

function purchaseReward(id, start_date, end_date, available_count) {
    if (getCookie('is_login') != 1) {
        openPopupMessage(lang('message_error_login'))
        return
    }
    if (available_count <= 0) {
        return openPopupMessage(lang('message_error_exceed'))
    }
    const rawNowDate = new Date()
    if (!isEmpty(start_date)) {
        const rawStartDate = new Date(start_date)
        if (rawStartDate > rawNowDate) {
            return openPopupMessage(lang('message_error_not_started'))
        }
    }
    if (!isEmpty(end_date)) {
        const rawEndDate = new Date(end_date).setHours(23, 59, 59)
        if (rawEndDate < rawNowDate) {
            return openPopupMessage(lang('message_error_expired'))
        }
    }
    window.location.href = `/project/purchase/reward/${id}`;
}
