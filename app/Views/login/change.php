<div class="container-md">
	<main>
		<section>
			<h3>ログイン情報変更</h3>
			<dl>
				<dt>ログインID</dt>
				<dd class="ms-3">
					<input id="login_id" name="login_id" class="text md-3" type="text" maxlength="16" value="<?= $memberData['login_id'] ?>">
					<div class="invalid-feedback">ユーザー名を選択して下さい</div>
					<span class="">4～16文字以内</span>
					<div id="login-id-alert" class="alert alert-danger mt-2 mb-0 p-1 d-none" role="alert"></div>
				</dd>
				<dt>メールアドレス</dt>
				<dd class="ms-3">
					<input id="mail_address" name="mail_address" class="text col-12" type="text" maxlength="128" value="<?= $memberData['mail_address'] ?>">
					<div id="mail-address-alert" class="alert alert-danger mt-2 mb-0 p-1 d-none" role="alert"></div>
				</dd>
				<dt>現在のパスワード</dt>
				<dd class="ms-3">
					<input id="password_old" name="password_old" type="password" class="text" placeholder="変更する場合のみ入力">
					<span class="">4～16文字以内</span>
					<div id="password-old-alert" class="alert alert-danger mt-2 mb-0 p-1 d-none" role="alert"></div>
				</dd>
				<dt>新しいパスワード</dt>
				<dd class="ms-3">
					<input id="password_new" name="password_new" type="password" class="text" placeholder="変更する場合のみ入力">
					<span class="">4～16文字以内</span>
					<div id="password-new-alert" class="alert alert-danger mt-2 mb-0 p-1 d-none" role="alert"></div>
				</dd>
				<dt>新しいパスワード（確認用）</dt>
				<dd class="ms-3">
					<input id="password_conf" name="password_conf" type="password" class="text" placeholder="変更する場合のみ入力">
					<span class="">4～16文字以内</span>
					<div id="password-conf-alert" class="alert alert-danger mt-2 mb-0 p-1 d-none" role="alert"></div>
				</dd>
			</dl>
			<div class="p-3"><button id="login-data-change">ログイン情報変更</button></div>
		</section>
	</main>
</div>

<!-- Modal：変更確認 -->
<div class="modal fade" id="loginDataChangeConfrim" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">ログイン情報変更確認</h2>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="request-offer-body">ログイン情報を変更しますか？</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">中止</button>
                <button type="button" id="login-data-change-confrim" class="btn btn-primary" data-bs-dismiss="modal">ログイン情報を変更する</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal：変更完了-->
<div class="modal fade" id="loginDataChangeComplete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">ログイン情報変更完了</h2>
            </div>
            <div class="modal-body">
                <p id="request-complete-body">ログイン情報を変更しました<br>※再ログインが必要になります</p>
            </div>
            <div class="modal-footer">
                <button type="button" id="login-data-change-complete" class="btn btn-primary" data-bs-dismiss="modal">確認</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal：変更エラー-->
<div class="modal fade" id="loginDataChangeError" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">変更エラー</h2>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="error-message"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">確認</button>
            </div>
        </div>
    </div>
</div>