
/**
 * topic 삭제 popup 여는 기능
 * @requires openPopup
 * @requires closePopup
 * @param url
 * @returns {Promise<void>}
 */
async function openPopupDelete(url) {
    let className = 'popup-delete';
    let css = await loadStyleFile('/asset/css/common/popup/delete.css', "." + className);
    let html = `
    <div class="text-wrap">
        Are you sure to delete?
    </div>`;
    html += `
    <div class="button-wrap controls">
        <a href="javascript:closePopup('${className}')" class="button cancel white">Cancel</a>
        <a href="javascript:confirmDelete('${url}')" class="button confirm black">Delete</a>
    </div>`;
    openPopup({
        className: className,
        style: `<style>${css}</style>`,
        html: html
    })
}

/**
 * topic 삭제 기능
 * @param id
 */
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
            history.back();
        },
        error: function (response, status, error) {
            openPopupErrors('popup-error', response, status, error);
        },
    });
}
