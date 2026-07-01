<!-- お知らせ登録確認モーダル -->
<div class="modal fade" id="noticeConfrimModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title">登録内容確認</h2>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div id="modal-created">
				<span id="notice-create-mame"></span>
				<span id="notice-datetime"></span>
			</div>
			<div class="modal-body" id="notice-conf-body">
<?php
/*
			<dl>
					<dt class="text-danger fw-bold">カテゴリー</dt>
					<dd id="notice-category-confrim"></dd>
					<dt class="text-danger fw-bold">タイトル</dt>
					<dd id="notice-title-confrim"></dd>
					<dt class="text-danger fw-bold mt-2">本文</dt>
					<dd id="notice-body-confrim"></dd>
					<dt class="text-danger fw-bold mt-2">関連イベント</dt>
					<dd id="notice-relation-confrim"></dd>
					<dt class="text-danger fw-bold mt-2 d-none">参加者のみにメール配信</dt>
					<dd id="notice-regist-user-mail-confrim" class="d-none"></dd>
					<dt class="text-danger fw-bold mt-4">添付資料</dt>
					<dd class="notice-file-confrim" id="notice-file-confrim"></dd>
				</dl>
*/ ?>
			</div>
			<div class="modal-footer">
				<p>上記内容でよろしければ登録完了ボタンを押下してください</p>
				<button type="button" class="btn btn-warning" data-bs-dismiss="modal">中止</button>
				<button type="button" class="btn btn-secondary" id="notice-regist-complete" data-bs-dismiss="modal">登録完了</button>
			</div>
		</div>
	</div>
</div>

<!-- 会員名簿登録確認モーダル -->
<div class="modal fade" id="memberListConfrimModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title">会員名簿登録確認</h2>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div id="modal-created">
				<span id="memberlist-create-mame"></span>
				<span id="memberlist-datetime"></span>
			</div>
			<div class="modal-body" id="memberlist-conf-body">
				<dl>
					<dt class="text-danger fw-bold mt-4">会員名簿資料</dt>
					<dt class="memberlist-file-confrim" id="memberlist-file-confrim"></dt>
				</dl>
				<div id="memberlist-mail-conf-body">
					<dl>
						<dt class="text-danger fw-bold">メールタイトル</dt>
						<dt id="memberlist-title-confrim"></dt>
						<dt class="text-danger fw-bold mt-2">メール本文</dt>
						<dt id="memberlist-body-confrim"></dt>
					</dl>
				</div>
			</div>
			<div class="modal-footer">
				<p>上記内容でよろしければ登録完了ボタンを押下してください</p>
				<button type="button" class="btn btn-warning" data-bs-dismiss="modal">中止</button>
				<button type="button" class="btn btn-secondary" id="memberlist-regist-complete" data-bs-dismiss="modal">登録完了</button>
			</div>
		</div>
	</div>
</div>
