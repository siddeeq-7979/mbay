var validate_address_form = function () {
    var form = $('#address_form');
    var errorHandler2 = $('.errorHandler', form);
    var successHandler2 = $('.successHandler', form);
    form.validate({
            errorElement: "div", // contain the error msg in a span tag
            errorClass: 'invalid-feedback',
            errorPlacement: function (error, element) { // render error placement for each input type

                error.appendTo($(element).closest('.form-group'));
            },
            ignore: ':hidden',
            rules: {
                address_1: {
                    required: true,
                },
                city: {
                    required: true,
                },
                country_id: {
                    required: true,
                },
                zip_code: {
                    required: true,
                    digits: true,
                },
                

            },
            messages: {
                address_1: {
                    required: $('#address_1_req_js').html()
                },
                city: {
                    required: $('#city_req_js').html()
                },
                country_id: {
                    required: $('#country_req_js').html()
                },
                zip_code: {
                    required: $('#zip_code_req_js').html()
                },

            },
            invalidHandler: function (event, validator) {
                successHandler2.hide();
                errorHandler2.show();
            },
            highlight: function (element) {
                $(element).closest('.form-control').removeClass('is-invalid');
                //display Checkbox invalid Data
                $(element).closest('.checkbox').removeClass('is-invalid').addClass('is-valid');
                // display OK icon
                $(element).closest('.form-control').removeClass('is-valid').addClass('is-invalid').find('.symbol').removeClass('ok').addClass('required');
                // add the Bootstrap error class to the control group
            },
            unhighlight: function (element) { // revert the change done by hightlight
                //display Checkbox invalid Data

                $(element).closest('.checkbox').removeClass('is-invalid').addClass('is-valid');
                $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid').find('.symbol').removeClass('ok').addClass('required');
                // set error class to the control group
            },
            success: function (label, element) {
                //display Checkbox invalid Data
                $(element).closest('.checkbox').removeClass('is-invalid').addClass('is-valid');
                $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid').find('.symbol').removeClass('ok').addClass('required');
                // mark the current input as valid and display OK icon
            },
            submitHandler: function (form) {
                successHandler2.show();
                errorHandler2.hide();
                form.submit();
                toastr.success('Success')
            }
        });
};

var validate_account_form = function () {
    var form = $('#account_details');
    var errorHandler2 = $('.errorHandler', form);
    var successHandler2 = $('.successHandler', form);
    form.validate({
            errorElement: "div", // contain the error msg in a span tag
            errorClass: 'invalid-feedback',
            errorPlacement: function (error, element) { // render error placement for each input type

                error.insertAfter(element);
            },
            ignore: ':hidden',
            rules: {
                
                first_name: {
                    required: true,
                },
                last_name: {
                    required: true,
                },
                phone_number: {
                    required: true,
                    digits: true,
                },
                

            },
            messages: {
                
                first_name: {
                    required: $('#city_req_js').html()
                },
                last_name: {
                    required: $('#country_req_js').html()
                },
                phone_number: {
                    required: $('#zip_code_req_js').html()
                },

            },
            invalidHandler: function (event, validator) {
                successHandler2.hide();
                errorHandler2.show();
            },
            highlight: function (element) {
                $(element).closest('.form-control').removeClass('is-invalid');
                //display Checkbox invalid Data
                $(element).closest('.checkbox').removeClass('is-invalid').addClass('is-valid');
                // display OK icon
                $(element).closest('.form-control').removeClass('is-valid').addClass('is-invalid').find('.symbol').removeClass('ok').addClass('required');
                // add the Bootstrap error class to the control group
            },
            unhighlight: function (element) { // revert the change done by hightlight
                //display Checkbox invalid Data

                $(element).closest('.checkbox').removeClass('is-invalid').addClass('is-valid');
                $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid').find('.symbol').removeClass('ok').addClass('required');
                // set error class to the control group
            },
            success: function (label, element) {
                //display Checkbox invalid Data
                $(element).closest('.checkbox').removeClass('is-invalid').addClass('is-valid');
                $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid').find('.symbol').removeClass('ok').addClass('required');
                // mark the current input as valid and display OK icon
            },
            submitHandler: function (form) {
                successHandler2.show();
                errorHandler2.hide();
                form.submit();
                toastr.success('Success')
            }
        });
};

$.validator.addMethod("validPass", function () {
    var isSuccess = false;
    $.ajax({url: "shop/check_current_password",
        data: {current_password: $("#current_password").val()},
        async: false,
        success:
        function (msg) {
            isSuccess = msg === "yes" ? true : false
        }
    });
    return isSuccess;
}, "Incorrect Password");

var validate_password_change_form = function () {
    var form = $('#password_details');
    var errorHandler2 = $('.errorHandler', form);
    var successHandler2 = $('.successHandler', form);
    form.validate({
            errorElement: "div", // contain the error msg in a span tag
            errorClass: 'invalid-feedback',
            errorPlacement: function (error, element) { // render error placement for each input type
            error.insertAfter(element);
                        },
            ignore: ':hidden',
            rules: {
                
                current_password: {
                    required: true,
                    validPass: true
                },
                password: {
                    required: true,
                },
                confirm_password: {
                    required: true,
                    
                },
                

            },
            messages: {
                
                current_password: {
                    required: $('#city_req_js').html(),
                    validPass: $('#inv_current_password_js').html()

                },
                password: {
                    required: $('#country_req_js').html()
                },
                confirm_password: {
                    required: $('#zip_code_req_js').html()
                },

            },
            invalidHandler: function (event, validator) {
                successHandler2.hide();
                errorHandler2.show();
            },
            highlight: function (element) {
                $(element).closest('.form-control').removeClass('is-invalid');
                //display Checkbox invalid Data
                $(element).closest('.checkbox').removeClass('is-invalid').addClass('is-valid');
                // display OK icon
                $(element).closest('.form-control').removeClass('is-valid').addClass('is-invalid').find('.symbol').removeClass('ok').addClass('required');
                // add the Bootstrap error class to the control group
            },
            unhighlight: function (element) { // revert the change done by hightlight
                //display Checkbox invalid Data

                $(element).closest('.checkbox').removeClass('is-invalid').addClass('is-valid');
                $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid').find('.symbol').removeClass('ok').addClass('required');
                // set error class to the control group
            },
            success: function (label, element) {
                //display Checkbox invalid Data
                $(element).closest('.checkbox').removeClass('is-invalid').addClass('is-valid');
                $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid').find('.symbol').removeClass('ok').addClass('required');
                // mark the current input as valid and display OK icon
            },
            submitHandler: function (form) {
                successHandler2.show();
                errorHandler2.hide();
                form.submit();
                toastr.success('Success')
            }
        });
};

$(document).ready(function () {
    validate_address_form();
    validate_account_form();
    validate_password_change_form();
});

$('#country_id').on('change', function (e) {
    var optionSelected = $("option:selected", this);
    var valueSelected = this.value;

    $.ajax({url: "register/get_states",
        data: {country_id: valueSelected},
        async: false,
        success: function (msg) {
            $('#state_span').html(msg);
            $("#state").select2({
                placeholder: "Select a State",
                allowClear: false
            });
        }
    });

});