<div class="container-md">
	<main>
		<section>
            <?= get_shinsa_chiho_detail_html($shinsaDetail, $shinsaTarget, $memberGradeDeta, $officerFlg, $noticeCategoryId) ?>
            <input type="hidden" id="shinsa_id" value="<?= $shinsaDetail['shinsa_id']; ?>">
            <input type="hidden" id="shinsa_target_id" value="<?= $shinsaDetail['shinsa_target_id']; ?>">
            <input type="hidden" id="request_mode">
		</section>
            <?php if ($officerFlg === true) : ?>
		<section id="officer-area">
                <?= shinsa_offer_member_list_html($shinsaDetail, $shinsaOfferMemberList) ?>
		</section>
            <?php endif; ?>
	</main>
</div>
