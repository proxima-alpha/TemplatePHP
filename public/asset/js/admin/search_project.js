/** artist search **/

function searchProject(target, page = 1) {
    apiRequest({
        type: 'GET',
        url: `/api/project?page=${page}`,
        dataType: 'json',
        success: async function (response, status, request) {
            if (!response.success) {
                openPopupErrors('popup-error', response, status, request);
                return;
            }
            let data = response.data
            openProjectSearchPopup(target, data.array, data.pagination)
        },
        error: function (response, status, error) {
            openPopupErrors('popup-error', response, status, error);
        },
    });
}

function onSearchProjectSelected(className, target, id) {
    const $parent = $(`.${className} .table-wrap`)
    let $input = $parent.find('input[type=hidden]');
    if ($input != undefined) {
        $input.remove();
    }
    $parent.append(`<input type="hidden" name="${target}" value="${id}">`)
    const $selected = $parent.find('.selected')
    if ($selected != undefined) {
        $selected.removeClass('selected')
    }
    $parent.find(`.item-${id}`).addClass('selected')
}

async function openProjectSearchPopup(target, array, pagination) {
    let className = `popup-${target}-search`;

    function addPagination($parent) {
        if (typeof $parent === 'string') {
            $parent = $($parent);
        }
        if (!$parent ||
            !$parent.get(0) ||
            !pagination ||
            !pagination['total'] ||
            !pagination['page'] ||
            !pagination['total-page'] ||
            !pagination['per-page']) {
            return;
        }

        let page = pagination['page'];
        let total_page = pagination['total-page'];

        let start = Math.floor((page - 1) / 5) * 5 + 1;
        let end = (Math.floor((page - 1) / 5) + 1) * 5;
        end = Math.min(end, total_page);

        let html = "";
        html += `<div class="pages">`
        if (start == 1) {
            html += `<span class="button disabled"><a href="#" onclick="return false"></a></span>`;
        } else {
            html += `<span class="button left"><a href="javascript:searchArtist('${target}', ${start - 5})"></a></span>`;
        }
        for (let i = start; i <= end; ++i) {
            html += `<span class="number ${i == page ? 'now' : ''}"><a href="javascript:searchArtist('${target}', ${i})">${i}</a></span>`;
        }
        if (total_page == end) {
            html += `<span class="button disabled"><a href="#" onClick="return false"></a></span>`;
        } else {
            html += `<span class="button left"><a href="javascript:searchArtist('${target}', ${start - 5})"></a></span>`;
        }
        html += `</div>`;
        $parent.prepend(html);
    }

    function getHtml() {
        let html = '';
        if (array.length == 0) {
            html += `
            <div class="no-data-box">
                <div class="no-data-wrap">
                    <img src="/asset/images/icon/err_empty_folder.png">
                    <span>No data available.</span>
                </div>
            </div>`
        } else {
            html += `
            <div class="table-wrap">
                <div class="row-title">
                    <div class="row">
                        <span class="column status">${lang('상태')}</span>
                        <span class="column title">${lang('title')}</span>
                    </div>
                </div>
                <ul>`
            for (let item of array) {
                html += `
                <li class="row">
                    <a class="button row-button item-${item['id']}" href="javascript:onSearchProjectSelected('${className}', '${target}', ${item['id']});">
                        <span class="column status">${item['status']}</span>
                        <span class="column title">${item['title']}</span>
                    </a>
                </li>`
            }
            html += `</ul>`;
        }
        html += '</div>';
        html += `
        <div class="control-button-wrap absolute line-before">
            <div class="control-button-box">
                <a href="javascript:cancelProjectSearch('${className}', '${target}');"
                    class="button under-line cancel">
                    <img src="/asset/images/icon/cancel.png"/>
                    <span>${lang('cancel')}</span>
                </a>
               <a href="javascript:confirmProjectSearch('${className}', '${target}')" class="button confirm">
                    <img src="/asset/images/icon/check.png"/>
                    <span>${lang('confirm')}</span>
                </a>
            </div>
        </div>`;
        return html;
    }

    $parent = $(`.${className}`);
    if ($parent.length > 0) {
        $container = $parent.find('.popup-inner-wrap');
        $container.empty();
        $container.append(getHtml());
        addPagination($parent.find('.control-button-wrap'), pagination)
    } else {
        let css = await loadStyleFile('/asset/css/common/table.css', "." + className);
        css += await loadStyleFile('/asset/css/common/popup/search_project.css', "." + className);
        openPopup({
            className: className,
            commonClassName: 'popup-search',
            style: `<style>${css}</style>`,
            html: getHtml(),
        }, ($parent) => {
            addPagination($parent.find('.control-button-wrap'), pagination)
        })
    }
}

function cancelProjectSearch(className, target) {
    closePopup(className);
}

function confirmProjectSearch(className, target) {
    closePopup(className);
}
