let hotelFilter =
    {
        dataList: null,
        monthNames: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
        monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        day: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        dayShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        code: null,
        init: function (list, panel, filter, filterRoom) {
            let _self = this;
            _self.list = list;
            _self.panel = panel;
            _self.filter = filter;
            _self.filterRoom = filterRoom;
            _self.dataList = dataList;
            _self.filterRoomList = _self.filter.find("#filter-rooms");

            _self.initEvent(list, panel, filter)

            _self.dataList.init(_self.list, _self.panel, _self.filter)
            _self.dataList.obHotelFilter = this;
            _self.createItems();
            _self.setFilterRoomsDescription();

        },
        initHotel: function (list, panel, filter, filterRoom, code) {
            let _self = this;
            _self.list = list;
            _self.panel = panel;
            _self.filter = filter;
            _self.filterRoom = filterRoom;
            _self.filterRoomList = _self.filter.find("#filter-rooms");
            _self.code = code;

            _self.filter.submit(function () {

                if (!_self.checkForm()) {
                    return false;
                }

                if (_self.list.find('.loader-img').length) {
                    return false;
                }

                _self.sendFormAjax();

                return false;
            })
            _self.initEvent(list, panel, filter)
            _self.createItems();
            _self.setFilterRoomsDescription();
            _self.filter.submit();
        },

        createItem: function (data) {
            let _self = this;
            if (_self.filterRoomList.find('.room').length < 8) {
                _self.filterRoomList.append(_self.getTemplateRoom(data));
            }
            if (_self.filterRoomList.find('.room').length === 8) {
                _self.filter.find('#filter-rooms-add').hide();
            }
            _self.setCountDataRoom();
            let its = _self.filterRoomList.find('.js-item');
            if (its.length > 1) {
                its.find('.js-remove-room').show();
            } else {
                its.find('.js-remove-room').hide();
            }
            _self.filterRoomList.scrollTop(_self.filterRoomList.height());
            if (_self.code) {
                _self.list.html('');
            }
        },

        createItems: function () {
            let _self = this;
            let rooms = _self.filterRoom.data('rooms');

            if (rooms !== undefined) {
                for (let i = 0; i < rooms.length; i++) {
                    _self.createItem(rooms[i]);
                }
            }
            if (_self.filterRoomList.find('.room').length === 0) {
                _self.createItem();
            }
        },

        sendFormAjax: function () {
            let _self = this;

            $('.view-more-block').remove();
            _self.filter.find("[name=_csrf]").remove();

            let url = document.location.pathname + "?" + _self.filter.serialize();
            history.pushState(null, null, url);

            _self.list.addClass('load-progress');

            $.get(url, function (html) {
                _self.list.html(html);
                _self.list.removeClass('load-progress');
                $('.reservation-in-basket').eq(0).trigger('click');
            });
        },

        setFilterRoomsDescription: function () {
            let _self = this;
            let rooms = _self.filter.find('.js-item');
            let adults = 0;
            let children = 0;
            _self.filter.find('[name*="[adult]"]').each(function(){
                adults += $(this).find('option:selected').val() * 1;
            });
            _self.filter.find('[name*="[children]"]').each(function(){
                children += $(this).find('option:selected').val() * 1;
            });
            $('.js-filter-rooms-description').html(
                'Room: ' + rooms.length + ': ' + adults + ' Adults, ' + children + ' Children'
            );
            try {
                filterControl.buildApplied();
            } catch (e) {

            }
        },

        openPopup: function () {
            let _self = this;
            let filter = _self.filterRoom;
            filter.toggle();
        },

        initEvent: function (list, panel, filter) {
            let _self = this;

            _self.filter.find('#filter-rooms-add').click(function () {
                _self.createItem();

                $('#filter-rooms [name*="[children]"]').eq(0).trigger('change');

                return false;
            })


            if ($('#filter-rooms .js-item').length <= 1) {
                $('#filter-rooms .js-item .js-remove-room').hide();
            }

            _self.filter.on('change', 'input[name="s[arrivalDate]"]', function () {
                if (_self.code) {
                    _self.list.html('');
                }
            });
            _self.filter.on('change', 'input[name="s[departureDate]"]', function () {
                if (_self.code) {
                    _self.list.html('');
                }
            });
            _self.filter.on('change', '#filter-rooms [name*="[adult]"]', function () {
                let children = $(this).closest('.js-item').find('[name*="[children]"]');
                children.empty();
                for (let i = 0; i <= 4 - $(this).val() * 1; i++) {
                    children.append($('<option>' + i + '</option>').attr('value', i).text(i));
                }
                children.trigger('change');

                if (_self.code) {
                    _self.list.html('');
                }
            })

            _self.filter.on('click', '#open-detail-room', function () {
                _self.openPopup();
                return false;
            })

            $(document).on('click', '.filter-room', function (e) {
                e.stopPropagation();
            });

            $(document).on('click', 'body', function () {
                $('.filter-room').hide();
            });

            _self.filter.on('change', '#filter-rooms [name*="[children]"]', function () {
                let room = $(this).closest('.js-item').parent().find('.js-item').index($(this).closest('.js-item'));
                let has = $(this).closest('.js-item').find(".js-list-children .js-it").length;

                let adult = $(this).closest('.js-item').find('[name*="[adult]"]')
                let adultVal = adult.find('option:selected').val() * 1

                if ($(this).val() * 1 === 0) {
                    $(this).closest('.js-item').find('.decor').hide()
                } else {
                    $(this).closest('.js-item').find('.decor').show()
                }

                if (adultVal + $(this).val() * 1 > 8) {
                    adult.val(8 - $(this).val() * 1)
                }

                if (has < $(this).val() * 1) {
                    for (let i = has; i < $(this).val(); i++) {
                        $(this).closest('.js-item').find(".js-list-children").append(_self.getTemplateChildren(room, i))
                    }
                }
                if (has > $(this).val() * 1) {
                    for (let i = has; i > $(this).val(); i--) {
                        $(this).closest('.js-item').find(".js-list-children .js-it").eq(i - 1).remove()
                    }
                }
                _self.setFilterRoomsDescription();
                return false;
            })

            _self.filter.on('click', '.js-remove-room', function () {
                let parent = $(this).closest('.js-item').parent();
                let its = parent.find('.js-item');
                if (its.length <= 1) {
                    its.find('.js-remove-room').hide();
                    return false;
                }
                $(this).closest('.js-item').remove();
                its = parent.find('.js-item');
                its.each(function (room) {
                    $(this).find('label.room').html('Room ' + (room + 1))

                    $(this).find('[name*="[children]"]').attr('name', 's[room][' + room + '][children]')
                    $(this).find('[name*="[adult]"]').attr('name', 's[room][' + room + '][adult]')
                    $(this).find('[name*="[age]"]').each(function (i) {
                        $(this).attr('name', 's[room][' + room + '][age][' + i + ']')
                    })
                })
                _self.filter.find('#filter-rooms-add').show()
                if (_self.code) {
                    _self.list.html('');
                }
                _self.setCountDataRoom();
                if (its.length <= 1) {
                    its.find('.js-remove-room').hide();
                }
                $('#filter-rooms [name*="[children]"]').eq(0).trigger('change');
                return false;
            })

            //_self.filter.on('click', '#reset-room', function(){
            $('#reset-room').click(function () {
                $('#filter-rooms .js-item').remove();
                _self.filter.find('#filter-rooms-add').trigger('click');
                _self.setCountDataRoom();
                return false;
            });

            _self.filter.on('change', '[name*="[age]"]', function () {
                if ($(this).find('option:selected').val() === '') {
                    $(this).parent().addClass('has-error');
                } else {
                    $(this).parent().removeClass('has-error');
                }
                if (_self.code) {
                    _self.list.html('');
                }
            })

            _self.filter.on('change', 'select', function () {
                _self.setCountDataRoom();
                if (_self.code) {
                    _self.list.html('');
                }
            })
        },

        checkForm: function () {
            let _self = this;

            let result = true;

            _self.filter.find('[name*="[age]"]').each(function () {
                if ($(this).find('option:selected').val() === '') {
                    result = false;
                    $(this).parent().addClass('has-error')
                } else {
                    $(this).parent().removeClass('has-error')
                }
            })
            if (!result) {
                _self.openPopup()
            }
            return result;
        },
        setCountDataRoom: function () {
            let _self = this;
            $('#count-room').html(_self.filter.find('.room').length)
            let adult = 0;
            let children = 0;

            _self.filter.find('[name*="[adult]"]  option:selected').each(function () {
                adult += $(this).val() * 1
            })
            _self.filter.find('[name*="[children]"]  option:selected').each(function () {
                children += $(this).val() * 1
            })

            $('#count-adult').html(adult)
            $('#count-children').html(children)
        },
        getTemplateChildren: function (room, i, v) {
            room = parseInt(room);
            i = parseInt(i);

            let html = '';
            html += '<div class="col-xs-4 it js-it">';
            html += '<label>Ch. ' + (i + 1) + ' Age</label>';
            html += '<select class="form-control" name="s[room][' + room + '][age][' + i + ']">';
            html += '<option value="">-</option>';
            for (let a = 0; a <= 17; a++) {
                html += '<option value="' + a + '" ' + (a === v ? 'selected="selected"' : '') + '>' + (a === 0 ? '<1' : a) + '</option>';
            }
            html += '</select>';
            html += '</div>';
            return html;
        },
        getTemplateRoom: function (dataRoom) {
            let _self = this;
            let html = '';
            let room = $('#filter-rooms .room').length;
            let adult = dataRoom ? dataRoom['adult'] : 1;

            html += '<div class="item js-item">';
            html += '<div class="row row-middle-padding">';
            html += '<div class="col-xs-12">';
            html += '<a href="#" class="remove js-remove-room" ' + (room === 0 ? 'style="display:none;"' : '') + '><span class="icon ibranson-fontello-2"></span> remove</a>';
            html += '<label class="room">Room ' + (room + 1) + '</label>';
            html += '</div>';
            html += '<div class="col-xs-4">';
            html += '<label>Adults</label>';
            html += '<select class="form-control" name="s[room][' + room + '][adult]">';
            for (let i = 1; i < 5; i++) {
                html += '<option value="' + i + '" ' + (dataRoom !== undefined && dataRoom['adult'] === i ? 'selected="selected"' : '') + '>' + i + '</option>';
            }
            html += '</select>';
            html += '</div>	';
            html += '<div class="col-xs-4">';
            html += '<label>Children</label>';
            html += '<select class="form-control" name="s[room][' + room + '][children]">';
            html += '<option value="0">0</option>';
            for (let i = 1; i < 5 - adult; i++) {
                html += '<option value="' + i + '" ' + (dataRoom !== undefined && dataRoom['children'] === i ? 'selected="selected"' : '') + '>' + i + '</option>';
            }
            html += '</select>';
            html += '</div>';

            html += '</div>	';

            html += '<div class="list-children js-list-children row row-middle-padding">';

            if (dataRoom !== undefined && dataRoom != null && dataRoom['age'] !== undefined && dataRoom['age'] != null) {
                for (let j = 0; j < dataRoom['age'].length; j++) {
                    html += _self.getTemplateChildren(room, j, dataRoom['age'][j]);
                }
            }
            html += '</div>';

            html += '</div>';
            return html;
        },
    }
