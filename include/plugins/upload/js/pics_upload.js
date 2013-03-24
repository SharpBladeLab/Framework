$(function() {

    $('.button').each(function(i, e) {

        var btn = $(e);

        var txt = $(e).prev();

        var button = btn, interval;

        new AjaxUpload(button, {
            action: 'upload.php',
            name: 'myfile',

           onSubmit: function(file, ext) {        
                    if (!(ext&&/^(jpg|png|jpeg|gif|JPG|PNG|JPEG|GIF|txt|flv|3gp)$/.test(ext))){

                            // 扩展名不允许

                            alert('错误：无效的文件扩展名!');

                           // 取消上传

                           return false;

                   }

                button.text('上传中');

                this.disable();

                interval = window.setInterval(function() {

                    var text = button.text();

                   if (text.length < 13) {

                        button.text(text + '.');

                   } else {

                        button.text('上传中');

                    }

                }, 200);

            },

            onComplete: function(file, response) {
                button.text('上传');

                if(response!='0')
				//alert(response);

                window.clearInterval(interval);

                // enable upload button

                this.enable();

                txt.val(response);

            }

        });

    });

});
