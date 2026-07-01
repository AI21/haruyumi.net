<?php

namespace App\Controllers\Admin;
use App\Controllers\CommonController;
use App\Libraries\Admin\AdminTaikaiLibrarie;
use App\Libraries\Admin\AdminNoticeLibrarie;

class AdminTaikai extends CommonController
{
    private $adminNoticeLibrarie;
    private $adminTaikaiLibrarie;
    private $admineHelper;

	public function __construct() {
        parent::__construct();

		$this->adminNoticeLibrarie = new AdminNoticeLibrarie();
		$this->adminTaikaiLibrarie = new AdminTaikaiLibrarie();
        helper('admin');
        helper('Admin/taikai');
	}

    // 大会更新
    public function taikai_revision()
    {
        // ログインチェック
        if ($this->loginLibrarie->login_check() === false) {
            // ログインしていない状態の場合はログインページに移動
            return redirect()->to('login');
        }

        $rule = [
            'taikai_id' => ['label' => '大会ID', 'rules' => 'required'],
        ];

        if ($this->validate($rule) === true) {

            // データ取得
            $taikaiData = $this->request->getPost();

            // 大会情報を取得
            $taikaiDetail = $this->taikaiLibrarie->get_taikai_detail($taikaiData['taikai_id'], $this->_memberData['member_id']);
            // データがない場合は大会メインに移動
            if (empty($taikaiDetail) === true) {
                return redirect()->to('taikai');
            }
            // 役員でない場合は大会メインに移動
            if (empty($taikaiDetail['officer_level']) === true) {
                return redirect()->to('taikai');
            }

            // 会場リスト取得
            $kaijoList = $this->commonLibrarie->get_kaijo_list($taikaiDetail['kasugai_flg']);

            // 協会役員リスト取得
            $kyokaiOfficerList = $this->commonLibrarie->get_kyokai_officer_list();
            
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
            'taikaiDetail' => $taikaiDetail,
            'kaijoList' => $kaijoList,
            'kyokaiOfficerList' => $kyokaiOfficerList,
			'officerFlg' => FLG_TRUE,
            'uploadFileNum' => UPLOAD_FILE_NUM,
            'page' => $page,
            'mode' => MODE_REVISION,
            'headerCss' => array('admin/form'),
            'footerJs' => array('admin/taikai'),
		];

        echo view('common/header', $data);
        echo view('common/menu');
        echo view('admin/taikai/taikai_regist');
        echo view('admin/taikai/modal_taikai');
        echo view('common/modal');
        return view('common/footer');
    }

