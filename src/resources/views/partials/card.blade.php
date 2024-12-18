<article class="card" data-id="{{ $card->id }}">
    <header>
        <h2><a href="/cards/{{ $card->id }}">{{ $card->name }}</a></h2>
        <a href="#" class="delete">&#10761;</a>
    </header>
    <ul>
        @each('partials.item', $card->items()->orderBy('id')->get(), 'item')
    </ul>
    <form class="new_item">
        <fieldset>
            <legend class="sr-only">New Item</legend>
            <input type="text" name="description" placeholder="new item" class="border rounded px-2 py-1">
        </fieldset>
    </form>
</article>