<div class="container-md">
	<main>
		<section>
			<div><a href="#" onclick="history.back()" return false;>戻る</a></div>
			<div id="notice-regist-area">
				<form id="notice-regist">
				<dl>
					<dt>カテゴリー</dt>
					<dd>
						<?php if ($mode === MODE_REGIST) : ?>
						<?= form_dropdown_category_notice('notice-category-id', $categoryNoticeList, $noticeCategoryId); ?>
						<?php elseif ($mode === MODE_REVISION) : ?>
						<p id="notice-category-name"><?= $noticeCategoryName; ?></p>
						<?= admin_form_hidden("notice-category-id", $noticeCategoryId, "notice-category-id"); ?>
						<?php endif; ?>
					</dd>
					<dt>タイトル</dt>
					<dd><?= admin_form_text("notice-title", $noticeTitle); ?></dd>
					<dt>本文</dt>
					<dd><?= admin_form_textarea("notice-body", $noticeBody); ?></dd>
					<dt>関連イベント（選択したイベントにリンクされます）</dt>
					<dd>
						<?= form_dropdown_category_notice('relation-event-id', $categoryNoticeList); ?>
					</dd>
					<?php if (empty($relationEventId) === false) : ?>
					<dt>参加者のみにメール配信</dt>
					<dd>
						<div class="form-switch">
							<input type="checkbox" class="form-check-input me-1" id="regist-user-mail-flg" role="switch">
							<label id="regist-user-mail-text" for="regist-user-mail-flg">しない</label>
						</div>
					</dd>
					<?php endif; ?>
					<dt>資料<span class="alert">画像・PDF・エクセル・ワード・テキスト形式のみ、<?= file_size_mb(UPLOAD_FILE_MAX_SIZE); ?>まで</span></dt>
					<dd>
						<?= form_file_document('notice', 'notice-files', $noticeDocumentList, $uploadFileNum); ?>
					</dd>
				</dl>
				<input type="hidden" id="notice-info-id" value="<?= $noticeInfoId; ?>">
				<input type="hidden" id="set-notice-category-id" value="<?= $noticeCategoryId; ?>">
				<input type="hidden" id="set-relation-event-id" value="<?= $relationEventId; ?>">
				<input type="hidden" id="upload-file-num" value="<?= $uploadFileNum; ?>">
				<input type="hidden" id="regist-mode" value="<?= $mode; ?>">
				</form>
				<div class="regist-area">
					<?php if ($mode === MODE_REGIST) : ?>
					<button type="button" id="notice-regist-check">登録確認</button>
					<?php elseif ($mode === MODE_REVISION) : ?>
					<button type="button" id="notice-regist-check">更新確認</button>
					<?php endif ; ?>
				</div>
			</div>
		</section>
	</main>
</div>
