$(document).ready(function () {
    try {
        $('.slick.uploader').initDraggable({
            onDragFinished: generateOnDragFinished('artist_preview'),
        });
    } catch (e) {
        // do nothing
        // topic view page doesn't need initDraggable
    }
    let $slick = $('.slider-wrap .slick');
    $slick.setOnResolutionChanged((event) => {
        let isSwipe = $slick.hasClass('uploader') ? false : true;
        $slick.setCustomSlick(event.detail.isMobile, {
            infinite: false,
            autoplay: false,
            draggable: false,
            swipe: isSwipe,
        });
        try {
            $slick.initDraggable({
                onDragFinished: generateOnDragFinished('artist_preview'),
            });
        } catch (e) {
            // do nothing
            // topic view page doesn't need initDraggable
        }
    })
});

function deleteUploadedFile(uploader_key, id) {
    let index = files.get(uploader_key).indexOf(id);
    if (index < 0) return;
    files.splice(uploader_key, index);

    let $container = $(`.uploader.profile`);
    $container.empty();

    $container.append(`
    <div class="upload-item-add"
         style="background: url('/asset/images/icon/plus_circle_big.png') no-repeat center; font-size: 0;">
        <label for="${uploader_key}-file" class="button"></label>
        <input type="file" name="file" multiple id="${uploader_key}-file"
               onchange="onFileUpload(this, '${uploader_key}', '${uploader_key}', 'image', generateOnFileUploaded('${uploader_key}'));"
               accept="video/*;image/png;image/jpg"/>
    </div>`);
}

function generateOnFileUploaded(uploader_key) {
    return (target, type, file_id, mime_type) => {
        files.push(uploader_key, file_id);
        let $container = $(`.uploader.profile`);
        $container.empty();

        let file_url = type == 'video' ? `/file/${file_id}/thumbnail` : `/file/${file_id}`
        let option = type == 'video' ? `background-size: cover;` : `background-size: contain;`
        $container.append(`
        <div class="upload-item" style="background: url('${file_url}') no-repeat center;font-size: 0;${option}">
            <div class="upload-item-hover">
                <a href="javascript:deleteUploadedFile('${uploader_key}', '${file_id}')"
                   class="button delete-image black">
                    <img src="/asset/images/icon/cancel_white.png"/>
                </a>
            </div>
        </div>`);
    }
}
