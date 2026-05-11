<div id="modal-win" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/tqywkdcz.json" trigger="hover" style="width:150px;height:150px">
                    </lord-icon>
                    <h4 class="mb-3 mt-4">Xin chúc mừng !</h4>
                    <p class="text-muted fs-15 mb-3">Kết quả phiên <span id="session_id">#39282</span> — <span id="trade-count">0</span> lệnh</p>
                    <div class="table-responsive">
                        <table class="table table-borderless text-start mb-0">
                            <tr>
                                <td class="text-muted">Tổng tiền đặt:</td>
                                <td class="fw-medium text-end" id="summary-total-amount">0 VND</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tổng tiền thắng:</td>
                                <td class="fw-medium text-end text-success" id="summary-win">0 VND</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tổng tiền thua:</td>
                                <td class="fw-medium text-end text-danger" id="summary-lose">0 VND</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Phí giao dịch:</td>
                                <td class="fw-medium text-end text-warning" id="summary-fee">0 VND</td>
                            </tr>
                            <tr class="border-top">
                                <td class="fw-bold">Tổng cộng:</td>
                                <td class="fw-bold text-end" id="summary-net">0 VND</td>
                            </tr>
                        </table>
                    </div>
                    <div class="hstack gap-2 justify-content-center mt-3">
                        <button class="btn btn-primary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 justify-content-center">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="modal-lost" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <h4 class="mb-3 mt-4">Chúc may mắn lần sau !</h4>
                    <p class="text-muted fs-15 mb-3">Kết quả phiên <span id="session_id">#39282</span> — <span id="trade-count">0</span> lệnh</p>
                    <div class="table-responsive">
                        <table class="table table-borderless text-start mb-0">
                            <tr>
                                <td class="text-muted">Tổng tiền đặt:</td>
                                <td class="fw-medium text-end" id="summary-total-amount">0 VND</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tổng tiền thắng:</td>
                                <td class="fw-medium text-end text-success" id="summary-win">0 VND</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tổng tiền thua:</td>
                                <td class="fw-medium text-end text-danger" id="summary-lose">0 VND</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Phí giao dịch:</td>
                                <td class="fw-medium text-end text-warning" id="summary-fee">0 VND</td>
                            </tr>
                            <tr class="border-top">
                                <td class="fw-bold">Tổng cộng:</td>
                                <td class="fw-bold text-end" id="summary-net">0 VND</td>
                            </tr>
                        </table>
                    </div>
                    <div class="hstack gap-2 justify-content-center mt-3">
                        <button class="btn btn-primary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 justify-content-center">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->