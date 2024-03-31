<?php

namespace App\Helpers;

final class HtmlHelper
{
    public static function getPagination($pagination, $pagination_link): string
    {
        $data = [];
        if (isset($pagination)) {
            $data['pagination'] = $pagination;
        }
        if (isset($pagination_link)) {
            if (Utils::endsWith($pagination_link, '/')) {
                $pagination_link = substr_replace($pagination_link, '', -1);
            }
            $data['pagination_link'] = $pagination_link;
        }
        return View("pagination", $data);
    }

    public static function getReply($topic_id, $input): string
    {
        $data = [];
        $data['topic_id'] = $topic_id;
        if (isset($input)) {
            $pagination = $input['pagination'];
            if (isset($pagination['page']) &&
                isset($pagination['per-page']) &&
                isset($pagination['total']) &&
                isset($pagination['total-page'])) {
                $data['pagination'] = [
                    'page' => $pagination['page'],
                    'per-page' => $pagination['per-page'],
                    'total' => $pagination['total'],
                    'total-page' => $pagination['total-page'],
                ];
            }
            if (isset($input['array'])) {
                $data['array'] = $input['array'];
            }
        }
        return View("reply", $data);
    }

    public static function covertTextarea($string)
    {
        return str_replace('\n', '&#10;', $string);
    }

    public static function showDataEmpty($data, $height = null): bool
    {
        if (!isset($data) || is_array($data) && sizeof($data) == 0) {
            self::showMessage('err_empty_folder', 'No data available.', $height);
            return false;
        }
        return true;
    }

    public static function showMessage($icon, $text, $height = null): void
    {
        $style = '';
        if (isset($height)) {
            $style = ' style="height : ' . $height . 'px; line-height: ' . ($height - 2) . 'px;"';
        }
        echo '<div class="no-data-box"' . $style . '>
                <div class="no-data-wrap">
                    <img src="/asset/images/icon/' . $icon . '.png">
                    <span>' . $text . '</span>
                </div>
            </div>';
    }

    private static function getDefaultTranslationKeys(): array
    {
        return [
            //common
            'confirm', 'cancel', 'edit', 'delete',
            // user popup
            'username', 'type', 'name', 'email',
            // popup control buttons
            'message_popup_delete',
        ];
    }

    public static function setTranslations(array $translations = []): void
    {
        if (sizeof($translations) == 0) return;
        $result = '
        <script type="text/javascript">
        // set common translations
        translations = {
            ...translations,
            ';
        foreach ($translations as $key) {
            $result .= "'" . $key . "' : '" . lang('Service.' . $key) . "',\n";
        }
        $result .= '}
        </script>
        ';
        echo $result;
    }

    public static function setTranslationsAdmin(array $translations = [])
    {
        $defaultTranslations = HtmlHelper::getDefaultTranslationKeys();
        return HtmlHelper::setTranslations(array_merge($defaultTranslations, $translations));
    }

    public static function setTranslationsClient(array $translations = [])
    {
        $defaultTranslations = array_merge(HtmlHelper::getDefaultTranslationKeys(), [
            // login
            'password', 'register', 'password_forget', 'login',
        ]);
        return HtmlHelper::setTranslations(array_merge($defaultTranslations, $translations));
    }


    public static function getPaginationLink($pagination_link, $number, $params = null, $pagination_key = null): string
    {
        if (isset($params) && $pagination_key) {
            $param_string = '';
            $prefix = '';
            foreach ($params as $key => $value) {
                if ($key == $pagination_key) {
                    $param_string .= $prefix . $key . "=" . $number;
                } else {
                    $param_string .= $prefix . $key . "=" . $value;
                }
                $prefix = "&";
            }
            return $pagination_link . "?" . $param_string;
        } else {
            return $pagination_link . "/" . $number;
        }
    }

    public static function getImageUploader($key, $file_id, $view_mode = 'input'): string
    {
        $html = '<div class="uploader ' . $key . '">';
        if ($view_mode == 'input') {
            if (isset($file_id)) {
                $html .=
                    '<div class="upload-item"
                         style="background: url(\'/file/' . $file_id . '\') no-repeat center;font-size: 0;background-size: cover;">
                        <div class="upload-item-hover">
                            <a href="javascript:deleteUploadedImageFile(\'' . $key . '\',\'' . $file_id . '\',\'image/png,image/jpg\')"
                               class="button delete-image black">
                                <img src="/asset/images/icon/cancel_white.png"/>
                            </a>
                        </div>
                    </div>';
            } else {
                $html .=
                    '<div class="upload-item-add"
                         style="background: url(\'/asset/images/icon/plus_circle_big.png\') no-repeat center; font-size: 0;">
                        <label for="artist_profile-file" class="button"></label>
                        <input type="file" name="file" multiple id="artist_profile-file"
                               onchange="onFileUpload(this, \'' . $key . '\');"
                               accept="image/png,image/jpg"/>
                    </div>';
            }
        } else {
            if (isset($file_id)) {
                $html .=
                    '<div class="upload-item button"
                        style="background: url(\'/file/' . $file_id . '\') no-repeat center;font-size: 0;background-size: cover;"
                        onclick="openImagePopup(\'' . $file_id . '\')">
                        Slider #' . $file_id . '
                    </div>';
            } else {
                return '';
            }
        }
        $html .= '</div>';
        return $html;
    }

    public static function getSlickUploader($key, $files, $view_mode = 'input'): string
    {
        $html = '';
        if ($view_mode == 'input') {
            $html .= '<div class="slider-wrap">
                    <div class="slick uploader ' . $key . '">';
            if (isset($files)) {
                foreach ($files as $index => $file) {
                    $url = $file['type'] == 'image' ? '/file/' . $file['id'] : '/file/' . $file['id'] . '/thumbnail';
                    $html .=
                        '<div class="slick-item draggable-item upload-item" draggable="true"
                            style="background: url(\'' . $url . '\') no-repeat center; background-size: cover; font-size: 0;">
                            Slider #' . $file['id'] . '
                            <input hidden type="text" name="id" value="' . $file['id'] . '">
                            <div class="upload-item-hover">
                                <a href="javascript:deleteUploadedSlickFile(' . $file['id'] . ', \'' . $key . '\')"
                                   class="button delete-image black">
                                    <img src="/asset/images/icon/cancel_white.png"/>
                                </a>
                            </div>
                        </div>';
                }
            }
            $html .=
                '<div class="slick-item upload-item-add"
                     style="background: url(\'/asset/images/icon/plus_circle_big.png\') no-repeat center; font-size: 0;">
                    <label for="artist_preview-file" class="button"></label>
                    <input type="file" name="file" multiple id="artist_preview-file"
                           onchange="onFileUpload(this, \'' . $key . '\');"
                           accept="video/*,image/png,image/jpg"/>
                </div>';
            $html .=
                '</div>
            </div>';
        } else {
            $html .=
                '<div class="slider-wrap">
                        <div class="slick">';
            if (isset($files)) {
                foreach ($files as $index => $file) {
                    $url = $file['type'] == 'image' ? '/file/' . $file['id'] : '/file/' . $file['id'] . '/thumbnail';
                    $html .=
                        '<div class="slick-item button"
                            style="background: url(\'' . $url . '\') no-repeat center; background-size: cover; font-size: 0;"
                            onclick="openImagePopup(' . $file['id'] . ', \'' . $file['type'] . '\', \'' . $file['mime_type'] . '\')">
                            Slider # ' . $file['id'] . ' 
                        </div>';
                }
            }
        $html .=
            '</div>
        </div>';
        }
        return $html;
    }
}
