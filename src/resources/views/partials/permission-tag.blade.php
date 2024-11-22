@if ($user->isAdmin())
<a class="inline-block bg-purple-100 text-purple-800 text-base font-semibold mr-2 px-2.5 py-0.5 rounded">ADMIN</a>
@elseif ($user->isModerator())
<a class="inline-block bg-green-100 text-green-800 text-base font-semibold mr-2 px-2.5 py-0.5 rounded">MOD</a>
@else
<a class="inline-block bg-blue-100 text-blue-800 text-base font-semibold mr-2 px-2.5 py-0.5 rounded">USER</a>
@endif