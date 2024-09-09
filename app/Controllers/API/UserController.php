<?php

namespace API;

use App\Helpers\GoogleAuthHelper;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Models\SettingModel;
use Models\UserModel;
use Models\VerificationCodeModel;

class UserController extends BaseApiController
{
    protected UserModel $userModel;
    protected VerificationCodeModel $verificationCodeModel;
    protected SettingModel $settingModel;

    public function __construct()
    {
        $this->userModel = model('Models\UserModel');
        $this->verificationCodeModel = model('Models\VerificationCodeModel');
        $this->settingModel = model('Models\SettingModel');
    }

    /**
     * [get] /api/user/get/profile
     * @return ResponseInterface
     */
    public function getProfile(): ResponseInterface
    {
        $response = [
            'success' => false,
        ];
        if (!$this->session->is_login) {
            $response['message'] = 'session is expired';
            return $this->response->setJSON($response);
        } else {
            return $this->typicallyFind($this->userModel, $this->session->user_id);
        }
    }

    /**
     * [post] /api/user/update/profile
     * @return ResponseInterface
     */
    public function updateProfile(): ResponseInterface
    {
        $response = [
            'success' => false,
        ];
        if (!$this->session->is_login) {
            $response['message'] = 'session is expired';
            return $this->response->setJSON($response);
        } else {
            $data = $this->request->getPost();
            $condition = ['is_deleted' => 0];
            if (isset($data['channel'])) {
                switch ($data['channel']) {
                    case 'kakao' :
                        $data['kakao_id'] = $data['channel_id'];
                        $condition = array_merge($condition, ['kakao_id' => $data['channel_id']]);
                        break;
                    case 'naver' :
                        $data['naver_id'] = $data['channel_id'];
                        $condition = array_merge($condition, ['naver_id' => $data['channel_id']]);
                        break;
                    case 'google' :
                        $data['google_id'] = $data['channel_id'];
                        $condition = array_merge($condition, ['google_id' => $data['channel_id']]);
                        break;
                }
            }
            if (sizeof($condition) > 0) {
                $user = $this->userModel->getLatest($condition);
                if ($user) {
                    $response['message'] = 'This account is already in used.';
                    return $this->response->setJSON($response);
                }
            }
            if ($this->session->is_admin && isset($data['email'])) {
                $user = $this->userModel->getLatest(['email' => $data['email'], 'is_deleted' => 0]);
                if (isset($user) && $user['id'] != $this->session->user_id) {
                    $response['message'] = 'This email is already in used.';
                    return $this->response->setJSON($response);
                }
            }
            return $this->typicallyUpdate($this->userModel, $this->session->user_id, $data);
            return $this->response->setJSON($response);
        }
    }

    /**
     * [get] /api/user/get/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function getUser($id): ResponseInterface
    {
        return $this->typicallyFind($this->userModel, $id);
    }

    /**
     * [post] /api/user/update/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function update($id): ResponseInterface
    {
        $response = [
            'success' => false,
        ];
        $validationRules = [
            'email' => [
                'label' => 'Email',
                'rules' => 'regex_match[[a-z0-9]+@[a-z]+\.[a-z]{2,3}]',
                'errors' => [
                    'regex_match' => '{field} format is not valid'
                ],
            ],
        ];

        if ($validationRules != null && !$this->validate($validationRules)) {
            $response['messages'] = $this->validator->getErrors();
        } else {
            $data = $this->request->getPost();
            return $this->typicallyUpdate($this->userModel, $id, $data);
        }
        return $this->response->setJSON($response);
    }

    /**
     * [delete] /api/user/delete/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function delete($id): ResponseInterface
    {
        $this->checkAdmin();
        $body = [
            'is_deleted' => 1,
        ];
        return $this->typicallyUpdate($this->userModel, $id, $body);
    }

    /**
     * 가장 마지막에 인증한 데이터 조회 및 체크 기능
     * @param string $email
     * @return array
     * @throws Exception
     */
    private function getLastVerificationCode(string $email, string $code): array
    {
        $result = $this->verificationCodeModel->getLatest(['email' => $email]);
        if (!$result) {
            throw new Exception('you have to send verification code first.');
        }
        if ($result['code'] != $code) {
            throw new Exception('please check your code again.');
        }
        $diff = time() - strtotime($result['created_at']);
        if ($diff > 600) {
            throw new Exception('code is already expired');
        }
        if ($result['is_used'] == 1) {
            throw new Exception('this code is already used');
        }
        return $result;
    }

