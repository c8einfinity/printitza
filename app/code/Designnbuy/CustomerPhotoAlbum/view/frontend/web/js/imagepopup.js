define([ 
    'jquery',
    'Magento_Ui/js/modal/modal',
    'main'
 ],
function(
    $,
    modal
){
    var options = {
        type: 'popup',
        responsive: true,
        innerScroll: true,
        buttons: [{
            text: $.mage.__('Continue'),
            class: 'mymodal1',
            click: function () {
                jQuery("#cropped-canvas").click();
                this.closeModal();
            }
        }]
    };

    var popup = modal(options, $('#popup-modal'));
    $(".click-me").on('click',function(){
        jQuery(this).parents("li.album-photo").addClass("activate");
        jQuery(".img-container #image").attr("src",jQuery(this).attr("data-img"));
        $("#popup-modal").modal("openModal");
    });
});