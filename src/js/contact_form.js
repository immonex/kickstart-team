const $ = jQuery

function init() {
	setTimeout(() => {
		$('.inx-team-contact-form input[name=consent]').on('change', function (e) {
			const el = $(this)
			const submitEl = el.parentsUntil('.inx-team-contact-form').parent().find('.inx-team-contact-form__submit')
			const resultEl = el.parentsUntil('.inx-team-contact-form').parent().find('.inx-team-contact-form__result')

			submitEl.attr('disabled', !el.prop('checked'))
			if (!submitEl.attr('disabled')) {
				resultEl[0].className = 'inx-team-contact-form__result uk-margin'
			}
		});

		$('.inx-team-contact-form').on('submit', function (e) {
			e.preventDefault()

			var form = $(this)
			var resultEl = form.find('.inx-team-contact-form__result').first()
			var submitEl = resultEl.parentsUntil('.inx-team-contact-form').parent().find('.inx-team-contact-form__submit')
			var spinner = form.children('.inx-team-contact-form__spinner').first()

			const autofilled = []
			form.find('input[data-com-onepassword-filled]').each((index, element) => {
				autofilled.push(element.name)
			})

			const formData = new FormData(form[0])
			formData.append('autofilled', JSON.stringify(autofilled))
			const serializedFormData = new URLSearchParams(formData).toString()

			form.find('.inx-team-contact-form__input--has-error').removeClass('inx-team-contact-form__input--has-error')
			resultEl[0].className = 'inx-team-contact-form__result uk-margin'
			spinner.show()

			$.post(
				form.attr('action'),
				serializedFormData,
				function (response) {
					var data = 'string' === typeof response ? JSON.parse( response.match( /{.*}/ ) ) : response

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

export { init }
