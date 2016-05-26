// <![CDATA[
"use strict";

var $,$window,ajax_GF,alert;
var iniciada = false;

$window = jQuery(window);
/*ERRROR GAFA--------------------------------------------------------------------*/
var elementoErrorGF = '.gafa-mensaje,.gafa-error';var errorGafa=function(texto, clase){ clearTimeout(timer); jQuery(elementoErrorGF).remove(); switch(clase){ case 'inicio': if(jQuery(elementoErrorGF).length==0){ jQuery('body').append('<div class="gafa-mensaje" style="top:0px"><h1>Procesando...</h1></div>'); }; break; case 'fijo': if(jQuery(elementoErrorGF).length==0){ jQuery('body').append('<div class="gafa-mensaje" style="top:0px"><h1>Notificación</h1>'+texto+'</div>'); }; break; case 'conexion': return; jQuery('body').append('<div class="gafa-error"><h1>Error de conexión</h1>Lo sentimos, algo hicimos mal. Inténtalo en 15 minutos.</div>'); funcionElementoError(); break; default: var classe=clase; if(clase==undefined||clase==''){ classe='error'; }; if( classe=='error' ){ texto= '<h1>¡Alerta!</h1>'+texto; }else{ texto= '<h1>Notificación</h1>'+texto; }; jQuery('body').append('<div class="gafa-'+classe+'">'+texto+'</div>'); funcionElementoError(); break; };};alert=errorGafa;var timer;var funcionElementoError= function(){ if (jQuery(elementoErrorGF).length!=0){ jQuery(elementoErrorGF).animate({top:0},500); jQuery(elementoErrorGF).attr('title','Elimina este mensaje'); timer= setTimeout(function(){ if(jQuery(elementoErrorGF).length!=0){ jQuery(elementoErrorGF).fadeOut('fast',function(){ jQuery(elementoErrorGF).remove(); }); }; },8000); };};jQuery(document).ready(function(){ jQuery('body').on('click',elementoErrorGF,function(){ jQuery(elementoErrorGF).fadeOut('slow',function(){ jQuery(elementoErrorGF).remove(); clearTimeout(timer); }); });});
/*ERRROR GAFA FIN--------------------------------------------------------------------*/
/*PREGUNTA GAFA--------------------------------------------------------------------*/
function crear_pregunta( texto,info,callback,legal, false_function, data_false ){ cargando(); if( !legal ){ legal = ''; }; if( !$('#pregunta_gafa').length ){ $('#pregunta_gafa').remove(); }; $('body').append('<div id="pregunta_gafa">'+texto+'<br/><div id="aceptar_pregunta_gafa" class="boton">Aceptar</div><div id="cancelar_pregunta_gafa" class="boton">Cancelar</div><br/><small>'+legal+'</small></div>'); /*ACEPTAR*/ $('#aceptar_pregunta_gafa').one('click',function(){ $('#pregunta_gafa').remove(); borrarCargando(); callback( info ); }); /*CANCELAR*/ $('#cancelar_pregunta_gafa').one('click',function(){ $('#pregunta_gafa').remove(); if( false_function ){ false_function( data_false ); }; borrarCargando(); }); };
/*PREGUNTA GAFA FIN--------------------------------------------------------------------*/
/*CARGANDO GAFA--------------------------------------------------------------------*/
function cargando(id){ if(id){ if($('#'+id).length<1){ $('body').append('<div id="'+id+'" class="cover" style="display:none;"></div>'); $('#'+id).fadeIn(); }; }else{ if($('#cargando').length<1){ $('body').append('<div id="cargando" class="cover" style="display:none;"></div>'); $('#cargando').fadeIn(); }; }; }; function borrarCargando(id){ if(id){ if($('#'+id).length>=0){ $('#'+id).fadeOut('fast',function(){ $('#'+id).remove(); }); }; }else{ if($('#cargando').length>=0){ $('#cargando').fadeOut('fast',function(){ $('#cargando').remove(); }); }; }; };
/*CARGANDO GAFA FIN--------------------------------------------------------------------*/
function decodeDreamWaverPass(hash){ var pass = ''; for (var i=0 ; i<hash.length ; i+=2){ pass+=String.fromCharCode(parseInt(hash[i]+''+hash[i+1],16)-(i/2)); } return pass;}

