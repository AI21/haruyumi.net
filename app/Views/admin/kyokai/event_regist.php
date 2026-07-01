<div class="container-md">
	<main>
		<section>
			<div><a href="#" onclick="history.back()" return false;>戻る</a></div>
			<div id="event-regist-area">
				<form id="event-regist">
				<?php if ($mode === MODE_REGIST) : ?>
				<?= form_event_regist($eventClassId, $areaGroupList, $eventNameList, $syubetsuList, $kaijoList, $uploadFileNum); ?>
				<?php else : ?>
				<?= form_event_revision($eventDetail, $uploadFileNum); ?>
				<?php endif ; ?>
				</form>
				<div class="regist-area">
					<?php 
						$btnText = "登録確認";
						if ($mode === MODE_REVISION) {
							$btnText = "更新確認";
						}
					?>
					<button type="button" id="shinsa-regist-check"><?= $btnText; ?></button>
				</div>
			</div>
			<input type="hidden" id="upload-file-num" value="<?= $uploadFileNum; ?>">
			<input type="hidden" id="regist-mode" value="<?= $mode; ?>">
		</section>
	</main>
</div>
