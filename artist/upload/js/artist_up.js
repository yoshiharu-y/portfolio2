//アップロード処理するphpファイル

var loadFile = "../upload/action/file_up.php";

//処理
$(function() {
     $('#up_new').change(function() {
         $(this).upload(loadFile, function(res) {
				$("#up_file").remove();
                $(res).insertAfter(this);
         }, 'html');
     });	    
});