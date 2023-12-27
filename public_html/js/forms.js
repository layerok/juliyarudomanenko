window.addEventListener('load', () => {

    // todo: change color of facebook logo, it must not be green

    const useForm = (formSelector, options) => {
        const form = document.querySelector(formSelector);
        const {
            rules, onSuccess = () => {
            }
        } = options;

        if (!form) {
            console.warn(`form [${formSelector}] is not found`)
            return {
                resetForm: () => {
                },
                getFormData: () => ({}),
                form: null
            }
        }

        const {
            validate,
            addListener,
            validateSingle
        } = window.useValidator({
            rules: rules
        })

        addListener((isValid, itemKey, ruleKey) => {
            const el = form.querySelector(`[name='${itemKey}']`)
            if (isValid) {
                el.classList.add("is-valid");
                el.classList.remove("is-invalid");
            } else {
                el.classList.add("is-invalid");
                el.classList.remove("is-valid");
            }
        })

        const resetForm = () => {
            formElements.forEach((el) => {
                el.value = "";
                el.classList.remove("is-invalid");
                el.classList.remove("is-valid");
            })
        }

        const formElements = Object.keys(rules).map((name) => form.querySelector(`[name='${name}']`));


        formElements.forEach(el => {
            el.addEventListener('keyup', (e) => {
                validateSingle(el.getAttribute('name'), el.value)
            })
        })

        const getFormData = () => {
            return formElements.reduce((acc, el) => ({
                [el.getAttribute('name')]: el.value,
                ... acc
            }), {});
        }

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const isValid = validate(getFormData())
            if (isValid) {
                onSuccess(e);
            }
        })

        return {
            resetForm,
            getFormData,
            form
        };
    }

    const initMessageForm = () => {

        const {resetForm} = useForm('#message-form', {
            rules: {
                'phone': ['required', 'phoneUa'],
                'name': ['required'],
            },
            onSuccess: (e) => {
                const form = e.currentTarget;
                const formData = new FormData(e.currentTarget);
                const url = form.getAttribute('action');

                fetch(url, {
                    method: "POST",
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest', // this header helps us detect client-side request
                    }
                }).then(res => res.json()).then((data) => {
                    if (data.errors.length > 0) {
                        return
                    }

                    resetForm();

                    $(".confirmation").show().delay(2000).fadeOut();

                }).catch(() => {
                    alert("Возникла ошибка. Попробуйте еще раз!");
                })
            }
        })

    }

    const initAppointmentForm = () => {
        const {resetForm} = useForm('#appointment-form', {
            rules: {
                'phone': ['required', 'phoneUa'],
                'name': ['required'],
            },
            onSuccess: (e) => {
                const form = e.currentTarget;
                const formData = new FormData(form);
                const url = form.getAttribute('action');

                fetch(url, {
                    method: "POST",
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest', // this header helps us detect client-side request
                    }
                }).then(res => res.json()).then((data) => {
                    if (data.errors.length > 0) {
                        // if (data.errors.hasOwnProperty('recaptcha_failed')) {
                        //$('#recaptchaError').show();
                        //} else {
                        // grecaptcha.reset($('.g-recaptcha'), {
                        //     'callback': function (response) {
                        //         $('#recaptchaError').hide();
                        //     }
                        // });
                        //}
                        return
                    }

                    resetForm()

                    window.location.href = "/appointment/thank-you";

                    //$('#recaptchaError').hide();
                    //grecaptcha.reset();
                }).catch(() => {
                    alert("Возникла ошибка. Попробуйте еще раз!");
                })
            }
        })
    }


    initMessageForm();
    initAppointmentForm();


    $.jMaskGlobals = {
        translation: {
            'n': {pattern: /\d/},
        }
    };

    $('.phone-mask').mask('+38(0nn)-nnn-nnnn').val("+38(0");


})