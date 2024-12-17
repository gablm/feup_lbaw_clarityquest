//#region General
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

function createElemFromRequest(request) {
	let parser = new DOMParser();
    let doc = parser.parseFromString(request.responseText, 'text/html');

	return doc.body.firstChild;
}

function charCounter(entry, object, max)
{
	let counter = entry.parentElement.querySelector(".counter");
	let size = object.value.length;

	counter.textContent = `${size}/${max} characters`;
}
//#endregion

//#region Dropdowns
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
//#endregion

//#region Change Tabs
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
//#endregion

//#region Info Modal
function closeInfoModal()
{
	let modal = document.querySelector('#info-modal');
	let text = modal.querySelector('#info-text');

	text.textContent = "";
	modal.classList.add('hidden');
	modal.classList.remove('flex');
}

function showInfoModal(content)
{
	let modal = document.querySelector('#info-modal');
	let text = modal.querySelector('#info-text');

	text.textContent = content;
	modal.classList.remove('hidden');
	modal.classList.add('flex');
}
//#endregion

//#region Delete Modals

var deleteFunc;

function showDeleteModal(id, request, setup) {
	let modal = document.querySelector('#delete-modal');
	let title = modal.querySelector('#delete-title');
	let desc = modal.querySelector('#delete-desc');

	modal.setAttribute('data-id', id);
	deleteFunc = request;

	setup(title, desc);

	modal.classList.remove('hidden');
	modal.classList.add('flex');
}

function closeDeleteModal() {
	let modal = document.querySelector('#delete-modal');
	let err = modal.querySelector('.err');

	modal.classList.add('hidden');
	modal.classList.remove('flex');
	err.classList.add('hidden');
}

function sendDeleteRequest() {
	let modal = document.querySelector('#delete-modal');
	let id = modal.getAttribute('data-id');
	let error = modal.querySelector('.err');

	deleteFunc(id, error);
}

//#region Delete Report
function setupDeleteReport(title, desc)
{
	title.textContent = "Delete Report";
	desc.textContent = "Are you sure you want to delete this report?";
}

function deleteReport(id, error)
{
	let report = document.querySelector('.report-card[data-id="' + id + '"]');

	sendAjaxRequest('DELETE', '/reports/' + id, {},
		(request) => {
			if (request.readyState != 4) return;

			if (request.status == 404)
			{
				error.classList.remove('hidden');
				error.textContent = "Error: This report does not exist (anymore).";
				return;
			}
	
			if (request.status != 200)
			{
				error.classList.remove('hidden');
				error.textContent = "Error: Internal server error. Try again later.";
				return;
			}

			report.remove();
			closeDeleteModal();
		});
}
//#endregion

//#region Delete User
function setupDeleteUser(title, desc)
{
	title.textContent = "Delete User";
	desc.textContent = "Are you sure you want to delete this user?";
}

function deleteUser(id, error) {
	let user = document.querySelector('#user[data-id="' + id + '"]');

	sendAjaxRequest('DELETE', '/users/' + id, {},
		(request) => {
			if (request.readyState != 4) return;

			if (request.status == 404)
			{
				error.classList.remove('hidden');
				error.textContent = "Error: This user does not exist (anymore).";
				return;
			}
	
			if (request.status != 200)
			{
				error.classList.remove('hidden');
				error.textContent = "Error: Internal server error. Try again later.";
				return;
			}

			user.remove();
			closeDeleteModal();
		});
}
//#endregion

//#region Delete Tag
function setupDeleteTag(title, desc)
{
	title.textContent = "Delete Tag";
	desc.textContent = "Are you sure you want to delete this tag?";
}

function deleteTag(id, error) {
	let tag = document.querySelector('#tag[data-id="' + id + '"]');

	sendAjaxRequest('DELETE', '/tags/' + id, {},
		(request) => {
			if (request.readyState != 4) return;
			
			if (request.status == 404)
			{
				error.classList.remove('hidden');
				error.textContent = "Error: This user does not exist (anymore).";
				return;
			}
		
			if (request.status != 200)
			{
				error.classList.remove('hidden');
				error.textContent = "Error: Internal server error. Try again later.";
				return;
			}

			tag.remove();
			closeDeleteModal();
		});
}
//#endregion

//#region Delete Answer
function setupDeleteAnswer(title, desc)
{
	title.textContent = "Delete Answer";
	desc.textContent = "Are you sure you want to delete this answer?";
}

function deleteAnswer(id, error) {
	let answer = document.querySelector('.answer[data-id="' + id + '"]');
	let answer_count = document.querySelector('#question-answer-count');

	sendAjaxRequest('DELETE', '/answers/' + id, {},
		(request) => {
			if (request.readyState != 4) return;
			if (request.status == 404)
			{
				error.classList.remove('hidden');
				error.textContent = "Error: This answer does not exist (anymore).";

				answer.remove();
				answer_count.textContent = answer_count.textContent - 1;
				return;
			}
			if (request.status != 200)
			{
				error.classList.remove('hidden');
				error.textContent = "Error: Internal server error. Try again later.";
				return;
			}

			answer.remove();
			answer_count.textContent = answer_count.textContent - 1;
			closeDeleteModal();
		});
}
//#endregion

