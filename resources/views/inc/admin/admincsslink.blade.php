      <!-- <link rel="apple-touch-icon" href="{{ URL::to('public/img/mx3-logo.png') }}"> -->
      <!-- <link rel="shortcut icon" type="image/x-icon" href="{{ URL::to('public/img/mx3-logo.png') }}"> -->
      <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,600%7CIBM+Plex+Sans:300,400,500,600,700" rel="stylesheet">

      <!-- BEGIN: Vendor CSS-->
      <link rel="stylesheet" type="text/css" href="{{ URL::to('public/admin/app-assets/vendors/css/vendors.min.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ URL::to('public/admin/app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ URL::to('public/admin/app-assets/vendors/css/pickers/daterange/daterangepicker.css') }}">
      <link href="{{ URL::to('public/admin/app-assets/vendors/jquery-ui-1.12.1/jquery-ui.css') }}" rel="stylesheet" />
      <link rel="stylesheet" type="text/css" href="{{ URL::to('public/admin/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ URL::to('public/admin/app-assets/vendors/css/charts/apexcharts.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ URL::to('public/admin/app-assets/vendors/css/extensions/swiper.min.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ URL::to('public/admin/app-assets/vendors/css/forms/select/select2.min.css') }}">
      <!-- END: Vendor CSS-->

      <!-- BEGIN: Theme CSS-->
      <link rel="stylesheet" type="text/css" href="{{ URL::to('public/admin/app-assets/css/bootstrap.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ URL::to('public/admin/app-assets/css/bootstrap-extended.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ URL::to('public/admin/app-assets/css/colors.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ URL::to('public/admin/app-assets/css/components.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ URL::to('public/admin/app-assets/css/themes/dark-layout.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ URL::to('public/admin/app-assets/css/themes/semi-dark-layout.css') }}">
      <!-- END: Theme CSS-->

      <!-- BEGIN: Page CSS-->
      <link rel="stylesheet" type="text/css" href="{{ URL::to('public/admin/app-assets/css/core/menu/menu-types/horizontal-menu.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ URL::to('public/admin/app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ URL::to('public/admin/app-assets/css/pages/dashboard-ecommerce.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ URL::to('public/admin/app-assets/css/pages/authentication.css') }}">
      <!-- END: Page CSS-->

       <!-- BEGIN: Input Tag CSS-->
      <link rel="stylesheet" type="text/css" href="{{ URL::to('public/admin/app-assets/css/bootstrap-tagsinput.css') }}">
      <!-- END: Custom CSS-->
      
      <!-- BEGIN: Custom CSS-->
      <link rel="stylesheet" type="text/css" href="{{ URL::to('public/admin/assets/css/style.css') }}">
      <!-- END: Custom CSS-->

      <style type="text/css">
        select.form-control:not([multiple="multiple"]) {
             background-image: url('../philsagapayroll/public/img/combo-arrow.png');
           }

          .custom-select{
            background-image: url('../philsagapayroll/public/img/combo-search.png');
            background-position: calc(100% - 12px) 10px, calc(100% - 20px) 13px, 100% 0;
            background-size: 20px 20px, 15px 15px;
           background-repeat: no-repeat;
           -webkit-appearance: none;
           -moz-appearance: none;;
           padding-right: 1.5rem;
           }
           
        option:disabled {
        background: #b3d9ff;
        color: #fff !important;
        font-weight: 400;
      }
         table tr:hover td {
            background: #cce6ff;
            cursor: pointer;
         }
         .table-layout .clickedrow td{
           background-color: #cce6ff;
          }

         .fieldset-border {
          margin-bottom: 20px;
          padding: 10px;
          border: 1px solid #727E8C;
        }

    .legend-text { 
      display: block;
      width: 100%;
      max-width: 100%;
      padding: 0;
      margin-bottom: 0.2rem;
      font-size: 1rem;
      line-height: inherit;
      color: #727E8C;
      white-space: normal;
      width: auto;
      }
      .modal .modal-content i {
        top: 3px; 
       }
        .table_default_half_height{
          min-height: 300px !important;
        }
        .dropdown-menu.dropdown-menu-right::before{
          right: 0px;
         left: 20px;
        }
        .text-readonly-color{
              background-color:#fff !important;
            }
         .white-color{
                  color:#fff !important;
            }
            .mx3-color{
                  color:#f68c1f !important;
            }

            .mx3-background-color{
                  background-color:#772d6b !important;
                  color: #fff;
            }

            .mx3-disabled-background-color{
                  background-color:#e0e5e7 !important;
            }

            .btn-mx3{
                  background-color:#772d6b !important;
            }

            .btn-mx3:hover, .btn-mx3.hover {
                background-color: #772d6b !important;
                color: #fff;
            } 

            .display_none {
                display: none;
            }            

            .display_unset {
                display: unset !important;
            }            

            .font-normal{
              font-weight: normal !important;
            }

            .remove-margin {
                margin: 0px;
            }            

            .align-center {
                text-align: center;
            }            

            .align-right {
                text-align: right;
            }            

            .bg-full-screen-image-new{
              background : url(public/img/bg.jpg) no-repeat center center;
              background-size : cover;
            }

            .float_right {
              float:right;
            }

            .disabled_border{
              border : 1px solid #a3afbd7a
            }

            .remove_md_padding{
              padding:0px !important; 
            }

            .remove_left_padding{
              padding-left:0px !important; 
            }

            .margin_1{
              margin:1px !important; 
            }

            .margin_top_10{
              margin-top: 10px !important; 
            }

            .margin_bottom_10{
              margin-bottom: 10px !important; 
            }

            .width_100_percent{
              width: 100% !important; 
            }

            .table_default_height{
              min-height: 500px !important; 
            }

            .table_productname_width{
              width: 200px !important; 
            }

            .table_qty_width{
              width: 50px !important; 
            }

            .table_unitmeasure_width{
              width: 50px !important; 
            }

            .table_price_width{
              width: 100px !important; 
            }

            .table_delete_width{
              width: 10px !important; 
            }

            .width_delete_button{
              width: 10px !important; 
            }

            .summary_totals1_width{
              width: 200px !important; 
            }

            .summary_totals_width{
              width: 150px !important; 
            }

            .ui-autocomplete {
              z-index: 999999;
            } 

            .required_field {
              color: red;
              font-size: 8px !important;
            } 

            .red_color {
              color: red;
            } 

            .white_color {
              color: #fff;
            } 

            .unpaid_color {
              background-color:#f61343 !important;
            } 

            .pending_color {
              background-color:#dfb921 !important;
            } 
            .unverified_color {
              background-color:#990099 !important;
            } 
            .verified_color {
              background-color:#0dbee4 !important;
            } 
            .packed_color {
              background-color:#0303d6 !important;
            } 
            .shipped_color {
              background-color:#33ccff !important;
            } 
            .shipback_color {
              background-color:#e85eaf !important;
            } 
            .delivered_color {
              background-color:#26d91a !important;
            } 
            .returned_color {
              background-color:#f53906 !important;
            } 
            .cancel_request_color {
              background-color:#ff1c97 !important;
            } 
            .unremitted_color {
              background-color:#db6715 !important;
            } 
            .remitted_color {
              background-color:#e9cf16 !important;
            } 

            #divLoader{
              position:fixed;
              top: 50%;
              left: 47%;
              margin-top: -50px;
              margin-left: -50px;
              background-color:#fff;
              z-index:10000000;
              filter: alpha(opacity=40); /* For IE8 and earlier */
            }  

      </style>

      <style type="text/css">
