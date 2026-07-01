<!-- 代理登録選択モーダル -->
<div class="modal fade" id="proxyAddMemberTaikaiModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title">大会参加者選択：代理登録</h2>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<dl>
					<dt class="text-danger fw-bold">登録参加者</dt>
					<dd>
						<?= get_member_list_tom_select_html($memberList, $taikaiOfferMemberList); ?>
					</dd>
				</dl>
			</div>
			<div class="modal-footer">
				<p>上記内容でよろしければ登録完了ボタンを押下してください</p>
				<button type="button" class="btn btn-warning" data-bs-dismiss="modal">中止</button>
				<button type="button" class="btn btn-secondary" id="taikai-add-member-proxy" data-bs-dismiss="modal">登録完了</button>
			</div>
		</div>
	</div>
</div>

<!-- 代理登録完了モーダル -->
<div class="modal fade" id="proxySelectMemberCompModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title">大会参加者登録完了：代理登録</h2>
				<button type="button" class="btn-close modal-reload" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">大会参加者登録が完了しました</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary modal-reload" data-bs-dismiss="modal">閉じる</button>
			</div>
		</div>
	</div>
</div>

<!-- 代理大会辞退確認モーダル -->
<div class="modal fade" id="proxyCancelMemberModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title">大会辞退参加者</h2>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<dl>
					<dt id="cancel-member-name"></dt>
					<dt id="cancel-member-holdergrade"></dt>
				</dl>
			</div>
			<div class="modal-footer">
				<p>上記内容でよろしければ大会辞退確定ボタンを押下してください</p>
                <input type="hidden" id="cancel-member-id">
				<button type="button" class="btn btn-warning" data-bs-dismiss="modal">中止</button>
				<button type="button" class="btn btn-secondary" id="proxy-cancel-member-complete" data-bs-dismiss="modal">大会辞退確定</button>
			</div>
		</div>
	</div>
</div>

<!-- 代理大会辞退完了モーダル -->
<div class="modal fade" id="proxyCancelMemberCompModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title">大会辞退完了：代理登録</h2>
				<button type="button" class="btn-close modal-reload" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">大会辞退登録が完了しました</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary modal-reload" data-bs-dismiss="modal">閉じる</button>
			</div>
		</div>
	</div>
</div>
