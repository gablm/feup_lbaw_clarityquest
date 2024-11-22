@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-semibold mb-4">Contact Us</h2>
        <p class="text-gray-700 mb-4">You can reach us by emailing one of the platform mantainers:</p>
		<ul class="ml-6 list-disc">
			<li>
				<a class="hover:underline" href="mailto:up202205636@up.pt">
					Sara Cortez (up202205636@up.pt)
				</a>
			</li>
			<li>
				<a class="hover:underline" href="mailto:up202206693@up.pt">
					Gabriel Lima (up202206693@up.pt)
				</a>
			</li>
			<li>
				<a class="hover:underline" href="mailto:up202205612@up.pt">
					Beatriz Ferreira (up202205612@up.pt)
				</a>
			</li>
		</ul>
    </div>
</div>
@endsection