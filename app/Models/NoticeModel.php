<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2024/11/13
 * Time: 16:07
 */
namespace App\Models;

class NoticeModel extends BaseQueryModel {

	protected $db;
	
	// お知らせカテゴリー 一覧情報取得
	public function get_category_notice_list(array $noticeCategoryAdminIdList) {

		$ret = array();

		$sql = '
			SELECT
				notice_category_id
				,notice_category_name
				,theme_coller
                ,file_directory
			FROM 
            	m_notice_category
			WHERE 1 = 1
				AND notice_category_id IN :noticeCategoryAdminIdList:
			ORDER BY
				order_no
		';

		$bind = array(
			'noticeCategoryAdminIdList' => $noticeCategoryAdminIdList
		);

		return $this->get_result_array($sql, $bind);
	}
	
	// お知らせカテゴリー 詳細情報取得
	public function get_category_notice_detail(int $noticeCategoryId) {

		$ret = array();

		$sql = '
			SELECT
				notice_category_id
				,notice_category_name
				,notice_category_name_full
				,theme_coller
                ,file_directory
			FROM 
            	m_notice_category
			WHERE
				notice_category_id = :noticeCategoryId:
		';

		$bind = array(
			'noticeCategoryId' => $noticeCategoryId
		);

		return $this->get_first_row($sql, $bind, 'array');
	}
	
	// お知らせ一覧情報取得
	public function get_notice_list($noticeCategoryId=NULL) {

		$ret = array();
		
		$sqlAddWhere = '';
		if (empty($noticeCategoryId) === false) {
			$sqlAddWhere = ' AND tni.notice_category_id = :noticeCategoryId:';
		}

		$sql = '
			SELECT
				tni.notice_info_id
				,tni.notice_category_id
				,tni.notice_title
				,tni.created
                ,mcn.notice_category_name
                ,mcn.theme_coller
                ,mem1.name_f AS cre_mame_f
                ,mem1.name_s AS cre_mame_s
                ,mem2.name_f AS mod_mame_f
                ,mem2.name_s AS mod_mame_s
			FROM 
            	t_notice_info tni
            	INNER JOIN m_notice_category mcn ON
                	mcn.notice_category_id = tni.notice_category_id
            	INNER JOIN m_member mem1 ON
                	mem1.member_id = tni.created_member_id
            	LEFT JOIN m_member mem2 ON
                	mem2.member_id = tni.modified_member_id
            WHERE
            	tni.delete_flg = :flgOff:
				' . $sqlAddWhere . '
			ORDER BY
				tni.created DESC
		';

		$bind = array(
			'flgOff' => DB_FLG_OFF,
		);
		if (empty($noticeCategoryId) === false) {
			$bind['noticeCategoryId'] = $noticeCategoryId;
		}

		return $this->get_result_array($sql, $bind);
	}
	
	// 関連するお知らせ一覧情報取得
	public function get_relation_notice_list($relationMenuId, $relationEventId) {

		$ret = array();

		$sql = '
			SELECT
				tni.notice_info_id
				,tni.notice_category_id
				,tni.notice_title
				,tni.created
                ,mcn.notice_category_name
                ,mcn.theme_coller
                ,mem1.name_f AS cre_mame_f
                ,mem1.name_s AS cre_mame_s
                ,mem2.name_f AS mod_mame_f
                ,mem2.name_s AS mod_mame_s
			FROM 
            	t_notice_info tni
            	INNER JOIN m_notice_category mcn ON
                	mcn.notice_category_id = tni.notice_category_id
            	INNER JOIN m_member mem1 ON
                	mem1.member_id = tni.created_member_id
            	LEFT JOIN m_member mem2 ON
                	mem2.member_id = tni.modified_member_id
            WHERE
            	tni.delete_flg = :flgOff:
				AND tni.relation_menu_id = :relationMenuId:
				AND tni.relation_event_id = :relationEventId:
			ORDER BY
				tni.created DESC
		';

		$bind = array(
			'flgOff' => DB_FLG_OFF,
			'relationMenuId' => $relationMenuId,
			'relationEventId' => $relationEventId,
		);

		return $this->get_result_array($sql, $bind);
	}
	
