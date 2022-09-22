// Lazy loaded Modules
jQuery(document).ready(function($) {
	// Form Processing
	if (document.getElementsByClassName('inx-team-contact-form').length > 0) {
		import(/* webpackChunkName: "contact_form" */ './contact_form').then((module) => { module.init() })
	}
})
