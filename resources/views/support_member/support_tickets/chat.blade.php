@extends('layouts.support_member') 

@section('content-header', tr('support_tickets'))

@section('styles')

<link rel="stylesheet" type="text/css" href=".{{asset('admin-assets/css/pages/chat-application.css')}}">

@endsection 

@section('breadcrumb')

<li class="breadcrumb-item">
  <a href="{{route('support_member.support_tickets.index')}}">{{tr('support_tickets')}}</a>
</li>

<li class="breadcrumb-item">{{ tr('view_support_tickets') }}</li>

@endsection 

@section('content')

<div class="vertical-layout vertical-menu chat-application  menu-expanded fixed-navbar">

    <div class="app-content">

        <div class="sidebar-left sidebar-fixed">

            <div class="sidebar">

                <div class="sidebar-content card d-none d-lg-block">

                    <div class="card-body chat-fixed-search">

                        <fieldset class="form-group position-relative has-icon-left m-0">

                            <input type="text" class="form-control" id="iconLeft4" placeholder="Search user">
                            <div class="form-control-position">
                                <i class="ft-search"></i>
                            </div>

                        </fieldset>

                    </div>

                    <div id="users-list" class="list-group position-relative">

                        <div class="users-list-padding media-list">

                        @foreach($support_chats as $support_chat)
                      
                            <a href="#" class="media border-0">

                                <div class="media-left pr-1">

                                   <span class="avatar avatar-md avatar-busy">

                                       <img class="media-object rounded-circle" src="{{$support_chat->user->picture ?? asset('placeholder.jpeg')}}"
                                          alt="User Image">
                                       <i></i>

                                   </span>

                                </div>

                                <div class="media-body w-100">

                                   <h6 class="list-group-item-heading"> {{$support_chat->user->name ?? "-"}}
                                      <span class="font-small-3 float-right primary">{{date('H:i A',strtotime($support_chat->updated_at))}}</span>
                                  
                                   </h6>

                                   <p class="list-group-item-text text-muted mb-0"><i class="ft-check primary font-small-2"></i> {{$support_chat->message}}
                                      <span class="float-right primary">
                                      <span class="badge badge-pill badge-primary">{{$support_chat->created_at->diffForHumans()}}</span>
                                      </span>
                                   </p>

                                </div>

                            </a>

                        @endforeach

                    </div>

                </div>

            </div>

         </div>

      </div>

      <div class="content-right">

         <div class="content-wrapper">

            <div class="content-header row">
            </div>

            <div class="content-body">

                <section class="chat-app-window">

                    <div class="badge badge-default mb-1">Chat History</div>

                    <div class="chats">

                        <div class="chats">
                            <div class="chat">
                               <div class="chat-avatar">
                                  <a class="avatar" data-toggle="tooltip" href="#" data-placement="right" title=""
                                     data-original-title="">
                                  <img src="../../../app-assets/images/portrait/small/avatar-s-1.png" alt="avatar"
                                     />
                                  </a>
                               </div>
                               <div class="chat-body">
                                  <div class="chat-content">
                                     <p>How can we help? We're here for you!</p>
                                  </div>
                               </div>
                            </div>
                            <div class="chat chat-left">

                                <div class="chat-avatar">
                                    <a class="avatar" data-toggle="tooltip" href="#" data-placement="left" title="" data-original-title="">
                                    <img src="../../../app-assets/images/portrait/small/avatar-s-7.png" alt="avatar"
                                     />
                                    </a>
                               </div>

                               <div class="chat-body">
                                    <div class="chat-content">
                                        <p>Hey John, I am looking for the best admin template.</p>
                                        <p>Could you please help me to find it out?</p>
                                    </div>
                                    <div class="chat-content">
                                        <p>It should be Bootstrap 4 compatible.</p>
                                    </div>
                               </div>

                            </div>

                        </div>

                    </div>

                </section>

                <section class="chat-app-form">
                    <form class="chat-app-input d-flex">
                        <fieldset class="form-group position-relative has-icon-left col-10 m-0">
                            <div class="form-control-position">
                               <i class="icon-emoticon-smile"></i>
                            </div>
                            <input type="text" class="form-control" id="iconLeft4" placeholder="Type your message">
                            <div class="form-control-position control-position-right">
                               <i class="ft-image"></i>
                            </div>
                        </fieldset>
                        <fieldset class="form-group position-relative has-icon-left col-2 m-0">
                            <button type="button" class="btn btn-primary"><i class="fa fa-paper-plane-o d-lg-none"></i>
                            <span class="d-none d-lg-block">Send</span>
                            </button>
                        </fieldset>
                    </form>

               </section>

            </div>

         </div>

      </div>

   </div>

</div>

@endsection

@section('scripts')

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/pages/chat-application.css')}}">

@endsection
