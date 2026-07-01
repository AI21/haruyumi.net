<div class="container-md">
	<main>
		<section>
			<div><a href="#" onclick="history.back()" return false;>戻る</a></div>
			<div id="document-regist-area">
				<form id="document-regist">
				<dl>
					<dt>資料：体育館利用<span class="alert">PDF形式のみ、<?= file_size_mb(UPLOAD_FILE_MAX_SIZE); ?>まで</span></dt>
					<dd><input type="file" id="document-file"></dd>
					<dt>メール配信</dt>
					<dd>
						<div class="form-switch">
							<input  type="checkbox"class="form-check-input" id="document-mail-send" checked="checked" role="switch">
							<span id="document-mail-text">する</span>
						</div>
					</dd>
					<div id="send-mail-detail">
						<dt>タイトル</dt>
						<dd><?= admin_form_text("document-title", '体育館 ' . $dateAdd2Month . '予約状況（練習）'); ?></dd>
						<dt>本文</dt>
						<dd><?= admin_form_textarea("document-body", "体育館 " . $dateAdd2Month . "予約状況（練習）を送ります。"); ?></dd>
					</div>
				</dl>
				</form>
				<div class="regist-area">
					<input type="hidden" id="document-category-id" value="<?= CATEGORY_ID_USEGYM; ?>">
					<button type="button" id="document-regist-check">登録確認</button>
				</div>
			</div>
		</section>
	</main>
</div>

<!-- 資料登録確認モーダル -->
<div class="modal fade" id="documentConfirmModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title">資料登録確認：体育館利用</h2>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div id="modal-created">
				<span id="document-create-name"></span>
				<span id="document-datetime"></span>
			</div>
			<div class="modal-body" id="document-conf-body">
				<dl>
					<dt class="text-danger fw-bold mt-4">資料：体育館利用</dt>
					<dt class="document-file-confrim" id="document-file-confrim"></dt>
				</dl>
				<div id="document-mail-conf-body">
					<dl>
						<dt class="text-danger fw-bold">メールタイトル</dt>
						<dt id="document-title-confrim"></dt>
						<dt class="text-danger fw-bold mt-2">メール本文</dt>
						<dt id="document-body-confrim"></dt>
					</dl>
				</div>
			</div>
			<div class="modal-footer">
				<p>上記内容でよろしければ登録完了ボタンを押下してください</p>
				<button type="button" class="btn btn-warning" data-bs-dismiss="modal">中止</button>
				<button type="button" class="btn btn-secondary" id="document-regist-complete" data-bs-dismiss="modal">登録完了</button>
			</div>
		</div>
	</div>
</div>

<!-- 資料登録完了モーダル -->
<div class="modal fade" id="documentRegistCompModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title" id="modal-title-document-regist-comp"></h2>
				<button type="button" class="btn-close modal-back-document" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" id="modal-body-document-regist-comp"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary modal-back-document" data-bs-dismiss="modal">閉じる</button>
			</div>
		</div>
	</div>
</div>