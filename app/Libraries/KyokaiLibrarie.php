<?php

namespace App\Libraries;
use App\Libraries\CommonLibrarie;
use App\Models\KyokaiModel;

class KyokaiLibrarie
{
	private $kyokaiModel;
    protected $errorMessage = "";

	public function __construct(){
		$this->commonLibrarie = new CommonLibrarie();
		$this->kyokaiModel = model(KyokaiModel::class);
	}

    /**
     * 協会イベント登録年度一覧取得
     * @return array 協会イベント登録年度一覧
     */
    public function get_kyokai_event_regist_nendo_list($fiscalYearId, &$SelectfiscalYearData): array
    {
        $result = $this->kyokaiModel->get_kyokai_event_regist_nendo_list();
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
     * 協会イベント一覧情報取得
     * @param int $fiscalYearId     年度ID
     * @param array $subMemuData    サブメニュー情報
     * @return array 協会イベント一覧情報
     */
    public function get_kyokai_event_list(int $fiscalYearId, array $subMemuData): array
    {
        $result = array();

        if (empty($subMemuData) === false) {
            if ($subMemuData['numRows'] > 0) {
                // カテゴリー毎の協会イベント情報を取得
                foreach ($subMemuData['result'] as $idx => $data) {
                    // $result[$data['tab_name']] = $this->kyokaiModel->get_kyokai_event_list($fiscalYearId, $data['category_id']);
                    $result[$data['tab_name']][DATA_MIRAI] = $this->kyokaiModel->get_kyokai_event_list($fiscalYearId, $data['category_id']);
                    $result[$data['tab_name']][DATA_KAKO] = $this->kyokaiModel->get_kyokai_event_list($fiscalYearId, $data['category_id'], true);
                }
            }
        }
		
        return $result;
    }

    /**
     * イベント詳細情報取得
     * @param int $eventId      イベントID
     * @param int $memberId     会員ID
     * @return array イベント詳細情報
     */
    public function get_event_detail(int $eventId, int $memberId): array
    {
        $result = array();

        $result = $this->kyokaiModel->get_event_detail($eventId, $memberId);
        if (empty($result) === false) {
            $result['organizer_flg'] = false;
            // イベント幹事情報取得
            $eventOrganizerList = $this->kyokaiModel->get_event_organizer_list($eventId);
            if ($eventOrganizerList['numRows'] > 0) {
                foreach ($eventOrganizerList['result'] as $idx => $data) {
                    if ($data['member_id'] == $memberId) {
                        // 幹事の場合はフラグON
                        $result['organizer_flg'] = true;
                    }
                }
            }
            
            // 添付資料リスト取得
            $result['event_document_list'] = $this->kyokaiModel->get_event_document_list($eventId);
        }

        return $result;
    }

    /**
     * 参加申込・キャンセル登録
     * @param int       $eventId            イベントID
     * @param array     $memberData         会員情報
     * @param string    $requestMode        申込モード
     * @return 
     */
    public function event_request_member(int $eventId, array $memberData, string $requestMode): bool
    {
        $result = false;
        $mailSubject = '';
        $mailBody = '';
        $subjectAdd = '';
        
        switch ($requestMode) {
            case REQUEST_JOIN :
                // 審査申込登録
                $result = $this->kyokaiModel->event_join_member($eventId, $memberData['member_id']);
                if ($result === false) {
                    $this->errorMessage = '参加申込登録ができませんでした';
                }
                $mailBody .= "参加申込がありました\n\n";
                $mailSubject = '【イベント ： ' . REQUEST_NAME_JOIN . '】';
                break;
            case REQUEST_CANCEL :
                // 審査申込キャンセル登録
                $result = $this->kyokaiModel->event_cancel_member($eventId, $memberData['member_id']);
                if ($result === false) {
                    $this->errorMessage = '参加キャンセル登録ができませんでした';
                }
                $mailBody .= "参加キャンセルがありました\n\n";
                $mailSubject = '【イベント ： ' . REQUEST_NAME_CANCEL . '】';
                break;
        }

        if ($result === true) {

            $mailToList = array();
            $mailCcList = array();
            $mailBccList = array();

            // イベント幹事情報取得
            $eventOrganizerList = $this->kyokaiModel->get_event_organizer_list($eventId);
            if ($eventOrganizerList['numRows'] > 0) {

                foreach ($eventOrganizerList['result'] as $idx => $data) {
                    if ($data['organizer_main_flg'] === ORGANIZER_LEVEL_MAIN) {
                        // 主担当幹事はTO送信
                        array_push($mailToList, $data['mail_address']);
                    } else {
                        // その他幹事はCC送信
                        array_push($mailCcList, $data['mail_address']);
                    }
                }

                // 審査簡易詳細情報取得
                $eventDetail = $this->kyokaiModel->get_event_detail($eventId, $memberData['member_id']);
                if (empty($eventDetail) === false) {
                    if (empty($eventDetail['kyokai_event_name']) === false) {
                        $mailSubject .= $eventDetail['kyokai_event_name'] . ' ';
                    }
                    $mailSubject .= $eventDetail['event_sub_name'];
                    $mailBody .= "日程 ： " . date_period_short_format($eventDetail['event_date_st'], $eventDetail['event_date_ed']) . "\n";
                }
                
                $mailBody .= "\n";
                $mailBody .= "申込者 ： " . $memberData['name_f'] . " " . $memberData['name_s'] . "\n";
                
                // メール配信
                $result = $this->commonLibrarie->send_mail_proc($mailToList, $mailCcList, $mailBccList, $mailSubject, $mailBody);
            }
        }
		
        return $result;
    }

    /**
     * イベント参加者一覧情報取得
     * @param int       $eventId            イベントID
     * @return array イベント参加者一覧情報
     */
    public function get_event_offer_member_list(int $eventId): array
    {
        $result = array();

        $result = $this->kyokaiModel->get_event_offer_member_list($eventId);

        return $result;
    }

}
