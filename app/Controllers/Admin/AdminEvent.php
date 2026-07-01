<?php

namespace App\Controllers\Admin;
use App\Controllers\CommonController;

class AdminEvent extends CommonController
{
    private $admineHelper;

	public function __construct() {
        parent::__construct();

        helper('admin');
        helper('Admin/event');
	}

    // 協会行事更新
    public function event_revision()
    {
        // ログインチェック
        if ($this->loginLibrarie->login_check() === false) {
            // ログインしていない状態の場合はログインページに移動
            return redirect()->to('login');
        }

        $rule = [
            'event_id' => ['label' => 'イベントID', 'rules' => 'required'],
        ];

        if ($this->validate($rule) === true) {

            // データ取得
            $eventData = $this->request->getPost();

            // 協会行事情報を取得
            $eventDetail = $this->kyokaiLibrarie->get_event_detail($eventData['event_id'], $this->_memberData['member_id']);
            // データがない場合は協会行事メインに移動
            if (empty($eventDetail) === true) {
                return redirect()->to('kyokai');
            }
            // 幹事でない場合は協会行事メインに移動
            if ($eventDetail['organizer_flg'] === false) {
                return redirect()->to('kyokai');
            }
            
        } else {
            $error = $this->validator->getErrors();
        }
        // if (empty($noticeDetail['notice_document_list']) === false) {
        //     $noticeDocumentList = $noticeDetail['notice_document_list'];
        // }
        
        // ページ
        $page = array();

		$data = [
            'fiscalYearId' => $this->_settingData->fiscal_year_id,
            'setting' => $this->_settingData,
            'memuInfo' => $this->_memuInfo,
            'memuData' => $this->_memuData,
            // 'categoryNoticeList' => $this->noticeLibrarie->get_category_notice_list($noticeAdminIdList),
            'eventDetail' => $eventDetail,
            // 'noticeCategoryId' => $noticeCategoryId,
            // 'noticeTitle' => $noticeTitle,
            // 'noticeBody' => $noticeBody,
            // 'noticeDocumentList' => $noticeDocumentList,
			'officerFlg' => FLG_TRUE,
            'uploadFileNum' => UPLOAD_FILE_NUM,
            'page' => $page,
            'mode' => MODE_REVISION,
            'headerCss' => array('admin/form'),
            // 'footerJs' => array('admin/notice'),
		];

        echo view('common/header', $data);
        echo view('common/menu');
        echo view('admin/kyokai/event_regist');
        echo view('admin/modal_admin');
        return view('common/footer');
    }

}