//#region Delete Comment
function setupDeleteComment(title, desc)
{
	title.textContent = "Delete Comment";
	desc.textContent = "Are you sure you want to delete this comment?";
}

function deleteComment(id, error) {
	let comment = document.querySelector('.comment[data-id="' + id + '"]');

	sendAjaxRequest('DELETE', '/comments/' + id, {},
		(request) => {
			if (request.readyState != 4) return;
			
			if (request.status == 404)
			{
				error.classList.remove('hidden');
				error.textContent = "Error: This answer does not exist (anymore).";
				
				comment.remove();
				return;
			}
			
			if (request.status != 200)
			{
				error.classList.remove('hidden');
				error.textContent = "Error: Internal server error. Try again later.";
				return;
			}

			comment.remove();
			closeDeleteModal();
		});
}
//#endregion
//#endregion

//#region Notifications
function deleteNotification(id) {
	let notification = document.querySelector('.notification[data-id="' + id + '"]');

	sendAjaxRequest('DELETE', '/notifications/' + id, {},
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200) return;

			notification.remove();
		});
}
//#endregion

//#region Quick Button Actions
function followQuestion(button) {
	let id = button.getAttribute('data-id');

	sendAjaxRequest('POST', '/questions/' + id, { },
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200)
			{
				showInfoModal("This action failed, please try again");
				return;
			}

			let btn = createElemFromRequest(request);
			button.parentElement.replaceChild(btn, button);
		});
}

function followTag(button) {
	let id = button.getAttribute('data-id');

	sendAjaxRequest('POST', '/tags/' + id, { },
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200)
			{
				showInfoModal("This action failed, please try again.");
				return;
			}

			let btn = createElemFromRequest(request);
			button.parentElement.replaceChild(btn, button);
		});
}

function blockUser(id) {
	let user = document.querySelector('#user[data-id="' + id + '"]');

	sendAjaxRequest('PATCH', '/users/' + id + '/block', {},
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200)
			{
				showInfoModal("This action failed, please try again.");
				return;
			}

			let btn = createElemFromRequest(request);
			user.parentElement.replaceChild(btn, user);
		});
}

function markAsCorrect(answerId) {
	let answerList = document.querySelector('#answer-list');

    sendAjaxRequest('POST', `/answers/${answerId}/correct`, {},
        (request) => {
            if (request.readyState != 4) return;
			if (request.status != 200)
			{
				showInfoModal("This action failed, please try again.");
				return;
			}

			let answer = createElemFromRequest(request);
			answerList.parentElement.replaceChild(answer, answerList);	
    });
}
//#endregion

//#region Admin Panel - Tags
//#region Tag Create
function showCreateTagModal() {
	let modal = document.querySelector('#tag-create');
	let text = modal.querySelector('#tag-name');
	let error = modal.querySelector('.err');

	modal.classList.remove('hidden');
	modal.classList.add('flex');

	error.classList.add('hidden');
	
	text.value = "";

	charCounter(modal.firstChild, text, 24);
}

function closeCreateTagModal() {
	let modal = document.querySelector('#tag-create');

	modal.removeAttribute('data-id');
	modal.classList.add('hidden');
	modal.classList.remove('flex');
}

function sendCreateTagRequest() {
    let tagList = document.querySelector('#tag-list');
	let modal = document.querySelector('#tag-create');
    let text = modal.querySelector('#tag-name');
	let error = modal.querySelector('.err');

	if (text.value == "")
	{
		error.classList.remove('hidden');
		error.textContent = "Error: Tag name can't be empty.";
		return;
	}

    sendAjaxRequest('POST', '/tags', { name: text.value },
        (request) => {
            if (request.readyState != 4) return;
            if (request.status == 302)
			{
				error.classList.remove('hidden');
				error.textContent = "Error: Invalid text contents.";
				return;
			}

			if (request.status != 200)
			{
				error.classList.remove('hidden');
				error.textContent = "Error: Internal server error. Try again later.";
				return;
			}

            let tag = createElemFromRequest(request);
			tagList.prepend(tag);

			text.value = "";
			closeCreateTagModal();
		});
}
//#endregion

//#region Tag Edit
function showEditTagModal(id, content) {
	let modal = document.querySelector('#edit-tag');
	modal.setAttribute('data-id', id);

	let text = modal.querySelector('#tag-text');
	text.value = content;
	charCounter(modal.firstChild, text, 24);

	modal.classList.remove('hidden');
	modal.classList.add('flex');

	let error = modal.querySelector('.err');
	error.classList.add('hidden');
}

function closeEditTagModal() {
	let modal = document.querySelector('#edit-tag');
	let text = modal.querySelector('#tag-text');
	let error = modal.querySelector('.err');

	modal.removeAttribute('data-id');
	modal.classList.add('hidden');
	modal.classList.remove('flex');
	error.classList.add('hidden');
	text.value = "";
}

