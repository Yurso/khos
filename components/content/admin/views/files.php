<script src="/public/js/dropzone/dist/min/dropzone.min.js"></script>
<link rel="stylesheet" href="/public/js/dropzone/dist/min/dropzone.min.css" />    

<script type="text/javascript">

    var currentDir = 'images';

    function openDir(dir) {

        $.ajax({
            method: "GET",
            url: "/admin/content/files/scan",
            dataType: "json",
            data: {sub_dir: dir},
            beforeSend: function() {
                $('#files-table').addClass('busy');
            },
            success: buildFilesTable
        }).done(function() {
            $('#files-table').removeClass('busy');
            currentDir = dir;
        });

    }

    function selectFile(url) {           
        var activeEditor = top.tinymce.activeEditor;        
        if (activeEditor) {
            activeEditor.windowManager.getParams().url = url
        }
        showInfo(url);
    }

    function showInfo(url) {

        $('.fmi-image').html('<img src="'+url+'" alt="" id="fmi-image">');

    }

    function buildFilesTable(files) {

        var result = '';

        files.forEach(function(item, i, arr) {

            var icon = '<i class="fa fa-file-o" aria-hidden="true"></i> ';            
            if (item.type == 'dir') {
                icon = '<i class="fa fa-folder" aria-hidden="true"></i> ';
            }
            
            result = result + '<tr>';
            result = result + '<td>';
            
            if (item.type == 'dir') {
                result = result + '<a href="#" onclick="openDir(\''+item.sub_dir+'\');return false;">';
                result = result + icon + item.name;
                result = result + '</a>';
            } else {
                result = result + '<a href="#" onclick="selectFile(\''+item.url+'\');return false;">';
                result = result + icon + item.name;
                result = result + '</a>';
            }
            
            result = result + '</td>';
            result = result + '<td style="text-align:center;">'+item.type+'</td>';
            result = result + '</tr>';
        });

        $('#files-table tbody').html(result);

    }
    
    $(document).ready(function(){  
        
        openDir(currentDir);

        Dropzone.options.fupload = {
            
            success: function(file, response) { 

                //console.log(file);

                var obj = jQuery.parseJSON(response);

                if (obj.success) {
                    //$(".uploaded-images").append('<input type="hidden" name="uploaded[]" value="'+obj.filename+'">');
                    openDir(currentDir);
                } else {
                    alert("Не удалось загрузить изображение. "+obj.message);
                    $(file.previewElement).remove();
                }       

            },
            dictDefaultMessage: "Поместите сюда файлы для загрузки"         

        };

    });

</script>

<!-- <h2 class="content-title">Просмотр файлов</h2> -->
<div class="files-manager">
    <div class="fm-scroll">        
        <table cellpadding="0" cellspacing="0" border="0" id="files-table">
            <thead>
                <th>Имя</th>
                <th width="40">Тип</th>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="fm-info">
        <div class="fmi-inner">
            <div class="fmi-image"></div>
            <div class="fmi-desc"></div>
            <div class="fmi-upload">
                <form action="/admin/content/files/upload" class="dropzone" id="fupload"></form>
            </div>
        </div>
    </div>
</div>