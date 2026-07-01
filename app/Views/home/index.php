
<div class="container-md">
	<div class="container-md bg-dark roots-main-title d-none">
		<nav aria-label="breadcrumb" class="">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="./">メイン</a></li>
				<li class="breadcrumb-item active" aria-current="page">Rules</li>
			</ol>
		</nav>
		<h1 class="p-1 text-center text-light">メイン</h1>
	</div>
	<main>
		<?= get_notice_list_html($noticeList, $useNoticeIdList, $memberData); ?>
		<?= get_menu_home_html($memuData['mainMenu']); ?>
	</main>
</div>