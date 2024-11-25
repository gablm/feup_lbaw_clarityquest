@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6" onload="showAdminTab('tags')">
	<div class="p-4 pt-4">
		<h2 class="text-2xl font-semibold mb-8">Administration Panel</h2>
		<div class="mb-6 border-b border-gray-200">
			<ul class="flex flex-row space-x-4 -mb-px text-lg font-medium">
				<li>
					<button
						id="reports-tab"
						class="tab-btn bg-blue-100 text-blue-600 border-b-2 border-blue-600 px-4 py-2 rounded-t-lg hover:bg-blue-200"
						onclick="showAdminTab('reports')">
						Reports
					</button>
				</li>
				<li>
					<button
						id="users-tab"
						class="tab-btn bg-gray-100 text-gray-600 border-b-2 border-transparent px-4 py-2 rounded-t-lg hover:bg-gray-200"
						onclick="showAdminTab('users')">
						Users
					</button>
				</li>
				@if (Auth::user()->isAdmin())
				<li>
					<button
						id="tags-tab"
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
			@if($users->isEmpty())
			<p class="pl-4 text-gray-700">No users found.</p>
			@else
			<div class="space-y-4">
				@foreach($users as $user)
				@include('partials.user-card', ['user' => $user, 'panel' => true])
				@endforeach
			</div>
			@endif
		</div>
		
		<div id="tags-section" class="tab-content hidden">
		@if (Auth::user()->isAdmin())
			<button onclick="showCreateTagModal()" class="ml-4 mb-4 nav-main">
				<i class="fa-solid fa-plus"></i>
				<span class="max-sm:hidden ml-1">Create</span>
			</button>
			<div id="tag-create" class="hidden modal fixed w-full h-full top-0 left-0 items-center justify-center">
				<div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
				<div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
					<div class="modal-content py-4 text-left px-6">
						<p class="text-2xl font-bold mb-4">Create Tag</p>
						<div class="mb-4">
							<input class="auth focus:outline-none focus:shadow-outline" type="text" id="tag-name" name="name" required>
						</div>
						<div class="mt-4 flex space-x-2 justify-end">
							<button class="modal-close tool-link" onclick="closeCreateTagModal()">Cancel</button>
							<button class="nav-main" onclick="sendCreateTagRequest()">Create</button>
						</div>
					</div>
				</div>
			</div>
			<div id="edit-tag" class="hidden modal modal-style">
				<div class="modal-overlay modal-bg"></div>
				<div class="modal-container modal-cont">
					<div class="modal-content py-4 text-left px-6">
						<p id="edit-title" class="text-2xl font-bold mb-4">Edit Tag</p>
						<div class="mb-4">
							<input class="auth focus:outline-none focus:shadow-outline resize-none" id="text" type="textarea" name="text" required>
						</div>
						<div class="mt-4 flex space-x-2 justify-end">
							<button class="modal-close tool-link" onclick="closeEditTagModal()">Cancel</button>
							<button class="nav-main" onclick="sendEditTagRequest()">Save</button>
						</div>
					</div>
				</div>
			</div>
			<div id="tag-list" class="space-y-4">
				@if($tags->isEmpty())
				<p class="pl-4 text-gray-700">No tags found.</p>
				@endif
				@foreach($tags as $tag)
				@include('partials.tag-card', $tag)
				@endforeach
			</div>
			@endif
		</div>
	</div>
</div>
@endsection