[tooltip]{
/*  margin:20px 60px;*/
  position:relative;
  display:inline-block;
}
[tooltip]::before {
    content: "";
    position: absolute;
    top:-6px;
    left:50%;
    transform: translateX(-50%);
    border-width: 4px 6px 0 6px;
    border-style: solid;
    border-color: rgba(0,0,0,0.7) transparent transparent     transparent;
    z-index: 99;
    opacity:0;
}

[tooltip-position='left']::before{
  left:0%;
  top:50%;
  margin-left:-12px;
  transform:translatey(-50%) rotate(-90deg) 
}
[tooltip-position='top']::before{
  left:50%;
}
[tooltip-position='buttom']::before{
  top:100%;
  margin-top:8px;
  transform: translateX(-50%) translatey(-100%) rotate(-180deg)
}
[tooltip-position='right']::before{
  left:100%;
  top:50%;
  margin-left:1px;
  transform:translatey(-50%) rotate(90deg)
}

[tooltip]::after {
    content: attr(tooltip);
    position: absolute;
    left:50%;
    top:-6px;
    transform: translateX(-50%)   translateY(-100%);
    background: rgba(0,0,0,0.7);
    text-align: center;
    color: #fff;
    padding:4px 2px;
    font-size: 12px;
    min-width: 150px;
    border-radius: 5px;
    pointer-events: none;
    padding: 4px 4px;
    z-index:99;
    opacity:0;
}

[tooltip-position='left']::after{
  left:0%;
  top:50%;
  margin-left:-8px;
  transform: translateX(-100%)   translateY(-50%);
}
[tooltip-position='top']::after{
  left:50%;
}
[tooltip-position='buttom']::after{
  top:100%;
  margin-top:8px;
  transform: translateX(-50%) translateY(0%);
}
[tooltip-position='right']::after{
  left:100%;
  top:50%;
  margin-left:8px;
  transform: translateX(0%)   translateY(-50%);
}

[tooltip]:hover::after,[tooltip]:hover::before {
   opacity:1
}

</style>


