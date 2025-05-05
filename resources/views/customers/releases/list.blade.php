<x-customer>
    <x-slot name="breadcrumbs">
        <x-elements.breadcrumb url="{{ route('customer.downloads.list') }}" name="Downloads"/>
    </x-slot>

    <x-slot name="header">Releases</x-slot>
    <x-slot name="subtitle">{{ $product->name }}</x-slot>

    <div class="releases-list">
        @foreach($releases as $release)
            <article class="releases-list--release">
                <header class="md:flex justify-between mb-1">
                    <div>
                        <h2 class="m-0">
                            {{ $release->version }}
                            @if($release->pre_release)
                                <x-elements.tag>Pre-release</x-elements.tag>
                            @endif
                        </h2>
                        <time datetime="{{ $release->created_at->toDateTimeString() }}">
                            {{ $release->created_at->toFormattedDateString() }}
                        </time>

                        @if($release->requirements)
                            <div class="release-requirements">
                                @foreach($release->requirements as $requirement)
                                    <span class="release-requirement release-requirement--{{ strtolower($requirement->name) }}">
                                        <span class="release-requirement--name">{{ $requirement->name }}</span>
                                        <span class="release-requirement--version">{{ $requirement->version }}</span>
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="releases-list--release--actions">
                        @if(auth()->user()->can('download', $release))
                            <a
                                href="{{ route('release.download', $release) }}"
                                class="button"
                            >
                                Download
                            </a>
                        @endif
                    </div>
                </header>

                <div class="releases-list--release--body">
                    {!! \Illuminate\Mail\Markdown::parse($release->notes) !!}
                </div>
            </article>
        @endforeach
    </div>
</x-customer>
