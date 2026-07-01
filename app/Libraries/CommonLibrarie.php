<?php

namespace App\Libraries;
use App\Models\CommonModel;

class CommonLibrarie
{
	private $commonModel;

	public function __construct(){
		$this->commonModel = model(CommonModel::class);
	}

    /**
     * 基本設定情報取得
     * @return object 基本設定情報
     */
    public function get_setting_data(): object
    {		
        return $this->commonModel->get_setting_data();
    }

    /**
     * 称号・段位リスト取得
     * @return object 称号・段位リスト情報
     */
    public function get_holder_grade_list(): array
    {		
        return $this->commonModel->get_holder_grade_list();
    }

    /**
     * 会場一覧情報取得
     * @param int $kasugaiFlg    春日井弓道会会場フラグ(1:協会会場のみ取得、FLG_OFF0:全ての会場を取得)
     * @return array 会場一覧情報
     */
    public function get_kaijo_list($kasugaiFlg=FLG_OFF): array
    {
        $result = array('numRows' => 0, 'result' => array());
        $cnt = 0;

        // 会場一覧情報取得
        $kaijoList = $this->commonModel->get_kaijo_list();
        if (empty($kaijoList) === false && $kaijoList['numRows'] > 0) {
            if ($kasugaiFlg === FLG_OFF) {
                // 全ての会場をセット
                return $kaijoList;
            }
            // 協会会場のみ抽出
            foreach ($kaijoList['result'] as $idx => $data) {
                if ($data['order_kasugai'] === FLG_ON) {
                    $result['result'][$idx] = $data;
                    $cnt++;
                }
            }
            $result['numRows'] = $cnt;
        }

        return $result;
    }

    /**
     * 弓道協会役員リスト情報取得
     * @return array 弓道協会役員リスト
     */
    public function get_kyokai_officer_list(): array
    {		
        return $this->commonModel->get_kyokai_officer_list();
    }

	/**
	 * メール配信処理
     * @param   array   $sendMailMemberList     メール配信対象者リスト
     * @param   string  $mailTitle   メールタイトル
     * @param   string  $mailTBody   メール本文
	 * @return bool
	 */
    public function send_mail_proc(array $mailToList, array $mailCcList, array $mailBccList, string $mailTitle, string $mailTBody): bool
    {
        $result = false;
        
        if (empty($mailToList) === false) {

            // メール配信対象者リストをセット
            $mailTo = implode(',', $mailToList);
            $mailCc = implode(',', $mailCcList);
            $mailBcc = implode(',', $mailBccList);

            mb_language("uni");
            $email = \Config\Services::email();
            $email->setFrom(SEND_MAIL_FROM, SEND_MAIL_FROM_NAME);
            $email->setTo($mailTo);
            $email->setCc($mailCc);
            $email->setBcc($mailBcc);
            $email->setSubject($mailTitle);
            $email->setMessage($mailTBody);
            $result = $email->send();
        }

        return true;
    }
}
