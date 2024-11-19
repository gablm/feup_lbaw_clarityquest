<div class="flex flex-col w-64 p-4 bg-gray-100">
	<a href= {{ url('questions/create') }}>
		<button class="auth-main mb-2">
			<i class="fa-solid fa-plus pr-2"></i>
			New Question
		</button>
	</a>
	<ul class="space-y-2">
		<li>
			<a href="/my-questions" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-200">
				My Questions
			</a>
		</li>
		<li>
			<a href="/my-answers" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-200">
				My Answers
			</a>
		</li>
		<li>
			<a href="/followed-questions" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-200">
				Followed Questions
			</a>
		</li>
		<li>
			<a href="/followed-tags" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-200">
				Followed Tags
			</a>
		</li>
	</ul>
</div>