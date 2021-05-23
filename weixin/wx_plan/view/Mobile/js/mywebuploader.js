jQuery(function() {
    var $ = jQuery,
        $list = $('#thelist'),
        $btn = $('#ctlBtn'),
        state = 'pending',
        uploader;

    uploader = WebUploader.create({

        // 不压缩image
        resize: false,

        // swf文件路径
        swf:urlflash,

        // 文件接收服务端。
        server: '',

        // 选择文件的按钮。可选。
        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
        pick: '#picker'
    });

    // 当有文件添加进来的时候
    uploader.on( 'fileQueued', function( file ) {
        
    });

    // 文件上传过程中创建进度条实时显示。
    uploader.on( 'uploadProgress', function( file, percentage ) {
        
    });

    uploader.on( 'uploadSuccess', function( file ) {

    });
        

    uploader.on( 'uploadError', function( file ) {
       
    });

    uploader.on( 'uploadComplete', function( file ) {
        
    });
});