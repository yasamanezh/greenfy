/**
 * Form Picker
 */

'use strict';

// Flat Picker
(function () {

    // Flat Picker
    // --------------------------------------------------------------------
    const flatpickrDate = document.querySelector('#flatpickr-date'),
        flatpickrTime = document.querySelector('#flatpickr-time'),
        flatpickrDateTime = document.querySelector('#flatpickr-datetime'),
        flatpickrMulti = document.querySelector('#flatpickr-multi'),
        flatpickrRange = document.querySelector('#flatpickr-range'),
        flatpickrInline = document.querySelector('#flatpickr-inline'),
        flatpickrFriendly = document.querySelector('#flatpickr-human-friendly'),
        flatpickrDisabledRange = document.querySelector('#flatpickr-disabled-range');

    // Date
    if (flatpickrDate) {
        flatpickrDate.flatpickr({
            disableMobile: "true",
            locale: 'fa',
            monthSelectorType: 'static'
        });
    }

    // Time
    if (flatpickrTime) {
        flatpickrTime.flatpickr({
            disableMobile: "true",
            locale: 'fa',
            enableTime: true,
            noCalendar: true,
        });
    }

    // Datetime
    if (flatpickrDateTime) {
        flatpickrDateTime.flatpickr({
            disableMobile: "true",
            locale: 'fa',
            enableTime: true,
            dateFormat: 'Y-m-d H:i'
        });
    }

    // Multi Date Select
    if (flatpickrMulti) {
        flatpickrMulti.flatpickr({
            disableMobile: "true",
            locale: 'fa',
            weekNumbers: true,
            enableTime: true,
            mode: 'multiple',
            minDate: 'today'
        });
    }

    // Range
    if (typeof flatpickrRange != undefined) {
        flatpickrRange.flatpickr({
            disableMobile: "true",
            locale: 'fa',
            mode: 'range'
        });
    }

    // Inline
    if (flatpickrInline) {
        flatpickrInline.flatpickr({
            disableMobile: "true",
            locale: 'fa',
            inline: true,
            allowInput: false,
            monthSelectorType: 'static'
        });
    }

    // Human Friendly
    if (flatpickrFriendly) {
        flatpickrFriendly.flatpickr({
            disableMobile: "true",
            locale: 'fa',
            altInput: true,
            altFormat: 'F j, Y',
            dateFormat: 'Y-m-d'
        });
    }

    // Disabled Date Range
    if (flatpickrDisabledRange) {
        const fromDate = new Date(Date.now() - 3600 * 1000 * 48);
        const toDate = new Date(Date.now() + 3600 * 1000 * 48);

        flatpickrDisabledRange.flatpickr({
            disableMobile: "true",
            locale: 'fa',
            dateFormat: 'Y-m-d',
            disable: [
                {
                    from: fromDate.toISOString().split('T')[0],
                    to: toDate.toISOString().split('T')[0]
                }
            ]
        });
    }
})();

$.fn.datepicker.dates['fa'] = {
    days: ["یکشنبه", "دوشنبه", "سه‌شنبه", "چهارشنبه", "پنجشنبه", "جمعه", "شنبه"],
    daysShort: ["یک", "دو", "سه", "چهار", "پنج", "جمعه", "شنبه"],
    daysMin: ["ی", "د", "س", "چ", "پ", "ج", "ش"],
    months: ["ژانویه", "فوریه", "مارس", "آپریل", "می", "جوئن", "جولای", "آگست", "سپتامبر", "اکتبر", "نوامبر", "دسامبر"],
    monthsShort: ["ژانویه", "فوریه", "مارس", "آپریل", "می", "جوئن", "جولای", "آگست", "سپتامبر", "اکتبر", "نوامبر", "دسامبر"],
    today: "امروز",
    clear: "پاک کردن",
    titleFormat: "MM yyyy", /* Leverages same syntax as 'format' */
};

