require(
    [
        'jquery',
        'Magento_Ui/js/modal/modal'
    ],
    function(
        $,
        modal
    ) {
        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            title: 'SEND US AN ENQUIRY',
            modalClass: 'enquiry-main-custom-modal',
            buttons: []
        };

        var popup = modal(options, $('#popup-modal'));
        $("#product-enquiry-button").on('click',function(){
            $(".modal-popup.enquiry-main-custom-modal .contact-container .col-contact-info, .modal-popup.enquiry-main-custom-modal .contact-container .form-area").removeClass("col-sm-6");
            $(".modal-popup.enquiry-main-custom-modal .contact-container .col-contact-info, .modal-popup.enquiry-main-custom-modal .contact-container .form-area").removeClass("col-xs-12");
            $(".modal-popup.enquiry-main-custom-modal .contact-container .col-contact-info, .modal-popup.enquiry-main-custom-modal .contact-container .form-area .page-title").remove();

            $("#popup-modal").modal("openModal");
        });
    }
);