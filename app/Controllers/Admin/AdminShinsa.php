<?php

namespace App\Controllers\Admin;
use App\Controllers\CommonController;
use App\Libraries\Admin\AdminMemberLibrarie;
use App\Libraries\Admin\AdminShinsaLibrarie;

class AdminShinsa extends CommonController
{
    private $adminMemberLibrarie;
    private $adminShinsaLibrarie;
        private $admineHelper;

	public function __construct() {
        parent::__construct();

		$this->adminMemberLibrarie = new AdminMemberLibrarie();
        $this->adminShinsaLibrarie = new AdminShinsaLibrarie();
        helper('admin');
        helper('Admin/shinsa');
	}

    // 審査登録
    public function shinsa_regist()
    {
        // ログインチェック
        if ($this->loginLibrarie->login_check() === false) {
            // ログインしていない状態の場合はログインページに移動
            return redirect()->to('login');
        }

        // データ取得
        $shinsaData = $this->request->getPost();

        $kyokaiOfficerId = 0;
        $areaGroupLevel = 0;
        $shinsaClassId = 0;

        // 審査種別を判定
        switch ($shinsaData['tab_name']) {
            case TAB_NAME_CHUOU:
                $kyokaiOfficerId = KYOKAI_OFFICER_ID_SHINSA_CHUOU;
                $areaGroupLevel = SHINSA_AREA_GROUP_CHUOU;
                $shinsaClassId = SHINSA_CLASS_ID_CHUOU;
                break;
            case TAB_NAME_RENGO:
                $kyokaiOfficerId = KYOKAI_OFFICER_ID_SHINSA_RENGO;
                $areaGroupLevel = SHINSA_AREA_GROUP_RENGO;
                $shinsaClassId = SHINSA_CLASS_ID_RENGO;
                break;
            case TAB_NAME_CHIHO:
                $kyokaiOfficerId = KYOKAI_OFFICER_ID_SHINSA_CHIHO;
                $areaGroupLevel = SHINSA_AREA_GROUP_CHIHO;
                $shinsaClassId = SHINSA_CLASS_ID_CHIHO;
                break;
            default:
                // 審査種別が不正な場合は審査メインに移動
                return redirect()->to('shinsa');
        }

        // 管理者情報を取得
        $kyokaiOfficerFlg = $this->memberLibrarie->chk_kyokai_officer_level($kyokaiOfficerId, $this->_memberData['member_id']);
        // データがない場合は審査メインに移動
        if ($kyokaiOfficerFlg === false) {
            return redirect()->to('shinsa');
        }

        // 地区リスト取得
        $areaGroupList = $this->shinsaLibrarie->get_area_group_list($areaGroupLevel);

        // 審査名称リスト取得
        $shinsaNameList = $this->shinsaLibrarie->get_shinsa_name_list($areaGroupLevel);

        // 称号・段位グループリスト取得
        $syubetsuList = $this->shinsaLibrarie->get_shinsa_holder_grade_list($shinsaClassId);

        // 会場リスト取得
        $kaijoList = $this->adminShinsaLibrarie->get_shinsa_kaijo_list($shinsaClassId);
        
        // ページ
        $page = array();

		$data = [
            'fiscalYearId' => $this->_settingData->fiscal_year_id,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
            'shinsaClassId' => $shinsaClassId,
            'areaGroupList' => $areaGroupList,
            'shinsaNameList' => $shinsaNameList,
            'syubetsuList' => $syubetsuList,
            'kaijoList' => $kaijoList,
			'officerFlg' => FLG_TRUE,
            'uploadFileNum' => UPLOAD_FILE_NUM,
            'page' => $page,
            'mode' => MODE_REGIST,
            'headerCss' => array('admin/form'),
            'footerJs' => array('admin/shinsa'),
		];

        echo view('common/header', $data);
        echo view('common/menu');
        echo view('admin/shinsa/shinsa_regist');
        echo view('admin/shinsa/modal_shinsa');
        return view('common/footer');
    }

