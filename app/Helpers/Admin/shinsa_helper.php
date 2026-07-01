<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2023/10/09
 * Time: 11:32
 */

/*
 * 審査更新フォームHTML
 * return
 */
function form_shinsa_regist($shinsaClassId, $areaGroupList, $shinsaNameList, $syubetsuList, $kaijoList, $uploadFileNum) {
	$html = '';

	$html .= '<dl>';

	// 審査区分
	$html .= '<dt>地区・審査会</dt>';
	$html .= '<dd>';
	if (empty($areaGroupList) === false) {
		$addEmptyFlg = true;
		if ($areaGroupList['numRows'] === 1) {
			$addEmptyFlg = false;
		}
		$html .= admin_form_dropdown($areaGroupList['result'], 'area-group-id', '', 'area_group_id', 'area_group_name', '', $addEmptyFlg);
		$html .= '　';
	} else {
		$html .= admin_form_hidden('area-group-id', 0);
	}
	$addEmptyFlg = true;
	if ($shinsaNameList['numRows'] === 1) {
		$addEmptyFlg = false;
	}
	$html .= admin_form_dropdown($shinsaNameList['result'], 'shinsa-name-id', '', 'shinsa_name_id', 'shinsa_name', '', $addEmptyFlg);
	$html .= '</dd>';
	// 審査日・審査種別
	$html .= '<dt>審査日・種別</dt>';
	$html .= '<dd>';
	$html .= '<ul class="mb-2" id="shinsa-date-tab" role="tablist">';
	if ($shinsaClassId === SHINSA_CLASS_ID_CHUOU) {
		// 中央審査の場合は3日分の審査日を表示
		for ($i = 0; $i < 3; $i++) {
			$html .= '<li class="nav-item mb-2" role="presentation">';
			$html .= $i + 1 . '日目';
			// $data = $shinsaDetail['date_holder_grade']['result'][$i] ?? null;
			// if (empty($data) === false) {
			// 	$html .= admin_form_date("shinsa-date-" . ($i + 1), $data['shinsa_date'], "shinsa-date-" . ($i + 1), 'ms-2');
			// 	$html .= admin_form_dropdown($syubetsuList['result'], 'holder-grade-id-' . ($i + 1), 'ms-2', 'holder_grade_id', 'holder_grade_name', $data['holder_grade_id'], true);
			// } else {
				$html .= admin_form_date("shinsa-date-" . ($i + 1), "", "shinsa-date-" . ($i + 1), "ms-2");
				$addEmptyFlg = true;
				if ($syubetsuList['numRows'] === 1) {
					$addEmptyFlg = false;
				}
				$html .= admin_form_dropdown($syubetsuList['result'], 'holder-grade-id-' . ($i + 1), 'ms-2', 'holder_grade_id', 'holder_grade_name', '', $addEmptyFlg);
			// }
			$html .= '</li>';
		}
	} else {
		// 中央審査以外の場合は1日分の審査日を表示
		$html .= '<li class="nav-item mb-2" role="presentation">';
		$data = $shinsaDetail['date_holder_grade']['result'][0] ?? null;
		$addEmptyFlg = true;
		// 種別が1件の場合、もしくは地方審査の場合は空選択なしとする
		if ($syubetsuList['numRows'] === 1 || $shinsaClassId === SHINSA_CLASS_ID_CHIHO) {
			$addEmptyFlg = false;
		}
		if (empty($data) === false) {
			$html .= admin_form_date("shinsa-date-1", $data['shinsa_date'], "shinsa-date-1");
			$html .= admin_form_dropdown($syubetsuList['result'], 'holder-grade-id-1', 'ms-2', 'holder_grade_id', 'holder_grade_name', $data['holder_grade_id'], $addEmptyFlg);
		} else {
			$html .= admin_form_date("shinsa-date-1", "", "shinsa-date-1");
			$html .= admin_form_dropdown($syubetsuList['result'], 'holder-grade-id-1', 'ms-2', 'holder_grade_id', 'holder_grade_name', '', $addEmptyFlg);
		}
		$html .= admin_form_hidden("shinsa-date-2", '');
		$html .= admin_form_hidden("holder-grade-id-2", '');
		$html .= admin_form_hidden("shinsa-date-3", '');
		$html .= admin_form_hidden("holder-grade-id-3", '');
		$html .= '</li>';
	}
	$html .= '</ul>';
	$html .= '</dd>';
	// 審査種別
	$html .= '<dt>審査種別総合（一覧表示用）</dt>';
	$html .= '<dd>';
	$addEmptyFlg = true;
	// 種別が1件の場合、もしくは地方審査の場合は空選択なしとする
	if ($syubetsuList['numRows'] === 1 || $shinsaClassId === SHINSA_CLASS_ID_CHIHO) {
		$addEmptyFlg = false;
	}
	$html .= admin_form_dropdown($syubetsuList['result'], 'all-holder-grade-id', '', 'holder_grade_id', 'holder_grade_name', '', $addEmptyFlg);
	$html .= '</dd>';
	// 会場
	$html .= '<dt>会場</dt>';
	$html .= '<dd>';
	// $html .= admin_form_dropdown($kaijoList['result'], 'kaijo-id-1', '', 'kaijo_id', 'kaijo_name', '', true);
	for ($i = 0; $i < 3; $i++) {
		$kaijoId = NULL;
		$additionalInfo = NULL;
		if (empty($shinsaDetail['kaijo_list']) === false) {
			if (empty($shinsaDetail['kaijo_list']['result'][$i]['kaijo_id']) === false) {
				$kaijoId = $shinsaDetail['kaijo_list']['result'][$i]['kaijo_id'];
			}
			if (empty($shinsaDetail['kaijo_list']['result'][$i]['additional_info']) === false) {
				$additionalInfo = $shinsaDetail['kaijo_list']['result'][$i]['additional_info'];
			}

		}
		$html .= '<p class="mt-1 mb-0">';
		$html .= '第' . ($i + 1) . ' ： ' . admin_form_dropdown($kaijoList['result'], 'kaijo-id-' . ($i + 1), '', 'kaijo_id', 'kaijo_name', $kaijoId, true);
		$html .= '<div class="ms-2">備考 ' . admin_form_text('additional-info-' . ($i + 1), $additionalInfo, "", 'mt-1') . '</div>';
		$html .= '</p>';
	}
	$html .= '</dd>';
	// 対象性別
	$html .= '<dt>対象性別</dt>';
	$html .= '<dd>';
	$html .= '<input type="radio" id="gender0" name="gender-cd" checked="checked" value="' . GENDER_ALL . '">';
	$html .= '<label for="gender0">問わず</label>';
	$html .= '<input type="radio" id="gender1" name="gender-cd" value="' . GENDER_MALE . '">';
	$html .= '<label for="gender1">男性のみ</label>';
	$html .= '<input type="radio" id="gender2" name="gender-cd" value="' . GENDER_FEMALE . '">';
	$html .= '<label for="gender2">女性のみ</label>';
	$html .= '</dd>';
	// 全弓連締切日
	$html .= '<dt>全弓連締切日</dt>';
	$html .= '<dd>';
	$html .= admin_form_date("uketuke-limit-zenkyuren", "", "uketuke-limit-zenkyuren");
	$html .= '</dd>';
	// 愛弓連申込期間
	$html .= '<dt>愛弓連申込期間</dt>';
	$html .= '<dd>';
	$html .= '<span class="form-switch me-2">';
	$checked = ' checked="checked"';
	$class = '';
	$label = '設定';
	if ($shinsaClassId === SHINSA_CLASS_ID_RENGO) {
		// 連合審査の場合は愛弓連申込期間の設定はなし
		$checked = '';
		$class = 'd-none';
		$label = '未定';
	}
	$html .= '<input type="checkbox" class="form-check-input mt-1 me-1" ' . $checked . ' id="uketuke-limit-aikyuren-set" role="switch">';
	$html .= '<label for="uketuke-limit-aikyuren-set" id="uketuke-limit-aikyuren-text">' . $label . '</label>';
	$html .= '</span>';
	$html .= '<span id="uketuke-limit-aikyuren-area" class="' . $class . '">';
	$html .= admin_form_date("uketuke-limit-aikyuren-st", "", "uketuke-limit-aikyuren-st");
	$html .= ' ～ ';
	$html .= admin_form_date("uketuke-limit-aikyuren-ed", "", "uketuke-limit-aikyuren-ed");
	$html .= '</span>';
	$html .= '</dd>';
	// WEB申込開始日

	// WEB申込開始日の算出（愛弓連申込開始日の1週間前） 曜日付き
	$webApplyStartDay = '';
	$webApplyDescription = '愛弓連申込開始日の1週間前';
	// if ($shinsaDetail['shinsa_class_id'] == SHINSA_CLASS_ID_RENGO) {
	// 	// 連合審査の場合は審査日の3ヶ月前
	// 	$webApplyDescription = '審査日の3ヶ月前';
	// 	$webApplyStartDay = date('Y/m/d', strtotime($shinsaDetail['date_holder_grade']['result'][0]['shinsa_date'] . ' -3 month'));
	// 	$week = ['日', '月', '火', '水', '木', '金', '土'];
	// 	$webApplyStartDay .= '（' . $week[date('w', strtotime($webApplyStartDay))] . '）';
	// } else {
	// 	if (!empty($shinsaDetail['uketuke_limit_aikyuren_st'])) {
	// 		$webApplyStartDay = date('Y/m/d', strtotime($shinsaDetail['uketuke_limit_aikyuren_st'] . ' -1 week'));
	// 		$week = ['日', '月', '火', '水', '木', '金', '土'];
	// 		$webApplyStartDay .= '（' . $week[date('w', strtotime($webApplyStartDay))] . '）';
	// 	}
	// }
	$html .= '<dt>' . KASUGAI_KYOKAI_NAME_SHORT . '申込開始日（' . $webApplyDescription . '）</dt>';
	$html .= '<dd id="web-apply-start-day-view">';
	$html .= $webApplyStartDay;
	$html .= '</dd>';
	
	// 添付資料
	$html .= '<hr>';
	$html .= '<dt>添付資料</dt>';
	$html .= '<dd>';
	$html .= form_file_document('shinsa', 'shinsa-files', '', $uploadFileNum);
	$html .= '</dd>';

	$html .= admin_form_hidden("shinsa-id", 0);
	$html .= admin_form_hidden("shinsa-class-id", $shinsaClassId);
	$html .= admin_form_hidden("web-apply-start-day", '');

	$html .= '</dl>';

	return $html;
}

