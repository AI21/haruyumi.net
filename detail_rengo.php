<div class="container-md">
	<main>
		<section>
            <?= get_shinsa_chuou_detail_html($shinsaDetail, $shinsaTarget, $memberGradeDeta, $officerFlg) ?>
            <input type="hidden" id="shinsa_id" value="<?= $shinsaDetail['shinsa_id']; ?>">
            <input type="hidden" id="shinsa_target_id" value="<?= $shinsaDetail['shinsa_target_id']; ?>">
            <input type="hidden" id="request_mode">
		</section>
	</main>
</div>
