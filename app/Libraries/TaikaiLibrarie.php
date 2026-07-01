<?php

namespace App\Libraries;
use App\Libraries\CommonLibrarie;
use App\Libraries\MemberLibrarie;
use App\Models\AdminModel;
use App\Models\TaikaiModel;

class TaikaiLibrarie
{
	private $adminModel;
	private $taikaiModel;
    protected $errorMessage = "";

	public function __construct(){
		$this->commonLibrarie = new CommonLibrarie();
		$this->memberLibrarie = new MemberLibrarie();
		$this->taikaiModel = model(TaikaiModel::class);
	}

    /**
     * 大会登録年度一覧取得
     * @return array 大会登録年度一覧
     */
    public function get_taikai_regist_nendo_list($fiscalYearId, &$SelectfiscalYearData): array
    {
        $result = $this->taikaiModel->get_taikai_regist_nendo_list();
        if (empty($result) === false && $result['numRows'] > 0) {
            foreach ($result['result'] as $idx => $data) {
                if ($data['fiscal_year_id'] == $fiscalYearId) {
                    // 選択年度がある場合は選択年度を設定
                    $SelectfiscalYearData = $data;
                }
            }
        }
        return $result;
    }

    /**
     * 大会一覧情報取得
     * @param int $fiscalYearId     年度ID
     * @param array $subMemuData    サブメニュー情報
     * @return array 大会一覧情報
     */
    public function get_taikai_list(int $fiscalYearId, array $subMemuData): array
    {
        $result = array();

        if (empty($subMemuData) === false) {
            if ($subMemuData['numRows'] > 0) {
                // カテゴリー毎＆未経過・過去の大会情報を取得
                foreach ($subMemuData['result'] as $idx => $data) {
                    $result[$data['tab_name']][DATA_MIRAI] = $this->taikaiModel->get_taikai_list($fiscalYearId, $data['category_id']);
                    $result[$data['tab_name']][DATA_KAKO] = $this->taikaiModel->get_taikai_list($fiscalYearId, $data['category_id'], true);
                }
            }
        }
		
        return $result;
    }

    /**
     * 大会詳細情報取得
     * @param int $taikaiId     大会ID
     * @param int $memberId     会員ID
     * @return array 大会詳細情報
     */
    public function get_taikai_detail(int $taikaiId, int $memberId): array
    {
        $result = array();

        // 大会詳細情報取得
        $result = $this->taikaiModel->get_taikai_detail($taikaiId, $memberId);

        // 大会関連資料一覧情報取得
        $taikaiDocumentList = $this->taikaiModel->get_taikai_document_list($taikaiId);
        if (empty($taikaiDocumentList) === false) {
            for ($i=0; $i<$taikaiDocumentList['numRows']; $i++) {
                $taikaiDocumentList['result'][$i]['ext_file'] = get_file_ext_icon_path($taikaiDocumentList['result'][$i]['document_ext']);
            }
            $result['taikai_document_list'] = $taikaiDocumentList;
        }
		
        return $result;
    }

    /**
     * 大会参加・キャンセル登録
     * @param int       $taikaiId     大会ID
     * @param array     $memberData   会員情報
     * @param string    $requestMode  申込モード
     * @return 
     */
    public function taikai_request_member(int $taikaiId, array $memberData, string $requestMode): bool
    {
        $result = false;
        $adminMailSubject = '';
        $adminMailBody = '';
        $subjectAdd = '';

        switch ($requestMode) {
            case REQUEST_JOIN :
                // 大会参加登録
                $result = $this->taikaiModel->taikai_join_member($taikaiId, $memberData['member_id']);
                if ($result === false) {
                    $this->errorMessage = '大会参加登録ができませんでした';
                }
                $adminMailSubject = '【大会 ： ' . REQUEST_NAME_JOIN . '】';
                $adminMailBody .= "大会の参加申込がありました\n\n";
                break;
            case REQUEST_CANCEL :
                // 大会キャンセル登録
                $result = $this->taikaiModel->taikai_cancel_member($taikaiId, $memberData['member_id']);
                if ($result === false) {
                    $this->errorMessage = '大会キャンセル登録ができませんでした';
                }
                $adminMailSubject = '【大会 ： ' . REQUEST_NAME_CANCEL . '】';
                $adminMailBody .= "大会の参加キャンセルがありました\n\n";
                break;
        }

        if ($result === true) {

            $mailToList = array();
            $mailCcList = array();
            $mailBccList = array();

            // 大会詳細情報取得
            $taikaiDetail = $this->taikaiModel->get_taikai_detail($taikaiId, 0);
            if (empty($taikaiDetail) === false) {
                
                // 協会役員
                $kyokaiOfficerMemberList = $this->memberLibrarie->get_kyokai_officer_member_list($taikaiDetail['kyokai_officer_id']);
                if (empty($kyokaiOfficerMemberList) === false) {
                    foreach ($kyokaiOfficerMemberList as $idx => $data) {
                        if ($data['officer_level'] === KYOKAI_OFFICER_LEVEL_BOSS) {
                            // 主担当幹事はTO送信
                            array_push($mailToList, $data['mail_address']);
                        } else {
                            // その他幹事でCC送信フラグがONの場合はCC送信
                            if ($data['mail_cc_flg'] === DB_FLG_ON) {
                                array_push($mailCcList, $data['mail_address']);
                            }
                        }
                    }
                }

                // メール件名・本文作成
                $adminMailSubject .= $taikaiDetail['taikai_name'] . ' ';
                $adminMailBody .= "大会名 ： " . $taikaiDetail['taikai_name'] . "\n";
                $adminMailBody .= "申請者 ： " . $memberData['name_f'] . " " . $memberData['name_s'] . "\n";
                
                $result = $this->commonLibrarie->send_mail_proc($mailToList, $mailCcList, $mailBccList, $adminMailSubject, $adminMailBody);
            }
            
        }
		
        return $result;
    }

    /**
     * 大会申請者一覧情報取得
     * @param int $taikaiId     大会ID
     * @return array 大会申請者一覧情報
     */
    public function get_taikai_offer_member_list(int $taikaiId): array
    {
        return $this->taikaiModel->get_taikai_offer_member_list($taikaiId);
    }

	/**
	 * ゲッター：エラーメッセージ
	 * @return string
	 */
    public function _get_error_message(): string
    {
        return $this->errorMessage;
    }

}
