<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2023/10/09
 * Time: 11:32
 */

/*
 * 空のデータを先頭に追加したドロップダウンを取得
 * return
 */
function form_dropdown_unshift($name, $options, $selected = [], $extra = '') {

	// 空のデータを先頭に追加
	$add_option = array("-1" => "");
	$options = $add_option + $options;

	return form_dropdown($name, $options, $selected, $extra);
}

/*
 * 配列から指定のキーに絞ったドロップダウンを取得
 * return
 */
function form_dropdown_key_unshift($name, $datas, $val, $key, $selected = [], $extra = '', $addEmpty=false) {

	$options = [];
	
	if (empty($datas) === true) {
		return '';
	}
	// 空データフラグがONの場合、先頭に空データを追加
	if ($addEmpty === true) {
		$add_option = array("-1" => "");
		$options = $add_option + $options;
	}
	// 対象データ
	foreach ($datas as $idx => $data) {
		$options[$data[$val]] = $data[$key];
	}

	return form_dropdown($name, $options, $selected, $extra);
}

/*
 * ファイルサイズ変換：KB → MB
 * return
 */
function file_size_mb($fileSize) {
	return $fileSize / 1024 . ' MB';
}

/*
 * ファイルアイコンパス取得
 * return
 */
function get_file_ext_icon_path($fileExt) {
	
	$ret = '';

	switch ($fileExt) {
		case 'pdf' :
			$ret = UPLOAD_FILE_EXT_ICON_PDF;
			break;
		case 'xls' :
		case 'xlsx' :
			$ret = UPLOAD_FILE_EXT_ICON_EXCEL;
			break;
		case 'doc' :
		case 'docx' :
			$ret = UPLOAD_FILE_EXT_ICON_WORD;
			break;
		case 'jpg' :
		case 'jpeg' :
			$ret = UPLOAD_FILE_EXT_ICON_JPG;
			break;
		case 'zip' :
			$ret = UPLOAD_FILE_EXT_ICON_ZIP;
			break;
		case 'txt' :
			$ret = UPLOAD_FILE_EXT_ICON_TXT;
			break;
	}
	
	return $ret;
}

// 年度切り替えセレクトボックスHTML生成
function get_nendo_switch_html($controller, $fiscalYearId, $registNendoList)
{
    $html = '';

    if (empty($registNendoList) === false && $registNendoList['numRows'] > 0) {
        $html .= '<div class="change-nendo d-flex flex-row-reverse">';
        $html .= '<select id="' . $controller . '-change-nendo" class="form-select form-select-sm w-auto d-inline-block">';
        foreach ($registNendoList['result'] as $idx => $data) {
            $selected = '';
            if ($fiscalYearId == $data['fiscal_year_id']) {
                $selected = ' selected';
            }
            $html .= '<option value="' . $data['fiscal_year_id'] . '"' . $selected . '>' . $data['wareki'] . '(' . $data['year'] . ')年度</option>';
        }
        $html .= '</select>';
        $html .= '</div>';
    }
    return $html;
}

// 添付資料HTML生成
function get_relation_document_html($documentList, $breakTitleFlg=false)
{
    $html = '';
    if (empty($documentList) === false && $documentList['numRows'] > 0) {
        $html .= '<hr>';
        $html .= '<h2>添付資料</h2>';
        $html .= '<section id="document-area">';
        $breakDocumentTypeTame = '';
        foreach ($documentList['result'] as $idx => $data) {
            if ($breakDocumentTypeTame !== $data['document_type_name']) {
                if ($idx !== 0) {
                    $html .= '</ul>';
                    $html .= '</section>';
                }
                $html .= '<section class="my-3 document-list">';
				if ($breakTitleFlg === true) {
                	$html .= '<h2>' . htmlspecialchars($data['document_type_name'], ENT_QUOTES, 'UTF-8') . '</h2>';
				}
                $html .= '<ul class="list-unstyled">';
            }
            $html .= '<li>';
            $html .= '<img src="' . SITE_ROOT . get_file_ext_icon_path($data['document_ext']) . '" alt="' . $data['document_ext'] . '">';
            $html .= '<a href="' . base_url($data['document_path']) . '" target="_blank">' . htmlspecialchars($data['document_name'], ENT_QUOTES, 'UTF-8') . '</a>';
            $html .= '</li>';
            $breakDocumentTypeTame = $data['document_type_name'];
        }
        $html .= '</ul>';
        $html .= '</section>';
        $html .= '</section>';
    }
    return $html;
}

// 関連するお知らせHTML生成
function get_relation_notice_html($noticeList, $officerLevel, $noticeCategoryId, $relationEventId=null)
{
	$html = '';
	if (empty($noticeList) === false && $noticeList['numRows'] > 0) {
		$html .= '<section id="relation-notice-area">';
		$html .= '<hr>';
		$html .= '<h2>お知らせ</h2>';
        // 担当役員(主担当と副担当)の場合はお知らせ投稿ボタン表示
        if (empty($officerLevel) === false && ($officerLevel === KYOKAI_OFFICER_LEVEL_BOSS || $officerLevel === KYOKAI_OFFICER_LEVEL_SUB)) {
            $url = SITE_ROOT . 'admin/notice_regist/' . $noticeCategoryId;
            if (empty($relationEventId) === false) {
                $url .= '/' . $relationEventId;
            }
            $html .= '<div class="btn-area-officer">';
            $html .= '<a href="' . $url . '">新規投稿</a>';
            $html .= '</div>';
        }
        $html .= '<table id="notice-list" class="table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>タイトル</th>';
        $html .= '<th>登録日</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        foreach ($noticeList['result'] as $idx => $data) {
            $html .= '<tr>';
            $html .= '<td class="title"><a href="#" class="notice-view" data-notice-info-id="' . $data['notice_info_id'] . '">' . $data['notice_title'] . '</a></td>';
            $html .= '<td class="date">' . date_format_jp($data['created'], false, DATE_FORMAT_MD) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
		$html .= '</section>';
	}
	return $html;
}
