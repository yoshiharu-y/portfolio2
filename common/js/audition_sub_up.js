//アップロード処理するphpファイル

var loadFile = "action/audition_img_up.php";

//処理
$(function() {
     $('#banner_add_new').change(function() {
         $(this).upload(loadFile, function(res) {
				$("#banner_img").remove();
                $(res).insertAfter(this);
         }, 'html');
     });	    
});