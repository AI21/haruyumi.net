<?php

namespace App\Controllers\Master;
use App\Controllers\CommonController;

class MasterIndex extends CommonController
{
    private $masterHelper;

	public function __construct() {
        parent::__construct();

        helper('master');
	}

    /**
     * マスタ管理メイン
     */
    public function index()
    {
        // print "マスタ管理メイン";
        // exit;
        // ログインチェック
        if ($this->loginLibrarie->login_check() === false) {
            // ログインしていない状態の場合はログインページに移動
            return redirect()->to('login');
        }
        
        $headerCss = array('admin/master/common');
        $footerJs = array('admin/master/common');

        // 管理役員チェック
        // $officerFlg = $this->loginLibrarie->chk_officer_menu($this->_memberData, $this->_memuInfo);
        // if ($officerFlg === false) {
        //     // 役員以外は一般メインに移動
        //     return redirect()->to('home');
        // }
        // データ取得
        // $kaijoList = $this->masterLibrarie->get_kaijo_list();

        // ページ
        $page = array();

        $data = [
            'fiscalYearId' => $this->_settingData->fiscal_year_id,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
			'officerFlg' => FLG_TRUE,
            'page' => $page,
            // 'kaijoList' => $kaijoList,
        ];

        echo view('common/header', $data);
        echo view('common/menu');
        echo view('admin/_master/index');
        return view('common/footer');
    }

}
