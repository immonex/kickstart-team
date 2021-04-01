(self["webpackChunk_immonex_kickstart_team"] = self["webpackChunk_immonex_kickstart_team"] || []).push([["contact_form"],{

/***/ "./src/js/src/contact_form.js":
/*!************************************!*\
  !*** ./src/js/src/contact_form.js ***!
  \************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "init": () => (/* binding */ init)
/* harmony export */ });
const $ = jQuery

function init() {
	setTimeout(() => {
		$('.inx-team-contact-form input[name=consent]').on('change', function (e) {
			const el = $(this);
			const submitEl = el.parentsUntil('.inx-team-contact-form').parent().find('.inx-team-contact-form__submit');
			submitEl.attr('disabled', !el.prop('checked'));
		});

		$('.inx-team-contact-form').on('submit', function (e) {
			e.preventDefault();

			var form = $(this);
			var resultEl = form.find('.inx-team-contact-form__result').first();
			var spinner = form.children('.inx-team-contact-form__spinner').first();

			form.find('.inx-team-contact-form__input--has-error').removeClass('inx-team-contact-form__input--has-error');
			resultEl[0].className = 'inx-team-contact-form__result uk-margin';
			spinner.show();

			$.post(
				form.attr('action'),
				form.serialize(),
				function (response) {
					if ('string' === typeof response) {
						var data = JSON.parse( response.match( /{.*}/ ) );
					} else {
						var data = response;
					}

					form[0].reset();

					if (inx_team.hide_form_after_submit) {
						form.find('.inx-team-contact-form__input:not(.inx-team-contact-form__result-wrap)').hide();
					}

					resultEl.html('<span uk-icon="icon: check; ratio: 2"></span> <span>' + data.message + '</span>');
					resultEl[0].className = 'inx-team-contact-form__result inx-team-contact-form__result--type--success uk-margin';
				},
				'json'
			).fail(function (xhr) {
				const data = xhr.responseJSON;

				$.each(data.field_errors, function (fieldName, message) {
					const inputEl = form.find('.inx-team-contact-form__input--name--' + fieldName).first();
					inputEl.children('.inx-team-contact-form__input-error').first().html(message);
					inputEl.addClass('inx-team-contact-form__input--has-error');
				});

				resultEl.html('<span uk-icon="icon: warning; ratio: 2"></span> <span>' + data.message + '</span>');
				resultEl[0].className = 'inx-team-contact-form__result inx-team-contact-form__result--type--error uk-margin';
			}).always(function (xhr) {
				spinner.hide();
			});
		});
	}, 2000)
} // init




