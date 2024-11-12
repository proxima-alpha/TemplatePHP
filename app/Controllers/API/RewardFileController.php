<?php

namespace API;

use CodeIgniter\HTTP\ResponseInterface;
use Crisu83\ShortId\ShortId;
use Exception;
use getID3;
use InvalidArgumentException;
use Models\BaseModel;
use Models\PurchaseItemRewardModel;
use Models\RewardFileModel;

class RewardFileController extends BaseApiController
{

    protected RewardFileModel $rewardFileModel;
    protected PurchaseItemRewardModel $purchaseItemRewardModel;

    public function __construct()
    {
        $this->db = db_connect();
        $this->rewardFileModel = model('Models\RewardFileModel');
        $this->purchaseItemRewardModel = model('Models\PurchaseItemRewardModel');
    }

    /**
     * [post] /api/reward-file/update/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function update($id): ResponseInterface
    {
        $this->checkAdmin();
        $data = $this->request->getPost();
        return $this->typicallyUpdate($this->rewardFileModel, $id, $data);
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
                $previousFile = $this->rewardFileModel->getLatest(['purchase_item_reward_id' => $purchaseItemId]);

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
                    'purchase_item_reward_id' => $purchaseItemId,
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
                $this->purchaseItemRewardModel->update($purchaseItemId, ['status' => 'waiting']);
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
                    'width' => $width,
                    'height' => $height,
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

    public function handleChunkUpload($purchaseItemId): ResponseInterface
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

            if ($validationRules) {
                $file = $this->request->getFile('file');
                $chunkIndex = $this->request->getPost('chunkIndex');
                $totalChunks = $this->request->getPost('totalChunks');
                $fileName = $this->request->getPost('fileName');
                // 파일별로 고유한 임시 디렉토리 생성 (첫 청크에서만)
                $temp_id = $this->request->getPost('temp_id');
                if (empty($temp_id)) {
                    $temp_id = ShortId::create()->generate();
                }

                // 모든 청크가 같은 디렉토리에 저장되도록 함
                $symbolic_path = 'rewards/temp/' . date("Y-m-d") . '/' . $temp_id;
                $temp_path = ROOTPATH . 'public/' . $symbolic_path;

                if (!is_dir($temp_path)) {
                    mkdir($temp_path, 0777, true);
                }

                // 청크 파일 저장
                $chunk_file = $temp_path . '/' . $fileName . '.part' . $chunkIndex;
                $file->move(dirname($chunk_file), basename($chunk_file));

                // 모든 청크가 업로드되었는지 확인
                $allChunksUploaded = true;
                for ($i = 0; $i < $totalChunks; $i++) {
                    if (!file_exists($temp_path . '/' . $fileName . '.part' . $i)) {
                        $allChunksUploaded = false;
                        break;
                    }
                }

                $response['success'] = true;
                $response['temp_id'] = $temp_id;  // 클라이언트에 temp_id 반환
                $response['chunksReceived'] = $chunkIndex + 1;
                $response['totalChunks'] = $totalChunks;

                // 모든 청크가 업로드된 경우
                if ($allChunksUploaded) {
                    // 최종 파일 경로 설정
                    $final_symbolic_path = 'rewards/' . date("Y-m-d") . '/' . ShortId::create()->generate();
                    $final_path = ROOTPATH . 'public/' . $final_symbolic_path;
                    mkdir($final_path, 0777, true);

                    // 청크 파일들을 하나로 합치기
                    $final_file = $final_path . '/' . $fileName;
                    $out = fopen($final_file, 'wb');

                    for ($i = 0; $i < $totalChunks; $i++) {
                        $in = fopen($temp_path . '/' . $fileName . '.part' . $i, 'rb');
                        stream_copy_to_stream($in, $out);
                        fclose($in);
                        unlink($temp_path . '/' . $fileName . '.part' . $i);
                    }
                    fclose($out);

                    // 임시 디렉토리 삭제
                    $this->removeDirectory($temp_path);

                    // MIME 타입 확인
                    $mime_type = mime_content_type($final_file);
                    $uploadedType = null;

                    if (str_starts_with($mime_type, 'image')) {
                        $uploadedType = 'image';
                    } else if (str_starts_with($mime_type, 'video')) {
                        $uploadedType = 'video';
                    } else {
                        throw new Exception("not allowed mime type");
                    }

                    // 파일 정보 처리
                    $width = 0;
                    $height = 0;
                    $time = 0;

                    switch ($uploadedType) {
                        case 'image':
                            $size = getimagesize($final_file);
                            $width = $size[0];
                            $height = $size[1];
                            break;
                        case 'video':
                            $getID3 = new getID3;
                            $ThisFileInfo = $getID3->analyze($final_file);
                            $getID3->CopyTagsToComments($ThisFileInfo);
                            $width = $ThisFileInfo['video']['resolution_x'];
                            $height = $ThisFileInfo['video']['resolution_y'];
                            $time = $ThisFileInfo['playtime_seconds'] ?? 0;
                            break;
                    }

                    $previousFile = $this->rewardFileModel->getLatest(['purchase_item_reward_id' => $purchaseItemId]);

                    // DB 처리
                    $this->db->transBegin();

                    $data = [
                        'purchase_item_reward_id' => $purchaseItemId,
                        'type' => $uploadedType,
                        'file_name' => $fileName,
                        'relative_path' => '/' . $final_symbolic_path . '/' . $fileName,
                        'width' => $width,
                        'height' => $height,
                        'mime_type' => $mime_type,
                        'time' => $time,
                        'path' => $final_path,
                        'symbolic_path' => $final_symbolic_path,
                    ];

                    $this->purchaseItemRewardModel->update($purchaseItemId, ['status' => 'waiting']);
                    $inserted_row_id = $this->rewardFileModel->insert($data);

                    if (!$inserted_row_id) {
                        $response['messages'] = $this->rewardFileModel->errors();
                        throw new Exception();
                    }

                    if (isset($previousFile)) {
                        $this->handleFileDelete("id = '" . $previousFile['id'] . "'");
                    }

                    $this->db->transCommit();

                    $response['fileCompleted'] = true;
                    $response['data'] = [
                        'id' => $inserted_row_id,
                        'mime_type' => $mime_type,
                        'type' => $uploadedType,
                        'relative_path' => '/' . $final_symbolic_path . '/' . $fileName,
                        'width' => $width,
                        'height' => $height,
                    ];
                }
            } else {
                $response['messages'] = $this->validator->getErrors();
            }
        } catch (Exception $e) {
            $this->db->transRollback();
            if (!isset($response['message'])) {
                $response['message'] = $e->getMessage();
            }
            if (isset($temp_path)) {
                $this->removeDirectory($temp_path);
            }
            if (isset($final_path)) {
                $this->removeDirectory($final_path);
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
