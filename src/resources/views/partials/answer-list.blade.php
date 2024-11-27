<div id="answer-list" class="flex flex-col space-y-8">
    @foreach ($answerList as $answer)
    @include('partials.answer', $answer)
    @endforeach
</div>