<div class="modal fade" id="logoutModel" tabindex="-1" role="dialog" aria-labelledby="logoutModel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered" role="document">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="logoutModelTitle">{{tr('confirm_logout')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body">
                <h5>{{tr('logout_note')}}</h5>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-pill" data-dismiss="modal">{{tr('close')}}</button>
                <a class="btn btn-primary btn-pill" href="{{route('admin.logout')}}">{{tr('logout')}}</a>
            </div>

        </div>

    </div>
    
</div>