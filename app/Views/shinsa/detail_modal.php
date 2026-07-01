<!-- Modal：申込・キャンセル 確認 -->
<div class="modal fade" id="shinsaRequestConfrim" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="modal-title-shinsa-request-confrim"></h1>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="modal-body-shinsa-request-confrim"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">中止</button>
                <button type="button" id="shinsa-request-offer" class="btn btn-primary" data-bs-dismiss="modal"></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal：申込・キャンセル 完了-->
<div class="modal fade" id="shinsaRequestComplete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="modal-title-shinsa-request-complete"></h1>
            </div>
            <div class="modal-body">
                <p id="modal-body-shinsa-request-complete"></p>
            </div>
            <div class="modal-footer">
                <button type="button" id="shinsa-request-complete" class="btn btn-primary" data-bs-dismiss="modal">確認</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal：結果報告 -->
<div class="modal fade" id="shinsaResultReportConfrim" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">審査結果報告確認</h1>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="modal-body-shinsa-result-report-confrim"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">中止</button>
                <button type="button" id="shinsa-result-report-submit" class="btn btn-primary" data-bs-dismiss="modal">報告する</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal：結果報告 完了-->
<div class="modal fade" id="shinsaResultReportComplete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">審査結果報告完了</h1>
            </div>
            <div class="modal-body">
                <p>審査結果を報告しました</p>
            </div>
            <div class="modal-footer">
                <button type="button" id="shinsa-result-report-complete" class="btn btn-primary" data-bs-dismiss="modal">確認</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal：審査結果代理登録 -->
<div class="modal fade" id="shinsaResultReportProxyConfrim" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">審査結果 代理登録確認</h1>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <dl>
                    <dt class="text-danger fw-bold">会員名</dt>
                    <dd id="shinsa-result-report-proxy-member-name" class="ms-3"></dd>
                    <dt class="text-danger fw-bold">審査結果</dt>
                    <dd class="ms-3">
                        <input type="radio" name="shinsa_result_report_proxy" id="shinsa-result-report-proxy-pass" value="<?= SHINSA_RESULT_FLG_PASS ?>">
                        <label for="shinsa-result-report-proxy-pass"><?= SHINSA_RESULT_VIEW_PASS ?></label><br>
                        <input type="radio" name="shinsa_result_report_proxy" id="shinsa-result-report-proxy-fail" value="<?= SHINSA_RESULT_FLG_FAIL ?>">
                        <label for="shinsa-result-report-proxy-fail"><?= SHINSA_RESULT_VIEW_FAIL ?></label><br>
                        <input type="radio" name="shinsa_result_report_proxy" id="shinsa-result-report-proxy-abstain" value="<?= SHINSA_RESULT_FLG_ABSTAIN ?>">
                        <label for="shinsa-result-report-proxy-abstain"><?= SHINSA_RESULT_VIEW_ABSTAIN ?></label><br>
                    </dd>
                </dl>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="shinsa-result-report-proxy-member-id">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">中止</button>
                <button type="button" id="shinsa-result-report-proxy-submit" class="btn btn-primary" data-bs-dismiss="modal">審査結果を代理登録する</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal：審査結果代理登録 完了-->
<div class="modal fade" id="shinsaResultReportProxyComplete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">審査結果 代理登録完了</h1>
            </div>
            <div class="modal-body">
                <p>審査結果を代理登録しました</p>
            </div>
            <div class="modal-footer">
                <button type="button" id="shinsa-result-report-complete" class="btn btn-primary" data-bs-dismiss="modal">確認</button>
            </div>
        </div>
    </div>
</div>

<?php if ($officerFlg === true) : ?>
<!-- Modal：昇段登録確認 -->
<div class="modal fade" id="rankupConfrimModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">昇段登録確認</h1>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <dl>
                    <dt class="text-danger fw-bold">会員名</dt>
                    <dd id="rankup-member-name" class="ms-3"></dd>
                    <dt class="text-danger fw-bold">現在の段位</dt>
                    <dd id="rankup-current-grade" class="ms-3"></dd>
                    <dt class="text-danger fw-bold">認許日</dt>
                    <dd id="rankup-acquired-day" class="ms-3">
                        <input type="date" id="acquired-day" class="form-control w-auto">
                    </dd>
                    <dt class="text-danger fw-bold">昇段後の称号・段位</dt>
                    <dd id="rankup-new-holder-grade" class="ms-3"></dd>
                </dl>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="rankup-member-id">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">中止</button>
                <button type="button" id="rankup-result" class="btn btn-primary" data-bs-dismiss="modal">昇段登録する</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal：昇段登録 完了-->
<div class="modal fade" id="rankupCompleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">昇段登録完了</h1>
            </div>
            <div class="modal-body">
                <p>昇段登録が完了しました</p>
            </div>
            <div class="modal-footer">
                <button type="button" id="shinsa-result-report-complete" class="btn btn-primary" data-bs-dismiss="modal">確認</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>