$(function () {

	$("#entrar").on('click', function(){

	        var name = $("#username").val();
	        var password = $("#password").val();
	        password = $.md5(password);
	        var respuesta;

	        $.post("controlador/fachada.php", {
                clase: 'usuario',
                oper: 'autenticar',
                id_usuario: name,
                contrasena: password
            }, function (data) {
            	if(data==true){
            		$('#mask , .login-popup').fadeOut(300 , function() {
			            $('#mask').remove();  
			        }); 
            	}else{
            		alert("Lo siento, no pudimos autenticarte");
            	}
            }, "json").always(function () {
                // $.unblockUI();
            });

	});
})