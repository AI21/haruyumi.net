<div class="container-md">
	<main>
		<section>
			<h2>審査一覧</h2>
			<?= get_nendo_switch_html(CONTROLLER_NAME_SHINSA, $fiscalYearId, $shinsaRegistNendoList); ?>
			<?= get_menu_tab_html($memuData['subMenu']); ?>
			<div class="tab-content">
				<?= get_shinsa_tab_html($setting, $SelectfiscalYearData, $shinsaList); ?>
				<?= get_notice_list_html($noticeList, $useNoticeIdList, $memberData, $noticeCategoryId); ?>
			</div>
		</section>
	</main>
</div>