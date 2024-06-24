<?php

namespace Views\Admin;

use Exception;
use Models\ArtistGroupModel;
use Models\CodeProjectModel;
use Models\CustomFileModel;
use Models\ProjectModel;
use Models\RewardModel;

class GuideController extends BaseAdminController
{
    protected ProjectModel $projectModel;
    protected ArtistGroupModel $artistGroupModel;
    protected CodeProjectModel $codeProjectModel;
    protected RewardModel $rewardModel;
    protected CustomFileModel $customFileModel;

    public function __construct()
    {
        parent::__construct();
        $this->isRestricted = true;
        $this->projectModel = model('Models\ProjectModel');
        $this->codeProjectModel = model('Models\CodeProjectModel');
        $this->rewardModel = model('Models\RewardModel');
        $this->artistGroupModel = model('Models\ArtistGroupModel');
        $this->customFileModel = model('Models\CustomFileModel');
    }

    /**
     * /admin/guide
     * @return string
     */
    public function index(): string
    {
        $data = $this->getViewData();
        return parent::loadHeader([
                'css' => [
                    '/library/quill',
                    '/common/tab_box',
                    '/admin/guide',
                ],
                'js' => [
                    '/library/quill/quill.min.js',
                    '/common/tab_box',
                    '/admin/guide',
                ],
            ])
            . view('/admin/guide', $data)
            . parent::loadFooter();
    }
}
