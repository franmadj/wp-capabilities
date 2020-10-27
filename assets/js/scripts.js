(function ($) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     */
    $(function () {
        $('.nav-tab').click(function () {
            console.log('f');
            if ($(this).hasClass('policies-tab')) {
                window.location = "/wp-admin/tools.php?page=" + script_data.plugin_page_hook + "&tab=policies";
                return;
            }
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('.in-tabs').removeClass('active');
            $('#' + $(this).data('tab')).addClass('active');
        });


        $('#cpt-custom-cap').change(function () {
            if ($(this).is(':checked')) {
                $('.capability-type').show();
            } else {
                $('.capability-type').hide();
            }
        });

        $('#cpt-name').blur(function () {
            let value = $(this).val();
            if (value.length && !$('#cpt-slug').val().length) {
                $('#cpt-slug').val(slugify(value))
            }
        });

        $('.cpt-edit').click(function (e) {
            e.preventDefault();
            let data = $(this).data('data');
            $('#cpt-name').val(data.name);
            $('#cpt-slug').val(data.slug);
            $('#cpt-description').text(data.description);
            $('#cpt-position').val(data.position);
            $('#cpt-name').val(data.name);
            if (data.has_archive)
                $('#cpt-has-archive').attr('checked', true);
            if (data.active)
                $('#cpt-active').attr('checked', true);

            if (data.custom_cap) {
                $('#cpt-custom-cap').attr('checked', true);
                $('.capability-type').show();
                $('#cpt-cap').val(data.cap[0] + '|' + data.cap[1]);
            }
            if (data.supports.length) {
                data.supports.forEach(function (el, i) {
                    $('#cpt-sp-' + el).attr('checked', true);
                });
            }
            $('.save-mt-cpt').hide();
            $('.update-mt-cpt').show().val(data.id);
            $('.cancel-update').show();
            $('html, body').animate({scrollTop: 0}, 800);
        });

        $('.cancel-update').click(function () {
            $('#cpt-form .inputs-value').val('');
            $('#cpt-form input[type="checkbox"]').attr('checked', false);
            $('.save-mt-cpt').show();
            $('.update-mt-cpt,.cancel-update').hide();
            $('html, body').animate({scrollTop: 0}, 800);
        });


        $('.role-edit').click(function (e) {
            e.preventDefault();
            let data = $(this).data('data');
            let slug = $(this).data('slug');
            console.log($(this).data('data'));
            $('#role-name').val(data.name);
            $('#role-slug').val(slug).attr('readonly', true);
            $('.save-mt-role').hide();
            $('.update-mt-role').show().val(slug);
            $('.cancel-update-role').show();
            $('html, body').animate({scrollTop: 0}, 800);
        });

        $('.role-cap-edit').click(function (e) {
            e.preventDefault();
            $('.caps-tab').click();
            $('#cap-roles').val($(this).attr('href')).change();
        });


        $('.cancel-update-role').click(function () {
            $('#role-form .inputs-value').val('');
            $('.save-mt-role').show();
            $('.update-mt-role,.cancel-update-role').hide();
            $('html, body').animate({scrollTop: 0}, 800);
        });


        $('#role-name').blur(function () {
            let value = $(this).val();
            if (value.length && !$('#role-slug').val().length) {
                $('#role-slug').val(slugify(value))
            }
        });


        var users = $('#cap-users').data('users');
        $('#cap-roles').change(function () {
            let role = $(this).val();
            currentCapEntity = {"entity": "role", "id": role};
            $('#cap-users').empty().append($('<option></option>').val('').text("Select User"));
            if (users[role]) {
                users[role].forEach(function (el) {
                    $('#cap-users').append($('<option></option>').val(el.id).text(el.name));
                })
            }
            $('.capabilities').attr('checked', false);
            jQuery.ajax({
                type: "post",
                url: script_data.url,
                data: "action=get_caps_by_role&role=" + $(this).val() + "&nonce=" + script_data.nonce,
                dataType: 'json',
                success: function (caps) {
                    setCapabilities(caps)
                }
            });
        });

        $('#cap-users').change(function () {
            let user = $(this).val();
            currentCapEntity = {"entity": "user", "id": user};
            $('.capabilities').attr('checked', false);
            jQuery.ajax({
                type: "post",
                url: script_data.url,
                data: "action=get_caps_by_user&user=" + user + "&nonce=" + script_data.nonce,
                dataType: 'json',
                success: function (caps) {
                    setCapabilities(caps)
                }
            });

        });

        function setCapabilities(caps) {
            capabilitiesEvent = false;
            Object.keys(caps).forEach(key => {
                if (caps[key]) {
                    $('#' + key).click();
                }
            });
            capabilitiesEvent = true;
        }

        var capabilitiesEvent = true;
        var currentCapEntity = {"entity": "role", "id": "administrator"};
        $('.capabilities').click(function (e) {
            if (!capabilitiesEvent)
                return true;
            if ('administrator' == currentCapEntity.id) {
                e.stopPropagation();
                e.preventDefault();
                return false;
            }
            console.log(e.currentTarget);
            var cap = $(this).val();
            var active = $(this).is(':checked') ? 1 : 0;

            jQuery.ajax({
                type: "post",
                url: script_data.url,
                data: "action=set_caps&entity=" + currentCapEntity.entity + "&id=" + currentCapEntity.id + "&cap=" + cap + "&active=" + active + "&nonce=" + script_data.nonce,

                success: function (res) {

                }
            });

        });


        var dialog = $("#dialog-form").dialog({
            autoOpen: false,

            modal: true,
            buttons: {
                "Save": addCap,
                Cancel: function () {
                    dialog.dialog("close");
                }
            },
            close: function () {
                $('#cap-name').val('');
            }
        });

        function addCap() {
            let capName = $('#cap-name').val();
            if (capName == '') {
                $('#cap-name').addClass("ui-state-error");
                return false;
            }
            jQuery.ajax({
                type: "post",
                url: script_data.url,
                data: "action=add_caps&cap-name=" + capName + "&nonce=" + script_data.nonce,
                success: function (res) {
                    if (res != 'ko') {
                        $('#capabilities-content ul').append('<li><input type="checkbox" id="' + res + '" value="' + res + '" class="capabilities">' + res + ' <a class="delete-cap" data-cap="' + res + '" href="#"><span class="dashicons dashicons-trash"></span></a></li>');
                        dialog.dialog("close");
                    } else {
                        $('#cap-name').addClass("ui-state-error");
                    }

                }
            });
        }

        $('#new-cap').click(function () {
            $("#dialog-form").dialog("open");

        });

        $(document).on('click', '.delete-cap', function (e) {
            e.preventDefault();
            if (!confirm('Remove Item?'))
                return false;
            let el = $(this);
            jQuery.ajax({
                type: "post",
                url: script_data.url,
                data: "action=delete_caps&cap-name=" + el.data('cap') + "&nonce=" + script_data.nonce,
                success: function (res) {
                    el.parent().remove();

                }
            });

        });
        
        var sub_delete = 0;
        var sub_publish = 0;
        var sub_read = 0;
        var sub_edit = 0;
        $('.caps-filter li').each(function () {
            if ($(this).hasClass('delete-type'))
                $('a[href="delete"]').find('span').text(++sub_delete)
            else if ($(this).hasClass('publish-type'))
                $('a[href="publish"]').find('span').text(++sub_publish)
            else if ($(this).hasClass('read-type'))
                $('a[href="read"]').find('span').text(++sub_read)
            else if ($(this).hasClass('edit-type'))
                $('a[href="edit"]').find('span').text(++sub_edit)
        });
        

        $('.filter-nav a').click(function (e) {
            e.preventDefault();
            $('.filter-nav a').removeClass('active')
            $(this).addClass('active')
            if ($(this).attr('href') == 'all') {
                $('#capabilities-content ul li').show();
                return false;
            }
            $('#capabilities-content ul li').hide();
            $('.' + $(this).attr('href') + '-type').show();
        });
        
        

        var selected_element = false;
        var selected_entity = false;




        $('body').on('change', '.mt-elements-content', function () {
            let el = $(this);
            let item = el.closest('.rules-item');
            let values = $(this).val().split('@');
            var submenu = '';
            if (values[0] == 'admin_menu')
                submenu = '&submenu=' + escape(JSON.stringify(script_data.submenu));
            console.log(submenu);
            jQuery.ajax({
                type: "post",
                dataType: 'json',
                url: script_data.url,
                data: "action=mt_get_sub_elements&type=" + values[0] + submenu + "&element=" + values[1] + "&nonce=" + script_data.nonce,
                success: function (res) {
                    item.find('.mt-sub-elements').html('<option value="all">All Items</option>');
                    res.forEach(function (el) {
                        item.find('.mt-sub-elements').append('<option value="' + el['key'] + '">' + el['label'] + '</option>');
                    });
                    if (el.data('sub')) {
                        item.find('.mt-sub-elements').val(el.data('sub'));
                        el.data('sub', false);
                    }
                }
            });
        });



        $('body').on('change', '.mt-elements-entities', function () {
            let el = $(this);
            let item = el.closest('.rules-item');
            let values = $(this).val().split('@');
            if ('plugin_capabilities' == values[0]) {
                item.find('.mt-sub-entities').html('<option value="all">All</option>');
                return false;
            }
            jQuery.ajax({
                type: "post",
                dataType: 'json',
                url: script_data.url,
                data: "action=mt_get_sub_elements&type=" + values[0] + "&element=" + values[1] + "&nonce=" + script_data.nonce,
                success: function (res) {
                    console.log(res);
                    item.find('.mt-sub-entities').html('<option value="all">All Entities</option>');
                    res.forEach(function (el) {
                        item.find('.mt-sub-entities').append('<option value="' + el['key'] + '">' + el['label'] + '</option>');
                    });
                    if (el.data('sub')) {
                        item.find('.mt-sub-entities').val(el.data('sub'));
                        el.data('sub', false);
                    }
                }
            });
        });

        $('.add-policy-rule').click(function (e) {
            e.preventDefault();
            var item = $('.rules-clonable').clone();
            item.removeClass('rules-clonable').addClass('rules-cloned');
            $('.rules-content').append(item);

        });

        $('body').on('click', '.substract-icon', function (e) {
            e.preventDefault();
            $(this).closest('.rules-item').remove();

        });




        $('.policy-edit').click(function (e) {
            e.preventDefault();
            let data = $(this).data('data');
            let slug = $(this).data('slug');
            console.log($(this).data('data'));
            $('.rules-cloned').remove();
            $('#policy-title').val(data.title);
            for (var i = 1; i < data.data.length; i++) {
                $('.add-policy-rule').click();
            }

            $('.rules-content .rules-item').each(function (i, el) {
                let _el = $(this);
                let rule = data.data[i];
                selected_element = rule.sub_element;
                selected_entity = rule.sub_entities;
                _el.find('.mt-elements-content').data('sub', rule.sub_element).val(rule.element).change();
                _el.find('.mt-elements-entities').data('sub', rule.sub_entities).val(rule.entities).change();
            });
            $('.save-mt-policy').hide();
            $('.update-mt-policy').show().val(slug);
            $('.cancel-update-policy').show();
            $('html, body').animate({scrollTop: 0}, 800);
        });

        $('.cancel-update-policy').click(function () {
            $('.rules-cloned').remove();
            $('#policies-form .inputs-value,.mt-elements-content,.mt-elements-entities,.mt-sub-elements,.mt-sub-entities').val('');
            $('.save-mt-policy').show();
            $('.update-mt-policy,.cancel-update-policy').hide();
            $('html, body').animate({scrollTop: 0}, 800);
        });

        




    });




})(jQuery);

function slugify(text)
{
    return text.toString().toLowerCase()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start of text
            .replace(/-+$/, '');            // Trim - from end of text
}
