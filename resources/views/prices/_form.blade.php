<p>
    <label for="name">Name:</label> <br>
    <input
        type="text"
        id="name"
        name="name"
        value="{{ old('name', $price->name) }}"
        autofocus
        required
    >
</p>
<x-forms.input-error name="name" />

<p>
    <label for="price">Price:</label> <br>
    <input
        type="text"
        id="price"
        name="price"
        value="{{ old('price', $price->name) }}"
        autofocus
        required
    >
</p>
<x-forms.input-error name="name" />
