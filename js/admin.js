$(function () {
    // user image
    $('#resume_user_image_selector').on('click', function (e) {
        window.open('media.php?plugin_id=admin.blog.theme&popup=1&select=1', 'dc_popup', 'alwaysRaised=yes,dependent=yes,toolbar=yes,height=500,width=760,' + 'menubar=no,resizable=yes,scrollbars=yes,status=no');
        e.preventDefault();
        return false;
    });

    $('#resume_user_image_reset').on('click', function (e) {
        var url = $('input[name="theme-url"]').val() + '/img/profile.jpg';
        $('#resume_user_image').val(url);
        $('#resume_user_image_src').attr('src', url);
        $('#resume_user_image_src').attr('alt', url);
    });

    $('#resume_user_image').on('change', function (e) {
        const url = $('input[name="theme-url"]').val() + '/img/profile.jpg';
        if ($('#resume_user_image').val() == url) {
            $('#resume_user_image_src').attr('src', url);
            $('#resume_user_image_src').attr('alt', url);
        } else if ($('#resume_user_image').val() == '') {
            return;
        } else {
            let src = $('#resume_user_image').val();
            $('#resume_user_image_src').attr('src', src);
            $('#resume_user_image_src').attr('alt', src);
        }
    });
});