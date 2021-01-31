/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

window.FrontendBookApi = window.FrontendBookApi || {};

/**
 * Frontend Book API
 *
 * This module serves as the API consumer for the booking wizard of the app.
 *
 * @module FrontendBookApi
 */
(function (exports) {

    'use strict';

    var unavailableDatesBackup;
    var selectedDateStringBackup;
    var processingUnavailabilities = false;

    /**
     * Get Available Hours
     *
     * This function makes an AJAX call and returns the available hours for the selected service,
     * provider and date.
     *
     * @param {String} selDate The selected date of which the available hours we need to receive.
     */
    exports.getAvailableHours = function (selDate) {
        $('#available-hours').empty();

        // Find the selected service duration (it is going to be send within the "postData" object).
        var selServiceDuration = 30; // Default value of duration (in minutes).
        $.each(GlobalVariables.availableServices, function (index, service) {
            if (service.id == $('#select-service').val()) {
                selServiceDuration = service.duration;
            }
        });

        // If the manage mode is true then the appointment's start date should return as available too.
        var appointmentId = FrontendBook.manageMode ? GlobalVariables.appointmentData.id : undefined;

        // Make ajax post request and get the available hours.
        var postUrl = GlobalVariables.baseUrl + '/index.php/appointments/ajax_get_available_hours';
        var postData = {
            csrfToken: GlobalVariables.csrfToken,
            service_id: $('#select-service').val(),
            provider_id: $('#select-provider').val(),
            selected_date: selDate,
            service_duration: selServiceDuration,
            manage_mode: FrontendBook.manageMode,
            appointment_id: appointmentId
        };

        $.post(postUrl, postData, function (response) {
            if (!GeneralFunctions.handleAjaxExceptions(response)) {
                return;
            }

            // The response contains the available hours for the selected provider and
            // service. Fill the available hours div with response data.
            if (response.length > 0) {
                var currColumn = 1;
                $('#available-hours').html('<div style="width:80px; float:left;"></div>');

                var timeFormat = GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : 'HH:mm';

                $.each(response, function (index, availableHour) {
                    if ((currColumn * 10) < (index + 1)) {
                        currColumn++;
                        $('#available-hours').append('<div style="width:80px; float:left;"></div>');
                    }

                    $('#available-hours div:eq(' + (currColumn - 1) + ')').append(
                        '<span class="available-hour">' + Date.parse(availableHour).toString(timeFormat) + '</span><br/>');
                });

                if (FrontendBook.manageMode) {
                    // Set the appointment's start time as the default selection.
                    $('.available-hour').removeClass('selected-hour');
                    $('.available-hour').filter(function () {
                        return $(this).text() === Date.parseExact(
                            GlobalVariables.appointmentData.start_datetime,
                            'yyyy-MM-dd HH:mm:ss').toString(timeFormat);
                    }).addClass('selected-hour');
                } else {
                    // Set the first available hour as the default selection.
                    $('.available-hour:eq(0)').addClass('selected-hour');
                }

                FrontendBook.updateConfirmFrame();

            } else {
                $('#available-hours').text(EALang.no_available_hours);
            }
        }, 'json').fail(GeneralFunctions.ajaxFailureHandler);
    };


    exports.updateHours = function (email, saldo) {
        
        var bancoHoras = saldo;  
        var customer_id ;
        var first;
        var last;
        var var_email;
        var phone=0;

        var getHoursUrl= GlobalVariables.baseUrl + '/index.php/appointments/ajax_get_hours';
        
        var postData = {
            csrfToken: GlobalVariables.csrfToken,
            email: email
        };
       
       
        $.ajax({
            url: getHoursUrl,
            type: 'POST',
            data: postData,
            dataType: 'json',
            async: false,
            success: function(data) {
                customer_id = data.id;
                first=data.first_name;
                last=data.last_name;
                phone=data.phone_number;
                var_email=data.email;
                
             }    
        });
        

        var customer = {
            first_name: first,
            last_name: last,
            email: var_email,
            phone_number: phone,
            hours: bancoHoras
        };
      
    
  
    if (customer_id != '') {
        customer.id = customer_id;
    }
    
    
    var postUrl = GlobalVariables.baseUrl + '/index.php/appointments/save_customer';
    var postData = {
        csrfToken: GlobalVariables.csrfToken,
        customer: JSON.stringify(customer)
    };
    
      $.ajax({
        url: postUrl,
        type: 'POST',
        data: postData,
        dataType: 'text',
        async: false,
        success: function(data) {

        },
        error: function(data){
            
        }   
    });

     
}

    /**
     * Register an appointment to the database.
     *
     * This method will make an ajax call to the appointments controller that will register
     * the appointment to the database.
     */
    exports.registerAppointment = function () {
        //alert("Registrando Dados");
        var $captchaText = $('.captcha-text');

        if ($captchaText.length > 0) {
            $captchaText.closest('.form-group').removeClass('has-error');
            if ($captchaText.val() === '') {
                $captchaText.closest('.form-group').addClass('has-error');
                return;
            }
        }

        var formData = jQuery.parseJSON($('input[name="post_data"]').val());

        var postData = {
            csrfToken: GlobalVariables.csrfToken,
            post_data: formData
        };

        if ($captchaText.length > 0) {
            postData.captcha = $captchaText.val();
        }

        if (GlobalVariables.manageMode) {
            postData.exclude_appointment_id = GlobalVariables.appointmentData.id;
        } 
    
        var postUrl = GlobalVariables.baseUrl + '/index.php/appointments/ajax_register_appointment';
        var $layer = $('<div/>');

        $.ajax({
            url: postUrl,
            method: 'post',
            data: postData,
            dataType: 'json',
            beforeSend: function (jqxhr, settings) {
                $layer
                    .appendTo('body')
                    .css({
                        background: 'white',
                        position: 'fixed',
                        top: '0',
                        left: '0',
                        height: '100vh',
                        width: '100vw',
                        opacity: '0.5'
                    });
            }
        })
            .done(function (response) {
                if (!GeneralFunctions.handleAjaxExceptions(response)) {
                    $('.captcha-title small').trigger('click');
                    return false;
                }

                if (response.captcha_verification === false) {
                    $('#captcha-hint')
                        .text(EALang.captcha_is_wrong)
                        .fadeTo(400, 1);

                    setTimeout(function () {
                        $('#captcha-hint').fadeTo(400, 0);
                    }, 3000);

                    $('.captcha-title small').trigger('click');

                    $captchaText.closest('.form-group').addClass('has-error');

                    return false;
                }
                
                //--- >> Código para pagamento via pagseguro ou banco de dados

                var bancoHoras;
                var saldoHoras;
                var qtdhoras=GlobalVariables.appointmentData.id_services;
                var horasService=$('#select-service').val();
                var email = $('#email').val();
                        
                bancoHoras=FrontendBookApi.getHoursBank();
    
                if (FrontendBook.manageMode) {
                   
                    saldoHoras = Number(bancoHoras) + Number(qtdhoras) - Number(horasService);
                //saldoHoras = qtdhoras;
    
                }else{
    
                    saldoHoras = Number(bancoHoras) - Number(horasService);
    
                }
                
                if(Number(saldoHoras) < 0 ){
    
                    var start_datetime = $('#select-date').datepicker('getDate').toString('yyyy-MM-dd')
                    + ' ' + Date.parse($('.selected-hour').text()).toString('HH:mm') + ':00';
                    var end_datetime = _calcEndDatetime();

                    saldoHoras=Number(saldoHoras)*(-1);

                    // var buttons = [
                    //     {
                    //         text: 'OK',
                    //         click: function () {
                                
                    //             FrontendBookApi.pagSeguro(email, saldoHoras, response.appointment_id, start_datetime, end_datetime);   //Chama função do Pagseguro enviando email, saldo de horas e timestamp ID                           
                    //             $('#message_box').dialog('close');
                    //         } 
                    //     },
                    //     {
                    //         text: EALang.cancel,
                    //         click: function () {
                    //             FrontendBookApi.removeAppointment(response.appointment_id);
                    //             $('#message_box').dialog('close');
                    //         }
                    //     }
                    // ];

                    var buttons = [
                        {
                            text: 'OK',
                            click: function () {
                                
                                FrontendBookApi.removeAppointment(response.appointment_id);
                                $('#message_box').dialog('close');
                            } 
                        }
                    ];
    
                    // var message='Você será redirecionado para a página do Pagseguro para efetuar o pagamento. \n ' 
                    //             + 'Caso não abra, desbloqueie o Pop-up para abrir a página do PagSeguro.';

                    var message='Se você não possui banco de horas entre em contato no Whatsapp 11-94039-7038 para efetivar seu agendamento';

                    GeneralFunctions.displayMessageBox('Agendamento não realizado', message, buttons);
                    // GeneralFunctions.displayMessageBox('Pagamento Online', message, buttons);
                    return;
                }else{
                    FrontendBookApi.updateHours(email, saldoHoras); //Atualiza banco de horas
                    window.location.href = GlobalVariables.baseUrl
                        + '/index.php/appointments/book_success/' + response.appointment_id; //Mostra mensagem de sucesso do agendamnto
                }




            })
            .fail(function (jqxhr, textStatus, errorThrown) {
                $('.captcha-title small').trigger('click');
                GeneralFunctions.ajaxFailureHandler(jqxhr, textStatus, errorThrown);
            })
            .always(function () {
                $layer.remove();
            });
    };

       /**
     * This method calculates the end datetime of the current appointment.
     * End datetime is depending on the service and start datetime fields.
     *
     * @return {String} Returns the end datetime in string format.
     */
    function _calcEndDatetime() {
        // Find selected service duration.
        var selServiceDuration = undefined;

        $.each(GlobalVariables.availableServices, function (index, service) {
            if (service.id == $('#select-service').val()) {
                selServiceDuration = service.duration;
                return false; // Stop searching ...
            }
        });

        // Add the duration to the start datetime.
        var startDatetime = $('#select-date').datepicker('getDate').toString('dd-MM-yyyy')
            + ' ' + Date.parse($('.selected-hour').text()).toString('HH:mm');
        startDatetime = Date.parseExact(startDatetime, 'dd-MM-yyyy HH:mm');
        var endDatetime = undefined;

        if (selServiceDuration !== undefined && startDatetime !== null) {
            endDatetime = startDatetime.add({'minutes': parseInt(selServiceDuration)});
        } else {
            endDatetime = new Date();
        }

        return endDatetime.toString('yyyy-MM-dd HH:mm:ss');
    }


    /**
     * Get the unavailable dates of a provider.
     *
     * This method will fetch the unavailable dates of the selected provider and service and then it will
     * select the first available date (if any). It uses the "FrontendBookApi.getAvailableHours" method to
     * fetch the appointment* hours of the selected date.
     *
     * @param {Number} providerId The selected provider ID.
     * @param {Number} serviceId The selected service ID.
     * @param {String} selectedDateString Y-m-d value of the selected date.
     */
    exports.getUnavailableDates = function (providerId, serviceId, selectedDateString) {
        if (processingUnavailabilities) {
            return;
        }

        var appointmentId = FrontendBook.manageMode ? GlobalVariables.appointmentData.id : undefined;

        var url = GlobalVariables.baseUrl + '/index.php/appointments/ajax_get_unavailable_dates';
        var data = {
            provider_id: providerId,
            service_id: serviceId,
            selected_date: encodeURIComponent(selectedDateString),
            csrfToken: GlobalVariables.csrfToken,
            manage_mode: FrontendBook.manageMode,
            appointment_id: appointmentId
        };

        $.ajax({
            url: url,
            type: 'GET',
            data: data,
            dataType: 'json'
        })
            .done(function (response) {
                unavailableDatesBackup = response;
                selectedDateStringBackup = selectedDateString;
                _applyUnavailableDates(response, selectedDateString, true);
            })
            .fail(GeneralFunctions.ajaxFailureHandler);
    };

    exports.applyPreviousUnavailableDates = function () {
        _applyUnavailableDates(unavailableDatesBackup, selectedDateStringBackup);
    };

    function _applyUnavailableDates(unavailableDates, selectedDateString, setDate) {
        setDate = setDate || false;

        processingUnavailabilities = true;

        // Select first enabled date.
        var selectedDate = Date.parse(selectedDateString);
        var numberOfDays = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + 1, 0).getDate();

        if (setDate && !GlobalVariables.manageMode) {
            for (var i = 1; i <= numberOfDays; i++) {
                var currentDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), i);
                if (unavailableDates.indexOf(currentDate.toString('yyyy-MM-dd')) === -1) {
                    $('#select-date').datepicker('setDate', currentDate);
                    FrontendBookApi.getAvailableHours(currentDate.toString('yyyy-MM-dd'));
                    break;
                }
            }
        }

        // If all the days are unavailable then hide the appointments hours.
        if (unavailableDates.length === numberOfDays) {
            $('#available-hours').text(EALang.no_available_hours);
        }

        // Grey out unavailable dates.
        $('#select-date .ui-datepicker-calendar td:not(.ui-datepicker-other-month)').each(function (index, td) {
            selectedDate.set({day: index + 1});
            if ($.inArray(selectedDate.toString('yyyy-MM-dd'), unavailableDates) != -1) {
                $(td).addClass('ui-datepicker-unselectable ui-state-disabled');
            }
        });

        processingUnavailabilities = false;
    }

    /**
     * Save the user's consent.
     *
     * @param {Object} consent Contains user's consents.
     */
    exports.saveConsent = function (consent) {
        var url = GlobalVariables.baseUrl + '/index.php/consents/ajax_save_consent';
        var data = {
            csrfToken: GlobalVariables.csrfToken,
            consent: consent
        };

        $.post(url, data, function (response) {
            if (!GeneralFunctions.handleAjaxExceptions(response)) {
                return;
            }
        }, 'json').fail(GeneralFunctions.ajaxFailureHandler);
    };


    exports.getHoursBank = function (response) {
        
        var bancoHoras=0;
        var phpUrl;
        var email = $('#email').val();
       
        phpUrl= GlobalVariables.baseUrl + '/index.php/appointments/ajax_get_hours';
       
        var postData = {
            csrfToken: GlobalVariables.csrfToken,
            email: email
        };
       
        $.ajax({
            url: phpUrl,
            type: 'POST',
            data: postData,
            dataType: 'json',
            async: false,
            success: function(data) {
                bancoHoras = data.hours;
            }    
        });
        
        if(bancoHoras == null){
            bancoHoras=0;
  
        }
       
        return bancoHoras;
    };

    exports.getCustomerData = function (response) {

        var bancoHoras;
        var customer_id = '';
        var first_name='';
        var last_name='';
        var email='';
        var phone_number='';
        var address = '';
        var city = '';
        var zip_code ='';
        var phpUrl;
        var email = $('#email').val();
        
        phpUrl= GlobalVariables.baseUrl + '/index.php/appointments/ajax_get_customer';
       
        var postData = {
            csrfToken: GlobalVariables.csrfToken,
            email: email
        };
       
        $.ajax({
            url: phpUrl,
            type: 'POST',
            data: postData,
            dataType: 'json',
            async: false,
            success: function(data) {
                
                customer_id = data.id;
                first_name=data.first_name;
                last_name=data.last_name;
                email=data.email;
                phone_number=data.phone_number;
                address = data.address;
                city = data.city;
                zip_code = data.zip_code;
                bancoHoras= data.hours;
                
            }    
        });
        
        if(customer_id !=0){

        $('input[id="first-name"]').css("background-color","#DCDCDC");
        
        $('input[id="last-name"]').css("background-color","#DCDCDC");
        
        $('input[id="phone-number"]').css("background-color","#DCDCDC");

        $('input[id="address"]').css("background-color","#DCDCDC");
    
        $('input[id="city"]').css("background-color","#DCDCDC");
    
        $('input[id="zip-code"]').css("background-color","#DCDCDC");

        $('input[id="first-name"]').val(first_name);
        $('input[id="last-name"]').val(last_name);
        $('input[id="phone-number"]').val(phone_number);
        $('input[id="address"]').val(address);
        $('input[id="city"]').val(city);
        $('input[id="zip-code"]').val(zip_code);
        $("#homeFieldset").prop('disabled', true);
        $("#homeFieldset2").prop('disabled', true);

        }else{

            $('input[id="first-name"]').val('');
            $('input[id="first-name"]').css("background-color","white");
            $('input[id="last-name"]').val('');
            $('input[id="last-name"]').css("background-color","white");
            $('input[id="phone-number"]').val('');
            $('input[id="phone-number"]').css("background-color","white");
            $('input[id="address"]').val('');
            $('input[id="address"]').css("background-color","white");
            $('input[id="city"]').val('');
            $('input[id="city"]').css("background-color","white");
            $('input[id="zip-code"]').val('');
            $('input[id="zip-code"]').css("background-color","white");
            $("#homeFieldset").prop('disabled', false);
            $("#homeFieldset2").prop('disabled', false);
            $('input[id="first-name"]').focus();
        }
        
    };


    /**
     * Delete personal information.
     *
     * @param {Number} customerToken Customer unique token.
     */
    exports.deletePersonalInformation = function (customerToken) {
        var url = GlobalVariables.baseUrl + '/index.php/privacy/ajax_delete_personal_information';
        var data = {
            csrfToken: GlobalVariables.csrfToken,
            customer_token: customerToken
        };

        $.post(url, data, function (response) {
            if (!GeneralFunctions.handleAjaxExceptions(response)) {
                return;
            }

            location.href = GlobalVariables.baseUrl;
        }, 'json').fail(GeneralFunctions.ajaxFailureHandler);
    };

    exports.pagSeguro = function (email, horas, appointmentId, start_datetime, end_datetime){
		
        var pagSegApiUrl; 
              
        pagSegApiUrl= GlobalVariables.baseUrl + '/index.php/appointments/pagSeguroApi';
        var postData = {
            csrfToken: GlobalVariables.csrfToken,
            hour: horas,
            email: email,
            appointmentId: appointmentId,
            start_datetime: start_datetime,
            end_datetime: end_datetime
        };     
        
        $.ajax({
            url: pagSegApiUrl,
            type: 'POST',
            data: postData,
            dataType: 'text',
            async: false,
            success: function(data) {   
                
                // window.location.href = GlobalVariables.baseUrl
                // + '/index.php/appointments/book_success/' + appointmentId;
                        
                window.open("https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html?code=" + data, "_self"); //Sandbox - Chama página do pagseguro com os dados da compra


                //window.open("https://pagseguro.uol.com.br/v2/checkout/payment.html?code=" + data, "_self"); //Production - Chama página do pagseguro com os dados da compra
    
            } 

        });

    }

    exports.removeAppointment = function (appointmentId){

        var removeAppointmentUrl= GlobalVariables.baseUrl + '/index.php/appointments/removeAppointmentApi';
        var postData = {
            csrfToken: GlobalVariables.csrfToken,
            appointmentId: appointmentId
        };     

        $.ajax({
            url: removeAppointmentUrl,
            type: 'POST',
            data: postData,
            dataType: 'text',
            async: false,
            success: function(data) {   
                
            },
            error: function(data){
                
            }   

        });
    }

})(window.FrontendBookApi);
