<div class="download--license">
    <label for="license-{{ $license->uuid }}" class="sr-only">Copy your license key</label>
    <input
        type="text"
        id="license-{{ $license->uuid }}"
        value="{{ $license->license_key }}"
        readonly
    >
</div>
