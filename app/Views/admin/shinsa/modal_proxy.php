<!-- 審査代理登録選択モーダル -->
<div class="modal fade" id="proxyAddMemberShinsaModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title">審査申請者選択：代理登録</h2>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<dl>
					<dt class="text-danger fw-bold">審査種別</dt>
					<dd>
						<?= form_dropdown_key_unshift('shinsa-target-id', $shinsaTargetList, 'shinsa_target_id', 'shinsa_target_name', '', 'id="shinsa-target-id"', true); ?>
					</dd>
					<dt class="text-danger fw-bold">申請者</dt>
					<dd>
						<select id="add-member-id" name="add-member-id">
							<option value="">選択してください</option>
						</select>
					</dd>
				</dl>
			</div>
			<div class="modal-footer">
				<p>上記内容でよろしければ登録完了ボタンを押下してください</p>
				<button type="button" class="btn btn-warning" data-bs-dismiss="modal">中止</button>
				<button type="button" class="btn btn-secondary" id="shinsa-add-member-proxy" data-bs-dismiss="modal">登録完了</button>
			</div>
		</div>
	</div>
</div>

<!-- 審査代理登録完了モーダル -->
<div class="modal fade" id="proxySelectMemberShinsaCompModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title">審査申請者登録完了：代理登録</h2>
				<button type="button" class="btn-close modal-reload" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">審査申請者登録が完了しました</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary modal-reload" data-bs-dismiss="modal">閉じる</button>
			</div>
		</div>
	</div>
</div>

<!-- 審査代理辞退確認モーダル -->
<div class="modal fade" id="proxyCancelMemberShinsaModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title">審査辞退参加者：代理登録</h2>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<dl>
					<dt class="text-danger fw-bold">辞退参加者</dt>
					<dd id="cancel-member-name"></dd>
					<dt class="text-danger fw-bold">審査種別</dt>
					<dd id="cancel-shinsa-target-name"></dd>
				</dl>
			</div>
			<div class="modal-footer">
				<p>上記内容でよろしければ審査辞退確定ボタンを押下してください</p>
                <input type="hidden" id="cancel-member-id">
				<button type="button" class="btn btn-warning" data-bs-dismiss="modal">中止</button>
				<button type="button" class="btn btn-secondary" id="proxy-cancel-member-complete" data-bs-dismiss="modal">審査辞退確定</button>
			</div>
		</div>
	</div>
</div>

<!-- 審査代理辞退完了モーダル -->
<div class="modal fade" id="proxyCancelMemberShinsaCompModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title">審査辞退完了：代理登録</h2>
				<button type="button" class="btn-close modal-reload" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">審査辞退登録が完了しました</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary modal-reload" data-bs-dismiss="modal">閉じる</button>
			</div>
		</div>
	</div>
</div>