    // 審査更新
    public function shinsa_revision()
    {
        // ログインチェック
        if ($this->loginLibrarie->login_check() === false) {
            // ログインしていない状態の場合はログインページに移動
            return redirect()->to('login');
        }

        $rule = [
            'shinsa_id' => ['label' => '審査ID', 'rules' => 'required'],
        ];

        if ($this->validate($rule) === true) {

            // データ取得
            $shinsaData = $this->request->getPost();

            // 審査情報を取得
            $shinsaDetail = $this->shinsaLibrarie->get_shinsa_detail($shinsaData['shinsa_id'], $this->_memberData['member_id']);
            // データがない場合は審査メインに移動
            if (empty($shinsaDetail) === true) {
                return redirect()->to('shinsa');
            }
            // 役員でない場合は審査メインに移動
            if (empty($shinsaDetail['officer_level']) === true) {
                return redirect()->to('shinsa');
            }

            // 会場リスト取得
            $kaijoList = $this->adminShinsaLibrarie->get_shinsa_kaijo_list($shinsaDetail['shinsa_class_id'], $shinsaDetail['area_group_id']);

            // 審査種別リスト取得
            $syubetsuList = $this->adminShinsaLibrarie->get_shinsa_shubetsu_list($shinsaDetail['shinsa_class_id']);
            
        } else {
            $error = $this->validator->getErrors();
        }
        
        // ページ
        $page = array();

		$data = [
            'fiscalYearId' => $this->_settingData->fiscal_year_id,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
            'shinsaDetail' => $shinsaDetail,
            'kaijoList' => $kaijoList,
            'syubetsuList' => $syubetsuList,
			'officerFlg' => FLG_TRUE,
            'uploadFileNum' => UPLOAD_FILE_NUM,
            'page' => $page,
            'mode' => MODE_REVISION,
            'headerCss' => array('admin/form'),
            'footerJs' => array('admin/shinsa'),
		];

        echo view('common/header', $data);
        echo view('common/menu');
        echo view('admin/shinsa/shinsa_regist');
        echo view('admin/shinsa/modal_shinsa');
        return view('common/footer');
    }