/*
 * 審査更新フォームHTML
 * return
 */
function form_shinsa_revision($shinsaDetail, $kaijoList, $syubetsuList, $uploadFileNum) {

	$html = '';

	// 審査名
	$shinsaName = '';
	if (empty($shinsaDetail['area_group_name']) === false) {
		$shinsaName = '【' . $shinsaDetail['area_group_name'] . '】';
	}
	$shinsaName .= $shinsaDetail['shinsa_name'] ?? '';

	$html .= '<dl>';
	// 審査名
	$html .= '<dt>審査名</dt>';
	$html .= '<dd>' . $shinsaName . '</dd>';
	// 審査日・審査種別
	$html .= '<dt>審査日・種別</dt>';
	$html .= '<dd>';
	$html .= '<ul class="mb-2" id="shinsa-date-tab" role="tablist">';
	if ($shinsaDetail['shinsa_class_id'] === SHINSA_CLASS_ID_CHUOU) {
		// 中央審査の場合は3日分の審査日を表示
		for ($i = 0; $i < 3; $i++) {
			$html .= '<li class="nav-item mb-2" role="presentation">';
			$html .= $i + 1 . '日目';
			$data = $shinsaDetail['date_holder_grade']['result'][$i] ?? null;
			if (empty($data) === false) {
				$html .= admin_form_date("shinsa-date-" . ($i + 1), $data['shinsa_date'], "shinsa-date-" . ($i + 1), 'ms-2');
				$html .= admin_form_dropdown($syubetsuList['result'], 'holder-grade-id-' . ($i + 1), 'ms-2', 'holder_grade_id', 'holder_grade_name', $data['holder_grade_id'], true);
			} else {
				$html .= admin_form_date("shinsa-date-" . ($i + 1), "", "shinsa-date-" . ($i + 1), "ms-2");
				$html .= admin_form_dropdown($syubetsuList['result'], 'holder-grade-id-' . ($i + 1), 'ms-2', 'holder_grade_id', 'holder_grade_name', '', true);
			}
			$html .= '</li>';
		}
	} else {
		// 中央審査以外の場合は1日分の審査日を表示
		$html .= '<li class="nav-item mb-2" role="presentation">';
		$data = $shinsaDetail['date_holder_grade']['result'][0] ?? null;
		if (empty($data) === false) {
			$html .= admin_form_date("shinsa-date-1", $data['shinsa_date'], "shinsa-date-1", 'ms-2');
			$html .= admin_form_dropdown($syubetsuList['result'], 'holder-grade-id-1', 'ms-2', 'holder_grade_id', 'holder_grade_name', $data['holder_grade_id'], true);
		} else {
			$html .= admin_form_date("shinsa-date-1", "", "shinsa-date-1", "ms-2");
			$html .= admin_form_dropdown($syubetsuList['result'], 'holder-grade-id-1', 'ms-2', 'holder_grade_id', 'holder_grade_name', '', true);
		}
		$html .= admin_form_hidden("shinsa-date-2", '');
		$html .= admin_form_hidden("holder-grade-id-2", '');
		$html .= admin_form_hidden("shinsa-date-3", '');
		$html .= admin_form_hidden("holder-grade-id-3", '');
		$html .= '</li>';
	}
	$html .= '</ul>';
	$html .= '</dd>';
	// 審査種別総合（中央審査のみ）
	if ($shinsaDetail['shinsa_class_id'] === SHINSA_CLASS_ID_CHUOU) {
		$html .= '<dt>審査種別総合（一覧表示用）</dt>';
		$html .= '<dd>';
		$html .= '<p class="mt-1 mb-0">';
		$html .= admin_form_dropdown($syubetsuList['result'], 'all-holder-grade-id', '', 'holder_grade_id', 'holder_grade_name', $shinsaDetail['all_holder_grade_id'], true);
		$html .= '</p>';
		$html .= '</dd>';
	} else {
		$html .= admin_form_hidden("all-holder-grade-id", $shinsaDetail['all_holder_grade_id']);
	}
	// 会場（第1〜3会場）
	$html .= '<dt>会場</dt>';
	$html .= '<dd>';
	for ($i = 0; $i < 3; $i++) {
		$kaijoId = NULL;
		$additionalInfo = NULL;
		if (empty($shinsaDetail['kaijo_list']) === false) {
			if (empty($shinsaDetail['kaijo_list']['result'][$i]['kaijo_id']) === false) {
				$kaijoId = $shinsaDetail['kaijo_list']['result'][$i]['kaijo_id'];
			}
			if (empty($shinsaDetail['kaijo_list']['result'][$i]['additional_info']) === false) {
				$additionalInfo = $shinsaDetail['kaijo_list']['result'][$i]['additional_info'];
			}

		}
		$html .= '<p class="mt-1 mb-0">';
		$html .= '第' . ($i + 1) . ' ： ' . admin_form_dropdown($kaijoList['result'], 'kaijo-id-' . ($i + 1), '', 'kaijo_id', 'kaijo_name', $kaijoId, true);
		$html .= '<div class="ms-2">備考 ' . admin_form_text('additional-info-' . ($i + 1), $additionalInfo, "", 'mt-1') . '</div>';
		$html .= '</p>';
	}
	$html .= '</dd>';
	$html .= '<dt>対象性別</dt>';
	$html .= '<dd>';
	$checked0 = '';
	$checked1 = '';
	$checked2 = '';
	switch ($shinsaDetail['gender_cd']) {
		case 1 :
			$checked1 = ' checked="checked"';
			break;
		case 2 :
			$checked2 = ' checked="checked"';
			break;
		default :
			$checked0 = ' checked="checked"';
	}
	$html .= '<input type="radio" id="gender0" name="gender-cd"' . $checked0 . ' value="' . GENDER_ALL . '">';
	$html .= '<label for="gender0">問わず</label>';
	$html .= '<input type="radio" id="gender1" name="gender-cd"' . $checked1 . ' value="' . GENDER_MALE . '">';
	$html .= '<label for="gender1">男性のみ</label>';
	$html .= '<input type="radio" id="gender2" name="gender-cd"' . $checked2 . ' value="' . GENDER_FEMALE . '">';
	$html .= '<label for="gender2">女性のみ</label>';
	$html .= '</dd>';
	// 全弓連締切日
	if ($shinsaDetail['shinsa_class_id'] === SHINSA_CLASS_ID_CHUOU) {
		$html .= '<dt>全弓連締切日</dt>';
		$html .= '<dd>';
		$html .= admin_form_date("uketuke-limit-zenkyuren", $shinsaDetail['uketuke_limit_zenkyuren'], "uketuke-limit-zenkyuren");
		$html .= '</dd>';
	} else {
		$html .= admin_form_hidden("uketuke-limit-zenkyuren", "");
	}
	// 愛弓連申込期間
	$html .= '<dt>愛弓連申込期間</dt>';
	$html .= '<dd>';
	$checked = '';
	$class = 'd-none';
	$label = '未定';
	if (empty($shinsaDetail['uketuke_limit_aikyuren_st']) === false or empty($shinsaDetail['uketuke_limit_aikyuren_ed']) === false) {
		$checked = ' checked="checked"';
		$class = '';
		$label = '設定';
	}
	$html .= '<span class="form-switch me-2">';
	$html .= '<input type="checkbox" class="form-check-input mt-1 me-1" id="uketuke-limit-aikyuren-set" ' . $checked . ' role="switch">';
	$html .= '<span id="uketuke-limit-aikyuren-text">' . $label . '</span>';
	$html .= '</span>';
	$html .= '<span id="uketuke-limit-aikyuren-area" class="' . $class . '">';
	$html .= admin_form_date("uketuke-limit-aikyuren-st", $shinsaDetail['uketuke_limit_aikyuren_st'], "uketuke-limit-aikyuren-st");
	$html .= ' ～ ';
	$html .= admin_form_date("uketuke-limit-aikyuren-ed", $shinsaDetail['uketuke_limit_aikyuren_ed'], "uketuke-limit-aikyuren-ed");
	$html .= '</span>';
	$html .= '</dd>';
	// WEB申込開始日

	// WEB申込開始日の算出（愛弓連申込開始日の1週間前） 曜日付き
	$webApplyStartDay = '';
	$webApplyStartDayHtml = '';
	$webApplyDescription = '愛弓連申込開始日の1週間前';
	if ($shinsaDetail['shinsa_class_id'] == SHINSA_CLASS_ID_RENGO) {
		// 連合審査の場合は審査日の3ヶ月前
		$webApplyDescription = '審査日の3ヶ月前';
		$webApplyStartDay = date('Y-m-d', strtotime($shinsaDetail['date_holder_grade']['result'][0]['shinsa_date'] . ' -3 month'));
		$webApplyStartDayHtml = date('Y/m/d', strtotime($shinsaDetail['date_holder_grade']['result'][0]['shinsa_date'] . ' -3 month'));
		$week = ['日', '月', '火', '水', '木', '金', '土'];
		$webApplyStartDayHtml .= '（' . $week[date('w', strtotime($webApplyStartDay))] . '）';
	} else {
		if (!empty($shinsaDetail['uketuke_limit_aikyuren_st'])) {
			$webApplyStartDay = date('Y-m-d', strtotime($shinsaDetail['uketuke_limit_aikyuren_st'] . ' -1 week'));
			$webApplyStartDayHtml = date('Y/m/d', strtotime($shinsaDetail['uketuke_limit_aikyuren_st'] . ' -1 week'));
			$week = ['日', '月', '火', '水', '木', '金', '土'];
			$webApplyStartDayHtml .= '（' . $week[date('w', strtotime($webApplyStartDay))] . '）';
		}
	}
	$html .= '<dt>' . KASUGAI_KYOKAI_NAME_SHORT . '申込開始日（' . $webApplyDescription . '）</dt>';
	$html .= '<dd id="web-apply-start-day-view">';
	$html .= $webApplyStartDayHtml;
	$html .= '</dd>';
	
	// 添付資料
	$html .= '<hr>';
	$html .= '<dt>添付資料（太字のファイルはお知らせ投稿されたものです）</dt>';
	$html .= '<dd>';
	$html .= form_file_document('shinsa', 'shinsa-files', $shinsaDetail['shinsa_document_list'], $uploadFileNum);
	$html .= '</dd>';
	
	$html .= '</dl>';

	$html .= admin_form_hidden("shinsa-id", $shinsaDetail['shinsa_id']);
	$html .= admin_form_hidden("shinsa-name", $shinsaName);
	$html .= admin_form_hidden("shinsa-class-id", $shinsaDetail['shinsa_class_id']);
	$html .= admin_form_hidden("web-apply-start-day", $webApplyStartDay);
	
	return $html;
}

