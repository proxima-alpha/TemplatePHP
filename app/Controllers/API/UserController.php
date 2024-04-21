<?php

namespace API;

use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Models\UserModel;
use Models\VerificationCodeModel;

class UserController extends BaseApiController
{
    protected UserModel $userModel;
    protected VerificationCodeModel $verificationCodeModel;

    public function __construct()
    {
        $this->userModel = model('Models\UserModel');
        $this->verificationCodeModel = model('Models\VerificationCodeModel');
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
                return $this->typicallyUpdate($this->userModel, $this->session->user_id, $data);
            }
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
    public function updateUser($id): ResponseInterface
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
            'username' => [
                'label' => 'Username',
                'rules' => 'required|min_length[5]|max_length[50]',
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
                $users = $this->userModel->get(['email' => $data['email']]);
                if (sizeof($users) > 0) {
                    throw new Exception('this email is already in used.');
                }
                $data['password'] = password_hash($data['password'], '2y', ["cost" => 5]);
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
            'username' => [
                'label' => 'Username',
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
                $users = $this->userModel->get(['username' => $data['username']]);
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
            'username' => [
                'label' => 'Username',
                'rules' => 'required|min_length[1]',
            ],
//            'code' => [
//                'label' => 'Code',
//                'rules' => 'required',
//            ],
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
                $users = $this->userModel->get(['username' => $data['username']]);
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
            'username' => [
                'label' => 'Username',
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
                $users = $this->userModel->get(['username' => $data['username']]);
                if (sizeof($users) == 0) {
                    throw new Exception('username is registered.');
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

}
