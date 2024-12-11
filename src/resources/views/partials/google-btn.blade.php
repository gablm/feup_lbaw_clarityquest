<a class="tool-link bg-white shadow-md rounded-lg space-x-2" href="{{ route('auth.google') }}">
	<i class="fa-brands fa-google"></i>
	@switch($linked)
	@case(0)
	<span>Login with Google</span>
	@break
	@case(1)
	<span>Link Google Account</span>
	@break
	@case(2)
	<span>Unlink Google Account</span>
	@break
	@endswitch
</a>