/*
 * 審査登録・更新確認HTML
 * return
 */
function form_shinsa_regist_confirm($shinsaDetail, $shinsaDocumentList, $fileList) {

	$html = '';
	$html .= '<dl>';
	// 審査名
	$html .= '<dt class="text-danger fw-bold">審査名</dt>';
	$html .= '<dd>';
	$html .= $shinsaDetail['shinsa_name'];
	$html .= '</dd>';
	// 審査日・審査種別
	$html .= '<dt class="text-danger fw-bold">審査日・審査種別</dt>';
	$html .= '<dd>';
	if ($shinsaDetail['shinsa_class_id'] === SHINSA_CLASS_ID_CHUOU) {
		// 中央審査の場合は3日分の審査日を表示
		for ($i=1; $i<=3; $i++) {
			if (empty($shinsaDetail['shinsa_date_' . $i]) === false) {
				$html .= $shinsaDetail['shinsa_date_' . $i] . ' ' . $shinsaDetail['holder_grade_name_' . $i] . '<br>';
			}
		}
	} else {
		// 中央審査以外の場合は1日分の審査日を表示
		$html .= $shinsaDetail['shinsa_date_1'] . ' ' . $shinsaDetail['holder_grade_name_1'] . '<br>';
		// $data = $shinsaDetail['date_holder_grade']['result'][0] ?? null;
		// if (empty($data) === false) {
		// 	$html .= '【' . $data['holder_grade_name'] . '】' . $data['shinsa_date'];
		// }
	}
	$html .= '</dd>';
	// 審査種別総合（中央審査のみ）
	if ($shinsaDetail['shinsa_class_id'] === SHINSA_CLASS_ID_CHUOU) {
		$html .= '<dt class="text-danger fw-bold">審査種別総合</dt>';
		$html .= '<dd>';
		$html .= $shinsaDetail['all_holder_grade_name'];
		$html .= '</dd>';
	}
	// 会場
	for ($i = 0; $i < 3; $i++) {
		if (empty($shinsaDetail['kaijo_id_' . ($i + 1)]) === false) {
			$html .= '<dt class="text-danger fw-bold">第' . ($i + 1) . '会場</dt>';
			$html .= '<dd>';
			$html .= $shinsaDetail['kaijo_name_' . ($i + 1)];
			if (empty($shinsaDetail['additional_info_' . ($i + 1)]) === false) {
				$html .= '<br>備考 : ' . $shinsaDetail['additional_info_' . ($i + 1)];
			}
			$html .= '</dd>';
		}
	}
	// 対象性別
	$html .= '<dt class="text-danger fw-bold">対象性別</dt>';
	$html .= '<dd>';
	switch ($shinsaDetail['gender_cd']) {
		case 1 :
			$html .= '男性のみ';
			break;
		case 2 :
			$html .= '女性のみ';
			break;
		default :
			$html .= '問わず';
	}
	$html .= '<dd>';
	// 全弓連締切日
	if (empty($shinsaDetail['uketuke_limit_zenkyuren']) === false) {
		$html .= '<dt class="text-danger fw-bold">全弓連締切日</dt>';
		$html .= '<dd>';
		$html .= date_format_jp($shinsaDetail['uketuke_limit_zenkyuren'], true, DATE_FORMAT_YMD);
		$html .= '</dd>';
	}
	// 愛弓連申込期間
	if (empty($shinsaDetail['uketuke_limit_aikyuren_st']) === false or empty($shinsaDetail['uketuke_limit_aikyuren_ed']) === false) {
		$html .= '<dt class="text-danger fw-bold">愛弓連申込期間</dt>';
		$html .= '<dd>';
		if (empty($shinsaDetail['uketuke_limit_aikyuren_st']) === false) {
			// $html .= $shinsaDetail['uketuke_limit_aikyuren_st'];
			$html .= date_format_jp($shinsaDetail['uketuke_limit_aikyuren_st'], true, DATE_FORMAT_YMD);
		}
		$html .= ' ～ ';
		if (empty($shinsaDetail['uketuke_limit_aikyuren_ed']) === false) {
			// $html .= $shinsaDetail['uketuke_limit_aikyuren_ed'];
			$html .= date_format_jp($shinsaDetail['uketuke_limit_aikyuren_ed'], true, DATE_FORMAT_YMD);
		}
		$html .= '</dd>';
	}
	// WEB申込開始日
	if (empty($shinsaDetail['web_apply_start_day']) === false) {
		$html .= '<dt class="text-danger fw-bold">' . KASUGAI_KYOKAI_NAME_SHORT . '申込開始日</dt>';
		$html .= '<dd>';
		$html .= date_format_jp($shinsaDetail['web_apply_start_day'], true, DATE_FORMAT_YMD);
		$html .= '</dd>';
	}
	// 添付資料
	$html .= '<hr>';
	$html .= '<dt class="text-danger fw-bold">添付資料</dt>';
	$html .= '<dd>';
	// 登録済み添付資料
	$documentCnt = 0;
	if (empty($shinsaDocumentList['result']) === false) {
		$html .= '<section>';
		$html .= '<h3>[登録済み]</h3>';
		$html .= '<ul>';
		foreach ($shinsaDocumentList['result'] as $idy => $data) {
			$html .= '<li>';
			$html .= '<img class="icon" src="../' . get_file_ext_icon_path($data['document_ext']) . '" alt="' . $data['document_name'] . '">';
			$html .= $data['document_name'];
			$html .= '</li>';
		}
		$html .= '</ul>';
		$html .= '</section>';
		$documentCnt++;
	}
	// 新規添付資料
	if (empty($fileList) === false) {
		$html .= '<section>';
		$html .= '<h3>[新規]</h3>';
		$html .= '<ul>';
		foreach ($fileList as $idx => $data) {
			$html .= '<li>';
			$html .= '<img class="icon" src="../' . $data['file_ext_path'] . '" alt="' . $data['file_name'] . '">';
			$html .= $data['file_name'];
			$html .= '</li>';
		}
		$html .= '</ul>';
		$html .= '</section>';
		$documentCnt++;
	}

	if ($documentCnt === 0) {
		$html .= '添付資料なし';
	} 
	$html .= '</dd>';

	$html .= '</dl>';

	return $html;
}
