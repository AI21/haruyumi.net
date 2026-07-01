<div class="container-md">
	<main>
		<section class="kaiin-area">
			<h2>春日井市弓道協会 <span class="d-block ms-3">会員名簿</span></h2>
			<?= get_nendo_switch_html(CONTROLLER_NAME_MEMBER, $fiscalYearId, $memberRegistNendoList); ?>
			<? //get_kaiin_list_file_html($memberData, $memberListFile); ?>
			<?= get_kaiin_list_html($memberData, $memberList); ?>
		</section>
	</main>
</div>