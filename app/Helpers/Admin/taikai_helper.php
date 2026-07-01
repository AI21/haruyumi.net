<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2023/10/09
 * Time: 11:32
 */

/*
 * 大会詳細フォームHTML
 * return
 */
function form_taikai_detail($taikaiDetail, $kaijoList, $kyokaiOfficerList, $uploadFileNum) {

	$html = '';
	// 大会回数のリストで1～100までを生成
	$taikaiNoList = array();
	for ($i = 1; $i <= 100; $i++) {
		$taikaiNoList[] = $i;	
	}
	
	$html .= '<dl>';
	// 大会名
	$html .= '<dt>大会名</dt>';
	$html .= '<dd>';
	$checked = '';
	$classMain = '';
	$classSub = 'd-none';
	$checkboxView = '別名表示しない';
	if (empty($taikaiDetail['taikai_sub_name']) === false) {
		$checked = ' checked="checked"';
		$classMain = 'd-none';
		$classSub = '';
		$checkboxView = '別名表示する';
	}
	// 大会名と大会回数の入力エリア
	$html .= '<p id="taikai-main-name" class="mt-1 mb-0 ' . $classMain . '">';
	if ($taikaiDetail['taikai_no_flg'] === DB_FLG_ON) {
		// 大会回数の表示フラグがONの場合は「第○回」を表示する
		$html .= admin_form_dropdown_number('taikai-no', 1, 100, $taikaiDetail['taikai_no'], true);
		$html .= $taikaiDetail['taikai_name'];
		$html .= admin_form_hidden('taikai-name', $taikaiDetail['taikai_name']);
	} else {
		$html .= $taikaiDetail['taikai_name'];
		$html .= admin_form_hidden('taikai-no', 0);
		$html .= admin_form_hidden('taikai-name', $taikaiDetail['taikai_name']);
	}
	$html .= '</p>';
	// 春日井市弓道協会主催の場合
	if ($taikaiDetail['kasugai_flg'] === DB_FLG_ON) {
		// 別名表示の入力エリアと表示フラグのチェックボックス
		$html .= '<p id="taikai-sub-name-area" class="mt-1 mb-0 ' . $classSub . '">';
		$html .= admin_form_text("taikai-sub-name", $taikaiDetail['taikai_sub_name']);
		$html .= '</p>';
		$html .= '<span class="form-switch me-2">';
		$html .= '<input type="checkbox" class="form-check-input mt-1 me-1" id="taikai-name-set" ' . $checked . ' role="switch">';
		$html .= '<span id="taikai-name-text">' . $checkboxView . '</span>';
		$html .= '</span>';
	}
	$html .= admin_form_hidden('taikai-no-flg', $taikaiDetail['taikai_no_flg']);
	$html .= '</dd>';
	// 大会日
	$html .= '<dt>大会日</dt>';
	$html .= '<dd>';
	$html .= admin_form_date("taikai-date-st", $taikaiDetail['taikai_date_st'], "taikai-date-st");
	// 大会終了日：協会主催以外
	if ($taikaiDetail['kasugai_flg'] === DB_FLG_OFF) {
		$html .= ' ～ ';
		$html .= admin_form_date("taikai-date-ed", $taikaiDetail['taikai_date_ed'], "taikai-date-ed");
	} else {
		$html .= admin_form_hidden("taikai-date-ed", "");
	}
	$html .= '</dd>';
	// 会場
	$html .= '<dt>会場</dt>';
	$html .= '<dd>';
	$checked = '';
	$classMain = '';
	$classOther = 'd-none';
	$checkboxView = '特設会場等を設定しない';
	if (empty($taikaiDetail['kaijo_other_name']) === false) {
		$checked = ' checked="checked"';
		$classMain = 'd-none';
		$classOther = '';
		$checkboxView = '特設会場等を設定する';
	}
	$html .= '<p class="mt-1 mb-0">';
	$html .= admin_form_dropdown($kaijoList['result'], 'kaijo-id', $classMain, 'kaijo_id', 'kaijo_name', $taikaiDetail['kaijo_id'], true);
	$html .= admin_form_text("kaijo-other-name", $taikaiDetail['kaijo_other_name'], "", $classOther);
	$html .= '</p>';
	$html .= '<p class="form-switch me-2">';
	$html .= '<input type="checkbox" id="kaijo-other-name-set" class="form-check-input mt-1 me-1" ' . $checked . ' role="switch">';
	$html .= '<span id="kaijo-other-name-text">' . $checkboxView . '</span>';
	$html .= '</p>';
	$html .= '</dd>';
	// 開場時間
	$html .= '<dt>開場時間</dt>';
	$html .= '<dd>';
	$html .= '<span class="form-switch me-2">';
	$checked = '';
	$class = 'd-none';
	$label = '未定';
	if (empty($taikaiDetail['taikai_open_time']) === false) {
		$checked = ' checked="checked"';
		$class = '';
		$label = '設定';
	}
	$html .= '<input type="checkbox" class="form-check-input mt-1 me-1" id="taikai-open-time-set" ' . $checked . ' role="switch">';
	$html .= '<label for="taikai-open-time-set" id="taikai-open-time-text">' . $label . '</label>';
	$html .= '</span>';
	$html .= admin_form_time("taikai-open-time", $taikaiDetail['taikai_open_time'], "taikai-open-time", $class);
	$html .= '</dd>';
	// 受付時間
	$html .= '<dt>受付時間</dt>';
	$html .= '<dd>';
	$html .= '<span class="form-switch me-2">';
	$checked = '';
	$class = 'd-none';
	$label = '未定';
	if (empty($taikaiDetail['taikai_uketuke_time']) === false) {
		$checked = ' checked="checked"';
		$class = '';
		$label = '設定';
	}
	$html .= '<input type="checkbox" class="form-check-input mt-1 me-1" id="taikai-uketuke-time-set" ' . $checked . ' role="switch">';
	$html .= '<label for="taikai-uketuke-time-set" id="taikai-uketuke-time-text">' . $label . '</label>';
	$html .= '</span>';
	$html .= admin_form_time("taikai-uketuke-time", $taikaiDetail['taikai_uketuke_time'], "taikai-uketuke-time", $class);
	$html .= '</dd>';
	// 大会時間
	$html .= '<dt>大会時間</dt>';
	$html .= '<dd>';
	$html .= '<span class="form-switch me-2">';
	$checked = '';
	$class = 'd-none';
	$label = '未定';
	if (empty($taikaiDetail['taikai_time_st']) === false or empty($taikaiDetail['taikai_time_ed']) === false) {
		$checked = ' checked="checked"';
		$class = '';
		$label = '設定';
	}
	$html .= '<input type="checkbox" class="form-check-input mt-1 me-1" id="taikai-time-set" ' . $checked . ' role="switch">';
	$html .= '<label for="taikai-time-set" id="taikai-time-text">' . $label . '</label>';
	$html .= '</span>';
	$html .= '<span id="taikai-time-area" class="' . $class . '">';
	$html .= admin_form_time("taikai-time-st", $taikaiDetail['taikai_time_st'], "taikai-time-st");
	$html .= ' ～ ';
	$html .= admin_form_time("taikai-time-ed", $taikaiDetail['taikai_time_ed'], "taikai-time-ed");
	$html .= '</span>';
	$html .= '</dd>';
	// 参加受付期間
	$html .= '<dt>参加受付期間</dt>';
	$html .= '<dd>';
	$checked = '';
	$class = 'd-none';
	$label = '未定';
	if (empty($taikaiDetail['taikai_uketuke_st']) === false or empty($taikaiDetail['taikai_uketuke_ed']) === false) {
		$checked = ' checked="checked"';
		$class = '';
		$label = '設定';
	}
	$html .= '<span class="form-switch me-2">';
	$html .= '<input type="checkbox" class="form-check-input mt-1 me-1" id="taikai-uketuke-set" ' . $checked . ' role="switch">';
	$html .= '<label for="taikai-uketuke-set" id="taikai-uketuke-text">' . $label . '</label>';
	$html .= '</span>';
	$html .= '<span id="taikai-uketuke-area" class="' . $class . '">';
	$html .= admin_form_date("taikai-uketuke-st", $taikaiDetail['taikai_uketuke_st'], "taikai-uketuke-st");
	$html .= ' ～ ';
	$html .= admin_form_date("taikai-uketuke-ed", $taikaiDetail['taikai_uketuke_ed'], "taikai-uketuke-ed");
	$html .= '</span>';
	$html .= '</dd>';
	// WEB申込
	$html .= '<dt>' . KASUGAI_KYOKAI_NAME_SHORT . 'で参加受付</dt>';
	$html .= '<dd>';
	$html .= '<div class="form-switch">';
	$checked = '';
	$label = 'しない';
	if ($taikaiDetail['web_apply_flg'] === DB_FLG_ON) {
		$checked = ' checked="checked"';
		$label = 'する';
	}
	$html .= '<input type="checkbox" class="form-check-input me-1" id="web-apply-flg" role="switch" ' . $checked . '>';
	$html .= '<label for="web-apply-flg" id="web-apply-flg-text">' . $label . '</label>';
	$html .= '</div>';
	$html .= '</dd>';
	// 個人申込：協会主催以外
	if ($taikaiDetail['kasugai_flg'] === DB_FLG_OFF) {
		$html .= '<dt>個人申込</dt>';
		$html .= '<dd>';
		$html .= '<div class="form-switch">';
		$checked = '';
		$label = 'しない';
		if ($taikaiDetail['indi_apply_flg'] === 1 ) {
			$checked = ' checked="checked"';
			$label = 'する';
		}
		$html .= '<input type="checkbox" class="form-check-input me-1" name="member_list_mail_send" id="member-list-mail-send" role="switch" ' . $checked . '>';
		$html .= '<label for="member-list-mail-send" id="member-list-mail-text">' . $label . '</label>';
		$html .= '</div>';
		$html .= '</dd>';
	} else {
		$html .= admin_form_hidden("member-list-mail-send", "");
	}
	// 対象性別：協会主催以外
	if ($taikaiDetail['kasugai_flg'] === DB_FLG_OFF) {
		$html .= '<dt>対象性別</dt>';
		$html .= '<dd>';
		$checked0 = '';
		$checked1 = '';
		$checked2 = '';
		switch ($taikaiDetail['gender_cd']) {
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
	} else {
		// $html .= admin_form_hidden("gender-cd", GENDER_ALL);
		$html .= '<input type="radio" id="gender0" class="d-none" name="gender-cd" checked="checked" value="' . GENDER_ALL . '">';
	}
	// 参加可能年齢：協会主催以外
	if ($taikaiDetail['kasugai_flg'] === DB_FLG_OFF) {
		$html .= '<dt>参加可能年齢</dt>';
		$html .= '<dd>';
		$html .= '<span class="form-switch me-2">';
		$checked = '';
		$class = 'd-none';
		$label = '年齢不問';
		if ($taikaiDetail['age_limit_min'] > 0 or $taikaiDetail['age_limit_max'] > 0) {
			$checked = ' checked="checked"';
			$class = '';
			$label = '設定';
		}
		$html .= '<input type="checkbox" class="form-check-input mt-1 me-1" id="age-limit-set" ' . $checked . ' role="switch">';
		$html .= '<label for="age-limit-set" id="taikai-age-text">' . $label . '</label>';
		$html .= '</span>';
		$html .= '<span id="taikai-age-area" class="' . $class . '">';
		$html .= admin_form_text("age-limit-min", $taikaiDetail['age_limit_min'], "", "form-w-3e text-end");
		$html .= ' ～ ';
		$html .= admin_form_text("age-limit-max", $taikaiDetail['age_limit_max'], "", "form-w-3e text-end");
		$html .= ' 歳</span>';
		$html .= '</dd>';
	} else {
		$html .= admin_form_hidden("age-limit-min", "0");
		$html .= admin_form_hidden("age-limit-max", "0");
	}
	// 参加資格
	$html .= '<dt>参加資格</dt>';
	$html .= '<dd>' . admin_form_textarea("eligibility", $taikaiDetail['eligibility']) . '</dd>';
	// 競技ルール
	$html .= '<dt>競技ルール</dt>';
	$html .= '<dd>' . admin_form_textarea("competition-rules", $taikaiDetail['competition_rules']) . '</dd>';
	// 表彰
	$html .= '<dt>表彰</dt>';
	$html .= '<dd>' . admin_form_textarea("awards", $taikaiDetail['awards']) . '</dd>';
	// 参加費：協会主催以外
	if ($taikaiDetail['kasugai_flg'] === DB_FLG_OFF) {
		$html .= '<dt>参加費</dt>';
		$html .= '<dd>' . admin_form_textarea("entry-fee", $taikaiDetail['entry_fee']) . '</dd>';
	} else {
		$html .= admin_form_hidden("entry-fee", "");
	}
	// 連絡事項
	$html .= '<dt>連絡事項</dt>';
	$html .= '<dd>' . admin_form_textarea("contact-info", $taikaiDetail['contact_info']) . '</dd>';
	// 添付資料
	$html .= '<hr>';
	$html .= '<dt>添付資料（太字のファイルはお知らせ投稿されたものです）</dt>';
	$html .= '<dd>';
	$html .= form_file_document('taikai', 'taikai-files', $taikaiDetail['taikai_document_list'], $uploadFileNum);
	$html .= '</dd>';
	
	$html .= '</dl>';

	$html .= admin_form_hidden("taikai-id", $taikaiDetail['taikai_id']);
	$html .= admin_form_hidden("kasugai-flg", $taikaiDetail['kasugai_flg']);
	
	return $html;
}

/*
 * 大会登録・更新確認HTML
 * return
 */
function form_taikai_regist_confirm($taikaiDetail, $taikaiDocumentList, $fileList) {

	$html = '';
	$html .= '<dl>';
	// 大会名
	$html .= '<dt class="text-danger fw-bold">大会名</dt>';
	$html .= '<dd>';
	if ($taikaiDetail['taikai_name_set'] === FLG_OFF) {
		// 大会回数の表示フラグがONの場合は「第○回」を表示する
		if ($taikaiDetail['taikai_no_flg'] === DB_FLG_ON) {
			$html .= '第' . $taikaiDetail['taikai_no'] . '回 ' . $taikaiDetail['taikai_name'];
		} else {
			$html .= $taikaiDetail['taikai_name'];
		}
	} else {
		// 別名表示の場合は入力した別名を表示する
		$html .= $taikaiDetail['taikai_sub_name'];
	}
	$html .= '</dd>';
	// 大会日
	$html .= '<dt class="text-danger fw-bold">大会日</dt>';
	$html .= '<dd>'. $taikaiDetail['taikai_date_st'];
	// 大会終了日：協会主催以外
	if ($taikaiDetail['kasugai_flg'] === DB_FLG_OFF) {
		$html .= ' ～ '. $taikaiDetail['taikai_date_ed'];
	}
	$html .= '</dd>';
	// 会場
	$html .= '<dt class="text-danger fw-bold">会場</dt>';
	$html .= '<dd>';
	if ($taikaiDetail['kaijo_other_name_set'] === FLG_OFF) {
		// 特設会場等が設定されていない場合は会場マスタの会場名を表示する
		$html .= $taikaiDetail['kaijo_name'];
	} else {
		// 特設会場等が設定されている場合は特設会場等を表示する
		$html .= $taikaiDetail['kaijo_other_name'];
	}
	$html .= '</dd>';
	// 開場時間
	$html .= '<dt class="text-danger fw-bold">開場時間</dt>';
	$html .= '<dd>';
	if ($taikaiDetail['taikai_open_time_set'] === DB_FLG_ON) {
		$html .= $taikaiDetail['taikai_open_time'];
	} else {
		$html .= '未定';
	}
	$html .= '</dd>';
	// 受付時間
	$html .= '<dt class="text-danger fw-bold">受付時間</dt>';
	$html .= '<dd>';
	if ($taikaiDetail['taikai_uketuke_time_set'] === DB_FLG_ON) {
		$html .= $taikaiDetail['taikai_uketuke_time'];
	} else {
		$html .= '未定';
	}
	$html .= '</dd>';
	// 大会時間
	$html .= '<dt class="text-danger fw-bold">大会時間</dt>';
	$html .= '<dd>';
	if ($taikaiDetail['taikai_time_set'] === DB_FLG_ON) {
		$html .= $taikaiDetail['taikai_time_st'] . ' ～ ' . $taikaiDetail['taikai_time_ed'];
	} else {
		$html .= '未定';
	}
	$html .= '</dd>';
	// 大会受付期間
	$html .= '<dt class="text-danger fw-bold">大会受付日程</dt>';
	$html .= '<dd>';
	if ($taikaiDetail['taikai_uketuke_set'] === DB_FLG_ON) {
		$html .= $taikaiDetail['taikai_uketuke_st'] . ' ～ ' . $taikaiDetail['taikai_uketuke_ed'];
	} else {
		$html .= '未定';
	}
	$html .= '</dd>';
	// 個人申込：協会主催以外
	// if ($taikaiDetail['kasugai_flg'] === DB_FLG_OFF) {
	// 	$html .= '<dt class="text-danger fw-bold">個人申込</dt>';
	// 	$html .= '<dd>';
	// 	$html .= '<div class="form-switch">';
	// 	$checked = '';
	// 	if ($taikaiDetail['indi_apply_flg'] === 1 ) {
	// 		$checked = ' checked="checked"';
	// 	}
	// 	$html .= '<input type="checkbox" class="form-check-input" name="member_list_mail_send" id="member-list-mail-send" role="switch" ' . $checked . '>';
	// 	$html .= '<span id="member-list-mail-text">する</span>';
	// 	$html .= '</div>';
	// 	$html .= '</dd>';
	// } else {
	// 	$html .= admin_form_hidden("member-list-mail-send", "");
	// }
	// 対象性別：協会主催以外
	if ($taikaiDetail['kasugai_flg'] === DB_FLG_OFF) {
		$html .= '<dt class="text-danger fw-bold">対象性別</dt>';
		$html .= '<dd>';
		switch ($taikaiDetail['gender_cd']) {
			case 1 :
				$html .= '男性のみ';
				break;
			case 2 :
				$html .= '女性のみ';
				break;
			default :
				$html .= '問わず';
		}
		$html .= '</dd>';
	} else {
		$html .= admin_form_hidden("gender-cd", "");
	}
	// WEB申込
	$html .= '<dt class="text-danger fw-bold">' . KASUGAI_KYOKAI_NAME_SHORT . 'で参加受付</dt>';
	$html .= '<dd>';
	if ($taikaiDetail['web_apply_flg'] === DB_FLG_ON) {
		$html .= 'する';
	} else {
		$html .= 'しない';
	}
	$html .= '</dd>';
	// 参加可能年齢：協会主催以外
	if ($taikaiDetail['kasugai_flg'] === DB_FLG_OFF) {
		$html .= '<dt class="text-danger fw-bold">参加可能年齢</dt>';
		$html .= '<dd>';
		if ($taikaiDetail['age_limit_set'] === DB_FLG_ON) {
			$html .= $taikaiDetail['age_limit_min'] . ' ～ ' . $taikaiDetail['age_limit_max'] . ' 歳';
		} else {
			$html .= '年齢不問';
		}
		$html .= '</dd>';
	} else {
		// 協会主催の場合は参加可能年齢は表示せず、hiddenで値を保持する
		$html .= admin_form_hidden("age-limit-min", "");
		$html .= admin_form_hidden("age-limit-max", "");
	}
	// 参加資格
	$html .= '<dt class="text-danger fw-bold">参加資格</dt>';
	if (empty($taikaiDetail['eligibility']) === false) {
		// 改行コードを<br>タグに変換して表示する
		$html .= '<dd>' . nl2br($taikaiDetail['eligibility']) . '</dd>';
	} else {
		$html .= '<dd>なし（非表示）</dd>';
	}
	$html .= '<dd>' . $taikaiDetail['eligibility'] . '</dd>';
	// 競技ルール
	$html .= '<dt class="text-danger fw-bold" class="text-danger fw-bold">競技ルール</dt>';
	if (empty($taikaiDetail['competition_rules']) === false) {
		// 改行コードを<br>タグに変換して表示する
		$html .= '<dd>' . nl2br($taikaiDetail['competition_rules']) . '</dd>';
	} else {
		$html .= '<dd>なし（非表示）</dd>';
	}
	// 表彰
	$html .= '<dt class="text-danger fw-bold">表彰</dt>';
	if (empty($taikaiDetail['awards']) === false) {
		// 改行コードを<br>タグに変換して表示する
		$html .= '<dd>' . nl2br($taikaiDetail['awards']) . '</dd>';
	} else {
		$html .= '<dd>なし（非表示）</dd>';
	}
	// 参加費：協会主催以外
	if ($taikaiDetail['kasugai_flg'] === DB_FLG_OFF) {
		$html .= '<dt class="text-danger fw-bold">参加費</dt>';
		if (empty($taikaiDetail['entry_fee']) === false) {
			// 改行コードを<br>タグに変換して表示する
			$html .= '<dd>' . nl2br($taikaiDetail['entry_fee']) . '</dd>';
		} else {
			$html .= '<dd>なし（非表示）</dd>';
		}
	} else {
		$html .= admin_form_hidden("entry-fee", "");
	}
	// 連絡事項
	$html .= '<dt class="text-danger fw-bold">連絡事項</dt>';
	if (empty($taikaiDetail['contact_info']) === false) {
		// 改行コードを<br>タグに変換して表示する
		$html .= '<dd>' . nl2br($taikaiDetail['contact_info']) . '</dd>';
	} else {
		$html .= '<dd>なし（非表示）</dd>';
	}
	// 添付資料
	$html .= '<hr>';
	$html .= '<dt class="text-danger fw-bold">添付資料</dt>';
	$html .= '<dd>';
	// 登録済み添付資料
	$documentCnt = 0;
	if (empty($taikaiDocumentList['result']) === false) {
		$html .= '<section>';
		$html .= '<h3>[登録済み]</h3>';
		$html .= '<ul>';
		foreach ($taikaiDocumentList['result'] as $idy => $data) {
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
