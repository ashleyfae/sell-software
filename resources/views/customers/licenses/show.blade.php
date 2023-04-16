<x-customer>
    <x-slot name="header">License Key</x-slot>

    <div class="box with-padding">
        <h3 class="mt-0">Details</h3>

        <ul>
            <li>Status: <x-elements.license-key-status :status="$license->status" /></li>
            <li>
                @if($license->isLifetime())
                    Never Expires
                @else
                    @if($license->isExpired())
                        Expired On:
                    @else
                        Expires On:
                        @endif
                        {{ $license->expires_at->toFormattedDateString() }}

                        &mdash;

                        <a href="{{ route('customer.licenses.renew', $license) }}">Renew Now</a>
                    @endif
            </li>
            <li>
                Site Activations: {{ $license->siteActivations ? $license->siteActivations->count() : 0 }} /
                @if($license->hasUnlimitedActivations())
                    unlimited
                @else
                    {{ $license->activation_limit }}
                @endif
            </li>
        </ul>
    </div>

    @if($license->siteActivations->isNotEmpty())
        <div class="box mt-8">
            <h3 class="mt-0">Site Activations</h3>
            <p>Your license key is activated on the following {{ \Illuminate\Support\Str::plural('site', $license->siteActivations->count()) }}:</p>

            <ul>
                @foreach($license->siteActivations as $site)
                    <li data-domain="{{ $site->domain }}">
                        {{ $site->domain }}

                        &mdash;

                        <button
                            type="button"
                            class="plain deactivate-site"
                            data-route="{{ route('api.licenses.activations.destroy', $license) }}"
                            data-domain="{{ $site->domain }}"
                        >Deactivate</button>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</x-customer>
