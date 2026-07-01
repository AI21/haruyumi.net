<div class="container-md">
	<main>
		<section>
			<div><a href="#" onclick="history.back()" return false;>戻る</a></div>
			<div id="notice-regist-area">
				<form id="notice-regist">
				<dl>
					<dt>会員名簿<span class="alert">PDF形式のみ、<?= file_size_mb(UPLOAD_FILE_MAX_SIZE); ?>まで</span></dt>
					<dd><input type="file" id="member-list-file"></dd>
					<dt>メール配信</dt>
					<dd>
						<div class="form-switch">
							<input  type="checkbox"class="form-check-input" id="member-list-mail-send" checked="checked" role="switch">
							<span id="member-list-mail-text">する</span>
						</div>
					</dd>
					<div id="send-mail-detail">
						<dt>タイトル</dt>
						<dd><?= admin_form_text("member-list-title", '【会員リスト】' . date_format_jp(date("Ymd"), false, DATE_FORMAT_YYMMDD_NENGO) . '版'); ?></dd>
						<dt>本文</dt>
						<dd><?= admin_form_textarea("member-list-body", "会員リスト（" . date_format_jp(date("Ymd"), false, DATE_FORMAT_YYMMDD_NENGO) . "版）を送付します。\r\rよろしくお願いいたします。"); ?></dd>
					</div>
				</dl>
				</form>
				<div class="regist-area">
					<button type="button" id="member-list-regist-check">登録確認</button>
				</div>
			</div>
		</section>
	</main>
</div>