	// お知らせ詳細情報取得
	public function get_notice_detail(int $noticeInfoId, int $memberId) {

		$ret = array();

		$sql = '
			SELECT
				tni.notice_info_id
                ,tni.notice_category_id
				,tni.notice_title
				,tni.notice_body
                ,tni.relation_menu_id
                ,tni.relation_event_id
				,tni.relation_event_name
                ,men.controller
				,tni.created
				,tni.modified
                ,mcn.notice_category_name
                ,mcn.theme_coller
                ,mem1.member_id AS cre_member_id
                ,mem1.name_f AS cre_mame_f
                ,mem1.name_s AS cre_mame_s
                ,mem2.member_id AS mod_member_id
                ,mem2.name_f AS mod_mame_f
                ,mem2.name_s AS mod_mame_s
                ,CASE WHEN mem1.member_id = :memberId: THEN :flgOn: ELSE :flgOff: END modified_flg
			FROM 
            	t_notice_info tni
            	INNER JOIN m_notice_category mcn ON
                	mcn.notice_category_id = tni.notice_category_id
            	INNER JOIN m_member mem1 ON
                	mem1.member_id = tni.created_member_id
            	LEFT JOIN m_member mem2 ON
                	mem2.member_id = tni.modified_member_id
                INNER JOIN m_menu men ON
                    tni.relation_menu_id = men.menu_id
            WHERE
				tni.notice_info_id = :noticeInfoId:
            	AND tni.delete_flg = :flgOff:
			ORDER BY
				tni.created DESC
		';

		$bind = array(
			'noticeInfoId' => $noticeInfoId,
			'memberId' => $memberId,
			'flgOn' => DB_FLG_ON,
			'flgOff' => DB_FLG_OFF,
		);

		return $this->get_first_row($sql, $bind, 'array');
	}
	
	// お知らせ添付資料リスト取得
	public function get_notice_document_list(int $noticeInfoId) {

		$ret = array();

		$sql = '
			SELECT
				doc.document_id
				,doc.document_name
				,doc.document_ext
				,doc.document_path
				,doc.created
				,doc.modified
			FROM 
            	t_document_notice doc
            WHERE
				doc.notice_info_id = :noticeInfoId:
			ORDER BY
				doc.document_id
		';

		$bind = array(
			'noticeInfoId' => $noticeInfoId,
		);

		return $this->get_result_array($sql, $bind);
	}
	
	// お知らせ添付資料詳細取得
	public function get_notice_document_detail(int $noticeInfoId, int $documentId) {

		$ret = array();

		$sql = '
			SELECT
				document_name
				,document_ext
				,doc.document_path
				,doc.created
				,doc.modified
			FROM 
            	t_document_notice doc
            WHERE
				doc.notice_info_id = :noticeInfoId:
				AND doc.document_id = :documentId:
		';

		$bind = array(
			'noticeInfoId' => $noticeInfoId,
			'documentId' => $documentId,
		);

		return $this->get_first_row($sql, $bind, 'array');
	}
	
	/**
	 * お知らせメール配信対象会員一覧情報取得
     * @param int $fiscalYearId     年度ID
	 */ 
	public function get_notice_mail_member_list() : array
	{
		// メール配信の弓道協会員一覧情報を検索
		$sql = "
			SELECT
				mem.mail_address
				,mem.name_f
				,mem.name_s
			FROM
				m_member mem
			WHERE
				mem.kasugai_regist_flg = :kasugaiRegistFlg:
				AND mem.notice_send_flg = :noticeSendFlg:
				AND mem.mail_address != ''
		";

		$bind = array(
			'kasugaiRegistFlg' => DB_FLG_ON,
			'noticeSendFlg' => DB_FLG_ON,
		);

		return $this->get_result_array($sql, $bind);
	}
	
	// お知らせ基本情報登録
	public function insert_notice_info(
		int $noticeCategoryId,
		string $noticeTitle,
		string $noticeBody,
		int $relationMenuId,
		int $relationEventId,
		string $relationEventName,
		int $memberId,
		int &$insertId)
	{
		$ret = array();

		$sql = '
			INSERT INTO t_notice_info (
				notice_category_id,
				notice_title,
				notice_body,
				relation_menu_id,
				relation_event_id,
				relation_event_name,
				created_member_id
			) VALUES (
				:noticeCategoryId:,
				:noticeTitle:,
				:noticeBody:,
				:relationMenuId:,
				:relationEventId:,
				:relationEventName:,
				:memberId:
			)
		';

		$bind = array(
			'noticeCategoryId' => $noticeCategoryId,
			'noticeTitle' => $noticeTitle,
			'noticeBody' => $noticeBody,
			'relationMenuId' => $relationMenuId,
			'relationEventId' => $relationEventId,
			'relationEventName' => $relationEventName,
			'memberId' => $memberId,
		);

		$result = $this->get_result_query($sql, $bind);
		if ($result === true) {
			$insertId = $this->get_insert_id();
		}

		return $result;
	}
	
