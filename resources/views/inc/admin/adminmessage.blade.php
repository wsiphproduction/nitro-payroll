    @if(Session("Error_Message"))
      <div class="alert alert-danger alert-dismissible mb-2" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
          <div class="d-flex align-items-center">
              <i class="bx bx-error"></i>
              <span>
                  {{ Session("Error_Message") }}
              </span>
          </div>
      </div>
    @endif

    @if(Session("Success_Message"))
      <div class="alert alert-success alert-dismissible mb-2" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
          <div class="d-flex align-items-center">
              <i class="bx bx-like"></i>
              <span>
                  {{ Session("Success_Message") }}
              </span>
          </div>
      </div>
    @endif


  <div class="toast-error toast-error-notification"  style="border:2px solid #fff;">
    <div class="toast-content" style="display: flex;">
        <div class="close-toast" style="padding-right:10px;cursor:pointer;">
          <i class="bx bx-window-close" style="font-size:30px;"></i>       
        </div>
          <div id="toast-error-message" class="toast-message"></div> 
    </div>
 </div>
 

  <div class="toast-success toast-success-notification"  style="border:2px solid #fff;">
     <div class="toast-content" style="display: flex;">
         <div class="close-toast" style="padding-right:10px;cursor:pointer;">
          <i class="bx bx-window-close" style="font-size:30px;"></i>       
        </div>
        <div id="toast-success-message" class="toast-message"></div> 
    </div>
 </div> 


