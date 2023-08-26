let hotelDetail = {
    init: function () {
        let _self = this;
        $(document).on('click', '.js-reservation-url', function () {
            let roomOb = $(this).closest('.js-room');

            $('.js-room').addClass('room-disabled');
            roomOb.removeClass('room-disabled').addClass('room-opened');

            _self.loadFormReservation(roomOb);
            return false;
        });
        $(document).on('click', '.js-reservation-cancel', function () {
            let roomOb = $(this).closest('.js-room');

            $('.js-room').removeClass('room-disabled').removeClass('room-opened');

            roomOb.find('.js-reservation-form').html('');

            return false;
        });
        $(document).on("click", ".js-input-factor", function () {
            let block = $(this).closest('.with-input-field');
            let input = block.find('input');
            let factor = $(this).data('factor');
            let max = input.attr('max');
            let count = input.val() * 1;
            count += factor;

            if (count <= 0) {
                count = 0;
            }
            if (count >= max) {
                count = max;
            }
            input.val(count);
            input.trigger('change');
            inputFactorControl(input);
        });
        $(document).on('change keyup', '.js-room-extra-count', function () {

            let roomExtraSum = ($(this).attr('price') * $(this).val());
            let roomOb = $(this).closest('.js-room');
            let costPerRoom = roomOb.data('roomPrice');
            let daysCount = roomOb.data('daysCount');
            let roomsCount = roomOb.find('.js-it-room').length;

            $(this).closest('.js-room-extra').find('.js-subtotal-cost').text('$ ' + number_format(roomExtraSum, 2, '.', ''));

            if ($(this).val() * 1 > 0) {
                $(this).closest('.js-room-extra').removeClass('zero');
            } else {
                $(this).closest('.js-room-extra').addClass('zero');
            }

            let total = 0;
            roomOb.find('.js-room-extra-count').each(function () {
                total += $(this).attr('price') * $(this).val();
            });

            total = total + costPerRoom * roomsCount * daysCount;

            roomOb.find('.js-room-total').html(
                '<div>' + roomsCount + ' Rooms</div>' +
                '<span class="cost">$ ' + number_format(total, 2, '.', '') + '</span>'
            );
        });

        $(document).on("submit", "form#bookingHotel", function () {
            let btn = $(this);
            $.post($(this).attr('action'), $(this).serialize(), function(data, textStatus, jqXHR) {
                btn.closest('.js-reservation-form').html(data);
            }).always(function(jqXHR) {
                if (jqXHR.status === 302) {
                    window.location.href = jqXHR.responseText;
                }
            });
            return false;
        });
    },
    loadFormReservation: function (roomOb) {
        let url = roomOb.find('.js-reservation-url').data('url');
        let formReservationContainer = roomOb.find('.js-reservation-form');

        formReservationContainer.html('<div class="load-progress"></div>');

        $.get(url, function (result) {
            formReservationContainer.html(result);
        });
    },
}