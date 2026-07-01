<div class="container-md">
	<main>
		<section>
            <?= get_kasugai_taikai_detail_html($taikaiDetail, $noticeCategoryId) ?>
            <input type="hidden" id="taikai_id" value="<?= $taikaiDetail['taikai_id']; ?>">
            <input type="hidden" id="request_mode" value="">
		</section>
		<?php if (empty($taikaiDetail['officer_level']) === false) : ?>
		<section id="officer-area">
			<?= taikai_offer_member_list_html($taikaiDetail, $taikaiOfferMemberList) ?>
		</section>
		<?php endif; ?>
	</main>
</div>
