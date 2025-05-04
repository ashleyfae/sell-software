<p>
    <label for="name">Name:</label> <br>
    <input
        type="text"
        id="name"
        name="name"
        value="{{ old('name', $product->name) }}"
        autofocus
        required
    >
</p>
<x-forms.input-error name="name" />

<label for="description">Description:</label> <br>
<textarea
    id="description"
    name="description"
    rows="5"
>{{ old('description', $product->description) }}</textarea>
<x-forms.input-error name="description" />

<p>
    <label for="git-repo">Git Repo:</label> <br>
    <input
        type="text"
        id="git-repo"
        name="git_repo"
        value="{{ old('git_repo', $product->git_repo) }}"
        required
    >
</p>
<x-forms.input-error name="git_repo" />

<p>
    <label for="stripe-id">Stripe ID:</label> <br>
    <input
        type="text"
        id="stripe-id"
        name="stripe_id"
        value="{{ old('stripe_id', $product->stripe_id) }}"
        required
    >
</p>
<x-forms.input-error name="stripe_id" />
