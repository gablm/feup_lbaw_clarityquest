//#region General
function encodeForAjax(data) {
    // Return null if no data provided
    if (data == null) return null;
    
    // Convert object into URL-encoded string format (key1=value1&key2=value2)
    return Object.keys(data).map(function (k) {
        return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
    }).join('&');
}

function sendAjaxRequest(method, url, data, handler) {
    let request = new XMLHttpRequest();

    // Initialize the request
    request.open(method, url, true);
    // Add CSRF token for Laravel security
    request.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
    // Set content type for form data
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    // Set up response handler
    request.onreadystatechange = function () {
        handler(this);
    };
    // Send the encoded data
    request.send(encodeForAjax(data));
}

function createElemFromRequest(request) {
    // Create a DOM parser to convert HTML string to DOM elements
    let parser = new DOMParser();
    // Parse the response text as HTML
    let doc = parser.parseFromString(request.responseText, 'text/html');
    // Return the first element from the parsed HTML
    return doc.body.firstChild;
}

function charCounter(entry, object, max) {
    // Find the counter element within the parent
    let counter = entry.parentElement.querySelector(".counter");
    // Get current text length
    let size = object.value.length;
    // Update counter display
    counter.textContent = `${size}/${max} characters`;
}
//#endregion

//#region Dropdowns
function toggleUserDropdown() {
    // Hide notification dropdown if open
    let otherDropdown = document.querySelector('#notification-dropdown');
    otherDropdown.classList.add('hidden');

    // Toggle visibility of user dropdown
    let dropdown = document.querySelector('#user-dropdown');
    dropdown.classList.toggle('hidden');
}

function toggleNotificationDropdown() {
    // Hide user dropdown if open
    let otherDropdown = document.querySelector('#user-dropdown');
    otherDropdown.classList.add('hidden');

    // Toggle visibility of notification dropdown
    let dropdown = document.querySelector('#notification-dropdown');
    dropdown.classList.toggle('hidden');
}
//#endregion

//#region Change Tabs
function showProfileTab(tab) {
    // Hide all sections first
    document.getElementById('questions-section').classList.add('hidden');
    document.getElementById('answers-section').classList.add('hidden');
    document.getElementById('medals-section').classList.add('hidden');

    // Reset all tab button styles
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'text-blue-600', 'border-blue-600', 'hover:bg-blue-200');
        btn.classList.add('bg-gray-100', 'text-gray-600', 'border-transparent', 'hover:bg-gray-200');
    });

    // Show selected section
    document.getElementById(`${tab}-section`).classList.remove('hidden');

    // Highlight active tab
    let elem = document.getElementById(`${tab}-tab`);
    elem.classList.add('bg-blue-100', 'text-blue-600', 'border-blue-600', 'hover:bg-blue-200');
    elem.classList.remove('bg-gray-100', 'text-gray-600', 'border-transparent', 'hover:bg-gray-200');
}

function showAdminTab(tab) {
    // Similar to showProfileTab but for admin sections
    document.getElementById('reports-section').classList.add('hidden');
    document.getElementById('users-section').classList.add('hidden');
    document.getElementById('tags-section').classList.add('hidden');

    // Reset all tab styles
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'text-blue-600', 'border-blue-600', 'hover:bg-blue-200');
        btn.classList.add('bg-gray-100', 'text-gray-600', 'border-transparent', 'hover:bg-gray-200');
    });

    // Show selected section
    document.getElementById(`${tab}-section`).classList.remove('hidden');

    // Highlight active tab
    let elem = document.getElementById(`${tab}-tab`);
    elem.classList.add('bg-blue-100', 'text-blue-600', 'border-blue-600', 'hover:bg-blue-200');
    elem.classList.remove('bg-gray-100', 'text-gray-600', 'border-transparent', 'hover:bg-gray-200');
}
//#endregion

