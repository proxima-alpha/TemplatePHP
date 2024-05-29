<?php

namespace Views\Admin;

use App\Helpers\Utils;
use Exception;
use Models\ArtistGroupModel;
use Models\CodeProjectModel;
use Models\ProjectModel;
use Models\RewardModel;

class ProjectController extends BaseAdminController
{
    protected ProjectModel $projectModel;
    protected CodeProjectModel $codeProjectModel;
    protected RewardModel $rewardModel;
    protected ArtistGroupModel $artistGroupModel;

    public function __construct()
    {
        parent::__construct();
        $this->isRestricted = true;
        $this->projectModel = model('Models\ProjectModel');
        $this->codeProjectModel = model('Models\CodeProjectModel');
        $this->rewardModel = model('Models\RewardModel');
        $this->artistGroupModel = model('Models\ArtistGroupModel');
    }

    /**
     * /admin/project/{page}
     * @param $page
     * @return string
     */
    function index($page = 1): string
    {
        $page = Utils::toInt($page);
        $data = $this->getViewData();
        try {
            $result = $this->projectModel->getPaginated([
                'per_page' => $this->per_page,
                'page' => $page,
            ], [
                'is_deleted' => 0
            ]);
            $data = array_merge($data, $result);
            $data = array_merge($data, [
                'pagination_link' => '/admin/project',
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/table',
                    '/admin/project/table',
                ],
                'js' => [
                ],
            ])
            . view('/admin/project/table', $data)
            . parent::loadFooter();
    }

    /**
     * /admin/project/{id}/view
     * @param $id
     * @return string
     */
    public function get($id): string
    {
        $data = $this->getViewData();
        try {
            $data = array_merge($data, $this->getProjectData($id));
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }

        return parent::loadHeader([
                'css' => [
                    '/common/uploader',
                    '/common/uploader_slider_box',
                    '/common/row_uploader_item',
                    '/common/input',
                    '/common/tab_box',
                    '/admin/project/common',
                ],
                'js' => [
                    '/library/slick/slick.min.js',
                    '/module/slick_custom',
                    '/module/uploader',
                    '/common/tab_box',
                    '/common/delete',
                    '/admin/project',
                ],
            ])
            . view('/admin/project/view', $data)
            . parent::loadFooter();
    }

    /**
     * /admin/project/{id}/edit
     * @param $id
     * @return string
     */
    public function edit($id = 1): string
    {
        $data = $this->getViewData();
        try {
            $codes = $this->codeProjectModel->get();
            $data['code_project'] = $codes;
            $data = array_merge($data, $this->getProjectData($id));
            $data = array_merge($data, [
                'type' => 'edit'
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/uploader',
                    '/common/row_uploader_item',
                    '/common/input',
                    '/common/tab_box',
                    '/admin/project/common',
                    '/admin/project/input',
                ],
                'js' => [
                    '/library/slick/slick.min.js',
                    '/module/slick_custom',
                    '/module/calendar',
                    '/module/draggable',
                    '/module/uploader',
                    '/common/tab_box',
                    '/admin/search_artist',
                    '/admin/project_input',
                    '/admin/project',
                ],
            ])
            . view('/admin/project/input', $data)
            . parent::loadFooter();
    }

    /**
     * /admin/project/create
     * @return string
     */
    public function create(): string
    {
        $data = $this->getViewData();
        try {
            $codes = $this->codeProjectModel->get();
            $data['code_project'] = $codes;
            $data = array_merge($data, [
                'type' => 'create'
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/uploader',
                    '/common/row_uploader_item',
                    '/common/input',
                    '/common/tab_box',
                    '/admin/project/common',
                    '/admin/project/input',
                ],
                'js' => [
                    '/library/slick/slick.min.js',
                    '/module/slick_custom',
                    '/module/calendar',
                    '/module/draggable',
                    '/module/uploader',
                    '/common/tab_box',
                    '/admin/search_artist',
                    '/admin/project_input',
                    '/admin/project',
                ],
            ])
            . view('/admin/project/input', $data)
            . parent::loadFooter();
    }

    /**
     * artist 조회시 필요한 데이터 불러오는 기능
     * @throws Exception
     */
    private function getProjectData($id): array
    {
        $result = [];
        $projects = $this->projectModel->get(['id' => $id, 'is_deleted' => 0]);
        if (sizeof($projects) != 1) throw new Exception('deleted');
        $project = $projects[0];
        $artists = $this->artistGroupModel->getArtists($id);
        $rewards = $this->rewardModel->get(['project_id' => $id, 'is_deleted' => 0]);
        $project['artists'] = $artists;
        $project['rewards'] = $rewards;
        $result['data'] = $project;
        return $result;
    }
}
