        <!-- MODAL -->
        <div id="new-product-category-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Product Category Information</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                    <div class="modal-body">

                        <input type="hidden" id="NewProductCategoryID" value="0" readonly>
                        <input type="hidden" id="DestiNewProductCategoryID" value="" readonly>
                        <input type="hidden" id="DestiNewProductCategory" value="" readonly>

                        <div class="row">
                            <div class="col-md-12">
                                <label for="NewProductCategory" class="font-normal">Category <span class="required_field">*</span></label>
                                <input id="NewProductCategory" type="text" class="form-control" placeholder="Category">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <fieldset class="form-group">
                                    <label for="NewCategoryStatus" class="font-normal">Status<span class="required_field">*</span></label>
                                    <div class="form-group">
                                        <select id="NewCategoryStatus" class="form-control select2">
                                            <option value="">Please Select Status</option>
                                            <option value="{{ config('app.STATUS_ACTIVE') }}">{{ config('app.STATUS_ACTIVE') }}</option>
                                            <option value="{{ config('app.STATUS_INACTIVE') }}">{{ config('app.STATUS_INACTIVE') }}</option>
                                        </select>
                                    </div>
                                </fieldset>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button id="btnNewProductCategorySave" type="button" class="btn btn-primary ml-1" onclick="SaveNewProductCategoryRecord()">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Save</span>
                        </button>
                        <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Cancel</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- END MODAL -->

        <!-- MODAL -->
        <div id="new-product-brand-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Product Category Information</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                    <div class="modal-body">

                        <input type="hidden" id="NewProductBrandID" value="0" readonly>
                        <input type="hidden" id="DestiNewProductBrandID" value="" readonly>
                        <input type="hidden" id="DestiNewProductBrand" value="" readonly>

                        <div class="row">
                            <div class="col-md-12">
                                <label for="NewProductBrand" class="font-normal">Brand <span class="required_field">*</span></label>
                                <input id="NewProductBrand" type="text" class="form-control" placeholder="Brand">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <fieldset class="form-group">
                                    <label for="NewBrandStatus" class="font-normal">Status<span class="required_field">*</span></label>
                                    <div class="form-group">
                                        <select id="NewBrandStatus" class="form-control select2">
                                            <option value="">Please Select Status</option>
                                            <option value="{{ config('app.STATUS_ACTIVE') }}">{{ config('app.STATUS_ACTIVE') }}</option>
                                            <option value="{{ config('app.STATUS_INACTIVE') }}">{{ config('app.STATUS_INACTIVE') }}</option>
                                        </select>
                                    </div>
                                </fieldset>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button id="btnNewProductBrandSave" type="button" class="btn btn-primary ml-1" onclick="SaveNewProductBrandRecord()">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Save</span>
                        </button>
                        <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Cancel</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- END MODAL -->

        <!-- MODAL -->
        <div id="transaction-log-modal" class="modal fade text-left w-100 " role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Transaction Log</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="card-content">
                            <div class="card-body card-dashboard">
                                <div class="table-responsive col-md-12">
                                    <table id="tblTransLog" class="table zero-configuration complex-headers border">
                                        <thead>
                                            <tr>
                                                <th>Date/Time</th>
                                                <th>Trans Type</th>
                                                <th>By</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- END MODAL -->

        <!--primary theme Modal -->
        <div id="primary-message-modal" class="modal fade text-left" tabindex="-1" role="dialog" aria-labelledby="spnPrimaryMessageHeader" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 id="spnPrimaryMessageHeader" class="modal-title white"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <span id="spnPrimaryMessage"></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span id="spnPrimaryMessageButtonLabel" class="d-none d-sm-block">OK</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!--Success theme Modal -->
        <div id="success-message-modal" class="modal fade text-left" tabindex="-1" role="dialog" aria-labelledby="spnSuccessMessageHeader" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h5 id="spnSuccessMessageHeader" class="modal-title white"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <span id="spnSuccessMessage"></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span id="spnSuccessMessageButtonLabel" class="d-none d-sm-block">OK</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!--Danger theme Modal -->
        <div id="danger-message-modal" class="modal fade text-left" tabindex="-1" role="dialog" aria-labelledby="spnDangerMessageHeader" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 id="spnDangerMessageHeader" class="modal-title white"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <span id="spnDangerMessage"></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span id="spnDangerMessageButtonLabel" class="d-none d-sm-block">OK</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!--info theme Modal -->
        <div id="info-message-modal" class="modal fade text-left" tabindex="-1" role="dialog" aria-labelledby="spnInfoMessageHeader" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h5 id="spnInfoMessageHeader" class="modal-title white"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <span id="spnInfoMessage"></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span id="spnInfoMessageButtonLabel" class="d-none d-sm-block">OK</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!--warning theme Modal -->
        <div id="warning-message-modal" class="modal fade text-left" tabindex="-1" role="dialog" aria-labelledby="spnWarningMessageHeader" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 id="spnWarningMessageHeader" class="modal-title white"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <span id="spnWarningMessage"></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span id="spnWarningMessageButtonLabel" class="d-none d-sm-block">OK</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
