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
	document.getElementById('medals-section').classList.add('hidden');

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
	let question = document.querySelector('.question');
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
	let question = document.querySelector('.question');
	let modal = question.querySelector('#edit-question');

	modal.classList.remove('hidden');
	modal.classList.add('flex');
}

function closeEditQuestionModal() {
	let question = document.querySelector('.question');
	let modal = question.querySelector('#edit-question');

	modal.classList.add('hidden');
	modal.classList.remove('flex');
}

function sendCreateAnswerRequest() {
	let question = document.querySelector('.question');
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
	let answer = document.querySelector('.answer[data-id="' + id + '"]');
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
	let comment = document.querySelector('.comment[data-id="' + id + '"]');

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
			post = document.querySelector('.comment[data-id="' + id + '"]');
			route = '/comments/';
			break;
		case 'answer':
			post = document.querySelector('.answer[data-id="' + id + '"]');
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
	let list = document.querySelector(`#comment-list-${id}`);

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
	let text = document.querySelector('#tag-name');

	modal.removeAttribute('data-id');
	modal.classList.remove('hidden');
	modal.classList.add('flex');
	text.value = "";
}

function closeCreateTagModal() {
	let modal = document.querySelector('#tag-create');
	let text = document.querySelector('#tag-name');

	modal.removeAttribute('data-id');
	modal.classList.add('hidden');
	modal.classList.remove('flex');
	text.value = "";
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
			text.value = "";
			closeCreateTagModal();
		});
}
/*
function sendVoteRequest(id, positive) {
	let count = document.querySelector(`#votes-${id}`);
	sendAjaxRequest('POST', '/posts/' + id, { positive: positive ? "true" : "false" },
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200) return;

			count.textContent = JSON.parse(request.responseText).votes;
		});
}*/
function sendVoteRequest(id, positive) {
	let voteStatus = document.querySelector('#vote-status');
	sendAjaxRequest('POST', '/posts/' + id, { positive: positive ? "true" : "false" },
		(request) => { 
            if (request.readyState != 4) return;
			if (request.status != 200) return;

			let parser = new DOMParser();
			let doc = parser.parseFromString(request.responseText, 'text/html');

			voteStatus.parentElement.replaceChild(doc.body.firstChild, voteStatus);	
    });
}

function showEditTagModal(id, content) {
	let modal = document.querySelector('#edit-tag');
	modal.setAttribute('data-id', id);

	let text = modal.querySelector('#text');
	text.value = content;

	modal.classList.remove('hidden');
	modal.classList.add('flex');
}

function closeEditTagModal() {
	let modal = document.querySelector('#edit-tag');
	let text = modal.querySelector('#text');

	modal.removeAttribute('data-id');
	modal.classList.add('hidden');
	modal.classList.remove('flex');
	text.value = "";
}

function sendEditTagRequest() {
	let modal = document.querySelector('#edit-tag');
	let text = modal.querySelector('#text');
	let id = modal.getAttribute('data-id');
	let tag = document.querySelector('#tag[data-id="' + id + '"]');

	sendAjaxRequest('PATCH', '/tags/' + id, { name: text.value },
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200) return;

			let parser = new DOMParser();
			let doc = parser.parseFromString(request.responseText, 'text/html');

			tag.parentElement.replaceChild(doc.body.firstChild, tag);
			closeEditTagModal();
		});
}

function showTagModal() {
    let modal = document.querySelector('#tag-modal');

    modal.classList.remove('hidden');
	modal.classList.add('flex');
}

function closeTagModal() {
    let modal = document.querySelector('#tag-modal');
	
    modal.classList.add('hidden');
	modal.classList.remove('flex');
}

function deleteTag(id) {
	let confirmed = confirm('Are you sure you want to delete this tag? This action cannot be undone.');
	if (confirmed == false) return;

	let tag = document.querySelector('#tag[data-id="' + id + '"]');

	sendAjaxRequest('DELETE', '/tags/' + id, {},
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200) return;

			tag.remove();
		});
}

function followQuestion(button) {
	let id = button.getAttribute('data-id');

	sendAjaxRequest('POST', '/questions/' + id, { },
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200) return;

			let parser = new DOMParser();
			let doc = parser.parseFromString(request.responseText, 'text/html');

			button.parentElement.replaceChild(doc.body.firstChild, button);
		});
}

