/**
 * @license
 * Copyright (C) 2010 The Libphonenumber Authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * @fileoverview  Phone Number Parser Demo.
 *
 * @author Nikolaos Trogkanis
 */

goog.require('goog.dom');
goog.require('goog.dom.query');
goog.require('goog.json');
goog.require('goog.proto2.ObjectSerializer');
goog.require('goog.string.StringBuffer');
goog.require('i18n.phonenumbers.AsYouTypeFormatter');
goog.require('i18n.phonenumbers.PhoneNumberFormat');
goog.require('i18n.phonenumbers.PhoneNumberType');
goog.require('i18n.phonenumbers.PhoneNumberUtil');
goog.require('i18n.phonenumbers.PhoneNumberUtil.ValidationResult');

var phone_check_error = false;

var INVALID_COUNTRY_CODE = "You input invalid country code.";
var TOO_SHORT = "You input too short phone number.";
var TOO_LONG = "You input too long phone number."

function phoneNumberParser() {
  var phoneNumber = $('#ClientPhone').val(); //$('ClientPhone').value;
  var regionCode = $('#defaultCountry').val(); //$('defaultCountry').value;
  var carrierCode = $('#carrierCode').val(); //$('carrierCode').value;
  var output = new goog.string.StringBuffer();
  try {
    var phoneUtil = i18n.phonenumbers.PhoneNumberUtil.getInstance();
    var number = phoneUtil.parseAndKeepRawInput(phoneNumber, regionCode);
    var isPossible = phoneUtil.isPossibleNumber(number);
    if (!isPossible) {
      var PNV = i18n.phonenumbers.PhoneNumberUtil.ValidationResult;
      switch (phoneUtil.isPossibleNumberWithReason(number)) {
        case PNV.INVALID_COUNTRY_CODE:
          phone_check_error = INVALID_COUNTRY_CODE;
          break;
        case PNV.TOO_SHORT:
          phone_check_error = TOO_SHORT;
          break;
        case PNV.TOO_LONG:
          phone_check_error = TOO_LONG;
          break;
      }
      // IS_POSSIBLE shouldn't happen, since we only call this if _not_
      // possible.
      phone_check_error = '\nNote: numbers that are not possible have type ' +
          'UNKNOWN, an unknown region, and are considered invalid.';
    } else {
      var isNumberValid = phoneUtil.isValidNumber(number);
    }
    var PNF = i18n.phonenumbers.PhoneNumberFormat;
  } catch (e) {
    //console.log('\n' + e);
  }

  if (isNumberValid)
    return true;
  else
    return false;
}

goog.exportSymbol('phoneNumberParser', phoneNumberParser);
