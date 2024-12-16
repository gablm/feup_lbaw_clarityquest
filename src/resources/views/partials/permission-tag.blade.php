@if ($user->isAdmin())
<span class="inline-block bg-purple-100 text-purple-800 text-base font-semibold mr-2 px-2.5 py-0.5 rounded">ADMIN</span>
@elseif ($user->isModerator())
<span class="inline-block bg-green-100 text-green-800 text-base font-semibold mr-2 px-2.5 py-0.5 rounded">MOD</span>
@elseif (Auth::check() && Auth::user()->isElevated() && $user->isBlocked())
<span class="inline-block bg-gray-100 text-gray-800 text-base font-semibold mr-2 px-2.5 py-0.5 rounded">BLOCKED</span>
@else
<span class="inline-block bg-blue-100 text-blue-800 text-base font-semibold mr-2 px-2.5 py-0.5 rounded">USER</span>
@endif