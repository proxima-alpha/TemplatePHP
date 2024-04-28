<?php

namespace API;

use CodeIgniter\HTTP\ResponseInterface;
use Crisu83\ShortId\ShortId;
use Exception;
use getID3;
use InvalidArgumentException;
use Models\BaseModel;
use Models\PurchaseItemModel;
use Models\RewardFileModel;

class RewardFileController extends BaseApiController
{

    protected RewardFileModel $rewardFileModel;
    protected PurchaseItemModel $purchaseItemModel;

    public function __construct()
    {
        $this->db = db_connect();
        $this->rewardFileModel = model('Models\RewardFileModel');
        $this->purchaseItemModel = model('Models\PurchaseItemModel');
    }

    /**
     * [post] /api/reward-file/upload/{purchase_item_id}
     * @param $purchaseItemId
     * @return ResponseInterface
     */
    public function uploadFile($purchaseItemId): ResponseInterface
    {
        $response = [
            'success' => false,
        ];
        try {
            $validationRules = $this->validate([
                'file' => [
                    'uploaded[file]',
                    'max_size[file,102400]',
                ],
            ]);
            $path = null;
            if ($validationRules) {
                $shortid = ShortId::create();
                $file = $this->request->getFile('file');
                $previousFile = $this->rewardFileModel->getLatest(['purchase_item_id' => $purchaseItemId]);

//                if (!$file->isValid()) {
//                    throw new \RuntimeException($file->getErrorString() . '(' . $file->getError() . ')');
//                }
                $file_name = $file->getClientName();
                $mime_type = $file->getClientMimeType();
                if (!isset($mime_type)) {
                    throw new Exception($file->getErrorString() . '(' . $file->getError() . ')');
                }
                $uploadedType = null;
                if (str_starts_with($mime_type, 'image')) {
                    $uploadedType = 'image';
                } else if (str_starts_with($mime_type, 'video')) {
                    $uploadedType = 'video';
                } else {
                    throw new Exception("not allowed mime type");
                }
                $symbolic_path = 'rewards/' . date("Y-m-d") . '/' . $shortid->generate();
                $path = ROOTPATH . 'public/' . $symbolic_path;
                mkdir($path, 0777, true);
                $file->move($path);

                $width = 0;
                $height = 0;
                $time = 0;
                switch ($uploadedType) {
                    case 'image' :
                        $size = getimagesize($path . '/' . $file_name);
                        $width = $size[0];
                        $height = $size[1];
                        break;
                    case 'video' :
                        $getID3 = new getID3;
                        $ThisFileInfo = $getID3->analyze($path . '/' . $file_name);
                        $getID3->CopyTagsToComments($ThisFileInfo);
                        $width = $ThisFileInfo['video']['resolution_x'];
                        $height = $ThisFileInfo['video']['resolution_y'];
                        $time = $ThisFileInfo['playtime_seconds'] ?? 0;
                        break;
                }

                $data = [
                    'purchase_item_id' => $purchaseItemId,
                    'type' => $uploadedType,
                    'file_name' => $file_name,
                    'relative_path' => '/' . $symbolic_path . '/' . $file_name,
                    'width' => $width,
                    'height' => $height,
                    'mime_type' => $mime_type,
                    'time' => $time,
                    'path' => $path,
                    'symbolic_path' => $symbolic_path,
                ];
                if (isset($target)) {
                    $data['target'] = $target;
                }
                $this->db->transBegin();
                $this->purchaseItemModel->update($purchaseItemId, ['status' => 'waiting']);
                $inserted_row_id = $this->rewardFileModel->insert($data);
                if (!$inserted_row_id) {
                    $response['messages'] = $this->rewardFileModel->errors();
                    throw new \Exception();
                }
                $response['success'] = true;
                $response['data'] = [
                    'id' => $inserted_row_id,
                    'mime_type' => $mime_type,
                    'type' => $uploadedType,
                    'relative_path' => '/' . $symbolic_path . '/' . $file_name,
                ];
                if (isset($previousFile)) {
                    $this->handleFileDelete("id = '" . $previousFile['id'] . "'");
                }
                $this->db->transCommit();
            } else {
                $response['messages'] = $this->validator->getErrors();
            }
        } catch (Exception $e) {
            //todo(log)
            $this->db->transRollback();
            if (!isset($response['message'])) {
                $response['message'] = $e->getMessage();
            }
            if (isset($path)) {
                $this->removeDirectory($path);
            }
        }
        return $this->response->setJSON($response);
    }

    /**
     * @param $id
     * @return ResponseInterface
     * @deprecated
     * [delete] /api/reward-file/delete/{id}
     */
    public function deleteFile($id): ResponseInterface
    {
        $this->checkAdmin();
        $response = [
            'success' => false,
        ];
        try {
            $result = $this->rewardFileModel->find($id);
            if ($result) {
                $this->removeDirectory($result['path']);
                $this->rewardFileModel->delete($id);
                $response['success'] = true;
            } else {
                $response['message'] = 'file does not exist.';
            }
        } catch (Exception $e) {
            //todo(log)
            $response['message'] = $e->getMessage();
        }
        return $this->response->setJSON($response);
    }

    /**
     * database 에서의 row 제거 및 파일 제거
     * @param $conditionQuery
     * @return void
     * @throws Exception
     */
    protected function handleFileDelete($conditionQuery): void
    {
        if ($conditionQuery == null || $conditionQuery == "") return;
        // 업로드 하였으나 할당되지 않은 이미지, 할당되어 있으나 할당을 제거할 이미지들에 대하여 정리
        // 삭제할 파일들 정보 미리 가져옴
        $files = BaseModel::transaction($this->db, [
            "SELECT * FROM reward_file WHERE " . $conditionQuery,
        ]);
        // 파일 삭제 실행
        foreach ($files as $item) {
            try {
                $this->removeDirectory($item['path']);
            } catch (Exception $e) {
                //todo log
            }
        }
        // data 삭제 실행
        BaseModel::transaction($this->db, [
            "DELETE FROM reward_file WHERE " . $conditionQuery,
        ]);
    }

    /**
     * 해당 path directory 내부의 모든 파일 제거하는 기능
     * @param string $path
     * @return void
     */
    private function removeDirectory(string $path): void
    {
        if (!is_dir($path)) {
            throw new InvalidArgumentException("$path must be a directory");
        }
        if (!str_ends_with($path, '/')) {
            $path .= '/';
        }
        $files = glob($path . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::removeDirectory($file);
            } else {
                unlink($file);
            }
        }
        rmdir($path);
    }
}