    /**
     * Ajax：大会資料削除
     */
    public function ajax_delete_taikai_document(): string
    {
        $result = false;
        $ret = [];
        $error = [];

        $rule = [
            'taikai_id' => ['label' => '大会ID', 'rules' => 'required'],
            'document_id' => ['label' => '資料ID', 'rules' => 'required'],
        ];

        if ($this->validate($rule) === true) {
            // データ取得
            $taikai_id = $this->request->getPost('taikai_id');
            $document_id = $this->request->getPost('document_id');

            // 大会資料削除
            $result = $this->adminTaikaiLibrarie->delete_taikai_document($taikai_id, $document_id);
            
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
     * Ajax：大会登録確認
     */
    public function ajax_taikai_regist_conf(): string
    {
        $result = false;
        $files = [];
        $ret = [];
        $error = [];

        // データ取得
        $taikaiData = $this->request->getPost();
        
        // MyRulesにデータをセット
        $myRules = new \App\Validation\MyRules();
        $myRules->setData($taikaiData);

        $rule = [
            'taikai_id' => ['label' => '大会ID', 'rules' => 'required|integer'],
            'taikai_no' => ['label' => '大会回数', 'rules' => 'integer'],
            'taikai_name' => ['label' => '大会名', 'rules' => 'required'],
            'taikai_date_st' => ['label' => '大会日(自)', 'rules' => 'required'],
            // 'taikai_date_st' => ['label' => '大会日(自)', 'rules' => 'required|valid_date[Y-m-d]'],
            // 'taikai_date_ed' => ['label' => '大会日(至)', 'rules' => 'valid_date[Y-m-d]'],
            // 'taikai_open_time' => ['label' => '開場時間', 'rules' => 'valid_time[H:i]'],
            'taikai_uketuke_time' => ['label' => '受付時間', 'rules' => 'valid_time[H:i]'],
            'taikai_time_st' => ['label' => '大会時間(自)', 'rules' => 'valid_time[H:i]'],
            'taikai_time_ed' => ['label' => '大会時間(至)', 'rules' => 'valid_time[H:i]'],
            // 'taikai_uketuke_st' => ['label' => '大会参加受付時間(自)', 'rules' => 'valid_date[Y-m-d]'],
            // 'taikai_uketuke_ed' => ['label' => '大会参加受付時間(至)', 'rules' => 'valid_date[Y-m-d]'],
            'web_apply_flg' => ['label' => '春弓テラスで参加受付', 'rules' => 'required'],
            'kaijo_other_name' => ['label' => '特設会場等', 'rules' => 'max_length[100]'],
            'gender_cd' => ['label' => '性別', 'rules' => 'required|in_list[0,1,2]'],
            'age_limit_set' => ['label' => '参加可能年齢設定', 'rules' => 'required|in_list[0,1]'],
            'age_limit_min' => ['label' => '参加可能年齢(最小)', 'rules' => 'integer'],
            'age_limit_max' => ['label' => '参加可能年齢(最大)', 'rules' => 'integer'],
            // 'eligibility' => ['label' => '参加資格', 'rules' => 'max_length[2000]'],
            // 'competition_rules' => ['label' => '競技規則', 'rules' => 'max_length[2000]'],
            // 'awards' => ['label' => '表彰', 'rules' => 'max_length[2000]'],
            // 'regist_mode' => ['label' => '登録モード', 'rules' => 'required'],
        ];

        $fileList = $this->request->getFiles();
        $fileCnt = count($fileList);
        for ($i=1; $i<=$fileCnt; $i++) {
            // ルール追加
            $name = 'taikai_files' . $i;
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

            // 会場が未選択の場合に特設会場が未入力の場合エラー
            if (empty($taikaiData['kaijo_id']) === true) {
                if (empty($taikaiData['kaijo_other_name']) === true) {
                    $result = false;
                    $error['kaijo_other_name'] = '会場が未選択の場合、特設会場等を入力してください';
                }
            }
            
            // 大会日付：前後チェック（自 <= 至）
            if ($taikaiData['taikai_time_set'] === FLG_ON && empty($taikaiData['taikai_date_st']) === false && empty($taikaiData['taikai_date_ed']) === false) {
                $checkTime = $taikaiData['taikai_date_ed'];
                if ($myRules->date_before($taikaiData['taikai_date_st'], $taikaiData['taikai_date_ed']) === false) {
                    $result = false;
                    $error['taikai_date_ed'] = '大会終了日は開始日と同日かそれ以降で指定してください';
                }
            }

            // 開場時間の入力チェック
            if ($taikaiData['taikai_open_time_set'] === FLG_ON) {
                if (empty($taikaiData['taikai_open_time_set']) === true || empty($taikaiData['taikai_open_time']) === true) {
                    $result = false;
                    $error['taikai_open_time'] = '開場時間が未入力です';
                }
            }
            
            // 開場時間と受付時間：前後チェック（開場時間 <= 受付時間）
            if ($taikaiData['taikai_uketuke_time_set'] === FLG_ON && empty($taikaiData['taikai_open_time']) === false && empty($taikaiData['taikai_uketuke_time']) === false) {
                if ($myRules->time_before($taikaiData['taikai_open_time'], $taikaiData['taikai_uketuke_time']) === false) {
                    $result = false;
                    $error['taikai_uketuke_time'] = '受付時間は開場時間以降で指定してください';
                }
                $checkTime = $taikaiData['taikai_uketuke_time'];
            }

            // 受付時間の入力チェック
            if ($taikaiData['taikai_uketuke_time_set'] === FLG_ON) {
                if (empty($taikaiData['taikai_uketuke_time_set']) === true || empty($taikaiData['taikai_uketuke_time']) === true) {
                    $result = false;
                    $error['taikai_uketuke_time'] = '受付時間が未入力です';
                }
            }
            
            // 受付時間と大会時間（自）：前後チェック（受付時間 <= 大会時間（自））
            if ($taikaiData['taikai_time_set'] === FLG_ON && empty($taikaiData['taikai_uketuke_time']) === false && empty($taikaiData['taikai_time_st']) === false) {
                if ($myRules->time_before($checkTime, $taikaiData['taikai_time_st']) === false) {
                    $result = false;    
                    $error['taikai_time_st'] = '大会終了時間は開始時間以降で指定してください';
                }
                $checkTime = $taikaiData['taikai_time_st'];
            }

            // 大会時間の入力チェック
            if ($taikaiData['taikai_time_set'] === FLG_ON) {
                if (empty($taikaiData['taikai_time_st']) === true) {
                    $result = false;
                    $error['taikai_time_st'] = '大会時間（自）が未入力です';
                }
                if (empty($taikaiData['taikai_time_ed']) === true) {
                    $result = false;
                    $error['taikai_time_ed'] = '大会時間（至）が未入力です';
                }
            }

            // 大会時間：前後チェック（大会時間（自） <= 大会時間（至））
            if ($taikaiData['taikai_time_set'] === FLG_ON && empty($taikaiData['taikai_time_st']) === false && empty($taikaiData['taikai_time_ed']) === false) {
                if ($myRules->time_before($taikaiData['taikai_time_st'], $taikaiData['taikai_time_ed']) === false) {
                    $result = false;
                    $error['taikai_time_ed'] = '大会終了時間は開始時間以降で指定してください';
                }
            }
            
            // 大会参加受付期間：前後チェック（自 <= 至）
            if ($taikaiData['taikai_uketuke_set'] === FLG_ON && empty($taikaiData['taikai_uketuke_st']) === false && empty($taikaiData['taikai_uketuke_ed']) === false) {
                if ($myRules->date_before($taikaiData['taikai_uketuke_st'], $taikaiData['taikai_uketuke_ed']) === false) {
                    $result = false;
                    $error['taikai_uketuke_ed'] = '大会参加受付終了日は開始日と同日かそれ以降で指定してください';
                }
            }

            // 大会受付終了日が大会日を超えていないかチェック（大会参加受付終了日 <= 大会日）
            if ($taikaiData['taikai_uketuke_set'] === FLG_ON && empty($taikaiData['taikai_uketuke_ed']) === false && empty($taikaiData['taikai_date_st']) === false) {
                if ($myRules->date_before($taikaiData['taikai_uketuke_ed'], $taikaiData['taikai_date_st']) === false) {
                    $result = false;
                    $error['taikai_uketuke_ed'] = '大会参加受付終了日は大会日と同日かそれ以前で指定してください';
                }
            }

            // 参加可能年齢：前後チェック（年齢制限（最小） <= 年齢制限（最大））
            if ($taikaiData['age_limit_set'] === FLG_ON && empty($taikaiData['age_limit_min']) === false && empty($taikaiData['age_limit_max']) === false) {
                if ($taikaiData['age_limit_min'] > $taikaiData['age_limit_max']) {
                     $result = false;
                     $error['age_limit_max'] = '参加可能年齢の最大は最小以上で指定してください';
                } elseif ($taikaiData['age_limit_min'] == $taikaiData['age_limit_max']) {
                     $result = false;
                     $error['age_limit_max'] = '参加可能年齢の最大は最小と同じ場合は指定できません';
                } elseif ($taikaiData['age_limit_min'] < 0) {
                    $result = false;
                    $error['age_limit_min'] = '参加可能年齢の最小は0以上で指定してください';
                }
            }

            // 登録済みの大会資料一覧取得
            $taikaiDocumentList = $this->adminTaikaiLibrarie->get_taikai_document_list($taikaiData['taikai_id']);
            
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
                $html = form_taikai_regist_confirm($taikaiData, $taikaiDocumentList, $files);
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
     * Ajax：大会登録処理
     */
    public function ajax_taikai_regist_proc(): string
    {
        $result = false;
        $ret = [];
        $error = [];

        $rule = [
            'taikai_id' => ['label' => '大会ID', 'rules' => 'required|integer'],
            'regist_mode' => ['label' => '登録モード', 'rules' => 'required'],
        ];

        $filesCnt = count($this->request->getFiles());
        for ($i=1; $i<=$filesCnt; $i++) {
            // ルール追加
            $name = 'taikai_files' . $i;
            $addRule = [$name => [
                'label' => '添付' . $i,
                'rules' => 'uploaded[' . $name . ']|max_size[' . $name . ', ' . UPLOAD_FILE_MAX_SIZE . ']|ext_in[' . $name . ',jpg,jpeg,pdf,xls,xlsx,doc,docx,zip,txt]'
            ]];
            $rule = array_merge($rule, $addRule);
        }

        if ($this->validate($rule) === true) {
            // データ取得
            $taikaiData = $this->request->getPost();
            $fileList = $this->request->getFiles();

            // 大会データ登録・更新
            $taikaiInfoId = 0;
            $result = $this->adminTaikaiLibrarie->taikai_info_proc($taikaiData, $this->_memberData['member_id'], $taikaiInfoId);
            if ($result === true) {
                // ファイル登録
                $result = $this->adminTaikaiLibrarie->taikai_files_proc($taikaiInfoId, $taikaiData, $fileList);
            } else {
                $error[] = '大会データの登録・更新に失敗しました。';
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
     * 大会代理参加登録
     */
    public function ajax_taikai_add_member_proxy(): string
    {
        $result = false;
        $ret = [];
        $error = [];
        
        $rule = [
            'taikai_id' => ['label' => '大会ID', 'rules' => 'required|integer'],
            'member_id_list' => ['label' => '参加者', 'rules' => 'required'],
        ];
        if ($this->validate($rule) === true) {
            // データ取得
            $taikaiId = $this->request->getPost('taikai_id');
            $memberIdList = $this->request->getPost('member_id_list');
            // 大会参加処理
            $result = $this->adminTaikaiLibrarie->taikai_add_member_proxy($taikaiId, $memberIdList);
            if ($result === false) {
                $error['taikai'] = $this->adminTaikaiLibrarie->_get_error_message();
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
     * 大会代理キャンセル登録
     */
    public function ajax_taikai_cancel_member_proxy(): string
    {
        $result = false;
        $ret = [];
        $error = [];
        
        $rule = [
            'taikai_id' => ['label' => '大会ID', 'rules' => 'required|integer'],
            'member_id' => ['label' => '会員ID', 'rules' => 'required|integer'],
        ];
        if ($this->validate($rule) === true) {
            // データ取得
            $taikaiId = $this->request->getPost('taikai_id');
            $memberId = $this->request->getPost('member_id');
            // 大会キャンセル処理
            $result = $this->adminTaikaiLibrarie->taikai_cancel_member_proxy($taikaiId, $memberId);
            if ($result === false) {
                $error['taikai'] = $this->adminTaikaiLibrarie->_get_error_message();
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
     * 大会参加者CSVダウンロード
     */
    public function ajax_taikai_member_csv_download(int $taikaiId) : void
    {
        // ログインチェック
        if ($this->loginLibrarie->login_check() === false) {
            // ログインしていない状態の場合は処理終了
            exit;
        }
        // 大会詳細情報取得
        $taikaiDetail = $this->taikaiLibrarie->get_taikai_detail($taikaiId, 0);
        if (empty($taikaiDetail) === true) {
            // データがない場合は処理終了
            exit;
        }
        // 大会参加者一覧情報取得
        $taikaiOfferMemberList = $this->taikaiLibrarie->get_taikai_offer_member_list($taikaiId);

        if (empty($taikaiOfferMemberList) === true) {
            // データがない場合は処理終了
            exit;
        } else {
            if ($taikaiOfferMemberList['numRows'] > 0) {
                
                // CSVデータ作成
                $csvData = "氏名,フリガナ,称号,段位,登録日時\n";
                foreach ($taikaiOfferMemberList['result'] as $idx => $data) {
                    $csvData .= 
                        $data['name_f'] . " " . $data['name_s'] . "," .
                        $data['kana_f'] . " " . $data['kana_s'] . "," .
                        $data['holder_name'] . "," .
                        $data['grade_name'] . "," .
                        $data['created'] . "\n";
                }

                // ファイル名設定
                $fileName = $taikaiDetail['taikai_name'] . "_" . date_format_en($taikaiDetail['taikai_date_st']) . '.csv';

                // ダウンロード用ヘッダー出力
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Content-Transfer-Encoding: binary');

                // BOM出力（Excelで文字化け防止） と Content-Length 設定
                $bom = "\xEF\xBB\xBF";
                header('Content-Length: ' . (strlen($bom) + strlen($csvData)));
                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

                echo $bom;

                // CSVデータ出力
                echo $csvData;

                // 出力を確実に終了して余計な出力を防ぐ
                exit;
            }
        }
    } 

}