    /**
     * [post] /api/user/registration/verify
     * registration step#01
     * @return ResponseInterface
     */
    public function verifyRegistration(): ResponseInterface
    {
        //TODO need to check user is already exist
        $data = $this->request->getPost();
        $validationRules = [
            'email' => [
                'label' => 'Email',
                'rules' => 'required|min_length[1]|regex_match[[a-z0-9]+@[a-z]+\.[a-z]{2,3}]',
                'errors' => [
                    'regex_match' => '{field} format is not valid'
                ],
            ],
            'code' => [
                'label' => 'Code',
                'rules' => 'required',
            ],
        ];

        $response = [
            'success' => false,
        ];
        if ($validationRules != null && !$this->validate($validationRules)) {
            $response['messages'] = $this->validator->getErrors();
        } else {
            try {
                $code = $this->getLastVerificationCode($data['email'], $data['code']);
                $this->verificationCodeModel->update($code['id'], [
                    'is_used' => 1,
                ]);
                $response['success'] = true;
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
                return $this->response->setJSON($response);
            }
        }
        return $this->response->setJSON($response);
    }

    /**
     * [post] /api/user/registration/register
     * registration step#02
     * @return ResponseInterface
     */
    public function register(): ResponseInterface
    {
        $data = $this->request->getPost();
        $validationRules = [
            'email' => [
                'label' => 'Email',
                'rules' => 'required|min_length[1]|regex_match[[a-z0-9]+@[a-z]+\.[a-z]{2,3}]',
                'errors' => [
                    'regex_match' => '{field} format is not valid'
                ],
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[8]',
            ],
            'confirm_password' => [
                'label' => 'Confirm Password',
                'rules' => 'required',
            ],
        ];

        $response = [
            'success' => false,
        ];
        if ($validationRules != null && !$this->validate($validationRules)) {
            $response['messages'] = $this->validator->getErrors();
        } else {
            try {
                if ($data['password'] != $data['confirm_password']) {
                    throw new Exception('please check two fields for \'password\' is same.');
                }
                $users = $this->userModel->get(['email' => $data['email'], 'is_deleted' => 0]);
                if (sizeof($users) > 0) {
                    throw new Exception('This email is already in used.');
                }
                $data['password'] = password_hash($data['password'], '2y', ["cost" => 5]);
                if (isset($data['channel'])) {
                    switch ($data['channel']) {
                        case 'kakao' :
                            $data['kakao_id'] = $data['channel_id'];
                            break;
                        case 'naver' :
                            $data['naver_id'] = $data['channel_id'];
                            break;
                        case 'google' :
                            $data['google_id'] = $data['channel_id'];
                            break;
                    }
                }
                $this->userModel->insert($data);
                $response['success'] = true;
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
            }
        }
        return $this->response->setJSON($response);
    }

    /**
     * [post] /api/user/reset-password/verify
     * reset password step#01
     * @return ResponseInterface
     */
    public function verifyResetPassword(): ResponseInterface
    {
        //TODO need to check user is already exist
        $data = $this->request->getPost();
        $validationRules = [
            'email' => [
                'label' => 'email',
                'rules' => 'required|min_length[1]',
            ],
            'code' => [
                'label' => 'Code',
                'rules' => 'required',
            ],
        ];

        $response = [
            'success' => false,
        ];
        if ($validationRules != null && !$this->validate($validationRules)) {
            $response['messages'] = $this->validator->getErrors();
        } else {
            try {
                $users = $this->userModel->get(['email' => $data['email'], 'is_deleted' => 0]);
                if (sizeof($users) == 0) {
                    throw new Exception('user does not exist');
                }
                if (sizeof($users) > 2) {
                    //todo error
                }
                $user = $users[0];

                $code = $this->getLastVerificationCode($user['email'], $data['code']);
                $this->verificationCodeModel->update($code['id'], [
                    'is_used' => 1,
                ]);
                $response['success'] = true;
                $response['data'] = [
                    'username' => $user['username']
                ];
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
            }
        }
        return $this->response->setJSON($response);
    }

