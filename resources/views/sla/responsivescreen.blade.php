@extends('layouts.app')
@section('content')
<div class="layout-2col layout-2col-settings">
  <div class="sidebar-2col">
    <div class="sidebar-title">
      {{ __('Mobile Application') }}
    </div>
    <div class="sidebar-title reports-sidebar-title">
      <span class="glyphicon glyphicon-phone"></span>
      {{ __('Mobile Application') }}
    </div>
  </div>
  <div class="content-2col">
    <div class="section-heading">
      {{ __('Mobile Application') }}
    </div>
    <div class="container_settings row-container form-container top-margin">
        <div class="inner-container row">
            <div class="col-xs-12">
                <img class="mobilescreen" src="{{ asset('img/dashboard.jpeg') }}" alt="" height="600px" width="300px">
                <img class="mobilescreen" src="{{ asset('img/manu.jpeg') }}" alt="" height="600px" width="300px">
                <img class="mobilescreen" src="{{ asset('img/customerservice.jpeg') }}" alt="" height="600px" width="300px">
                <img class="mobilescreen" src="{{ asset('img/customerportal.jpeg') }}" alt="" height="600px" width="300px">
            </div>
            <div class="col-xs-12">
              <img class="mobilescreen" src="{{ asset('img/contactpage.jpeg') }}" alt="" height="600px" width="300px">
              <img class="mobilescreen" src="{{ asset('img/enduserportal.jpeg') }}" alt="" height="600px" width="300px">
              <img class="mobilescreen" src="{{ asset('img/workflow.jpeg') }}" alt="" height="600px" width="300px">
              <img class="mobilescreen" src="{{ asset('img/ticket.jpeg') }}" alt="" height="600px" width="300px">
          </div>
        </div>
    </div>
  </div>
</div>
<style>
  .mobilescreen{
    margin-left: 20px;
    margin-top: 50px;
    border: solid 2px;
  }
    .layout-2col-settings{
      margin-top: -19px;
    }
    .reports-sidebar-title{
      font-size: 15px;
    }
  </style>
@endsection
