<?php

namespace App\Libraries;
use App\Libraries\CommonLibrarie;
use App\Libraries\MemberLibrarie;
use App\Models\ShinsaModel;

class ShinsaLibrarie
{
	private $shinsaModel;
    protected $errorMessage = "";

	public function __construct(){
		$this->commonLibrarie = new CommonLibrarie();
		$this->memberLibrarie = new MemberLibrarie();
		$this->shinsaModel = model(ShinsaModel::class);
	}

    /**
     * 地区リスト取得
     * @param   int     $areaGroupLevel     地区グループレベル
     * @return  array   地区リスト
     */
    public function get_area_group_list(int $areaGroupLevel): array
    {
        $result = array();

        // 地方審査以外の場合は地区グループリストを取得
        if ($areaGroupLevel != SHINSA_AREA_GROUP_CHIHO) {
            $result = $this->shinsaModel->get_area_group_list($areaGroupLevel);
        }

        return $result;
    }

    /**
     * 審査名称リスト取得
     * @param   int     $areaGroupLevel     地区グループレベル
     * @return  array   審査名称リスト
     */
    public function get_shinsa_name_list(int $areaGroupLevel): array
    {
        $result = array();

        $result = $this->shinsaModel->get_shinsa_name_list($areaGroupLevel);
		
        return $result;
    }

    /**
     * 称号・段位グループリスト取得
     * @param   int     $shinsaClassId     審査区分ID
     * @return  array   称号・段位グループリスト
     */
    public function get_shinsa_holder_grade_list(int $shinsaClassId): array
    {
        $result = array();

        $result = $this->shinsaModel->get_shinsa_holder_grade_list($shinsaClassId);
		
        return $result;
    }

    /**
     * 称号リスト取得
     * @param   int     $holderId     称号ID
     * @return  array   称号リスト
     */
    public function get_holder_list(int $holderId): array
    {
        $result = array();

        $result = $this->shinsaModel->get_holder_list($holderId);
		
        return $result;
    }

    /**
     * 段位グループリスト取得
     * @param   int     $gradeGroupId     段位グループID
     * @return  array   段位グループリスト
     */
    public function get_grade_group_list(int $gradeGroupId): array
    {
        $result = array();

        $result = $this->shinsaModel->get_grade_group_list($gradeGroupId);
		
        return $result;
    }

