var BootstrapDatepicker = {
    init: function () {
        $(".input_datepicker").datepicker({
            todayHighlight: !0,
            orientation: "bottom left",
            autoclose: true,
            daysOfWeekDisabled: '0',
            maxViewMode: 1,
            startDate: new Date(),
            templates: {leftArrow: '<i class="la la-angle-left"></i>', rightArrow: '<i class="la la-angle-right"></i>'}
        }).on('changeDate', function (e) {
            var timerpick = $(".input_timepicker");
            if (e.date.getDay() === 6) {
                timerpick.datetimepicker('setHoursDisabled', [0, 1, 2, 3, 4, 5, 6, 7, 8, 19, 20, 21, 22, 23]);
            }
            else {
                timerpick.datetimepicker('setHoursDisabled', [0,1,2,3,4,5,6,7,21,22,23]);
            }
            timerpick.val("");
            timerpick.datetimepicker('update');
        });
    }
};
jQuery(document).ready(function () {
    BootstrapDatepicker.init()
});