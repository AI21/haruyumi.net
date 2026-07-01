<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2024/11/13
 * Time: 16:07
 */
namespace App\Models;

use CodeIgniter\Model;

class TaikaiModel extends BaseQueryModel {

	protected $db;

	/**
	 * 大会登録年度一覧取得
	 * @return array
	 */
	public function get_taikai_regist_nendo_list() : array
	{
		$sql = '
			SELECT 
				tti.fiscal_year_id
				,mfn.year
				,mfn.wareki
			FROM 
				t_taikai_info tti
				INNER JOIN m_fiscal_nendo mfn ON
					mfn.fiscal_year_id = tti.fiscal_year_id
			GROUP BY
				fiscal_year_id
		';

		$bind = array(
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 大会一覧情報取得
     * @param int $fiscalYearId		年度ID
     * @param int $categoryId		カテゴリーID
     * @param bool $pastFlg			過去フラグ
     * @return array
	 */
	public function get_taikai_list(int $fiscalYearId, int $categoryId, bool $pastFlg=false) : array
	{
		// 過去未来判定
		$sqlTaikaiDateCalc = '>=';
		$sqlTaikaiDateOrder = 'ASC';
		if ($pastFlg == true) {
			$sqlTaikaiDateCalc = '<';
			$sqlTaikaiDateOrder = 'DESC';
		}

		$sql = '
			SELECT
				tti.taikai_id,
				mta.taikai_name,
				mta.taikai_name_short,
				tti.taikai_sub_name,
				tti.taikai_no,
				tti.taikai_date_st,
				tti.taikai_date_ed,
				tti.taikai_uketuke_st,
				tti.taikai_uketuke_ed,
				tti.abort_flg,
                CASE WHEN knh.kaijo_name IS NOT NULL THEN knh.kaijo_name ELSE mka.kaijo_name END kaijo_name,
                CASE WHEN knh.kaijo_name IS NOT NULL THEN knh.kaijo_name_short ELSE mka.kaijo_name_short END kaijo_name_short,
                CASE WHEN knh.kaijo_name IS NOT NULL THEN knh.kaijo_name_abb ELSE mka.kaijo_name_abb END kaijo_name_abb,
                tti.kaijo_other_name
			FROM
				t_taikai_info tti
			INNER JOIN m_taikai mta ON
				mta.taikai_m_id = tti.taikai_m_id
			LEFT JOIN m_kaijo mka ON
				mka.kaijo_id = tti.kaijo_id
            LEFT JOIN m_kaijo_name_history knh ON
                knh.kaijo_id = mka.kaijo_id
                AND tti.taikai_date_st BETWEEN knh.change_date_st AND knh.change_date_ed
			WHERE
				tti.fiscal_year_id = :fiscalYearId: 
				AND tti.category_id = :categoryId: 
				AND tti.taikai_date_st ' . $sqlTaikaiDateCalc . ' CURDATE()
			ORDER BY
				tti.taikai_date_st ' . $sqlTaikaiDateOrder . '
		';

		$bind = array(
			'fiscalYearId' => $fiscalYearId,
			'categoryId' => $categoryId,
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 未経過の大会一覧情報取得
     * @return array
	 */
	public function get_unexpired_taikai_list() : array
	{
		$sql = '
			SELECT
				tti.taikai_id,
				mta.taikai_name,
				mta.taikai_name_short,
				tti.taikai_sub_name,
				tti.taikai_no,
				tti.taikai_date_st,
				tti.taikai_date_ed,
				tti.taikai_uketuke_st,
				tti.taikai_uketuke_ed,
				tti.abort_flg,
                CASE WHEN knh.kaijo_name IS NOT NULL THEN knh.kaijo_name ELSE mka.kaijo_name END kaijo_name,
                CASE WHEN knh.kaijo_name IS NOT NULL THEN knh.kaijo_name_short ELSE mka.kaijo_name_short END kaijo_name_short,
                CASE WHEN knh.kaijo_name IS NOT NULL THEN knh.kaijo_name_abb ELSE mka.kaijo_name_abb END kaijo_name_abb,
                tti.kaijo_other_name
			FROM
				t_taikai_info tti
			INNER JOIN m_taikai mta ON
				mta.taikai_m_id = tti.taikai_m_id
			LEFT JOIN m_kaijo mka ON
				mka.kaijo_id = tti.kaijo_id
            LEFT JOIN m_kaijo_name_history knh ON
                knh.kaijo_id = mka.kaijo_id
                AND tti.taikai_date_st BETWEEN knh.change_date_st AND knh.change_date_ed
			WHERE
				tti.taikai_date_st >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
			ORDER BY
				tti.category_id,
				tti.taikai_date_st ASC
		';

		$bind = array(
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 大会詳細情報取得
     * @param int $taikaiId			大会ID
     * @param int $memberId			会員ID
     * @return array
	 */
	public function get_taikai_detail(int $taikaiId, int $memberId) : array
	{
		$sql = '
			SELECT
				mta.taikai_name,
				mta.taikai_name_short,
                mta.near_far_flg,
                mta.kasugai_flg,
                mta.taikai_no_flg,
				tti.taikai_id,
				tti.category_id,
				tti.fiscal_year_id,
				tti.taikai_id,
				tti.taikai_no,
				tti.taikai_sub_name,
				tti.taikai_date_st,
				tti.taikai_date_ed,
				tti.taikai_open_time,
				tti.taikai_uketuke_time,
				tti.taikai_time_st,
				tti.taikai_time_ed,
				tti.taikai_uketuke_st,
				tti.taikai_uketuke_ed,
                tti.age_limit_min,
                tti.age_limit_max,
				tti.web_apply_flg,
				tti.indi_apply_flg,
                tti.gender_cd,
				tti.kaijo_id,
				tti.kaijo_other_name,
				tti.eligibility,
				tti.competition_rules,
				tti.awards,
				tti.entry_fee,
				tti.contact_info,
				tti.created,
				tti.modified,
				CASE WHEN tof.created IS NOT NULL THEN :flgOn: ELSE :flgOff: END sanka_flg,
                mko.kyokai_officer_name,
				mta.kyokai_officer_id,
                kom.officer_level,
                CASE WHEN knh.kaijo_name IS NOT NULL THEN knh.kaijo_name ELSE mka.kaijo_name END kaijo_name,
                CASE WHEN knh.kaijo_name IS NOT NULL THEN knh.kaijo_name_short ELSE mka.kaijo_name_short END kaijo_name_short,
                CASE WHEN knh.kaijo_name IS NOT NULL THEN knh.kaijo_name_abb ELSE mka.kaijo_name_abb END kaijo_name_abb,
                tti.kaijo_other_name
			FROM
				t_taikai_info tti
			LEFT JOIN m_taikai mta ON
				mta.taikai_m_id = tti.taikai_m_id
            LEFT JOIN m_kyokai_officer mko ON
                mko.kyokai_officer_id = mta.kyokai_officer_id
                AND mko.use_flg = :flgOn:
            LEFT JOIN m_kyokai_officer_member kom ON
                kom.kyokai_officer_id = mko.kyokai_officer_id
                AND kom.member_id = :memberId:
			LEFT JOIN m_kaijo mka ON
				mka.kaijo_id = tti.kaijo_id
            LEFT JOIN m_kaijo_name_history knh ON
                knh.kaijo_id = mka.kaijo_id
                AND tti.taikai_date_st BETWEEN knh.change_date_st AND knh.change_date_ed
            LEFT JOIN t_taikai_offer_member tof ON
            	tof.taikai_id = tti.taikai_id
                AND tof.member_id = :memberId:
			WHERE
				tti.taikai_id = :taikaiId: 
		';

		$bind = array(
			'taikaiId' => $taikaiId,
			'memberId' => $memberId,
			'flgOn' => DB_FLG_ON,
			'flgOff' => DB_FLG_OFF,
		);

		return $this->get_first_row($sql, $bind, 'array');
	}

	/**
	 * 大会関連資料一覧情報取得
	 * @param int $taikaiId			大会ID
	 * @return array
	 */
	public function get_taikai_document_list(int $taikaiId) : array
	{
		$sql = '
            SELECT
                tdt.document_id,
                tdt.document_name,
                tdt.document_ext,
                tdt.document_path,
                tdt.notice_info_id,
                tdt.created,
                tdt.modified,
                mdt.document_type_name
            FROM 
                t_document_taikai tdt
                INNER JOIN m_document_type mdt ON
                    mdt.document_type_id = tdt.document_type_id
			WHERE
				tdt.taikai_id = :taikaiId:
            ORDER BY
                mdt.order_no,
                tdt.document_id
		';

		$bind = array(
			'taikaiId' => $taikaiId,
		);

		return $this->get_result_array($sql, $bind);
	}
	
	// 大会添付資料詳細取得
	public function get_taikai_document_detail(int $taikaiId, int $documentId) {

		$ret = array();

		$sql = '
			SELECT
				document_name
				,document_ext
				,doc.document_path
				,doc.created
				,doc.modified
			FROM 
            	t_document_taikai doc
            WHERE
				doc.taikai_id = :taikaiId:
				AND doc.document_id = :documentId:
		';

		$bind = array(
			'taikaiId' => $taikaiId,
			'documentId' => $documentId,
		);

		return $this->get_first_row($sql, $bind, 'array');
	}
	
	/**
	 * 大会参加登録
     * @param int $taikaiId			大会ID
     * @param int $memberId			会員ID
     * @return bool
	 */
	public function taikai_join_member(int $taikaiId, int $memberId) : bool
	{
		$sql = '
			INSERT INTO t_taikai_offer_member (
				taikai_id,
				member_id,
				created
			) VALUES (
				:taikaiId:,
				:memberId:,
				NOW()
			)
			ON DUPLICATE KEY UPDATE
    			created = NOW()
		';

		$bind = array(
			'taikaiId' => $taikaiId,
			'memberId' => $memberId,
		);

		return $this->get_result_query($sql, $bind);
	}
	
	/**
	 * 大会キャンセル登録
     * @param int $taikaiId			大会ID
     * @param int $memberId			会員ID
     * @return bool
	 */
	public function taikai_cancel_member(int $taikaiId, int $memberId) : bool
	{
		$sql = '
			DELETE FROM t_taikai_offer_member 
			WHERE 
				taikai_id = :taikaiId:
				AND member_id = :memberId:
		';

		$bind = array(
			'taikaiId' => $taikaiId,
			'memberId' => $memberId,
		);

		return $this->get_result_query($sql, $bind);
	}
	
	/**
	 * 大会申請者一覧情報取得
     * @param int $taikaiId			大会ID
     * @return array
	 */
	public function get_taikai_offer_member_list(int $taikaiId) : array
	{
		$sql = '
           	SELECT
		   		tom.created,
                mem.member_id,
                mem.name_f,
                mem.name_s,
                mem.kana_f,
                mem.kana_s,
				mem.mail_address,
                mgh.holder_id,
                mgh.grade_id,
                hol.holder_name,
                gra.grade_name,
                hgo.hg_order,
                CASE WHEN mgh.holder_id = 0 THEN 9 ELSE mgh.holder_id END AS holder_order
            FROM t_taikai_offer_member tom
                INNER JOIN m_member mem ON
                    mem.member_id = tom.member_id
                INNER JOIN t_member_grade_holder mgh ON
                    mgh.member_id = mem.member_id
                INNER JOIN m_holder hol ON
                    hol.holder_id = mgh.holder_id
                INNER JOIN m_grade gra ON
                    gra.grade_id = mgh.grade_id
                INNER JOIN m_holder_grade_order hgo ON
                    hgo.holder_id = hol.holder_id
                    AND hgo.grade_id = gra.grade_id
            WHERE
                tom.taikai_id = :taikaiId:
            ORDER BY
                holder_order,
                hgo.hg_order,
                tom.created
		';

		$bind = array(
			'taikaiId' => $taikaiId,
		);
		
		return $this->get_result_array($sql, $bind);
	}

}