<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2023/10/09
 * Time: 11:32
 */

/*
 * 会員登録・更新の内容確認HTMLを取得
 * return
 */
function member_regist_confirm($memberData, $holderGradeList) {

	$html = '';
	$genderList = [
		['code' => 0, 'name' => '未登録'],
		['code' => 1, 'name' => '男性'],
		['code' => 2, 'name' => '女性'],
	];

	// 段位・級位を分割
    $holder = $memberData['holder_grade_cd'];
    $holderArr = explode('|', $holder);
    $holder_id = isset($holderArr[0]) ? $holderArr[0] : '';
    $grade_id = isset($holderArr[1]) ? $holderArr[1] : '';

    // 段位名・級位名を取得
    $holder_name = '';
    $grade_name = '';
    if (!empty($holderGradeList['result'])) {
        foreach ($holderGradeList['result'] as $row) {
            if (isset($row['holder_id']) && $row['holder_id'] == $holder_id) {
                $holder_name = $row['holder_name'];
            }
            if (isset($row['grade_id']) && $row['grade_id'] == $grade_id) {
                $grade_name = $row['grade_name'];
            }
        }
    }
	if (empty($holder_name) === true && empty($grade_name) === true) {
		$grade_name .= 'なし';
	}

	$html .= '<dl>';
	// 会員名
	$html .= '<dt class="text-danger fw-bold">会員名</dt>';
	$html .= '<dt class="ms-3">' . $memberData['member_name_f'] . ' ' . $memberData['member_name_s'] . '</dt>';
	// 会員名よみかな
	$html .= '<dt class="text-danger fw-bold mt-1">会員名よみかな</dt>';
	$html .= '<dt class="ms-3">' . $memberData['member_kana_f'] . ' ' . $memberData['member_kana_s'] . '</dt>';
	// 性別
	$html .= '<dt class="text-danger fw-bold mt-1">性別</dt>';
	$html .= '<dt class="ms-3">' . get_code_name($genderList, $memberData['gender_cd']) . '</dt>';
	// 称号と段位・級位
	$html .= '<dt class="text-danger fw-bold mt-1">称号・段(級)位</dt>';
    $html .= '<dt class="ms-3">' . $holder_name . $grade_name . '</dt>';
	// 称号取得日
	if (empty($memberData['holder_acquired_day']) === false) {
		$html .= '<div id="holder-acquired-day-area" class="ms-3">';
		$html .= '<dt class="text-danger fw-bold mt-1">称号取得日</dt>';
		$date = date('Y年m月d日', strtotime($memberData['holder_acquired_day']));
		$html .= '<dt class="ms-3">' . $date . '</dt>';
		$html .= '</div>';
	}
	// 段(級)位取得日
	if (empty($memberData['grade_acquired_day']) === false) {
		$html .= '<div id="holder-acquired-day-area" class="ms-3">';
		$html .= '<dt class="text-danger fw-bold mt-1">段(級)位取得日</dt>';
		$date = date('Y年m月d日', strtotime($memberData['grade_acquired_day']));
		$html .= '<dt class="ms-3">' . $date . '</dt>';
		$html .= '</div>';
	}
	// 春日井弓道協会メイン会員
	$html .= '<dt class="text-danger fw-bold mt-1">春日井弓道協会メイン会員</dt>';
	$html .= '<dt class="ms-3">' . get_form_checkbox_name($memberData['kasugai_regist_flg']) . '</dt>';
	// 春日井弓道協会登録日
	$html .= '<dt class="text-danger fw-bold mt-1">春日井弓道協会登録日</dt>';
    $date = date('Y年m月d日', strtotime($memberData['kasugai_regist_date']));
    $html .= '<dt class="ms-3">' . $date . '</dt>';
	// 愛弓連登録
	$html .= '<dt class="text-danger fw-bold mt-1">愛弓連登録</dt>';
	$html .= '<dt class="ms-3">' . get_form_checkbox_name($memberData['aiti_renmei_regist_flg']) . '</dt>';
	// お知らせメール受信
	// $html .= '<dt class="text-danger fw-bold mt-1">お知らせメール受信</dt>';
	// $html .= '<dt class="ms-3">' . get_form_checkbox_name($memberData['notice_send_flg']) . '</dt>';
	// メールアドレス
	$html .= '<dt class="text-danger fw-bold mt-1">メールアドレス</dt>';
	$html .= '<dt class="ms-3">' . $memberData['mail_address'] . '</dt>';

	$html .= '</dl>';

	return $html;
}