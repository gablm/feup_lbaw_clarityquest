<div class="flex flex-col max-sm:hidden sm:w-64 md:w-64 sm:p-4 bg-gray-100">
	<a href= {{ url('questions/create') }}>
		<button class="auth-main mb-2">
			<i class="fa-solid fa-plus pr-2"></i>
			New Question
		</button>
	</a>
	<ul class="space-y-2">
		<li>
			<a href="/followed-questions" class="side-link">
				Followed Questions
			</a>
		</li>
		<li>
			<a href="/followed-tags" class="side-link">
				Followed Tags
			</a>
		</li>
	</ul>
</div>