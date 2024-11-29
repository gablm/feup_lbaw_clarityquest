<div class="mt-4 w-full bg-white shadow-md rounded-lg">
	<a class="flex flex-row space-x-2 p-2" href="{{ route('auth.google') }}">
		<i class="fa-brands fa-google"></i>
		@switch($linked)
		@case(0)
		<span>Login with Google</span>
		@break
		@case(1)
		<span>Login with Google</span>
		@break
		@case(2)
		<span>Login with Google</span>
		@break
		@endswitch
	</a>
</div>