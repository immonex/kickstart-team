(self.webpackChunk_immonex_kickstart_team=self.webpackChunk_immonex_kickstart_team||[]).push([[847],{266:(t,n,a)=>{"use strict";a.r(n),a.d(n,{init:()=>i});const e=jQuery;function i(){setTimeout((()=>{e(".inx-team-contact-form input[name=consent]").on("change",(function(t){const n=e(this);n.parentsUntil(".inx-team-contact-form").parent().find(".inx-team-contact-form__submit").attr("disabled",!n.prop("checked"))})),e(".inx-team-contact-form").on("submit",(function(t){t.preventDefault();var n=e(this),a=n.find(".inx-team-contact-form__result").first(),i=n.children(".inx-team-contact-form__spinner").first();n.find(".inx-team-contact-form__input--has-error").removeClass("inx-team-contact-form__input--has-error"),a[0].className="inx-team-contact-form__result uk-margin",i.show(),e.post(n.attr("action"),n.serialize(),(function(t){if("string"==typeof t)var e=JSON.parse(t.match(/{.*}/));else e=t;n[0].reset(),inx_team.hide_form_after_submit&&n.find(".inx-team-contact-form__input:not(.inx-team-contact-form__result-wrap)").hide(),a.html('<span uk-icon="icon: check; ratio: 2"></span> <span>'+e.message+"</span>"),a[0].className="inx-team-contact-form__result inx-team-contact-form__result--type--success uk-margin"}),"json").fail((function(t){const i=t.responseJSON;e.each(i.field_errors,(function(t,a){const e=n.find(".inx-team-contact-form__input--name--"+t).first();e.children(".inx-team-contact-form__input-error").first().html(a),e.addClass("inx-team-contact-form__input--has-error")})),a.html('<span uk-icon="icon: warning; ratio: 2"></span> <span>'+i.message+"</span>"),a[0].className="inx-team-contact-form__result inx-team-contact-form__result--type--error uk-margin"})).always((function(t){i.hide()}))}))}),2500)}}}]);