function encodeForAjax(data) {
	if (data == null) return null;
	return Object.keys(data).map(function (k) {
		return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
	}).join('&');
}

function sendAjaxRequest(method, url, data, handler) {
	let request = new XMLHttpRequest();

	request.open(method, url, true);
	request.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
	request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	request.onreadystatechange = function () {
		if (this.readyState == 4 && this.status == 200) {
			handler(this);
		}
	};
	request.send(encodeForAjax(data));
}

function sendEditQuestionRequest() {
	let question = document.querySelector('#question');
	let id = question.getAttribute('data-id');
	let title = question.querySelector('#title');
	let description = question.querySelector('#description');

	sendAjaxRequest('PATCH', '/questions/' + id, { title: title.value, description: description.value },
		(request) => {
			let parser = new DOMParser();
			let doc = parser.parseFromString(request.responseText, 'text/html');

			question.parentElement.replaceChild(doc.body, question);
		});
}

function showEditQuestionModal() {
	let question = document.querySelector('#question');
	let modal = question.querySelector('#edit');
	
	modal.classList.remove('hidden');
}

function closeEditQuestionModal()
{
	let question = document.querySelector('#question');
	let modal = question.querySelector('#edit');
	
	modal.classList.add('hidden');
}