//#region Info Modal
function closeInfoModal() {
    let modal = document.querySelector('#info-modal');
    let text = modal.querySelector('#info-text');

    // Clear modal content and hide it
    text.textContent = "";
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function showInfoModal(content) {
    let modal = document.querySelector('#info-modal');
    let text = modal.querySelector('#info-text');

    // Set modal content and show it
    text.textContent = content;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
//#endregion

//#region Delete Modals
// Store delete function reference
var deleteFunc;

function showDeleteModal(id, request, setup) {
    let modal = document.querySelector('#delete-modal');
    let title = modal.querySelector('#delete-title');
    let desc = modal.querySelector('#delete-desc');

    // Store item ID and delete function
    modal.setAttribute('data-id', id);
    deleteFunc = request;

    // Set up modal content
    setup(title, desc);

    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeDeleteModal() {
    let modal = document.querySelector('#delete-modal');
    let err = modal.querySelector('.err');

    // Hide modal and clear error
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    err.classList.add('hidden');
}

function sendDeleteRequest() {
    let modal = document.querySelector('#delete-modal');
    let id = modal.getAttribute('data-id');
    let error = modal.querySelector('.err');

    // Execute stored delete function
    deleteFunc(id, error);
}
//#endregion

//#region Delete Report
// Sets up the confirmation modal for deleting a report
function setupDeleteReport(title, desc) {
    title.textContent = "Delete Report";
    desc.textContent = "Are you sure you want to delete this report?";
}

// Handles the deletion of a report via AJAX
function deleteReport(id, error) {
    // Find the report card element in the DOM
    let report = document.querySelector('.report-card[data-id="' + id + '"]');

    // Send DELETE request to server
    sendAjaxRequest('DELETE', '/reports/' + id, {},
        (request) => {
            if (request.readyState != 4) return; // Exit if request not complete

            // Handle 404 error - report doesn't exist
            if (request.status == 404) {
                error.classList.remove('hidden');
                error.textContent = "Error: This report does not exist (anymore).";
                return;
            }
    
            // Handle server error
            if (request.status != 200) {
                error.classList.remove('hidden');
                error.textContent = "Error: Internal server error. Try again later.";
                return;
            }

            // Remove report from DOM and close modal on success
            report.remove();
            closeDeleteModal();
        });
}
//#endregion
//#region Delete User
// Sets up the confirmation modal for deleting a user
function setupDeleteUser(title, desc) {
    title.textContent = "Delete User";
    desc.textContent = "Are you sure you want to delete this user?";
}

// Handles the deletion of a user via AJAX
function deleteUser(id, error) {
    let user = document.querySelector('#user[data-id="' + id + '"]');

    sendAjaxRequest('DELETE', '/users/' + id, {},
        (request) => {
            if (request.readyState != 4) return;

            // Handle 404 error - user doesn't exist
            if (request.status == 404) {
                error.classList.remove('hidden');
                error.textContent = "Error: This user does not exist (anymore).";
                return;
            }
    
            // Handle server error
            if (request.status != 200) {
                error.classList.remove('hidden');
                error.textContent = "Error: Internal server error. Try again later.";
                return;
            }

            // Remove user from DOM and close modal
            user.remove();
            closeDeleteModal();
        });
}
//#endregion
//#region Delete Tag
// Sets up the confirmation modal for deleting a tag
function setupDeleteTag(title, desc) {
    title.textContent = "Delete Tag";
    desc.textContent = "Are you sure you want to delete this tag?";
}

// Handles the deletion of a tag via AJAX
function deleteTag(id, error) {
    let tag = document.querySelector('#tag[data-id="' + id + '"]');

    sendAjaxRequest('DELETE', '/tags/' + id, {},
        (request) => {
            if (request.readyState != 4) return;
            
            // Handle 404 error - tag doesn't exist
            if (request.status == 404) {
                error.classList.remove('hidden');
                error.textContent = "Error: This user does not exist (anymore).";
                return;
            }
        
            // Handle server error
            if (request.status != 200) {
                error.classList.remove('hidden');
                error.textContent = "Error: Internal server error. Try again later.";
                return;
            }

            // Remove tag from DOM and close modal
            tag.remove();
            closeDeleteModal();
        });
}
//#endregion
//#region Delete Answer
// Sets up the confirmation modal for deleting an answer
function setupDeleteAnswer(title, desc) {
    title.textContent = "Delete Answer";
    desc.textContent = "Are you sure you want to delete this answer?";
}

// Handles the deletion of an answer via AJAX
function deleteAnswer(id, error) {
    let answer = document.querySelector('.answer[data-id="' + id + '"]');
    let answer_count = document.querySelector('#question-answer-count');

    sendAjaxRequest('DELETE', '/answers/' + id, {},
        (request) => {
            if (request.readyState != 4) return;

            // Handle 404 error - answer doesn't exist
            if (request.status == 404) {
                error.classList.remove('hidden');
                error.textContent = "Error: This answer does not exist (anymore).";

                // Update UI even if answer not found
                answer.remove();
                answer_count.textContent = answer_count.textContent - 1;
                return;
            }

            // Handle server error
            if (request.status != 200) {
                error.classList.remove('hidden');
                error.textContent = "Error: Internal server error. Try again later.";
                return;
            }

            // Update UI on success
            answer.remove();
            answer_count.textContent = answer_count.textContent - 1;
            closeDeleteModal();
        });
}
//#endregion
//#region Delete Comment
// Sets up the confirmation modal for deleting a comment
function setupDeleteComment(title, desc) {
    title.textContent = "Delete Comment";
    desc.textContent = "Are you sure you want to delete this comment?";
}

// Handles the deletion of a comment via AJAX
function deleteComment(id, error) {
    let comment = document.querySelector('.comment[data-id="' + id + '"]');

    sendAjaxRequest('DELETE', '/comments/' + id, {},
        (request) => {
            if (request.readyState != 4) return;
            
            // Handle 404 error - comment doesn't exist
            if (request.status == 404) {
                error.classList.remove('hidden');
                error.textContent = "Error: This answer does not exist (anymore).";
                
                comment.remove();
                return;
            }
            
            // Handle server error
            if (request.status != 200) {
                error.classList.remove('hidden');
                error.textContent = "Error: Internal server error. Try again later.";
                return;
            }

            // Update UI on success
            comment.remove();
            closeDeleteModal();
        });
}
//#endregion
//#region Notifications
// Handles the deletion of a notification via AJAX
function deleteNotification(id) {
    let notification = document.querySelector('.notification[data-id="' + id + '"]');

    sendAjaxRequest('DELETE', '/notifications/' + id, {},
        (request) => {
            if (request.readyState != 4) return;
            if (request.status != 200) return;

            // Remove notification from DOM on success
            notification.remove();
        });
}
//#endregion
//#region Quick Button Actions
// Handles following a question via AJAX
function followQuestion(button) {
    let id = button.getAttribute('data-id');

    sendAjaxRequest('POST', '/questions/' + id, { },
        (request) => {
            if (request.readyState != 4) return;
            
            // Handle server error
            if (request.status != 200) {
                showInfoModal("This action failed, please try again");
                return;
            }

            // Update button UI on success
            let btn = createElemFromRequest(request);
            button.parentElement.replaceChild(btn, button);
        });
}

// Handles following a tag via AJAX
function followTag(button) {
    let id = button.getAttribute('data-id');

    sendAjaxRequest('POST', '/tags/' + id, { },
        (request) => {
            if (request.readyState != 4) return;
            
            // Handle server error
            if (request.status != 200) {
                showInfoModal("This action failed, please try again.");
                return;
            }

            // Update button UI on success
            let btn = createElemFromRequest(request);
            button.parentElement.replaceChild(btn, button);
        });
}

// Handles blocking a user via AJAX
function blockUser(id) {
    let user = document.querySelector('#user[data-id="' + id + '"]');

    sendAjaxRequest('PATCH', '/users/' + id + '/block', {},
        (request) => {
            if (request.readyState != 4) return;
            
            // Handle server error
            if (request.status != 200) {
                showInfoModal("This action failed, please try again.");
                return;
            }

            // Update UI on success
            let btn = createElemFromRequest(request);
            user.parentElement.replaceChild(btn, user);
        });
}

// Marks an answer as correct via AJAX
function markAsCorrect(answerId) {
    let answerList = document.querySelector('#answer-list');

    sendAjaxRequest('POST', `/answers/${answerId}/correct`, {},
        (request) => {
            if (request.readyState != 4) return;
            
            // Handle server error
            if (request.status != 200) {
                showInfoModal("This action failed, please try again.");
                return;
            }

            // Update answer list UI on success
            let answer = createElemFromRequest(request);
            answerList.parentElement.replaceChild(answer, answerList);    
    });
}

// Handles voting on posts via AJAX
function sendVoteRequest(id, positive) {
    let voteStatus = document.querySelector('#vote-status-' + id);

    sendAjaxRequest('POST', '/posts/' + id, { positive: positive ? "true" : "false" },
        (request) => { 
            if (request.readyState != 4) return;
            
            // Handle server error
            if (request.status != 200) {
                showInfoModal("This action failed, please try again.");
                return;
            }

            // Update vote status UI on success
            let vote = createElemFromRequest(request);
            voteStatus.parentElement.replaceChild(vote, voteStatus);    
    });
}

//#region Tag Create
// Shows the modal for creating a new tag
function showCreateTagModal() {
    let modal = document.querySelector('#tag-create');
    let text = modal.querySelector('#tag-name');
    let error = modal.querySelector('.err');

    // Show modal and reset fields
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    error.classList.add('hidden');
    text.value = "";

    // Initialize character counter
    charCounter(modal.firstChild, text, 24);
}

// Closes the create tag modal
function closeCreateTagModal() {
    let modal = document.querySelector('#tag-create');

    modal.removeAttribute('data-id');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Handles creating a new tag via AJAX
function sendCreateTagRequest() {
    let tagList = document.querySelector('#tag-list');
    let modal = document.querySelector('#tag-create');
    let text = modal.querySelector('#tag-name');
    let error = modal.querySelector('.err');

    // Validate input
    if (text.value == "") {
        error.classList.remove('hidden');
        error.textContent = "Error: Tag name can't be empty.";
        return;
    }

    sendAjaxRequest('POST', '/tags', { name: text.value },
        (request) => {
            if (request.readyState != 4) return;

            // Handle validation error
            if (request.status == 302) {
                error.classList.remove('hidden');
                error.textContent = "Error: Invalid text contents.";
                return;
            }

            // Handle server error
            if (request.status != 200) {
                error.classList.remove('hidden');
                error.textContent = "Error: Internal server error. Try again later.";
                return;
            }

            // Update UI on success
            let tag = createElemFromRequest(request);
            tagList.prepend(tag);
            text.value = "";
            closeCreateTagModal();
        });
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
// Shows modal for editing a tag with pre-filled content
function showEditTagModal(id, content) {
    let modal = document.querySelector('#edit-tag');
    modal.setAttribute('data-id', id);  // Store tag ID for later use

    // Pre-fill the tag text and setup character counter
    let text = modal.querySelector('#tag-text');
    text.value = content;
    charCounter(modal.firstChild, text, 24);  // Limit tag to 24 chars

    // Show modal and reset error state
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    let error = modal.querySelector('.err');
    error.classList.add('hidden');
}

// Closes tag edit modal and resets its state
function closeEditTagModal() {
    let modal = document.querySelector('#edit-tag');
    let text = modal.querySelector('#tag-text');
    let error = modal.querySelector('.err');

    // Reset modal state and hide it
    modal.removeAttribute('data-id');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    error.classList.add('hidden');
    text.value = "";
}

// Handles the AJAX request to edit a tag
function sendEditTagRequest() {
    let modal = document.querySelector('#edit-tag');
    let text = modal.querySelector('#tag-text');
    let id = modal.getAttribute('data-id');
    let tag = document.querySelector('#tag[data-id="' + id + '"]');
    let error = modal.querySelector('.err');

    // Validate input
    if (text.value == "") {
        error.classList.remove('hidden');
        error.textContent = "Error: Tag name can't be empty.";
        return;
    }

    // Send PATCH request to update tag
    sendAjaxRequest('PATCH', '/tags/' + id, { name: text.value },
        (request) => {
            if (request.readyState != 4) return;
            
            // Handle various error cases
            if (request.status == 404) {
                error.classList.remove('hidden');
                error.textContent = "Error: This tag does not exist (anymore).";
                return;
            }
            
            if (request.status == 405) {
                error.classList.remove('hidden');
                error.textContent = "Error: Invalid field submitted to the server.";
                return;
            }
    
            if (request.status != 200) {
                error.classList.remove('hidden');
                error.textContent = "Error: Internal server error. Try again later.";
                return;
            }

            // Update UI with new tag on success
            let newTag = createElemFromRequest(request);
            tag.parentElement.replaceChild(newTag, tag);
            closeEditTagModal();
        });
}
//#endregion
//#region Report Create
// Shows modal for creating a report on a post
function showReportPostModal(type, id, content) {
    let modal = document.querySelector('#report-post');
    // Store post ID and type for later use
    modal.setAttribute('data-id', id);
    modal.setAttribute('data-type', type);

    // Reset and setup reason input with character limit
    let reason = modal.querySelector('#report-reason');
    reason.value = "";
    charCounter(modal.firstChild, reason, 100);

    // Display the content being reported
    let text = modal.querySelector('#report-text');
    text.textContent = content;

    // Set appropriate title based on post type
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

// Function to close the post report modal and reset its state
function closeReportPostModal() {
    // Get modal elements
    let modal = document.querySelector('#report-post');
    let title = modal.querySelector('#report-edit-title');
    let reason = modal.querySelector('#report-reason');
    let text = modal.querySelector('#report-text');
    let error = modal.querySelector("#report-error");

    // Clear the post ID from the modal
    modal.removeAttribute('data-id');

    // Reset form fields
    reason.value = "";
    text.value = "";
    title.textContent = "Report ??";
    
    // Hide error message
    error.classList.add('hidden');

    // Hide the modal
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Function to send a post report to the server
function sendReportPostRequest() {
    // Get modal elements
    let modal = document.querySelector('#report-post');
    let id = modal.getAttribute('data-id');
    let reason = modal.querySelector('#report-reason');
    let error = modal.querySelector("#report-error");

    // Validate that reason is not empty
    if (!reason.value) {
        error.classList.remove('hidden');
        error.textContent = "Error: Reason can't be empty.";
        return;
    }

    // Send AJAX request to report the post
    sendAjaxRequest('POST', '/reports', { id: id, reason: reason.value },
        (request) => {
            if (request.readyState != 4) return;

            // Handle different response status codes
            if (request.status == 404) {
                error.classList.remove('hidden');
                error.textContent = "Error: This post does not exist (anymore).";
                return;
            }
            
            if (request.status == 405) {
                error.classList.remove('hidden');
                error.textContent = "Error: Invalid field submitted to the server.";
                return;
            }

            if (request.status != 200) {
                error.classList.remove('hidden');
                error.textContent = "Error: Internal server error. Try again later.";
                return;
            }

            // On success, close modal and show confirmation
            closeReportPostModal();
            showInfoModal("Report sent successfully!");
        });
}
//#endregion
//#region Answer Create
// Function to create a new answer to a question
function sendCreateAnswerRequest(id) {
    // Get relevant DOM elements
    let answerList = document.querySelector('#answer-list');
    let text = document.querySelector('#answer-text');
    let answerCount = document.querySelector('#question-answer-count');
    let error = document.querySelector('.add-err');

    // Validate answer text is not empty
    if (text.value == "") {
        error.classList.remove('hidden');
        error.textContent = "Error: Answer content can't be empty.";
        return;
    }

    // Send AJAX request to create answer
    sendAjaxRequest('POST', '/answers', { id: id, text: text.value },
        (request) => {
            if (request.readyState != 4) return;

            // Handle error responses
            if (request.status == 302) {
                error.classList.remove('hidden');
                error.textContent = "Error: Invalid text contents.";
                return;
            }

            if (request.status != 200) {
                error.classList.remove('hidden');
                error.textContent = "Error: Internal server error. Try again later.";
                return;
            }

            // On success, add new answer to the page
            let answer = createElemFromRequest(request);
            answerList.prepend(answer);

            // Reset form and update UI
            text.value = "";
            charCounter(text.parentElement, text, 500);
            error.classList.add('hidden');
            answerCount.textContent = Number(answerCount.textContent) + 1;
        });
}
//#endregion
//#region Comment Create
// Function to display the comment creation modal
function showCreateCommentModal(id) {
    let modal = document.querySelector('#add-comment');
    modal.setAttribute('data-id', id);

    // Reset error state and form
    let error = modal.querySelector('.err');
    error.classList.add('hidden');

    let text = modal.querySelector('#text');
    text.value = "";
    charCounter(modal.firstChild, text, 500);

    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}


function closeCreateCommentModal() {
	let modal = document.querySelector('#add-comment');
	let text = modal.querySelector('#text');
	let error = modal.querySelector('.err');

	modal.removeAttribute('data-id');
	modal.classList.add('hidden');
	modal.classList.remove('flex');
	error.classList.add('hidden');
	text.value = "";
}

function sendCreateCommentRequest() {
	let modal = document.querySelector('#add-comment');
	let text = modal.querySelector('#text');

	let id = modal.getAttribute('data-id');
	let list = document.querySelector(`#comment-list-${id}`);
	let error = modal.querySelector('.err');

	if (text.value == "")
	{
		error.classList.remove('hidden');
		error.textContent = "Error: Comment content can't be empty.";
		return;
	}

	sendAjaxRequest('POST', '/comments/', { id: id, text: text.value },
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

			let comment = createElemFromRequest(request)
			list.prepend(comment);

			closeCreateCommentModal();
		});
}
//#endregion
//#region Post Edit
function showEditPostModal(type, id, content) {
    // Get the modal element
    let modal = document.querySelector('#edit-post');
    // Set the data-id and data-type attributes
    modal.setAttribute('data-id', id);
    modal.setAttribute('data-type', type);

    // Get the text area and set its value
    let text = modal.querySelector('#edit-text');
    text.value = content;
    // Initialize character counter
    charCounter(modal.firstChild, text, 500);

    // Hide any previous error messages
    let error = modal.querySelector('.err');
    error.classList.add('hidden');

    // Set the modal title based on the type
    let title = modal.querySelector('#edit-title');
    switch (type) {
        case 'comment':
            title.textContent = 'Edit Comment';
            break;
        case 'answer':
            title.textContent = 'Edit Answer';
            break;
    }

    // Show the modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeEditPostModal() {
    // Get the modal element
    let modal = document.querySelector('#edit-post');
    // Remove data-id and data-type attributes
    modal.removeAttribute('data-id');
    modal.removeAttribute('data-type');

    // Clear the text area
    let text = modal.querySelector('#edit-text');
    text.value = "";

    // Reset the modal title
    let title = modal.querySelector('#edit-title');
    title.textContent = "Edit ??";

    // Hide any error messages
    let error = modal.querySelector('.err');
    error.classList.add('hidden');

    // Hide the modal
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function sendEditPostRequest() {
    // Get the modal and its elements
    let modal = document.querySelector('#edit-post');
    let error = modal.querySelector('.err');
    let text = modal.querySelector('#edit-text');

    // Get the post ID and type
    let id = modal.getAttribute('data-id');
    let type = modal.getAttribute('data-type');
    let route, post, typeName;

    // Determine the route and post element based on the type
    switch (type) {
        case 'comment':
            post = document.querySelector('.comment[data-id="' + id + '"]');
            route = '/comments/';
            typeName = "Comment";
            break;
        case 'answer':
            post = document.querySelector('.answer[data-id="' + id + '"]');
            route = '/answers/';
            typeName = "Answer";
            break;
    }

    // Validate the text input
    if (text.value == "") {
        error.classList.remove('hidden');
        error.textContent = `Error: ${typeName} content can't be empty.`;
        return;
    }

    // Send the AJAX request to update the post
    sendAjaxRequest('PATCH', route + id, { text: text.value },
        (request) => {
            if (request.readyState != 4) return;
            if (request.status == 404) {
                error.classList.remove('hidden');
                error.textContent = "Error: This tag does not exist (anymore).";
                return;
            }
                
            if (request.status == 405) {
                error.classList.remove('hidden');
                error.textContent = "Error: Invalid field submitted to the server.";
                return;
            }
        
            if (request.status != 200) {
                error.classList.remove('hidden');
                error.textContent = "Error: Internal server error. Try again later.";
                return;
            }

            // Replace the old post with the updated one
            let p = createElemFromRequest(request);
            post.parentElement.replaceChild(p, post);

            // Close the modal
            closeEditPostModal();
        });
}
//#endregion

//#region Edit Tags
function showTagModal() {
    // Get the modal element
    let modal = document.querySelector('#tag-modal');

    // Show the modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeTagModal() {
    // Get the modal element
    let modal = document.querySelector('#tag-modal');
    
    // Hide the modal
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
//#endregion

//#region Question Edit
function showEditQuestionModal() {
    // Get the question and modal elements
    let question = document.querySelector('.question');
    let modal = question.querySelector('#edit-question');

    // Hide any previous error messages
    let error = modal.querySelectorAll('.err');
    error.forEach((elem) => {
        elem.classList.add('hidden');
    });

    // Show the modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeEditQuestionModal() {
    // Get the question and modal elements
    let question = document.querySelector('.question');
    let modal = question.querySelector('#edit-question');

    // Hide any error messages
    let error = modal.querySelectorAll('.err');
    error.forEach((elem) => {
        elem.classList.add('hidden');
    });

    // Hide the modal
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function sendEditQuestionRequest() {
    // Get the question and its elements
    let question = document.querySelector('.question');
    let id = question.getAttribute('data-id');
    let title = question.querySelector('#title');
    let description = question.querySelector('#description');

    // Get the modal and its error elements
    let modal = question.querySelector('#edit-question');
    let errors = modal.querySelectorAll('.err');
    errors.forEach((elem) => {
        elem.classList.add('hidden');
    });

    // Validate the title input
    let errorTitle = modal.querySelector('.err#err-eq-title');
    if (title.value == "") {
        errorTitle.classList.remove('hidden');
        errorTitle.textContent = `Title content can't be empty.`;
    }

    // Validate the description input
    let errorDesc = modal.querySelector('.err#err-eq-desc');
    if (description.value == "") {
        errorDesc.classList.remove('hidden');
        errorDesc.textContent = `Description content can't be empty.`;
    }

    // If validation fails, return early
    if (title.value == "" || description.value == "") return;

    let error = modal.querySelector('.err#err-eq-gen');

    // Send the AJAX request to update the question
    sendAjaxRequest('PATCH', '/questions/' + id, { title: title.value, description: description.value },
        (request) => {
            if (request.readyState != 4) return;
            if (request.status == 404) {
                error.classList.remove('hidden');
                error.textContent = "Error: This question does not exist (anymore).";
                return;
            }
                    
            if (request.status == 405) {
                error.classList.remove('hidden');
                error.textContent = "Error: Invalid field submitted to the server.";
                return;
            }
            
            if (request.status != 200) {
                error.classList.remove('hidden');
                error.textContent = "Error: Internal server error. Try again later.";
                return;
            }

            // Replace the old question with the updated one
            let q = createElemFromRequest(request);
            question.parentElement.replaceChild(q, question);

            // Close the modal
            closeEditQuestionModal();
        });
}
//#endregion

//#region Create User
function showCreateUserModal() {
    // Get the modal element
    let modal = document.querySelector('#user-create');

    // Show the modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // Hide any previous error messages
    let error = modal.querySelectorAll('.err');
    error.forEach((elem) => {
        elem.classList.add('hidden');
    });
}

function closeCreateUserModal() {
    // Get the modal element
    let modal = document.querySelector('#user-create');
    // Hide any error messages
    let error = modal.querySelectorAll('.err');
    error.forEach((elem) => {
        elem.classList.add('hidden');
    });

    // Clear all input fields
    let inputs = modal.querySelectorAll("input");
    inputs.forEach((elem) => {
        elem.value = "";
    });

    // Hide the modal
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function sendCreateUserRequest() {
    // Get the user list and modal elements
    let userList = document.querySelector('#user-list');
    let modal = document.querySelector('#user-create');
    // Hide any previous error messages
    let errors = modal.querySelectorAll('.err');
    errors.forEach((elem) => {
        elem.classList.add('hidden');
    });

    let failedValidation = false;

    // Validate the name input
    let name = document.querySelector('#user-name');
    let errorName = modal.querySelector('.err#err-uc-name');
    if (name.value == "") {
        errorName.classList.remove('hidden');
        errorName.textContent = `Name can't be empty.`;
        failedValidation = true;
    }

    // Validate the username input
    let handle = document.querySelector('#user-username');
    let errorHandle = modal.querySelector('.err#err-uc-username');
    if (handle.value == "") {
        errorHandle.classList.remove('hidden');
        errorHandle.textContent = `Handle can't be empty.`;
        failedValidation = true;
    }

    // Validate the email input
    let email = document.querySelector('#user-email');
    let errorEmail = modal.querySelector('.err#err-uc-email');
    if (email.value.includes("@") == false || email.value == "") {
        errorEmail.classList.remove('hidden');
        errorEmail.textContent = `Email is not valid.`;
        failedValidation = true;
    }

    // Validate the password input
    let password = document.querySelector('#user-password');
    let errorPassword = modal.querySelector('.err#err-uc-password');
    if (password.value.length < 8) {
        errorPassword.classList.remove('hidden');
        errorPassword.textContent = `Password must be longer than 8 characters.`;
        failedValidation = true;
    }

    // If validation fails, return early
    if (failedValidation == true) return;
    
    let role = document.querySelector('#user-role');
    let error = modal.querySelector('.err#err-eq-gen');

    // Send the AJAX request to create the user
    sendAjaxRequest('POST', '/users', { name: name.value, username: handle.value,
                                        email: email.value, password: password.value,
                                        role: role.value },
        (request) => {
            if (request.readyState != 4) return;
            if (request.status == 302) {
                error.classList.remove('hidden');
                error.textContent = "Error: Invalid field contents.";
                return;
            }
        
            if (request.status != 200) {
                error.classList.remove('hidden');
                error.textContent = "Error: Internal server error. Try again later.";
                return;
            }

            // Add the new user to the user list
            let user = createElemFromRequest(request);
            userList.prepend(user);
            // Close the modal
            closeCreateUserModal();
        });
}

function displayError(element, message) {
    // Create an icon element for the error
    let icon = document.createElement('i');
    icon.classList.add('fa-solid', 'fa-exclamation-circle', 'mr-2'); // Add Font Awesome classes and margin-right
    // Create a text node for the error message
    let text = document.createTextNode(message);
    // Append the icon and text to the error element
    element.appendChild(icon);
    element.appendChild(text);
    // Show the error element
    element.classList.remove('hidden');
}