// * Pickers with jQuery dependency (jquery)
$(function () {
    // Bootstrap Datepicker
    // --------------------------------------------------------------------
    var bsDatepickerBasic = $('#bs-datepicker-basic'),
        bsDatepickerFormat = $('#bs-datepicker-format'),
        bsDatepickerRange = $('#bs-datepicker-daterange'),
        bsDatepickerDisabledDays = $('#bs-datepicker-disabled-days'),
        bsDatepickerMultidate = $('#bs-datepicker-multidate'),
        bsDatepickerOptions = $('#bs-datepicker-options'),
        bsDatepickerAutoclose = $('#bs-datepicker-autoclose'),
        bsDatepickerInlinedate = $('#bs-datepicker-inline');

    // Basic
    if (bsDatepickerBasic.length) {
        bsDatepickerBasic.datepicker({
            language: 'fa',
            todayHighlight: true,
            orientation: isRtl ? 'auto right' : 'auto left'
        });
    }

    // Format
    if (bsDatepickerFormat.length) {
        bsDatepickerFormat.datepicker({
            language: 'fa',
            todayHighlight: true,
            format: 'dd/mm/yyyy',
            orientation: isRtl ? 'auto right' : 'auto left'
        });
    }

    // Range
    if (bsDatepickerRange.length) {
        bsDatepickerRange.datepicker({
            language: 'fa',
            todayHighlight: true,
            orientation: isRtl ? 'auto right' : 'auto left'
        });
    }

    // Disabled Days
    if (bsDatepickerDisabledDays.length) {
        bsDatepickerDisabledDays.datepicker({
            language: 'fa',
            todayHighlight: true,
            daysOfWeekDisabled: [0, 6],
            orientation: isRtl ? 'auto right' : 'auto left'
        });
    }

    // Multiple
    if (bsDatepickerMultidate.length) {
        bsDatepickerMultidate.datepicker({
            language: 'fa',
            multidate: true,
            todayHighlight: true,
            orientation: isRtl ? 'auto right' : 'auto left'
        });
    }

    // Options
    if (bsDatepickerOptions.length) {
        bsDatepickerOptions.datepicker({
            language: 'fa',
            calendarWeeks: true,
            clearBtn: true,
            todayHighlight: true,
            orientation: isRtl ? 'auto left' : 'auto right'
        });
    }

    // Auto close
    if (bsDatepickerAutoclose.length) {
        bsDatepickerAutoclose.datepicker({
            language: 'fa',
            todayHighlight: true,
            autoclose: true,
            orientation: isRtl ? 'auto right' : 'auto left'
        });
    }

    // Inline picker
    if (bsDatepickerInlinedate.length) {
        bsDatepickerInlinedate.datepicker({
            language: 'fa',
            todayHighlight: true
        });
    }

    // Bootstrap Daterange Picker
    // --------------------------------------------------------------------

    var dateRangePickerFa = {
        "format": "YYYY/MM/DD",
        "separator": " - ",
        "applyLabel": "اعمال",
        "cancelLabel": "انصراف",
        "startLabel": 'تاریخ شروع',
        "endLabel": 'تاریخ پایان',
        "fromLabel": "از",
        "toLabel": "تا",
        "weekLabel": "هفته",
        "customRangeLabel": "انتخاب بازه",
        "daysOfWeek": ["ی", "د", "س", "چ", "پ", "ج", "ش"],
        "monthNames": ["ژانویه", "فوریه", "مارس", "آوریل", "می", "ژوئن", "جولای", "اوت", "سپتامبر", "اکتبر", "نوامبر", "دسامبر"],
    };

    var dateRangePickerTimeFa = {
        "format": "YYYY/MM/DD h:mm A",
        "separator": " - ",
        "applyLabel": "اعمال",
        "cancelLabel": "انصراف",
        "startLabel": 'تاریخ شروع',
        "endLabel": 'تاریخ پایان',
        "fromLabel": "از",
        "toLabel": "تا",
        "weekLabel": "هفته",
        "customRangeLabel": "انتخاب بازه",
        "daysOfWeek": ["ی", "د", "س", "چ", "پ", "ج", "ش"],
        "monthNames": ["ژانویه", "فوریه", "مارس", "آوریل", "می", "ژوئن", "جولای", "اوت", "سپتامبر", "اکتبر", "نوامبر", "دسامبر"],
    };

    var bsRangePickerBasic = $('#bs-rangepicker-basic'),
        bsRangePickerSingle = $('#bs-rangepicker-single'),
        bsRangePickerTime = $('#bs-rangepicker-time'),
        bsRangePickerRange = $('#bs-rangepicker-range'),
        bsRangePickerWeekNum = $('#bs-rangepicker-week-num'),
        bsRangePickerDropdown = $('#bs-rangepicker-dropdown'),
        bsRangePickerCancelBtn = document.getElementsByClassName('cancelBtn');

    // Basic
    if (bsRangePickerBasic.length) {
        bsRangePickerBasic.daterangepicker({
            locale: dateRangePickerFa,
            opens: isRtl ? 'left' : 'right'
        });
    }

    // Single
    if (bsRangePickerSingle.length) {
        bsRangePickerSingle.daterangepicker({
            locale: dateRangePickerFa,
            singleDatePicker: true,
            opens: isRtl ? 'left' : 'right'
        });
    }

    // Time & Date
    if (bsRangePickerTime.length) {
        bsRangePickerTime.daterangepicker({
            timePicker: true,
            timePickerIncrement: 30,
            locale: dateRangePickerTimeFa,
            opens: isRtl ? 'left' : 'right'
        });
    }

    if (bsRangePickerRange.length) {
        bsRangePickerRange.daterangepicker({
            locale: dateRangePickerFa,
            ranges: {
                'امروز': [moment(), moment()],
                'دیروز': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '7 روز قبل': [moment().subtract(6, 'days'), moment()],
                '۳۰ روز قبل': [moment().subtract(29, 'days'), moment()],
                'این ماه': [moment().startOf('month'), moment().endOf('month')],
                'ماه قبلی': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            opens: isRtl ? 'left' : 'right'
        });
    }

    // Week Numbers
    if (bsRangePickerWeekNum.length) {
        bsRangePickerWeekNum.daterangepicker({
            locale: dateRangePickerFa,
            showWeekNumbers: true,
            opens: isRtl ? 'left' : 'right'
        });
    }
    // Dropdown
    if (bsRangePickerDropdown.length) {
        bsRangePickerDropdown.daterangepicker({
            locale: dateRangePickerFa,
            showDropdowns: true,
            opens: isRtl ? 'left' : 'right'
        });
    }

    // Adding btn-label-secondary class in cancel btn
    for (var i = 0; i < bsRangePickerCancelBtn.length; i++) {
        bsRangePickerCancelBtn[i].classList.remove('btn-default');
        bsRangePickerCancelBtn[i].classList.add('btn-label-primary');
    }

    // jQuery Timepicker
    // --------------------------------------------------------------------

    var timePickerLocaleFa = {
        am: ' ق.ظ',
        pm: ' ب.ظ',
        AM: ' ق.ظ',
        PM: ' ب.ظ',
        decimal: '.',
        mins: 'دقیقه',
        hr: 'ساعت',
        hrs: 'ساعت'
    };

    var basicTimepicker = $('#timepicker-basic'),
        minMaxTimepicker = $('#timepicker-min-max'),
        disabledTimepicker = $('#timepicker-disabled-times'),
        formatTimepicker = $('#timepicker-format'),
        stepTimepicker = $('#timepicker-step'),
        altHourTimepicker = $('#timepicker-24hours');

    // Basic
    if (basicTimepicker.length) {
        basicTimepicker.timepicker({
            lang: timePickerLocaleFa,
            orientation: isRtl ? 'r' : 'l'
        });
    }

    // Min & Max
    if (minMaxTimepicker.length) {
        minMaxTimepicker.timepicker({
            lang: timePickerLocaleFa,
            minTime: '2:00pm',
            maxTime: '7:00pm',
            showDuration: true,
            orientation: isRtl ? 'r' : 'l'
        });
    }

    // Disabled Picker
    if (disabledTimepicker.length) {
        disabledTimepicker.timepicker({
            lang: timePickerLocaleFa,
            disableTimeRanges: [
                ['12am', '3am'],
                ['4am', '4:30am']
            ],
            orientation: isRtl ? 'r' : 'l'
        });
    }

    // Format Picker
    if (formatTimepicker.length) {
        formatTimepicker.timepicker({
            lang: timePickerLocaleFa,
            timeFormat: 'H:i:s',
            orientation: isRtl ? 'r' : 'l'
        });
    }

    // Steps Picker
    if (stepTimepicker.length) {
        stepTimepicker.timepicker({
            lang: timePickerLocaleFa,
            step: 15,
            orientation: isRtl ? 'r' : 'l'
        });
    }

    // 24 Hours Format
    if (altHourTimepicker.length) {
        altHourTimepicker.timepicker({
            lang: timePickerLocaleFa,
            show: '24:00',
            timeFormat: 'H:i:s',
            orientation: isRtl ? 'r' : 'l'
        });
    }
});

// color picker (pickr)
// --------------------------------------------------------------------

var colorPickerLocaleFa = {
    'ui:dialog': 'دیالوگ انتخاب رنگ',
    'btn:toggle': 'تغییر وضعیت دیالوگ انتخاب رنگ',
    'btn:swatch': 'نمونه رنگ',
    'btn:last-color': 'استفاده از رنگ قبلی',
    'btn:save': 'ذخیره',
    'btn:cancel': 'لغو',
    'btn:clear': 'پاک کردن'
};


(function () {
    const classicPicker = document.querySelector('#color-picker-classic'),
        monolithPicker = document.querySelector('#color-picker-monolith'),
        nanoPicker = document.querySelector('#color-picker-nano');

    // classic
    if (classicPicker) {
        pickr.create({
            el: classicPicker,
            theme: 'classic',
            default: 'rgba(102, 108, 232, 1)',
            swatches: [
                'rgba(102, 108, 232, 1)',
                'rgba(40, 208, 148, 1)',
                'rgba(255, 73, 97, 1)',
                'rgba(255, 145, 73, 1)',
                'rgba(30, 159, 242, 1)'
            ],
            components: {
                // Main components
                preview: true,
                opacity: true,
                hue: true,

                // Input / output Options
                interaction: {
                    hex: true,
                    rgba: true,
                    hsla: true,
                    hsva: true,
                    cmyk: true,
                    input: true,
                    clear: true,
                    save: true
                }
            },
            i18n: colorPickerLocaleFa
        });
    }

    // monolith
    if (monolithPicker) {
        pickr.create({
            el: monolithPicker,
            theme: 'monolith',
            default: 'rgba(40, 208, 148, 1)',
            swatches: [
                'rgba(102, 108, 232, 1)',
                'rgba(40, 208, 148, 1)',
                'rgba(255, 73, 97, 1)',
                'rgba(255, 145, 73, 1)',
                'rgba(30, 159, 242, 1)'
            ],
            components: {
                // Main components
                preview: true,
                opacity: true,
                hue: true,

                // Input / output Options
                interaction: {
                    hex: true,
                    rgba: true,
                    hsla: true,
                    hsva: true,
                    cmyk: true,
                    input: true,
                    clear: true,
                    save: true
                }
            },
            i18n: colorPickerLocaleFa
        });
    }

    // nano
    if (nanoPicker) {
        pickr.create({
            el: nanoPicker,
            theme: 'nano',
            default: 'rgba(255, 73, 97, 1)',
            swatches: [
                'rgba(102, 108, 232, 1)',
                'rgba(40, 208, 148, 1)',
                'rgba(255, 73, 97, 1)',
                'rgba(255, 145, 73, 1)',
                'rgba(30, 159, 242, 1)'
            ],
            components: {
                // Main components
                preview: true,
                opacity: true,
                hue: true,

                // Input / output Options
                interaction: {
                    hex: true,
                    rgba: true,
                    hsla: true,
                    hsva: true,
                    cmyk: true,
                    input: true,
                    clear: true,
                    save: true
                }
            },
            i18n: colorPickerLocaleFa
        });
    }
})();
