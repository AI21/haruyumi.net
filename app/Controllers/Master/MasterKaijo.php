<?php

namespace App\Controllers\Master;
use App\Controllers\CommonController;

class MasterKaijo extends CommonController
{
    private $masterHelper;

	public function __construct() {
        parent::__construct();

        helper('master');
	}

    /**
     * 会場マスタ
     */
    public function index()
    {
        print "会場マスタ";
        exit;
        // ログインチェック
        if ($this->loginLibrarie->login_check() === false) {
            // ログインしていない状態の場合はログインページに移動
            return redirect()->to('login');
        }
        // 管理役員チェック
        $officerFlg = $this->loginLibrarie->chk_officer_menu($this->_memberData, $this->_memuInfo);
        if ($officerFlg === false) {
            // 役員以外は会場マスタメインに移動
            return redirect()->to('kaijo');
        }
        // データ取得
        $kaijoList = $this->masterLibrarie->get_kaijo_list();
        // ページ
        $page = array();
        $data = [
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
            'kaijoList' => $kaijoList,
        ];
        return view('master/kaijo', $data);
    }

}
