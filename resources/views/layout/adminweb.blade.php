<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

  <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
      
      {{-- csrf --}}
      <meta name="csrf-token" content="{{ csrf_token() }}">
       
      <link rel="icon" href="favicon.ico" type="image/x-icon">
      <title>.:: NITRO PAYROLL ::. | {{ $Page }}</title>

      @include('inc.admin.admincsslink')
        <script src="{{ URL::to('public/admin/app-assets/vendors/js/vendors.min.js') }}"></script>
       <style>
        
#style-2::-webkit-scrollbar-track
{
  -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
  border-radius: 10px;
  background-color: #F5F5F5;
}

#style-2::-webkit-scrollbar
{
  width: 12px;
  background-color: #F5F5F5;
}

#style-2::-webkit-scrollbar-thumb
{
  border-radius: 10px;
  -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
  background-color: #d9d9d9;
}

.checkbox.checkbox-primary input:checked ~ label::before, .checkbox.radio-primary input:checked ~ label::before, .radio.checkbox-primary input:checked ~ label::before, .radio.radio-primary input:checked ~ label::before {
    background-color: #f68c1f;
    border-color: #f68c1f;
}
        .main-menu .navbar-header .navbar-brand .brand-logo .logo{
          height: 30px;
        }
        table th, .table td {
          padding: 7px 7px !important;
        }
        .table.dataTable thead .sorting:before, .table.dataTable thead .sorting:after, .table.dataTable thead .sorting_asc:before, .table.dataTable thead .sorting_asc:after, .table.dataTable thead .sorting_desc:before, .table.dataTable thead .sorting_desc:after{
              top: -1px !important;
              right: 3px;
        }
         .table thead{
          text-transform: capitalize;
          background: #475F7B;
        }
        .table.dataTable thead .sorting:before, .table.dataTable thead .sorting:after, .table.dataTable thead .sorting_asc:before, .table.dataTable thead .sorting_asc:after, .table.dataTable thead .sorting_desc:before, .table.dataTable thead .sorting_desc:after{
          color:#fff;
          font-size:12px;
          line-height:35px;
        }
        table.dataTable thead>tr>th.sorting_asc, table.dataTable thead>tr>th.sorting_desc, table.dataTable thead>tr>th.sorting, table.dataTable thead>tr>td.sorting_asc, table.dataTable thead>tr>td.sorting_desc, table.dataTable thead>tr>td.sorting{
              color: #fff;
        }
        .table {
         color: black;
        }
        .row {
          margin-right: 0px; 
           margin-left: 0px;
        }
        .card-header{
           padding: 13px 32px;
        }
        .card .card-title{
          font-size: 17px;
        }
        .custom-select{
          height: auto;
        }
        .mb-1, .my-1 {
           margin-bottom: 5px !important;
        }
        .main-menu.menu-light .navigation > li {
            margin: 0 10px;
            transition: background-color 0.5s ease;
        }
        .main-menu.menu-light .navigation .navigation-header{
          background: #475F7B;
          margin:5px 15px;
          padding: 4px;
          text-align: center;
        }
       
        /* Outline buttons */
        .btn-outline-primary {
          border: 1px solid #475F7B;
          background-color: transparent;
          color: #475F7B !important;
        }
        .text-align-left{
          text-align: lef;
        }
         .text-align-right{
          text-align: right;
        }
        .div-percent{
          display: flex;
          border-radius: 4px 4px 4px 4px;
          border-left: 1px solid #e5e7eb;
          border-bottom: 1px solid #e5e7eb;
          border-top: 1px solid #e5e7eb;
        }
     
        .percent-sign {
          display: flex;
          color: #9ca3af;
          height: 100%;
          background: #f3f4f6;
          padding: 9px 16px;
          font-size: 14px;  
          font-weight: bolder;
          border-right: 1px solid #DFE3E7;
          line-height: 21px;
          }
          .no-border{

             line-height: 20px;
          border-radius: 0px 0px 0px 0px;
          border-left: 0px;
          border-bottom: 0px;
          border-top: 0px;

          }
        .btn-outline-primary:hover, .btn-outline-primary.hover {
          background-color: #475F7B !important;
          color: #fff !important;
          border: 1px solid #475F7B;
        }
        .form-control:focus {
        color: #475F7B;
        background-color: #FFFFFF;
        border-color: #475F7B;
        outline: 0;
        box-shadow: 0 3px 8px 0 rgba(0, 0, 0, 0.1);
      }
      .btn-primary {
        border-color: #475F7B !important;
        background-color: #475F7B !important;
        color: #fff;
      }
      .btn-primary:hover, .btn-primary.hover {
        background-color: #475F7B !important;
        color: #fff;
      }
      .search-txt{
        font-size: 11px;
        color:red;
        font-weight: normal;
        padding-left:10px;
        text-transform: inherit !important;
        font-style: italic;
      }
        .badge {
          padding: 4px 4px !important; 
        }
        .breadcrumb-item.active{
          color:#475F7B !important;
          font-size: 14px;
        }
        .main-menu .ps__thumb-y {
    background-color: #f68c1f;
}
.ps__thumb-y{
  transition: unset;
  -webkit-transition: unset;
}
.form-group {
    margin-bottom: 6px;
}
.modal-header {
    padding: 6px 6px;
  }
  .modal-body{
        padding: 4px 15px;
  }
  .modal .modal-content .modal-header{
    background: #475F7B;
  }
  .modal .modal-content .modal-header .modal-title{
    font-size: 15px;
  }
  .modal .modal-content .modal-header .close{
    background-color: #f68c1f;
    opacity: 1;
    color: #fff;
  }
  .modal-footer{
    padding: 8px 8px;
  }
  label {
    text-transform: capitalize;
    font-size: 13.5px;
    font-weight: 600;
   }    
  </style>

  <style>

  .close-toast:hover{
    background-color: transparent !important;
  }
   .bx bx-window-close:hover{
    background-color: transparent !important;
  }

  .toast-error-notification {
  z-index: 99999999;
  position: fixed;
  top: 100px;
  right: -310px;
  width: 300px;
  background-color: #bd2130;
  color: #fff;
  box-shadow: 0px 0px 4px #404040ba;
  border-radius: 4px 0 0 4px;
  text-align: center;
  padding:10px;

  a:link,
  a:visited,
  a:hover {
    color: #ffcb00;
    text-decoration: underline;
  }

  a:hover {
    color: #ffe062;
    text-decoration: underline;
  }

  .toast-content {

      width: 100%;
      text-align: center;
      padding: 5px;
      font-size: 16px;
      color:#fff;

    .close-toast {
      font-weight: 700;
      opacity: 0.8;
      text-shadow: 0 1px 0 #fff;
      font-size: 20px;
      line-height: 12px;
      color: #fff;
      width: 25px;
      left: -7px;
      position: absolute;
      top: -8px;
      cursor: pointer;
    }
    .close-toast:hover{
      background-color: black;
    }
    .toast-message {
      color:#fff;
      line-height: 17px;
      text-align: center;
    }
  }
}

