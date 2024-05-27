let default_identifier = '';
let uploadData = {
    ids: {},
    extras: {},
    identifiers: {},
    checkEmpty(key) {
        if (!this.ids[key]) {
            this.ids[key] = [];
            this.extras[key] = [];
        }
    },
    get(key) {
        this.checkEmpty(key);
        return this.ids[key];
    },
    getKeys() {
        return Object.keys(this.ids);
    },
    getExtra(key) {
        this.checkEmpty(key);
        return this.extras[key];
    },
    set(key, index, value) {
        this.checkEmpty(key);
        const valueIndex = this.ids[key].indexOf(value);
        this.ids[key][index] = value;
        const tempType = this.extras[key][index];
        this.extras[key][index] = this.extras[key][valueIndex];
        this.extras[key][valueIndex] = tempType;
    },
    push(key, value, extra = null) {
        this.checkEmpty(key);
        this.ids[key].push(value);
        this.extras[key].push(extra);
    },
    splice(key, index) {
        this.checkEmpty(key);
        this.ids[key].splice(index, 1);
        this.extras[key].splice(index, 1);
    },
    clear() {
        this.ids = {};
        this.extras = {};
        this.identifiers = {};
    },
    clearItems() {
        for (const key in this.ids) {
            this.ids[key] = [];
            this.extras[key] = [];
            this.identifiers[key] = [];
        }
    },
    getIdentifier(key) {
        return this.identifiers[key];
    },
    setIdentifier(key, value) {
        this.identifiers[key] = value;
    },
};

function deleteUploadedSlickFile(target = 'topic', id) {
    let index = uploadData.get(target).indexOf(id.toString());
    if (index < 0) return;
    let $uploader = $(`.uploader.${target}`);
    if($uploader.hasClass('slick'))
    {
        $uploader.removeCustomSlickItem(index)
    }
    uploadData.splice(target, index);
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
    let index = uploadData.get(target).indexOf(id);
    if (index < 0) return;
    uploadData.splice(target, index);

    let $container = $(`.uploader.${target}`);
    $container.empty();
    $container.append(`
    <div class="upload-item-add"
         style="background: url('/asset/images/icon/plus_circle_big.png') no-repeat center; font-size: 0;">
        <label for="${target}-file" class="button"></label>
        <input type="file" name="file" id="${target}-file"
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
    uploadData.setIdentifier(target, identifier);

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
            const data = response.data;
            const file_id = data.id;
            const type = data.type;
            const relative_path = data.relative_path;
            const width = data.width;
            const height = data.height;

            uploadData.push(target, file_id.toString());

            if (callback && typeof callback == 'function') {
                callback(target, file_id.toString(), {
                    type: type,
                    relative_path: relative_path,
                    width: width,
                    height: height,
                });
            } else {
                let file_url = relative_path
                let $uploader = $(`.uploader.${target}`);

                if ($uploader.attr('class').includes('slick')) {
                    let index = $uploader.attr('total') - 1;
                    if (type == 'image') {
                        $uploader.addCustomSlickItem(index,
                            `<div class="slick-item draggable-item upload-item" draggable="true"
                        style="background: url('${file_url}') no-repeat center;font-size: 0; background-size: cover;">
                        Slider #${file_id}
                        <input hidden type="text" name="id" value="${file_id}">
                        <div class="upload-item-hover">
                            <a href="javascript:deleteUploadedSlickFile('${target}', '${file_id}')"
                               class="button delete-image black">
                                <img src="/asset/images/icon/cancel_white.png"/>
                            </a>
                        </div>
                    </div>`);
                    } else {
                        $uploader.addCustomSlickItem(index,
                            `<div class="slick-item draggable-item upload-item" draggable="true">
                                <video preload="metadata">
                                    <source src="${file_url}">
                                </video>
                            <input hidden type="text" name="id" value="${file_id}">
                            <div class="upload-item-hover">
                                <a href="javascript:deleteUploadedSlickFile('${target}', '${file_id}')"
                                   class="button delete-image black">
                                    <img src="/asset/images/icon/cancel_white.png"/>
                                </a>
                            </div>
                        </div>`);
                    }

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
    const identifier = uploadData.getIdentifier(target);
    apiRequest({
        type: 'POST',
        url: `/api/file/${target}/confirm${identifier ? `/${identifier}` : ''}`,
        data: {
            files: uploadData.get(target),
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

        let fromIndex = uploadData.get(target).indexOf(fromId);
        let toIndex = uploadData.get(target).indexOf(toId);
        if (fromIndex < 0 || toIndex < 0) {
            throw Error("can't find id value in temporary stored array");
            return false;
        }
        uploadData.set(target, fromIndex, toId);
        uploadData.set(target, toIndex, fromId);

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
