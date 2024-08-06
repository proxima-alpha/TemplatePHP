function onRewardFileUpload(
    element,
    id) {
    if (element.files.length == 0) return;
    let form = new FormData();
    for (let i in element.files) {
        let file = element.files[i];
        form.append('file', file);
    }

    apiRequest({
        type: 'POST',
        url: `/api/reward-file/upload/${id}`,
        data: form,
        processData: false,
        contentType: false,
        cache: false,
        dataType: "json",
        success: function (response, status, request) {
            if (!response.success) {
                openPopupErrors('popup-error', response, status, request);
                return;
            }
            const data = response.data;
            $(`#${id} .upload-item-add span`).remove()
            $(`#${id} .upload-item-add`).append(`
            <video preload="metadata">
                <source>
            </video>
            `)
            let $uploader = $(`#${id} .upload-item-add`);
            let $videos = $uploader.find('video');
            for (let i = 0; i < $videos.length; ++i) {
                const $video = $videos.eq(i);
                $video[0].addEventListener('loadeddata', function () {
                    const timeoutId = setTimeout(() => {
                        onVideoLoaded(this, data.id, data.width, data.height)
                        clearTimeout(timeoutId)
                    }, 100);
                });
            }
            let fileObject = window.URL.createObjectURL(element.files[0])
            $uploader.find('video source').attr('src', fileObject)
        },
        error: function (response, status, error) {
            openPopupErrors('popup-error', response, status, error);
        },
    });
}

function onVideoLoaded(element, file_id, width, height) {
    $(`.loading-wrap`).css({
        display: 'none'
    })
    const data = createPoster(element, width, height)
    apiRequest({
        type: 'POST',
        url: `/api/reward-file/update/${file_id}`,
        data: {
            poster: data
        },
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) {
                openPopupErrors('popup-error', response, status, request);
                return;
            }
            window.location.reload();
        },
        error: function (response, status, error) {
            openPopupErrors('popup-error', response, status, error);
        },
    });
}

function confirmReward(id) {
    apiRequest({
        type: 'POST',
        url: `/api/purchase-item-reward/confirm/${id}`,
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) {
                openPopupErrors('popup-error', response, status, request);
                return;
            }
            window.location.reload();
        },
        error: function (response, status, error) {
            openPopupErrors('popup-error', response, status, error);
        },
    });
}

function confirmDelete(url) {
    apiRequest({
        type: 'DELETE',
        url: url,
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) {
                openPopupErrors('popup-error', response, status, request);
                return;
            }
            window.location.reload();
        },
        error: function (response, status, error) {
            openPopupErrors('popup-error', response, status, error);
        },
    });
}
