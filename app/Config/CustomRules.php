<?php

namespace Config;

use CodeIgniter\Validation\StrictRules\Rules;

class CustomRules
{
    private Rules $rules;
    /**
     * バリデーション：ログインID・メールアドレスの重複チェック
     */
    public function check_member($loginId, $mailAddress, &$error = null)
    {
        $result = true;
        // $memberId = $this->_session->memberData['member_id'];
        // $result = $this->loginLibrarie->check_member($memberId, $loginId, $mailAddress);    
        if ($result === false) {
            // $this->set_message('check_member', 'ログインID・メールアドレスが重複しています');
            $error = 'ログインID・メールアドレスが重複しています';
        }
        return $result;
    }

    /**
     * バリデーション：会員名簿のメール配信チェック
     */
    public function check_member_list_send_mail($loginId, $mailAddress, &$error = null)
    {
        $result = true;
        // $memberId = $this->_session->memberData['member_id'];
        // $result = $this->loginLibrarie->check_member($memberId, $loginId, $mailAddress);    
        if ($result === false) {
            // $this->set_message('check_member', 'ログインID・メールアドレスが重複しています');
            $error = 'ログインID・メールアドレスが重複しています';
        }
        return $result;
    }

}