    /**
     * [post] /api/user/reset-password/confirm
     * reset password step#02
     * @return ResponseInterface
     */
    public function confirmResetPassword(): ResponseInterface
    {
        //TODO need to check user is already exist
        $data = $this->request->getPost();
        $validationRules = [
            'email' => [
                'label' => 'Email',
                'rules' => 'required',
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[8]',
            ],
            'confirm_password' => [
                'label' => 'Confirm Password',
                'rules' => 'required',
            ],
        ];

        $response = [
            'success' => false,
        ];
        if ($validationRules != null && !$this->validate($validationRules)) {
            $response['messages'] = $this->validator->getErrors();
        } else {
            try {
                if ($data['password'] != $data['confirm_password']) {
                    throw new Exception('please check two fields for \'password\' is same.');
                }
                $users = $this->userModel->get(['email' => $data['email'], 'is_deleted' => 0]);
                if (sizeof($users) == 0) {
                    throw new Exception('user does not exist');
                }
                if (sizeof($users) > 2) {
                    //todo error
                }
                $user = $users[0];
                $data['password'] = password_hash($data['password'], '2y', ["cost" => 5]);
                $this->userModel->update($user['id'], $data);
                $response['success'] = true;
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
            }
        }
        return $this->response->setJSON($response);
    }

    /**
     * [post] /api/user/login
     * @return ResponseInterface
     */
    public function login(): ResponseInterface
    {
        $data = $this->request->getPost();
        $validationRules = [
            'email' => [
                'label' => 'Email',
                'rules' => 'required|min_length[5]|max_length[50]',
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required',
            ],
        ];

        $response = [
            'success' => false,
        ];
        if ($validationRules != null && !$this->validate($validationRules)) {
            $response['messages'] = $this->validator->getErrors();
        } else {
            try {
                //TODO 원래는 username 기반이어야 함, 추후 롤백 필요
//                $users = $this->userModel->get(['username' => $data['username'], 'is_deleted' => 0]);
//                if (sizeof($users) == 0) {
                $users = $this->userModel->get(['email' => $data['email'], 'is_deleted' => 0]);
//                }
                if (sizeof($users) == 0) {
                    throw new Exception('user is not registered.');
                }
                $user = $users[0];
                if (!password_verify($data['password'], $user['password'])) {
                    throw new Exception('password is not correct.');
                }
                $this->session->set([
                    'username' => $user['username'],
                    'user_email' => $user['email'],
                    'user_name' => strlen($user['name']) == 0 ? $user['username'] : $user['name'],
                    'user_id' => $user['id'],
                    'user_type' => $user['type'],
                    'is_login' => true,
                    'is_admin' => $user['type'] == 'admin' || $user['type'] == 'member',
                ]);
                $response['success'] = true;
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
                return $this->response->setJSON($response);
            }
        }
        return $this->response->setJSON($response);
    }

