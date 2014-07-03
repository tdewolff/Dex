function escapeHtml(s) {
	return s
		 .replace(/&/g, "&amp;")
		 .replace(/</g, "&lt;")
		 .replace(/>/g, "&gt;")
		 .replace(/"/g, "&quot;")
		 .replace(/'/g, "&#039;");
 }

function ajaxRetrieveError(data) {
	if (typeof data['responseJSON'] !== 'undefined' && typeof data['responseJSON']['formatted_error'] !== 'undefined') { // PHP error but still handled by API
		return data['responseJSON']['formatted_error'].join('<br>');
	} else if (typeof data['responseText'] !== 'undefined') { // Non-JSON response
		return '<p class="error">' + escapeHtml(data['responseText']) + '</p>';
	} else if (typeof data['statusText'] !== 'undefined') { // Some XHR thing went wrong
		return '<p class="error">' + escapeHtml(data['statusText']) + '</p>';
	} else { // ...shrugs
		return '<p class="error">' + escapeHtml(data) + '</p>';
	}
}