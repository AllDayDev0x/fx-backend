<div id="report_{{$user->id}}" class="modal fade" role="dialog">
    <div class="modal-dialog  modal-lg">

        <form action="{{route('admin.users.send_custom_report')}}">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title">{{ tr('custom_report') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="row">

                        <input type="hidden" id="user_id" name="user_id" value="{{$user->id}}">


                        <div class="col-md-6 premium_account">
                            <h6 class="">
                                <label for="user_name">{{ tr('user_name') }}</label>&nbsp;: &nbsp;
                                <a href="{{route('admin.users.view' , ['user_id' => $user->id])}}">
                                    {{$user->name}}
                                </a>
                            </h6>
                        </div>

                    </div>

                    <br>

                    <div class="row">

                            <div class="form-group col-md-6">

                                <label for="from_date">{{ tr('from_date') }}  <span class="admin-required">*</span></label>

                                <input type="text" class="form-control datetimepicker" id="from_date" name="from_date" placeholder="{{ tr('from_date') }}" value="" required>
                            </div>

                            <div class="form-group col-md-6">

                                <label for="to_date">{{ tr('to_date') }}  <span class="admin-required">*</span></label>

                                <input type="text" class="form-control datetimepicker" id="to_date" name="to_date" placeholder="{{ tr('to_date') }}" value="" required>
                            </div>

                        </div>
                        
                    <br>

                </div>
                <div class="modal-footer">
                    <div class="pull-right">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{tr('cancel')}}</button>
                        <button type="submit" name="type" formaction="{{route('admin.users.send_custom_report')}}" class="btn btn-primary" value="submit">{{tr('email')}}</button>
                        <button type="submit" name="type" formaction="{{route('admin.users.send_custom_report')}}" class="btn btn-info" value="export">Export to Excel</button>
                        </div>
                    <div class="clearfix"></div>
                </div>
            </div>

        </form>

    </div>

</div>

