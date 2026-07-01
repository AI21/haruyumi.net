
<body>
<header>
    <h2><?= $title; ?></h2>
</header>
<main>
    <section>
        <?= get_ouji_calendar_html($oujiSchedule, $arrayHoliday); ?>
<?php /*
        <table id="ouji_shejule">
            <tr>
                <th class="">日程</th>
                <th class="times">午前</th>
                <th class="times">午後</th>
                <th class="times">夜間</th>
                <th class="">備考</th>
            </tr>
            <?php for ($i=0; $i<SCHEJULE_MAX; $i++) : 
                $w = date('w', strtotime("+$i day"));
                $day = date('Ymd', strtotime("+$i day"));
                $sc = $oujiSchedule[$day];
            ?>
            <tr class="<?php if (WEEK_JP[$w] == '土') { echo 'saturday'; } elseif (WEEK_JP[$w] == '日' || in_array($day, $arrayHoliday)) { echo 'sunday'; } ?>">
                <td class="day"><?= date('m/d', strtotime("+$i day")) . "(" . WEEK_JP[$w]. ")"; ?></td>
                <td class=""></td>
                <td class=""></td>
                <td class=""></td>
                <td class="event"></td>
            </tr>
            <?php endfor; ?>
        </table>
            {for $i=0 to 13}
{assign var=w "+$i day"|strtotime|date_format:'%w'}
{assign var=day "+$i day"|strtotime|date_format:'%Y%m%d'}
{assign var=sc $oujiShejule[$day]}
            <tr class="{if $dow.$w eq '土'}saturday{elseif $dow.$w eq '日' or in_array($day,$arrayHoliday)}sunday{/if}">
                <td class="day">{"+$i day"|strtotime|date_format:"%-m/%d"}({$dow.$w})</td>
                <td class="{if $sc['morning'] eq '－'}no-use{elseif $sc['morning'] eq '○'}{elseif $sc['morning'] eq '体育館'}taiiku{else}open{/if}">{$sc['morning']}
                {if $sc['morning_open_user'] ne ''}<span>({$sc['morning_open_user']})</span>{/if}</td>
                <td class="{if $sc['afternoon'] eq '－'}no-use{elseif $sc['afternoon'] eq '○'}{elseif $sc['afternoon'] eq '体育館'}taiiku{else}open{/if}"">{$sc['afternoon']}
                {if $sc['afternoon_open_user'] ne ''}<span>({$sc['afternoon_open_user']})</span>{/if}</td>
                <td class="{if $sc['night'] eq '－'}no-use{elseif $sc['night'] eq '○'}{elseif $sc['night'] eq '体育館'}taiiku{else}open{/if}"">{$sc['night']}
                {if $sc['night_open_user'] ne ''}<span>({$sc['night_open_user']})</span>{/if}</td>
                <td class="event">{$sc['event']}</td>
            </tr>
{/for}
*/ ?>
        <div id="mail_set_area">
            <button id="btn_change_view">表示切替</button>
            <button id="btn_mail_set">メール受信設定</button>
            <form method="post" id="mail_set" action="mail_set.php" target="mail_set"></form>
        </div>
    </section>
    <form method="post" id="change_view">
        <input type="hidden" id="view" name="view" value="{$change_view}">
    </form>
</main>
