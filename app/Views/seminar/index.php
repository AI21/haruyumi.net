<div class="container-md">
	<main>
		<section>
			<h2>講習会一覧</h2>
			<?php //get_nendo_switch_html(CONTROLLER_NAME_SEMINAR, $fiscalYearId, $seminarRegistNendoList); ?>
			<?= get_menu_tab_html($memuData['subMenu']); ?>
			<div class="tab-content">
				<?= get_seminar_tab_html($seminarList); ?>
				<?= get_notice_list_html($noticeList, $useNoticeIdList, $memberData, $noticeCategoryId); ?>
			</div>
		</section>
	</main>
</div>