.toast-show {
  animation-name: showtoast;
  animation-duration: 6s;
}
@keyframes  showtoast {
  0% {
    right: -310px;
    opacity: 1;
  }
  5% {
    right: 0px;
    opacity: 1;
  }
  95% {
    right: 0px;
    opacity: 1;
  }
  100% {
    opacity: 0;
  }
}

 .toast-success-notification {
      z-index: 99999999;
      position: fixed;
      top: 100px;
      right: -310px;
      width: 300px;
      background-color: #119744;
      color: #fff;
      box-shadow: 0px 0px 4px #404040ba;
      border-radius: 4px 0 0 4px;
      text-align: center;
      padding:10px;

      a:link,
      a:visited,
        a:hover {
        color: #ffcb00;
        text-decoration: underline;
        }

        a:hover {
          color: #ffe062;
          text-decoration: underline;
        }

        .toast-content {

        width: 100%;
        text-align: center;
        padding: 5px;
        font-size: 16px;
           color:#fff;

        .close-toast {
          font-weight: 700;
          opacity: 0.8;
          text-shadow: 0 1px 0 #fff;
          padding: 4px;
          font-size: 20px;
          line-height: 12px;
          color: #fff;
          width: 25px;
          left: -7px;
          position: absolute;
          top: -8px;
          cursor: pointer;
          z-index:99999;
        }
        .close-toast:hover{
          background-color: black;
        }
        .toast-message {
          color:#fff;
          line-height: 17px;
          text-align: center;
        }
      }
    }
    .x-success-close{
      left: 20px;
      border: 1px solid #fff;
      width: 15px;
      height: 17px;
      background: #fff;
      color: #4bd52c;
      font-weight: bold;
      float: left;
      border-radius: 3px;
      font-size: 13px;
      line-height: 15px;
    }

  </style>
  <style>
    .col-1, .col-2, .col-3, .col-4, .col-5, .col-6, .col-7, .col-8, .col-9, .col-10, .col-11, .col-12, .col, .col-auto, .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm, .col-sm-auto, .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12, .col-md, .col-md-auto, .col-lg-1, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg, .col-lg-auto, .col-xl-1, .col-xl-2, .col-xl-3, .col-xl-4, .col-xl-5, .col-xl-6, .col-xl-7, .col-xl-8, .col-xl-9, .col-xl-10, .col-xl-11, .col-xl-12, .col-xl, .col-xl-auto{
         padding-right: 6px;
         padding-left: 6px;
    }
  </style>
  </head>
  <!-- END: Head-->

  <!-- BEGIN: Body-->
  @if($Page == "Admin Login")
    <body class="vertical-layout vertical-menu-modern 1-column navbar-sticky footer-static bg-full-screen-image-new  blank-page blank-page" data-open="click" data-menu="vertical-menu-modern" data-col="1-column">
  @else
    @if(Session("ADMIN_LOGGED_IN"))
      <body class="vertical-layout vertical-menu-modern 2-columns  navbar-sticky footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">

      @include('inc.admin.adminsidenav')

    @else
      <body class="vertical-layout vertical-menu-modern 1-column navbar-sticky footer-static bg-full-screen-image-new  blank-page blank-page" data-open="click" data-menu="vertical-menu-modern" data-col="1-column">
    @endif
  @endif

      <div id="divLoader" style="display:none;border: 1px solid gray;border-radius: 5px;box-shadow: 10px 10px 10px rgba(0, 0, 0, 0.4);padding-top: 10px;">
        <center>
        <img src="{{URL::asset('public/img/loader.gif')}}" style="height: 7rem;width: auto;padding-top: 10px;"> 
         <div id='dvLoading' style="position: relative;top: -80px; color:red;">
             <center>
               <span id="spnLoadingLabel" style="font-size:17px;padding: 10px;padding-bottom: 10px;">Loading..<span>
            </center>
          </div>

        <span  id="divLoader1" style="display:none;padding: 5px;">
          <div style="position: relative;top: -45px;color:red;text-align: center;font-size: 15px;">
            <span id="spnLoader1Text" style="display:none;"> do somthing..</span> 
            <span id="spnTotalData" style="display:none;">0/0</span>
           </div> 
      </span>
      </center>
      </div>

      <!-- Message Nofit-->
      @include('inc.admin.adminmessage')
      <!-- BEGIN: Content-->
      @yield('content')
      <!-- END: Content-->
      @include('inc.admin.adminmodal')
      @include('inc.admin.adminjsfooter')
      @include('inc.admin.adminfooter')

  </body>

</html>

