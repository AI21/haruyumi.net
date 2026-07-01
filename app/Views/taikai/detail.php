<div class="container-md">
	<main>
		<section>
            <?= get_kasugai_taikai_detail_html($taikaiDetail) ?>
            <input type="hidden" id="taikai_id" value="<?= $taikaiDetail['taikai_id']; ?>">
            <input type="hidden" id="request_mode" value="">
		</section>
	</main>
</div>
