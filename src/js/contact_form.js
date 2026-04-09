const $ = jQuery

function init() {
	$('.inx-team-contact-form input[name=consent]').attr('disabled', true)

	setTimeout(() => {
		$('.inx-team-contact-form input[name=consent]').attr('disabled', false)

		$('.inx-team-contact-form input[name=consent]').on('change', function (e) {
			const el = $(this)
			const form = el.parentsUntil('.inx-team-contact-form').parent()
			const submitEl = form.find('.inx-team-contact-form__submit')
			const resultEl = form.find('.inx-team-contact-form__result')

			const tsEl = form.find('.inx-team-contact-form__turnstile')
			const tsWidgetID = tsEl.length > 0 ? tsEl.attr('widget-id') : false

			if (inx_team.enable_ts && el.prop('checked')) {
				if (tsWidgetID === false) {
					initTurnstile(submitEl, resultEl)
				} else {
					turnstile.reset(tsWidgetID)
				}
			} else if ( inx_team.enable_ts && !el.prop('checked') ) {
				hideTurnstileError(resultEl)
				submitEl.attr('disabled', true)
			} else {
				submitEl.attr('disabled', !el.prop('checked'))

			}

			if (!submitEl.attr('disabled')) {
				resultEl[0].className = 'inx-team-contact-form__result uk-margin'
			}
		})

		$('.inx-team-contact-form').on('submit', function (e) {
			e.preventDefault()

			const form = $(this)
			const resultEl = form.find('.inx-team-contact-form__result').first()
			const submitEl = resultEl.parentsUntil('.inx-team-contact-form').parent().find('.inx-team-contact-form__submit')
			const spinner = form.children('.inx-team-contact-form__spinner').first()

			const autofilled = []
			form.find('input[data-com-onepassword-filled],input:autofill').each((index, element) => {
				autofilled.push(element.name)
			})

			const formData = new FormData(form[0])
			formData.append('autofilled', JSON.stringify(autofilled))
			const serializedFormData = new URLSearchParams(formData).toString()

			form.find('.inx-team-contact-form__input--has-error').removeClass('inx-team-contact-form__input--has-error')
			spinner.show()

			$.post(
				form.attr('action'),
				serializedFormData,
				function (response) {
					var data = 'string' === typeof response ? JSON.parse(response.match(/{.*}/)) : response

					form[0].reset()
					form.find('textarea').val('')
					submitEl.attr('disabled', true)

					if (inx_team.hide_form_after_submit) {
						form.find('.inx-team-contact-form__input:not(.inx-team-contact-form__result-wrap)').hide()
					}

					if ( 'undefined' !== typeof response.redirect_url && response.redirect_url ) {
						window.location.href = response.redirect_url
						return
					}

					resultEl.html('<span uk-icon="icon: check; ratio: 2"></span> <span>' + data.message + '</span>')
					resultEl[0].className = 'inx-team-contact-form__result inx-team-contact-form__result--type--success uk-margin'
				},
				'json'
			).fail(function (xhr) {
				const data = xhr.responseJSON

				$.each(data.field_errors, function (fieldName, message) {
					const inputEl = form.find('.inx-team-contact-form__input--name--' + fieldName).first()
					inputEl.children('.inx-team-contact-form__input-error').first().html(message)
					inputEl.addClass('inx-team-contact-form__input--has-error')
				})

				resultEl.html('<span uk-icon="icon: warning; ratio: 2"></span> <span>' + data.message + '</span>')
				resultEl[0].className = 'inx-team-contact-form__result inx-team-contact-form__result--type--error uk-margin'
			}).always(function (xhr) {
				spinner.hide()
			})
		})
	}, 2500)
} // init

function handleTurnstileError(errorCode, resultEl) {
  const errorFamily = Math.floor(errorCode / 1000);

  switch(errorFamily) {
    case 100:
      showError(inx_team.ts_error_msg_refresh, resultEl);
      break;
    case 110:
      showError(inx_team.ts_error_msg_config, resultEl);
      break;
    case 300:
    case 600:
      showError(inx_team.ts_error_msg_security, resultEl);
      break;
    default:
      showError(inx_team.ts_error_msg_unexpected, resultEl);
  }
}

function showError(message, resultEl) {
	resultEl.html('<span uk-icon="icon: warning; ratio: 2"></span> <span>' + message + '</span>')
	resultEl[0].className = 'inx-team-contact-form__result inx-team-contact-form__result--type--error uk-margin'
} // showError

function hideTurnstileError(resultEl) {
	resultEl.html('')
	resultEl[0].className = 'inx-team-contact-form__result'
} // hideTurnstileError

function initTurnstile(submitEl, resultEl) {
	window.inxkicktmRenderTurnstile = function() {
		if (!inx_team.enable_ts || !inx_team.ts_sitekey) return

		const form = submitEl.parentsUntil('.inx-team-contact-form').parent()
		const tsEl = form.find('.inx-team-contact-form__turnstile')[0]

		const tsWidgetID = turnstile.render(tsEl, {
			sitekey: inx_team.ts_sitekey,
			appearance: 'interaction-only',
			retry: 'never',
			callback: function (token) {
				submitEl.attr('disabled', false)
				resultEl[0].className = 'inx-team-contact-form__result uk-margin'
			},
			'error-callback': function(errorCode) {
				submitEl.attr('disabled', true)
				console.error('Turnstile error:', errorCode)
				handleTurnstileError(errorCode, resultEl)
				return true
			}
		})

		tsEl.setAttribute('widget-id', tsWidgetID)
	}

	const form = submitEl.parentsUntil('.inx-team-contact-form').parent()
	const consentEl = form.find('.inx-team-contact-form__consent-text')

	let div = document.createElement('div')
	div.setAttribute('class', 'inx-team-contact-form__turnstile')
	consentEl.append(div)

	const script = document.createElement('script')
	script.setAttribute('src', 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit&onload=inxkicktmRenderTurnstile')
	script.setAttribute('defer', '')
	document.head.appendChild(script)
} // initTurnstile

export { init }
