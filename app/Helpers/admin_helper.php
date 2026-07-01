<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2023/10/09
 * Time: 11:32
 */

/*
 * HIDDENフォームHTMLを取得
 * return
 */
function admin_form_hidden($name, $value="", $id="", $class="") {

	if (empty($id) === true) {
		$id = $name;
	}
	if (empty($class) === false) {
		$class = ' class="' . $class . '"';
	}

	$html = '';
	$html .= '<input type="hidden" name="' . $name . '" id="' . $id . '" value="' . $value . '"' . $class . '>';
	
	return $html;
}

/*
 * テキストフォームHTMLを取得
 * return
 */
function admin_form_text($name, $value="", $id="", $class="") {

	if (empty($id) === true) {
		$id = $name;
	}
	if (empty($class) === false) {
		$class = ' class="' . $class . '"';
	}

	$html = '';
	$html .= '<input type="text" name="' . $name . '" id="' . $id . '" value="' . $value . '"' . $class . '>';
	
	return $html;
}

/*
 * テキストエリアフォームHTMLを取得
 * return
 */
function admin_form_textarea($id, $value="") {

	$html = '';
	$html .= '<textarea id="' . $id . '">';
	$html .= $value;	
	$html .= '</textarea>';
	
	return $html;
}

/*
 * DATEテキストフォームHTMLを取得
 * return
 */
function admin_form_date($name, $value="", $id="", $class="", $min="", $max="") {

	if (empty($id) === false) {
		$id = ' id="' . $id . '"';
	}
	if (empty($class) === false) {
		$class = ' class="' . $class . '"';
	}
	if (empty($min) === false) {
		$min = ' min="' . $min . '"';
	}
	if (empty($max) === false) {
		$max = ' max="' . $max . '"';
	}

	$html = '';
	$html .= '<input type="date" name="' . $name . '" " value="' . $value . '"' . $id . $class . $min . $max . '>';
	
	return $html;
}

/*
 * TIMEテキストフォームHTMLを取得
 * return
 */
function admin_form_time($name, $value="", $id="", $class="", $min="", $max="", $step="300") {

	if (empty($id) === false) {
		$id = ' id="' . $id . '"';
	}
	if (empty($class) === false) {
		$class = ' class="' . $class . '"';
	}
	if (empty($min) === false) {
		$min = ' min="' . $min . '"';
	}
	if (empty($max) === false) {
		$max = ' max="' . $max . '"';
	}

	// 時間フォーマット調整
	if (!empty($value)) {
		$value = date('H:i', strtotime($value));
	}

	$html = '';
	$html .= '<input type="time" name="' . $name . '" value="' . $value . '"' . $id . $class . $min . $max . ' step="' . $step . '">';
	
	return $html;
}

if (!function_exists('get_code_name')) {
    function get_code_name($list, $code) {
        foreach ($list as $item) {
            if ($item['code'] == $code) {
                return $item['name'];
            }
        }
        return '未登録';
    }
}

if (!function_exists('get_form_checkbox_name')) {
    function get_form_checkbox_name($flg) {
        return ($flg == 1) ? 'する' : 'しない';
    }
}

/*
 * ドロップダウンフォームHTMLを取得
 * return
 */
function admin_form_dropdown($dataList, $id, $class="", $dataValue='', $dataName='', $selecetdValue='', $addEmpty=false) {

	// 空のデータを先頭に追加
	$html = '';
	if (empty($dataList) === false) {
		$html .= '<select id="' . $id . '" class="' . $class . '">';
		// 空データフラグがONの場合、先頭に空データを追加
		if ($addEmpty === true) {
			$html .= '<option value="">&nbsp;</option>';
		}
		foreach ($dataList as $idx => $data) {
			$selected = '';
			if ($selecetdValue === $data[$dataValue]) {
				$selected = ' selected="selected"';
			}
			$html .= '<option value="' . $data[$dataValue] . '"' . $selected . '>' . $data[$dataName] . '</option>';
		}
		$html .= '</select>';
	}
	
	return $html;
}

/*
 * 数値型ドロップダウンフォームHTMLを取得
 * return
 */
function admin_form_dropdown_number($id, $start, $end, $selecetdValue='', $addEmpty=false) {

	// 空のデータを先頭に追加
	$html = '';
	$html .= '<select id="' . $id . '" class="number">';
	// 空データフラグがONの場合、先頭に空データを追加
	if ($addEmpty === true) {
		$html .= '<option value="0">回数なし</option>';
	}
	for ($i=$start; $i<=$end; $i++) {
		$selected = '';
		if ($selecetdValue == $i) {
			$selected = ' selected="selected"';
		}
		$html .= '<option value="' . $i . '"' . $selected . '>第 ' . $i . ' 回</option>';
	}
	$html .= '</select>';
	
	return $html;
}

/*
 * お知らせカテゴリードロップダウンフォームHTMLを取得
 * return
 */
function form_dropdown_category_notice($name, $categoryNoticeList, $noticeCategoryId='') {

	// 空のデータを先頭に追加
	$html = '';
	if (empty($categoryNoticeList) === false) {
		$disabled = '';
		if (empty($noticeCategoryId) === false) {
			// お知らせカテゴリIDが存在する場合はドロップダウンを無効化
			$disabled = ' disabled';
		}
		$html .= '<select id="' . $name . '"' . $disabled . '>';
		foreach ($categoryNoticeList['result'] as $idx => $data) {
			$selected = '';
			if ($noticeCategoryId === $data['notice_category_id']) {
				$selected = ' selected="selected"';
			}
			$html .= '<option value="' . $data['notice_category_id'] . '"' . $selected . '>' . $data['notice_category_name'] . '</option>';
		}
		$html .= '</select>';
	}
	
	return $html;
}

/*
 * 添付ファイルフォームHTMLを取得
 * return
 */
function form_file_document($documentType, $formId, $noticeDocumentList, $fileNum=UPLOAD_FILE_NUM) {

	$html = '';
	$idx = 1;
	
	// 登録済み添付ファイルデータ表示
	if (empty($noticeDocumentList) === false) {
		$documentNum = $noticeDocumentList['numRows'];
		$idx += $documentNum;
		foreach ($noticeDocumentList['result'] as $idy => $data) {
			$html .= '<div class="regist-files">';
			$html .= '<button type="button" class="' . $documentType . '-document-delete" data-document-id="' . $data['document_id'] . '" data-document-name="' . $data['document_name'] . '">資料削除</button>';
			$html .= '<img class="icon" src="' . SITE_ROOT . $data['ext_file'] . '" alt="' . $data['document_name'] . '">';
			// お知らせIDが存在し、IDが登録されている場合は太字表示
			if (empty($data['notice_category_id']) === false && $data['notice_category_id'] > 0) {
				$html .= '<strong>' . $data['document_name'] . '</strong> ';
			} else {
				$html .= $data['document_name'];
			}
			$html .= '</div>';
		}
	}
	
	// 添付ファイルフォームHTML
	for ($i=$idx; $i<=$fileNum; $i++) {
		$html .= '<input type="file" id="' . $formId . $i . '">';
	}
	
	return $html;
}
