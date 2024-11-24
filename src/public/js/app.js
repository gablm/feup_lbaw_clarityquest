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
	let otherDropdown = document.querySelector('#notification-dropdown');
	otherDropdown.classList.add('hidden');

	let dropdown = document.querySelector('#user-dropdown');
	dropdown.classList.toggle('hidden');
}

function toggleNotificationDropdown() {
	let otherDropdown = document.querySelector('#user-dropdown');
	otherDropdown.classList.add('hidden');

	let dropdown = document.querySelector('#notification-dropdown');
	dropdown.classList.toggle('hidden');
}

function showProfileTab(tab) {
	document.getElementById('questions-section').classList.add('hidden');
	document.getElementById('answers-section').classList.add('hidden');


	document.querySelectorAll('.tab-btn').forEach(btn => {
		btn.classList.remove('bg-blue-100', 'text-blue-600', 'border-blue-600', 'hover:bg-blue-200');
		btn.classList.add('bg-gray-100', 'text-gray-600', 'border-transparent', 'hover:bg-gray-200');
	});

	document.getElementById(`${tab}-section`).classList.remove('hidden');

	let elem = document.getElementById(`${tab}-tab`);
	elem.classList.add('bg-blue-100', 'text-blue-600', 'border-blue-600', 'hover:bg-blue-200');
	elem.classList.remove('bg-gray-100', 'text-gray-600', 'border-transparent', 'hover:bg-gray-200');
}

function showAdminTab(tab) {
	document.getElementById('reports-section').classList.add('hidden');
	document.getElementById('users-section').classList.add('hidden');
	document.getElementById('tags-section').classList.add('hidden');

	document.querySelectorAll('.tab-btn').forEach(btn => {
		btn.classList.remove('bg-blue-100', 'text-blue-600', 'border-blue-600', 'hover:bg-blue-200');
		btn.classList.add('bg-gray-100', 'text-gray-600', 'border-transparent', 'hover:bg-gray-200');
	});

	document.getElementById(`${tab}-section`).classList.remove('hidden');

	let elem = document.getElementById(`${tab}-tab`);
	elem.classList.add('bg-blue-100', 'text-blue-600', 'border-blue-600', 'hover:bg-blue-200');
	elem.classList.remove('bg-gray-100', 'text-gray-600', 'border-transparent', 'hover:bg-gray-200');
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
			closeEditQuestionModal();
		});
}

function showEditQuestionModal() {
	let question = document.querySelector('#question');
	let modal = question.querySelector('#edit');

	modal.classList.remove('hidden');
}

function closeEditQuestionModal() {
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
			if (request.status != 200) {
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

function deleteAnswer(object) {
	let confirmed = confirm('Are you sure you want to delete this answer? This action cannot be undone.');
	if (confirmed == false) return;

	let id = object.getAttribute('data-id');
	let answer = document.querySelector('#answer[data-id="' + id + '"]');
	let answer_count = document.querySelector('#question-answer-count');

	sendAjaxRequest('DELETE', '/answers/' + id, {},
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200) return;

			answer.remove();
			answer_count.textContent = answer_count.textContent - 1;
		});
}

function deleteComment(object) {
	let confirmed = confirm('Are you sure you want to delete this comment? This action cannot be undone.');
	if (confirmed == false) return;

	let id = object.getAttribute('data-id');
	let comment = document.querySelector('#comment[data-id="' + id + '"]');

	sendAjaxRequest('DELETE', '/comments/' + id, {},
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200) return;

			comment.remove();
		});
}

function showEditPostModal(type, id, content) {
	let modal = document.querySelector('#edit-post');
	modal.setAttribute('data-id', id);
	modal.setAttribute('data-type', type);

	let text = modal.querySelector('#text');
	text.value = content;

	let title = modal.querySelector('#edit-title');
	switch (type) {
		case 'comment':
			title.textContent = 'Edit Comment';
			break;
		case 'answer':
			title.textContent = 'Edit Answer';
			break;
	}

	modal.classList.remove('hidden');
	modal.classList.add('flex');
}

function closeEditPostModal() {
	let modal = document.querySelector('#edit-post');
	let text = modal.querySelector('#text');
	let title = modal.querySelector('#edit-title');

	modal.removeAttribute('data-id');
	modal.removeAttribute('data-type');
	modal.classList.add('hidden');
	modal.classList.remove('flex');
	text.value = "";
	title.textContent = "Edit ??";
}

function sendEditPostRequest() {
	let modal = document.querySelector('#edit-post');
	let text = modal.querySelector('#text');
	let id = modal.getAttribute('data-id');
	let type = modal.getAttribute('data-type');
	let route, post;

	switch (type) {
		case 'comment':
			post = document.querySelector('#comment[data-id="' + id + '"]');
			route = '/comments/';
			break;
		case 'answer':
			post = document.querySelector('#answer[data-id="' + id + '"]');
			route = '/answers/';
			break;
	}

	sendAjaxRequest('PATCH', route + id, { text: text.value },
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200) return;

			let parser = new DOMParser();
			let doc = parser.parseFromString(request.responseText, 'text/html');

			post.parentElement.replaceChild(doc.body.firstChild, post);
			closeEditPostModal();
		});
}

function showCreateCommentModal(id) {
	let modal = document.querySelector('#add-comment');
	modal.setAttribute('data-id', id);

	modal.classList.remove('hidden');
	modal.classList.add('flex');
}

function closeCreateCommentModal() {
	let modal = document.querySelector('#add-comment');

	modal.removeAttribute('data-id');
	modal.classList.add('hidden');
	modal.classList.remove('flex');
	text.value = "";
}

function sendCreateCommentRequest() {
	let modal = document.querySelector('#add-comment');
	let text = modal.querySelector('#text');

	let id = modal.getAttribute('data-id');
	let list = document.querySelector(`#comment-list-${id}`)

	sendAjaxRequest('PUT', '/comments/', { id: id, text: text.value },
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200) return;

			let parser = new DOMParser();
			let doc = parser.parseFromString(request.responseText, 'text/html');

			list.prepend(doc.body.firstChild);
			closeCreateCommentModal();
		});
}

function showCreateTagModal() {
	let modal = document.querySelector('#tag-create');

	modal.classList.remove('hidden');
}

function closeCreateTagModal() {
	let modal = document.querySelector('#tag-create');

	modal.classList.add('hidden');
}

function sendCreateTagRequest() {
	let tagList = document.querySelector('#tag-list');
	let text = document.querySelector('#tag-name');

	sendAjaxRequest('PUT', '/tags', { name: text.value },
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200) return;

			let parser = new DOMParser();
			let doc = parser.parseFromString(request.responseText, 'text/html');

			tagList.prepend(doc.body.firstChild);
			closeCreateTagModal();
		});
}
