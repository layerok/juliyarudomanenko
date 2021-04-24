$( document ).ready(function() {

    let form = $('#appointment-form');
    let phoneInput = $("[name='phone']");
    let nameInput = $("[name='name']");

     /* Мне это не нравиться и уже 3 часа ночи поэтому сделаю так, потом переделаю  */
    let isPhoneValid = false;
    let isNameValid = false;

    function setInvalid(obj){
        obj.addClass("is-invalid");
        obj.removeClass("is-valid");
    }
    function setValid(obj){
        obj.addClass("is-valid");
        obj.removeClass("is-invalid");
    }
    function setUndefined(arr){
        arr.forEach(function(element){
            element.removeClass("is-invalid");
            element.removeClass("is-valid");
        })
    }

    function clearValue(arr){
        arr.forEach(function(element){
            element.val("");
        })
        
        
    }

    nameInput.on("keyup",function(){

        if($(this).val() == ""){
            setInvalid($(this));
            isNameValid = false;
        }else{
            setValid($(this));
            isNameValid = true;
        }

    });

    phoneInput.on("keyup",function(){

        let val = $(this).val();
        let phone = /^\+?3?8?\(?0\d{2}\)?\-?\d{3}\-?\d{2}\d{2}$/;

        if(!phone.test(val)){
            setInvalid($(this));
            isPhoneValid = false;
        }else{
            setValid($(this));
            isPhoneValid = true;
        }

    });

    
    form.submit(function(e){

         e.preventDefault();

         if(isPhoneValid && isNameValid){
            let form = $(this);        
            let formData = new FormData(form[0]);

            $.ajax({
                type: 'POST',
                url: form.attr("action"),
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(data){
                    if(data.errors.length == 0){
                        $('#recaptchaError').hide();
                        setUndefined([phoneInput,nameInput]);
                        clearValue([phoneInput,nameInput]);
                        grecaptcha.reset();
                        isPhoneValid = false;
                        isNameValid = false;
                        window.location.href = "/appointment/thank-you";
                    }else{
                        if(data.errors.hasOwnProperty('recaptcha_failed')){
                            $('#recaptchaError').show();
                        }
                        grecaptcha.reset($('.g-recaptcha'),{
                            'callback': function(response){
                                $('#recaptchaError').hide();
                            }
                        });
                    }
                    
                },
                error: function(data){
                    alert("Возникла ошибка. Попробуйте еще раз!");
                }
            });
        }else{
            if(!isPhoneValid){
                setInvalid(phoneInput);
            }
            if(!isNameValid){
                setInvalid(nameInput);
            }
            
        } 
        
       
    });


});