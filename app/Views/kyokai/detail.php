<div class="container-md">
	<main>
		<section>
			<div class="tab-content">
				<?= get_kyokai_event_detail_html($eventDetail); ?>
				<input type="hidden" id="event_id" value="<?= $eventDetail['event_id']; ?>">
				<input type="hidden" id="request_mode">
			</div>
		</section>
<?php /*
		<section id="notice-area">
			<?= event_detail_notice_list_html($eventDetail) ?>
		</section>
*/ ?>
		<?php if ($eventDetail['organizer_flg'] === true) : ?>
		<section id="officer-area">
			<?= event_offer_member_list_html($eventDetail, $eventOfferMemberList) ?>
		</section>
		<?php endif; ?>
	</main>
</div>