var Inputmask = {
    init: function () {
        $(".maskPhoneNumber").inputmask("mask", {
            mask: "(999) 999-9999",
            removeMaskOnSubmit: true,
        })
    }
};
jQuery(document).ready(function () {
    Inputmask.init()
});