/***/ })

}]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9AaW1tb25leC9raWNrc3RhcnQtdGVhbS8uL3NyYy9qcy9zcmMvY29udGFjdF9mb3JtLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiI7Ozs7Ozs7Ozs7Ozs7QUFBQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxHQUFHOztBQUVIO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsK0NBQStDLEdBQUc7QUFDbEQsTUFBTTtBQUNOO0FBQ0E7O0FBRUE7O0FBRUE7QUFDQTtBQUNBOztBQUVBLCtDQUErQztBQUMvQztBQUNBLEtBQUs7QUFDTDtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQSxLQUFLOztBQUVMLGdEQUFnRDtBQUNoRDtBQUNBLElBQUk7QUFDSjtBQUNBLElBQUk7QUFDSixHQUFHO0FBQ0gsRUFBRTtBQUNGLENBQUM7O0FBRWMiLCJmaWxlIjoiY29udGFjdF9mb3JtLmpzIiwic291cmNlc0NvbnRlbnQiOlsiY29uc3QgJCA9IGpRdWVyeVxuXG5mdW5jdGlvbiBpbml0KCkge1xuXHRzZXRUaW1lb3V0KCgpID0+IHtcblx0XHQkKCcuaW54LXRlYW0tY29udGFjdC1mb3JtIGlucHV0W25hbWU9Y29uc2VudF0nKS5vbignY2hhbmdlJywgZnVuY3Rpb24gKGUpIHtcblx0XHRcdGNvbnN0IGVsID0gJCh0aGlzKTtcblx0XHRcdGNvbnN0IHN1Ym1pdEVsID0gZWwucGFyZW50c1VudGlsKCcuaW54LXRlYW0tY29udGFjdC1mb3JtJykucGFyZW50KCkuZmluZCgnLmlueC10ZWFtLWNvbnRhY3QtZm9ybV9fc3VibWl0Jyk7XG5cdFx0XHRzdWJtaXRFbC5hdHRyKCdkaXNhYmxlZCcsICFlbC5wcm9wKCdjaGVja2VkJykpO1xuXHRcdH0pO1xuXG5cdFx0JCgnLmlueC10ZWFtLWNvbnRhY3QtZm9ybScpLm9uKCdzdWJtaXQnLCBmdW5jdGlvbiAoZSkge1xuXHRcdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG5cdFx0XHR2YXIgZm9ybSA9ICQodGhpcyk7XG5cdFx0XHR2YXIgcmVzdWx0RWwgPSBmb3JtLmZpbmQoJy5pbngtdGVhbS1jb250YWN0LWZvcm1fX3Jlc3VsdCcpLmZpcnN0KCk7XG5cdFx0XHR2YXIgc3Bpbm5lciA9IGZvcm0uY2hpbGRyZW4oJy5pbngtdGVhbS1jb250YWN0LWZvcm1fX3NwaW5uZXInKS5maXJzdCgpO1xuXG5cdFx0XHRmb3JtLmZpbmQoJy5pbngtdGVhbS1jb250YWN0LWZvcm1fX2lucHV0LS1oYXMtZXJyb3InKS5yZW1vdmVDbGFzcygnaW54LXRlYW0tY29udGFjdC1mb3JtX19pbnB1dC0taGFzLWVycm9yJyk7XG5cdFx0XHRyZXN1bHRFbFswXS5jbGFzc05hbWUgPSAnaW54LXRlYW0tY29udGFjdC1mb3JtX19yZXN1bHQgdWstbWFyZ2luJztcblx0XHRcdHNwaW5uZXIuc2hvdygpO1xuXG5cdFx0XHQkLnBvc3QoXG5cdFx0XHRcdGZvcm0uYXR0cignYWN0aW9uJyksXG5cdFx0XHRcdGZvcm0uc2VyaWFsaXplKCksXG5cdFx0XHRcdGZ1bmN0aW9uIChyZXNwb25zZSkge1xuXHRcdFx0XHRcdGlmICgnc3RyaW5nJyA9PT0gdHlwZW9mIHJlc3BvbnNlKSB7XG5cdFx0XHRcdFx0XHR2YXIgZGF0YSA9IEpTT04ucGFyc2UoIHJlc3BvbnNlLm1hdGNoKCAvey4qfS8gKSApO1xuXHRcdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0XHR2YXIgZGF0YSA9IHJlc3BvbnNlO1xuXHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdGZvcm1bMF0ucmVzZXQoKTtcblxuXHRcdFx0XHRcdGlmIChpbnhfdGVhbS5oaWRlX2Zvcm1fYWZ0ZXJfc3VibWl0KSB7XG5cdFx0XHRcdFx0XHRmb3JtLmZpbmQoJy5pbngtdGVhbS1jb250YWN0LWZvcm1fX2lucHV0Om5vdCguaW54LXRlYW0tY29udGFjdC1mb3JtX19yZXN1bHQtd3JhcCknKS5oaWRlKCk7XG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0cmVzdWx0RWwuaHRtbCgnPHNwYW4gdWstaWNvbj1cImljb246IGNoZWNrOyByYXRpbzogMlwiPjwvc3Bhbj4gPHNwYW4+JyArIGRhdGEubWVzc2FnZSArICc8L3NwYW4+Jyk7XG5cdFx0XHRcdFx0cmVzdWx0RWxbMF0uY2xhc3NOYW1lID0gJ2lueC10ZWFtLWNvbnRhY3QtZm9ybV9fcmVzdWx0IGlueC10ZWFtLWNvbnRhY3QtZm9ybV9fcmVzdWx0LS10eXBlLS1zdWNjZXNzIHVrLW1hcmdpbic7XG5cdFx0XHRcdH0sXG5cdFx0XHRcdCdqc29uJ1xuXHRcdFx0KS5mYWlsKGZ1bmN0aW9uICh4aHIpIHtcblx0XHRcdFx0Y29uc3QgZGF0YSA9IHhoci5yZXNwb25zZUpTT047XG5cblx0XHRcdFx0JC5lYWNoKGRhdGEuZmllbGRfZXJyb3JzLCBmdW5jdGlvbiAoZmllbGROYW1lLCBtZXNzYWdlKSB7XG5cdFx0XHRcdFx0Y29uc3QgaW5wdXRFbCA9IGZvcm0uZmluZCgnLmlueC10ZWFtLWNvbnRhY3QtZm9ybV9faW5wdXQtLW5hbWUtLScgKyBmaWVsZE5hbWUpLmZpcnN0KCk7XG5cdFx0XHRcdFx0aW5wdXRFbC5jaGlsZHJlbignLmlueC10ZWFtLWNvbnRhY3QtZm9ybV9faW5wdXQtZXJyb3InKS5maXJzdCgpLmh0bWwobWVzc2FnZSk7XG5cdFx0XHRcdFx0aW5wdXRFbC5hZGRDbGFzcygnaW54LXRlYW0tY29udGFjdC1mb3JtX19pbnB1dC0taGFzLWVycm9yJyk7XG5cdFx0XHRcdH0pO1xuXG5cdFx0XHRcdHJlc3VsdEVsLmh0bWwoJzxzcGFuIHVrLWljb249XCJpY29uOiB3YXJuaW5nOyByYXRpbzogMlwiPjwvc3Bhbj4gPHNwYW4+JyArIGRhdGEubWVzc2FnZSArICc8L3NwYW4+Jyk7XG5cdFx0XHRcdHJlc3VsdEVsWzBdLmNsYXNzTmFtZSA9ICdpbngtdGVhbS1jb250YWN0LWZvcm1fX3Jlc3VsdCBpbngtdGVhbS1jb250YWN0LWZvcm1fX3Jlc3VsdC0tdHlwZS0tZXJyb3IgdWstbWFyZ2luJztcblx0XHRcdH0pLmFsd2F5cyhmdW5jdGlvbiAoeGhyKSB7XG5cdFx0XHRcdHNwaW5uZXIuaGlkZSgpO1xuXHRcdFx0fSk7XG5cdFx0fSk7XG5cdH0sIDIwMDApXG59IC8vIGluaXRcblxuZXhwb3J0IHsgaW5pdCB9XG4iXSwic291cmNlUm9vdCI6IiJ9