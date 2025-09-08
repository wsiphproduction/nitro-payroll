      <!-- BEGIN: Vendor JS-->
      <script src="{{ URL::to('public/admin/app-assets/fonts/LivIconsEvo/js/LivIconsEvo.tools.js') }}"></script>
      <script src="{{ URL::to('public/admin/app-assets/fonts/LivIconsEvo/js/LivIconsEvo.defaults.js') }}"></script>
      <script src="{{ URL::to('public/admin/app-assets/fonts/LivIconsEvo/js/LivIconsEvo.min.js') }}"></script>
      <!-- BEGIN Vendor JS-->

      <script src="{{ URL::to('public/admin/app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script>
      <script src="{{ URL::to('public/admin/app-assets/vendors/js/pickers/pickadate/picker.date.js') }}"></script>
      <script src="{{ URL::to('public/admin/app-assets/vendors/js/pickers/pickadate/picker.time.js') }}"></script>
      <script src="{{ URL::to('public/admin/app-assets/vendors/js/pickers/pickadate/legacy.js') }}"></script>
      <script src="{{ URL::to('public/admin/app-assets/vendors/js/pickers/daterange/moment.min.js') }}"></script>
      <script src="{{ URL::to('public/admin/app-assets/vendors/js/pickers/daterange/daterangepicker.js') }}"></script>
      <script src="{{ URL::to('public/admin/app-assets/vendors/js/ui/jquery.sticky.js') }}"></script>
      <script src="{{ URL::to('public/admin/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
      <script src="{{ URL::to('public/admin/app-assets/vendors/js/tables/datatable/dataTables.rowsGroup.js') }}"></script>
      <script src="{{ URL::to('public/admin/app-assets/vendors/js/charts/apexcharts.min.js') }}"></script>
      <script src="{{ URL::to('public/admin/app-assets/vendors/js/extensions/swiper.min.js') }}"></script>
      <script src="{{ URL::to('public/admin/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
      <!-- END: Page Vendor JS-->

      <script src="{{ URL::to('public/admin/app-assets/js/scripts/configs/horizontal-menu.js') }}"></script>
      <script src="{{ URL::to('public/admin/app-assets/js/core/app-menu.js') }}"></script>
      <script src="{{ URL::to('public/admin/app-assets/js/core/app.js') }}"></script>
      <script src="{{ URL::to('public/admin/app-assets/js/scripts/components.js') }}"></script>
      <script src="{{ URL::to('public/admin/app-assets/js/scripts/footer.js') }}"></script>
      <script src="{{URL::to('public/admin/app-assets/vendors/jquery-ui-1.12.1/jquery-ui.min.js') }}"></script>
      <script src="{{URL::to('public/admin/app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js') }}"></script>
      <!-- END: Theme JS-->
      <script src="{{ URL::to('public/admin/app-assets/js/scripts/tag-input/bootstrap-tagsinput.js') }}"></script>
      <script src="{{ URL::to('public/admin/app-assets/js/scripts/forms/select/form-select2.js') }}"></script>
      <script src="{{ URL::to('public/admin/excel/xlsx.full.min.js') }}"></script>

      <script type="text/javascript">

            var loadFile = function(event) {
              var reader = new FileReader();
              reader.onload = function(){
                var output = document.getElementById('output');
                output.src = reader.result;
              };
              reader.readAsDataURL(event.target.files[0]);
            };
            
            $('#tblTransLog').DataTable( {
                'paging'      : false,
                'lengthChange': false,
                'searching'   : false,
                'ordering'    : false,
                'info'        : false,
                'autoWidth'   : false
            });


            function buttonOneClick(vID, vLabel, vIsDisabled){
                  var btn = $("#"+vID);
                  if(vIsDisabled){
                        btn.html('<img src="{{ URL::to('public/img/button-loader.gif') }}" style="max-height:15px;">');
                        btn.attr("disabled", true);
                  }else{
                        vHtml = "<i class='bx bx-check d-block d-sm-none'></i>";
                        vHtml += "<span class='d-none d-sm-block'>" + vLabel + "</span>";
                        btn.html(vHtml);
                        btn.removeAttr("disabled");
                  }
            } 
                       
            function FormatDecimal(vValue, vDecimal){

              var vReturn
              try {
                vReturn = vValue.toString().replace(",","");
                vReturn = parseFloat(vValue);
                vReturn = vReturn.toLocaleString("en-US", { maximumFractionDigits: 2, minimumFractionDigits: 2 });
              }
              catch(err) {
                vReturn = 0;
              }

              return vReturn;
            }

            function ExcelColumn(vRow, vCol){

              var vAlpha = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"];

              var intInnerCol = 0;
              var vReturn = "";
              var intCol = 0;
              for(var intCntr = 0; intCntr <= vCol; intCntr++){

                if(intCol >= vAlpha.length){
                  vReturn = vAlpha[intInnerCol];
                  intInnerCol = intInnerCol + 1;
                  intCol = 0;
                }

                if(((vAlpha.length * intInnerCol) + intCol) == vCol){
                    vReturn = vReturn + vAlpha[intCol];
                }else{
                  intCol = intCol + 1;
                }
                
              }

              vReturn = vReturn + vRow;

              return vReturn;

            }

            $(function () {

              $(".DecimalOnly").on("keypress keyup blur",function (event) {
                  $(this).val($(this).val().replace(/[^0-9\.]/g,''));
                  if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                      event.preventDefault();
                  }
              });

              $(".NumberOnly").on("keypress keyup blur",function (event) {
                 $(this).val($(this).val().replace(/[^\d].+/, ""));
                  if ((event.which < 48 || event.which > 57)) {
                      event.preventDefault();
                  }
              });

              $('.AlphaNumericOnly').keypress(function (e) {
                  var regex = new RegExp("^[a-zA-Z0-9]+$");
                  var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                  if (regex.test(str)) {
                      return true;
                  }

                  e.preventDefault();
                  return false;
              });

              function GetUrlParameters(vUrl) {

                  vUrl = vUrl || window.location.search.substring(1);
                  var urlParams = {};
                  var match,
                      pl     = /\+/g,  // Regex for replacing addition symbol with a space
                      search = /([^&=]+)=?([^&]*)/g,
                      decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); },
                      query  = vUrl;

                  while (match = search.exec(query))
                     urlParams[decode(match[1])] = decode(match[2]);

                  return urlParams;

              };

            })  


      function toast(toastClassName, toastMessage) {

        let toastMain = document.getElementsByClassName(toastClassName)[0];
        let toastContent = document.getElementById("toast-error-message");
        
        if(toastClassName!='toast-error'){
             toastContent = document.getElementById("toast-success-message");
        }
        
        toastContent.innerHTML = toastMessage;
        toastMain.classList.remove("toast-show");
        
        setTimeout(function () {
          toastMain.classList.add("toast-show");
        }, 150);
        toastMain.addEventListener("click", function () {
          toastMain.classList.remove("toast-show");
        });
      
    }

    function showHasErrorMessage(vElemID,vErrorMessage){

      //remove existing toast message if any success message so it will not overlap
      let toastMain = document.getElementsByClassName('toast-success')[0];
      toastMain.classList.remove("toast-show");

      if(vElemID!=''){
        $("#"+ vElemID+"").css({'border':'#a94442 1px solid'});   
        $("#"+ vElemID+"").focus();
      }

      toast('toast-error', vErrorMessage);
      return;

     }

     function showHasSuccessMessage(vSuccessMessage){

         //remove existing toast message if any error message so it will not overlap
        let toastMain = document.getElementsByClassName('toast-error')[0];
        toastMain.classList.remove("toast-show");
      
       toast('toast-success', vSuccessMessage);
       return;
     }

     function getFormattedDate(date) {
      let year = date.getFullYear();
      let month = (1 + date.getMonth()).toString().padStart(2, '0');
      let day = date.getDate().toString().padStart(2, '0');
    
      return month + '/' + day + '/' + year;
    }   

       </script>