var config = {
    map: {
        "*": {
            'designdropzone': 'Designnbuy_CustomerPhotoAlbum/js/dropzone',
            'modelpopup': 'Designnbuy_CustomerPhotoAlbum/js/imagepopup',
            'cropper': 'Designnbuy_CustomerPhotoAlbum/js/cropper',
            'main': 'Designnbuy_CustomerPhotoAlbum/js/main',
        }
    },
    shim: {
        "designdropzone": {
            deps: [
                "jquery"
            ]
        },
    }
};