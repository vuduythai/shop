<div id="modalConfirmCart" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <p> {{ Theme::lang('lang.msg.add_item_success') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    {{ Theme::lang('lang.msg.continue_shopping') }}
                </button>
                <a href="/cart" class="btn btn-black">
                    {{ Theme::lang('lang.msg.go_to_cart') }}
                </a>
            </div>
        </div>

    </div>
</div>