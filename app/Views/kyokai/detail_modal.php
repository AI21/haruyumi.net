<!-- Modal：申込・キャンセル 確認 -->
<div class="modal fade" id="eventRequestConfrim" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="modal-title-event-request"></h1>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="modal-body-event-request"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">中止</button>
                <button type="button" id="event-request-offer" class="btn btn-primary" data-bs-dismiss="modal"></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal：申込・キャンセル 完了-->
<div class="modal fade" id="eventRequestComp" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="modal-title-event-request-comp">参加申込完了</h1>
            </div>
            <div class="modal-body">
                <p id="modal-body-event-request-comp"></p>
            </div>
            <div class="modal-footer">
                <button type="button" id="event-request-complete" class="btn btn-primary" data-bs-dismiss="modal">確認</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal：申込・キャンセル エラー-->
<div class="modal fade" id="eventRequestError" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">参加申込・キャンセルエラー</h1>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="error-message"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">確認</button>
            </div>
        </div>
    </div>
</div>