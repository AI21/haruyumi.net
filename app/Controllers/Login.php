<?php

namespace App\Controllers;
use App\Libraries\LoginLibrarie;

class Login extends CommonController
{

	public function __construct() {
        parent::__construct();
		$this->loginLibrarie = new LoginLibrarie();
	}

    /**
     * 画面：ログイン
     */
    public function index()
    {
        // ログインチェック
        if ($this->loginLibrarie->login_check() === true) {
            // ログイン状態の場合はメインページに移動
            return redirect()->to('');
        }
        
		$data = [
            'fiscalYearId' => 0,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
			'officerFlg' => FLG_FALSE,
            'page' => array(),
            'headerCss' => array(),
            'footerJs' => array('login'),
            'nonMenu' => true
		];

        echo view('common/header', $data);
        echo view('common/menu');
        echo view('login/index');
        return view('common/footer');
    }

    /**
     * 画面：ログイン情報変更
     */
    public function change()
    {
        // ログインチェック
        if ($this->loginLibrarie->login_check() === false) {
            // ログインしていない状態の場合はログインページに移動
            return redirect()->to('login');
        }
        
		$data = [
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
			'memberData' => $this->_memberData,
			'officerFlg' => FLG_FALSE,
            'page' => array(),
            'headerCss' => array(),
            'footerJs' => array('login_change'),
		];
        
        echo view('common/header', $data);
        echo view('common/menu');
        echo view('login/change');
        return view('common/footer');
    }

    /**
     * Ajax：ログイン処理
     */
    public function ajax_login_process(): string
    {
        $result = false;
        $ret = [];
        $error = [];
        
        $rule = [
            'member_id_mail' => ['label' => 'メールアドレス or 会員ID', 'rules' => 'trim|required|max_length[128]'],
            'password' => ['label' => 'パスワード', 'rules' => 'trim|required|max_length[16]']
        ];
        if ($this->validate($rule) === true) {
            // ログイン処理
            $memberIdMail = $this->request->getPost('member_id_mail');
            $password = $this->request->getPost('password');
            $result = $this->loginLibrarie->login_process($memberIdMail, $password);
            if ($result === false) {
                $error['message'] = $this->loginLibrarie->_get_error_message();
            }
        } else {
            $error = $this->validator->getErrors();
        }

        $ret = [
            'result' => $result,
            'error' => $error
        ];
        return json_encode($ret);
    }

    /**
     * Ajax：ログイン情報変更チェック
     */
    public function ajax_login_change_check(): string
    {
        $result = false;
        $ret = [];
        $error = [];
        
        $rule = [
            'login_id' => ['label' => 'ログインID', 'rules' => 'alpha_numeric|min_length[4]|max_length[16]'],
            // 'mail_address' => ['label' => 'メールアドレス', 'rules' => 'valid_email'],
            'mail_address' => ['label' => 'メールアドレス', 'rules' => 'check_member'],
        ];
        if ($this->validate($rule) === true) {
            
            // 独自チェック
            $result = $this->loginLibrarie->login_change_check($this->request->getPost());

            if ($result === false) {
                $error['message'] = $this->loginLibrarie->_get_error_message();
            }
        } else {
            $error = $this->validator->getErrors();
        }

        $ret = [
            'result' => $result,
            'error' => $error
        ];
        return json_encode($ret);
    }

    /**
     * Ajax：ログイン情報変更処理
     * バリデーション処理は事前に行っているので、ここでは行わない
     */
    public function ajax_login_change_process(): string
    {
        $result = false;
        $ret = [];
        $error = [];

        // ログイン情報変更処理
        $result = $this->loginLibrarie->login_change_process($this->request->getPost());
        if ($result === false) {
            $error['login_change'] = $this->taikaiLibrarie->_get_error_message();
        }

        $ret = [
            'result' => $result,
            'error' => $error
        ];
        return json_encode($ret);
    }

    /**
     * バリデーション：ログインID・メールアドレスの重複チェック
     */
    public function _check_member()
    {
        $result = false;
        // $memberId = $this->_session->memberData['member_id'];
        // $result = $this->loginLibrarie->check_member($memberId, $loginId, $mailAddress);    
        if ($result === false) {
            $this->set_message('check_member', 'ログインID・メールアドレスが重複しています');
        }
    }

}
