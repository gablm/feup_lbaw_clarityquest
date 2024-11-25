<article id="tag" class="w-full bg-white shadow-md rounded-lg mb-4" data-id="{{ $tag->id }}">
	<div class="flex flex-row p-4">
		<a href="{{ url('/tag/' . $tag->id) }}" class="tag-big">{{ $tag->name }}</a>
		<button onclick="editTag({{ $tag->id }}, '{{ $tag->name }}')" class="tool-link">
			<i class="fa-solid fa-pencil"></i>
			<span class="max-md:hidden ml-1">Edit</span>
		</button>
		<button onclick="deleteTag({{ $tag->id }})" class="tool-link text-red-500">
			<i class="fa-solid fa-trash"></i>
			<span class="max-md:hidden ml-1">Delete</span>
		</button>
	</div>
</article>