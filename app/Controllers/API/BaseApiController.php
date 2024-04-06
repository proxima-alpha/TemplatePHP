<?php

namespace API;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use Models\BaseModel;

class BaseApiController extends BaseController
{
    /**
     * 주어진 model 을 이용해 한 개의 table row 를 가져오는 기능
     * - library 에서 정의 된 find 함수 이용
     * @param BaseModel $model
     * @param string $id
     * @return ResponseInterface
     */
    protected function typicallyFind(BaseModel $model, string $id): ResponseInterface
    {
        $response = [
            'success' => false,
        ];

        try {
            $result = $model->find($id);
            if (!$result) throw new Exception('not exist');
            $response['success'] = true;
            $response['data'] = $result;
        } catch (Exception $e) {
            //todo(log)
            $response['message'] = $e->getMessage();
        }
        return $this->response->setJSON($response);
    }

    /**
     * 주어진 model 을 이용해 한 개의 table row 를 가져오는 기능
     * - custom 된 get 함수 이용
     * @param BaseModel $model
     * @param string $id
     * @return ResponseInterface
     */
    protected function typicallyGet(BaseModel $model, string $id, array $condition = null): ResponseInterface
    {
        $response = [
            'success' => false,
        ];

        try {
            if (!isset($condition)) {
                $condition = [];
            }
            $condition['id'] = $id;
            $result = $model->get($condition);
            if (sizeof($result) == 0) {
                throw new Exception("not exist");
            }
            $response['success'] = true;
            $response['data'] = $result[0];
        } catch (Exception $e) {
            //todo(log)
            $response['message'] = $e->getMessage();
        }
        return $this->response->setJSON($response);
    }

    /**
     * 주어진 model 을 이용해 table row 생성하는 기능
     * @param BaseModel $model
     * @param array $data
     * @param array|null $validationRules
     * @param $doAdditionalCheck // Validation Rules 외에 체크가 필요한 부분을 위해 추가
     * @return ResponseInterface
     */
    protected function typicallyCreate(BaseModel $model, array $data, array $validationRules = null, $doAdditionalCheck = null): ResponseInterface
    {
        $response = [
            'success' => false,
        ];

        if ($validationRules != null && !$this->validate($validationRules)) {
            $response['messages'] = $this->validator->getErrors();
        } else {
            try {
                if ($doAdditionalCheck != null && is_callable($doAdditionalCheck)) {
                    $doAdditionalCheck($model, $data);
                }
                if (isset($data['id'])) unset($data['id']);
                if (!$model->insert($data)) {
                    $response['messages'] = $model->errors();
                } else {
                    $response['success'] = true;
                }
            } catch (Exception $e) {
                //todo(log)
                $response['message'] = $e->getMessage();
            }
        }

        return $this->response->setJSON($response);
    }

    /**
     * 주어진 model 을 이용해 table row 를 업데이트 하는 기능
     * @param BaseModel $model
     * @param string $id
     * @param array $data
     * @return ResponseInterface
     */
    protected function typicallyUpdate(BaseModel $model, string $id, array $data, array $validationRules = null, $doAfterUpdate = null): ResponseInterface
    {
        $response = [
            'success' => false,
        ];

        if (strlen($id) == 0) {
            $response['message'] = "field 'id' should not be empty.";
        } else {
            if ($validationRules != null && !$this->validate($validationRules)) {
                $response['messages'] = $this->validator->getErrors();
            } else {
                try {
                    if (isset($data['id'])) unset($data['id']);
                    $model->update($id, $data);
                    if ($doAfterUpdate != null && is_callable($doAfterUpdate)) {
                        $doAfterUpdate($model, $data);
                    }
                    $response['success'] = true;
                } catch (Exception $e) {
                    //todo(log)
                    $response['message'] = $e->getMessage();
                }
            }
        }
        return $this->response->setJSON($response);
    }

    /**
     * 주어진 model 을 이용해 table row 를 삭제하는 기능
     * @param BaseModel $model
     * @param string $id
     * @return ResponseInterface
     */
    protected function typicallyDelete(BaseModel $model, string $id): ResponseInterface
    {
        $response = [
            'success' => false,
        ];

        if (strlen($id) == 0) {
            $response['message'] = "field 'id' should not be empty.";
        } else {
            try {
                $model->delete($id);
                $response['success'] = true;
            } catch (Exception $e) {
                //todo(log)
                $response['message'] = $e->getMessage();
            }
        }
        return $this->response->setJSON($response);
    }

    protected function checkAdmin(): void
    {
        if (!$this->session->is_admin) {
            $response = Services::response();
            $response->setJSON([
                'success' => false,
                'message' => 'action is not allowed',
            ]);
            $response->sendBody();
            exit;
        }
    }

    protected function checkSelfAccess(string $user_id, bool $skip_for_admin = false): void
    {
        if ((!$skip_for_admin || !$this->session->is_admin) && $this->session->user_id != $user_id) {
            $response = Services::response();
            $response->setJSON([
                'success' => false,
                'message' => 'action is not allowed',
            ]);
            $response->sendBody();
            exit;
        }
    }

    #[NoReturn] protected function handleException(Exception $e)
    {
        switch ($e->getMessage()) {
            case 'wrongTimeFormat' :
                $response = Services::response();
                $response->setJSON([
                    'success' => false,
                    'message' => lang('Errors.wrongTimeFormat'),
                ]);
                $response->sendBody();
                exit;
            default :
            {
                $response = Services::response();
                $response->setJSON([
                    'success' => false,
                    'message' => lang('Errors.internalServerError'),
                ]);
                exit;
            }
        }

    }
}
