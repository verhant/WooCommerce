/**
 * Verhant DPP Admin JavaScript
 *
 * Handles AJAX calls for settings validation, import, and export.
 *
 * @package Verhant_DPP
 */

(function () {
	'use strict';

	/**
	 * Make an AJAX request to the Verhant backend.
	 *
	 * @param {string}   action   WordPress AJAX action name.
	 * @param {Object}   data     Additional POST data.
	 * @param {Function} callback Callback receiving (success, data).
	 */
	function verhantAjax(action, data, callback) {
		var formData = new FormData();
		formData.append('action', action);
		formData.append('nonce', verhantDpp.nonce);

		if (data) {
			Object.keys(data).forEach(function (key) {
				formData.append(key, data[key]);
			});
		}

		var xhr = new XMLHttpRequest();
		xhr.open('POST', verhantDpp.ajax_url, true);
		xhr.onload = function () {
			if (xhr.status >= 200 && xhr.status < 300) {
				var response;
				try {
					response = JSON.parse(xhr.responseText);
				} catch (e) {
					callback(false, { message: verhantDpp.i18n.error });
					return;
				}
				callback(response.success, response.data);
			} else {
				callback(false, { message: verhantDpp.i18n.error });
			}
		};
		xhr.onerror = function () {
			callback(false, { message: verhantDpp.i18n.error });
		};
		xhr.send(formData);
	}

	/**
	 * Show a result message in a target element.
	 *
	 * @param {HTMLElement} el      Target element.
	 * @param {string}      message Message HTML.
	 * @param {string}      type    'success' or 'error'.
	 */
	function showResult(el, message, type) {
		el.style.display = 'block';
		el.className = 'verhant-result verhant-result-' + type;
		el.innerHTML = message;
	}

	document.addEventListener('DOMContentLoaded', function () {

		// Validate token button.
		var validateBtn = document.getElementById('verhant-validate-token');
		var validateResult = document.getElementById('verhant-validation-result');

		if (validateBtn) {
			validateBtn.addEventListener('click', function () {
				validateBtn.disabled = true;
				validateBtn.textContent = verhantDpp.i18n.validating;

				verhantAjax('verhant_validate_token', {}, function (success, data) {
					validateBtn.disabled = false;
					validateBtn.textContent = verhantDpp.i18n.success;

					if (success) {
						showResult(
							validateResult,
							verhantDpp.i18n.connected_as + ' <strong>' + data.email + '</strong> (' + verhantDpp.i18n.plan + ': ' + data.plan + ')',
							'success'
						);
					} else {
						showResult(validateResult, data.message, 'error');
					}

					setTimeout(function () {
						validateBtn.textContent = document.documentElement.lang === 'it-IT' ? 'Verifica connessione' : 'Verify Connection';
					}, 2000);
				});
			});
		}

		// Import button.
		var importBtn = document.getElementById('verhant-import-btn');
		var importResult = document.getElementById('verhant-import-result');

		if (importBtn) {
			importBtn.addEventListener('click', function () {
				importBtn.disabled = true;
				importBtn.textContent = verhantDpp.i18n.importing;

				verhantAjax('verhant_sync_import', {}, function (success, data) {
					importBtn.disabled = false;
					importBtn.textContent = document.documentElement.lang === 'it-IT' ? 'Avvia import' : 'Start Import';

					if (success) {
						showResult(
							importResult,
							data.imported + ' ' + verhantDpp.i18n.imported + ', ' +
							data.updated + ' ' + verhantDpp.i18n.updated + ', ' +
							data.errors + ' ' + verhantDpp.i18n.errors,
							'success'
						);
					} else {
						showResult(importResult, data.message, 'error');
					}
				});
			});
		}

		// Export button.
		var exportBtn = document.getElementById('verhant-export-btn');
		var exportResult = document.getElementById('verhant-export-result');

		if (exportBtn) {
			exportBtn.addEventListener('click', function () {
				exportBtn.disabled = true;
				exportBtn.textContent = verhantDpp.i18n.exporting;

				verhantAjax('verhant_sync_export', {}, function (success, data) {
					exportBtn.disabled = false;
					exportBtn.textContent = document.documentElement.lang === 'it-IT' ? 'Avvia export' : 'Start Export';

					if (success) {
						showResult(
							exportResult,
							data.updated + ' ' + verhantDpp.i18n.products_updated + ', ' +
							data.skipped + ' ' + verhantDpp.i18n.skipped,
							'success'
						);
					} else {
						showResult(exportResult, data.message, 'error');
					}
				});
			});
		}

		// Auto-sync toggle.
		var autoSyncCheckbox = document.getElementById('verhant-auto-sync');
		var autoSyncResult = document.getElementById('verhant-auto-sync-result');

		if (autoSyncCheckbox) {
			autoSyncCheckbox.addEventListener('change', function () {
				verhantAjax('verhant_save_auto_sync', { enabled: autoSyncCheckbox.checked ? 'true' : 'false' }, function (success, data) {
					if (success) {
						showResult(autoSyncResult, verhantDpp.i18n.success, 'success');
					} else {
						showResult(autoSyncResult, data.message, 'error');
					}
					setTimeout(function () {
						autoSyncResult.style.display = 'none';
					}, 2000);
				});
			});
		}
	});
})();
