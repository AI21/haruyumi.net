<!-- お知らせモーダル -->
<div class="modal fade" id="commonNoticeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title" id="modal-title-common-notice"></h2>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div id="modal-created">
				<span id="notice-create-name"></span>
				<span id="notice-datetime"></span>
			</div>
			<div class="modal-body" id="modal-body-common-notice"></div>
			<div class="modal-footer">
				<div id="admin-revision" data-notice-info-id="-1" class="d-none">
					<button type="button" id="notice-revision" class="btn btn-warning" data-bs-dismiss="modal">修正</button>　
					<button type="button" id="notice-delete" class="btn btn-danger" data-bs-dismiss="modal">削除</button>
				</div>
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
			</div>
		</div>
	</div>
</div>

<!-- 各種登録完了モーダル -->
<div class="modal fade" id="commonRegistCompModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title" id="modal-title-common-regist-comp"></h2>
				<button type="button" class="btn-close modal-back-home" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" id="modal-body-common-regist-comp"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary modal-back-home" data-bs-dismiss="modal">閉じる</button>
			</div>
		</div>
	</div>
</div>
