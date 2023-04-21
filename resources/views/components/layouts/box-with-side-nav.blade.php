<div class="box with-side-menu">
    <nav class="with-side-menu--nav">
        <ul>
            {{ $nav }}
        </ul>
    </nav>

    <section class="with-side-menu--content">
        <div class="with-side-menu--content--inner">
            {{ $slot }}
        </div>
    </section>
</div>
