let default_identifier = '';
let files = {
    ids: {},
    identifiers: {},
    checkEmpty(key) {
        if (!this.ids[key]) {
            this.ids[key] = [];
        }
    },
    get(key) {
        this.checkEmpty(key);
        return this.ids[key];
    },
    set(key, index, value) {
        this.checkEmpty(key);
        this.ids[key][index] = value;
    },
    push(key, value) {
        this.checkEmpty(key);
        this.ids[key].push(value);
    },
    splice(key, index) {
        this.checkEmpty(key);
        this.ids[key].splice(index, 1);
    },
    clear() {
        this.ids = {};
        this.identifiers = {};
    },
    getIdentifier(key) {
        return this.identifiers[key];
    },
    setIdentifier(key, index, value) {
        this.identifiers[key] = value;
    },
};

function deleteUploadedSlickFile(id, key = 'topic', callback) {
    let index = files.get(key).indexOf(id);
    if (index < 0) return;
    if (callback && typeof callback === 'function') {
        callback();
    } else {
        let $slick = $('.slick.uploader');
        $slick.removeCustomSlickItem(index)
    }
    files.splice(key, index);
    // apiRequest({
    //     type: 'DELETE',
    //     url: `/api/file/delete/${id}`,
    //     dataType: 'json',
    //     success: function (response, status, request) {
    //         openPopupErrors('popup-error', response, status, request);
    //     },
    //     error: function (response, status, error) {
    //         openPopupErrors('popup-error', response, status, error);
    //     },
    // });
}

function deleteUploadedImageFile(target, id, accept) {
    let index = files.get(target).indexOf(id);
    console.log(accept)
    if (index < 0) return;
    files.splice(target, index);

    let $container = $(`.uploader.${target}`);
    $container.empty();
    $container.append(`
    <div class="upload-item-add"
         style="background: url('/asset/images/icon/plus_circle_big.png') no-repeat center; font-size: 0;">
        <label for="${target}-file" class="button"></label>
        <input type="file" name="file" multiple id="${target}-file"
               onchange="onFileUpload(this, '${target}');"
               accept="${accept}"/>
    </div>`);
}

//todo make callback
function onFileUpload(
    element,
    target = 'topic',
    callback) {
    if (element.files.length == 0) return;
    let form = new FormData();
    for (let i in element.files) {
        let file = element.files[i];
        form.append('file', file);
    }

    const identifier = default_identifier;
    files.setIdentifier(target, identifier);

    form.append('target', target)

    apiRequest({
        type: 'POST',
        url: `/api/file/${target}/upload/${identifier}`,
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
            let data = response.data;
            let file_id = data.id;
            let mime_type = data.mime_type;
            let type = data.type;

            files.push(target, file_id.toString());

            if (callback && typeof callback == 'function') {
                callback(target, type, file_id.toString(), mime_type);
            } else {
                let file_url = type == 'video' ? `/file/${file_id}/thumbnail` : `/file/${file_id}`
                let option = type == 'video' ? `background-size: cover;` : `background-size: contain;`
                let $uploader = $(`.uploader.${target}`);

                if ($uploader.attr('class').includes('slick')) {
                    let index = $uploader.attr('total') - 1;
                    $uploader.addCustomSlickItem(index,
                        `<div class="slick-item draggable-item upload-item" draggable="true"
                        style="background: url('${file_url}') no-repeat center;font-size: 0;${option}">
                        Slider #${file_id}
                        <input hidden type="text" name="id" value="${file_id}">
                        <div class="upload-item-hover">
                            <a href="javascript:deleteUploadedSlickFile('${file_id}', '${target}')"
                               class="button delete-image black">
                                <img src="/asset/images/icon/cancel_white.png"/>
                            </a>
                        </div>
                    </div>`);

                    $uploader.initDraggable({
                        onDragFinished: generateOnDragFinished(target),
                    });
                } else {
                    $uploader.empty();
                    $uploader.append(`
                    <div class="upload-item" style="background: url('${file_url}') no-repeat center;font-size: 0;background-size: cover;">
                        <div class="upload-item-hover">
                            <a href="javascript:deleteUploadedImageFile('${target}', '${file_id}', 'image/png,image/jpg')"
                               class="button delete-image black">
                                <img src="/asset/images/icon/cancel_white.png"/>
                            </a>
                        </div>
                    </div>`);
                }
            }
            // reset input file
            element.type = ''
            element.type = 'file'
        },
        error: function (response, status, error) {
            openPopupErrors('popup-error', response, status, error);
            // reset input file
            element.type = ''
            element.type = 'file'
        },
    });
}

function dropEditingFiles(target = 'topic', callback) {
    if (isEmpty(default_identifier)) return;
    apiRequest({
        type: 'POST',
        url: `/api/file/${target}/refresh/${default_identifier}`,
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) {
                openPopupErrors('popup-error', response, status, request);
                return;
            }
            if (callback && typeof callback == 'function') callback();
        },
        error: function (response, status, error) {
            openPopupErrors('popup-error', response, status, error);
        },
    });
}

function confirmEditFiles(target = 'topic', callback) {
    const identifier = files.getIdentifier(target);
    if (isEmpty(identifier)) return;
    apiRequest({
        type: 'POST',
        url: `/api/file/${target}/confirm/${identifier}`,
        data: {
            files: files.get(target),
        },
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) {
                openPopupErrors('popup-error', response, status, request);
                return;
            }
            if (callback && typeof callback == 'function') callback();
        },
        error: function (response, status, error) {
            openPopupErrors('popup-error', response, status, error);
        },
    });
}

function generateOnDragFinished(target) {
    return async (from, to) => {
        function getInputValue(parent) {
            let elements = parent.getElementsByTagName('input');
            if (elements.length == 0) return null;
            return elements[0].value;
        }

        let fromId = getInputValue(from);
        let toId = getInputValue(to);
        if (!fromId || !toId) {
            throw Error("can't find id value");
            return false;
        }

        let fromIndex = files.get(target).indexOf(fromId);
        let toIndex = files.get(target).indexOf(toId);
        if (fromIndex < 0 || toIndex < 0) {
            throw Error("can't find id value in temporary stored array");
            return false;
        }
        files.set(target, fromIndex, toId);
        files.set(target, toIndex, fromId);

        let temp = from.style.background;
        from.style.background = to.style.background;
        to.style.background = temp;
        return true;
    }
}

// async function onDragFinished(from, to) {
//     function getInputValue(parent) {
//         let elements = parent.getElementsByTagName('input');
//         if (elements.length == 0) return null;
//         return elements[0].value;
//     }
//
//     let fromId = getInputValue(from);
//     let toId = getInputValue(to);
//     if (!fromId || !toId) {
//         throw Error("can't find id value");
//         return false;
//     }
//
//     let key = 'image';
//     let fromIndex = files.get(key).indexOf(fromId);
//     let toIndex = files.get(key).indexOf(toId);
//     if (fromIndex < 0 || toIndex < 0) {
//         throw Error("can't find id value in temporary stored array");
//         return false;
//     }
//     files.set(key, fromIndex, toId);
//     files.set(key, toIndex, fromId);
//
//     let temp = from.style.background;
//     from.style.background = to.style.background;
//     to.style.background = temp;
//     return true;
// };

window.onbeforeunload = function () {
    dropEditingFiles('all')
}
