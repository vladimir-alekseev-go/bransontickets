let payment = {
    init: function () {

        $('#paymentformaddcard-same_as_billing').change(function () {
            if ($(this).is(':checked')) {
                $('.is-billing').hide()
            } else {
                $('.is-billing').show()
            }
        });

        $("input[name='PaymentFormAddCard[zip]']").change(function () {
            getFullAddress($(this).val(), function (data) {

                $("input[name='PaymentFormAddCard[state]']").val(data.state)
                $("input[name='PaymentFormAddCard[city]']").val(data.city)
                $("select[name='PaymentFormAddCard[country]'] option").filter(function () {
                    return $(this).text() === data.country;
                }).prop('selected', true);
            })
        });

        let cardNumber = $('input[name*="[card_number]"]');
        if (cardNumber.length > 0) {
            cardNumber.each(function(){
                $(this).payform('formatCardNumber');
            });
        }
        let expiryDate = $('input[name*="[expiry_date]"]');
        if (expiryDate.length > 0) {
            expiryDate.each(function(){
                $(this).payform('formatCardExpiry');
            });
        }

        let cvvCode = $('input[name*="[cvv_code]"]');
        if (cvvCode.length > 0) {
            cvvCode.each(function () {
                $(this).payform('formatCardCVC');
            });
        }
        /*
        //cardFromNumber
        $('#paymentformaddcard-cvv_code').payment('formatCardCVC');
        $('#paymentform-cvv_code').payment('formatCardCVC');

        $('#paymentformaddcard-card_number').validateCreditCard(function (result) {
            var ob = $('#paymentformaddcard-card_number');
            var datatype = ob.parent().attr("data-type")
            ob.parent().removeClass(datatype)
            if (result.card_type) {
                ob.parent().attr("data-type", "card-type-" + result.card_type.name)
                ob.parent().addClass("card-type-" + result.card_type.name)

                $('#paymentform-cvv_code').attr("maxlength", result.card_type.name === "amex" ? 4 : 3)
            } else {
                $('#paymentformaddcard-cvv_code').attr("maxlength", 4)
            }
        })

        $('#paymentformaddcard-card_number').payment('formatCardNumber');

        $('#paymentformaddcard-expiry_date').keyup(function (e) {
            if (this.value.length > 2 && this.value.charAt(2) !== '/') {
                this.value = this.value.slice(0, 2) + '/' + this.value.slice(2, 4);
            }
        });*/
    },
    billingHide: function () {
        $('.is-billing').hide()
    }
}