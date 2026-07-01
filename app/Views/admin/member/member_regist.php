<?php
	$memberId 				= (!isset($memberDetail->member_id)) ? '' : $memberDetail->member_id;
	$nameF 					= (!isset($memberDetail->name_f)) ? '' : $memberDetail->name_f;
	$nameS 					= (!isset($memberDetail->name_s)) ? '' : $memberDetail->name_s;
	$kanaF 					= (!isset($memberDetail->kana_f)) ? '' : $memberDetail->kana_f;
	$kanaS 					= (!isset($memberDetail->kana_s)) ? '' : $memberDetail->kana_s;
	$genderCd 				= (!isset($memberDetail->gender_cd)) ? '' : $memberDetail->gender_cd;
	$holderId 				= (!isset($memberDetail->holder_id)) ? '' : $memberDetail->holder_id;
	$gradeId 				= (!isset($memberDetail->grade_id)) ? '' : $memberDetail->grade_id;
	$holderAcquiredDay 		= (!isset($memberDetail->holder_acquired_day)) ? '' : $memberDetail->holder_acquired_day;
	$gradeAcquiredDay 		= (!isset($memberDetail->grade_acquired_day)) ? '' : $memberDetail->grade_acquired_day;
	$kasugaiRegistFlg		= (!isset($memberDetail->kasugai_regist_flg)) ? DB_FLG_ON : $memberDetail->kasugai_regist_flg;
	$kasugaiRegistDate		= (!isset($memberDetail->kasugai_regist_date)) ? date('Y-m-d') : $memberDetail->kasugai_regist_date;
	$aitiRenmeiRegistFlg	= (!isset($memberDetail->aiti_renmei_regist_flg)) ? DB_FLG_OFF : $memberDetail->aiti_renmei_regist_flg;
	// $noticeSendFlg			= (!isset($memberDetail->notice_send_flg)) ? DB_FLG_ON : $memberDetail->notice_send_flg;
	$mailAddress			= (!isset($memberDetail->mail_address)) ? '' : $memberDetail->mail_address;
	// 性別チェック
	$genderMenMarked = '';
	$genderWomanMarked = '';
	$genderOtherMarked = '';
	switch ($genderCd) {
		case '1':
			$genderMenMarked = 'checked="checked"';
			break;
		case '2':
			$genderWomanMarked = 'checked="checked"';
			break;
		case '0':
			$genderOtherMarked = 'checked="checked"';
			break;
	}
	// 称号・段位・級位
	$holderGgradeId = $holderId . '|' . $gradeId;
	$holderArea = (empty($holderId) === true) ? ' d-none' : '';
	$gradeArea = (empty($gradeId) === true) ? ' d-none' : '';
	// 春日井弓道協会メイン会員
	$kasugaiRegistChecked = 'checked="checked"';
	$kasugaiRegistText = '主会員';
	if ($kasugaiRegistFlg === DB_FLG_OFF) {
		$kasugaiRegistChecked = '';
		$kasugaiRegistText = '他支部・他協会会員';
	}
	// 愛弓連登録
	$aitiRenmeiRegistChecked = 'checked="checked"';
	$aitiRenmeiRegistText = 'している';
	if ($aitiRenmeiRegistFlg === DB_FLG_OFF) {
		$aitiRenmeiRegistChecked = '';
		$aitiRenmeiRegistText = 'していない';
	}
	// お知らせメール受信
	// $noticeSendChecked = 'checked="checked"';
	// $noticeSendText = 'する';
	// if ($noticeSendFlg === DB_FLG_OFF) {
	// 	$noticeSendChecked = '';
	// 	$noticeSendText = 'しない';
	// }
