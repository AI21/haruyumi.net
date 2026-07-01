<div class="container-md">
	<main>
		<section>
			<h2>大会一覧</h2>
			<?= get_nendo_switch_html(CONTROLLER_NAME_TAIKAI, $fiscalYearId, $taikaiRegistNendoList); ?>
			<?= get_menu_tab_html($memuData['subMenu']); ?>
			<div class="tab-content">
				<?= get_taikai_tab_html($setting, $SelectfiscalYearData, $taikaiList); ?>
				<?= get_notice_list_html($noticeList, $useNoticeIdList, $memberData, $noticeCategoryId); ?>
			</div>
		</section>
	</main>
</div>