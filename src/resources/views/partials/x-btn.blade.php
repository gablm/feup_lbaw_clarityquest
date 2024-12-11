<a class="tool-link bg-white shadow-md rounded-lg space-x-2" href="{{ route('auth.x') }}">
	<i class="fa-brands fa-x-twitter"></i>
	@switch($linked)
	@case(0)
	<span>Login with X</span>
	@break
	@case(1)
	<span>Link X Account</span>
	@break
	@case(2)
	<span>Unlink X Account</span>
	@break
	@endswitch
</a>