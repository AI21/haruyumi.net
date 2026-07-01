<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2024/11/13
 * Time: 16:07
 */
namespace App\Models\Admin;

use App\Models\BaseQueryModel;

class AdminDocumentModel extends BaseQueryModel {

	protected $db;
	
	// 資料ファイル情報登録
	public function insert_document_info(int $documentCategoryId, string $documentName, string $documentExt, string $documentPath) : bool
	{

		$sql = '
			INSERT INTO t_document_info (
				document_category_id
				,document_name
				,document_ext
				,document_path
				,created
			) VALUES (
				:documentCategoryId:,
				:documentName:,
				:documentExt:,
				:documentPath:,
				NOW()
			)
		';

		$bind = array(
			'documentCategoryId' => $documentCategoryId,
			'documentName' => $documentName,
			'documentExt' => $documentExt,
			'documentPath' => $documentPath,
		);

		return $this->get_result_query($sql, $bind);
	}

}