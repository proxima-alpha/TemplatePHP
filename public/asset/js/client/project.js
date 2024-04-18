$(document).ready(function () {
    const $slick = $('#artists .slick');
    $slick.slick({
        infinite: false,
        autoplay: true,
        draggable: true,
        slidesToShow: 6,
        duration: 2000
    })
    $slick.setVideoCoverStyle();
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
            const $container = $('.container-inner .artist-box');
            $container.empty();
            let html = `
                <div class="title-text-wrap">
                    <h3 class="name">${data.name}</h3>
                    <h4 class="job">${data.job}</h4>
                </div>
                <p class="content-text-wrap">${data.introduction.replaceAll('\n', '<br/>')}</p>`;

            if (data.previews.length > 0) {
                html += `
                <div class="content-wrap-inner slider-wrap">
                    <div class="slick-wrap">
                        <div class="slick">`;

                for (let preview of data.previews) {
                    let file_id = preview['id'];
                    let type = preview['type'];
                    let file_url = preview['relative_path'];
                    if(type == 'image') {
                        html += `
                        <div class="slick-item button"
                             style="background: url('${file_url}') no-repeat center; font-size: 0; background-size: cover;"
                             onclick="openImagePopup(${file_id})">
                        </div>`;
                    } else {
                        html += `
                        <div class="slick-item button">
                            <video preload="metadata">
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
            $container.setVideoCoverStyle();
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