function sendEditTagRequest() {
	let modal = document.querySelector('#edit-tag');
	let text = modal.querySelector('#tag-text');
	let id = modal.getAttribute('data-id');
	let tag = document.querySelector('#tag[data-id="' + id + '"]');
	let error = modal.querySelector('.err');

	if (text.value == "")
	{
		error.classList.remove('hidden');
		error.textContent = "Error: Tag name can't be empty.";
		return;
	}

	sendAjaxRequest('PATCH', '/tags/' + id, { name: text.value },
		(request) => {
			if (request.readyState != 4) return;
			if (request.status == 404)
			{
				error.classList.remove('hidden');
				error.textContent = "Error: This tag does not exist (anymore).";
				return;
			}
			
			if (request.status == 405)
			{
				error.classList.remove('hidden');
				error.textContent = "Error: Invalid field submitted to the server.";
				return;
			}
	
			if (request.status != 200)
			{
				error.classList.remove('hidden');
				error.textContent = "Error: Internal server error. Try again later.";
				return;
			}

			let newTag = createElemFromRequest(request);
			tag.parentElement.replaceChild(newTag, tag);

			closeEditTagModal();
		});
}
//#endregion
//#endregion

//#region Question Page - Report
function showReportPostModal(type, id, content) {
	let modal = document.querySelector('#report-post');
	modal.setAttribute('data-id', id);
	modal.setAttribute('data-type', type);

	let reason = modal.querySelector('#report-reason');
	reason.value = "";
	charCounter(modal.firstChild, reason, 100);

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
	
	let title = modal.querySelector('#report-edit-title');
	let error = modal.querySelector("#report-error");

	modal.removeAttribute('data-id');
	modal.classList.add('hidden');
	modal.classList.remove('flex');
	text.value = "";
	reason.value = "";
	title.textContent = "Report ??";
	error.classList.add('hidden');
}

function sendReportPostRequest() {
	let modal = document.querySelector('#report-post');
	let id = modal.getAttribute('data-id');
	let reason = modal.querySelector('#report-reason');
	let error = modal.querySelector("#report-error");

	if (!reason.value)
	{
		error.classList.remove('hidden');
		error.textContent = "Error: Reason can't be empty.";
		return;
	}

	sendAjaxRequest('POST', '/reports', { id: id, reason: reason.value },
		(request) => {
			if (request.readyState != 4) return;

			if (request.status == 404)
			{
				error.classList.remove('hidden');
				error.textContent = "Error: This post does not exist (anymore).";
				return;
			}
			
			if (request.status == 405)
			{
				error.classList.remove('hidden');
				error.textContent = "Error: Invalid field submitted to the server.";
				return;
			}

			if (request.status != 200)
			{
				error.classList.remove('hidden');
				error.textContent = "Error: Internal server error. Try again later.";
				return;
			}

			closeReportPostModal();
			showInfoModal("Report sent successfully!");
		});
}
//#endregion

function sendCreateAnswerRequest(id) {
	let answerList = document.querySelector('#answer-list');
	let text = document.querySelector('#answer-text');
	let answerCount = document.querySelector('#question-answer-count');
	let error = document.querySelector('.add-err');

	if (text.value == "")
	{
		error.classList.remove('hidden');
		error.textContent = "Error: Answer content can't be empty.";
		return;
	}

	sendAjaxRequest('POST', '/answers', { id: id, text: text.value },
		(request) => {
			if (request.readyState != 4) return;

			if (request.status == 302)
			{
				error.classList.remove('hidden');
				error.textContent = "Error: Invalid text contents.";
				return;
			}

			if (request.status != 200)
			{
				error.classList.remove('hidden');
				error.textContent = "Error: Internal server error. Try again later.";
				return;
			}

			let answer = createElemFromRequest(request);
			answerList.prepend(answer);

			text.value = "";
			charCounter(text.parentElement, text, 5000);
			
			errorBox.classList.add('hidden');
			answerCount.textContent = Number(answerCount.textContent) + 1;
		});
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

			let q = createElemFromRequest(request);
			question.parentElement.replaceChild(q, question);

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

			let p = createElemFromRequest(request);
			post.parentElement.replaceChild(p, post);

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

	sendAjaxRequest('POST', '/comments/', { id: id, text: text.value },
		(request) => {
			if (request.readyState != 4) return;
			if (request.status != 200) return;

			let comment = createElemFromRequest(request)
			list.prepend(comment);

			closeCreateCommentModal();
		});
}

function sendVoteRequest(id, positive) {
	let voteStatus = document.querySelector('#vote-status-' + id);
	sendAjaxRequest('POST', '/posts/' + id, { positive: positive ? "true" : "false" },
		(request) => { 
            if (request.readyState != 4) return;
			if (request.status != 200) return;

			let vote = createElemFromRequest(request);
			voteStatus.parentElement.replaceChild(vote, voteStatus);	
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

    sendAjaxRequest('POST', '/users', { name: name.value, username: handle.value,
										email: email.value, password: password.value,
										role: role.value },
        (request) => {
            if (request.readyState != 4) return;
            if (request.status != 200) return;

            let user = createElemFromRequest(request);
			userList.prepend(user);
			closeCreateUserModal();
		});
}
