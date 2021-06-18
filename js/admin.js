$(function () {
    
    function getFileThumb(file) {

        const name = file;
        const lastDot = name.lastIndexOf('.');

        var ext = name.substring(lastDot + 1);
        var fileName = (name.split('\\').pop().split('/').pop().split('.'))[0];

        return name.substr(0, name.lastIndexOf('/')) + '/.' + fileName + '_s.' + ext.replace('jpeg', 'jpg');;
    }

    function FileExists(url) {
        var xhr = new XMLHttpRequest();
        xhr.open('HEAD', url, false);
        xhr.send();

        if (xhr.status == "404") {
            return false;
        } else {
            return true;
        }
    }

    // default image
    $('#default-image-selector').on('click', function (e) {
        $('input[name="change-button-id"]').val(this.id);
        window.open('media.php?plugin_id=admin.blog.theme&popup=1&select=1', 'dc_popup', 'alwaysRaised=yes,dependent=yes,toolbar=yes,height=500,width=760,' + 'menubar=no,resizable=yes,scrollbars=yes,status=no');
        e.preventDefault();
        return false;
    });

    $('#default-image-selector-reset').on('click', function (e) {
        var url = $('input[name="theme-url"]').val() + '/img/intro-bg.jpg';
        var thumb = $('input[name="theme-url"]').val() + '/img/.intro-bg_s.jpg';
        $('#default-image-url').val(url);
        $('#default-image-thumb-url').attr('src', thumb);
    });

    $('#default-image-url').on('change', function (e) {
        var url = $('input[name="theme-url"]').val() + '/img/intro-bg.jpg';
        var thumb = $('input[name="theme-url"]').val() + '/img/.intro-bg_s.jpg';
        if ($('#default-image-url').val() == url) {
            $('#default-image-thumb-url').attr('src', thumb);
        } else if ($('#default-image-url').val() == '') {
            return;
        } else {
            thumb = getFileThumb($('#default-image-url').val());
            if (FileExists(thumb)) {
                $('#default-image-thumb-url').attr('src', thumb);
            } else {
                thumb = $('input[name="theme-url"]').val() + '/img/no-thumb.jpg';
                $('#default-image-thumb-url').attr('src', thumb);
            }
        }
    });
});