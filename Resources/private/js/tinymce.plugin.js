tinymce.PluginManager.add('filemanager', function (editor) {

    function filemanager(id, value, type, win) {

        var title = "Gestionnaire de médias"; // TODO
        if (typeof editor.settings.filemanager_title !== "undefined" && editor.settings.filemanager_title) {
            title = editor.settings.filemanager_title;
        }

        var path = '/admin/content/media-browser?mode=tinymce';
        if (type == 'image') {
            path = path + '&types[]=image';
        } else if (type == 'media') {
            path = path + '&types[]=video&types[]=audio'
        }
        tinymce.activeEditor.windowManager.open({
            title: title,
            file: path,
            width: 900,
            height: 470,
            resizable: true/*,
             maximizable: true,
             inline: 1*/

        }, {
            setUrl: function (url) {
                var fieldElm = win.document.getElementById(id);
                fieldElm.value = editor.convertURL(url);
            }
        });
    }

    tinymce.activeEditor.settings.file_browser_callback = filemanager;

/*
    function imagesUploadHandler(blobInfo, success, failure) {

        //console.log('imagesUploadHandler');
        //console.log(blobInfo);

        var xhr, formData;

        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', "/app_dev.php/tinymce/upload");
        //xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

        xhr.onload = function () {
            var json;

            if (xhr.status != 200) {
                failure("HTTP Error: " + xhr.status);
                return;
            }

            json = JSON.parse(xhr.responseText);

            if (!json || typeof json.location != "string") {
                failure("Invalid JSON: " + xhr.responseText);
                return;
            }

            success(json.location);
        };

        //console.log('building form data ...');

        var reader = new FileReader();
        reader.readAsDataURL(blobInfo.blob());
        reader.onloadend = function () {
            formData = new FormData();
            formData.append('data', reader.result);
            xhr.send(formData);
        };
    }

    tinymce.activeEditor.settings.images_upload_handler = imagesUploadHandler;
*/

    return false;
});