/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/src/frontend.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/src/contact_form.js":
/*!************************************!*\
  !*** ./src/js/src/contact_form.js ***!
  \************************************/
/*! no static exports found */
/***/ (function(module, exports) {

jQuery(document).ready(function($) {

	$('.inx-team-contact-form input[name=consent]').change(function (e) {
		const el = $(this);
		const submitEl = el.parentsUntil('.inx-team-contact-form').parent().find('.inx-team-contact-form__submit');

		submitEl.attr('disabled', !el.attr('checked'));
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

});


/***/ }),

/***/ "./src/js/src/frontend.js":
/*!********************************!*\
  !*** ./src/js/src/frontend.js ***!
  \********************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _contact_form_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./contact_form.js */ "./src/js/src/contact_form.js");
/* harmony import */ var _contact_form_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_contact_form_js__WEBPACK_IMPORTED_MODULE_0__);
// Form Processing


/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy4vc3JjL2pzL3NyYy9jb250YWN0X2Zvcm0uanMiLCJ3ZWJwYWNrOi8vLy4vc3JjL2pzL3NyYy9mcm9udGVuZC5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiO1FBQUE7UUFDQTs7UUFFQTtRQUNBOztRQUVBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBOztRQUVBO1FBQ0E7O1FBRUE7UUFDQTs7UUFFQTtRQUNBO1FBQ0E7OztRQUdBO1FBQ0E7O1FBRUE7UUFDQTs7UUFFQTtRQUNBO1FBQ0E7UUFDQSwwQ0FBMEMsZ0NBQWdDO1FBQzFFO1FBQ0E7O1FBRUE7UUFDQTtRQUNBO1FBQ0Esd0RBQXdELGtCQUFrQjtRQUMxRTtRQUNBLGlEQUFpRCxjQUFjO1FBQy9EOztRQUVBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQSx5Q0FBeUMsaUNBQWlDO1FBQzFFLGdIQUFnSCxtQkFBbUIsRUFBRTtRQUNySTtRQUNBOztRQUVBO1FBQ0E7UUFDQTtRQUNBLDJCQUEyQiwwQkFBMEIsRUFBRTtRQUN2RCxpQ0FBaUMsZUFBZTtRQUNoRDtRQUNBO1FBQ0E7O1FBRUE7UUFDQSxzREFBc0QsK0RBQStEOztRQUVySDtRQUNBOzs7UUFHQTtRQUNBOzs7Ozs7Ozs7Ozs7QUNsRkE7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0EsRUFBRTs7QUFFRjtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLDhDQUE4QyxHQUFHO0FBQ2pELEtBQUs7QUFDTDtBQUNBOztBQUVBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQSw4Q0FBOEM7QUFDOUM7QUFDQSxJQUFJO0FBQ0o7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsSUFBSTs7QUFFSiwrQ0FBK0M7QUFDL0M7QUFDQSxHQUFHO0FBQ0g7QUFDQSxHQUFHO0FBQ0gsRUFBRTs7QUFFRixDQUFDOzs7Ozs7Ozs7Ozs7O0FDeEREO0FBQUE7QUFBQTtBQUFBIiwiZmlsZSI6InNyYy9qcy9mcm9udGVuZC5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwgeyBlbnVtZXJhYmxlOiB0cnVlLCBnZXQ6IGdldHRlciB9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZGVmaW5lIF9fZXNNb2R1bGUgb24gZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yID0gZnVuY3Rpb24oZXhwb3J0cykge1xuIFx0XHRpZih0eXBlb2YgU3ltYm9sICE9PSAndW5kZWZpbmVkJyAmJiBTeW1ib2wudG9TdHJpbmdUYWcpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgU3ltYm9sLnRvU3RyaW5nVGFnLCB7IHZhbHVlOiAnTW9kdWxlJyB9KTtcbiBcdFx0fVxuIFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgJ19fZXNNb2R1bGUnLCB7IHZhbHVlOiB0cnVlIH0pO1xuIFx0fTtcblxuIFx0Ly8gY3JlYXRlIGEgZmFrZSBuYW1lc3BhY2Ugb2JqZWN0XG4gXHQvLyBtb2RlICYgMTogdmFsdWUgaXMgYSBtb2R1bGUgaWQsIHJlcXVpcmUgaXRcbiBcdC8vIG1vZGUgJiAyOiBtZXJnZSBhbGwgcHJvcGVydGllcyBvZiB2YWx1ZSBpbnRvIHRoZSBuc1xuIFx0Ly8gbW9kZSAmIDQ6IHJldHVybiB2YWx1ZSB3aGVuIGFscmVhZHkgbnMgb2JqZWN0XG4gXHQvLyBtb2RlICYgOHwxOiBiZWhhdmUgbGlrZSByZXF1aXJlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnQgPSBmdW5jdGlvbih2YWx1ZSwgbW9kZSkge1xuIFx0XHRpZihtb2RlICYgMSkgdmFsdWUgPSBfX3dlYnBhY2tfcmVxdWlyZV9fKHZhbHVlKTtcbiBcdFx0aWYobW9kZSAmIDgpIHJldHVybiB2YWx1ZTtcbiBcdFx0aWYoKG1vZGUgJiA0KSAmJiB0eXBlb2YgdmFsdWUgPT09ICdvYmplY3QnICYmIHZhbHVlICYmIHZhbHVlLl9fZXNNb2R1bGUpIHJldHVybiB2YWx1ZTtcbiBcdFx0dmFyIG5zID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yKG5zKTtcbiBcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KG5zLCAnZGVmYXVsdCcsIHsgZW51bWVyYWJsZTogdHJ1ZSwgdmFsdWU6IHZhbHVlIH0pO1xuIFx0XHRpZihtb2RlICYgMiAmJiB0eXBlb2YgdmFsdWUgIT0gJ3N0cmluZycpIGZvcih2YXIga2V5IGluIHZhbHVlKSBfX3dlYnBhY2tfcmVxdWlyZV9fLmQobnMsIGtleSwgZnVuY3Rpb24oa2V5KSB7IHJldHVybiB2YWx1ZVtrZXldOyB9LmJpbmQobnVsbCwga2V5KSk7XG4gXHRcdHJldHVybiBucztcbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSBcIi4vc3JjL2pzL3NyYy9mcm9udGVuZC5qc1wiKTtcbiIsImpRdWVyeShkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24oJCkge1xuXG5cdCQoJy5pbngtdGVhbS1jb250YWN0LWZvcm0gaW5wdXRbbmFtZT1jb25zZW50XScpLmNoYW5nZShmdW5jdGlvbiAoZSkge1xuXHRcdGNvbnN0IGVsID0gJCh0aGlzKTtcblx0XHRjb25zdCBzdWJtaXRFbCA9IGVsLnBhcmVudHNVbnRpbCgnLmlueC10ZWFtLWNvbnRhY3QtZm9ybScpLnBhcmVudCgpLmZpbmQoJy5pbngtdGVhbS1jb250YWN0LWZvcm1fX3N1Ym1pdCcpO1xuXG5cdFx0c3VibWl0RWwuYXR0cignZGlzYWJsZWQnLCAhZWwuYXR0cignY2hlY2tlZCcpKTtcblx0fSk7XG5cblx0JCgnLmlueC10ZWFtLWNvbnRhY3QtZm9ybScpLm9uKCdzdWJtaXQnLCBmdW5jdGlvbiAoZSkge1xuXHRcdGUucHJldmVudERlZmF1bHQoKTtcblxuXHRcdHZhciBmb3JtID0gJCh0aGlzKTtcblx0XHR2YXIgcmVzdWx0RWwgPSBmb3JtLmZpbmQoJy5pbngtdGVhbS1jb250YWN0LWZvcm1fX3Jlc3VsdCcpLmZpcnN0KCk7XG5cdFx0dmFyIHNwaW5uZXIgPSBmb3JtLmNoaWxkcmVuKCcuaW54LXRlYW0tY29udGFjdC1mb3JtX19zcGlubmVyJykuZmlyc3QoKTtcblxuXHRcdGZvcm0uZmluZCgnLmlueC10ZWFtLWNvbnRhY3QtZm9ybV9faW5wdXQtLWhhcy1lcnJvcicpLnJlbW92ZUNsYXNzKCdpbngtdGVhbS1jb250YWN0LWZvcm1fX2lucHV0LS1oYXMtZXJyb3InKTtcblx0XHRyZXN1bHRFbFswXS5jbGFzc05hbWUgPSAnaW54LXRlYW0tY29udGFjdC1mb3JtX19yZXN1bHQgdWstbWFyZ2luJztcblx0XHRzcGlubmVyLnNob3coKTtcblxuXHRcdCQucG9zdChcblx0XHRcdGZvcm0uYXR0cignYWN0aW9uJyksXG5cdFx0XHRmb3JtLnNlcmlhbGl6ZSgpLFxuXHRcdFx0ZnVuY3Rpb24gKHJlc3BvbnNlKSB7XG5cdFx0XHRcdGlmICgnc3RyaW5nJyA9PT0gdHlwZW9mIHJlc3BvbnNlKSB7XG5cdFx0XHRcdFx0dmFyIGRhdGEgPSBKU09OLnBhcnNlKCByZXNwb25zZS5tYXRjaCggL3suKn0vICkgKTtcblx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHR2YXIgZGF0YSA9IHJlc3BvbnNlO1xuXHRcdFx0XHR9XG5cblx0XHRcdFx0Zm9ybVswXS5yZXNldCgpO1xuXG5cdFx0XHRcdGlmIChpbnhfdGVhbS5oaWRlX2Zvcm1fYWZ0ZXJfc3VibWl0KSB7XG5cdFx0XHRcdFx0Zm9ybS5maW5kKCcuaW54LXRlYW0tY29udGFjdC1mb3JtX19pbnB1dDpub3QoLmlueC10ZWFtLWNvbnRhY3QtZm9ybV9fcmVzdWx0LXdyYXApJykuaGlkZSgpO1xuXHRcdFx0XHR9XG5cblx0XHRcdFx0cmVzdWx0RWwuaHRtbCgnPHNwYW4gdWstaWNvbj1cImljb246IGNoZWNrOyByYXRpbzogMlwiPjwvc3Bhbj4gPHNwYW4+JyArIGRhdGEubWVzc2FnZSArICc8L3NwYW4+Jyk7XG5cdFx0XHRcdHJlc3VsdEVsWzBdLmNsYXNzTmFtZSA9ICdpbngtdGVhbS1jb250YWN0LWZvcm1fX3Jlc3VsdCBpbngtdGVhbS1jb250YWN0LWZvcm1fX3Jlc3VsdC0tdHlwZS0tc3VjY2VzcyB1ay1tYXJnaW4nO1xuXHRcdFx0fSxcblx0XHRcdCdqc29uJ1xuXHRcdCkuZmFpbChmdW5jdGlvbiAoeGhyKSB7XG5cdFx0XHRjb25zdCBkYXRhID0geGhyLnJlc3BvbnNlSlNPTjtcblxuXHRcdFx0JC5lYWNoKGRhdGEuZmllbGRfZXJyb3JzLCBmdW5jdGlvbiAoZmllbGROYW1lLCBtZXNzYWdlKSB7XG5cdFx0XHRcdGNvbnN0IGlucHV0RWwgPSBmb3JtLmZpbmQoJy5pbngtdGVhbS1jb250YWN0LWZvcm1fX2lucHV0LS1uYW1lLS0nICsgZmllbGROYW1lKS5maXJzdCgpO1xuXHRcdFx0XHRpbnB1dEVsLmNoaWxkcmVuKCcuaW54LXRlYW0tY29udGFjdC1mb3JtX19pbnB1dC1lcnJvcicpLmZpcnN0KCkuaHRtbChtZXNzYWdlKTtcblx0XHRcdFx0aW5wdXRFbC5hZGRDbGFzcygnaW54LXRlYW0tY29udGFjdC1mb3JtX19pbnB1dC0taGFzLWVycm9yJyk7XG5cdFx0XHR9KTtcblxuXHRcdFx0cmVzdWx0RWwuaHRtbCgnPHNwYW4gdWstaWNvbj1cImljb246IHdhcm5pbmc7IHJhdGlvOiAyXCI+PC9zcGFuPiA8c3Bhbj4nICsgZGF0YS5tZXNzYWdlICsgJzwvc3Bhbj4nKTtcblx0XHRcdHJlc3VsdEVsWzBdLmNsYXNzTmFtZSA9ICdpbngtdGVhbS1jb250YWN0LWZvcm1fX3Jlc3VsdCBpbngtdGVhbS1jb250YWN0LWZvcm1fX3Jlc3VsdC0tdHlwZS0tZXJyb3IgdWstbWFyZ2luJztcblx0XHR9KS5hbHdheXMoZnVuY3Rpb24gKHhocikge1xuXHRcdFx0c3Bpbm5lci5oaWRlKCk7XG5cdFx0fSk7XG5cdH0pO1xuXG59KTtcbiIsIi8vIEZvcm0gUHJvY2Vzc2luZ1xuaW1wb3J0ICcuL2NvbnRhY3RfZm9ybS5qcyciXSwic291cmNlUm9vdCI6IiJ9