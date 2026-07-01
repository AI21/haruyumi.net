<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2024/11/13
 * Time: 16:07
 */
namespace App\Models;

class CommonModel extends BaseQueryModel {

	protected $db;
	
	// 基本設定情報取得
	public function get_setting_data() {

		$ret = array();

		$sql = '
			SELECT
				mse.fiscal_year_id
				,mfy.year
				,mfy.wareki
			FROM m_setting mse
				INNER JOIN m_fiscal_nendo mfy
					ON mfy.fiscal_year_id = mse.fiscal_year_id
		';

		$bind = array(
		);

		return $this->get_first_row($sql, $bind);
	}
	
	// 称号一覧情報取得
	public function get_holder_list() : array
	{

		$sql = '
			SELECT
				mho.holder_id
				, mho.holder_name
				, mho.order_no
			FROM m_holder mho
			ORDER BY
				mho.order_no
		';

		$bind = array(
		);

		return $this->get_result_array($sql, $bind);
	}
	
	// 会場一覧情報取得
	public function get_kaijo_list() : array
	{

		$sql = '
			SELECT
				kai.kaijo_id
				, kai.kaijo_name
				, kai.kaijo_name_short
				, kai.kaijo_name_abb
				, kai.pref_id
				, kai.organiz_local_flg
				, kai.order_pref
				, kai.order_kasugai
			FROM
				m_kaijo kai
			WHERE
				kai.use_flg = :useFlg:
			ORDER BY
                kai.order_kasugai IS NULL ASC
                ,kai.order_kasugai = :flgOff: ASC
                ,kai.order_kasugai
                ,kai.pref_id
		';

		$bind = array(
			'useFlg' => DB_FLG_ON,
			'flgOff' => DB_FLG_OFF
		);

		return $this->get_result_array($sql, $bind);
	}
	
	// 弓道協会役員リスト取得
	public function get_kyokai_officer_list() : array
	{

		$sql = '
			SELECT
				mko.kyokai_officer_id
				, mko.kyokai_officer_name
			FROM 
				m_kyokai_officer mko
			WHERE
				mko.use_flg = :useFlg:
		';

		$bind = array(
			'useFlg' => DB_FLG_ON
		);

		return $this->get_result_array($sql, $bind);
	}
	
	// 称号・段位リスト取得
	public function get_holder_grade_list() : array
	{

		$sql = '
			SELECT
				hol.holder_id
				,hol.holder_name
				,gra.grade_id
				,gra.grade_name
				,hgo.hg_order
				,CONCAT(hol.holder_name, gra.grade_name) AS holder_grade_name
			FROM
				m_holder_grade_order hgo
			LEFT JOIN m_holder hol ON
				hol.holder_id = hgo.holder_id
			LEFT JOIN m_grade gra ON
				gra.grade_id = hgo.grade_id
			ORDER BY
				hgo.hg_order
		';

		$bind = array(
		);

		return $this->get_result_array($sql, $bind);
	}

}