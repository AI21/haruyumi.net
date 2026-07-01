<div class="container-md">
	<main>
		<section>
			<div><a href="#" onclick="history.back()" return false;>戻る</a></div>
			<div id="taikai-regist-area">
				<form id="taikai-regist">
				<?= form_taikai_detail($taikaiDetail, $kaijoList, $kyokaiOfficerList, $uploadFileNum); ?>
				</form>
				<div class="regist-area">
					<?php if ($mode === 'regist') : ?>
					<button type="button" id="taikai-regist-check">登録確認</button>
					<?php elseif ($mode === 'revision') : ?>
					<button type="button" id="taikai-regist-check">更新確認</button>
					<?php endif ; ?>
				</div>
			</div>
			<input type="hidden" id="upload-file-num" value="<?= $uploadFileNum; ?>">
			<input type="hidden" id="regist-mode" value="<?= $mode; ?>">
		</section>
	</main>
</div>
