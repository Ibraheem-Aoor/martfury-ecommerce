@php
    $urlCurrent = URL::current();
    $children->loadMissing(['slugable', 'children:id,name,parent_id', 'children.slugable']);
@endphp

<span class="sub-toggle"><i class="icon-angle"></i></span>
<ul class="sub-menu" @if (in_array($urlCurrent, collect($children->toArray())->pluck('url')->toArray())) style="display:block" @endif>
    @foreach($children as $category)
        <li class="@if($urlCurrent == $category->url) current-menu-item @endif @if ($category->children->count()) menu-item-has-children @endif"><a href="{{ $category->url }}">{{ $category->name }}</a>
            @if ($category->children->count())
                @include(Theme::getThemeNamespace() . '::views.ecommerce.includes.sub-categories', ['children' => $category->children])
            @endif
        </li>
    @endforeach
</ul>
