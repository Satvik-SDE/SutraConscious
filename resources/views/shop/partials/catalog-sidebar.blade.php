<aside data-reveal="left" class="lg:sticky lg:top-28 lg:self-start space-y-8">
    <div>
        <div class="eyebrow-dim mb-4">Shop</div>
        <ul class="space-y-3">
            <li>
                <a href="{{ route('shop') }}" class="block text-sm tracking-wide {{ $activeDepartment === null && $activeCategory === null ? 'text-brand-blue font-medium' : 'text-brand-black hover:text-brand-blue' }}">
                    <span class="inline-flex items-center gap-2">
                        @if($activeDepartment === null && $activeCategory === null)<span class="w-1.5 h-1.5 rounded-full bg-brand-blue"></span>@endif
                        Shop All
                    </span>
                </a>
            </li>
            @foreach($departments as $department)
                <li>
                    <a href="{{ route('department.show', $department->slug) }}" class="block text-sm tracking-wide {{ $activeDepartment === $department->slug && $activeCategory === null ? 'text-brand-blue font-medium' : 'text-brand-black hover:text-brand-blue' }}">
                        <span class="inline-flex items-center gap-2">
                            @if($activeDepartment === $department->slug && $activeCategory === null)<span class="w-1.5 h-1.5 rounded-full bg-brand-blue"></span>@endif
                            {{ $department->name }}
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    @if($activeDepartment)
        @php
            $sidebarDepartment = $departments->firstWhere('slug', $activeDepartment);
        @endphp
        @if($sidebarDepartment && $sidebarDepartment->activeCategories->isNotEmpty())
            <div class="rule"></div>
            <div>
                <div class="eyebrow-dim mb-4">Collections</div>
                <ul class="space-y-3">
                    @foreach($sidebarDepartment->activeCategories as $cat)
                        <li>
                            <a href="{{ route('category.show', $cat->slug) }}" class="block text-sm tracking-wide {{ $activeCategory === $cat->slug ? 'text-brand-blue font-medium' : 'text-brand-black hover:text-brand-blue' }}">
                                <span class="inline-flex items-center gap-2">
                                    @if($activeCategory === $cat->slug)<span class="w-1.5 h-1.5 rounded-full bg-brand-blue"></span>@endif
                                    {{ $cat->name }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    @elseif($activeCategory)
        @php
            $sidebarCategory = $departments->flatMap->activeCategories->firstWhere('slug', $activeCategory);
            $sidebarDepartment = $sidebarCategory ? $departments->firstWhere('id', $sidebarCategory->department_id) : null;
        @endphp
        @if($sidebarDepartment && $sidebarDepartment->activeCategories->isNotEmpty())
            <div class="rule"></div>
            <div>
                <div class="eyebrow-dim mb-4">Collections in {{ $sidebarDepartment->name }}</div>
                <ul class="space-y-3">
                    @foreach($sidebarDepartment->activeCategories as $cat)
                        <li>
                            <a href="{{ route('category.show', $cat->slug) }}" class="block text-sm tracking-wide {{ $activeCategory === $cat->slug ? 'text-brand-blue font-medium' : 'text-brand-black hover:text-brand-blue' }}">
                                <span class="inline-flex items-center gap-2">
                                    @if($activeCategory === $cat->slug)<span class="w-1.5 h-1.5 rounded-full bg-brand-blue"></span>@endif
                                    {{ $cat->name }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif

    @if(! empty($showFabricPledge))
        <div class="rule"></div>
        <div class="bg-brand-skin/30 border border-surface-line p-5">
            <div class="eyebrow-dim mb-2">Fabric Pledge</div>
            <p class="text-sm text-brand-black/75 leading-relaxed">Every Sutra Conscious piece is 100% cotton. No blends. No synthetics. Ever.</p>
        </div>
    @endif

    @if(! empty($sidebarNoteTitle) && ! empty($sidebarNoteBody))
        <div class="rule"></div>
        <div class="bg-brand-skin/30 border border-surface-line p-5">
            <div class="eyebrow-dim mb-2">{{ $sidebarNoteTitle }}</div>
            <p class="text-sm text-brand-black/75 leading-relaxed">{{ $sidebarNoteBody }}</p>
        </div>
    @endif
</aside>