    /**
     * [post] /api/user/auto-login
     * @return ResponseInterface
     */
    public function autoLogin(): ResponseInterface
    {
        $data = $this->request->getPost();
        $validationRules = [
            'channel' => [
                'label' => 'Channel',
                'rules' => 'required',
            ],
            'channel_id' => [
                'label' => 'Channel Id',
                'rules' => 'required',
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'required',
            ],
        ];

        $response = [
            'success' => false,
        ];
        if ($validationRules != null && !$this->validate($validationRules)) {
            $response['messages'] = $this->validator->getErrors();
        } else {
            try {
                $users = [];
                switch ($data['channel']) {
                    case 'kakao':
                        $users = $this->userModel->get(['kakao_id' => $data['channel_id'], 'is_deleted' => 0]);
                        break;
                    case 'naver' :
                        $users = $this->userModel->get(['naver_id' => $data['channel_id'], 'is_deleted' => 0]);
                        break;
                    case 'google' :
                        $users = $this->userModel->get(['google_id' => $data['channel_id'], 'is_deleted' => 0]);
                        break;
                    default:
                        throw new Exception('This channel is not supported.');
                }
                if (sizeof($users) == 0) {
                    $compareUsers = $this->userModel->get(['email' => $data['email'], 'is_deleted' => 0]);
                    if (sizeof($compareUsers) > 0) {
                        throw new Exception('This email is already in used.');
                    }
                    throw new Exception('user is not registered.');
                }
                $user = $users[0];
                $this->session->set([
                    'username' => $user['username'],
                    'user_email' => $user['email'],
                    'user_name' => strlen($user['name']) == 0 ? $user['username'] : $user['name'],
                    'user_id' => $user['id'],
                    'user_type' => $user['type'],
                    'is_login' => true,
                    'is_admin' => $user['type'] == 'admin' || $user['type'] == 'member',
                ]);
                $response['success'] = true;
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
                return $this->response->setJSON($response);
            }
        }
        return $this->response->setJSON($response);
    }

    /**
     * [post] /api/user/logout
     * @return ResponseInterface
     */
    public function logout(): ResponseInterface
    {
        $response = [
            'success' => true,
        ];
        $this->session->set([
            'username' => null,
            'user_email' => null,
            'user_name' => null,
            'user_id' => null,
            'user_type' => null,
            'is_login' => null,
            'is_admin' => null,
        ]);
        return $this->response->setJSON($response);
    }

    /**
     * [post] /api/user/password-change
     * @return ResponseInterface
     */
    public function changePassword(): ResponseInterface
    {
        $data = $this->request->getPost();
        $validationRules = [
            'current_password' => [
                'label' => 'Current Password',
                'rules' => 'required|min_length[8]',
            ],
            'new_password' => [
                'label' => 'New Password',
                'rules' => 'required|min_length[8]',
            ],
            'confirm_new_password' => [
                'label' => 'Confirm New Password',
                'rules' => 'required',
            ],
        ];

        $response = [
            'success' => false,
        ];

        if (!$this->session->is_login) {
            $response['message'] = 'session is expired';
        } else {
            if ($validationRules != null && !$this->validate($validationRules)) {
                $response['messages'] = $this->validator->getErrors();
            } else {
                try {
                    if ($data['new_password'] != $data['confirm_new_password']) {
                        throw new Exception('please check two fields for \'new password\' is same.');
                    }
                    $user = $this->userModel->find($this->session->user_id);
                    if (!$user) {
                        throw new Exception('not exist');
                    }
                    if (!password_verify($data['current_password'], $user['password'])) {
                        throw new Exception('password is not correct.');
                    }
                    $this->userModel->update($this->session->user_id, [
                        'password' => password_hash($data['new_password'], '2y', ["cost" => 5]),
                    ]);
                    $response['success'] = true;
                } catch (Exception $e) {
                    $response['message'] = $e->getMessage();
                }
            }
        }
        return $this->response->setJSON($response);
    }

    public function getGoogleProfile()
    {
        $response = [
            'success' => false,
        ];

        $data = $this->request->getPost();
        if (!isset($data['code'])) throw new Exception('wrong parameter');
        $accessToken = GoogleAuthHelper::getToken($data['code'], $data['redirect_uri']);
        if (!$accessToken) throw new Exception('Google::Failed to issue access token.');
        $result = GoogleAuthHelper::getProfile($accessToken);
        if (!isset($result['error'])) {
            $response['success'] = true;
            $response['data'] = $result;
        } else {
            $result['message'] = 'Google::' . $response['error_description'];
        }
        return $this->response->setJSON($response);
    }

}
