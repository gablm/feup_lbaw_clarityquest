@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6" onload="showAdminTab('reports')">
	<div class="p-8">
		<h2 class="text-2xl font-semibold mb-4">Administration Panel</h2>
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
				<li>
					<button
						id="tags-tab"
						class="tab-btn bg-gray-100 text-gray-600 border-b-2 border-transparent px-4 py-2 rounded-t-lg hover:bg-gray-200"
						onclick="showAdminTab('tags')">
						Tags
					</button>
				</li>
			</ul>
		</div>

		<div id="reports-section" class="tab-content">
			@if($reports->isEmpty())
			<p class="text-gray-700">No reposts! YAY</p>
			@else
			<div class="space-y-4">
				@foreach($reports as $report)
				<div>
					Report 1
				</div>
				@endforeach
			</div>
			@endif
		</div>

		<div id="users-section" class="tab-content hidden">
			@if($users->isEmpty())
			<p class="text-gray-700">No users found.</p>
			@else
			<div class="space-y-4">
				@foreach($users as $user)
				@include('partials.user-card', $user)
				@endforeach
			</div>
			@endif
		</div>

		<div id="tags-section" class="tab-content hidden">
			@if($tags->isEmpty())
			<p class="text-gray-700">No tags found.</p>
			@else
			<div class="space-y-4">
				@foreach($tags as $tag)
				<div>{{ $tag->name }}</div>
				@endforeach
			</div>
			@endif
		</div>
	</div>
</div>
@endsection