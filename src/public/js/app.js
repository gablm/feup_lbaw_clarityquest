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
		handler(this);
	};
	request.send(encodeForAjax(data));
}

function toggleUserDropdown() {
	let dropdown = document.querySelector('#user-dropdown');
	dropdown.classList.toggle('hidden');
}

function sendEditQuestionRequest() {
	let question = document.querySelector('#question');
	let id = question.getAttribute('data-id');
	let title = question.querySelector('#title');
	let description = question.querySelector('#description');

	sendAjaxRequest('PATCH', '/questions/' + id, { title: title.value, description: description.value },
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200) return;
			
			let parser = new DOMParser();
			let doc = parser.parseFromString(request.responseText, 'text/html');

			question.parentElement.replaceChild(doc.body.firstChild, question);
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

function sendCreateAnswerRequest() {
	let question = document.querySelector('#question');
	let id = question.getAttribute('data-id');
	let answerList = document.querySelector('#answer-list');
	let text = document.querySelector('#answer-text');
	let errorBox = document.querySelector("#answer-create-err");
	let answer_count = document.querySelector('#question-answer-count');

	sendAjaxRequest('PUT', '/answers', { id: id, text: text.value },
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200)
			{
				errorBox.classList.remove('hidden');
				return;
			}

			let parser = new DOMParser();
			let doc = parser.parseFromString(request.responseText, 'text/html');

			answerList.prepend(doc.body.firstChild);
			text.value = "";
			errorBox.classList.add('hidden');
			answer_count.textContent = Number(answer_count.textContent) + 1;
		});
}

function deleteAnswer(object)
{
	let confirmed = confirm('Are you sure you want to delete this answer? This action cannot be undone.');
	if (confirmed == false) return;

	let id = object.getAttribute('data-id');
	let answer = document.querySelector('#answer[data-id="' + id + '"]');
	let answer_count = document.querySelector('#question-answer-count');

	sendAjaxRequest('DELETE', '/answers/' + id, { },
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200) return;

			answer.remove();
			answer_count.textContent= answer_count.textContent - 1;
		});
}

function deleteComment(object)
{
	let confirmed = confirm('Are you sure you want to delete this comment? This action cannot be undone.');
	if (confirmed == false) return;

	let id = object.getAttribute('data-id');
	let comment = document.querySelector('#comment[data-id="' + id + '"]');

	sendAjaxRequest('DELETE', '/comments/' + id, { },
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200) return;

			comment.remove();
		});
}

function showEditAnswerModal(id) {
	let answer = document.querySelector('#answer[data-id="' + id + '"]');
	let modal = answer.querySelector('#answer-edit');
	
	modal.classList.remove('hidden');
}

function closeEditAnswerModal()
{
	let modal = document.querySelector('#answer-edit:not(.hidden)');
	
	modal.classList.add('hidden');
}

function sendEditAnswerRequest(id) {
	let answer = document.querySelector('#answer[data-id="' + id + '"]');
	let text = answer.querySelector('#text');

	sendAjaxRequest('PATCH', '/answers/' + id, { text: text.value },
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200) return;
			
			let parser = new DOMParser();
			let doc = parser.parseFromString(request.responseText, 'text/html');

			answer.parentElement.replaceChild(doc.body.firstChild, answer);
		});
}

function showEditCommentModal(id) {
	let answer = document.querySelector('#comment[data-id="' + id + '"]');
	let modal = answer.querySelector('#comment-edit');
	
	modal.classList.remove('hidden');
}

function closeEditCommentModal()
{
	let modal = document.querySelector('#comment-edit:not(.hidden)');
	
	modal.classList.add('hidden');
}

function sendEditCommentRequest(id) {
	let answer = document.querySelector('#comment[data-id="' + id + '"]');
	let text = answer.querySelector('#text');

	sendAjaxRequest('PATCH', '/comments/' + id, { text: text.value },
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200) return;
			
			let parser = new DOMParser();
			let doc = parser.parseFromString(request.responseText, 'text/html');

			answer.parentElement.replaceChild(doc.body.firstChild, answer);
		});
}