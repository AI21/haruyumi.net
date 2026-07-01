<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2024/11/13
 * Time: 16:07
 */
namespace App\Models;

use CodeIgniter\Model;

class KyokaiModel extends BaseQueryModel {

	protected $db;

	/**
	 * 協会イベント登録年度一覧取得
	 * @return array
	 */
	public function get_kyokai_event_regist_nendo_list() : array
	{
		$sql = '
			SELECT 
				tsi.fiscal_year_id
				,mfn.year
				,mfn.wareki
			FROM 
				t_event_info tsi
				INNER JOIN m_fiscal_nendo mfn ON
					mfn.fiscal_year_id = tsi.fiscal_year_id
			GROUP BY
				fiscal_year_id
		';

		$bind = array(
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 協会行事一覧情報取得
     * @param int $fiscalYearId		年度ID
     * @param int $categoryId		カテゴリーID
     * @param bool $pastFlg			過去フラグ
     * @return array
	 */
	public function get_kyokai_event_list(int $fiscalYearId, int $categoryId, bool $pastFlg=false) : array
	{
		// 過去未来判定
		$sqlKyokaiDateCalc = '>=';
		$sqlKyokaiDateOrder = 'ASC';
		if ($pastFlg == true) {
			$sqlKyokaiDateCalc = '<';
			$sqlKyokaiDateOrder = 'DESC';
		}

		$sql = '
			SELECT
				kei.event_id,
				kei.event_sub_name,
				kei.event_no,
				kei.event_date_st,
				kei.event_date_ed,
				kei.event_date_ambiguous_flg,
				kei.event_open_time,
				kei.event_uketuke_time,
				kei.event_time_st,
				kei.event_time_ed,
				kei.recruit_flg,
				kei.abort_flg,
				mka.kaijo_name,
				mka.kaijo_name_short,
				kei.kaijo_other_name,
				kei.kaijo_other_name_short,
                mke.kyokai_event_name
			FROM
				t_event_info kei
			LEFT JOIN m_kaijo mka ON
				mka.kaijo_id = kei.kaijo_id
			LEFT JOIN m_event mke ON
				mke.kyokai_event_id = kei.kyokai_event_id
			WHERE
				kei.fiscal_year_id = :fiscalYearId: 
				AND kei.category_id = :categoryId: 
				AND kei.event_date_st ' . $sqlKyokaiDateCalc . ' CURDATE()
			ORDER BY
				kei.event_date_st ' . $sqlKyokaiDateOrder . '
		';

		$bind = array(
			'fiscalYearId' => $fiscalYearId,
			'categoryId' => $categoryId,
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 未経過の協会行事一覧情報取得
     * @return array
	 */
	public function get_unexpired_kyokai_event_list() : array
	{
		$sql = '
			SELECT
				kei.event_id,
				kei.event_no,
				kei.event_date_st,
                mke.kyokai_event_name
			FROM
				t_event_info kei
			INNER JOIN m_event mke ON
				mke.kyokai_event_id = kei.kyokai_event_id
                AND mke.kyokai_event_id IN (1,4,5,6,7)
			WHERE
				kei.event_date_st >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
			ORDER BY
				kei.event_date_st ASC
		';

		$bind = array(
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 未経過のその他一覧情報取得
     * @return array
	 */
	public function get_unexpired_other_event_list() : array
	{
		$sql = '
			SELECT
				tei.event_id,
				tei.event_no,
				tei.event_date_st,
                tei.event_sub_name
			FROM
				t_event_info tei
			WHERE
				tei.event_date_st >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
                AND tei.kyokai_event_id = :kyokaiEventId:
			ORDER BY
				tei.event_date_st ASC
		';

		$bind = array(
			'kyokaiEventId' => KYOKAI_EVENT_ID_OTHER,
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * イベント詳細情報取得
     * @param int $eventId		イベントID
     * @param int $memberId		会員ID
     * @return array
	 */
	public function get_event_detail(int $eventId, int $memberId) : array
	{
		$sql = '
			SELECT
				kei.event_id,
				kei.category_id,
				kei.fiscal_year_id,
                CASE WHEN mke.kyokai_event_name != "" THEN mke.kyokai_event_name ELSE kei.event_sub_name END event_name,
				kei.event_sub_name,
				kei.event_no,
				kei.event_date_st,
				kei.event_date_ed,
				kei.event_date_ambiguous_flg,
				kei.event_uketuke_date_st,
				kei.event_uketuke_date_ed,
				kei.event_open_time,
				kei.event_uketuke_time,
                CASE WHEN kei.event_time_st != "" THEN kei.event_time_st ELSE NULL END event_time_st,
                CASE WHEN kei.event_time_ed != "" THEN kei.event_time_ed ELSE NULL END event_time_ed,
				kei.recruit_flg,
				kei.recruit_rank_flg,
				kei.abort_flg,
				mka.kaijo_name,
				mka.kaijo_name_short,
				kei.kaijo_other_name,
				kei.kaijo_other_name_short,
                mke.kyokai_event_name,
				kei.entry_fee,
                CASE WHEN eof.event_offer_id IS NOT NULL THEN 1 ELSE 0 END sanka_flg,
                CASE WHEN teo.event_id IS NOT NULL THEN 1 ELSE 0 END organizer_flg,
				teo.organizer_main_flg
			FROM
				t_event_info kei
			LEFT JOIN m_kaijo mka ON
				mka.kaijo_id = kei.kaijo_id
			LEFT JOIN m_event mke ON
				mke.kyokai_event_id = kei.kyokai_event_id
			LEFT JOIN t_event_offer_member eof ON
				eof.event_id = kei.event_id
				AND eof.member_id = :memberId:
            LEFT JOIN t_event_organizer teo ON
                teo.event_id = kei.event_id
                AND teo.member_id = :memberId:
			WHERE
				kei.event_id = :eventId: 
		';

		$bind = array(
			'memberId' => $memberId,
			'eventId' => $eventId,
		);

		return $this->get_first_row($sql, $bind, 'array');
	}
	
	// 添付資料リスト取得
	public function get_event_document_list(int $eventId) {

		$ret = array();

		$sql = '
			SELECT
				doc.document_id,
				doc.document_type_id,
				doc.document_name,
				doc.document_ext,
				doc.document_path,
				doc.created,
				doc.modified,
                mdt.document_type_name
			FROM 
            	t_document_event doc
                INNER JOIN m_document_type mdt ON
                    mdt.document_type_id = doc.document_type_id
            WHERE
				doc.event_id = :eventId:
			ORDER BY
				doc.document_id
		';

		$bind = array(
			'eventId' => $eventId,
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * イベント担当者情報取得
     * @param int $eventId		イベントID
     * @return array
	 */
	public function get_event_organizer_list(int $eventId) : array
	{
		$sql = '
            SELECT
                mem.member_id
                ,mem.name_f
                ,mem.name_s
                ,mem.kana_f
                ,mem.kana_s
                ,mem.mail_address
                ,teo.organizer_main_flg 
            FROM
                t_event_organizer teo 
                LEFT JOIN m_member mem ON
                    mem.member_id = teo.member_id
            WHERE
                teo.event_id = :eventId: 
            ORDER BY
                organizer_main_flg DESC
                ,member_id
		';

		$bind = array(
			'eventId' => $eventId,
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 審査申請者一覧情報取得
     * @param int $eventId		イベントID
     * @return array
	 */
	public function get_event_offer_member_list(int $eventId) : array
	{
		$sql = '
           	SELECT
		   		eof.modified,
                mem.member_id,
                mem.name_f,
                mem.name_s,
				mem.mail_address,
                hol.holder_name,
                gra.grade_name
            FROM t_event_offer_member eof
                INNER JOIN m_member mem ON
                    mem.member_id = eof.member_id
                INNER JOIN t_member_grade_holder mgh ON
                    mgh.member_id = mem.member_id
                INNER JOIN m_holder hol ON
                    hol.holder_id = mgh.holder_id
                INNER JOIN m_grade gra ON
                    gra.grade_id = mgh.grade_id
            WHERE
                eof.event_id = :eventId:
		';

		$bind = array(
			'eventId' => $eventId,
		);
		
		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * イベント参加登録
     * @param int $eventId			イベントID
     * @param int $memberId			会員ID
     * @return bool
	 */
	public function event_join_member(int $eventId, int $memberId) : bool
	{
		$sql = '
			INSERT INTO t_event_offer_member (
				event_id,
				member_id,
				modified
			) VALUES (
				:eventId:,
				:memberId:,
				NOW()
			)
			ON DUPLICATE KEY UPDATE
    			modified = NOW()
		';

		$bind = array(
			'eventId' => $eventId,
			'memberId' => $memberId,
		);

		return $this->get_result_query($sql, $bind);
	}
	
	/**
	 * イベント参加キャンセル登録
     * @param int $eventId			イベントID
     * @param int $memberId			会員ID
     * @return bool
	 */
	public function event_cancel_member(int $eventId, int $memberId) : bool
	{
		$sql = '
			DELETE FROM t_event_offer_member 
			WHERE 
				event_id = :eventId:
				AND member_id = :memberId:
		';

		$bind = array(
			'eventId' => $eventId,
			'memberId' => $memberId,
		);

		return $this->get_result_query($sql, $bind);
	}

}