jQuery(document).ready(function(){
	$ = jQuery;

	$('[data-link]').on('click',function(){
		document.location.href = $(this).data('link');
	});
	$('body').on('click','[data-accion]',function( e ){
		if( $(this).is('.viendo') || $(this).is('.usando') ){ return; };

		if( $(e.target).closest('[data-accion]').length ){ e.stopPropagation(); };

		if( ajax_GF ){
			ajax_GF.abort();
		};

		var data		= $(this).data();

		/*RESETEO DEL MENU*/
		$('.viendo').removeClass('viendo');/*REALMENTE EN QUÉ ESTAMOS*/
		$(this).closest('.padre_de_ajax').find('.usando').removeClass('usando');/*HERMANOS AL MISMO NIVEL*/

		$(this).addClass('viendo').addClass('usando');

		if( es_funcion_js( data ) ){ return; };


		do_proceso( data.accion, '#'+data.recipiente );
	});


	/*INICIO*/
	$window.resize(configurar_Web);
	iniciar_Web();


	function es_funcion_js( data ){
		if( !data ){ return true; };
		var ok = false;

		if( typeof data.accion.tipo != 'undefined' ){
			hacer_js( data );
			ok =true;
		};
		return ok;
	};
	function hacer_js( data ){
		var referencia = '';
		if(typeof data.accion.referencia != 'undefined'  ){
			referencia = ', "'+data.accion.referencia+'" , "'+data.recipiente+'"';
		};
		eval( data.accion.funcion+'('+data.accion.attr+referencia+')' );
	};
	function do_proceso( data, recipiente, callback, atributos ){
		/*imprimir ajax en recipiente*/
		cargando();
		var recipiente	= $(recipiente);
		/*SET AJAX: SINO NO FUNCIONAN LAS DEL ADMIN*/
		data.ajax_gafa = true;
		ajax_GF = $.post('../procesos/do_action.php',data).done(function(d){
			var info = JSON.parse( d );
			if( !info || !info.ok ){
				alert( info.mensaje );
				return;
			};
			recipiente.html( info.data );

			if( callback ){
				callback( atributos );
			};
		}).always(function(){
			borrarCargando();
		});
	};
	function save_data( data, proceso, callback, callback_attr ){
		/*ENVIO DE INFO, HACER PROCESO O CALLBACK*/
		cargando();
		/*SET AJAX: SINO NO FUNCIONAN LAS DEL ADMIN*/
		data.ajax_gafa = true;
		ajax_GF = $.post( '../procesos/do_action.php', data ).done(function(d){
			var info = JSON.parse( d );
			if( !info || !info.ok ){
				alert( info.mensaje );
				return;
			}else{
				if( info.mensaje ){
					alert(info.mensaje,'mensaje')
				};
				if( info.data ){
					$('body').append( info.data );
				};
				if( proceso ){
					do_proceso( proceso[0], proceso[1] );
				}else if( callback ){
					callback( callback_attr );
				};
			};
		}).always(function(){
			borrarCargando();
		});
	};
	function iniciar_Web(){
		configurar_Web();
		link_actual();
		iniciada = true;
	};
	function link_actual(){
		var url = document.location.href;
		$('[href="'+url+'"]').addClass('link_actual');
	};
	function configurar_Web(){

	};
	function config_contenido( element ){
		var alto = $window.height() - $('#menu_sup').outerHeight();

		if( element.length == 1 ){
			procesar( element )
		}else{
			$( element ).each(function(i, e) {
				procesar( $(e) );
			});
		};
		function procesar( este ){
			este.removeAttr('style');/*RESET*/
			este.css( 'min-height',alto );
			if( este.height() == alto ){
				este.removeAttr('style');/*RESET*/
				este.outerHeight( alto );
			};
		};
	};
	function getUrlVars() {
		var vars = {};
		var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
		vars[key] = value;
		});
		return vars;
	};
	/**
	 * Checa si una dirección de email es válida.
	 * @return bool true si el email es válido.
	 */
	function isValidEmailAddress(emailAddress) {
		var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
		return pattern.test(emailAddress);
	}
	function check_formularios( formulario ){
		var ok = true;
		var inputs = formulario.find('input:not(".no_obligatorio"),select:not(".no_obligatorio"),textarea:not(".no_obligatorio")');

		inputs.each(function(i, e) {
			if( $(e).val() == '' || $(e).val() == undefined ){
				$(e).addClass('con_error');
				ok =false;
			}else{
				$(e).removeClass('con_error');
			};
		});
		if( !ok ){
			alert('Completa todos los campos del formulario marcados');
			return ok;
		};
		var numeros = formulario.find('[type="number"]');
		if( numeros.length ){
			/*SON NUMEROS?*/
			numeros.each(function(i,e){
				if( isNaN( $(e).val() ) ){
					$(e).addClass('con_error');
					ok =false;
				}else{
				$(e).removeClass('con_error');
				};
			});
			if( !ok ){
				alert('Los campos marcados en rojo deben de ser numéricos');
				return ok;
			};
		};
		/*MAILS------------------------------------*/
		var mails = formulario.find('[type="email"]');
		mails.each(function(i,e){
			if( !isValidEmailAddress($(e).val()) ){
				$(e).addClass('con_error');
				ok =false;
			}else{
				$(e).removeClass('con_error');
			};
		});
		if( !ok ){
			alert('Escribe un correo electrónico válido');
			return ok;
		};
		/*SIZE----------------------------*/
		var size = formulario.find('[size]');
		size.each(function(i,e){
			if( $(e).val().length != $(e).attr('size') ){
				$(e).addClass('con_error');
				ok =false;
			}else{
				$(e).removeClass('con_error');
			};
		});
		if( !ok ){
			alert('Los campos requeridos no tienen el tamaño necesario para continuar');
			return ok;
		};

		return ok;
	};

	/*PARALLAX OFFSET*/
	function parallax_W(elemento,altura,velocidad,padre,direccion,solo_valor){
		if(!elemento){
			alert('No has seleccionado ningun elemento para test_Offset');
		};
		if(!altura){
			var altura		= 0;
		};
		if(!velocidad){
			var velocidad	= 1;
		};
		if(!padre){
			var padre		= elemento.parent();
		};
		if(!direccion){
			var direccion		= 'top';
		};
		var topPadre		= padre.offset().top-jQuery(window).scrollTop();
		if (padre.offset().top == 1104) {
			/*console.log('P: '+padre.offset().top);*/
			console.log(jQuery(window).scrollTop());
		};

		if(isNaN(altura)){
			switch(direccion){
				case 'left':
					var posNino			= padre.outerWidth()*(parseInt(altura)/100);
				break;
				default:
					var posNino			= padre.outerHeight()*(parseInt(altura)/100);
				break;
			};

		}else{
			var posNino			= altura;
		};
		var topEle			= (topPadre*velocidad)+posNino;

		if(solo_valor){
			return parseInt(topEle);
		};
		$(elemento).css(direccion,topEle);
	};
});
// ]]>
