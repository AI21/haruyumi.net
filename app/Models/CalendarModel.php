<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2024/11/13
 * Time: 16:07
 */
namespace App\Models;

use CodeIgniter\Model;

class CalendarModel extends BaseQueryModel {

	protected $db;
	
	/**
	 * イベント一覧情報取得
     * @return array
	 */
	public function get_document_list($categoryId=1) : array
	{
		$sql = '
			SELECT
				document_name
				,document_ext
				,document_path
				,created
			FROM 
				t_document_calender
			WHERE
				document_category_id = :categoryId:
				AND delete_flg = :deleteFlg:
			ORDER BY
				created
		';

		$bind = [
			'categoryId' => $categoryId,
			'deleteFlg' => DB_FLG_OFF,
		];

		return $this->get_result_array($sql, $bind);
	}
	
}