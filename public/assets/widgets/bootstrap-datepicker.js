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
            var newDate = new Date(e.date);

            newDate.setMinutes(0);

            if (newDate.getDay() === 6) {
                timerpick.datetimepicker('setHoursDisabled', [0, 1, 2, 3, 4, 5, 6, 7, 8, 19, 20, 21, 22, 23]);
                newDate.setHours(9);
            }
            else {
                timerpick.datetimepicker('setHoursDisabled', [0, 1, 2, 3, 4, 5, 6, 7, 21, 22, 23]);
                newDate.setHours(8);
            }

            dateNow = new Date();
            compareDateOne = new Date(dateNow.getFullYear(), dateNow.getMonth(), dateNow.getDate());
            compareDateTwo = new Date(newDate.getFullYear(), newDate.getMonth(), newDate.getDate());

            if (compareDateOne.getTime() === compareDateTwo.getTime()) {
                newDate.setHours((new Date()).getHours() + 1);
            }

            timerpick.datetimepicker('setStartDate', newDate);
            timerpick.datetimepicker('setEndDate', new Date(e.date.setHours(23)));
            timerpick.datetimepicker('setInitialDate', newDate);
            timerpick.val("");
            timerpick.datetimepicker('update');
        });
    }
};
jQuery(document).ready(function () {
    BootstrapDatepicker.init()
});