    /**
     * 審査登録年度一覧取得
     * @return array 審査登録年度一覧
     */
    public function get_shinsa_regist_nendo_list($fiscalYearId, &$SelectfiscalYearData): array
    {
        $result = $this->shinsaModel->get_shinsa_regist_nendo_list();
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
     * 段位・連盟登録情報取得
     * @param   int     $memberId     会員ID
     * @return  array   審査申込対象情報
     */
    public function get_member_grade_data(int $memberId): array
    {
        $result = array();

        $result = $this->shinsaModel->get_member_grade_data($memberId);

        // 審査申込可能チェック
        $shinsaOfferChuou = true;   // 中央審査
        $shinsaOfferRengo = true;   // 連合審査
        $shinsaOfferChiho = true;   // 地方審査
        // 審査共通：全弓連休会中、全弓連IDなしの場合はNG
        if (
            $result['renmei_adjourning_flg'] === FLG_ON ||
            empty($result['renmei_id']) === true
        ) {
            $shinsaOfferChuou = false;
            $shinsaOfferRengo = false;
            $shinsaOfferChiho = false;
        }

        // 地方審査は愛弓連登録をしていない場合はNG
        if (empty($result['aiti_renmei_regist_flg']) === true) {
            $shinsaOfferChiho = false;
        }
        $result['shinsa_offer_chuou'] = $shinsaOfferChuou;
        $result['shinsa_offer_rengo'] = $shinsaOfferRengo;
        $result['shinsa_offer_chiho'] = $shinsaOfferChiho;
		
        return $result;
    }

    /**
     * 審査一覧情報取得
     * @param int   $fiscalYearId       年度ID
     * @param array $subMemuData        サブメニュー情報
     * @param int   $memberId           会員ID
     * 
     * @return array 会員一覧情報
     */
    public function get_shinsa_list(int $fiscalYearId, array $subMemuData, int $memberId): array
    {

        if (empty($subMemuData) === false) {
            if ($subMemuData['numRows'] > 0) {
                // カテゴリー毎＆未経過・過去の大会情報を取得
                foreach ($subMemuData['result'] as $idx => $data) {
                    $result[$data['tab_name']][SHINSA_INFORMATION] = $this->shinsaModel->get_shinsa_category_information($data['category_id']);
                    $result[$data['tab_name']][DATA_MIRAI] = $this->shinsaModel->get_shinsa_list($fiscalYearId, $data['category_id']);
                    $result[$data['tab_name']][DATA_KAKO] = $this->shinsaModel->get_shinsa_list($fiscalYearId, $data['category_id'], true);
                    // 審査種別毎の管理役員チェック
                    switch ($data['category_id']) {
                        case CATEGORY_ID_SHINSA_CHUOU :
                            $result[$data['tab_name']][DATA_OFFICER_FLG] = $this->memberLibrarie->chk_kyokai_officer_level(KYOKAI_OFFICER_ID_SHINSA_CHUOU, $memberId, KYOKAI_OFFICER_LEVEL_BOSS);
                            break;
                        case CATEGORY_ID_SHINSA_RENGO :
                            $result[$data['tab_name']][DATA_OFFICER_FLG] = $this->memberLibrarie->chk_kyokai_officer_level(KYOKAI_OFFICER_ID_SHINSA_RENGO, $memberId, KYOKAI_OFFICER_LEVEL_BOSS);
                            break;
                        case CATEGORY_ID_SHINSA_CHIHO :
                            $result[$data['tab_name']][DATA_OFFICER_FLG] = $this->memberLibrarie->chk_kyokai_officer_level(KYOKAI_OFFICER_ID_SHINSA_CHIHO, $memberId, KYOKAI_OFFICER_LEVEL_BOSS);
                            break;
                    }
                    // $result[$data['tab_name']][DATA_KAKO] = $this->memberLibrarie->chk_kyokai_officer_level(11, $memberId, KYOKAI_OFFICER_LEVEL_BOSS);
                }
            }
        }
		
        return $result;
    }

    /**
     * 審査詳細情報取得
     * @param int $shinsaId     審査ID
     * @param int $memberId     会員ID
     * @return array 審査詳細情報
     */
    public function get_shinsa_detail(int $shinsaId, int $memberId): array
    {
        $result = array();

        // 審査詳細情報取得
        $result = $this->shinsaModel->get_shinsa_detail($shinsaId, $memberId);
        // 審査が経過していて、参加している場合
        if (empty($result) === false && $result['future_flg'] === DB_FLG_OFF && $result['sanka_flg'] === DB_FLG_ON) {
            // 審査の合格対象の段位・級位を取得
            $result['pass_grade_group'] = $this->shinsaModel->get_grade_group($result['pass_grade_group_id']);
        }

        // 審査関連資料一覧情報取得
        $shinsaDocumentList = $this->shinsaModel->get_shinsa_document_list($shinsaId);
        if (empty($shinsaDocumentList) === false) {
            for ($i=0; $i<$shinsaDocumentList['numRows']; $i++) {
                $shinsaDocumentList['result'][$i]['ext_file'] = get_file_ext_icon_path($shinsaDocumentList['result'][$i]['document_ext']);
            }
            $result['shinsa_document_list'] = $shinsaDocumentList;
        }

        return $result;
    }

    /**
     * 審査申込対象一覧情報取得
     * @param   array     $shinsaDetail     審査詳細情報
     * @param   array     $memberGradeDeta  会員段位・連盟登録情報
     * @return  array     審査申込対象情報
     */
    public function get_shinsa_target_list(array $shinsaDetail, array $memberGradeDeta): array
    {
        $result = array();

        // 中央審査は審査日程から審査申込対象を取得
        if ($shinsaDetail['shinsa_class_id'] === SHINSA_CLASS_ID_CHUOU) {
            $result = $this->shinsaModel->get_shinsa_target_holder_list($shinsaDetail, $memberGradeDeta);
        } else {
            $result = $this->shinsaModel->get_shinsa_target_list($shinsaDetail, $memberGradeDeta);
        }

        return $result;
    }

    /**
     * 審査対象クラス一覧情報取得
     * @param   array     $holderGrade     審査種別情報
     * @return  array     審査申込対象情報
     */
    public function get_shinsa_target_all_list(array $holderGrade): array
    {
        $result = array();

        // 審査対象クラス一覧を取得
        if (empty($holderGrade) === false) {
            // 審査の対象種別から称号・段位グループIDを配列にする
            $holderGradeIdList = array();
            foreach ($holderGrade['result'] as $idx => $data) {
                $holderGradeIdList[] = $data['holder_grade_id'];
            }
            // 審査種別の称号・段位グループIDをもとに審査対象クラス一覧を取得
            $shinsaTargetAllList = $this->shinsaModel->get_shinsa_target_all_list($holderGradeIdList);
            if (empty($shinsaTargetAllList) === false && $shinsaTargetAllList['numRows'] > 0) {
                $result = $shinsaTargetAllList['result'];
            }	
        }

        return $result;
    }

    /**
     * 審査申込・キャンセル登録
     * @param int       $shinsaId           審査ID
     * @param int       $shinsaTargetId     審査対象ID
     * @param array     $memberData         会員情報
     * @param string    $requestMode        申込モード
     * @return 
     */
    public function shinsa_request_member(int $shinsaId, int $shinsaTargetId, array $memberData, string $requestMode): bool
    {
        $result = false;
        $adminMailSubject = '';
        $adminMailBody = '';
        $subjectAdd = '';
        
        switch ($requestMode) {
            case REQUEST_JOIN :
                // 審査申込登録
                $result = $this->shinsaModel->shinsa_join_member($shinsaId, $shinsaTargetId, $memberData['member_id']);
                if ($result === false) {
                    $this->errorMessage = '審査申込登録ができませんでした';
                }
                $adminMailBody .= "審査申込がありました\n\n";
                $adminMailSubject = '【審査 ： ' . REQUEST_NAME_JOIN . '】';
                break;
            case REQUEST_CANCEL :
                // 審査申込キャンセル登録
                $result = $this->shinsaModel->shinsa_cancel_member($shinsaId, $memberData['member_id']);
                if ($result === false) {
                    $this->errorMessage = '審査申込キャンセル登録ができませんでした';
                }
                $adminMailBody .= "審査申込キャンセルがありました\n\n";
                $adminMailSubject = '【審査 ： ' . REQUEST_NAME_CANCEL . '】';
                break;
        }

        if ($result === true) {

            $mailToList = array();
            $mailCcList = array();
            $mailBccList = array();

            // 審査申込対象情報取得
            $shinsaTargetData = $this->shinsaModel->get_shinsa_target($shinsaId, $shinsaTargetId);
            if (empty($shinsaTargetData) === false) {

                $kyokaiOfficerId = 0;
                switch ($shinsaTargetData['shinsa_class_id']) {
                    case SHINSA_CLASS_ID_CHUOU :
                        $kyokaiOfficerId = KYOKAI_OFFICER_ID_SHINSA_CHUOU;
                        break;
                    case SHINSA_CLASS_ID_RENGO :
                        $kyokaiOfficerId = KYOKAI_OFFICER_ID_SHINSA_RENGO;
                        break;
                    case SHINSA_CLASS_ID_CHIHO :
                    case SHINSA_CLASS_ID_VIDEO :
                        $kyokaiOfficerId = KYOKAI_OFFICER_ID_SHINSA_CHIHO;
                        break;
                }
                
                // 協会役員
                $kyokaiOfficerMemberList = $this->memberLibrarie->get_kyokai_officer_member_list($kyokaiOfficerId);
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

                // 審査簡易詳細情報取得
                $shinsaDetail = $this->shinsaModel->get_shinsa_detail_convenience($shinsaId);
                if (empty($shinsaDetail) === false) {
                    if (empty($shinsaDetail['area_group_name']) === false) {
                        $adminMailSubject .= $shinsaDetail['area_group_name'] . ' ';
                    }
                    $adminMailSubject .= $shinsaDetail['shinsa_name'];
                    $adminMailBody .= "審査日程 ： " . date_period_short_format($shinsaDetail['shinsa_date_min'], $shinsaDetail['shinsa_date_max']) . "\n";
                }
                
                $adminMailBody .= "審査種別 ： " . $shinsaTargetData['shinsa_class_name'] . "\n";
                $adminMailBody .= "審査対象 ： " . $shinsaTargetData['shinsa_target_name'] . "\n\n";
                $adminMailBody .= "申込者　 ： " . $memberData['name_f'] . " " . $memberData['name_s'] . "\n";
                
                $result = $this->commonLibrarie->send_mail_proc($mailToList, $mailCcList, $mailBccList, $adminMailSubject, $adminMailBody);
            }
            
        }
		
        return $result;
    }

    /**
     * 審査結果登録
     * @param int       $shinsaId               審査ID
     * @param int       $shinsaTargetId         審査対象ID
     * @param int       $resultFlg              審査結果フラグ
     * @param array     $memberData             会員情報
     * @return 
     */
    public function shinsa_result_report(int $shinsaId, int $shinsaTargetId, int $resultFlg, array $memberData): bool
    {
        $result = false;
        $adminMailSubject = '';
        $adminMailBody = '';
        $subjectAdd = '';
        $resultResult = '';
        
        // 審査結果フラグチェック
        switch ($resultFlg) {
            case SHINSA_RESULT_FLG_PASS :
                $resultResult = "合格";
                break;
            case SHINSA_RESULT_FLG_FAIL :
                $resultResult = "不合格";
                break;
            case SHINSA_RESULT_FLG_ABSTAIN :
                $resultResult = "棄権";
                break;
        }

        // 審査結果登録
        $result = $this->shinsaModel->shinsa_result_report($shinsaId, $resultFlg, $memberData['member_id']);
        if ($result === true) {

            // 担当役員へのメール送信準備
            $mailToList = array();
            $mailCcList = array();
            $mailBccList = array();

            // 審査申込対象情報取得
            $shinsaTargetData = $this->shinsaModel->get_shinsa_target($shinsaId, $shinsaTargetId);
            if (empty($shinsaTargetData) === false) {

                $kyokaiOfficerId = 0;
                switch ($shinsaTargetData['shinsa_class_id']) {
                    case SHINSA_CLASS_ID_CHUOU :
                        $kyokaiOfficerId = KYOKAI_OFFICER_ID_SHINSA_CHUOU;
                        break;
                    case SHINSA_CLASS_ID_RENGO :
                        $kyokaiOfficerId = KYOKAI_OFFICER_ID_SHINSA_RENGO;
                        break;
                    case SHINSA_CLASS_ID_CHIHO :
                    case SHINSA_CLASS_ID_VIDEO :
                        $kyokaiOfficerId = KYOKAI_OFFICER_ID_SHINSA_CHIHO;
                        break;
                }
                
                // 協会役員
                $kyokaiOfficerMemberList = $this->memberLibrarie->get_kyokai_officer_member_list($kyokaiOfficerId);
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

                // 審査簡易詳細情報取得
                $shinsaDetail = $this->shinsaModel->get_shinsa_detail_convenience($shinsaId);
                if (empty($shinsaDetail) === false) {
                    if (empty($shinsaDetail['area_group_name']) === false) {
                        $adminMailSubject .= $shinsaDetail['area_group_name'] . ' ';
                    }
                    $adminMailSubject .= $shinsaDetail['shinsa_name'];
                    $adminMailBody .= "審査結果の報告がありました\n\n";
                    $adminMailBody .= "審査日程 ： " . date_period_short_format($shinsaDetail['shinsa_date_min'], $shinsaDetail['shinsa_date_max']) . "\n";
                    $adminMailBody .= "審査種別 ： " . $shinsaTargetData['shinsa_class_name'] . "\n";
                    $adminMailBody .= "審査対象 ： " . $shinsaTargetData['shinsa_target_name'] . "\n\n";
                    $adminMailBody .= "受審者　 ： " . $memberData['name_f'] . " " . $memberData['name_s'] . "\n\n";
                    $adminMailBody .= "審査結果 ： " . $resultResult . "\n";
                    $result = $this->commonLibrarie->send_mail_proc($mailToList, $mailCcList, $mailBccList, $adminMailSubject, $adminMailBody);
                }
                
            }
        }
		
        return $result;
    }

    /**
     * 審査結果の役員による代理登録
     * @param int   $shinsaId   審査ID
     * @param int   $resultFlg  審査結果フラグ
     * @param int   $memberId   会員ID
     * @return 
     */
    public function shinsa_result_report_proxy(int $shinsaId, int $resultFlg, int $memberId): bool
    {
        // 審査結果登録
        return $this->shinsaModel->shinsa_result_report($shinsaId, $resultFlg, $memberId);
    }

    /**
     * 審査申請者一覧情報取得
     * @param int $shinsaId     審査ID
     * @param array $shinsaDetail 審査詳細情報
     * @return array 審査申請者一覧情報
     */
    public function get_shinsa_offer_member_list(int $shinsaId, array $shinsaDetail): array
    {
        $result = array();
        $shinsaDateAfterFlg = false;

        // 審査日の経過チェック
        $currentDate = date('Y-m-d');
        foreach ($shinsaDetail["date_holder_grade"]["result"] as $idx => $data) {
            // 審査日が当日以降の場合はフラグON
            if ($data['shinsa_date'] <= $currentDate) {
                $shinsaDateAfterFlg = true;
                break;
            }
        }
        $result = $this->shinsaModel->get_shinsa_offer_member_list($shinsaId, $shinsaDateAfterFlg);

        return $result;
    }

    /**
     * 昇段お知らせ本文取得
     * @param array $shinsaDetail   審査詳細情報
     * @param int   $shinsaId       審査ID
     * @return array 昇段対象者一覧情報
     */
    public function shinsa_rankup_notice_body(array $shinsaDetail, int $shinsaId): string
    {
        $noticeBody = '';

        // 昇段対象者一覧情報取得
        $shinsaRankupMemberList = $this->shinsaModel->get_shinsa_rankup_member_list($shinsaId);
        if ($shinsaRankupMemberList['numRows'] > 0) {

            // 審査の最終日を取得
            $shinsaLastDate = $this->shinsaModel->get_shinsa_last_date($shinsaDetail['shinsa_id']);
            // 審査最終日を "n月j日" 形式に整形
            if (empty($shinsaLastDate) === false) {
                $ts = strtotime($shinsaLastDate);
                if ($ts !== false) {
                    $shinsaLastDate = date('n月j日', $ts);
                }
            } else {
                $shinsaLastDate = date('n月j日');
            }

            $rankupList = array();
            foreach ($shinsaRankupMemberList['result'] as $idx => $data) {
                // 称号＆段位・級位毎に名前を格納
                $rankupList[$data['holder_name'] . $data['grade_name']][] = $data['name_f'] . $data['name_s'];
            }
            // 称号＆段位・級位毎の本文を生成
            $rankupMemberNoticeBody = '';
            $breakRank = '';
            foreach ($rankupList as $holderGrade => $memberList) {
                // 称号＆段位・級位の見出し
                foreach ($memberList as $idx => $memberName) {
                    $rankupMemberNoticeBody .= $memberName . 'さん、';
                }
                // 末尾の読点を削除
                $rankupMemberNoticeBody = mb_substr($rankupMemberNoticeBody, 0, -1);
                $rankupMemberNoticeBody .= 'が' . $holderGrade . 'に' . "\n";
            }
            // 末尾の読点を削除
            $rankupMemberNoticeBody = mb_substr($rankupMemberNoticeBody, 0, -2);

            // 審査会場の情報がある場合は審査会場名を追加
            $shisaKaijo = '';
            if (empty($shinsaDetail['kaijo_list']) === false) {
                // 開場が単一会場の場合は会場名を追加、複数会場の場合は会場名は追加しない
                if ($shinsaDetail['kaijo_list']['numRows'] === 1) {
                    $shisaKaijo .= '（' . $shinsaDetail['kaijo_list']['result'][0]['kaijo_name_short'] . '）';
                }
            }
            
            // メール本文
            $noticeBody = '本日' . $shinsaLastDate . $shisaKaijo . 'で行われた';
            $noticeBody .= $shinsaDetail['shinsa_class_name'] . 'で' . "\n";
            $noticeBody .= $rankupMemberNoticeBody . 'に' . "\n";
            $noticeBody .= '見事合格されました。' . "\n";
            $noticeBody .= 'おめでとうございます。' . "\n";
        }

        return $noticeBody;
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
