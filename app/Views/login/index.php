<div class="container-md">
	<main>
		<section>
			<h3><?= SYSTEM_NAME; ?></h3>
			<table id="admin">
				<tbody>
					<tr>
						<th>メールアドレス</th>
						<td><input id="member_id_mail" name="member_id_mail" class="text md-2" type="text" maxlength="128"></td>
					</tr>
					<tr>
						<th>パスワード</th>
						<td><input id="password" name="password" type="password" class="text"></td>
					</tr>
					<tr>
						<td colspan="2"><button id="login-check">ログイン</button></td>
					</tr>
				</tbody>
			</table>
		</section>
	</main>
</div>

<!-- Modal： -->
<div class="modal fade" id="notLogin" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">ログインエラー</h2>
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