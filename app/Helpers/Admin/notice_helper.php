<?php
/**
 * Created by PhpStorm.
 * User: imac_pc
 * Date: 2023/10/09
 * Time: 11:32
 */

/*
 * お知らせ登録・更新の内容確認HTMLを取得
 * return
 */
function notice_regist_confirm($noticeData, $existingDocumentList, $uploadFiles) {

	$html = '';
	// return $html;

	$html .= '<dl>';
	// カテゴリー
	$html .= '<dt class="text-danger fw-bold">カテゴリー</dt>';
	$html .= '<dt class="ms-3">' . $noticeData['notice_category'] . '</dt>';
	// タイトル
	$html .= '<dt class="text-danger fw-bold mt-1">タイトル</dt>';
	$html .= '<dt class="ms-3">' . $noticeData['notice_title'] . '</dt>';
	// 本文
	$html .= '<dt class="text-danger fw-bold mt-1">本文</dt>';
	$html .= '<dt class="ms-3">' . nl2br($noticeData['notice_body']) . '</dt>';
	// 関連イベント
	$html .= '<dt class="text-danger fw-bold mt-1">関連イベント</dt>';
	$html .= '<dt class="ms-3">' . $noticeData['relation_event'] . '</dt>';
	// 参加者のみにメール配信
	$html .= '<dt class="text-danger fw-bold mt-1">参加者のみにメール配信</dt>';
	if (empty($noticeData['regist_user_mail_flg']) === false && $noticeData['regist_user_mail_flg'] == 1) {
		$html .= '<dt class="ms-3">する</dt>';
	} else {
		$html .= '<dt class="ms-3">しない</dt>';
	}
	// 添付資料
	$html .= '<dt class="text-danger fw-bold mt-1">添付資料</dt>';
	$html .= '<dd class="notice-file-confrim" id="notice-file-confrim">';
	$html .= '<section>';
	if (empty($existingDocumentList) === false) {
		$html .= '<h3>[登録済み]</h3>';
		$html .= '<ul>';
		foreach ($existingDocumentList as $document) {
			$html .= '<li>';
			$html .= '<img class="icon" src="' . base_url() . $document['file_ext_path'] . '" alt="' . $document['file_name'] . '">';
			$html .= '<span>' . $document['file_name'] . '</span>';
			$html .= '</li>';
		}
		$html .= '</ul>';
		$html .= '</section>';
	} elseif (empty($uploadFiles) === false) {
		$html .= '<h3>[新規]</h3>';
		$html .= '<ul>';
		foreach ($uploadFiles as $file) {
			$html .= '<li>';
			$html .= '<img class="icon" src="' . base_url() . $file['file_ext_path'] . '" alt="' . $file['file_name'] . '">';
			$html .= '<span>' . $file['file_name'] . '</span>';
			$html .= '</li>';
		}
		$html .= '</ul>';
	} else {
		$html .= '<h3>[新規]</h3>';
		$html .= '添付資料なし';
	}
	$html .= '</section>';
	$html .= '</dd>';

	$html .= '</dl>';

	return $html;
}