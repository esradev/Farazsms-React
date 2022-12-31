/** ippanel.js **/
(function($){
	//$('.bookly-nav-item a').parent().text('پنل پیامک');
	$('#bookly_sms_beferest_url').after('<p class="help-block">پیش فرض پنل پیامک آی پی پنل: ippanel.com (در صورت درخواست پشتیبان سامانه پیامک تغییر دهید.)</p>');
	if($('#bookly_sms_beferest_username').length && $('#bookly_sms_beferest_username').val() !== '' && $('#bookly_sms_beferest_password').val() !== ''){
		$.ajax({
			type: 'post',
			async: true,
			url: '',
			data: {
				bookly_ippanel_auth : 'bookly_ippanel_auth',
			},
			beforeSend: function(){
				$('#bookly_settings_smspanel').prepend('<div class="loading"> در حال بررسی وضعیت درگاه<span style="">...</span></div>'); 
			},
			success: function (res) {
				console.log(res);
				/*var obj = JSON.parse(res);
				var message = '';				
				if(obj['auth'] == true) {	
					if(obj['credit'] < 20000 && obj['credit'] > 10000){
						var creditbgColor = '#ffcf7b';
					} else if (obj['credit'] < 8000){
						var creditbgColor = '#ff7b7b';
					} else {
						var creditbgColor = '#baddba';
					}
					message = '<div><span class="credit" style="background:'+creditbgColor+'">اعتبار پنل پیامک : '+obj['credit']+' ریال</span>';
					$('.rangineMessageBox td').html(message);
				}else{
					message += '<p style="color:red">نام کاربری و یا رمز عبور وارد شده درست نمی باشد.</p>';
					$('#bookly_settings_smspanel .loading').html(message);
				}*/
				$('#bookly_settings_smspanel .loading').html(res);
			},
		});
	}
	$( document ).ajaxComplete(function( event, xhr, settings ) {
		/*console.log(event);
		console.log(xhr);
		console.log(settings);
		$('#bookly-js-notification-modal').addClass('test');
		$('textarea#bookly-js-message').addClass('test');
	  if ( settings.url === "ajax/test.html" ) {
		console.log( "Triggered ajaxComplete handler. The result is " +
		  xhr.responseText );
	  }*/
	});
	$(window).load(function(){
		$('textarea#bookly-js-message').after("<div><span class='pattern-wizard'>ابزار تنظیم پترن</span><span class='pattern-test'>تست پیامک (بدون جایگذاری متغیرها)</span><div class='panel-modal'></div><style>.pattern-wizard, .pattern-test {	background: #1e91cf;	border-radius: 5px;	margin: 5px;	display: inline-block;	padding: 5px 10px;	color: #fff;	font-weight: bold;	font-size: 14px;	cursor: pointer;}.panel-modal {	border: 1px solid #ddd;	background: #dfdfdf;	width: 100%;	padding: 10px;	direction: rtl;	font-size: 14px;	display:none;}.panel-modal .pattern-message {	background: #eee;	padding: 10px;	border-radius: 10px;	color: blue;}.onlinepattern {	background: #fff;	padding: 10px;	border-radius: 5px;}.onlinepattern label {	display: inline-block;	padding: 0 0 0 10px;}.panel-modal label {	min-width: 100px;}.panel-modal input {    border: 1px solid #aaa;    padding: 5px 8px;    width: 220px;    display: inline-block;}.panel-modal .hidden{	display: none;}.onlinepattern .form-group {    margin: 0 0 10px 0;}.panel-modal .onlinepcodechecker,.panel-modal .patterninsert {	padding: 4px;	margin: 0 5px;}.pcodecheckresult div {	margin-bottom: 5px;}.send-test-sms-btn {    background: #31a92e;    border-radius: 5px;    margin: 2px;    display: inline-block;    padding: 5px 10px;    color: #fff;    font-size: 14px;    cursor: pointer;}</style>");
		$('#bookly-js-notification-modal .modal-dialog').on('hidden.bs.modal', function () {
		  console.log(1);
		})
		$(".pattern-wizard").click(function(){
			var paternmodal = $(this).parent().find('.panel-modal');
			var targettextarea = $(this).parents('.form-group').find('textarea');
			paternmodal.show(300);
			modalbody = '<div class="onlinepattern"><div class="form-group"><label>کد الگو:</label><input class="patterncodeinput form-control" placeholder="کد الگوی مورد نظر خود را وارد نمایید" ><button class="btn btn-default onlinepcodechecker" type="button">بررسی الگو</button></div><div class="pattern-message"></div><div class="pcodecheckresult"></div><div><button class="btn btn-default patterninsert hidden" type="button">ثبت الگو در کادر پیامک</button></div></div><div class="helppattern"><p><b>راهنما:</b> برای استفاده از سیستم پترن برای ارسال سریع پیامک باید یک متن پیامک به دلخواه خود در سامانه پیامک ثبت کنید.</p> پس از ثبت و تایید پترن از بخش تنظیم پترن به صورت آنلاین کد پترن را درج و متغیرهای هر پارامتر را وارد نمایید.</div>';
				
			paternmodal.html(modalbody);
			$('.onlinepcodechecker').click(function(){
				var patterncodeinput = $(this).parent().find('.patterncodeinput');
				var onlinepattern = $(this).parents('.onlinepattern');
				var patternmessage = onlinepattern.find('.pattern-message');
				var pcodecheckresult = onlinepattern.find('.pcodecheckresult');
				
				pcode = patterncodeinput.val();
				$.ajax({
					type: 'GET',
					url: '',
					data: 'action=ippanelpatterncheck&pcode=' + pcode,
					dataType: 'json',
					beforeSend: function(){
						patternmessage.html('<i class="fa fa-spinner fa-spin"></i> در حال بررسی الگو...');
					},
					success: function(json) {
					  if(json.status == 0){
						patternmessage.html(json.message.replace("\n","<br>"));
						var pvars = json.vars;
						var output = '<p>لطفاً پارامترهای پترن را با متغیرهای سایت تکمیل نمایید.</p>';
						pvars.forEach(function(value, index, array){
							output += '<div><label>'+value+'</label><input type="text"/></div>';
						});
						pcodecheckresult.html(output);
						onlinepattern.find('.variableshelp,.patterninsert').removeClass('hidden');
						
						$(this).addClass('hidden');
						patternoutput = 'pcode='+pcode.trim();
						onlinepattern.find('.patterninsert').click(function(){
							onlinepattern.find('.pcodecheckresult div').each(function(){
								patternoutput += "\n"+$(this).find('label').text()+'='+$(this).find('input').val().trim();
							})
							targettextarea.val(patternoutput).addClass('pattern');
							paternmodal.hide(300);
							var lang = targettextarea.attr('id').split('-');
							$('#length-'+lang[2]).html('پس از جایگذاری متغیرها بیش از ' + json.message.length + ' کاراکتر خواهد شد. (' + Math.ceil(json.message.length / 64) + ' صفحه پیامک)');
						});
					  }else if(json.status == 997 || json.status == 404){
						  patternmessage.html('این الگو در دسترس شما نیست.');
					  }else{
						  patternmessage.html(json.message);
					  }
					}
				});
			});
		});
		$('.pattern-test').on('click',function(){
			$('.send-test-sms-mobile').parent().remove();
			var targettextarea = $(this).parents('.form-group').find('textarea');
			var trid = targettextarea.attr('id')
			$(this).after('<div><input class="send-test-sms-mobile" id="'+trid+'_test_mobile" type="text" placeholder="mobile" /><span class="send-test-sms-btn" id="'+trid+'_sendTestSms">ارسال</span><br><span class="result"></span></div>');
			$('#'+trid+'_sendTestSms').on('click',function(){
				var phone = $('input#'+trid+'_test_mobile').val();
				var body = $('#'+trid).val();
				var result = sms_test(phone,body,$(this).parent().find('span.result'));
				if(result == 'phonenotfound') {
					$('input#'+trid+'_test_mobile').focus();
					return false;
				}else if(result == 'bodyisempty'){
				targettextarea.focus();
					return false;
				}
				
			});
			return false;
		});
	});
	function sms_test(phone,body,resultbox){
		if ( phone === '' ) {
			alert('لطفاً یک شماره همراه برای دریافت پیامک تست وارد کنید.');
			return 'phonenotfound';
		}
		if ( body === '' ) {
			alert('متن پیامک خالی هست.');
			return 'bodyisempty';
		}
		var data  = {
			action : 'bookly_sms_test',
			phonenumber : phone,
			smstext : body,
		};
		$.ajax({
			type: 'GET',
			url: '',
			data: data,
			dataType: 'json',
			beforeSend: function(){
				resultbox.html('<span style="color:blue"><i class="fa fa-spinner fa-spin"></i> در حال ارسال...</span>');
			},
			success: function(json) {
				console.log(json.success);
				if ( json.success == true ) {
					resultbox.html('<span style="color:green">پیامک تست ارسال شد.</span>');
				}else{
					resultbox.html('<span style="color:red">پیامک ارسال نشد.</span>');
				}
			}
		});
		return false;
	}
})(jQuery);