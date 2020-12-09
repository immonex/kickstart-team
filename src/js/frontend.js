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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy4vc3JjL2pzL3NyYy9jb250YWN0X2Zvcm0uanMiLCJ3ZWJwYWNrOi8vLy4vc3JjL2pzL3NyYy9mcm9udGVuZC5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiO1FBQUE7UUFDQTs7UUFFQTtRQUNBOztRQUVBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBOztRQUVBO1FBQ0E7O1FBRUE7UUFDQTs7UUFFQTtRQUNBO1FBQ0E7OztRQUdBO1FBQ0E7O1FBRUE7UUFDQTs7UUFFQTtRQUNBO1FBQ0E7UUFDQSwwQ0FBMEMsZ0NBQWdDO1FBQzFFO1FBQ0E7O1FBRUE7UUFDQTtRQUNBO1FBQ0Esd0RBQXdELGtCQUFrQjtRQUMxRTtRQUNBLGlEQUFpRCxjQUFjO1FBQy9EOztRQUVBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQSx5Q0FBeUMsaUNBQWlDO1FBQzFFLGdIQUFnSCxtQkFBbUIsRUFBRTtRQUNySTtRQUNBOztRQUVBO1FBQ0E7UUFDQTtRQUNBLDJCQUEyQiwwQkFBMEIsRUFBRTtRQUN2RCxpQ0FBaUMsZUFBZTtRQUNoRDtRQUNBO1FBQ0E7O1FBRUE7UUFDQSxzREFBc0QsK0RBQStEOztRQUVySDtRQUNBOzs7UUFHQTtRQUNBOzs7Ozs7Ozs7Ozs7QUNsRkE7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQSxFQUFFOztBQUVGO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsOENBQThDLEdBQUc7QUFDakQsS0FBSztBQUNMO0FBQ0E7O0FBRUE7O0FBRUE7QUFDQTtBQUNBOztBQUVBLDhDQUE4QztBQUM5QztBQUNBLElBQUk7QUFDSjtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQSxJQUFJOztBQUVKLCtDQUErQztBQUMvQztBQUNBLEdBQUc7QUFDSDtBQUNBLEdBQUc7QUFDSCxFQUFFOztBQUVGLENBQUM7Ozs7Ozs7Ozs7Ozs7QUN2REQ7QUFBQTtBQUFBO0FBQUEiLCJmaWxlIjoic3JjL2pzL2Zyb250ZW5kLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7IGVudW1lcmFibGU6IHRydWUsIGdldDogZ2V0dGVyIH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBkZWZpbmUgX19lc01vZHVsZSBvbiBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnIgPSBmdW5jdGlvbihleHBvcnRzKSB7XG4gXHRcdGlmKHR5cGVvZiBTeW1ib2wgIT09ICd1bmRlZmluZWQnICYmIFN5bWJvbC50b1N0cmluZ1RhZykge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBTeW1ib2wudG9TdHJpbmdUYWcsIHsgdmFsdWU6ICdNb2R1bGUnIH0pO1xuIFx0XHR9XG4gXHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCAnX19lc01vZHVsZScsIHsgdmFsdWU6IHRydWUgfSk7XG4gXHR9O1xuXG4gXHQvLyBjcmVhdGUgYSBmYWtlIG5hbWVzcGFjZSBvYmplY3RcbiBcdC8vIG1vZGUgJiAxOiB2YWx1ZSBpcyBhIG1vZHVsZSBpZCwgcmVxdWlyZSBpdFxuIFx0Ly8gbW9kZSAmIDI6IG1lcmdlIGFsbCBwcm9wZXJ0aWVzIG9mIHZhbHVlIGludG8gdGhlIG5zXG4gXHQvLyBtb2RlICYgNDogcmV0dXJuIHZhbHVlIHdoZW4gYWxyZWFkeSBucyBvYmplY3RcbiBcdC8vIG1vZGUgJiA4fDE6IGJlaGF2ZSBsaWtlIHJlcXVpcmVcbiBcdF9fd2VicGFja19yZXF1aXJlX18udCA9IGZ1bmN0aW9uKHZhbHVlLCBtb2RlKSB7XG4gXHRcdGlmKG1vZGUgJiAxKSB2YWx1ZSA9IF9fd2VicGFja19yZXF1aXJlX18odmFsdWUpO1xuIFx0XHRpZihtb2RlICYgOCkgcmV0dXJuIHZhbHVlO1xuIFx0XHRpZigobW9kZSAmIDQpICYmIHR5cGVvZiB2YWx1ZSA9PT0gJ29iamVjdCcgJiYgdmFsdWUgJiYgdmFsdWUuX19lc01vZHVsZSkgcmV0dXJuIHZhbHVlO1xuIFx0XHR2YXIgbnMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLnIobnMpO1xuIFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkobnMsICdkZWZhdWx0JywgeyBlbnVtZXJhYmxlOiB0cnVlLCB2YWx1ZTogdmFsdWUgfSk7XG4gXHRcdGlmKG1vZGUgJiAyICYmIHR5cGVvZiB2YWx1ZSAhPSAnc3RyaW5nJykgZm9yKHZhciBrZXkgaW4gdmFsdWUpIF9fd2VicGFja19yZXF1aXJlX18uZChucywga2V5LCBmdW5jdGlvbihrZXkpIHsgcmV0dXJuIHZhbHVlW2tleV07IH0uYmluZChudWxsLCBrZXkpKTtcbiBcdFx0cmV0dXJuIG5zO1xuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IFwiLi9zcmMvanMvc3JjL2Zyb250ZW5kLmpzXCIpO1xuIiwialF1ZXJ5KGRvY3VtZW50KS5yZWFkeShmdW5jdGlvbigkKSB7XG5cblx0JCgnLmlueC10ZWFtLWNvbnRhY3QtZm9ybSBpbnB1dFtuYW1lPWNvbnNlbnRdJykub24oJ2NoYW5nZScsIGZ1bmN0aW9uIChlKSB7XG5cdFx0Y29uc3QgZWwgPSAkKHRoaXMpO1xuXHRcdGNvbnN0IHN1Ym1pdEVsID0gZWwucGFyZW50c1VudGlsKCcuaW54LXRlYW0tY29udGFjdC1mb3JtJykucGFyZW50KCkuZmluZCgnLmlueC10ZWFtLWNvbnRhY3QtZm9ybV9fc3VibWl0Jyk7XG5cdFx0c3VibWl0RWwuYXR0cignZGlzYWJsZWQnLCAhZWwucHJvcCgnY2hlY2tlZCcpKTtcblx0fSk7XG5cblx0JCgnLmlueC10ZWFtLWNvbnRhY3QtZm9ybScpLm9uKCdzdWJtaXQnLCBmdW5jdGlvbiAoZSkge1xuXHRcdGUucHJldmVudERlZmF1bHQoKTtcblxuXHRcdHZhciBmb3JtID0gJCh0aGlzKTtcblx0XHR2YXIgcmVzdWx0RWwgPSBmb3JtLmZpbmQoJy5pbngtdGVhbS1jb250YWN0LWZvcm1fX3Jlc3VsdCcpLmZpcnN0KCk7XG5cdFx0dmFyIHNwaW5uZXIgPSBmb3JtLmNoaWxkcmVuKCcuaW54LXRlYW0tY29udGFjdC1mb3JtX19zcGlubmVyJykuZmlyc3QoKTtcblxuXHRcdGZvcm0uZmluZCgnLmlueC10ZWFtLWNvbnRhY3QtZm9ybV9faW5wdXQtLWhhcy1lcnJvcicpLnJlbW92ZUNsYXNzKCdpbngtdGVhbS1jb250YWN0LWZvcm1fX2lucHV0LS1oYXMtZXJyb3InKTtcblx0XHRyZXN1bHRFbFswXS5jbGFzc05hbWUgPSAnaW54LXRlYW0tY29udGFjdC1mb3JtX19yZXN1bHQgdWstbWFyZ2luJztcblx0XHRzcGlubmVyLnNob3coKTtcblxuXHRcdCQucG9zdChcblx0XHRcdGZvcm0uYXR0cignYWN0aW9uJyksXG5cdFx0XHRmb3JtLnNlcmlhbGl6ZSgpLFxuXHRcdFx0ZnVuY3Rpb24gKHJlc3BvbnNlKSB7XG5cdFx0XHRcdGlmICgnc3RyaW5nJyA9PT0gdHlwZW9mIHJlc3BvbnNlKSB7XG5cdFx0XHRcdFx0dmFyIGRhdGEgPSBKU09OLnBhcnNlKCByZXNwb25zZS5tYXRjaCggL3suKn0vICkgKTtcblx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHR2YXIgZGF0YSA9IHJlc3BvbnNlO1xuXHRcdFx0XHR9XG5cblx0XHRcdFx0Zm9ybVswXS5yZXNldCgpO1xuXG5cdFx0XHRcdGlmIChpbnhfdGVhbS5oaWRlX2Zvcm1fYWZ0ZXJfc3VibWl0KSB7XG5cdFx0XHRcdFx0Zm9ybS5maW5kKCcuaW54LXRlYW0tY29udGFjdC1mb3JtX19pbnB1dDpub3QoLmlueC10ZWFtLWNvbnRhY3QtZm9ybV9fcmVzdWx0LXdyYXApJykuaGlkZSgpO1xuXHRcdFx0XHR9XG5cblx0XHRcdFx0cmVzdWx0RWwuaHRtbCgnPHNwYW4gdWstaWNvbj1cImljb246IGNoZWNrOyByYXRpbzogMlwiPjwvc3Bhbj4gPHNwYW4+JyArIGRhdGEubWVzc2FnZSArICc8L3NwYW4+Jyk7XG5cdFx0XHRcdHJlc3VsdEVsWzBdLmNsYXNzTmFtZSA9ICdpbngtdGVhbS1jb250YWN0LWZvcm1fX3Jlc3VsdCBpbngtdGVhbS1jb250YWN0LWZvcm1fX3Jlc3VsdC0tdHlwZS0tc3VjY2VzcyB1ay1tYXJnaW4nO1xuXHRcdFx0fSxcblx0XHRcdCdqc29uJ1xuXHRcdCkuZmFpbChmdW5jdGlvbiAoeGhyKSB7XG5cdFx0XHRjb25zdCBkYXRhID0geGhyLnJlc3BvbnNlSlNPTjtcblxuXHRcdFx0JC5lYWNoKGRhdGEuZmllbGRfZXJyb3JzLCBmdW5jdGlvbiAoZmllbGROYW1lLCBtZXNzYWdlKSB7XG5cdFx0XHRcdGNvbnN0IGlucHV0RWwgPSBmb3JtLmZpbmQoJy5pbngtdGVhbS1jb250YWN0LWZvcm1fX2lucHV0LS1uYW1lLS0nICsgZmllbGROYW1lKS5maXJzdCgpO1xuXHRcdFx0XHRpbnB1dEVsLmNoaWxkcmVuKCcuaW54LXRlYW0tY29udGFjdC1mb3JtX19pbnB1dC1lcnJvcicpLmZpcnN0KCkuaHRtbChtZXNzYWdlKTtcblx0XHRcdFx0aW5wdXRFbC5hZGRDbGFzcygnaW54LXRlYW0tY29udGFjdC1mb3JtX19pbnB1dC0taGFzLWVycm9yJyk7XG5cdFx0XHR9KTtcblxuXHRcdFx0cmVzdWx0RWwuaHRtbCgnPHNwYW4gdWstaWNvbj1cImljb246IHdhcm5pbmc7IHJhdGlvOiAyXCI+PC9zcGFuPiA8c3Bhbj4nICsgZGF0YS5tZXNzYWdlICsgJzwvc3Bhbj4nKTtcblx0XHRcdHJlc3VsdEVsWzBdLmNsYXNzTmFtZSA9ICdpbngtdGVhbS1jb250YWN0LWZvcm1fX3Jlc3VsdCBpbngtdGVhbS1jb250YWN0LWZvcm1fX3Jlc3VsdC0tdHlwZS0tZXJyb3IgdWstbWFyZ2luJztcblx0XHR9KS5hbHdheXMoZnVuY3Rpb24gKHhocikge1xuXHRcdFx0c3Bpbm5lci5oaWRlKCk7XG5cdFx0fSk7XG5cdH0pO1xuXG59KTtcbiIsIi8vIEZvcm0gUHJvY2Vzc2luZ1xuaW1wb3J0ICcuL2NvbnRhY3RfZm9ybS5qcyciXSwic291cmNlUm9vdCI6IiJ9