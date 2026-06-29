/*
 Copyright 2014 Google Inc. All rights reserved.

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
 */

(function() {

    var cookieName = 'displayCookieConsent';
    var cookieConsentId = 'cookieChoiceInfo';
    var dismissLinkId = 'cookieChoiceDismiss';
    var dismissIconId = 'cookieChoiceDismissIcon';

    var allowedPositions = ['top', 'bottom'];
    var allowedStyles = ['light', 'dark', 'minimal', 'card'];

    function _whitelist(value, allowed, fallback) {
        for (var i = 0; i < allowed.length; i++) {
            if (allowed[i] === value) {
                return value;
            }
        }
        return fallback;
    }

    function showCookieBar() {
        var data = window._wfCookieConsentSettings;
        if (typeof data != 'undefined' && typeof data.wf_linkhref != 'undefined') {
            var appearance = {
                position: _whitelist(data.wf_position, allowedPositions, 'bottom'),
                style: _whitelist(data.wf_style, allowedStyles, 'light'),
                autoDark: !!data.wf_auto_dark
            };
            _showCookieConsent(
                htmlDecode(data.wf_cookietext),
                htmlDecode(data.wf_dismisstext),
                htmlDecode(data.wf_linktext),
                data.wf_linkhref,
                appearance,
                false
            );
        }
    }

    function _createHeaderElement(cookieText, dismissText, linkText, linkHref, appearance) {
        var cookieConsentElement = document.createElement('div');
        var wrapper = document.createElement('div');
        wrapper.className = 'wf-cc__inner';

        cookieConsentElement.id = cookieConsentId;
        cookieConsentElement.className = 'wf-cc wf-cc--' + appearance.position +
            ' wf-cc--theme-' + appearance.style +
            (appearance.autoDark ? ' wf-cc--auto-dark' : '');

        wrapper.appendChild(_createConsentText(cookieText));
        if (!!linkText && !!linkHref) {
            wrapper.appendChild(_createInformationLink(linkText, linkHref));
        }
        wrapper.appendChild(_createDismissLink(dismissText));

        cookieConsentElement.appendChild(wrapper);
        cookieConsentElement.appendChild(_createDismissIcon());

        return cookieConsentElement;
    }

    function _createDialogElement(cookieText, dismissText, linkText, linkHref) {
        var cookieConsentElement = document.createElement('div');
        cookieConsentElement.id = cookieConsentId;
        cookieConsentElement.className = 'wf-cc';

        var glassPanel = document.createElement('div');
        glassPanel.className = 'wf-cc__glass';

        var content = document.createElement('div');
        content.className = 'wf-cc__content';

        var dialog = document.createElement('div');
        dialog.className = 'wf-cc__dialog';

        var dismissLink = _createDismissLink(dismissText);
        dismissLink.className += ' wf-cc__dismiss--block';

        content.appendChild(_createConsentText(cookieText));
        if (!!linkText && !!linkHref) {
            content.appendChild(_createInformationLink(linkText, linkHref));
        }
        content.appendChild(dismissLink);
        dialog.appendChild(content);
        cookieConsentElement.appendChild(glassPanel);
        cookieConsentElement.appendChild(dialog);
        return cookieConsentElement;
    }

    function _setElementText(element, text) {
        // IE8 does not support textContent, so we should fallback to innerText.
        var supportsTextContent = 'textContent' in document.body;

        if (supportsTextContent) {
            element.textContent = text;
        } else {
            element.innerText = text;
        }
    }

    function _createConsentText(cookieText) {
        var consentText = document.createElement('span');
        _setElementText(consentText, cookieText);
        return consentText;
    }

    function _createDismissLink(dismissText) {
        var dismissLink = document.createElement('a');
        _setElementText(dismissLink, dismissText);
        dismissLink.id = dismissLinkId;
        dismissLink.href = '#';
        dismissLink.className = 'wf-cc__dismiss';
        return dismissLink;
    }

    function _createDismissIcon() {
        var dismissIcon = document.createElement('a');
        dismissIcon.id = dismissIconId;
        dismissIcon.href = '#';
        dismissIcon.className = 'wf-cc__close';
        return dismissIcon;
    }

    function _createInformationLink(linkText, linkHref) {
        var infoLink = document.createElement('a');
        _setElementText(infoLink, linkText);
        infoLink.href = linkHref;
        infoLink.target = '_blank';
        infoLink.rel = 'noopener';
        infoLink.className = 'wf-cc__link';
        return infoLink;
    }

    function _dismissLinkClick() {
        _saveUserPreference();
        _removeCookieConsent();
        return false;
    }

    function _showCookieConsent(cookieText, dismissText, linkText, linkHref, appearance, isDialog) {
        if (_shouldDisplayConsent()) {
            _removeCookieConsent();
            var consentElement = (isDialog) ?
                _createDialogElement(cookieText, dismissText, linkText, linkHref) :
                _createHeaderElement(cookieText, dismissText, linkText, linkHref, appearance);
            var fragment = document.createDocumentFragment();
            fragment.appendChild(consentElement);
            document.body.appendChild(fragment.cloneNode(true));
            document.getElementById(dismissLinkId).onclick = _dismissLinkClick;
            document.getElementById(dismissIconId).onclick = _dismissLinkClick;
        }
    }

    function showCookieConsentBar(cookieText, dismissText, linkText, linkHref) {
        _showCookieConsent(cookieText, dismissText, linkText, linkHref, {
            position: 'bottom',
            style: 'light',
            autoDark: false
        }, false);
    }

    function showCookieConsentDialog(cookieText, dismissText, linkText, linkHref) {
        _showCookieConsent(cookieText, dismissText, linkText, linkHref, null, true);
    }

    function _removeCookieConsent() {
        var cookieChoiceElement = document.getElementById(cookieConsentId);
        if (cookieChoiceElement != null) {
            cookieChoiceElement.parentNode.removeChild(cookieChoiceElement);
        }
    }

    function _saveUserPreference() {
        // Set the cookie expiry to one year after today.
        var expiryDate = new Date();
        expiryDate.setFullYear(expiryDate.getFullYear() + 1);
        document.cookie = cookieName + '=y;path=/; expires=' + expiryDate.toGMTString();
    }

    function _shouldDisplayConsent() {
        // Display the header only if the cookie has not been set.
        return !document.cookie.match(new RegExp(cookieName + '=([^;]+)'));
    }

    function htmlDecode(input) {
        var e = document.createElement('textarea');
        e.innerHTML = input;
        return e.childNodes.length === 0 ? "" : e.childNodes[0].nodeValue;
    }


    if (document.addEventListener) {
        document.addEventListener('DOMContentLoaded', showCookieBar);
    } else {
        document.attachEvent('onreadystatechange', function(event) {
            if (document.readyState === "complete") {
                showCookieBar();
            }
        });
    }

})();