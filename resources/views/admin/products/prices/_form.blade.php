<?php /** @var \App\Models\ProductPrice $price */ ?>
<p>
    <label for="name">Name:</label>
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
    <label for="price">Stripe ID:</label>
    <input
        type="text"
        id="stripe_id"
        name="stripe_id"
        value="{{ old('stripe_id', $price->stripe_id) }}"
        placeholder="price_xxx"
        required
    >
</p>
<x-forms.input-error name="stripe_id" />

<p>License duration:</p>

<div class="flex">
    <div>
        <p>
            <input
                type="number"
                id="license_period"
                name="license_period"
                value="{{ old('license_period', $price->license_period) }}"
                @class(['hidden' => old('license_period_unit', $price->license_period_unit) === \App\Enums\PeriodUnit::Lifetime])
            >
        </p>
        <x-forms.input-error name="license_period" />
    </div>

    <div>
        <p>
            <select
                id="license_period_unit"
                name="license_period_unit"
            >
                @foreach(\App\Enums\PeriodUnit::cases() as $periodUnit)
                    <option
                        value="{{ $periodUnit->value }}"
                        @selected(old('license_period_unit', $price->license_period_unit) == $periodUnit)
                    >
                        {{ $periodUnit->name }}
                    </option>
                @endforeach
            </select>
        </p>
        <x-forms.input-error name="license_period_unit" />
    </div>
</div>

<div>
    <label for="activation_limit">Activation Limit:</label>
    <div id="activation-limit-wrapper" class="flex">
        <input
            type="number"
            id="activation_limit"
            name="activation_limit"
            value="{{ old('activation_limit', $price->activation_limit) }}"
        >
        <span>Sites</span>
    </div>
</div>
<x-forms.input-error name="activation_limit" />

<p>
    <label for="is_active">
        <input
            type="checkbox"
            id="is_active"
            name="is_active"
            value="1"
            @checked(! empty(old('is_active', $price->is_active)))
        >
        Active
    </label>
</p>
