<div class="container-md">
	<main>
		<section>
			<div class="tab-content">
				<?= get_seminar_detail_html($seminarDetail); ?>
				<input type="hidden" id="seminar_id" value="<?= $seminarDetail['seminar_id']; ?>">
				<input type="hidden" id="request_mode">
			</div>
		</section>
	</main>
</div>