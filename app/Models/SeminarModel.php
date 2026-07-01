<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2024/11/13
 * Time: 16:07
 */
namespace App\Models;

use CodeIgniter\Model;

class SeminarModel extends BaseQueryModel {

	protected $db;

	/**
	 * 講習会登録年度一覧取得
	 * @return array
	 */
	public function get_seminar_regist_nendo_list() : array
	{
		$sql = '
			SELECT 
				tsi.fiscal_year_id
				,mfn.year
				,mfn.wareki
			FROM 
				t_seminar_info tsi
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
	 * 講習会一覧情報取得
     * @param int $fiscalYearId		年度ID
     * @param int $categoryId		カテゴリーID
     * @return array
	 */
	public function get_seminar_list(int $fiscalYearId, int $categoryId) : array
	{
		$sql = '
			SELECT
				tsi.seminar_id,
				tsi.seminar_sub_name,
				tsi.seminar_no,
				tsi.seminar_date_st,
				tsi.seminar_date_ed,
				tsi.uketuke_limit_aikyuren_st,
				tsi.uketuke_limit_aikyuren_ed,
				tsi.uketuke_limit_kasugai_st,
				tsi.uketuke_limit_kasugai_ed,
				mka.kaijo_name,
				mka.kaijo_name_short,
				tsi.kaijo_other_name
			FROM
				t_seminar_info tsi
			LEFT JOIN m_kaijo mka ON
				mka.kaijo_id = tsi.kaijo_id
			WHERE
				tsi.fiscal_year_id = :fiscalYearId: 
				AND tsi.category_id = :categoryId: 
				AND tsi.seminar_date_st >= CURDATE()
			ORDER BY
				tsi.seminar_date_st ASC
		';

		$bind = array(
			'fiscalYearId' => $fiscalYearId,
			'categoryId' => $categoryId,
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 未経過の講習会一覧情報取得
     * @return array
	 */
	public function get_unexpired_seminar_list() : array
	{
		$sql = '
			SELECT
				tsi.seminar_id,
				tsi.seminar_sub_name,
				tsi.seminar_no,
				tsi.seminar_date_st
			FROM
				t_seminar_info tsi
			WHERE
				tsi.seminar_date_st >= CURDATE()
			ORDER BY
				tsi.seminar_date_st ASC
		';

		$bind = array(
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 未経過の研修会一覧情報取得
     * @return array
	 */
	public function get_unexpired_training_list() : array
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
				AND mke.kyokai_event_id = :categoryId:
			WHERE
				kei.event_date_st >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
			ORDER BY
				kei.event_date_st ASC
		';

		$bind = array(
			'categoryId' => KYOKAI_EVENT_ID_KENSYU,
		);

		return $this->get_result_array($sql, $bind);
	}
	
	/**
	 * 講習会詳細情報取得
     * @param int $seminarId			講習会ID
     * @return array
	 */
	public function get_seminar_detail(int $seminarId) : array
	{
		$sql = '
			SELECT
				tsi.seminar_id,
				tsi.category_id,
				tsi.fiscal_year_id,
				tsi.seminar_m_id,
				tsi.seminar_no,
				tsi.seminar_sub_name,
				tsi.seminar_date_st,
				tsi.seminar_date_ed,
				tsi.seminar_open_time,
				tsi.seminar_uketuke_time,
				tsi.seminar_time_st,
				tsi.seminar_time_ed,
				tsi.uketuke_limit_aikyuren_st,
				tsi.uketuke_limit_aikyuren_ed,
				tsi.uketuke_limit_kasugai_st,
				tsi.uketuke_limit_kasugai_ed,
				tsi.age_limit_min,
				tsi.age_limit_max,
				tsi.kaijo_id,
				mka.kaijo_name,
				mka.kaijo_name_short,
				tsi.kaijo_other_name,
				tsi.entry_fee,
				tsi.contact_info,
				tsi.created,
				tsi.modified
			FROM
				t_seminar_info tsi
				LEFT JOIN m_kaijo mka ON
					mka.kaijo_id = tsi.kaijo_id
			WHERE
				seminar_id = :seminarId:
		';

		$bind = array(
			'seminarId' => $seminarId,
		);

		return $this->get_first_row($sql, $bind, 'array');
	}

	/**
	 * 講習会関連資料一覧情報取得
	 * @param int $seminarId			講習会ID
	 * @return array
	 */
	public function get_seminar_document_list(int $seminarId) : array
	{
		$sql = '
            SELECT
                tds.document_id,
                tds.document_name,
                tds.document_ext,
                tds.document_path,
                tds.created,
                tds.modified,
                mdt.document_type_name
            FROM 
                t_document_seminar tds
                INNER JOIN m_document_type mdt ON
                    mdt.document_type_id = tds.document_type_id
			WHERE
				tds.seminar_id = :seminarId:
            ORDER BY
                mdt.order_no,
                tds.document_id
		';

		$bind = array(
			'seminarId' => $seminarId,
		);

		return $this->get_result_array($sql, $bind);
	}
	
}