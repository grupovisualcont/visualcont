/*
 * 
 * @version 1.0.0
 */

const objMaster = {};

$(function() {

    objMaster.printErrors = function(errors) {
        if (Array.isArray(errors)) {
            errors.forEach(function(obj, pos) {
                const id = obj.form_id;
                const message = obj.message;
                const el = $(document.getElementById(id));
                if (el.length > 0) {
                    let content = $(el.parent());
                    if (content.length > 0) {
                        content.addClass('was-validated');
                        content.addClass('is-invalid');
                        let elFeedback = $('#' + id + 'Feedback');
                        if (elFeedback.length > 0) {
                            elFeedback.html(message);
                        } else {
                            content.append('<div class="invalid-feedback" id="' + id + 'Feedback" >' + message + '</div>');
                        }
                        el[0].setCustomValidity(message);
                    }
                }
            });
        }
    };

    objMaster.clearErrors = function() {
        $('.is-invalid').removeClass('is-invalid');
        $('.was-validated').removeClass('was-validated');
    };

    objMaster.alert = function() {
        
    };

});

$(document).ready(function() {
    
    jQuery(".mydatepicker").datepicker({
        format: "dd/mm/yyyy",
        'language' : 'es',
        autoclose: true
    });

});