	// お知らせ基本情報更新
	public function update_notice_info(
		int $noticeInfoId,
		int $noticeCategoryId,
		string $noticeTitle,
		string $noticeBody, 
		int $relationMenuId,
		int $relationEventId,
		string $relationEventName,
		int $memberId)
	{

		$ret = array();

		$sql = '
			UPDATE 
				t_notice_info 
			SET 
				notice_category_id = :noticeCategoryId:,
				notice_title = :noticeTitle:,
				notice_body = :noticeBody:,
				relation_menu_id = :relationMenuId:,
				relation_event_id = :relationEventId:,
				relation_event_name = :relationEventName:,
				modified_member_id = :memberId:,
				modified = NOW()
			WHERE
				notice_info_id = :noticeInfoId:
		';

		$bind = array(
			'noticeCategoryId' => $noticeCategoryId,
			'noticeTitle' => $noticeTitle,
			'noticeBody' => $noticeBody,
			'relationMenuId' => $relationMenuId,
			'relationEventId' => $relationEventId,
			'relationEventName' => $relationEventName,
			'memberId' => $memberId,
			'noticeInfoId' => $noticeInfoId,
		);

		return $this->get_result_query($sql, $bind);
	}
	
	// お知らせファイル情報登録
	public function insert_document_notice(int $noticeInfoId, int $documentId, string $documentName, string $documentExt, string $documentPath) {

		$ret = array();

		$sql = '
			INSERT INTO t_document_notice (
				notice_info_id
				,document_id
				,document_name
				,document_ext
				,document_path
			) VALUES (
				:noticeInfoId:,
				:documentId:,
				:documentName:,
				:documentExt:,
				:documentPath:
			)
		';

		$bind = array(
			'noticeInfoId' => $noticeInfoId,
			'documentId' => $documentId,
			'documentName' => $documentName,
			'documentExt' => $documentExt,
			'documentPath' => $documentPath,
		);

		return $this->get_result_query($sql, $bind);
	}
	
	// お知らせ情報削除（倫理削除）
	public function delete_notice_info(int $noticeInfoId, int $memberId) {

		$ret = array();

		$sql = '
			UPDATE 
				t_notice_info 
			SET 
				delete_flg = :deleteFlg:,
				deleted_member_id = :memberId:,
				deleted = NOW()
			WHERE
				notice_info_id = :noticeInfoId:
		';

		$bind = array(
			'noticeInfoId' => $noticeInfoId,
			'memberId' => $memberId,
			'deleteFlg' => DB_FLG_ON,
		);

		return $this->get_result_query($sql, $bind);
	}
	
	// お知らせファイル情報削除
	public function delete_notice_document(int $noticeInfoId, int $documentId) {

		$ret = array();

		$sql = '
			DELETE FROM 
				t_document_notice 
			WHERE
				notice_info_id = :noticeInfoId:
				AND document_id = :documentId:
		';

		$bind = array(
			'noticeInfoId' => $noticeInfoId,
			'documentId' => $documentId,
		);

		return $this->get_result_query($sql, $bind);
	}
	
	// お知らせシーケンスNo取得
	public function get_notice_seq_no(int $fiscalYearId, int $noticeCategoryId, int &$noticeSeqNo) {

		$ret = array();

		$sql = '
			INSERT INTO seq_notice (
				fiscal_year_id
				,notice_category_id
				,seq_no
			) VALUES (
				:fiscalYearId:,
				:noticeCategoryId:,
				1
			) ON DUPLICATE KEY UPDATE
				seq_no = seq_no + 1
		';

		$bind = array(
			'fiscalYearId' => $fiscalYearId,
			'noticeCategoryId' => $noticeCategoryId,
		);

		$result = $this->get_result_query($sql, $bind);
		if ($result === false) {
			return false;
		}

		// シーケンスNO取得
		$sql = '
			SELECT
				seq_no
			FROM 
				seq_notice
			WHERE
				fiscal_year_id = :fiscalYearId:
				AND notice_category_id = :noticeCategoryId:
		';

		$result = $this->get_first_row($sql, $bind);
		if (empty($result) === true) {
			return false;
		}
		$noticeSeqNo = $result->seq_no;

		return true;
	}

}