<?php

namespace App\Libraries\Admin;
use App\Models\AdminModel;
use App\Models\ShinsaModel;
use App\Models\TaikaiModel;
use App\Models\KyokaiModel;
use App\Models\SeminarModel;
use App\Models\MemberModel;

class AdminLibrarie
{
	private $adminModel;
	private $noticeModel;
	private $shinsaModel;
	private $taikaiModel;
    private $kyokaiModel;
    private $seminarModel;
	private $memberModel;
    protected $_session;
    protected $errorMessage = "";

	public function __construct(){
		$this->adminModel = model(AdminModel::class);
        $this->noticeModel = model(NoticeModel::class);
        $this->shinsaModel = model(ShinsaModel::class);
        $this->taikaiModel = model(TaikaiModel::class);
        $this->kyokaiModel = model(KyokaiModel::class);
        $this->seminarModel = model(SeminarModel::class);
        $this->memberModel = model(MemberModel::class);
        $this->_session = session();
	}

	/**
	 * 未経過大会・審査等の一覧情報取得
     * @param   int     $noticeCategoryId  お知らせカテゴリーID
	 * @return array
	 */
    public function get_unexpired_event_list(int $noticeCategoryId): array
    {
        $result = false;
        
        switch ($noticeCategoryId) {
            case NOTICE_CATEGORY_ID_KASUGAI :
                // 未経過の協会行事一覧情報取得
                $result = $this->kyokaiModel->get_unexpired_kyokai_event_list();
                break;
            case NOTICE_CATEGORY_ID_SHINSA :
                // 未経過の審査一覧情報取得
                $result = $this->shinsaModel->get_unexpired_shinsa_list();
                break;
            case NOTICE_CATEGORY_ID_TAIKAI :
                // 未経過の大会一覧情報取得
                $result = $this->taikaiModel->get_unexpired_taikai_list();
                break;
            case NOTICE_CATEGORY_ID_SEMINAR :
                // 未経過の講習会一覧情報取得
                $result = $this->seminarModel->get_unexpired_seminar_list();
                break;
            case NOTICE_CATEGORY_ID_TRAINING :
                // 未経過の研修会一覧情報取得
                $result = $this->seminarModel->get_unexpired_training_list();
                break;
            case NOTICE_CATEGORY_ID_OTHER :
                // 未経過のその他一覧情報取得
                $result = $this->kyokaiModel->get_unexpired_other_event_list();
                break;
        }

        return $result;
    }

	/**
	 * メール配信処理
     * @param   array   $sendMailMemberList     メール配信対象者リスト
     * @param   string  $mailTitle   メールタイトル
     * @param   string  $mailTBody   メール本文
	 * @return bool
	 */
    public function send_mail_proc(array $sendMailMemberList, string $mailTitle, string $mailTBody): bool
    {
        $result = false;
        
        if (empty($sendMailMemberList) === false) {

            // メール配信対象者リストをBCCにセット
            $mailBcc = implode(',', $sendMailMemberList);

            mb_language("uni");
            $email = \Config\Services::email();
            $email->setFrom(SEND_MAIL_FROM, SEND_MAIL_FROM_NAME);
            $email->setTo(SEND_MAIL_TO);
            $email->setBcc($mailBcc);
            $email->setSubject($mailTitle);
            $email->setMessage($mailTBody);
            $result = $email->send();
        }

        return true;
    }

}
