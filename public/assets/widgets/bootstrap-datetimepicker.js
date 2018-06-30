var BootstrapDatetimepicker = {
    init: function () {
        var initialDate = (new Date());

        $(".input_timepicker").datetimepicker({
            todayHighlight: true,
            autoclose: true,
            pickerPosition: "bottom-right",
            format: "hh:ii",
            minView: 1,
            maxView: 1,
            startView: 1,
            forceParse: 0,
            startDate: new Date(initialDate.setMinutes(0)),
            endDate: new Date(initialDate.setHours(23)),
            initialDate: new Date(initialDate.setMinutes(0)),
            hoursDisabled: [0, 1, 2, 3, 4, 5, 6, 7, 21, 22, 23]
        });

        $(".input_datetimepicker").datetimepicker({
            todayHighlight: false,
            minView: 1,
            autoclose: true,
            clearBtn : true,
            pickerPosition: "bottom-right",
            format: "mm/dd/yyyy hh:ii",
            forceParse: 0
        })
    }
};
jQuery(document).ready(function () {
    BootstrapDatetimepicker.init()
});