function followTag(button) {
	let id = button.getAttribute('data-id');

	sendAjaxRequest('POST', '/tags/' + id, { },
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200) return;

			let parser = new DOMParser();
			let doc = parser.parseFromString(request.responseText, 'text/html');

			button.parentElement.replaceChild(doc.body.firstChild, button);
		});
}

function markAsCorrect(answerId) {
	let answerList = document.querySelector('#answer-list');
    sendAjaxRequest('POST', `/answers/${answerId}/correct`, {},
        (request) => {
            if (request.readyState != 4) return;
			if (request.status != 200) return;

			let parser = new DOMParser();
			let doc = parser.parseFromString(request.responseText, 'text/html');

			answerList.parentElement.replaceChild(doc.body.firstChild, answerList);	
    });
}

function deleteUser(id) {
	let confirmed = confirm('Are you sure you want to delete this user? This action cannot be undone.');
	if (confirmed == false) return;

	let user = document.querySelector('#user[data-id="' + id + '"]');

	sendAjaxRequest('DELETE', '/users/' + id, {},
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200) return;

			user.remove();
		});
}

function blockUser(id) {
	let user = document.querySelector('#user[data-id="' + id + '"]');

	sendAjaxRequest('PATCH', '/users/' + id + '/block', {},
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200) return;

			let parser = new DOMParser();
			let doc = parser.parseFromString(request.responseText, 'text/html');

			user.parentElement.replaceChild(doc.body.firstChild, user);
		});
}

function deleteNotification(id) {
	let notification = document.querySelector('#notification[data-id="' + id + '"]');

	sendAjaxRequest('DELETE', '/notifications/' + id, {},
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200) return;

			notification.remove();
		});
}

function showCreateUserModal() {
	let modal = document.querySelector('#user-create');

	modal.classList.remove('hidden');
	modal.classList.add('flex');
}

function closeCreateUserModal() {
	let modal = document.querySelector('#user-create');
	let name = document.querySelector('#user-name');
	let handle = document.querySelector('#user-username');
	let email = document.querySelector('#user-email');
	let password = document.querySelector('#user-password');
	let role = document.querySelector('#user-role');

	modal.classList.add('hidden');
	modal.classList.remove('flex');
	name.value = "";
	handle.value = "";
	email.value = "";
	password.value = "";
	role.value = "";
}

function sendCreateUserRequest() {
    let userList = document.querySelector('#user-list');
    let name = document.querySelector('#user-name');
	let handle = document.querySelector('#user-username');
	let email = document.querySelector('#user-email');
	let password = document.querySelector('#user-password');
	let role = document.querySelector('#user-role');

    sendAjaxRequest('PUT', '/users', { name: name.value, username: handle.value,
										email: email.value, password: password.value,
										role: role.value },
        (request) => {
            if (request.readyState != 4) return;
            if (request.status != 200) return;

            let parser = new DOMParser();
            let doc = parser.parseFromString(request.responseText, 'text/html');

			userList.prepend(doc.body.firstChild);
			closeCreateUserModal();
		});
}

function showReportPostModal(type, id, content) {
	let modal = document.querySelector('#report-post');
	modal.setAttribute('data-id', id);
	modal.setAttribute('data-type', type);

	let text = modal.querySelector('#report-text');
	text.textContent = content;

	let title = modal.querySelector('#report-edit-title');
	switch (type) {
		case 'question':
			title.textContent = 'Report Question';
			break;
		case 'comment':
			title.textContent = 'Report Comment';
			break;
		case 'answer':
			title.textContent = 'Report Answer';
			break;
	}

	modal.classList.remove('hidden');
	modal.classList.add('flex');
}

function closeReportPostModal() {
	let modal = document.querySelector('#report-post');
	let text = modal.querySelector('#report-text');
	let reason = modal.querySelector('#report-reason');
	let title = modal.querySelector('#report-edit-title');

	modal.removeAttribute('data-id');
	modal.classList.add('hidden');
	modal.classList.remove('flex');
	text.value = "";
	reason.value = "";
	title.textContent = "Report ??";
}

function sendReportPostRequest() {
	let modal = document.querySelector('#report-post');
	let id = modal.getAttribute('data-id');
	let reason = modal.querySelector('#report-reason');

	sendAjaxRequest('PUT', '/reports', { id: id, reason: reason.value },
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200) return;

			closeEditPostModal();
			showSuccessModal("Report send successfully!");
		});
}