    /**
     * Ajax：審査登録確認
     */
    public function ajax_shinsa_regist_conf(): string
    {
        $result = false;
        $files = [];
        $ret = [];
        $error = [];

        // データ取得
        $shinsaData = $this->request->getPost();
        
        // MyRulesにデータをセット
        $myRules = new \App\Validation\MyRules();
        $myRules->setData($shinsaData);

        $rule = [
            'shinsa_id' => ['label' => '審査ID', 'rules' => 'required|integer'],
            'shinsa_name' => ['label' => '審査名', 'rules' => 'required'],
            'shinsa_date_1' => ['label' => '審査日', 'rules' => 'required'],
            // 'shinsa_date_1' => ['label' => '審査日', 'rules' => 'required|valid_date[Y-m-d]'],
            // 'shinsa_date_2' => ['label' => '審査日2', 'rules' => 'valid_date[Y-m-d]'],
            // 'shinsa_date_3' => ['label' => '審査日3', 'rules' => 'valid_date[Y-m-d]'],
            'holder_grade_id_1' => ['label' => '審査日種別', 'rules' => 'required|integer'],
            'kaijo_other_name' => ['label' => '特設会場等', 'rules' => 'max_length[100]'],
            'all_holder_grade_id' => ['label' => '審査種別', 'rules' => 'required|integer'],
            'gender_cd' => ['label' => '性別', 'rules' => 'required|in_list[0,1,2]'],
            // 'uketuke_limit_zenkyuren' => ['label' => '全弓連締切日', 'rules' => 'valid_date[Y-m-d]'],
            // 'uketuke_limit_aikyuren_st' => ['label' => '愛弓連申込期間(自)', 'rules' => 'valid_date[Y-m-d]'],
            // 'uketuke_limit_aikyuren_ed' => ['label' => '愛弓連申込期間(至)', 'rules' => 'valid_date[Y-m-d]'],
            'regist_mode' => ['label' => '登録モード', 'rules' => 'required'],
        ];

        $fileList = $this->request->getFiles();
        $fileCnt = count($fileList);
        for ($i=1; $i<=$fileCnt; $i++) {
            // ルール追加
            $name = 'shinsa_files' . $i;
            $addRule = [$name => [
                'label' => '添付' . $i,
                'rules' => 'uploaded[' . $name . ']|max_size[' . $name . ', ' . UPLOAD_FILE_MAX_SIZE . ']|ext_in[' . $name . ',jpg,jpeg,pdf,xls,xlsx,doc,docx,zip,txt]'
            ]];
            $rule = array_merge($rule, $addRule);
        }
        
        if ($this->validate($rule) === true) {
            // バリデーション成功後に日付・時刻チェック
            $result = true;
            $checkTime = '00:00:00';

            // 処理モード取得
            $registMode = $shinsaData['regist_mode'];

            // 審査クラスID取得
            $shisaClassId = $shinsaData['shinsa_class_id'];

            // 新規登録の場合
            if ($registMode === MODE_REGIST) {
                // 地方審査以外は地区の必須チェックを行う
                if ($shisaClassId !== SHINSA_CLASS_ID_CHIHO) {
                    if (empty($shinsaData['area_group_id']) === true) {
                        $result = false;
                        $error['area_group_id'] = '地区を選択してください';
                    }
                }
                // 審査会のチェック
                if (empty($shinsaData['shinsa_name_id']) === true) {
                    $result = false;
                    $error['shinsa_name_id'] = '審査会を選択してください';
                }
            }

            // 中央審査の場合は3日目まで審査日があるため、審査日2・3が入力されている場合は審査種別の必須チェックを行う
            if ($shisaClassId === SHINSA_CLASS_ID_CHUOU) {
                if (empty($shinsaData['shinsa_date_2']) === false) {
                    if (empty($shinsaData['holder_grade_id_2']) === true) {
                        $result = false;
                        $error['shinsa_date_2'] = '審査2日目の審査種別を指定してください';
                    }
                }
                if (empty($shinsaData['shinsa_date_3']) === false) {
                    if (empty($shinsaData['holder_grade_id_3']) === true) {
                        $result = false;
                        $error['shinsa_date_3'] = '審査3日目の審査種別を指定してください';
                    }
                }
            }

            // 全会場が未選択の場合は未入力エラー
            if (empty($shinsaData['kaijo_id_1']) === true && empty($shinsaData['kaijo_id_2']) === true && empty($shinsaData['kaijo_id_3']) === true) {
                $result = false;
                $error['kaijo_other_name_1'] = '会場を1つ以上選択してください';
            }

            // 同じ会場が複数選択されている場合はエラー
            $kaijoIdList = array();
            for ($i = 1; $i <= 3; $i++) {
                if (empty($shinsaData['kaijo_id_' . $i]) === false) {
                    if (in_array($shinsaData['kaijo_id_' . $i], $kaijoIdList) === true) {
                        $result = false;
                        $error['kaijo_id_' . $i] = '同じ会場が複数選択されています';
                    } else {
                        $kaijoIdList[] = $shinsaData['kaijo_id_' . $i];
                    }
                }
            }
            
            // 愛弓連申込期間：前後チェック（自 <= 至）
            if ($shinsaData['uketuke_limit_aikyuren_set'] === FLG_ON && empty($shinsaData['uketuke_limit_aikyuren_st']) === false && empty($shinsaData['uketuke_limit_aikyuren_ed']) === false) {
                if ($myRules->date_before($shinsaData['uketuke_limit_aikyuren_st'], $shinsaData['uketuke_limit_aikyuren_ed']) === false) {
                    $result = false;
                    $error['uketuke_limit_aikyuren_ed'] = '愛弓連申込終了日は開始日と同日かそれ以降で指定してください';
                }
            }
            
            // 全弓連締切日が設定されている場合、愛弓連申込期間（至）が全弓連締切日を超えていないかチェック（愛弓連申込期間（至） <= 全弓連締切日）
            if (empty($shinsaData['uketuke_limit_zenkyuren']) === false) {
                if ($myRules->date_before($shinsaData['uketuke_limit_aikyuren_ed'], $shinsaData['uketuke_limit_zenkyuren']) === false) {
                    $result = false;
                    $error['uketuke_limit_aikyuren_ed'] = '愛弓連申込終了日は全弓連締切日と同日かそれ以前で指定してください';
                }
            }

            // 登録済みの審査資料一覧取得
            $shinsaDocumentList = $this->adminShinsaLibrarie->get_shinsa_document_list($shinsaData['shinsa_id']);
            
            // アップロードファイル情報取得
            foreach ($fileList as $key => $file) {
                $fileExtPath = get_file_ext_icon_path($file->guessExtension());
                $files[] = array(
                    'file_name' => $file->getName(),
                    'file_ext_path' => $fileExtPath
                );
            }

            // エラーがなければhtml生成
            if ($result === true) {
                $html = form_shinsa_regist_confirm($shinsaData, $shinsaDocumentList, $files);
            }
        } else {
            $error = $this->validator->getErrors();
        }

        $ret = [
            'result' => $result,
            'files' => $files,
            'html' => $html ?? '',
            'error' => $error
        ];
        return json_encode($ret);
    }

