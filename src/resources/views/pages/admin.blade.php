@extends('layouts.app')

@section('title', 'Admin Panel')

@section('content')
<div class="container mx-auto p-6" onload="showAdminTab('tags')">
	<div class="p-4 pt-4">
		<h2 class="text-2xl font-semibold mb-8">
			Admin Panel
			@include('partials.tip', ['tip' => "Here is the panel for managing reports, users and tags."])
		</h2>
		<div class="mb-6 border-b border-gray-200">
			<ul class="flex flex-row space-x-4 -mb-px text-lg font-medium">
				<li>
					<button id="reports-tab"
						class="tab-btn bg-blue-100 text-blue-600 border-b-2 border-blue-600 px-4 py-2 rounded-t-lg hover:bg-blue-200"
						onclick="showAdminTab('reports')">
						Reports
					</button>
				</li>
				<li>
					<button id="users-tab"
						class="tab-btn bg-gray-100 text-gray-600 border-b-2 border-transparent px-4 py-2 rounded-t-lg hover:bg-gray-200"
						onclick="showAdminTab('users')">
						Users
					</button>
				</li>
				@if (Auth::user()->isAdmin())
					<li>
						<button id="tags-tab"
							class="tab-btn bg-gray-100 text-gray-600 border-b-2 border-transparent px-4 py-2 rounded-t-lg hover:bg-gray-200"
							onclick="showAdminTab('tags')">
							Tags
						</button>
					</li>
				@endif
			</ul>
		</div>

		<div id="reports-section" class="tab-content">
			@if($reports->isEmpty())
				<p class="pl-4 text-gray-700">No reposts! YAY</p>
			@else
				<div class="space-y-4">
					@foreach($reports as $report)
						@include('partials.report-card', $report)
					@endforeach
				</div>
			@endif
		</div>

		<div id="users-section" class="tab-content hidden">
			@if(Auth::user()->isAdmin())
				<button onclick="showCreateUserModal()" class="ml-4 mb-4 nav-main">
					<i class="fa-solid fa-plus"></i>
					<span class="ml-1">Create</span>
				</button>
				<div id="user-create" class="hidden modal modal-style">
					<div class="modal-overlay modal-bg"></div>
					<div class="modal-container modal-cont">
						<form class="modal-content py-4 text-left px-6" onsubmit="sendCreateUserRequest(); return false;">
							<p class="text-2xl font-bold mb-4">Create User</p>
							<div class="flex flex-col mb-4">
								<label class="auth" for="user-name">Name</label>
								<input class="auth focus:outline-none focus:shadow-outline" id="user-name" type="text"
									name="name" placeholder="John Doe" required>
								<span id="err-uc-name" class="err hidden auth-error bold mt-1"></span>
							</div>

							<div class="flex flex-col mb-4">
								<label class="auth" for="user-username">Handle</label>
								<input class="auth focus:outline-none focus:shadow-outline" id="user-username" type="text"
									name="username" placeholder="@JohnDoe" required>
								<span id="err-uc-username" class="err hidden auth-error bold mt-1"></span>
							</div>

							<div class="flex flex-col mb-4">
								<label class="auth" for="user-email">E-Mail Address</label>
								<input class="auth focus:outline-none focus:shadow-outline" id="user-email" type="email"
									name="email" placeholder="john.doe@example.com" required>
								<span id="err-uc-email" class="err hidden auth-error bold mt-1"></span>
							</div>

							<div class="flex flex-col mb-4">
								<label class="auth" for="user-password">Password</label>
								<input class="auth focus:outline-none focus:shadow-outline" id="user-password"
									type="password" name="password" title="User Password" required>
								<span id="err-uc-password" class="err hidden auth-error bold mt-1"></span>
							</div>

							<div class="mb-4">
								<label class="auth" for="user-role">Role</label>
								<select name="role" id="user-role" class="auth focus:outline-none focus:shadow-outline" required>
									@foreach (\App\Enum\User\Permission::cases() as $role)
										<option value="{{ $role->value }}" @if($role->value == 'REGULAR') selected @endif>
											{{ $role->name }}
										</option>
									@endforeach
								</select>
							</div>
							<span id="err-uc-gen" class="err hidden auth-error bold mt-1"></span>
							<div class="mt-4 flex space-x-2 justify-end">
								<button class="modal-close tool-link" type="button" onclick="closeCreateUserModal()">Cancel</button>
								<button type="submit" class="nav-main">Create</button>
							</div>
						</form>
					</div>
				</div>
			@endif
			<div id="user-list" class="space-y-4">
				@if($users->isEmpty())
					<p class="pl-4 text-gray-700">No users found.</p>
				@else
					@foreach($users as $user)
						@include('partials.user-card', ['user' => $user, 'panel' => true])
					@endforeach
				@endif
			</div>
		</div>

		<div id="tags-section" class="tab-content hidden">
			@if (Auth::user()->isAdmin())
				<button onclick="showCreateTagModal()" class="ml-4 mb-4 nav-main">
					<i class="fa-solid fa-plus"></i>
					<span class="ml-1">Create</span>
				</button>
				<div id="tag-create" class="hidden modal modal-style">
					<div class="modal-overlay modal-bg"></div>
					<div class="modal-container modal-cont">
						<form class="modal-content py-4 text-left px-6" onsubmit="sendCreateTagRequest(); return false;">
							<p class="text-2xl font-bold mb-4">Create Tag</p>
							<div class="flex flex-col mb-4">
								<input onkeyup="charCounter(this, this, 24)" onkeydown="charCounter(this, this, 24)"
									class="auth focus:outline-none focus:shadow-outline" type="text"
									id="tag-name" placeholder="Tag Name" required aria-label="Tag Name" name="name" maxlength="24" required>
								<span class="counter my-2">0/24 characters</span>
								<span class="err hidden auth-error bold mt-1"></span>
							</div>
							<div class="mt-4 flex space-x-2 justify-end">
								<button class="modal-close tool-link" type="button" onclick="closeCreateTagModal()">Cancel</button>
								<button class="nav-main" type="submit">Create</button>
							</div>
						</form>
					</div>
				</div>
				<div id="edit-tag" class="hidden modal modal-style">
					<div class="modal-overlay modal-bg"></div>
					<div class="modal-container modal-cont">
						<form class="modal-content py-4 text-left px-6" onsubmit="sendEditTagRequest(); return false;">
							<p id="edit-title" class="text-2xl font-bold mb-4">Edit Tag</p>
							<div class="flex flex-col mb-4">
								<input onkeyup="charCounter(this, this, 24)" onkeydown="charCounter(this, this, 24)"
									class="auth focus:outline-none focus:shadow-outline resize-none" id="tag-text"
									type="textarea" name="text" title="Tag Name" required maxlength="24" required>
								<span class="counter my-2">0/24 characters</span>
								<span class="err hidden auth-error bold mt-1"></span>
							</div>
							<div class="mt-4 flex space-x-2 justify-end">
								<button class="modal-close tool-link" type="button" onclick="closeEditTagModal()">Cancel</button>
								<button class="nav-main" type="submit">Save</button>
							</div>
						</form>
					</div>
				</div>
				<div id="tag-list" class="space-y-4">
					@if($tags->isEmpty())
						<p class="pl-4 text-gray-700">No tags found.</p>
					@endif
					@foreach($tags as $tag)
						@include('partials.tag-card', ['tag' => $tag, 'panel' => true])
					@endforeach
				</div>
			@endif
		</div>
	</div>
</div>
@endsection