var citiesModule = (function ($, document) {
    'use strict';

    var app = {
        init: function () {
            this.registerPreloader();
            this.registerRemoveDataEvent();

            this.reinit();
        },
        reinit: function () {
            this.registerShowResetPasswordFormEvent();
            this.registerToggleAddCityFormEvent();
            this.registerShowCityListFormEvent();

            // регистрация обработчиков модальных форм
            this.registerBasicFormSubmit('#login-form, #request-password-reset-form',
                function (form, data) {
                    if (data !== 'success') {
                        form.yiiActiveForm('updateMessages', data, true);
                    }
                });
            this.registerBasicFormSubmit('#signup-form', function (form, data) {
                if (data !== 'success') {
                    form.yiiActiveForm('updateMessages', data, true);
                    // обновление каптчи
                    $('#signupform-verifycode-image').trigger('click');
                }
            });
            this.registerBasicFormSubmit('#city-form', function (form, data) {
                if (data !== 'success') {
                    form.yiiActiveForm('updateMessages', data, true);
                } else {
                    if ($('#cities')) {
                        $.pjax.reload({container: '#cities'});
                    }
                    $('#modal-form').modal('hide');
                }
            });
            this.registerBasicFormSubmit('#add-city-form', function (form, data) {
                if (!data.status) {
                    form.yiiActiveForm('updateMessages', data, true);
                } else if (data.status === 'success') {
                    // добавление в список городов + добавление в выбранные значения
                    var $idsCity = $('#review-ids_city'),
                        $newCity = $('<option value="' + data.id + '">' +
                            data.name + '</option>'),
                        cities = $idsCity.val() || [];
                    $idsCity.append($newCity);
                    cities.push(data.id);
                    $idsCity.val(cities).trigger('change');

                    $('.review-form, .add-city-form').toggle();
                } else {
                    console.log('fail', data);
                }
            });
            this.registerReviewFormSubmit();
        },

        registerPreloader: function () {
            $(document).ajaxSend(function () {
                $('#preloader').show();
            });
            $(document).ajaxComplete(function () {
                $('#preloader').hide();
            });
        },
        registerRemoveDataEvent: function () {
            $('#modal-form').on('hidden.bs.modal', function (e) {
                $(this).removeData();
            });
        },
        registerShowResetPasswordFormEvent: function () {
            $('#show-reset-password-form').click(function () {
                $('.site-login, .site-request-password-reset').toggle();
            })
        },
        registerToggleAddCityFormEvent: function () {
            $('.toggle-add-city-form').click(function (e) {
                $('.review-form, .add-city-form').toggle();
            });
        },
        registerShowCityListFormEvent: function () {
            $('#show-city-list-form').click(function () {
                $('.city-by-ip-form, .city-list-form').toggle();
            });
        },

        registerBasicFormSubmit: function (formSelector, doneCallback) {
            $(formSelector).on('beforeSubmit', function (e) {
                var $form = $(this);
                $.post(
                    $form.attr('action'),
                    $form.serialize()
                )
                    .done(function (data) {
                        doneCallback($form, data);
                    })
                    .fail(function (data) {
                        console.log('fail', data);
                    });
                return false;
            })
        },

        registerReviewFormSubmit: function () {
            $('#review-form').on('beforeSubmit', function () {
                var $form = $(this);
                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        if (data !== 'success') {
                            $form.yiiActiveForm('updateMessages', data, true);
                        } else {
                            if ($('#refreshReviews').length) {
                                $.pjax.reload({container: '#refreshReviews'});
                            }
                            if ($('#refreshReview').length) {
                                $.pjax.reload({container: '#refreshReview'});
                            }
                            $('#modal-form').modal('hide');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log('fail', error);
                    }
                });
                return false;
            });
        },

        // выбор города
        chooseCity: function () {
            $('#modal-form').modal({
                show: true,
                backdrop: 'static',
                keyboard: false,
                remote: '/review/choose-city'
            });
        }
    }

    return {
        init: function () {
            app.init();
        },
        reinit: function () {
            app.reinit();
        },
        chooseCity: function () {
            app.chooseCity();
        }
    };
})(jQuery, document);

citiesModule.init();
