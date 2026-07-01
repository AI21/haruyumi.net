<div class="container-md">
	<main>
		<section>
			<h2>協会行事一覧</h2>
			<?= get_nendo_switch_html(CONTROLLER_NAME_KYOKAI, $fiscalYearId, $kyokaiRegistNendoList); ?>
			<?= get_menu_tab_html($memuData['subMenu']); ?>
			<div class="tab-content">
				<?= get_kyokai_event_tab_html($setting, $SelectfiscalYearData, $kyokaiEventList, $memberData); ?>
				<?= get_notice_list_html($noticeList, $useNoticeIdList, $memberData); ?>
			</div>
		</section>
	</main>
</div>