?>
<div class="container-md">
	<main>
		<section>
			<div><a href="#" onclick="history.back()" return false;>戻る</a></div>
			<div id="member-regist-area">
				<form id="member-regist">
				<dl>
					<dt>会員名</dt>
					<dd>
						性：<?= admin_form_text('member-name-f', $nameF, 'member-name-f', 'form-w-7e'); ?>
						名：<?= admin_form_text('member-name-s', $nameS, 'member-name-s', 'form-w-7e'); ?>
					</dd>
					<dt>会員名よみかな</dt>
					<dd>
						性：<?= admin_form_text('member-kana-f', $kanaF, 'member-kana-f', 'form-w-7e'); ?>
						名：<?= admin_form_text('member-kana-s', $kanaS, 'member-kana-s', 'form-w-7e'); ?>
					</dd>
					<dt>性別</dt>
					<dd>
						<div>
							<input type="radio" name="gender-cd" id="gender-cd-1" <?= $genderMenMarked; ?>>
							<lavel for="gender-cd-1">男性</lavel>
							<input type="radio" name="gender-cd" id="gender-cd-2" class="ms-2" <?= $genderWomanMarked; ?>>
							<lavel for="gender-cd-2">女性</lavel>
							<input type="radio" name="gender-cd" id="gender-cd-0" class="ms-2" <?= $genderOtherMarked; ?>>
							<lavel for="gender-cd-0">未登録</lavel>
						</div>
					</dd>
					<?php if ($holderGradeList['numRows'] > 0) : ?>
					<dt>称号・段(級)位</dt>
					<dd>
						<select id="holder-grade-cd" class="form-w-10e">
							<option value="">なし</option>
							<?php
								foreach ($holderGradeList['result'] as $idx => $data) {
									$selected = '';
									if ($holderGgradeId === $data['holder_id'] . '|' . $data['grade_id']) {
										$selected = ' selected="selected"';
									}
									echo '<option value="' . $data['holder_id'] . '|' . $data['grade_id'] . '" '. $selected . '>' . $data['holder_name'] . $data['grade_name'] . '</option>';
								}
							?>
						</select>
					</dd>
					<div id="holder-acquired-day-area" class="ms-3<?= $holderArea; ?>">
						<dt>称号取得日</dt>
						<dd>
							<div>
								<?= admin_form_date('holder-acquired-day', $holderAcquiredDay, 'holder-acquired-day', '', '', date('Y-m-d')); ?>
							</div>
						</dd>
					</div>
					<div id="grade-acquired-day-area" class="ms-3<?= $gradeArea; ?>">
						<dt>段(級)位取得日</dt>
						<dd>
							<div>
								<?= admin_form_date('grade-acquired-day', $gradeAcquiredDay, 'grade-acquired-day', '', '', date('Y-m-d')); ?>
							</div>
						</dd>
					</div>
					<?php endif ; ?>
					<dt>春日井弓道協会メイン会員</dt>
					<dd>
						<div class="form-switch">
							<input type="checkbox" class="form-check-input" id="kasugai-regist-flg" <?= $kasugaiRegistChecked; ?> role="switch">
							<label for="kasugai-regist-flg" id="kasugai-regist-main-text"><?= $kasugaiRegistText; ?></label>
						</div>
					</dd>
					<dt>春日井弓道協会登録日</dt>
					<dd>
						<div>
							<?= admin_form_date('kasugai-regist-date', $kasugaiRegistDate, 'kasugai-regist-date', '', '', date('Y-m-d')); ?>
						</div>
					</dd>
					<dt>愛弓連登録</dt>
					<dd>
						<div class="form-switch">
							<input type="checkbox" class="form-check-input" id="aiti-renmei-regist-flg" <?= $aitiRenmeiRegistChecked; ?> role="switch">
							<label for="aiti-renmei-regist-flg" id="aiti-renmei-regist-text"><?= $aitiRenmeiRegistText; ?></label>
						</div>
					</dd>
<?php /*
					<dt>お知らせメール受信</dt>
					<dd>
						<div class="form-switch">
							<input type="checkbox" class="form-check-input" id="notice-send-flg" <?= $noticeSendChecked; ?> role="switch">
							<label for="notice-send-flg" id="notice-send-text"><?= $noticeSendText; ?></label>
						</div>
					</dd>
*/ ?>
					<dt>メールアドレス</dt>
					<dd>
						<?= admin_form_text('mail-address', $mailAddress, 'mail-address', 'form-w-mail'); ?>
					</dd>
					</dl>
				<input type="hidden" id="member-id" value="<?= $memberId; ?>">
				<?= admin_form_hidden('regist-mode', $mode); ?>
				</form>
				<div class="regist-area">
					<?php if ($mode === MODE_REGIST) : ?>
					<button type="button" id="member-regist-check">登録確認</button>
					<?php elseif ($mode === MODE_REVISION) : ?>
					<button type="button" id="member-regist-check">更新確認</button>
					<?php endif ; ?>
				</div>
			</div>
		</section>
	</main>
</div>
<!-- 会員登録モーダル -->
<div class="modal fade" id="memberRegistModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title" id="member-title-member-regist"></h2>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" id="member-body-member-regist"></div>
			<div class="modal-footer">
				<p>上記内容でよろしければ登録完了ボタンを押下してください</p>
				<button type="button" class="btn btn-warning" data-bs-dismiss="modal">中止</button>
				<button type="button" class="btn btn-secondary" id="member-regist-complete" data-bs-dismiss="modal">登録完了</button>
			</div>
		</div>
	</div>
</div>

<!-- 会員登録完了モーダル -->
<div class="modal fade" id="memberRegistCompModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title" id="modal-title-member-regist-comp"></h2>
				<button type="button" class="btn-close modal-back-member" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" id="modal-body-member-regist-comp"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary modal-back-member" data-bs-dismiss="modal">閉じる</button>
			</div>
		</div>
	</div>
</div>