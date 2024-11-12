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

function onChunkRewardFileUpload(element, id) {
    if (element.files.length == 0) return;
    const $progress = $(element).parent().find('.progress');
    const file = element.files[0];

    // 파일 크기 체크 (100MB)
    const maxSize = 100 * 1024 * 1024; // 100MB in bytes
    if (file.size > maxSize) {
        openPopupMessage(lang('message_file_too_big'))
        return;
    }

    const chunkSize = 1024 * 1024 * 4; // 4MB 청크
    const totalChunks = Math.ceil(file.size / chunkSize);
    let currentChunk = 0;
    let fileObject = window.URL.createObjectURL(file);
    let temp_id = null;  // 임시 ID 저장용

    async function uploadChunk() {
        const start = currentChunk * chunkSize;
        const end = Math.min(start + chunkSize, file.size);
        const chunk = file.slice(start, end);

        const form = new FormData();
        form.append('file', chunk);
        form.append('fileName', file.name);
        form.append('chunkIndex', currentChunk);
        form.append('totalChunks', totalChunks);
        if (temp_id) {  // 두 번째 청크부터는 temp_id 포함
            form.append('temp_id', temp_id);
        }


        apiRequest({
            type: 'POST',
            url: `/api/reward-file/chunk-upload/${id}`,
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
                // 첫 번째 청크 응답에서 temp_id 저장
                if (response.temp_id) {
                    temp_id = response.temp_id;
                }
                try {
                    // 진행률 표시 (필요한 경우)
                    const progress = ((currentChunk + 1) / totalChunks * 100).toFixed(2);
                    $progress.text(` ${progress}%`)
                    // 모든 청크가 완료되었고 서버에서 파일 처리가 완료된 경우
                    if (response.fileCompleted) {
                        const data = response.data;
                        $(`#${id} .upload-item-add span`).remove();
                        $(`#${id} .upload-item-add`).append(`
                            <video preload="metadata">
                                <source>
                            </video>
                        `);

                        let $uploader = $(`#${id} .upload-item-add`);
                        let $videos = $uploader.find('video');
                        for (let i = 0; i < $videos.length; ++i) {
                            const $video = $videos.eq(i);
                            $video[0].addEventListener('loadeddata', function () {
                                const timeoutId = setTimeout(() => {
                                    onVideoLoaded(this, data.id, data.width, data.height);
                                    clearTimeout(timeoutId);
                                }, 100);
                            });
                        }

                        $uploader.find('video source').attr('src', fileObject);
                        return;
                    }

                    // 다음 청크 업로드
                    currentChunk++;
                    if (currentChunk < totalChunks) {
                        uploadChunk();
                    }

                } catch (error) {
                    openPopupErrors('popup-error', error.responseJSON || error, error.status || 'error', error.statusText || null);
                }
            },
            error: function (response, status, error) {
                openPopupErrors('popup-error', response, status, error);
            },
        });
    }

    // 첫 청크 업로드 시작
    uploadChunk();
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
