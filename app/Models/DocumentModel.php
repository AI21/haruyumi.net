<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2024/11/13
 * Time: 16:07
 */
namespace App\Models;

class DocumentModel extends BaseQueryModel {

	protected $db;
	
	/**
	 * お知らせ管理フラグ取得
     * @param int $categoryId		カテゴリーID
     * @return array
	 */ 
	public function get_document_list(int $categoryId) : ?array
	{
		$sql = '
			SELECT
				document_name
				,document_ext
				,document_path
				,created
			FROM 
				t_document_info
			WHERE
				document_category_id = :categoryId:
				AND delete_flg = :deleteFlg:
				-- 登録日から3か月以内
				AND created >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
			ORDER BY
				created DESC
		';

		$bind = [
			'categoryId' => $categoryId,
			'deleteFlg' => DB_FLG_OFF,
		];

		return $this->get_result_array($sql, $bind);
	}

}