    /**
     * Ajax：審査登録処理
     */
    public function ajax_shinsa_regist_proc(): string
    {
        $result = false;
        $ret = [];
        $error = [];

        $rule = [
            'shinsa_id' => ['label' => '審査ID', 'rules' => 'required|integer'],
            'regist_mode' => ['label' => '登録モード', 'rules' => 'required'],
        ];

        $filesCnt = count($this->request->getFiles());
        for ($i=1; $i<=$filesCnt; $i++) {
            // ルール追加
            $name = 'shinsa_files' . $i;
            $addRule = [$name => [
                'label' => '添付' . $i,
                'rules' => 'uploaded[' . $name . ']|max_size[' . $name . ', ' . UPLOAD_FILE_MAX_SIZE . ']|ext_in[' . $name . ',jpg,jpeg,pdf,xls,xlsx,doc,docx,zip,txt]'
            ]];
            $rule = array_merge($rule, $addRule);
        }

        if ($this->validate($rule) === true) {
            // データ取得
            $shinsaData = $this->request->getPost();
            $fileList = $this->request->getFiles();

            // 審査データ登録・更新
            $shinsaId = 0;
            $result = $this->adminShinsaLibrarie->shinsa_info_proc($this->_settingData->fiscal_year_id, $shinsaData, $this->_memberData['member_id'], $shinsaId);
            if ($result === true) {
                // ファイル登録
                $result = $this->adminShinsaLibrarie->shinsa_files_proc($shinsaId, $shinsaData, $fileList);
                if ($result === false) {
                    $this->errorMessage = '審査添付ファイルの登録ができませんでした';
                }
            } else {
                $error[] = $this->adminShinsaLibrarie->_get_error_message();
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

    // お知らせ投稿：審査合格
    public function notice_regist_shinsa_promotion($shinsaId)
    {
        // ログインチェック
        if ($this->loginLibrarie->login_check() === false) {
            // ログインしていない状態の場合はログインページに移動
            return redirect()->to('login');
        }

        // お知らせ管理者チェック
        $noticeAdminIdList = $this->_memberData['notice_admin_id_list'];
        if (empty($noticeAdminIdList) === true) {
            // お知らせ管理ページがない場合はメインに移動
            return redirect()->to('home');
        }

        // 審査情報取得
        $shinsaDetail = $this->shinsaLibrarie->get_shinsa_detail($shinsaId, $this->_memberData['member_id']);
        
        // 昇段お知らせ本文取得
        $noticeBody = $this->shinsaLibrarie->shinsa_rankup_notice_body($shinsaDetail, $shinsaId);
        if ($noticeBody === '') {
            // 昇段者がいない場合（本文なし）はメインに移動
            return redirect()->to('shinsa');
        }
        
        // ページ
        $page = array();

		$data = [
            'fiscalYearId' => $this->_settingData->fiscal_year_id,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
            'categoryNoticeList' => $this->noticeLibrarie->get_category_notice_list($noticeAdminIdList),
            'noticeInfoId' => "",
            'noticeId' => NOTICE_CATEGORY_ID_SHINSA,
            'noticeTitle' => "審査合格のお知らせ",
            'noticeBody' => $noticeBody,
            'noticeDocumentList' => array(),
			'officerFlg' => FLG_TRUE,
            'uploadFileNum' => UPLOAD_FILE_NUM,
            'page' => $page,
            'mode' => MODE_REGIST,
            'headerCss' => array('admin/form'),
            'footerJs' => array('admin/notice'),
		];

        echo view('common/header', $data);
        echo view('common/menu');
        echo view('admin/notice/notice_regist_shinsa_promotion');
        echo view('common/modal');
        echo view('admin/modal_admin');
        return view('common/footer');
    }

    /**
     * 審査代理参加登録
     */
    public function ajax_shinsa_add_member_proxy(): string
    {
        $result = false;
        $ret = [];
        $error = [];
        
        $rule = [
            'shinsa_id' => ['label' => '審査ID', 'rules' => 'required|integer'],
            'shinsa_target_id' => ['label' => '審査対象', 'rules' => 'required|integer'],
            'member_id' => ['label' => '参加者', 'rules' => 'required|integer'],
        ];
        if ($this->validate($rule) === true) {
            // データ取得
            $shinsaId = $this->request->getPost('shinsa_id');
            $shinsaTargetId = $this->request->getPost('shinsa_target_id');
            $memberId = $this->request->getPost('member_id');
            // 審査参加処理
            $result = $this->adminShinsaLibrarie->shinsa_add_member_proxy($shinsaId, $shinsaTargetId, $memberId);
            if ($result === false) {
                $error['shinsa'] = $this->adminShinsaLibrarie->_get_error_message();
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
     * 審査結果：代理登録
     */
    public function ajax_shinsa_result_report_proxy(): string
    {
        $result = false;
        $ret = [];
        $error = [];
        
        $rule = [
            'shinsa_id' => ['label' => '審査ID', 'rules' => 'required|integer'],
            'member_id' => ['label' => '会員ID', 'rules' => 'required|integer'],
            'result_flg' => ['label' => '審査結果', 'rules' => 'required|integer'],
        ];
        if ($this->validate($rule) === true) {
            // データ取得
            $shinsaId = $this->request->getPost('shinsa_id');
            $memberId = $this->request->getPost('member_id');
            $resultFlg = $this->request->getPost('result_flg');
            
            // 審査結果の代理登録処理
            $result = $this->shinsaLibrarie->shinsa_result_report_proxy($shinsaId, $resultFlg, $memberId);
            if ($result === false) {
                $error[] = '審査結果の代理登録に失敗しました。';
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
     * 昇段登録
     */
    public function ajax_rankup_result(): string
    {
        $result = false;
        $ret = [];
        $error = [];
        
        $rule = [
            'shinsa_id' => ['label' => '審査ID', 'rules' => 'required|integer'],
            'member_id' => ['label' => '会員ID', 'rules' => 'required|integer'],
            'pass_holder_id' => ['label' => '昇段称号ID', 'rules' => 'required|integer'],
            'pass_grade_id' => ['label' => '昇段段位ID', 'rules' => 'required|integer'],
            'acquired_day' => ['label' => '認許日', 'rules' => 'required'],
        ];
        if ($this->validate($rule) === true) {
            // データ取得
            $shinsaId = $this->request->getPost('shinsa_id');
            $memberId = $this->request->getPost('member_id');
            $passHolderId = $this->request->getPost('pass_holder_id');
            $passGradeId = $this->request->getPost('pass_grade_id');
            $acquiredDay = $this->request->getPost('acquired_day');

            if ($passHolderId > 0) {
                // 称号更新処理
                $result = $this->adminMemberLibrarie->rankup_holder_proc($shinsaId, $memberId, $passHolderId, $acquiredDay);
                if ($result === false) {
                    $error[] = '称号更新に失敗しました。';
                }
            }
            if ($passGradeId > 0) {
                // 段位・級位更新処理
                $result = $this->adminMemberLibrarie->rankup_grade_proc($shinsaId, $memberId, $passGradeId, $acquiredDay);
                if ($result === false) {
                    $error[] = '段位・級位更新に失敗しました。';
                }
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
     * 審査昇段登録用合格段位グループ取得
     */
    public function ajax_get_pass_grade_group(): string
    {
        $result = false;
        $ret = [];
        $error = [];
        
        $rule = [
            'pass_holder_id' => ['label' => '昇段対象称号ID', 'rules' => 'required|integer'],
            'pass_grade_group_id' => ['label' => '昇段対象段位グループID', 'rules' => 'required|integer'],
        ];
        if ($this->validate($rule) === true) {
            // データ取得
            $passHolderId = $this->request->getPost('pass_holder_id');
            $passGradeGroupId = $this->request->getPost('pass_grade_group_id');
            // 称号リスト取得
            $holderList = $this->shinsaLibrarie->get_holder_list($passHolderId);
            if ($holderList === false) {
                $error['holderList'] = $this->shinsaLibrarie->_get_error_message();
            }
            // 合格段位グループリスト取得
            $gradeList = $this->shinsaLibrarie->get_grade_group_list($passGradeGroupId);
            if ($gradeList === false) {
                $error['gradeList'] = $this->shinsaLibrarie->_get_error_message();
            }
            $result['holderList'] = $holderList;
            $result['gradeList'] = $gradeList;
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
     * 審査会場リスト取得
     */
    public function ajax_get_shinsa_kaijo_list(): string
    {
        $result = false;
        $ret = [];
        $data = [];
        $error = [];

        // データ取得
        $shinsaClassId = $this->request->getPost('shinsa_class_id');
        $areaGroupId = $this->request->getPost('area_group_id');
        // 審査会場リスト取得
        $shinsaKaijoList = $this->adminShinsaLibrarie->get_shinsa_kaijo_list($shinsaClassId, $areaGroupId);
        if ($shinsaKaijoList === false) {
            $error['shinsaKaijoList'] = $this->adminShinsaLibrarie->_get_error_message();
        } else {
            $result = true;
        }

        $ret = [
            'result' => $result,
            'shinsaKaijoList' => $shinsaKaijoList,
            'error' => $error
        ];
        return json_encode($ret);
    }

    /**
     * 審査種別の対象者リスト取得
     */
    public function ajax_get_shinsa_target_member_list(): string
    {
        $result = false;
        $ret = [];
        $data = [];
        $error = [];
        
        // データ取得
        $shinsaTargetId = $this->request->getPost('shinsa_target_id');
        $shinsaId = $this->request->getPost('shinsa_id');

        // 審査種別の対象者リスト取得
        $shinsaTargetList = $this->adminShinsaLibrarie->get_shinsa_target_member_list($this->_settingData->fiscal_year_id, $shinsaId, $shinsaTargetId);
        if ($shinsaTargetList === false) {
            $error['shinsaTargetList'] = $this->adminShinsaLibrarie->_get_error_message();
        } else {
            $result = true;
        }

        $ret = [
            'result' => $result,
            'shinsaTargetList' => $shinsaTargetList,
            'error' => $error
        ];
        return json_encode($ret);
    }

    /**
     * 審査代理キャンセル登録
     */
    public function ajax_shinsa_cancel_member_proxy(): string
    {
        $result = false;
        $ret = [];
        $error = [];
        
        $rule = [
            'shinsa_id' => ['label' => '審査ID', 'rules' => 'required|integer'],
            'member_id' => ['label' => '会員ID', 'rules' => 'required|integer'],
        ];
        if ($this->validate($rule) === true) {
            // データ取得
            $shinsaId = $this->request->getPost('shinsa_id');
            $memberId = $this->request->getPost('member_id');
            // 審査キャンセル処理
            $result = $this->adminShinsaLibrarie->shinsa_cancel_member_proxy($shinsaId, $memberId);
            if ($result === false) {
                $error['shinsa'] = $this->adminShinsaLibrarie->_get_error_message();
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

}
