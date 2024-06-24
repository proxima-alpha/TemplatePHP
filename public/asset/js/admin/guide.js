let guideData = {};
$(document).ready(function () {
    refresh(() => {
        refreshSetting()
    });
});

function editSetting(target) {
    if (isEmpty(target)) return;
    let $parent = $(`.content-box.${target}`)
    let $container = $parent.find(`.content-wrap`);
    $container.empty()

    $parent.addClass('editing')

    refreshSetting();
}

function refresh(callback) {
    apiRequest({
        type: 'GET',
        url: `/api/setting/guide`,
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) return;
            guideData = response.data
            if (callback && typeof callback == 'function') callback();
        },
        error: function (response, status, error) {
        },
    });
}

function refreshViews(target) {
    refresh(() => {
        let $parent = $(`.content-box.${target}`)
        $parent.removeClass('editing')
        refreshSetting()
    })
}

function confirmSettingEdit(target) {
    if (isEmpty(target) || !guideData[target]) return;
    let data = {};
    if (guideData[target]['quill']) {
        data[target] = {}
        if (guideData[target]['quill']['ko']) {
            data[target]['ko'] = guideData[target]['quill']['ko'].getSemanticHTML();
        }
        if (guideData[target]['quill']['en']) {
            data[target]['en'] = guideData[target]['quill']['en'].getSemanticHTML();
        }
    }
    apiRequest({
        type: 'POST',
        url: `/api/setting/guide`,
        data: data,
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) {
                openPopupErrors('popup-error', response, status, request);
                return;
            }
            refreshViews(target);
        },
        error: function (response, status, error) {
            openPopupErrors('popup-error', response, status, error);
        },
    });
}

function setEditing($parent, target) {
    let $container = $parent.find(`.content-wrap`);
    $container.append(`
    <div class="tab-box">
        <div class="tab-button-wrap">
            <a class="button ko active" onclick="clickTab(this,'ko')">한국어</a>
            <a class="button en" onclick="clickTab(this,'en')">English</a>
        </div>
        <div class="tab-wrap ko active">
            <div class="input-wrap">
                <div id="editor-ko" class="quill-editor"></div>
            </div>
        </div>
        <div class="tab-wrap en">
            <div class="input-wrap">
                <div id="editor-en" class="quill-editor"></div>
            </div>
        </div>
    </div>`);

    let $wrapButtonControls = $parent.find(`.control-button-wrap`);
    $wrapButtonControls.empty();
    $wrapButtonControls.append(`
    <a href="javascript:refreshViews('${target}');"
       class="button under-line cancel">
        <img src="/asset/images/icon/cancel.png"/>
        <span>${lang('cancel')}</span>
    </a>
    <a href="javascript:confirmSettingEdit('${target}');"
       class="button under-line confirm">
        <img src="/asset/images/icon/check.png"/>
        <span>${lang('confirm')}</span>
    </a>`)
    let quillKo, quillEn
    const quillOption = {
        modules: {
            toolbar: [
                [{header: [1, 2, false]}],
                ['bold', 'italic', 'underline'],
                ['image'],
                [{'list': 'ordered'}, {'list': 'bullet'}],
                [{'indent': '-1'}, {'indent': '+1'}],
                [{'color': []}, {'background': []},],
                [{'align': []}],
            ],
        },
        theme: 'snow', // or 'bubble'

    };
    quillKo = new Quill('#editor-ko', quillOption);
    quillKo.setContents(quillKo.clipboard.convert({html: guideData['how_to_use']['ko']}));
    quillEn = new Quill('#editor-en', quillOption);
    quillEn.setContents(quillKo.clipboard.convert({html: guideData['how_to_use']['en']}));
    guideData['how_to_use']['quill'] = {
        ko: quillKo,
        en: quillEn,
    }
}

function setView($parent, target) {
    let $container = $parent.find(`.content-wrap`);
    $container.append(`
    <div class="tab-box">
        <div class="tab-button-wrap">
            <a class="button ko active" onclick="clickTab(this,'ko')">한국어</a>
            <a class="button en" onclick="clickTab(this,'en')">English</a>
        </div>
        <div class="tab-wrap ko active">
            <div class="input-wrap">
                <div class="quill-html ql-container">${guideData['how_to_use']['ko']}</div>
            </div>
        </div>
        <div class="tab-wrap en">
            <div class="input-wrap">
                <div class="quill-html ql-container">${guideData['how_to_use']['en']}</div>
            </div>
        </div>
    </div>`);

    let $wrapButtonControls = $parent.find(`.control-button-wrap`);
    $wrapButtonControls.empty();
    $wrapButtonControls.append(`
    <a href="javascript:editSetting('${target}');"
       class="button under-line edit">
        <img src="/asset/images/icon/edit.png"/>
        <span>${lang('edit')}</span>
    </a>`)
}

function refreshSetting() {
    let refresh = (target) => {
        if (isEmpty(target)) return;
        let $parent = $(`.content-box.${target}`)
        if ($parent.length == 0) return;
        let $container = $parent.find(`.content-wrap`);
        $container.empty()

        if ($parent.hasClass('editing')) {
            setEditing($parent, target)
        } else {
            setView($parent, target)
        }
    }

    let targets = Object.keys(guideData);
    for (let i in targets) {
        refresh(targets[i])
    }
}
